<?php
/**
 * ExecutiveRepository.php - Capa de Acceso a Datos del Centro Ejecutivo
 * Centraliza todas las consultas SQL optimizadas del módulo.
 * 
 * @author POSVENTA Team
 * @version 1.0.0
 */

class ExecutiveRepository {
    private $db;
    
    public function __construct() {
        $this->db = new Database;
    }
    
    /**
     * Resumen ejecutivo: una sola consulta agrupada
     */
    public function queryExecutiveSummary($start, $end) {
        $sql = "SELECT 
                    COUNT(DISTINCT v.id) as tickets,
                    SUM(dv.cantidad) as productos_vendidos,
                    COUNT(DISTINCT v.id_cliente) as clientes_atendidos,
                    SUM(dv.cantidad * dv.precio_venta) as ventas_totales,
                    SUM(dv.descuento) as descuentos_totales,
                    SUM(dv.cantidad * dv.costo) as costo_ventas,
                    SUM((dv.cantidad * dv.precio_venta) - dv.descuento) as ventas_netas,
                    SUM((dv.cantidad * dv.precio_venta) - dv.descuento - (dv.cantidad * dv.costo)) as utilidad_bruta
                FROM detalle_ventas dv
                INNER JOIN ventas v ON dv.id_venta = v.id
                WHERE DATE(v.fecha) BETWEEN :start AND :end";
        $this->db->query($sql);
        $this->db->bind(':start', $start);
        $this->db->bind(':end', $end);
        $res = $this->db->single();
        
        return $res ? [
            'tickets' => intval($res->tickets ?? 0),
            'productos_vendidos' => intval($res->productos_vendidos ?? 0),
            'clientes_atendidos' => intval($res->clientes_atendidos ?? 0),
            'ventas_totales' => floatval($res->ventas_totales ?? 0),
            'descuentos_totales' => floatval($res->descuentos_totales ?? 0),
            'costo_ventas' => floatval($res->costo_ventas ?? 0),
            'ventas_netas' => floatval($res->ventas_netas ?? 0),
            'utilidad_bruta' => floatval($res->utilidad_bruta ?? 0)
        ] : [];
    }
    
    public function queryInventoryValuation() {
        $sql = "SELECT COUNT(*) as total_productos, SUM(stock) as inventario_disponible,
                    SUM(stock * precio_compra) as capital_invertido,
                    SUM(stock * precio_venta) as valor_comercial,
                    SUM(stock * (precio_venta - precio_compra)) as ganancia_potencial
                FROM productos WHERE estado = 1";
        $this->db->query($sql);
        $res = $this->db->single();
        return $res ? [
            'total_productos' => intval($res->total_productos ?? 0),
            'inventario_disponible' => intval($res->inventario_disponible ?? 0),
            'capital_invertido' => floatval($res->capital_invertido ?? 0),
            'valor_comercial' => floatval($res->valor_comercial ?? 0),
            'ganancia_potencial' => floatval($res->ganancia_potencial ?? 0)
        ] : [];
    }
    
    public function queryInventoryTurnover() {
        $sql = "SELECT SUM(dv.cantidad * dv.costo) as costo_ventas
                FROM detalle_ventas dv INNER JOIN ventas v ON dv.id_venta = v.id
                WHERE v.fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        $this->db->query($sql);
        $res = $this->db->single();
        return ['costo_ventas' => floatval($res->costo_ventas ?? 0)];
    }
    
    public function queryProductsWithoutMovement($days = 30) {
        $sql = "SELECT COUNT(*) as total FROM productos p
                WHERE p.estado = 1 AND p.id NOT IN (
                    SELECT dv.id_producto FROM detalle_ventas dv
                    INNER JOIN ventas v ON dv.id_venta = v.id
                    WHERE v.fecha >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                ) AND p.id NOT IN (
                    SELECT dc.id_producto FROM detalle_compras dc
                    INNER JOIN compras c ON dc.id_compra = c.id
                    WHERE c.fecha >= DATE_SUB(CURDATE(), INTERVAL :days2 DAY)
                )";
        $this->db->query($sql);
        $this->db->bind(':days', $days);
        $this->db->bind(':days2', $days);
        $res = $this->db->single();
        return intval($res->total ?? 0);
    }
    
