<?php
class Sales extends Controller {
    private $saleModel;

    public function __construct() {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->saleModel = $this->model('Sale');
        $this->db = new Database;
    }

    public function index() {
        $sales = $this->saleModel->getSales();
        $data = ['sales' => $sales];
        $this->view('sales/index', $data);
    }

    public function invoice($id) {
        $this->db->query('SELECT v.*, u.nombre as usuario_nombre, c.nombre as cliente_nombre, c.direccion as cliente_direccion, c.telefono as cliente_telefono
                          FROM ventas v
                          INNER JOIN usuarios u ON v.id_usuario = u.id
                          INNER JOIN clientes c ON v.id_cliente = c.id
                          WHERE v.id = :id');
        $this->db->bind(':id', $id);
        $sale = $this->db->single();

        $this->db->query('SELECT dv.*, p.nombre as producto_nombre
                          FROM detalle_ventas dv
                          INNER JOIN productos p ON dv.id_producto = p.id
                          WHERE dv.id_venta = :id');
        $this->db->bind(':id', $id);
        $details = $this->db->resultSet();

        $data = [
            'sale' => $sale,
            'details' => $details
        ];

        $this->view('sales/invoice', $data);
    }

    public function printReceipt($id) {
        $this->db->query('SELECT * FROM impresoras WHERE activa = 1 LIMIT 1');
        $printer = $this->db->single();
        
        if (!$printer) {
            flash('sales_message', 'No hay impresora activa configurada', 'alert alert-warning');
            redirect('sales');
        }

        $this->db->query('SELECT v.*, u.nombre as usuario_nombre, c.nombre as cliente_nombre, c.direccion as cliente_direccion, c.telefono as cliente_telefono
                          FROM ventas v
                          INNER JOIN usuarios u ON v.id_usuario = u.id
                          INNER JOIN clientes c ON v.id_cliente = c.id
                          WHERE v.id = :id');
        $this->db->bind(':id', $id);
        $sale = $this->db->single();

        $this->db->query('SELECT dv.*, p.nombre as producto_nombre
                          FROM detalle_ventas dv
                          INNER JOIN productos p ON dv.id_producto = p.id
                          WHERE dv.id_venta = :id');
        $this->db->bind(':id', $id);
        $details = $this->db->resultSet();

        require_once APPROOT . '/lib/ReceiptPrinter.php';
        $rp = \App\Lib\ReceiptPrinter::fromDatabase([
            'tipo' => $printer->tipo,
            'conexion' => $printer->conexion,
            'ancho_papel' => $printer->ancho_papel,
        ]);

        $settings = [
            'nombre_negocio' => getConfig('nombre_negocio', 'POSVENTA'),
            'ruc' => getConfig('ruc', ''),
            'direccion' => getConfig('direccion', ''),
            'telefono' => getConfig('telefono', ''),
            'iva' => getConfig('iva', 15),
        ];

        $success = $rp->printSaleReceipt(
            (array)$sale,
            array_map(function($d) { return (array)$d; }, $details),
            $settings
        );

        if ($success) {
            flash('sales_message', 'Ticket enviado a impresora', 'alert alert-success');
        } else {
            flash('sales_message', 'Error al imprimir ticket', 'alert alert-danger');
        }
        redirect('sales');
    }
    
    public function invoicePdf($id) {
        $this->db->query('SELECT v.*, u.nombre as usuario_nombre, c.nombre as cliente_nombre, c.direccion as cliente_direccion, c.telefono as cliente_telefono
                          FROM ventas v
                          INNER JOIN usuarios u ON v.id_usuario = u.id
                          INNER JOIN clientes c ON v.id_cliente = c.id
                          WHERE v.id = :id');
        $this->db->bind(':id', $id);
        $sale = $this->db->single();
        
        if (!$sale) {
            flash('sales_message', 'Venta no encontrada', 'alert alert-danger');
            redirect('sales');
        }
        
        $this->db->query('SELECT dv.*, p.nombre as producto_nombre
                          FROM detalle_ventas dv
                          INNER JOIN productos p ON dv.id_producto = p.id
                          WHERE dv.id_venta = :id');
        $this->db->bind(':id', $id);
        $details = $this->db->resultSet();
        
        require_once APPROOT . '/../vendor/autoload.php';
        use Dompdf\Dompdf;
        use Dompdf\Options;
        
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        
        $dompdf = new Dompdf($options);
        
        // Load view for PDF
        ob_start();
        require APPROOT . '/views/sales/invoice_pdf.php';
        $html = ob_get_clean();
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = 'Factura_' . $sale->numero_factura . '.pdf';
        $dompdf->stream($filename, ['Attachment' => false]);
        exit;
    }
}