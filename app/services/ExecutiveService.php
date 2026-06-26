<?php
/**
 * ExecutiveService.php - Capa de Negocio del Centro Ejecutivo
 * 
 * Orquesta la lógica financiera y de Business Intelligence.
 * Centraliza cálculos de rentabilidad, márgenes, KPIs y valoración.
 * 
 * @author POSVENTA Team
 * @package POSVENTA\Services
 * @version 1.0.0
 */

class ExecutiveService {
    private $repository;
    private $saleModel;
    
    public function __construct() {
        $this->repository = new ExecutiveRepository();
        $this->saleModel = new Sale();
    }
    
    /**
     * Obtiene datos del período anterior para comparativa
     */
    public function getPreviousPeriod($start, $end) {
        $startDate = new DateTime($start);
        $endDate = new DateTime($end);
        $interval = $startDate->diff($endDate);
        $days = max(1, $interval->days + 1);
        
        $prevEnd = (new DateTime($start))->modify('-1 day')->format('Y-m-d');
        $prevStart = (new DateTime($prevEnd))->modify("-{$days} days")->format('Y-m-d');
        
        return $this->repository->queryExecutiveSummary($prevStart, $prevEnd);
    }
    
    /**
     * Obtiene análisis completo de inventario
     */
    public function getInventoryAnalysis() {
        $valuation = $this->repository->queryInventoryValuation();
        $turnover = $this->repository->queryInventoryTurnover();
        $withoutMovement = $this->repository->queryProductsWithoutMovement(30);
        $costoVentasAnual = $this->repository->queryAnnualCOGS();
        
        $capitalInvertido = floatval($valuation['capital_invertido'] ?? 0);
        $valorComercial = floatval($valuation['valor_comercial'] ?? 0);
        $costoVentas30 = floatval($turnover['costo_ventas'] ?? 0);
        
        $rotacion = $capitalInvertido > 0 ? ($costoVentas30 / $capitalInvertido) : 0;
        $rotacionAnual = $capitalInvertido > 0 ? ($costoVentasAnual / $capitalInvertido) : 0;
        
        return [
            'capital_invertido'      => $capitalInvertido,
            'valor_comercial'        => $valorComercial,
            'ganancia_potencial'     => floatval($valuation['ganancia_potencial'] ?? 0),
            'inventario_disponible'  => intval($valuation['inventario_disponible'] ?? 0),
            'total_productos'        => intval($valuation['total_productos'] ?? 0),
            'comprometido'           => 0,
            'rotacion'               => round($rotacion, 2),
            'rotacion_anual'         => round($rotacionAnual, 2),
            'sin_movimiento'         => intval($withoutMovement),
            'dias_rotacion'          => $rotacion > 0 ? round(30 / $rotacion, 1) : 0
        ];
    }
    
    /**
     * Obtiene el resumen ejecutivo completo para un período
     */
    public function getExecutiveSummary($start, $end) {
        $summary = $this->repository->queryExecutiveSummary($start, $end);
        $previous = $this->getPreviousPeriod($start, $end);
        
        $ventasNetas = floatval($summary['ventas_netas'] ?? 0);
        $costoVentas = floatval($summary['costo_ventas'] ?? 0);
        $utilidadBruta = floatval($summary['utilidad_bruta'] ?? 0);
        $tickets = intval($summary['tickets'] ?? 0);
        
        $margenComercial = $ventasNetas > 0 ? ($utilidadBruta / $ventasNetas) * 100 : 0;
        $ticketPromedio = $tickets > 0 ? ($ventasNetas / $tickets) : 0;
        $costoPorcentaje = $ventasNetas > 0 ? ($costoVentas / $ventasNetas) * 100 : 0;
        
        $crecimiento = $previous['ventas_netas'] > 0 
            ? (($ventasNetas - $previous['ventas_netas']) / $previous['ventas_netas']) * 100 
            : 0;
        
        return [
            'ventas_totales'     => floatval($summary['ventas_totales'] ?? 0),
            'ventas_netas'       => $ventasNetas,
            'costo_ventas'       => $costoVentas,
            'utilidad_bruta'     => $utilidadBruta,
            'utilidad_neta'      => $utilidadBruta,
            'margen_comercial'   => $margenComercial,
            'costo_porcentaje'   => $costoPorcentaje,
            'ticket_promedio'    => $ticketPromedio,
            'clientes_atendidos' => intval($summary['clientes_atendidos'] ?? 0),
            'productos_vendidos' => intval($summary['productos_vendidos'] ?? 0),
            'descuentos_totales' => floatval($summary['descuentos_totales'] ?? 0),
            'tickets'            => $tickets,
            'crecimiento'        => $crecimiento,
            'periodo_anterior'   => $previous
        ];
    }

