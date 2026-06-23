<?php
class Pos extends Controller {
    private $saleModel;
    private $productModel;
    private $clientModel;

    public function __construct() {
        if (!isLoggedIn()) {
            redirect('users/login');
        }

        $this->saleModel = $this->model('Sale');
        $this->productModel = $this->model('Product');
        $this->clientModel = $this->model('Client');
    }

    public function index() {
        $clients = $this->clientModel->getClients();
        $invoiceNumber = $this->saleModel->getNextInvoiceNumber();

        $this->db = new Database;
        $this->db->query('SELECT * FROM configuracion WHERE id = 1');
        $settings = $this->db->single();

        $data = [
            'clients' => $clients,
            'invoiceNumber' => $invoiceNumber,
            'iva' => $settings->iva ?? 0
        ];

        $this->view('pos/index', $data);
    }

    public function searchProduct() {
        $query = $_GET['q'] ?? '';
        $this->db = new Database;
        $this->db->query('SELECT * FROM productos WHERE (nombre LIKE :q OR codigo_barras = :code OR codigo_interno = :code) AND estado = 1');
        $this->db->bind(':q', '%'.$query.'%');
        $this->db->bind(':code', $query);
        $products = $this->db->resultSet();
        echo json_encode($products);
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $jsonData = json_decode(file_get_contents('php://input'), true);
            if ($jsonData) {
                $sale_id = $this->saleModel->saveSale($jsonData);
                if ($sale_id) {
                    echo json_encode(['status' => 'success', 'sale_id' => $sale_id]);
                } else {
                    echo json_encode(['status' => 'error']);
                }
                exit;
            }
        }
    }
}
