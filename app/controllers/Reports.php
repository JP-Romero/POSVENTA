<?php
class Reports extends Controller {
    public function __construct() {
        if (!isLoggedIn() || !isAdmin()) {
            redirect('users/login');
        }
        $this->db = new Database;
    }

    public function index() {
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

        $data = [
            'day_sales' => $day_sales,
            'month_sales' => $month_sales,
            'best_sellers' => $best_sellers
        ];

        $this->view('reports/index', $data);
    }
}
