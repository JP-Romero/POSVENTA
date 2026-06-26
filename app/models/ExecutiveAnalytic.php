<?php
class ExecutiveAnalytic {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getExecutiveSummary($start, $end) {
        $this->db->query("SELECT 
                            COUNT(DISTINCT v.id) as tickets,
                            SUM(dv.cantidad) as productos_vendidos,
                            COUNT(DISTINCT v.id_cliente) as clientes_atendidos,
                            SUM(dv.cantidad * dv.precio_venta) as ventas_totales,
                            SUM(dv.cantidad * dv.costo) as costo_ventas,
                            SUM(dv.descuento) as descuentos_totales,
                            SUM((dv.cantidad * dv.precio_venta) - dv.descuento) as ventas_netas,
                            SUM((dv.cantidad * dv.precio_venta) - dv.descuento - (dv.cantidad * dv.costo)) as utilidad_bruta
                          FROM detalle_ventas dv
                          INNER JOIN ventas v ON dv.id_venta = v.id
                          WHERE DATE(v.fecha) BETWEEN :start AND :end");
        $this->db->bind(':start', $start);
        $this->db->bind(':end', $end);
        $res = $this->db->single();

        $ventasNetas = floatval($res->ventas_netas ?? 0);
        $utilidadBruta = floatval($res->utilidad_bruta ?? 0);
        $tickets = intval($res->tickets ?? 0);

        $margen_comercial = $ventasNetas > 0 ? ($utilidadBruta / $ventasNetas) * 100 : 0;
        $ticket_promedio = $tickets > 0 ? ($ventasNetas / $tickets) : 0;

        return [
            'ventas_netas' => $ventasNetas,
            'costo_ventas' => floatval($res->costo_ventas ?? 0),
            'utilidad_bruta' => $utilidadBruta,
            'margen_comercial' => $margen_comercial,
            'ticket_promedio' => $ticket_promedio,
            'clientes_atendidos' => intval($res->clientes_atendidos ?? 0),
            'productos_vendidos' => intval($res->productos_vendidos ?? 0),
            'descuentos_totales' => floatval($res->descuentos_totales ?? 0)
        ];
    }

