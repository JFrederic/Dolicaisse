<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../api/Facture.php';
require_once __DIR__ . '/../api/Tiers.php';

class FactureTest extends TestCase {

    private $tiersId;

    protected function setUp(): void {
        // Crée un client de test pour associer la facture
        $tiers = new Tiers();
        $client = $tiers->create([
            "name" => "ClientFactureTest",
            "client" => 1
        ]);
        $this->tiersId = $client['id'];
    }

    public function testCreateAndSearchInvoice() {
        $facture = new Facture();

        // Champs minimaux pour création de facture
        $data = [
            "socid" => 10,
            "type" => 0, // 0 = standard, 1 = replacement, 2 = credit
            "date" => date('Y-m-d'),
            "lines" => [
                [
                    "desc" => "Test produit",
                    "subprice" => 10,
                    "qty" => 1,
                    "tva_tx" => 20
                ]
            ]
        ];
        $result = $facture->create($data);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);

        // Recherche par numéro
        $searchResult = $facture->search($result['ref']);
        $this->assertIsArray($searchResult);
        $this->assertNotEmpty($searchResult);
    }

    public function testGetTotalPaid() {
        $facture = new Facture();
        $total = $facture->getTotalPaid();
        $this->assertIsNumeric($total);
        $this->assertGreaterThanOrEqual(0, $total);
    }
}
