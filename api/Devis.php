<?php

require_once __DIR__ . '/DolibarrApi.php';

class Devis extends DolibarrApi
{
    protected $endpoint = '/proposals';

    // Tu peux ajouter ici des méthodes spécifiques au devis si besoin, sinon tu hérites de tout le reste
    public function createFullDevis($data)
{
    session_start();

    $proposal = [
        'socid' => $data['tiers_id'],
        // 'ref' => $newRef, // optionnel
        'date' => date('Y-m-d'),
        // 'status' => 0, // 0 = brouillon (draft)
    ];

    // Création du devis
    $resp = $this->call('POST', '/proposals', $proposal);
    if (!is_numeric($resp)) return ['error' => true, 'message' => 'Erreur création devis'];

    $proposalId = $resp;

    // Ajout des lignes produits/services
    if (!empty($data['produits']) && is_array($data['produits'])) {
        foreach ($data['produits'] as $prod) {
            $this->call('POST', "/proposals/$proposalId/lines", [
                "fk_product" => $prod['id'],
                "fk_propal" => $proposalId,
                "default_lang" => "fr_FR",
                "libelle" => $prod['designation'],
                "desc" => $prod['designation'],
                "product_label" => $prod['designation'],
                "product_ref" => $prod['ref'],
                "ref" => $prod['ref'],
                "product_barcode" => $prod['ref'],
                "fk_product_type" => "0", // 0 = produit, 1 = service
                'qty' => $prod['qty'],
                'subprice' => $prod['pu'],
                'tva_tx' => $prod['tva'] ?? 20,
                'remise_percent' => $prod['remise'] ?? 0,
            ]);
            // On ne décrémente pas le stock sur un devis !
        }
    }

    // Validation du devis (optionnel, si tu veux que le devis ne reste pas en brouillon)
    $this->call('POST', "/proposals/$proposalId/validate");

    return ['id' => $proposalId];
}

    // Exemple : méthode spécifique de validation de devis (optionnel)
    public function validate($id)
    {
        // L'API Dolibarr propose souvent une action de validation via /validate
        return $this->call('POST', $this->endpoint . '/' . $id . '/validate');
    }
}
