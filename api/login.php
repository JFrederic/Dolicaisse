<?php
session_start();
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');
$codes = require 'entrepot_codes.php';

$input = file_get_contents('php://input');
var_dump($input); // Pour déboguer, voir ce qui est reçu
$code = trim($input['code'] ?? '');

$entrepot_id = null;
foreach ($codes as $id => $codeList) {
    if (in_array($code, $codeList, true)) {
        $entrepot_id = $id;
        break;
    }
}

if ($entrepot_id !== null) {
    $_SESSION['entrepot_id'] = $entrepot_id;
    echo json_encode(['success' => true, 'entrepot_id' => $entrepot_id]);
} else {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => "Code invalide", "data"=>json_encode(['code' => $input , 'data' => $codes])]);
}
