<?php
require 'app/bootstrap.php';
$db = new Database();
$db->query("SELECT id, fecha, total, id_usuario FROM ventas");
$ventas = $db->resultSet();
echo "Ventas:\n";
print_r($ventas);
