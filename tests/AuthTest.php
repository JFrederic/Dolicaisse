<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../api/Auth.php';

class AuthTest extends TestCase {

    public function testLogin() {
        $auth = new Auth();
        $entity = 1; // Remplace avec une entité valide
        $username = '97packadmin    '; // Remplace avec un user valide
        $password = '974admin'; // Remplace avec le vrai mot de passe
        $this->expectNotToPerformAssertions();
        $result = $auth->login($username, $password, $entity);
        $this->assertArrayHasKey('success', $result);
        // PAS TESTABLE SANS CREDENTIELS VALIDE
    }
}