    /**
     * Obtiene rentabilidad por entidad con paginación
     */
    public function getProfitabilityByEntity($entity, $start, $end, $page = 1, $perPage = 20, $sort = 'utilidad', $order = 'DESC') {
        $method = 'queryProfitabilityBy' . ucfirst($entity);
        
        if (!method_exists($this->repository, $method)) {
            $entity = 'producto';
            $method = 'queryProfitabilityByProducto';
        }
        
        $results = $this->repository->$method($start, $end, $sort, $order, $page, $perPage);
        $total = $this->repository->getTotalProfitabilityRows($entity, $start, $end);
        
        foreach ($results as &$row) {
            $row['margen'] = $row['ventas_netas'] > 0 
                ? ($row['utilidad'] / $row['ventas_netas']) * 100 
                : 0;
        }
        
        return [
            'data' => $results,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => max(1, ceil($total / $perPage))
        ];
    }
    
    /**
     * Obtiene datos completos de KPIs
     */
    public function getKPIData() {
        $kpis = $this->repository->queryKPIs();
        
        $roi = $kpis['costo_ventas'] > 0 
            ? ($kpis['utilidad_bruta'] / $kpis['costo_ventas']) * 100 
            : 0;
        
        $margenBruto = $kpis['ventas_netas'] > 0 
            ? ($kpis['utilidad_bruta'] / $kpis['ventas_netas']) * 100 
            : 0;
        
        $margenNeto = $margenBruto * 0.7;
        $topProducts = $this->repository->queryTopProfitableProducts(10);
        $bottomProducts = $this->repository->queryLeastProfitableProducts(10);
        $growth = $this->repository->queryGrowthRates();
        
        return [
            'roi'                => round($roi, 2),
            'margen_bruto'       => round($margenBruto, 2),
            'margen_neto'        => round($margenNeto, 2),
            'rotacion'           => round($kpis['rotacion'] ?? 0, 2),
            'ticket_promedio'    => round($kpis['ticket_promedio'] ?? 0, 2),
            'crecimiento_mensual' => round($growth['mensual'] ?? 0, 2),
            'crecimiento_anual'   => round($growth['anual'] ?? 0, 2),
            'top_products'       => $topProducts,
            'bottom_products'    => $bottomProducts,
            'ventas_netas'       => floatval($kpis['ventas_netas'] ?? 0),
            'utilidad_bruta'     => floatval($kpis['utilidad_bruta'] ?? 0),
            'costo_ventas'       => floatval($kpis['costo_ventas'] ?? 0),
            'tickets'            => intval($kpis['tickets'] ?? 0)
        ];
    }
    
    /**
     * Obtiene datos de tendencias para gráficos
     */
    public function getTrendsData($start, $end) {
        $dailyTrends = $this->repository->querySalesTrends($start, $end);
        $monthlyComparison = $this->repository->queryMonthlyComparison(date('Y'));
        $hourlySales = $this->repository->queryHourlySales($start, $end);
        
        $labels = [];
        $ventas = [];
        $costos = [];
        $utilidades = [];
        
        foreach ($dailyTrends as $t) {
            $labels[] = date('d/m/Y', strtotime($t['dia']));
            $ventas[] = floatval($t['ventas']);
            $costos[] = floatval($t['costos']);
            $utilidades[] = floatval($t['utilidad']);
        }
        
        return [
            'daily' => ['labels' => $labels, 'ventas' => $ventas, 'costos' => $costos, 'utilidades' => $utilidades],
            'monthly' => $monthlyComparison,
            'hourly' => $hourlySales
        ];
    }
    
    /**
     * Auditoría financiera
     */
    public function getFinancialAudit($filters = []) {
        return $this->repository->queryFinancialAudit($filters);
    }
    
    /**
     * Registra evento de auditoría
     */
    public function registerAuditEvent($type, $description, $userId = null) {
        return $this->repository->registerAuditEvent($type, $description, $userId);
    }
    
    /**
     * Calcula márgenes
     */
    public function calculateMargins($sales, $costs, $discounts = 0) {
        $netSales = $sales - $discounts;
        $profit = $netSales - $costs;
        $margin = $netSales > 0 ? ($profit / $netSales) * 100 : 0;
        return ['net_sales' => $netSales, 'profit' => $profit, 'margin_pct' => round($margin, 2)];
    }
    
    /**
     * Exporta rentabilidad a CSV
     */
    public function exportProfitabilityToCSV($entity, $start, $end) {
        $data = $this->getProfitabilityByEntity($entity, $start, $end, 1, 10000);
        $filename = "rentabilidad_{$entity}_{$start}_a_{$end}.csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Entidad', 'Cantidad Vendida', 'Ventas Netas', 'Costo Total', 'Descuentos', 'Utilidad', 'Margen %']);
        
        foreach ($data['data'] as $row) {
            fputcsv($output, [
                $row['nombre'], $row['cantidad_vendida'],
                number_format($row['ventas_netas'], 2, '.', ''),
                number_format($row['costo'], 2, '.', ''),
                number_format($row['descuentos'], 2, '.', ''),
                number_format($row['utilidad'], 2, '.', ''),
                number_format($row['margen'], 2, '.', '')
            ]);
        }
        fclose($output);
        exit;
    }
}

