<?php

// Configuración para entorno de producción
return [
    'app' => [
        'debug' => false,
        'env' => 'production',
    ],
    'database' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: '',
        'name' => getenv('DB_NAME') ?: 'libreria_db',
        'charset' => 'utf8mb4',
    ],
    'session' => [
        'lifetime' => 7200,
        'secure' => true,
        'httponly' => true,
    ],
    'cache' => [
        'enabled' => true,
        'driver' => 'file',
    ],
];
