<?php
// SEULEMENT EN MODE DEV
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, DOLAPIKEY");
// public/index.php
require_once '../api/Tiers.php';
require_once '../api/Produit.php';
require_once '../api/Facture.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? null;
// var_dump($action);die()
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
    // etc, ajoute les autres routes ici
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Bad action']);
        break;
}
