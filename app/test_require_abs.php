<?php
// Simulate autoloader using __DIR__
$className = 'Database';
$file = __DIR__ . '/../app/core/' . $className . '.php';
echo "Trying to require: $file\n";
echo "Current directory: " . getcwd() . "\n";
echo "__DIR__ of this script: " . __DIR__ . "\n";
if (file_exists($file)) {
    echo "File exists (according to file_exists)\n";
} else {
    echo "File does NOT exist (according to file_exists)\n";
}
if (is_readable($file)) {
    echo "File is readable\n";
} else {
    echo "File is NOT readable\n";
}
echo "Attempting require...\n";
require_once $file;
echo "Required successfully\n";
?>