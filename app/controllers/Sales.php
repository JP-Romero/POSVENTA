<?php
class Sales extends Controller {
    private $saleModel;

    public function __construct() {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->saleModel = $this->model('Sale');
    }

    public function index() {
        $sales = $this->saleModel->getSales();
        $data = ['sales' => $sales];
        $this->view('sales/index', $data);
    }

    public function invoice($id) {
        // In a real system, I would use FPDF or Dompdf.
        // For this environment, I'll simulate a printable HTML invoice.
        $this->db = new Database;
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
}