    public function queryAnnualCOGS() {
        $sql = "SELECT SUM(dv.cantidad * dv.costo) as costo_ventas
                FROM detalle_ventas dv INNER JOIN ventas v ON dv.id_venta = v.id
                WHERE YEAR(v.fecha) = YEAR(CURDATE())";
        $this->db->query($sql);
        $res = $this->db->single();
        return floatval($res->costo_ventas ?? 0);
    }

    // ========== PROFITABILITY QUERIES ==========
    
    private function buildProfitabilityQuery($selectFields, $joins, $groupBy, $start, $end, $orderBy, $limit, $offset) {
        $sql = "SELECT $selectFields, SUM(dv.cantidad) as cantidad_vendida,
                    SUM(dv.cantidad * dv.precio_venta) as ventas,
                    SUM(dv.descuento) as descuentos,
                    SUM((dv.cantidad * dv.precio_venta) - dv.descuento) as ventas_netas,
                    SUM(dv.cantidad * dv.costo) as costo,
                    SUM((dv.cantidad * dv.precio_venta) - dv.descuento - (dv.cantidad * dv.costo)) as utilidad
                FROM detalle_ventas dv
                INNER JOIN ventas v ON dv.id_venta = v.id
                $joins
                WHERE DATE(v.fecha) BETWEEN :start AND :end
                GROUP BY $groupBy
                ORDER BY $orderBy LIMIT :limit OFFSET :offset";
        $this->db->query($sql);
        $this->db->bind(':start', $start);
        $this->db->bind(':end', $end);
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        return $this->db->resultSetArray();
    }
    
    public function queryProfitabilityByProducto($start, $end, $sort = 'utilidad', $order = 'DESC', $page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;
        $orderBy = $this->sortField($sort, $order);
        return $this->buildProfitabilityQuery('p.nombre', 'INNER JOIN productos p ON dv.id_producto = p.id', 'p.id, p.nombre', $start, $end, $orderBy, $perPage, $offset);
    }
    
    public function queryProfitabilityByCategoria($start, $end, $sort = 'utilidad', $order = 'DESC', $page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;
        $orderBy = $this->sortField($sort, $order);
        return $this->buildProfitabilityQuery('c.nombre', 'INNER JOIN productos p ON dv.id_producto = p.id INNER JOIN categorias c ON p.id_categoria = c.id', 'c.id, c.nombre', $start, $end, $orderBy, $perPage, $offset);
    }
    
    public function queryProfitabilityByVendedor($start, $end, $sort = 'utilidad', $order = 'DESC', $page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;
        $orderBy = $this->sortField($sort, $order);
        return $this->buildProfitabilityQuery('u.nombre', 'INNER JOIN usuarios u ON v.id_usuario = u.id', 'u.id, u.nombre', $start, $end, $orderBy, $perPage, $offset);
    }
    
    public function queryProfitabilityByCliente($start, $end, $sort = 'utilidad', $order = 'DESC', $page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;
        $orderBy = $this->sortField($sort, $order);
        return $this->buildProfitabilityQuery('cli.nombre', 'INNER JOIN clientes cli ON v.id_cliente = cli.id', 'cli.id, cli.nombre', $start, $end, $orderBy, $perPage, $offset);
    }
    
    public function queryProfitabilityByProveedor($start, $end, $sort = 'utilidad', $order = 'DESC', $page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;
        $orderBy = $this->sortField($sort, $order);
        return $this->buildProfitabilityQuery('pr.nombre', 'INNER JOIN productos p ON dv.id_producto = p.id INNER JOIN proveedores pr ON p.id_proveedor = pr.id', 'pr.id, pr.nombre', $start, $end, $orderBy, $perPage, $offset);
    }
    
