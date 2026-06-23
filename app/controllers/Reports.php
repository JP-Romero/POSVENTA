<?php
class Reports extends Controller {
    public function __construct() {
        if (!isLoggedIn() || !isAdmin()) {
            redirect('users/login');
        }
        $this->db = new Database;
    }

    public function index() {
        $tab = $_GET['tab'] ?? 'ventas';
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
        
        // Sales of the day
        $this->db->query('SELECT SUM(total) as total FROM ventas WHERE DATE(fecha) = CURDATE()');
        $day_sales = $this->db->single()->total ?? 0;

        // Sales of the month
        $this->db->query('SELECT SUM(total) as total FROM ventas WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())');
        $month_sales = $this->db->single()->total ?? 0;

        // Best sellers
        $this->db->query('SELECT p.nombre, SUM(dv.cantidad) as total_sold
                          FROM detalle_ventas dv
                          INNER JOIN productos p ON dv.id_producto = p.id
                          GROUP BY dv.id_producto
                          ORDER BY total_sold DESC LIMIT 5');
        $best_sellers = $this->db->resultSet();

        // Ventas por día (última semana)
        $this->db->query('SELECT DATE(fecha) as dia, SUM(total) as total, COUNT(*) as count
                          FROM ventas WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                          GROUP BY DATE(fecha) ORDER BY dia ASC');
        $sales_week = $this->db->resultSet();

        // Ventas por método de pago (mes actual)
        $this->db->query('SELECT metodo_pago, SUM(total) as total, COUNT(*) as count
                          FROM ventas WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())
                          GROUP BY metodo_pago');
        $sales_by_payment = $this->db->resultSet();

        // Productos bajo stock
        $this->db->query('SELECT p.*, c.nombre as categoria_nombre
                          FROM productos p
                          INNER JOIN categorias c ON p.id_categoria = c.id
                          WHERE p.stock <= p.stock_minimo AND p.estado = 1
                          ORDER BY p.stock ASC');
        $low_stock = $this->db->resultSet();

        // Productos agotados
        $this->db->query('SELECT p.*, c.nombre as categoria_nombre
                          FROM productos p
                          INNER JOIN categorias c ON p.id_categoria = c.id
                          WHERE p.stock = 0 AND p.estado = 1');
        $out_of_stock = $this->db->resultSet();

        // Menos vendidos
        $this->db->query('SELECT p.nombre, COALESCE(SUM(dv.cantidad), 0) as total_sold
                          FROM productos p
                          LEFT JOIN detalle_ventas dv ON p.id = dv.id_producto
                          LEFT JOIN ventas v ON dv.id_venta = v.id AND MONTH(v.fecha) = MONTH(CURDATE()) AND YEAR(v.fecha) = YEAR(CURDATE())
                          WHERE p.estado = 1
                          GROUP BY p.id
                          ORDER BY total_sold ASC LIMIT 5');
        $least_sellers = $this->db->resultSet();

        // Compras por proveedor (mes actual)
        $this->db->query('SELECT pr.nombre as proveedor, SUM(c.total) as total, COUNT(*) as count
                          FROM compras c
                          INNER JOIN proveedores pr ON c.id_proveedor = pr.id
                          WHERE MONTH(c.fecha) = MONTH(CURDATE()) AND YEAR(c.fecha) = YEAR(CURDATE())
                          GROUP BY pr.id ORDER BY total DESC');
        $purchases_by_provider = $this->db->resultSet();

        // Compras por fecha (último mes)
        $this->db->query('SELECT DATE(fecha) as dia, SUM(total) as total
                          FROM compras WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                          GROUP BY DATE(fecha) ORDER BY dia ASC');
        $purchases_by_date = $this->db->resultSet();

        $data = [
            'activeTab' => $tab,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'day_sales' => $day_sales,
            'month_sales' => $month_sales,
            'best_sellers' => $best_sellers,
            'least_sellers' => $least_sellers,
            'sales_week' => $sales_week,
            'sales_by_payment' => $sales_by_payment,
            'low_stock' => $low_stock,
            'out_of_stock' => $out_of_stock,
            'purchases_by_provider' => $purchases_by_provider,
            'purchases_by_date' => $purchases_by_date,
        ];

        $this->view('reports/index', $data);
    }

    // Export sales report to CSV
    public function exportSales() {
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

        $this->db->query('SELECT v.*, c.nombre as cliente_nombre, u.nombre as usuario_nombre
                          FROM ventas v
                          INNER JOIN clientes c ON v.id_cliente = c.id
                          INNER JOIN usuarios u ON v.id_usuario = u.id
                          WHERE DATE(v.fecha) BETWEEN :inicio AND :fin
                          ORDER BY v.fecha DESC');
        $this->db->bind(':inicio', $fecha_inicio);
        $this->db->bind(':fin', $fecha_fin);
        $sales = $this->db->resultSet();

        $filename = "reporte_ventas_{$fecha_inicio}_a_{$fecha_fin}.csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Factura', 'Fecha', 'Cliente', 'Vendedor', 'Subtotal', 'IVA', 'Total', 'Método Pago']);

        foreach ($sales as $sale) {
            fputcsv($output, [
                $sale->numero_factura,
                date('d/m/Y H:i', strtotime($sale->fecha)),
                $sale->cliente_nombre,
                $sale->usuario_nombre,
                number_format($sale->subtotal, 2, ',', '.'),
                number_format($sale->impuesto, 2, ',', '.'),
                number_format($sale->total, 2, ',', '.'),
                $sale->metodo_pago
            ]);
        }
        fclose($output);
        exit;
    }

    // Export inventory report
    public function exportInventory() {
        $this->db->query('SELECT p.*, c.nombre as categoria_nombre, pr.nombre as proveedor_nombre
                          FROM productos p
                          INNER JOIN categorias c ON p.id_categoria = c.id
                          INNER JOIN proveedores pr ON p.id_proveedor = pr.id
                          ORDER BY p.nombre');
        $products = $this->db->resultSet();

        $filename = "reporte_inventario_" . date('Ymd') . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Código Interno', 'Código Barras', 'Nombre', 'Categoría', 'Proveedor', 'Precio Compra', 'Precio Venta', 'Stock', 'Stock Mínimo', 'Estado', 'Valor Stock']);

        foreach ($products as $p) {
            $valor = $p->stock * $p->precio_compra;
            fputcsv($output, [
                $p->codigo_interno,
                $p->codigo_barras,
                $p->nombre,
                $p->categoria_nombre,
                $p->proveedor_nombre,
                number_format($p->precio_compra, 2, ',', '.'),
                number_format($p->precio_venta, 2, ',', '.'),
                $p->stock,
                $p->stock_minimo,
                $p->estado ? 'Activo' : 'Inactivo',
                number_format($valor, 2, ',', '.')
            ]);
        }
        fclose($output);
        exit;
    }

    // Export purchases report
    public function exportPurchases() {
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

        $this->db->query('SELECT c.*, pr.nombre as proveedor_nombre, u.nombre as usuario_nombre
                          FROM compras c
                          INNER JOIN proveedores pr ON c.id_proveedor = pr.id
                          INNER JOIN usuarios u ON c.id_usuario = u.id
                          WHERE DATE(c.fecha) BETWEEN :inicio AND :fin
                          ORDER BY c.fecha DESC');
        $this->db->bind(':inicio', $fecha_inicio);
        $this->db->bind(':fin', $fecha_fin);
        $purchases = $this->db->resultSet();

        $filename = "reporte_compras_{$fecha_inicio}_a_{$fecha_fin}.csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Comprobante', 'Fecha', 'Proveedor', 'Usuario', 'Total']);

        foreach ($purchases as $c) {
            fputcsv($output, [
                $c->comprobante,
                date('d/m/Y H:i', strtotime($c->fecha)),
                $c->proveedor_nombre,
                $c->usuario_nombre,
                number_format($c->total, 2, ',', '.')
            ]);
        }
        fclose($output);
        exit;
    }
}