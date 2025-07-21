<?php
// api/DolibarrApi.php
require_once __DIR__ . '/../load_env.php';
class DolibarrApi {
    private $apiKey;
    private $baseUrl;

    public function __construct() {
        $this->apiKey = $_ENV['DOLIBARR_API_KEY'];
        $this->baseUrl =$_ENV['DOLIBARR_API_URL'];
    }

    public function call($method, $endpoint, $data = [], $query = []) {
        $url = $this->baseUrl . $endpoint;
        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }
        
        $headers = [
            'DOLAPIKEY: ' . $this->apiKey,
            'Content-Type: application/json'
        ];

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
        ];

        if ($method == 'POST' || $method == 'PUT' || $method == 'PATCH') {
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        }

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err) throw new Exception("Curl Error: $err");
        // Modif : si erreur 400+, on retourne false (pas d'exception)
        if ($httpCode >= 400) return false;

        return json_decode($response, true);
    }
}
