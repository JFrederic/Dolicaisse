<?php
// api/Facture.php
require_once 'DolibarrApi.php';
class Facture extends DolibarrApi
{
    protected $apiKey;

    public function __construct()
    {
        parent::__construct();
        if (property_exists($this, 'apiKey')) {
            $this->apiKey = $this->apiKey;
        } elseif (property_exists($this, 'apikey')) {
            $this->apiKey = $this->apikey;
        }
    }
    // Recherche facture (déjà ok)
    public function search($search)
    {
        return $this->call('GET', '/invoices', [], [
            'sqlfilters' => "(f.ref:=:'$search' OR f.societe_tiers_nom:=:'$search' OR f.societe_tiers_prenom:=:'$search')"
        ]);
    }
    // Dernière ref
    public function getLastInvoiceRef()
    {
        $factures = $this->call('GET', '/invoices', [], [
            'sortfield' => 'rowid',
            'sortorder' => 'desc',
            'limit' => 1
        ]);
        return (!empty($factures) && isset($factures[0]['ref'])) ? $factures[0]['ref'] : null;
    }
    public function getPdf($invoiceId)
    {
        // Génère le PDF si besoin
        $this->call('POST', "/invoices/$invoiceId/document", ['model' => 'crabe']);

        $data = $this->call('GET', "/invoices/$invoiceId");
        if (!$data || empty($data['ref'])) return false;
        $ref = $data['ref'];
        $url = "https://97packs.mobisoft.fr/dolibarr/documents/facture/$ref/$ref.pdf";
        var_dump($url);
        $headers = [
            'DOLAPIKEY: ' . $this->apiKey
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $pdf = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 200 && $pdf) {
            $tmpFile = sys_get_temp_dir() . "/facture_$ref.pdf";
            file_put_contents($tmpFile, $pdf);
            return $tmpFile;
        }
        return false;
    }



    public function generateTicket($invoiceId)
    {
        // 1. Récupère la facture et ses lignes depuis l'API Dolibarr
        $facture = $this->call('GET', "/invoices/$invoiceId");
        $lines   = $this->call('GET', "/invoices/$invoiceId/lines");

        if (!$facture || !$lines) return false;

        // 2. Compose le contenu du ticket
        $content  = "=== TICKET DE CAISSE ===\n";
        $content .= "Facture: " . ($facture['ref'] ?? $invoiceId) . "\n";
        $content .= "Date: " . ($facture['date'] ?? date('Y-m-d')) . "\n";
        $content .= "Client: " . ($facture['socname'] ?? '') . "\n";
        $content .= "------------------------------\n";
        foreach ($lines as $line) {
            $content .= str_pad($line['desc'], 20) .
                str_pad($line['qty'], 3, ' ', STR_PAD_LEFT) . " x " .
                number_format($line['subprice'], 2, ',', '.') . " € = " .
                number_format($line['total_ttc'], 2, ',', '.') . " €\n";
        }
        $content .= "------------------------------\n";
        $content .= "TOTAL TTC: " . number_format($facture['total_ttc'], 2, ',', '.') . " €\n";
        $content .= "Merci de votre visite !\n";

        // 3. Stocke dans un fichier temporaire (ou return direct)
        $filename = sys_get_temp_dir() . "/ticket_" . $invoiceId . ".txt";
        file_put_contents($filename, $content);

        return $filename;
    }

    // Génération ref
    public function generateInvoiceRef($lastRef)
    {
        if (preg_match('/^FA(\\d{4})(\\d+)$/', $lastRef, $m)) {
            $next = str_pad(((int)$m[2]) + 1, strlen($m[2]), '0', STR_PAD_LEFT);
            return 'FA' . $m[1] . "-" . $next;
        }
        return 'FA' . date('Y') . '001';
    }
    // Création complète d'une facture : lignes, paiements, stocks
    public function createFullInvoice($data)
    {
        session_start();
        $warehouseId = $_SESSION['entrepot_id'] ?? 1; // à utiliser dans createFullInvoice
        // Puis passe $warehouseId en paramètre pour la décrémentation de stock

        $lastRef = $this->getLastInvoiceRef();
        $newRef = $this->generateInvoiceRef($lastRef);
        $invoice = [
            'socid' => $data['tiers_id'],
            // 'ref' => $newRef,
            'date' => date('Y-m-d'),
            'type' => 0
        ];
        $resp = $this->call('POST', '/invoices', $invoice);
        if (!is_numeric($resp)) return ['error' => true, 'message' => 'Erreur création facture'];
        $invoiceId = $resp;
        // Nouvelle gestion: ajoute tous les produits reçus dans $data['produits']

        if (!empty($data['produits']) && is_array($data['produits'])) {
            foreach ($data['produits'] as $prod) {
                $this->call('POST', "/invoices/$invoiceId/lines", [
                    "fk_product" => $prod['id'],
                    "fk_facture" => $invoiceId,
                    "libelle" => $prod['designation'],
                    "desc" => $prod['designation'],
                    "product_label" => $prod['designation'],
                    "product_ref" => $prod['ref'],
                    "ref" => $prod['ref'],
                    "product_barcode" => $prod['ref'],
                    "fk_product_type" => "0",
                    'qty' => $prod['qty'],
                    'subprice' => $prod['pu'],
                    'tva_tx' => $prod['tva'] ?? 20,
                    'remise_percent' => $prod['remise'] ?? 0,
                ]);
                // Décrément stock
                if (isset($data['warehouseId'])) {
                    $stockResp = $this->call('POST', "/stockmovements", [
                        'product_id' => $prod['id'],
                        'warehouse_id' => $warehouseId,
                        'qty' => -abs($prod['qty'])
                        // "movementcode" => "SORTIE_VENTE",
                        // 'movementlabel' => 'Sortie vente',
                    ]);

                    if (isset($stockResp['error']) && $stockResp['error']) {
                        return ['error' => true, 'message' => 'Erreur mouvement de stock', 'details' => $stockResp];
                    }
                }
            }
        }
        $this->call('POST', "/invoices/$invoiceId/validate", ['status' => 2]);
        // Paiements (identique)
        if (!empty($data['paiements']) && is_array($data['paiements'])) {
            foreach ($data['paiements'] as $p) {
                $mode = $p['mode'] == 'CB' ? 6 : ($p['mode'] == 'Espèces' ? 4 : ($p['mode'] == 'Chèque' ? 2 : 0));
                $params = [
                    'datepaye'          => date('Y-m-d'), // ou passe la date si dispo
                    'paymentid'        => $mode, // ID mode paiement Dolibarr
                    'closepaidinvoices' => "yes", // ferme la facture si payée
                    'accountid'         => 1, // à adapter selon ton compte bancaire Dolibarr
                    // 'amount'            => $p['montant'],
                ];
                // var_dump($params,"ID Facture: $invoiceId");
                // Champs facultatifs selon le type de paiement
                // if (!empty($p['num_payment']))  $params['num_payment']  = $p['num_payment'];
                // if (!empty($p['comment']))      $params['comment']      = $p['comment'];
                // if (!empty($p['chqemetteur']))  $params['chqemetteur']  = $p['chqemetteur'];
                // if (!empty($p['chqbank']))      $params['chqbank']      = $p['chqbank'];
                $resp = $this->call('POST', "/invoices/$invoiceId/payments", $params);
                // var_dump($resp);
            }
        }


        return ['id' => $invoiceId, 'ref' => $newRef];
    }
}
