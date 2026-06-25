<?php
require 'app/bootstrap.php';
$db = new Database();

echo "Seeding DB...\n";

// 1. Create a User
$user_pass = password_hash('123456', PASSWORD_DEFAULT);
$db->query("INSERT IGNORE INTO usuarios (id_rol, nombre, usuario, password, estado) VALUES (1, 'Cajero Prueba', 'cajero_test', :pass, 1)");
$db->bind(':pass', $user_pass);
$db->execute();
echo "User created.\n";

// 2. Create 5 Categories
$categories = ['Libros', 'Papelería', 'Tecnología', 'Regalos', 'Arte y Diseño'];
foreach($categories as $cat) {
    $db->query("INSERT IGNORE INTO categorias (nombre, descripcion) VALUES (:n, :d)");
    $db->bind(':n', $cat);
    $db->bind(':d', 'Categoría de ' . $cat);
    $db->execute();
}
echo "Categories created.\n";

// 3. Create 5 Providers
for ($i=1; $i<=5; $i++) {
    $db->query("INSERT IGNORE INTO proveedores (nombre, telefono, correo, direccion) VALUES (:n, :t, :e, :d)");
    $db->bind(':n', 'Proveedor ' . $i);
    $db->bind(':t', '555-000' . $i);
    $db->bind(':e', 'proveedor' . $i . '@ejemplo.com');
    $db->bind(':d', 'Calle Principal ' . $i);
    $db->execute();
}
echo "Providers created.\n";

// 4. Create 5 Clients
for ($i=1; $i<=5; $i++) {
    $db->query("INSERT IGNORE INTO clientes (nombre, telefono, correo, direccion) VALUES (:n, :t, :e, :d)");
    $db->bind(':n', 'Cliente Frecuente ' . $i);
    $db->bind(':t', '888-000' . $i);
    $db->bind(':e', 'cliente' . $i . '@ejemplo.com');
    $db->bind(':d', 'Av. Sur ' . $i);
    $db->execute();
}
echo "Clients created.\n";

// 5. Create 10 Products
// We need to fetch a category and provider id to link them.
$db->query("SELECT id FROM categorias LIMIT 1");
$catId = $db->single()->id;

$db->query("SELECT id FROM proveedores LIMIT 1");
$provId = $db->single()->id;

for ($i=1; $i<=10; $i++) {
    $db->query("INSERT IGNORE INTO productos (id_categoria, id_proveedor, codigo_barras, nombre, descripcion, precio_compra, precio_venta, stock, stock_minimo, estado) 
                VALUES (:cat, :prov, :cod, :nom, :des, :pc, :pv, :st, :sm, 1)");
    $db->bind(':cat', $catId);
    $db->bind(':prov', $provId);
    $db->bind(':cod', 'PROD00' . $i);
    $db->bind(':nom', 'Producto de Prueba ' . $i);
    $db->bind(':des', 'Descripción del producto ' . $i);
    $db->bind(':pc', rand(10, 50));
    $db->bind(':pv', rand(60, 100));
    $db->bind(':st', rand(20, 100));
    $db->bind(':sm', 5);
    $db->execute();
}
echo "Products created.\n";
echo "Seed complete.\n";
?>
