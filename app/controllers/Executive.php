<?php
/**
 * ExecutiveController.php - Centro Ejecutivo (Business Intelligence)
 * Panel financiero exclusivo para Administrador/Dueño.
 * 
 * @author POSVENTA Team
 * @version 1.0.0
 */

class Executive extends Controller {
    private $executiveService;
    private $executiveModel;

    public function __construct() {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        
        if (!isAdmin()) {
            http_response_code(403);
            flash('access_error', 'Acceso Denegado: Módulo exclusivo para administradores.', 'alert alert-danger');
            redirect('pages/dashboard');
            exit;
        }

        $this->executiveService = new ExecutiveService();
        $this->executiveModel = $this->model('ExecutiveAnalytic');
    }

    public function index() {
        redirect('executive/resume');
    }

    /**
     * Vista 1: Resumen Ejecutivo con múltiples períodos
     */
    public function resume() {
        $start = $_GET['start'] ?? date('Y-m-01');
        $end = $_GET['end'] ?? date('Y-m-t');
        
        $hoy = $this->executiveService->getExecutiveSummary(date('Y-m-d'), date('Y-m-d'));
        $semana = $this->executiveService->getExecutiveSummary(date('Y-m-d', strtotime('monday this week')), date('Y-m-d'));
        $mes = $this->executiveService->getExecutiveSummary($start, $end);
        $anio = $this->executiveService->getExecutiveSummary(date('Y-01-01'), date('Y-m-d'));

        $data = [
            'active_tab' => 'resume',
            'start' => $start,
            'end' => $end,
            'summary' => $mes,
            'hoy' => $hoy,
            'semana' => $semana,
            'anio' => $anio
        ];
        
        $this->view('executive/resume', $data);
    }

    /**
     * Vista 2: Capital e Inventario
     */
    public function inventory() {
        $analysis = $this->executiveService->getInventoryAnalysis();
        $data = ['active_tab' => 'inventory', 'analysis' => $analysis];
        $this->view('executive/inventory', $data);
    }

    /**
     * Vista 3: Rentabilidad (paginada y ordenable)
     */
    public function profitability() {
        $start = $_GET['start'] ?? date('Y-m-01');
        $end = $_GET['end'] ?? date('Y-m-t');
        $entity = $_GET['entity'] ?? 'producto';
        $page = intval($_GET['page'] ?? 1);
        $sort = $_GET['sort'] ?? 'utilidad';
        $order = $_GET['order'] ?? 'DESC';

        $report = $this->executiveService->getProfitabilityByEntity($entity, $start, $end, $page, 20, $sort, $order);

        $data = [
            'active_tab' => 'profitability',
            'start' => $start, 'end' => $end,
            'entity' => $entity,
            'report' => $report['data'],
            'total' => $report['total'],
            'page' => $report['page'],
            'totalPages' => $report['totalPages'],
            'sort' => $sort, 'order' => $order
        ];
        $this->view('executive/profitability', $data);
    }

    /**
     * Vista 4: KPIs
     */
    public function kpi() {
        $kpis = $this->executiveService->getKPIData();
        $data = ['active_tab' => 'kpi', 'kpis' => $kpis];
        $this->view('executive/kpi', $data);
    }

    /**
     * Vista 5: Tendencias
     */
    public function trends() {
        $data = [
            'active_tab' => 'trends',
            'start' => $_GET['start'] ?? date('Y-m-01'),
            'end' => $_GET['end'] ?? date('Y-m-t')
        ];
        $this->view('executive/trends', $data);
    }

    /**
     * Vista 6: Auditoría Financiera
     */
    public function audit() {
        $filters = [];
        if (!empty($_GET['start'])) $filters['start'] = $_GET['start'];
        if (!empty($_GET['end'])) $filters['end'] = $_GET['end'];
        if (!empty($_GET['user_id'])) $filters['user_id'] = intval($_GET['user_id']);

        $logs = $this->executiveService->getFinancialAudit($filters);
        $data = ['active_tab' => 'audit', 'logs' => $logs];
        $this->view('executive/audit', $data);
    }

    /**
     * API: Datos para Chart.js
     */
    public function apiChartData() {
        header('Content-Type: application/json');
        $start = $_GET['start'] ?? date('Y-m-01');
        $end = $_GET['end'] ?? date('Y-m-t');
        $trends = $this->executiveService->getTrendsData($start, $end);

        echo json_encode([
            'labels' => $trends['daily']['labels'],
            'datasets' => [
                ['label' => 'Ventas Netas', 'data' => $trends['daily']['ventas'], 'borderColor' => '#0d6efd', 'backgroundColor' => 'rgba(13, 110, 253, 0.1)'],
                ['label' => 'Costos', 'data' => $trends['daily']['costos'], 'borderColor' => '#dc3545', 'backgroundColor' => 'rgba(220, 53, 69, 0.1)'],
                ['label' => 'Utilidad Bruta', 'data' => $trends['daily']['utilidades'], 'borderColor' => '#198754', 'backgroundColor' => 'rgba(25, 135, 84, 0.1)']
            ]
        ]);
        exit;
    }

    /**
     * API: Datos rápidos AJAX
     */
    public function apiResumeData() {
        header('Content-Type: application/json');
        $summary = $this->executiveService->getExecutiveSummary(
            $_GET['start'] ?? date('Y-m-01'),
            $_GET['end'] ?? date('Y-m-t')
        );
        echo json_encode($summary);
        exit;
    }

    /**
     * Exportar Rentabilidad a CSV
     */
    public function exportProfitability() {
        $start = $_GET['start'] ?? date('Y-m-01');
        $end = $_GET['end'] ?? date('Y-m-t');
        $entity = $_GET['entity'] ?? 'producto';
        $this->executiveService->exportProfitabilityToCSV($entity, $start, $end);
    }
}