    public function queryProfitabilityByMarca($start, $end, $sort = 'utilidad', $order = 'DESC', $page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;
        $orderBy = $this->sortField($sort, $order);
        return $this->buildProfitabilityQuery("COALESCE(p.marca, 'Sin Marca') as nombre", 'INNER JOIN productos p ON dv.id_producto = p.id', 'p.marca', $start, $end, $orderBy, $perPage, $offset);
    }
    
    public function getTotalProfitabilityRows($entity, $start, $end) {
        $joins = [
            'producto' => 'INNER JOIN productos p ON dv.id_producto = p.id',
            'categoria' => 'INNER JOIN productos p ON dv.id_producto = p.id INNER JOIN categorias c ON p.id_categoria = c.id',
            'vendedor' => '', 'cliente' => '',
            'proveedor' => 'INNER JOIN productos p ON dv.id_producto = p.id INNER JOIN proveedores pr ON p.id_proveedor = pr.id',
            'marca' => 'INNER JOIN productos p ON dv.id_producto = p.id'
        ];
        $groupBy = [
            'producto' => 'p.id', 'categoria' => 'c.id', 'vendedor' => 'v.id_usuario',
            'cliente' => 'v.id_cliente', 'proveedor' => 'pr.id', 'marca' => 'p.marca'
        ];
        $join = $joins[$entity] ?? $joins['producto'];
        $group = $groupBy[$entity] ?? $groupBy['producto'];
        $sql = "SELECT COUNT(*) as total FROM (SELECT 1 FROM detalle_ventas dv
                INNER JOIN ventas v ON dv.id_venta = v.id $join
                WHERE DATE(v.fecha) BETWEEN :start AND :end GROUP BY $group) as sub";
        $this->db->query($sql);
        $this->db->bind(':start', $start);
        $this->db->bind(':end', $end);
        $res = $this->db->single();
        return intval($res->total ?? 0);
    }
    
    // ========== KPI QUERIES ==========
    
    public function queryKPIs() {
        $sql = "SELECT SUM(dv.cantidad * dv.precio_venta) as ventas_totales,
                    SUM(dv.descuento) as descuentos,
                    SUM((dv.cantidad * dv.precio_venta) - dv.descuento) as ventas_netas,
                    SUM(dv.cantidad * dv.costo) as costo_ventas,
                    SUM((dv.cantidad * dv.precio_venta) - dv.descuento - (dv.cantidad * dv.costo)) as utilidad_bruta,
                    COUNT(DISTINCT v.id) as tickets
                FROM detalle_ventas dv INNER JOIN ventas v ON dv.id_venta = v.id
                WHERE MONTH(v.fecha) = MONTH(CURDATE()) AND YEAR(v.fecha) = YEAR(CURDATE())";
        $this->db->query($sql);
        $res = $this->db->single();
        $ventasNetas = floatval($res->ventas_netas ?? 0);
        $tickets = intval($res->tickets ?? 0);
        $capital = $this->queryInventoryValuation();
        return [
            'ventas_netas' => $ventasNetas, 'utilidad_bruta' => floatval($res->utilidad_bruta ?? 0),
            'costo_ventas' => floatval($res->costo_ventas ?? 0), 'tickets' => $tickets,
            'ticket_promedio' => $tickets > 0 ? $ventasNetas / $tickets : 0,
            'rotacion' => $capital['capital_invertido'] > 0 ? floatval($res->costo_ventas ?? 0) / $capital['capital_invertido'] : 0
        ];
    }
    
    public function queryTopProfitableProducts($limit = 10) {
        $sql = "SELECT p.nombre, SUM((dv.cantidad * dv.precio_venta) - dv.descuento - (dv.cantidad * dv.costo)) as utilidad,
                    SUM(dv.cantidad) as vendidos
                FROM detalle_ventas dv INNER JOIN ventas v ON dv.id_venta = v.id
                INNER JOIN productos p ON dv.id_producto = p.id
                WHERE YEAR(v.fecha) = YEAR(CURDATE())
                GROUP BY p.id ORDER BY utilidad DESC LIMIT :lim";
        $this->db->query($sql);
        $this->db->bind(':lim', $limit);
        return $this->db->resultSetArray();
    }
    
    public function queryLeastProfitableProducts($limit = 10) {
        $sql = "SELECT p.nombre, SUM((dv.cantidad * dv.precio_venta) - dv.descuento - (dv.cantidad * dv.costo)) as utilidad,
                    SUM(dv.cantidad) as vendidos
                FROM detalle_ventas dv INNER JOIN ventas v ON dv.id_venta = v.id
                INNER JOIN productos p ON dv.id_producto = p.id
                WHERE YEAR(v.fecha) = YEAR(CURDATE())
                GROUP BY p.id ORDER BY utilidad ASC LIMIT :lim";
        $this->db->query($sql);
        $this->db->bind(':lim', $limit);
        return $this->db->resultSetArray();
    }
    
    public function queryGrowthRates() {
        $sql = "SELECT SUM((dv.cantidad * dv.precio_venta) - dv.descuento) as ventas FROM detalle_ventas dv INNER JOIN ventas v ON dv.id_venta = v.id WHERE MONTH(v.fecha) = MONTH(CURDATE()) AND YEAR(v.fecha) = YEAR(CURDATE())";
        $this->db->query($sql);
        $currentMonth = floatval($this->db->single()->ventas ?? 0);
        $sql = "SELECT SUM((dv.cantidad * dv.precio_venta) - dv.descuento) as ventas FROM detalle_ventas dv INNER JOIN ventas v ON dv.id_venta = v.id WHERE MONTH(v.fecha) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(v.fecha) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";
        $this->db->query($sql);
        $prevMonth = floatval($this->db->single()->ventas ?? 0);
        $sql = "SELECT SUM((dv.cantidad * dv.precio_venta) - dv.descuento) as ventas FROM detalle_ventas dv INNER JOIN ventas v ON dv.id_venta = v.id WHERE YEAR(v.fecha) = YEAR(CURDATE())";
        $this->db->query($sql);
        $currentYear = floatval($this->db->single()->ventas ?? 0);
        $sql = "SELECT SUM((dv.cantidad * dv.precio_venta) - dv.descuento) as ventas FROM detalle_ventas dv INNER JOIN ventas v ON dv.id_venta = v.id WHERE YEAR(v.fecha) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 YEAR))";
        $this->db->query($sql);
        $prevYear = floatval($this->db->single()->ventas ?? 0);
        return ['mensual' => $prevMonth > 0 ? (($currentMonth - $prevMonth) / $prevMonth) * 100 : 0,
            'anual' => $prevYear > 0 ? (($currentYear - $prevYear) / $prevYear) * 100 : 0,
            'mes_actual' => $currentMonth, 'mes_anterior' => $prevMonth,
            'anio_actual' => $currentYear, 'anio_anterior' => $prevYear];
    }
    
    // ========== TREND QUERIES ==========
    
    public function querySalesTrends($start, $end) {
        $sql = "SELECT DATE(v.fecha) as dia,
                    SUM((dv.cantidad * dv.precio_venta) - dv.descuento) as ventas,
                    SUM(dv.cantidad * dv.costo) as costos,
                    SUM((dv.cantidad * dv.precio_venta) - dv.descuento - (dv.cantidad * dv.costo)) as utilidad
                FROM detalle_ventas dv INNER JOIN ventas v ON dv.id_venta = v.id
                WHERE DATE(v.fecha) BETWEEN :start AND :end
                GROUP BY DATE(v.fecha) ORDER BY dia ASC";
        $this->db->query($sql);
        $this->db->bind(':start', $start);
        $this->db->bind(':end', $end);
        return $this->db->resultSetArray();
    }
    
    public function queryMonthlyComparison($year) {
        $sql = "SELECT MONTH(v.fecha) as mes,
                    SUM((dv.cantidad * dv.precio_venta) - dv.descuento) as ventas,
                    SUM(dv.cantidad * dv.costo) as costos,
                    SUM((dv.cantidad * dv.precio_venta) - dv.descuento - (dv.cantidad * dv.costo)) as utilidad
                FROM detalle_ventas dv INNER JOIN ventas v ON dv.id_venta = v.id
                WHERE YEAR(v.fecha) = :year
                GROUP BY MONTH(v.fecha) ORDER BY mes ASC";
        $this->db->query($sql);
        $this->db->bind(':year', $year);
        return $this->db->resultSetArray();
    }
    
    public function queryHourlySales($start, $end) {
        $sql = "SELECT HOUR(v.fecha) as hora,
                    COUNT(DISTINCT v.id) as tickets,
                    SUM((dv.cantidad * dv.precio_venta) - dv.descuento) as ventas
                FROM detalle_ventas dv INNER JOIN ventas v ON dv.id_venta = v.id
                WHERE DATE(v.fecha) BETWEEN :start AND :end
                GROUP BY HOUR(v.fecha) ORDER BY hora ASC";
        $this->db->query($sql);
        $this->db->bind(':start', $start);
        $this->db->bind(':end', $end);
        return $this->db->resultSetArray();
    }
    
    // ========== AUDIT QUERIES ==========
    
    public function queryFinancialAudit($filters = []) {
        $where = "WHERE dv.descuento > 0";
        $params = [];
        
        if (!empty($filters['start'])) {
            $where .= " AND v.fecha >= :start";
            $params[':start'] = $filters['start'];
        }
        if (!empty($filters['end'])) {
            $where .= " AND v.fecha <= :end";
            $params[':end'] = $filters['end'];
        }
        if (!empty($filters['user_id'])) {
            $where .= " AND v.id_usuario = :user_id";
            $params[':user_id'] = $filters['user_id'];
        }
        
        $sql = "SELECT v.fecha, v.numero_factura, u.nombre as usuario,
                    p.nombre as producto, dv.cantidad, dv.descuento,
                    (dv.cantidad * dv.precio_venta) as subtotal, dv.costo
                FROM detalle_ventas dv
                INNER JOIN ventas v ON dv.id_venta = v.id
                INNER JOIN usuarios u ON v.id_usuario = u.id
                INNER JOIN productos p ON dv.id_producto = p.id
                $where ORDER BY v.fecha DESC LIMIT 200";
        $this->db->query($sql);
        foreach ($params as $key => $val) {
            $this->db->bind($key, $val);
        }
        return $this->db->resultSetArray();
    }
    
    public function registerAuditEvent($type, $description, $userId = null) {
        // Placeholder for future audit_log table
        return true;
    }
    
    // ========== HELPERS ==========
    
    private function sortField($sort, $order) {
        $allowed = ['nombre', 'cantidad_vendida', 'ventas', 'ventas_netas', 'costo', 'descuentos', 'utilidad', 'margen'];
        $sort = in_array($sort, $allowed) ? $sort : 'utilidad';
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
        
        // Map to actual SQL field names
        $fieldMap = [
            'nombre' => 'nombre',
            'cantidad_vendida' => 'cantidad_vendida',
            'ventas' => 'SUM(dv.cantidad * dv.precio_venta)',
            'ventas_netas' => 'SUM((dv.cantidad * dv.precio_venta) - dv.descuento)',
            'costo' => 'SUM(dv.cantidad * dv.costo)',
            'descuentos' => 'SUM(dv.descuento)',
            'utilidad' => 'SUM((dv.cantidad * dv.precio_venta) - dv.descuento - (dv.cantidad * dv.costo))',
            'margen' => 'SUM((dv.cantidad * dv.precio_venta) - dv.descuento - (dv.cantidad * dv.costo))'
        ];
        
        return ($fieldMap[$sort] ?? 'utilidad') . " $order";
    }
}
