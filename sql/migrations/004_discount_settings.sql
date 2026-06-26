-- ============================================
-- Migration 004: Discount Settings
-- ============================================

ALTER TABLE `configuracion`
  ADD COLUMN `descuento` DECIMAL(5,2) DEFAULT 0.00 AFTER `iva_enabled`,
  ADD COLUMN `descuento_enabled` TINYINT(1) DEFAULT 0 AFTER `descuento`;
