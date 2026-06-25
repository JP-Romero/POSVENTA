-- Script to add tables for Z-Report and Cash Register Movements

USE `libreria_db`;

-- Tabla para registrar movimientos de efectivo (Fondo inicial, retiros, pagos)
CREATE TABLE IF NOT EXISTS `movimientos_caja` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `tipo` enum('Entrada','Salida') NOT NULL,
  `concepto` varchar(255) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_movimiento_caja_usuario` (`id_usuario`),
  CONSTRAINT `fk_movimiento_caja_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para guardar el historial de Cortes de Caja (Reportes Z)
CREATE TABLE IF NOT EXISTS `cortes_caja` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL,
  `ventas_brutas` decimal(10,2) NOT NULL DEFAULT 0.00,
  `descuentos` decimal(10,2) NOT NULL DEFAULT 0.00,
  `ventas_netas` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_efectivo` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_tarjeta` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_transferencia` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fondo_inicial` decimal(10,2) NOT NULL DEFAULT 0.00,
  `ingresos_caja` decimal(10,2) NOT NULL DEFAULT 0.00,
  `egresos_caja` decimal(10,2) NOT NULL DEFAULT 0.00,
  `efectivo_esperado` decimal(10,2) NOT NULL DEFAULT 0.00,
  `efectivo_real` decimal(10,2) NOT NULL DEFAULT 0.00,
  `diferencia` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tickets_emitidos` int(11) NOT NULL DEFAULT 0,
  `ticket_promedio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `primer_ticket` varchar(20) DEFAULT NULL,
  `ultimo_ticket` varchar(20) DEFAULT NULL,
  `fecha_corte` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_corte_caja_usuario` (`id_usuario`),
  CONSTRAINT `fk_corte_caja_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
