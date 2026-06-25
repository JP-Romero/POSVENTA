<?php
// Producción: no mostrar errores al usuario
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/../app/config/config.php';

// Load Helpers
require_once 'helpers/url_helper.php';
require_once 'helpers/session_helper.php';
require_once 'helpers/config_helper.php';

// Establecer zona horaria
date_default_timezone_set('America/Managua');

// Load Composer Autoload
require_once __DIR__ . '/../vendor/autoload.php';

// Autoload Core Libraries
spl_autoload_register(function($className){
  require_once __DIR__ . '/../app/core/' . $className . '.php';
});