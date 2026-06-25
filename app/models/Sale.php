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

    public function getInvoiceNumber($sale_id) {
        $this->db->query('SELECT numero_factura FROM ventas WHERE id = :id');
        $this->db->bind(':id', $sale_id);
        $row = $this->db->single();
        return $row->numero_factura ?? null;
    }

    // --- MÉTODOS PARA REPORTE Z Y ESTADÍSTICAS ---

    public function getResumenVentas($fecha_inicio, $fecha_fin) {
        $this->db->query("SELECT 
                            COUNT(id) as tickets_emitidos,
                            MIN(numero_factura) as primer_ticket,
                            MAX(numero_factura) as ultimo_ticket,
                            SUM(total) as ventas_netas,
                            (SELECT SUM(descuento) FROM detalle_ventas dv INNER JOIN ventas v2 ON dv.id_venta = v2.id WHERE v2.fecha >= :f1 AND v2.fecha <= :f2) as descuentos
                          FROM ventas 
                          WHERE fecha >= :fecha_inicio AND fecha <= :fecha_fin");
        $this->db->bind(':fecha_inicio', $fecha_inicio);
        $this->db->bind(':fecha_fin', $fecha_fin);
        $this->db->bind(':f1', $fecha_inicio);
        $this->db->bind(':f2', $fecha_fin);
        return $this->db->single();
    }

    public function getVentasPorMetodoPago($fecha_inicio, $fecha_fin) {
        $this->db->query("SELECT metodo_pago, SUM(total) as total
                          FROM ventas 
                          WHERE fecha >= :fecha_inicio AND fecha <= :fecha_fin
                          GROUP BY metodo_pago");
        $this->db->bind(':fecha_inicio', $fecha_inicio);
        $this->db->bind(':fecha_fin', $fecha_fin);
        return $this->db->resultSet();
    }

    public function getVentasPorCategoria($fecha_inicio, $fecha_fin) {
        $this->db->query("SELECT c.nombre as categoria, SUM(dv.cantidad * dv.precio_venta - dv.descuento) as total, SUM(dv.cantidad) as piezas
                          FROM detalle_ventas dv
                          INNER JOIN ventas v ON dv.id_venta = v.id
                          INNER JOIN productos p ON dv.id_producto = p.id
                          INNER JOIN categorias c ON p.id_categoria = c.id
                          WHERE v.fecha >= :fecha_inicio AND v.fecha <= :fecha_fin
                          GROUP BY c.id
                          ORDER BY total DESC");
        $this->db->bind(':fecha_inicio', $fecha_inicio);
        $this->db->bind(':fecha_fin', $fecha_fin);
        return $this->db->resultSet();
    }

    public function getProductosMasVendidos($fecha_inicio, $fecha_fin, $limit = 10) {
        $this->db->query("SELECT p.nombre as producto, SUM(dv.cantidad) as cantidad, SUM(dv.cantidad * dv.precio_venta - dv.descuento) as total
                          FROM detalle_ventas dv
                          INNER JOIN ventas v ON dv.id_venta = v.id
                          INNER JOIN productos p ON dv.id_producto = p.id
                          WHERE v.fecha >= :fecha_inicio AND v.fecha <= :fecha_fin
                          GROUP BY p.id
                          ORDER BY cantidad DESC
                          LIMIT :limit");
        $this->db->bind(':fecha_inicio', $fecha_inicio);
        $this->db->bind(':fecha_fin', $fecha_fin);
        $this->db->bind(':limit', $limit, 'int');
        return $this->db->resultSet();
    }
}
