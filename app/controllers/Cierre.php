<?php
class Cierre extends Controller {
    private $cajaModel;
    private $saleModel;

    public function __construct() {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->cajaModel = $this->model('Caja');
        $this->saleModel = $this->model('Sale');
        $this->db = new Database;
    }

    public function index() {
        // Get the last cut off date to know when this shift started
        $ultimo_corte = $this->cajaModel->getUltimoCorte();
        $fecha_inicio = $ultimo_corte ? $ultimo_corte->fecha_fin : date('Y-m-d 00:00:00');
        $fecha_fin = date('Y-m-d H:i:s');

        // Fetch Data for Z Report
        $resumenVentas = $this->saleModel->getResumenVentas($fecha_inicio, $fecha_fin);
        $ventasPorCategoria = $this->saleModel->getVentasPorCategoria($fecha_inicio, $fecha_fin);
        $ventasMetodosPago = $this->saleModel->getVentasPorMetodoPago($fecha_inicio, $fecha_fin);
        
        // Movimientos de efectivo
        $movimientosTotales = $this->cajaModel->getTotalesMovimientos($fecha_inicio, $fecha_fin);
        $listaMovimientos = $this->cajaModel->getMovimientos($fecha_inicio, $fecha_fin);

        // Map payment methods to simple array
        $pagos = ['Efectivo' => 0, 'Tarjeta' => 0, 'Transferencia' => 0];
        foreach ($ventasMetodosPago as $pago) {
            $pagos[$pago->metodo_pago] = (float)$pago->total;
        }

        // Calculate expected cash
        // Efectivo esperado = Efectivo de ventas + Fondo/Entradas - Salidas
        $ventas_efectivo = $pagos['Efectivo'];
        $entradas = $movimientosTotales->total_entradas ?? 0;
        $salidas = $movimientosTotales->total_salidas ?? 0;
        
        // Find Fondo Inicial explicitly if needed, but here we assume it's just an 'Entrada'
        $fondo_inicial = 0;
        foreach($listaMovimientos as $mov) {
            if($mov->concepto == 'Fondo Inicial' || $mov->concepto == 'Fondo de Caja Inicial') {
                $fondo_inicial += $mov->monto;
            }
        }

        $efectivo_esperado = $ventas_efectivo + $entradas - $salidas;

        $data = [
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'resumen' => $resumenVentas,
            'categorias' => $ventasPorCategoria,
            'pagos' => $pagos,
            'entradas' => $entradas,
            'salidas' => $salidas,
            'fondo_inicial' => $fondo_inicial,
            'efectivo_esperado' => $efectivo_esperado,
            'listaMovimientos' => $listaMovimientos
        ];

        $this->view('pos/cierre', $data);
    }

    public function registrarMovimiento() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->validateCsrf();

            $data = [
                'tipo' => trim($_POST['tipo']),
                'concepto' => trim($_POST['concepto']),
                'monto' => trim($_POST['monto'])
            ];

            if ($this->cajaModel->addMovimiento($data)) {
                flash('caja_message', 'Movimiento registrado correctamente', 'alert alert-success');
            } else {
                flash('caja_message', 'Error al registrar movimiento', 'alert alert-danger');
            }
            redirect('cierre');
        }
    }

    public function cerrarTurno() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->validateCsrf();

            $efectivo_real = trim($_POST['efectivo_real']);
            $efectivo_esperado = trim($_POST['efectivo_esperado']);
            
            // Re-fetch data to ensure accuracy at the exact moment of closing
            $ultimo_corte = $this->cajaModel->getUltimoCorte();
            $fecha_inicio = $ultimo_corte ? $ultimo_corte->fecha_fin : date('Y-m-d 00:00:00');
            $fecha_fin = date('Y-m-d H:i:s');
            
            $resumenVentas = $this->saleModel->getResumenVentas($fecha_inicio, $fecha_fin);
            $ventasMetodosPago = $this->saleModel->getVentasPorMetodoPago($fecha_inicio, $fecha_fin);
            $movimientosTotales = $this->cajaModel->getTotalesMovimientos($fecha_inicio, $fecha_fin);
            
            $pagos = ['Efectivo' => 0, 'Tarjeta' => 0, 'Transferencia' => 0];
            foreach ($ventasMetodosPago as $pago) {
                $pagos[$pago->metodo_pago] = (float)$pago->total;
            }

            $ventas_brutas = ($resumenVentas->ventas_netas ?? 0) + ($resumenVentas->descuentos ?? 0);

            $data = [
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'ventas_brutas' => $ventas_brutas,
                'descuentos' => $resumenVentas->descuentos ?? 0,
                'ventas_netas' => $resumenVentas->ventas_netas ?? 0,
                'total_efectivo' => $pagos['Efectivo'],
                'total_tarjeta' => $pagos['Tarjeta'],
                'total_transferencia' => $pagos['Transferencia'],
                'fondo_inicial' => 0, // Simplified, assume included in ingresos
                'ingresos_caja' => $movimientosTotales->total_entradas ?? 0,
                'egresos_caja' => $movimientosTotales->total_salidas ?? 0,
                'efectivo_esperado' => $efectivo_esperado,
                'efectivo_real' => $efectivo_real,
                'diferencia' => $efectivo_real - $efectivo_esperado,
                'tickets_emitidos' => $resumenVentas->tickets_emitidos ?? 0,
                'ticket_promedio' => ($resumenVentas->tickets_emitidos > 0) ? (($resumenVentas->ventas_netas ?? 0) / $resumenVentas->tickets_emitidos) : 0,
                'primer_ticket' => $resumenVentas->primer_ticket ?? null,
                'ultimo_ticket' => $resumenVentas->ultimo_ticket ?? null
            ];

            $id_corte = $this->cajaModel->saveCorte($data);

            if ($id_corte) {
                echo json_encode(['status' => 'success', 'id' => $id_corte]);
            } else {
                echo json_encode(['status' => 'error']);
            }
            exit;
        }
    }
    
    // We will add imprimirCorte later when updating ReceiptPrinter
}
