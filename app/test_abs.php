<?php
echo __DIR__ . PHP_EOL;
$path = '../app/core/Database.php';
echo "Relative path: $path\n";
$abs = __DIR__ . '/../app/core/Database.php';
echo "Absolute constructed: $abs\n";
if (file_exists($abs)) {
    echo "Absolute path exists\n";
} else {
    echo "Absolute path does not exist\n";
}
echo "realpath of absolute: " . realpath($abs) . PHP_EOL;
?>