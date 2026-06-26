-- ============================================
-- Migration 002: Settings & Payments Upgrade
-- ============================================

-- 1. Add new columns to configuracion
ALTER TABLE `configuracion`
  ADD COLUMN `iva_enabled` TINYINT(1) DEFAULT 1 AFTER `iva`,
  ADD COLUMN `exchange_rate` DECIMAL(10,4) DEFAULT 36.5000 AFTER `iva_enabled`,
  ADD COLUMN `payment_methods` VARCHAR(255) DEFAULT 'efectivo,tarjeta,dolar,mixto' AFTER `exchange_rate`;

-- 2. Update ventas for split payments, USD, and mixed
ALTER TABLE `ventas`
  ADD COLUMN `pago_efectivo` DECIMAL(10,2) DEFAULT 0.00 AFTER `metodo_pago`,
  ADD COLUMN `pago_tarjeta` DECIMAL(10,2) DEFAULT 0.00 AFTER `pago_efectivo`,
  ADD COLUMN `pago_dolar` DECIMAL(10,2) DEFAULT 0.00 AFTER `pago_tarjeta`,
  ADD COLUMN `pago_dolar_equiv` DECIMAL(10,2) DEFAULT 0.00 AFTER `pago_dolar`,
  ADD COLUMN `total_dolares` DECIMAL(10,2) DEFAULT 0.00 AFTER `pago_dolar_equiv`,
  ADD COLUMN `tasa_cambio` DECIMAL(10,4) DEFAULT 36.5000 AFTER `total_dolares`;

-- 3. Update metodo_pago enum to include Dolar and Mixto
ALTER TABLE `ventas`
  MODIFY COLUMN `metodo_pago` VARCHAR(20) NOT NULL DEFAULT 'Efectivo';
