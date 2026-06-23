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
        // Use hardcoded currency symbol as fallback
        // In a real implementation, this would come from configuration
        $currency_symbol = 'C$'; // Córdoba symbol
        return $currency_symbol . ' ' . $formatted;
    }
    return $formatted;
}

// Format number short (1K, 1M)
function fmtShort($n, $symbol = true){
    $n = floatval($n);
    // Use hardcoded currency symbol as fallback
    $currency_symbol = 'C$'; // Córdoba symbol
    $prefix = $symbol ? $currency_symbol . ' ' : '';
    if ($n == 0) return $prefix . '—';
    if (abs($n) >= 1000000) return $prefix . number_format($n / 1000000, 2) . 'M';
    if (abs($n) >= 1000) return $prefix . number_format($n / 1000, 1) . 'K';
    return $prefix . number_format($n, 2, ',', '.');
}