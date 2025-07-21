<?php

// load_env.php
$envFile = __DIR__ . './.env';
if (file_exists($envFile)) {
    $lines = file($envFile);
    foreach ($lines as $line) {
        if (preg_match('/^\s*([\w_]+)\s*=\s*(.*)\s*$/', $line, $matches)) {
            $_ENV[$matches[1]] = trim($matches[2]);
            putenv($matches[1] . '=' . trim($matches[2]));
        }
    }
}
