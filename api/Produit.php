<?php
// api/Produit.php
require_once 'DolibarrApi.php';

class Produit extends DolibarrApi
{
    // Recherche produit par ref (code-barre) ou label (nom)
    public function search($search)
    {
        // On commence par chercher par ref (code-barre)
        $resByRef = $this->call('GET', '/products', [], [
            'sqlfilters' => "(t.ref:=:'$search')"
        ]);
        if (!empty($resByRef)) return $resByRef;
        // Si aucun résultat, on cherche par nom (label) avec LIKE et limite 20
        $resByLabel = $this->call('GET', '/products', [], [
            'sqlfilters' => "(t.label:like:'%$search%')",
            'limit' => 20
        ]);
        return $resByLabel;
    }
}
