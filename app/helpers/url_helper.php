<?php
  // Simple page redirect
  function redirect($page){
    header('location: ' . URLROOT . '/' . $page);
  }

  // Escape HTML for output
  function h($str){
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
  }
