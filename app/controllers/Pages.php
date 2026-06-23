<?php
  class Pages extends Controller {
    public function __construct(){
      if(!isLoggedIn()){
        redirect('users/login');
      }
      $this->db = new Database;
    }

    public function index(){
      // Totals for dashboard
      $this->db->query('SELECT COUNT(*) as total FROM productos');
      $total_products = $this->db->single()->total;

      $this->db->query('SELECT COUNT(*) as total FROM productos WHERE stock <= stock_minimo');
      $low_stock = $this->db->single()->total;

      $this->db->query('SELECT SUM(total) as total FROM ventas WHERE DATE(fecha) = CURDATE()');
      $daily_sales = $this->db->single()->total ?? 0;

      $this->db->query('SELECT v.*, c.nombre as cliente_nombre FROM ventas v INNER JOIN clientes c ON v.id_cliente = c.id ORDER BY v.fecha DESC LIMIT 5');
      $recent_sales = $this->db->resultSet();

      // Chart: Ventas últimos 7 días
      $this->db->query('SELECT DATE(fecha) as dia, SUM(total) as total FROM ventas WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY DATE(fecha) ORDER BY dia ASC');
      $sales_7days = $this->db->resultSet();

      // Chart: Top 5 categorías más vendidas
      $this->db->query('SELECT c.nombre as categoria, SUM(dv.cantidad) as total_vendido
                        FROM detalle_ventas dv
                        INNER JOIN productos p ON dv.id_producto = p.id
                        INNER JOIN categorias c ON p.id_categoria = c.id
                        GROUP BY c.id ORDER BY total_vendido DESC LIMIT 5');
      $top_categories = $this->db->resultSet();

      // Chart: Ventas por método de pago
      $this->db->query('SELECT metodo_pago, COUNT(*) as cantidad, SUM(total) as total
                        FROM ventas WHERE DATE(fecha) = CURDATE() GROUP BY metodo_pago');
      $payment_methods = $this->db->resultSet();

      // Prepare chart data
      $chart_sales_labels = [];
      $chart_sales_data = [];
      for ($i = 6; $i >= 0; $i--) {
          $date = date('Y-m-d', strtotime("-$i days"));
          $chart_sales_labels[] = date('d/m', strtotime($date));
          $found = false;
          foreach ($sales_7days as $s) {
              if ($s->dia === $date) {
                  $chart_sales_data[] = floatval($s->total);
                  $found = true;
                  break;
              }
          }
          if (!$found) $chart_sales_data[] = 0;
      }

      $chart_cat_labels = [];
      $chart_cat_data = [];
      foreach ($top_categories as $c) {
          $chart_cat_labels[] = $c->categoria;
          $chart_cat_data[] = intval($c->total_vendido);
      }

      $chart_pay_labels = [];
      $chart_pay_data = [];
      foreach ($payment_methods as $p) {
          $chart_pay_labels[] = $p->metodo_pago;
          $chart_pay_data[] = floatval($p->total);
      }

      $data = [
        'title' => 'Panel de Control',
        'total_products' => $total_products,
        'low_stock' => $low_stock,
        'daily_sales' => $daily_sales,
        'recent_sales' => $recent_sales,
        'chart_sales_labels' => json_encode($chart_sales_labels),
        'chart_sales_data' => json_encode($chart_sales_data),
        'chart_cat_labels' => json_encode($chart_cat_labels),
        'chart_cat_data' => json_encode($chart_cat_data),
        'chart_pay_labels' => json_encode($chart_pay_labels),
        'chart_pay_data' => json_encode($chart_pay_data)
      ];

      $this->view('pages/index', $data);
    }

    public function about(){
      $data = [
        'title' => 'Sobre Nosotros'
      ];

      $this->view('pages/about', $data);
    }

    public function apiDashboardSummary(){
      header('Content-Type: application/json');
      
      // Ventas del mes
      $this->db->query('SELECT SUM(total) as total FROM ventas WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())');
      $month_sales = $this->db->single()->total ?? 0;
      
      // Tickets hoy
      $this->db->query('SELECT COUNT(*) as total FROM ventas WHERE DATE(fecha) = CURDATE()');
      $today_tickets = $this->db->single()->total ?? 0;
      
      // Promedio ticket hoy
      $this->db->query('SELECT AVG(total) as avg FROM ventas WHERE DATE(fecha) = CURDATE()');
      $avg_ticket = $this->db->single()->avg ?? 0;
      
      // Productos vendidos hoy
      $this->db->query('SELECT SUM(dv.cantidad) as total FROM detalle_ventas dv INNER JOIN ventas v ON dv.id_venta = v.id WHERE DATE(v.fecha) = CURDATE()');
      $products_sold = $this->db->single()->total ?? 0;

      echo json_encode([
        'success' => true,
        'month_sales' => fmt($month_sales),
        'today_tickets' => $today_tickets,
        'avg_ticket' => fmt($avg_ticket),
        'products_sold' => $products_sold
      ]);
    }
  }
