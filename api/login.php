<?php

header('Content-Type: application/json');
$warehouses = require 'entrepot_codes.php';

$input = json_decode(file_get_contents('php://input'), true);
$code = strtoupper(trim($input['code'] ?? ''));

if (
    isset($warehouses[$code])
) {
    session_start();
    $_SESSION['entrepot_id'] = $warehouses[$code]['entrepot'];
    echo json_encode([
        'success' => true,
        'entrepot_id' => $warehouses[$code]['entrepot'],
        'nom' => $warehouses[$code]['nom']
    ]);
} else {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => "Code ou entrepot incorrect"]);
}
