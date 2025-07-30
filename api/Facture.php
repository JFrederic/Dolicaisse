<?php
// api/Facture.php
require_once 'DolibarrApi.php';
require '..\vendor\autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\EscposImage;

class Facture extends DolibarrApi
{

    // public function __construct()
    // {
    //     parent::__construct();
    //     if (property_exists($this, 'apiKey')) {
    //         $this->apiKey = $this->apiKey;
    //     } elseif (property_exists($this, 'apikey')) {
    //         $this->apiKey = $this->apikey;
    //     }
    // }
    // Recherche facture (déjà ok)
    public function search($search)
    {
        return $this->call('GET', '/invoices', [], [
            'sqlfilters' => "(t.ref:=:'$search')"
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

        // Récupère la référence de la facture
        $data = $this->call('GET', "/invoices/$invoiceId");
        if (!$data || empty($data['ref'])) return false;
        $ref = $data['ref'];
        $params = "?modulepart=facture&original_file=$ref/$ref.pdf";
        $url = $this->baseUrl . '/documents/download' . $params;
        $headers = [
            'DOLAPIKEY: ' . $this->apiKey
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Dolibarr retourne souvent du JSON si pas PDF, sinon binaire ou JSON avec base64
        $json = @json_decode($response, true);
        if (isset($json['content']) && isset($json['filename'])) {
            // Cas JSON Dolibarr : base64 + infos
            $pdf_content = base64_decode($json['content']);
            $filename = $json['filename'];
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            echo $pdf_content;
            exit;
        } elseif ($httpCode == 200 && $response) {
            // Cas PDF binaire direct
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="facture_' . $ref . '.pdf"');
            echo $response;
            exit;
        } else {
            http_response_code(404);
            echo "PDF non trouvé ou erreur.";
            exit;
        }
    }

    public function getFacturesByPeriode($dateDebut, $dateFin)
    {
        $params = [
            'sortfield' => 'date',
            'sortorder' => 'desc',
            'limit' => 1000,
            'sqlfilters' => "(f.date>='" . $dateDebut . "' AND f.date<='" . $dateFin . "')"
        ];
        $factures = $this->call('GET', '/invoices', [], $params);
        $result = [];
        foreach ($factures as $f) {
            $heure = isset($f['date']) ? date('H:i', strtotime($f['date'])) : '';
            // Exemple: on suppose $f['paiement'] (mode) existe, sinon adapte selon ton schéma !
            $couleur = "";
            $mode = strtolower($f['paiement'] ?? $f['mode_reglement_code'] ?? '');
            if ($mode == "cb" || $mode == "carte" || $mode == "card") $couleur = "#7FFF7F"; // vert
            elseif ($mode == "especes" || $mode == "cash") $couleur = "#FFBF60"; // orange
            // ... adapte ici selon tes données

            $result[] = [
                'ref' => $f['ref'] ?? '',
                'date' => isset($f['date']) ? date('d/m/Y', strtotime($f['date'])) : '',
                'heure' => $heure,
                'ttc' => $f['total_ttc'] ?? '',
                'client' => $f['societe_tiers_nom'] ?? '',
                'paiement' => $f['paiement'] ?? $f['mode_reglement_code'] ?? '',
                'color' => $couleur
            ];
        }
        return $result;
    }




    /**
     * Imprime un ticket de caisse compatible Windows et Linux
     * @param int $invoiceId
     * @param array $config  ex: [ 'type'=>'windows', 'printer'=>'EPSON' ] OU [ 'type'=>'linux', 'printer'=>'/dev/usb/lp0' ] OU [ 'type'=>'network', 'ip'=>'192.168.1.100', 'port'=>9100 ]
     */
    public function generateTicketText($invoiceId, $config)
    {
        $facture = $this->call('GET', "/invoices/$invoiceId");
        $payments = $this->call('GET', "/invoices/$invoiceId/payments");
        // var_dump($facture, $payments);die();
        if ($payments[0]['type'] == "LIQ") {
            $mode_paiement = "Especes";
        } else if ($payments[0]['type'] == "CB") {
            $mode_paiement = "CB";
        } else {
            $mode_paiement = "Chèque";
        }
        $lines   = $this->call('GET', "/invoices/$invoiceId/lines");
        if (!$facture || !$lines) return false;

        $width = 42;
        // En-tête centré
        $center = function ($txt) use ($width) {
            return str_pad($txt, $width, " ", STR_PAD_BOTH) . "\n";
        };
        $content  = $center("974 EMBALLAGES");
        $content .= $center("28 RUE GABRIEL DE KERGUELEN");
        $content .= $center("97490 SAINTE CLOTILDE");
        $content .= $center("TEL: 0262 300 818");
        $content .= $center("SIRET: 91088213400017 / APE: 4690Z");
        $content .= str_repeat("-", $width) . "\n";
        $content .= "\n";
        $content .= "Date: " . date('d/m/Y - H:i', strtotime($facture['date_validation'])) . "   N: " . $facture['ref'] . "\n";
        $content .= "\n";
        $content .= "ENTREPRISE: " . ($facture['socname'] ?? '') . "\n\n";

        // Titre articles
        $content .= "ARTICLE(S)            QTE  PU     MONTANT\n";
        $content .= str_repeat("-", $width) . "\n";

        // Produits sur 2 lignes : première ligne = ref + nom, deuxième ligne = QTE PU MONTANT
        foreach ($lines as $line) {
            $ref = $line['ref'] ?? '';
            $label = $line['desc'] ?? '';
            $qty = $line['qty'];
            $pu = number_format($line['subprice'], 2, ',', ' ');
            $total = number_format($line['total_ttc'], 2, ',', ' ');
            // Première ligne (code barre + libellé 1ère partie)
            $lib1 = ($ref ? "($ref)" : "") . $label;
            // Si trop long, coupe après 30
            $lib1a = mb_substr($lib1, 0, 30, 'UTF-8');
            $lib2a = mb_substr($lib1, 30, $width - 2, 'UTF-8');
            $content .= $lib1a . "\n";
            // Deuxième ligne : (suite libellé si besoin) + qte/pu/total aligné à droite
            $ligne2 = ($lib2a ? $lib2a : "");
            $ligne2 = str_pad($ligne2, 20); // 20 caractères à gauche pour libellé suite (vide sinon)
            $ligne2 .= str_pad($qty, 4, " ", STR_PAD_LEFT) . " x ";
            $ligne2 .= str_pad($pu, 7, " ", STR_PAD_LEFT);
            $ligne2 .= str_pad($total, 8, " ", STR_PAD_LEFT);
            $content .= $ligne2 . "\n";
        }

        $content .= str_repeat("-", $width) . "\n";
        // Total aligné à droite
        $content .= str_pad("TOTAL TTC:", $width - 10, " ", STR_PAD_LEFT) . str_pad(number_format($facture['total_ttc'], 2, ',', ' '), 10, " ", STR_PAD_LEFT) . "\n";


        $content .=  $mode_paiement . str_pad(number_format($facture['total_ttc'], 2, ',', ' '), $width - 2, " ", STR_PAD_LEFT) . "\n\n";

        // Section TVA, titres centrés
        $content .= $center("Information sur TVA incluse");
        $content .= str_repeat("-", $width) . "\n";
        $content .= $center("Taux %   Base H.T.   TVA €   TTC €");
        $content .= str_repeat("-", $width) . "\n";
        $tva = [];
        foreach ($lines as $line) {
            $t = $line['tva_tx'] ?? 0;
            $base = $line['total_ht'] ?? ($line['subprice'] * $line['qty']);
            $ttc = $line['total_ttc'] ?? ($line['subprice'] * $line['qty']);
            $tvam = $ttc - $base;
            if (!isset($tva[$t])) $tva[$t] = ['base' => 0, 'tvam' => 0, 'ttc' => 0];
            $tva[$t]['base'] += $base;
            $tva[$t]['tvam'] += $tvam;
            $tva[$t]['ttc'] += $ttc;
        }
        foreach ($tva as $tx => $v) {
            $content .= str_pad(number_format($tx, 2), 8, " ", STR_PAD_LEFT)
                . str_pad(number_format($v['base'], 2, ',', ' '), 10, " ", STR_PAD_LEFT)
                . str_pad(number_format($v['tvam'], 2, ',', ' '), 8, " ", STR_PAD_LEFT)
                . str_pad(number_format($v['ttc'], 2, ',', ' '), 8, " ", STR_PAD_LEFT)
                . "\n";
        }
        $totalBase = array_sum(array_column($tva, 'base'));
        $totalTVA = array_sum(array_column($tva, 'tvam'));
        $totalTTC = array_sum(array_column($tva, 'ttc'));
        $content .= str_repeat("-", $width) . "\n";
        $content .= str_pad("Total:", 8, " ", STR_PAD_LEFT)
            . str_pad(number_format($totalBase, 2, ',', ' '), 10, " ", STR_PAD_LEFT)
            . str_pad(number_format($totalTVA, 2, ',', ' '), 8, " ", STR_PAD_LEFT)
            . str_pad(number_format($totalTTC, 2, ',', ' '), 8, " ", STR_PAD_LEFT) . "\n\n";

        // Footer bien centré
        $content .= $center("Caisse n : 1");
        $content .= $center("REMBOURSEMENT LE JOUR MEME UNIQUEMENT");
        $content .= $center("ECHANGE OU AVOIR SOUS 15 JOURS");
        $content .= $center("(HORS ARTICLE A DATE)");
        $content .= $center("PRODUITS NON UTILISES ET DANS LEUR EMBALLAGE");
        $content .= $center("D'ORIGINE. MERCI ET A BIENTOT");
        $content .= str_repeat("-", $width) . "\n";
        $content .= $center($facture['ref']);


        $connector = null;
        if ($config['type'] === 'windows') {
            // Imprimante partagée Windows (nom de partage, ex: EPSON)
            $connector = new WindowsPrintConnector($config['printer']);
        } elseif ($config['type'] === 'linux') {
            // Chemin Linux : /dev/usb/lp0 ou /dev/usb/lp1 etc
            $connector = new FilePrintConnector($config['printer']);
        } elseif ($config['type'] === 'network') {
            // Imprimante réseau : IP + port (souvent 9100)
            $connector = new NetworkPrintConnector($config['ip'], $config['port'] ?? 9100);
        } else {
            throw new Exception("Type de connecteur non supporté !");
        }

        $printer = new Printer($connector);
        $logo = "../public/assets/logo.png";
        if (is_file($logo) && file_exists($logo)) {
            // $printer->setJustification(Printer::JUSTIFY_CENTER);
            $img = EscposImage::load($logo);
            $printer->bitImage($img);
            $printer->feed(1);
        }
        // $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($content);
        $printer->feed(2);
        if ($config['printer'] != "Printer800") {
            $printer->cut();
        } else {
            $printer->cut(Printer::CUT_PARTIAL);
        }
        $printer->close();
        return $content;
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
                    "default_lang" => "fr_FR",
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
