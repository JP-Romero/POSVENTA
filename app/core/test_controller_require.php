<?php
echo __DIR__ . "\n";
echo getcwd() . "\n";
$file = '../app/controllers/Pages.php';
echo "Trying to require: $file\n";
if (file_exists($file)) {
    echo "File exists\n";
} else {
    echo "File does NOT exist\n";
}
if (is_readable($file)) {
    echo "File is readable\n";
} else {
    echo "File is NOT readable\n";
}
require_once $file;
echo "Required successfully\n";
?>