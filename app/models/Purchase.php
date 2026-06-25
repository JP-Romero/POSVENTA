<?php
class Purchase {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Get all purchases
    public function getPurchases() {
        $this->db->query('SELECT c.*, p.nombre as proveedor_nombre
                          FROM compras c
                          INNER JOIN proveedores p ON c.id_proveedor = p.id
                          ORDER BY c.fecha DESC');
        return $this->db->resultSet();
    }

    // Save purchase and update inventory
    public function savePurchase($data) {
        try {
            $this->db->beginTransaction();

            // 1. Insert into compras
            $this->db->query('INSERT INTO compras (id_proveedor, id_usuario, total, comprobante) VALUES (:id_proveedor, :id_usuario, :total, :comprobante)');
            $this->db->bind(':id_proveedor', $data['id_proveedor']);
            $this->db->bind(':id_usuario', $_SESSION['user_id']);
            $this->db->bind(':total', $data['total']);
            $this->db->bind(':comprobante', $data['comprobante']);

            if (!$this->db->execute()) {
                return false;
            }

            // Get last insert ID
            $this->db->query('SELECT LAST_INSERT_ID() as id');
            $purchase_id = $this->db->single()->id;

            // 2. Insert into detalle_compras and update stock
            foreach ($data['items'] as $item) {
                // Insert detail
                $this->db->query('INSERT INTO detalle_compras (id_compra, id_producto, cantidad, precio_compra) VALUES (:id_compra, :id_producto, :cantidad, :precio_compra)');
                $this->db->bind(':id_compra', $purchase_id);
                $this->db->bind(':id_producto', $item['id_producto']);
                $this->db->bind(':cantidad', $item['cantidad']);
                $this->db->bind(':precio_compra', $item['precio_compra']);
                $this->db->execute();

                // Update product stock
                $this->db->query('UPDATE productos SET stock = stock + :cantidad, precio_compra = :precio_compra WHERE id = :id_producto');
                $this->db->bind(':cantidad', $item['cantidad']);
                $this->db->bind(':precio_compra', $item['precio_compra']);
                $this->db->bind(':id_producto', $item['id_producto']);
                $this->db->execute();

                // Record inventory movement
                $this->db->query('INSERT INTO movimientos_inventario (id_producto, tipo, cantidad, motivo) VALUES (:id_producto, "Entrada", :cantidad, "Compra")');
                $this->db->bind(':id_producto', $item['id_producto']);
                $this->db->bind(':cantidad', $item['cantidad']);
                $this->db->execute();
            }

            $this->db->endTransaction();
            return true;
        } catch (Exception $e) {
            $this->db->cancelTransaction();
            return false;
        }
    }
}
