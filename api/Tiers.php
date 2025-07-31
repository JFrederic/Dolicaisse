<?php
// api/Tiers.php
require_once 'DolibarrApi.php';

class Tiers extends DolibarrApi
{

    // Génère le code client au format C{2LettresNom}{2LettresPrenom}{CodePostal}-1
    private function generateCustomerCode($name, $firstname, $zip)
    {
        // Nettoyage : retire les accents, passe en majuscule, retire tout sauf lettres
        
        $name = strtoupper(preg_replace('/[^A-Za-z]/', '', self::normalize($name)));
        $firstname = strtoupper(preg_replace('/[^A-Za-z]/', '', self::normalize($firstname)));
        $zip = substr($zip, 0, 5);

        $partName = substr($name, 0, 2);
        $partFirstname = substr($firstname, 0, 2);
        return "C" . $partName . $partFirstname . $zip . "-1";
    }

    // Pour gérer les accents
    private static function normalize($string)
    {
        return iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
    }

    public function search($search)
    {
        return $this->call('GET', '/thirdparties', [], [
            'sqlfilters' => "(t.nom:like:'%$search%')"
        ]);
    }

    // Recherche un client par email, retourne la fiche ou null
    public function findByEmail($email) {
        if (!$email) return null;
        $result = $this->call('GET', '/thirdparties', [], [
            'sqlfilters' => "(t.email:=:'$email')"
        ]);
        return $result == false ? 0 : $result[0];
    }

    // Retourne tous les clients ayant un code similaire (pour gérer les doublons de ref)
    public function findByCodeClient($baseCodeClient) {
        $result = $this->call('GET', '/thirdparties', [], [
            'sqlfilters' => "(t.ref:like:'$baseCodeClient%')"
        ]);
        return $result == false ? 0 : $result;
    }

    // Création de client avec vérifications
    public function create($data)
    {
        $baseCodeClient = $this->generateCustomerCode(
            $data['name'] ?? '',
            $data['firstname'] ?? '',
            $data['zip'] ?? ''
        );
        $email = $data['email'] ?? '';

        // Recherche si client avec cet email existe déjà
        $existingClient = $this->findByEmail($email);
        if ($existingClient) {
            return [
                'error' => true,
                'message' => "Un client avec cet email existe déjà.",
                'client' => $existingClient
            ];
        }

        // Recherche tous les codes similaires existants
        $similarCodes = $this->findByCodeClient($baseCodeClient);
        $codeClient = $baseCodeClient;
        if (!empty($similarCodes)) {
            // Trouve les suffixes utilisés
            $maxSuffix = 1;
            foreach ($similarCodes as $client) {
                if (preg_match('#^' . preg_quote($baseCodeClient, '#') . '-(\d+)$#', $client['ref'], $matches)) {
                    $suffix = intval($matches[1]);
                    if ($suffix >= $maxSuffix) $maxSuffix = $suffix + 1;
                }
            }
            $codeClient = $baseCodeClient . '-' . $maxSuffix;
        }

        // Prépare les champs
        $data['code_client'] = $codeClient;
        $data['ref'] = $codeClient;
        $data['client'] = 1;
        $data['fournisseur'] = 0;

        // Création du client
        return $this->call('POST', '/thirdparties', $data);
    }
}
