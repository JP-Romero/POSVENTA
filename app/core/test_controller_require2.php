<?php
echo __DIR__ . "\n";
echo getcwd() . "\n";
$file = '../app/controllers/Pages.php';
echo "Trying to require: $file\n";
$abs = __DIR__ . '/' . $file;
echo "Absolute path: $abs\n";
if (file_exists($abs)) {
    echo "Absolute file exists\n";
} else {
    echo "Absolute file does NOT exist\n";
}
if (file_exists($file)) {
    echo "Relative file exists\n";
} else {
    echo "Relative file does NOT exist\n";
}
if (is_readable($file)) {
    echo "Relative file readable\n";
} else {
    echo "Relative file NOT readable\n";
}
require_once $file;
echo "Required successfully\n";