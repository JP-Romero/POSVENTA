<?php
  class Pages extends Controller {
    public function __construct(){
      if(!isLoggedIn()){
        redirect('users/login');
      }
      $this->db = new Database;
    }

    public function index(){
      // Render the main dashboard (button grid) - no data needed
      $this->view('pages/index');
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
