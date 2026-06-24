<?php
echo __DIR__ . PHP_EOL;
$path = '../app/core/Database.php';
echo $path . PHP_EOL;
if (file_exists($path)) {
    echo 'File exists' . PHP_EOL;
} else {
    echo 'File does not exist' . PHP_EOL;
}
@require_once $path;
echo 'After require' . PHP_EOL;