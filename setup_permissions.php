<?php
require_once __DIR__ . '/app/config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "
    CREATE TABLE IF NOT EXISTS `usuario_permisos` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `id_usuario` int(11) NOT NULL,
      `modulo` varchar(50) NOT NULL COMMENT 'Nombre del módulo: products, categories, providers, purchases, sales, inventory, reports, settings, users',
      `acceso` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1: Tiene acceso, 0: No tiene acceso',
      PRIMARY KEY (`id`),
      UNIQUE KEY `usuario_modulo_unique` (`id_usuario`, `modulo`),
      KEY `fk_permiso_usuario` (`id_usuario`),
      CONSTRAINT `fk_permiso_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $pdo->exec($sql);
    echo "Tabla usuario_permisos creada exitosamente.\n";
    
    // Insertar permisos por defecto para el admin
    $stmt = $pdo->prepare("INSERT IGNORE INTO `usuario_permisos` (`id_usuario`, `modulo`, `acceso`) VALUES (1, 'products', 1)");
    $stmt->execute();
    $stmt = $pdo->prepare("INSERT IGNORE INTO `usuario_permisos` (`id_usuario`, `modulo`, `acceso`) VALUES (1, 'categories', 1)");
    $stmt->execute();
    $stmt = $pdo->prepare("INSERT IGNORE INTO `usuario_permisos` (`id_usuario`, `modulo`, `acceso`) VALUES (1, 'providers', 1)");
    $stmt->execute();
    $stmt = $pdo->prepare("INSERT IGNORE INTO `usuario_permisos` (`id_usuario`, `modulo`, `acceso`) VALUES (1, 'purchases', 1)");
    $stmt->execute();
    $stmt = $pdo->prepare("INSERT IGNORE INTO `usuario_permisos` (`id_usuario`, `modulo`, `acceso`) VALUES (1, 'sales', 1)");
    $stmt->execute();
    $stmt = $pdo->prepare("INSERT IGNORE INTO `usuario_permisos` (`id_usuario`, `modulo`, `acceso`) VALUES (1, 'inventory', 1)");
    $stmt->execute();
    $stmt = $pdo->prepare("INSERT IGNORE INTO `usuario_permisos` (`id_usuario`, `modulo`, `acceso`) VALUES (1, 'reports', 1)");
    $stmt->execute();
    $stmt = $pdo->prepare("INSERT IGNORE INTO `usuario_permisos` (`id_usuario`, `modulo`, `acceso`) VALUES (1, 'settings', 1)");
    $stmt->execute();
    $stmt = $pdo->prepare("INSERT IGNORE INTO `usuario_permisos` (`id_usuario`, `modulo`, `acceso`) VALUES (1, 'users', 1)");
    $stmt->execute();
    
    echo "Permisos por defecto creados para administrador.\n";
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}