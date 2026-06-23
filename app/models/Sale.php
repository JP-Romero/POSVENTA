<?php
class Sale {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getSales() {
        $this->db->query('SELECT v.*, u.nombre as usuario_nombre, c.nombre as cliente_nombre
                          FROM ventas v
                          INNER JOIN usuarios u ON v.id_usuario = u.id
                          INNER JOIN clientes c ON v.id_cliente = c.id
                          ORDER BY v.fecha DESC');
        return $this->db->resultSet();
    }

    public function saveSale($data) {
        try {
            $this->db->beginTransaction();

            // 1. Insert into ventas
            $this->db->query('INSERT INTO ventas (id_usuario, id_cliente, numero_factura, subtotal, impuesto, total, metodo_pago)
                              VALUES (:id_usuario, :id_cliente, :numero_factura, :subtotal, :impuesto, :total, :metodo_pago)');

            $this->db->bind(':id_usuario', $_SESSION['user_id']);
            $this->db->bind(':id_cliente', $data['id_cliente']);
            $this->db->bind(':numero_factura', $data['numero_factura']);
            $this->db->bind(':subtotal', $data['subtotal']);
            $this->db->bind(':impuesto', $data['impuesto']);
            $this->db->bind(':total', $data['total']);
            $this->db->bind(':metodo_pago', $data['metodo_pago']);

            if (!$this->db->execute()) return false;

            $this->db->query('SELECT LAST_INSERT_ID() as id');
            $sale_id = $this->db->single()->id;

            // 2. Insert details and update stock
            foreach ($data['items'] as $item) {
                $this->db->query('INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_venta, descuento)
                                  VALUES (:id_venta, :id_producto, :cantidad, :precio_venta, :descuento)');
                $this->db->bind(':id_venta', $sale_id);
                $this->db->bind(':id_producto', $item['id_producto']);
                $this->db->bind(':cantidad', $item['cantidad']);
                $this->db->bind(':precio_venta', $item['precio_venta']);
                $this->db->bind(':descuento', $item['descuento']);
                $this->db->execute();

                // Update stock
                $this->db->query('UPDATE productos SET stock = stock - :cantidad WHERE id = :id_producto');
                $this->db->bind(':cantidad', $item['cantidad']);
                $this->db->bind(':id_producto', $item['id_producto']);
                $this->db->execute();

                // Record movement
                $this->db->query('INSERT INTO movimientos_inventario (id_producto, tipo, cantidad, motivo)
                                  VALUES (:id_producto, "Salida", :cantidad, "Venta")');
                $this->db->bind(':id_producto', $item['id_producto']);
                $this->db->bind(':cantidad', $item['cantidad']);
                $this->db->execute();
            }

            $this->db->endTransaction();
            return $sale_id;
        } catch (Exception $e) {
            $this->db->cancelTransaction();
            return false;
        }
    }

    public function getNextInvoiceNumber() {
        $this->db->query('SELECT MAX(id) as last_id FROM ventas');
        $row = $this->db->single();
        $next = ($row->last_id ?? 0) + 1;
        return 'FAC-' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }
}
