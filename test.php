<?php 

require_once 'api/DolibarrApi.php';
require_once './load_env.php';


echo "Début du test\n";


try {
    $api = new DolibarrApi();
    echo "DolibarrApi instanciée\n";
   

    // Recherche d'un produit par ref ou nom
    $searchParams = [
        'sqlfilters' => "(t.ref:=:'test')" // Remplacez PROD_REF par la référence recherchée
        // ou par exemple : 'sqlfilters' => "(t.label:=:'NOM_PRODUIT')"
    ];
    $products = $api->call('GET', 'products', $searchParams);
    echo "Résultat de la recherche produit :\n";
    print_r($products);

} catch (Exception $e) {
    echo "Erreur Dolibarr : " . $e->getMessage();
}
echo "Fin du test\n";
