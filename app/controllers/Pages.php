<?php
  class Pages extends Controller {
    public function __construct(){
      if(!isLoggedIn()){
        redirect('users/login');
      }
    }

    public function index(){
      $this->db = new Database;

      // Totals for dashboard
      $this->db->query('SELECT COUNT(*) as total FROM productos');
      $total_products = $this->db->single()->total;

      $this->db->query('SELECT COUNT(*) as total FROM productos WHERE stock <= stock_minimo');
      $low_stock = $this->db->single()->total;

      $this->db->query('SELECT SUM(total) as total FROM ventas WHERE DATE(fecha) = CURDATE()');
      $daily_sales = $this->db->single()->total ?? 0;

      $this->db->query('SELECT v.*, c.nombre as cliente_nombre FROM ventas v INNER JOIN clientes c ON v.id_cliente = c.id ORDER BY v.fecha DESC LIMIT 5');
      $recent_sales = $this->db->resultSet();

      $data = [
        'title' => 'Panel de Control',
        'total_products' => $total_products,
        'low_stock' => $low_stock,
        'daily_sales' => $daily_sales,
        'recent_sales' => $recent_sales
      ];

      $this->view('pages/index', $data);
    }

    public function about(){
      $data = [
        'title' => 'Sobre Nosotros'
      ];

      $this->view('pages/about', $data);
    }
  }
