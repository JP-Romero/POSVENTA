<?php
echo __DIR__ . PHP_EOL;
$path = '../app/core/Database.php';
echo $path . PHP_EOL;
echo realpath($path) . PHP_EOL;
if (file_exists($path)) {
    echo 'File exists' . PHP_EOL;
} else {
    echo 'File does not exist' . PHP_EOL;
}
echo 'Is dir? ' . is_dir('../app/core') . PHP_EOL;
echo 'List of ../app/core:' . PHP_EOL;
foreach (scandir('../app/core') as $f) {
    echo "  $f\n";
}