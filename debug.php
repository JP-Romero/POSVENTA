<?php
$path = '../app/core/Database.php';
echo "Path: $path\n";
echo "Exists? " . file_exists($path) ? 'yes' : 'no' . "\n";
echo "is_readable? " . is_readable($path) ? 'yes' : 'no' . "\n";
echo "Resolved realpath: " . realpath($path) . "\n";
try {
    require_once $path;
    echo "Required successfully\n";
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
?>