<?php
// SEULEMENT EN MODE DEV
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, DOLAPIKEY");
// public/index.php
require_once '../api/Tiers.php';
require_once '../api/Produit.php';
require_once '../api/Facture.php';
require_once '../api/Devis.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? null;
// var_dump($action);die();
switch ($action) {
    case 'search_tiers':
        $tiers = new Tiers();
        echo json_encode($tiers->search($_GET['q']));
        break;
    case 'create_tiers':
        $tiers = new Tiers();
        echo json_encode($tiers->create(json_decode(file_get_contents('php://input'), true)));
        break;
    case 'search_product':
        $produit = new Produit();
        echo json_encode($produit->search($_GET['q']));
        break;
    case 'search_invoice':
        $facture = new Facture();
        echo json_encode($facture->search($_GET['q']));
        break;
    case 'create_invoice':
        $facture = new Facture();
        $input = json_decode(file_get_contents('php://input'), true);
        echo json_encode($facture->createFullInvoice($input));
        break;
    case 'journal_factures':
        $dateDebut = $_GET['date_debut'] ?? date('Y-m-d');
        $dateFin = $_GET['date_fin'] ?? date('Y-m-d');
        $facture = new Facture();
        $result = $facture->getFacturesByPeriode($dateDebut, $dateFin);
        echo json_encode($result);
        break;
    case 'search_download_facture_pdf':
        $facture = new Facture();
        $invoiceId = $_GET['invoiceId'] ?? 0;
        $file = $facture->getPdf($invoiceId);
        if ($file && file_exists($file)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="facture_' . $invoiceId . '.pdf"');
            readfile($file);
            // Optionnel : unlink($file); // supprime le fichier temporaire si tu veux
            exit;
        } else {
            http_response_code(404);
            echo "PDF non trouvé.";
        }
        break;
    case 'download_facture_pdf':
        $facture = new Facture();
        $invoiceId = $_GET['invoiceId'] ?? 0;
        $file = $facture->getPdf($invoiceId);
        if ($file && file_exists($file)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="facture_' . $invoiceId . '.pdf"');
            readfile($file);
            // Optionnel : unlink($file); // supprime le fichier temporaire si tu veux
            exit;
        } else {
            http_response_code(404);
            echo "PDF non trouvé.";
        }
        break;

    case 'download_ticket':
        $facture = new Facture();
        $invoiceId = $_GET['invoiceId'] ?? 0;
        $ticketTxt = $facture->generateTicketText($invoiceId, ['type' => 'windows', 'printer' => 'TM-T81']);
        if ($ticketTxt) {
            header('Content-Type: text/plain; charset=UTF-8');
            header('Content-Disposition: attachment; filename="ticket_' . $invoiceId . '.txt"');
            echo $ticketTxt;
            exit;
        }
        http_response_code(404);
        echo "Ticket introuvable.";
        break;
    case 'create_devis':
        $devis = new Devis();
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $devis->createFullDevis($data);
        echo json_encode($result);
        break;

    case 'search_devis':
        $search = $_GET['search'] ?? '';
        $result = $devis->search($search);
        echo json_encode($result);
        break;


    // etc, ajoute les autres routes ici
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Bad action']);
        break;
}
