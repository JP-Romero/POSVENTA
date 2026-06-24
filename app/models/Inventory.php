<?php
class Inventory {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Get stock status
    public function getStockStatus() {
        $this->db->query('SELECT p.*, c.nombre as categoria_nombre
                          FROM productos p
                          INNER JOIN categorias c ON p.id_categoria = c.id
                          ORDER BY p.stock ASC');
        return $this->db->resultSet();
    }

    // Get low stock products
    public function getLowStock() {
        $this->db->query('SELECT p.*, c.nombre as categoria_nombre
                          FROM productos p
                          INNER JOIN categorias c ON p.id_categoria = c.id
                          WHERE p.stock <= p.stock_minimo');
        return $this->db->resultSet();
    }

    // Get Kardex for a product
    public function getKardex($product_id = null) {
        $sql = 'SELECT m.*, p.nombre as producto_nombre
                FROM movimientos_inventario m
                INNER JOIN productos p ON m.id_producto = p.id';

        if($product_id) {
            $sql .= ' WHERE m.id_producto = :product_id';
        }

        $sql .= ' ORDER BY m.fecha DESC';

        $this->db->query($sql);

        if($product_id) {
            $this->db->bind(':product_id', $product_id);
        }

        return $this->db->resultSet();
    }
}