    public function getInventoryValuation() {
        $this->db->query("SELECT 
                            SUM(stock * precio_compra) as capital_invertido,
                            SUM(stock * precio_venta) as valor_comercial,
                            SUM(stock * (precio_venta - precio_compra)) as ganancia_potencial,
                            SUM(stock) as inventario_disponible
                          FROM productos
                          WHERE estado = 1 AND stock > 0");
        return $this->db->single();
    }

    public function getInventoryStatus() {
        // Productos sin movimiento en los últimos 30 días
        $this->db->query("SELECT COUNT(*) as sin_movimiento 
                          FROM productos p 
                          WHERE p.estado = 1 
                          AND p.id NOT IN (
                              SELECT id_producto FROM detalle_ventas dv 
                              INNER JOIN ventas v ON dv.id_venta = v.id 
                              WHERE v.fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                          )");
        $sin_mov = $this->db->single()->sin_movimiento ?? 0;

        // Inventario comprometido (podría ser apartados, pero por ahora devolvemos 0 si no hay módulo)
        $comprometido = 0; 
        
        // Rotación de inventario (Costo de Bienes Vendidos / Inventario Promedio)
        // Simplificado: Costo Ventas 30 días / Inventario Actual
        $this->db->query("SELECT SUM(dv.cantidad * dv.costo) as cogs 
                          FROM detalle_ventas dv 
                          INNER JOIN ventas v ON dv.id_venta = v.id 
                          WHERE v.fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
        $cogs = $this->db->single()->cogs ?? 0;
        
        $val = $this->getInventoryValuation();
        $inventario_actual = $val->capital_invertido ?? 1; // avoid div by 0
        
        $rotacion = $cogs / ($inventario_actual > 0 ? $inventario_actual : 1);

        return [
            'sin_movimiento' => $sin_mov,
            'comprometido' => $comprometido,
            'rotacion' => $rotacion
        ];
    }

    public function getProfitabilityByEntity($entity, $start, $end) {
        $groupField = '';
        $join = '';
        $nameField = '';
        
        if ($entity == 'producto') {
            $nameField = 'p.nombre as nombre';
            $join = 'INNER JOIN productos p ON dv.id_producto = p.id';
            $groupField = 'p.id';
        } elseif ($entity == 'categoria') {
            $nameField = 'c.nombre as nombre';
            $join = 'INNER JOIN productos p ON dv.id_producto = p.id INNER JOIN categorias c ON p.id_categoria = c.id';
            $groupField = 'c.id';
        } elseif ($entity == 'vendedor') {
            $nameField = 'u.nombre as nombre';
            $join = 'INNER JOIN usuarios u ON v.id_usuario = u.id';
            $groupField = 'u.id';
        } elseif ($entity == 'cliente') {
            $nameField = 'cli.nombre as nombre';
            $join = 'INNER JOIN clientes cli ON v.id_cliente = cli.id';
            $groupField = 'cli.id';
        } else {
            return [];
        }

        $sql = "SELECT 
                    $nameField,
                    SUM(dv.cantidad) as cantidad_vendida,
                    SUM(dv.cantidad * dv.precio_venta) as ventas,
                    SUM(dv.cantidad * dv.costo) as costo,
                    SUM(dv.descuento) as descuentos,
                    SUM((dv.cantidad * dv.precio_venta) - dv.descuento - (dv.cantidad * dv.costo)) as utilidad
                FROM detalle_ventas dv
                INNER JOIN ventas v ON dv.id_venta = v.id
                $join
                WHERE DATE(v.fecha) BETWEEN :start AND :end
                GROUP BY $groupField
                ORDER BY utilidad DESC";

        $this->db->query($sql);
        $this->db->bind(':start', $start);
        $this->db->bind(':end', $end);
        return $this->db->resultSet();
    }

    public function getKPIs() {
        // ROI = (Ganancia / Inversión) * 100
        // Obtenemos del mes actual
        $start = date('Y-m-01');
        $end = date('Y-m-t');
        $mes_actual = $this->getExecutiveSummary($start, $end);
        
        // Mes anterior
        $start_prev = date('Y-m-01', strtotime('-1 month'));
        $end_prev = date('Y-m-t', strtotime('-1 month'));
        $mes_anterior = $this->getExecutiveSummary($start_prev, $end_prev);

        $roi = $mes_actual['costo_ventas'] > 0 ? ($mes_actual['utilidad_bruta'] / $mes_actual['costo_ventas']) * 100 : 0;
        
        $crecimiento_ventas = 0;
        if ($mes_anterior['ventas_netas'] > 0) {
            $crecimiento_ventas = (($mes_actual['ventas_netas'] - $mes_anterior['ventas_netas']) / $mes_anterior['ventas_netas']) * 100;
        }

        return [
            'roi' => $roi,
            'margen_bruto' => $mes_actual['margen_comercial'],
            'crecimiento_ventas' => $crecimiento_ventas,
            'ticket_promedio' => $mes_actual['ticket_promedio']
        ];
    }

    public function getSalesTrends($start, $end) {
        $this->db->query("SELECT DATE(v.fecha) as dia, 
                                 SUM((dv.cantidad * dv.precio_venta) - dv.descuento) as ventas,
                                 SUM(dv.cantidad * dv.costo) as costos,
                                 SUM((dv.cantidad * dv.precio_venta) - dv.descuento - (dv.cantidad * dv.costo)) as utilidad
                          FROM detalle_ventas dv
                          INNER JOIN ventas v ON dv.id_venta = v.id
                          WHERE DATE(v.fecha) BETWEEN :start AND :end
                          GROUP BY DATE(v.fecha)
                          ORDER BY dia ASC");
        $this->db->bind(':start', $start);
        $this->db->bind(':end', $end);
        return $this->db->resultSet();
    }

    public function getFinancialAudit() {
        // Auditoría financiera, ej. Descuentos otorgados en ventas recientes
        $this->db->query("SELECT v.fecha, v.numero_factura, u.nombre as usuario, p.nombre as producto, dv.cantidad, dv.descuento 
                          FROM detalle_ventas dv
                          INNER JOIN ventas v ON dv.id_venta = v.id
                          INNER JOIN usuarios u ON v.id_usuario = u.id
                          INNER JOIN productos p ON dv.id_producto = p.id
                          WHERE dv.descuento > 0
                          ORDER BY v.fecha DESC LIMIT 100");
        return $this->db->resultSet();
    }
}
