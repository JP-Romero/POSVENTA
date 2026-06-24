<?php
require_once '../app/config/config.php';

<<<<<<< HEAD
// Load Helpers
require_once 'helpers/url_helper.php';
require_once 'helpers/session_helper.php';
require_once 'helpers/config_helper.php';
=======
  // Load Helpers
  require_once 'helpers/url_helper.php';
  require_once 'helpers/session_helper.php';
  require_once 'helpers/config_helper.php';

  // Load Composer Autoload
  require_once __DIR__ . '/../vendor/autoload.php';
>>>>>>> a6741f722e4afd6f45cdb32385cdef28de967fb2

// Load Composer Autoload
require_once __DIR__ . '/../vendor/autoload.php';

// Autoload Core Libraries
spl_autoload_register(function($className){
  require_once '../app/core/' . $className . '.php';
});
