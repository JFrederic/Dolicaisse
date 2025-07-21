<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../api/Produit.php';

class ProduitTest extends TestCase {

    public function testSearchProductByRef() {
        $produit = new Produit();
        $result = $produit->search('2002506000018'); // Remplace par un code connu pour un vrai test
        $this->assertIsArray($result);
        if (count($result)) {
            $this->assertArrayHasKey('id', $result[0]);
            $this->assertArrayHasKey('ref', $result[0]);
        }
    }

    public function testSearchProductByLabel() {
        $produit = new Produit();
        $result = $produit->search('casquette001'); // Remplace par un nom connu pour un vrai test
        $this->assertIsArray($result);
        if (count($result)) {
            $this->assertArrayHasKey('id', $result[0]);
            $this->assertArrayHasKey('label', $result[0]);
        }
    }
}
