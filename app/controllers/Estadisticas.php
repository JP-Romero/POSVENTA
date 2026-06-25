<?php
class Estadisticas extends Controller {
    private $saleModel;

    public function __construct() {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->saleModel = $this->model('Sale');
        $this->db = new Database;
    }

    public function index() {
        $this->view('pos/estadisticas');
    }

    public function obtenerDatos() {
        $periodo = $_GET['periodo'] ?? 'hoy';
        
        $fecha_fin = date('Y-m-d 23:59:59');
        $fecha_inicio = date('Y-m-d 00:00:00');

        switch ($periodo) {
            case 'hoy':
                $fecha_inicio = date('Y-m-d 00:00:00');
                break;
            case 'semana':
                // Inicio de la semana (Lunes)
                $fecha_inicio = date('Y-m-d 00:00:00', strtotime('monday this week'));
                break;
            case 'mes':
                $fecha_inicio = date('Y-m-01 00:00:00');
                break;
            case '3meses':
                $fecha_inicio = date('Y-m-d 00:00:00', strtotime('-3 months'));
                break;
            case '6meses':
                $fecha_inicio = date('Y-m-d 00:00:00', strtotime('-6 months'));
                break;
            case 'anio':
                $fecha_inicio = date('Y-01-01 00:00:00');
                break;
        }

        $resumen = $this->saleModel->getResumenVentas($fecha_inicio, $fecha_fin);
        $categorias = $this->saleModel->getVentasPorCategoria($fecha_inicio, $fecha_fin);
        $mas_vendidos = $this->saleModel->getProductosMasVendidos($fecha_inicio, $fecha_fin, 10);

        // Calcular piezas totales para el resumen general
        $piezas_vendidas = 0;
        foreach ($categorias as $cat) {
            $piezas_vendidas += $cat->piezas;
        }

        echo json_encode([
            'resumen' => [
                'total_recaudado' => $resumen->ventas_netas ?? 0,
                'tickets_cobrados' => $resumen->tickets_emitidos ?? 0,
                'piezas_vendidas' => $piezas_vendidas
            ],
            'categorias' => $categorias,
            'mas_vendidos' => $mas_vendidos
        ]);
        exit;
    }
}
