<?php
// Producción: no mostrar errores al usuario
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Cargar configuración
require_once __DIR__ . '/../app/config/config.php';

// Cargar Helpers
require_once 'helpers/url_helper.php';
require_once 'helpers/session_helper.php';
require_once 'helpers/config_helper.php';

// Establecer zona horaria
date_default_timezone_set('America/Managua');

// Cargar autoload de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Autoload de clases
spl_autoload_register(function($className) {
    $directories = ['core', 'services', 'models', 'repositories'];
    foreach ($directories as $directory) {
        $file = __DIR__ . '/../app/' . $directory . '/' . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});