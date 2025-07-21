<?php 

function CallAPI($method, $apikey, $url, $data = false)
{
    $curl = curl_init();
    $httpheader = ['DOLAPIKEY: '.$apikey];

    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            $httpheader[] = "Content-Type:application/json";

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            break;
        case "PUT":

	    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
            $httpheader[] = "Content-Type:application/json";

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // Optional Authentication:
	//    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	//    curl_setopt($curl, CURLOPT_USERPWD, "username:password");

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $httpheader);

    $result = curl_exec($curl);

    curl_close($curl);
var_dump($result);
    return $result;
}

CallAPI('GET', '7dd7557dad2a7aa657b502e6c8a9448a7abf49f1', 'https://97packs.mobisoft.fr/dolibarr/htdocs/api/index.php/products', ['sqlfilters' => '(t.ref:=:"PACK-001")']);