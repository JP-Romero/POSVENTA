<?php
  // Simple page redirect
  function redirect($page){
    header('location: ' . URLROOT . '/' . $page);
  }

  // Escape HTML for output
  function h($str){
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
  }

  // Format number as currency
  function fmt($n, $symbol = true){
    $n = floatval($n);
    $formatted = number_format($n, 2, ',', '.');
    if ($symbol) {
      $cfg = getConfig('moneda_simbolo', 'C$');
      return $cfg . ' ' . $formatted;
    }
    return $formatted;
  }

  // Format number short (1K, 1M)
  function fmtShort($n, $symbol = true){
    $n = floatval($n);
    $cfg = getConfig('moneda_simbolo', 'C$');
    $prefix = $symbol ? $cfg . ' ' : '';
    if ($n == 0) return $prefix . '—';
    if (abs($n) >= 1000000) return $prefix . number_format($n / 1000000, 2) . 'M';
    if (abs($n) >= 1000) return $prefix . number_format($n / 1000, 1) . 'K';
    return $prefix . number_format($n, 2, ',', '.');
  }
