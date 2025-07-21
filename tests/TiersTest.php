<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../api/Tiers.php';

class TiersTest extends TestCase {

    public function testSearchTiersByName() {
        $tiers = new Tiers();
        $result = $tiers->search('client001');
        $this->assertIsArray($result);
        if (count($result)) {
            $this->assertArrayHasKey('id', $result[0]);
            $this->assertArrayHasKey('name', $result[0]);
        }
    }

    // public function testCreateTiersMinimal() {
    //     $tiers = new Tiers();
    //     $data = [
    //         "name" => "ClientTestPHPUnit",
    //         "client" => 4 // Obligatoire
    //     ];
    //     $result = $tiers->create($data);
    //     $this->assertIsArray($result);
    //     $this->assertArrayHasKey('id', $result['id']);
    //     $this->assertEquals('ClientTestPHPUnit', $result['name']);
    //     $this->assertEquals(4, $result['client']);
        
    // }
}
