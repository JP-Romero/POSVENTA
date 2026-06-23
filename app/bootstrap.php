<?php
  require_once '../app/config/config.php';

  // Autoload Core Libraries
  spl_autoload_register(function($className){
    require_once '../app/core/' . $className . '.php';
  });
