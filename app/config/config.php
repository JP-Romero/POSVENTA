<?php
// Database params (entorno sobrescribe con variables de entorno)
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'libreria_db');

// App Root
define('APPROOT', dirname(dirname(__FILE__)));
// URL Root (sobrescribir en producción con URL real)
define('URLROOT', getenv('URLROOT') ?: 'http://localhost/POSVENTA');
// Site Name
define('SITENAME', 'Librería Pos');
// App Version
define('APPVERSION', '1.0.0');
