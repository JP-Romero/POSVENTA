<?php

// Configuración para entorno de desarrollo
return [
    'app' => [
        'debug' => true,
        'env' => 'development',
    ],
    'database' => [
        'host' => 'localhost',
        'user' => 'root',
        'pass' => '',
        'name' => 'libreria_db',
        'charset' => 'utf8mb4',
    ],
    'session' => [
        'lifetime' => 7200,
        'secure' => false,
        'httponly' => true,
    ],
    'cache' => [
        'enabled' => false,
        'driver' => 'file',
    ],
];
