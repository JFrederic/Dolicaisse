<?php
// api/Auth.php
require_once 'DolibarrApi.php';

class Auth extends DolibarrApi {
    public function login($username, $password, $entity) {
        return $this->call('POST', '/login', [
            'login' => $username,
            'password' => $password,
            'entity' => $entity
        ]);
    }
}
