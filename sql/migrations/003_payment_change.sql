ALTER TABLE ventas
  ADD COLUMN efectivo_recibido DECIMAL(10,2) DEFAULT 0.00 AFTER tasa_cambio,
  ADD COLUMN cambio DECIMAL(10,2) DEFAULT 0.00 AFTER efectivo_recibido;
