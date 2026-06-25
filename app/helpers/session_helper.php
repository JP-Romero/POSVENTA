<?php
  // Sesión segura: configuración antes de iniciar
  if (session_status() === PHP_SESSION_NONE) {
      ini_set('session.cookie_httponly', 1);
      ini_set('session.cookie_samesite', 'Strict');
      ini_set('session.use_strict_mode', 1);
      ini_set('session.gc_maxlifetime', 7200);
      session_start();
  }

  // Flash message helper (XSS-safe)
  function flash($name = '', $message = '', $class = 'alert alert-success'){
    if(!empty($name)){
      if(!empty($message) && empty($_SESSION[$name])){
        $_SESSION[$name] = $message;
        $_SESSION[$name. '_class'] = $class;
      } elseif(empty($message) && !empty($_SESSION[$name])){
        $class = !empty($_SESSION[$name. '_class']) ? $_SESSION[$name. '_class'] : '';
        echo '<div class="'.htmlspecialchars($class, ENT_QUOTES, 'UTF-8').'" id="msg-flash">'.htmlspecialchars($_SESSION[$name], ENT_QUOTES, 'UTF-8').'</div>';
        unset($_SESSION[$name]);
        unset($_SESSION[$name. '_class']);
      }
    }
  }

  function isLoggedIn(){
    return isset($_SESSION['user_id']);
  }

  function isAdmin(){
    return isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 1;
  }
  
  // Check if user has access to a specific module
  function canAccess($module){
    if (isAdmin()) {
      return true;
    }
    
    if (!isLoggedIn()) {
      return false;
    }
    
    require_once APPROOT . '/models/User.php';
    $userModel = new User;
    return $userModel->canAccessModule($_SESSION['user_id'], $module);
  }

  // CSRF Token generation and validation
  function generateCsrfToken(){
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
  }

  function validateCsrfToken($token){
    if (empty($_SESSION['csrf_token'])) return false;
    return hash_equals($_SESSION['csrf_token'], $token);
  }

  function csrfField(){
    return '<input type="hidden" name="csrf_token" value="'.generateCsrfToken().'">';
  }
