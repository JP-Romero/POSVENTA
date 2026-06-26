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
        $this->cajaModel = $this->model('Caja');
        $this->db = new Database;
    }

    public function index() {
        $clients = $this->clientModel->getClients();
        $invoiceNumber = $this->saleModel->getNextInvoiceNumber();

        $this->db->query('SELECT * FROM configuracion WHERE id = 1');
        $settings = $this->db->single();

        // Get active printer
        $this->db->query('SELECT * FROM impresoras WHERE activa = 1 LIMIT 1');
        $printer = $this->db->single();

        // Load real categories from DB
        $this->db->query('SELECT id, nombre FROM categorias ORDER BY nombre ASC');
        $categories = $this->db->resultSet();

        $data = [
            'clients' => $clients,
            'invoiceNumber' => $invoiceNumber,
            'iva' => $settings->iva ?? 0,
            'iva_enabled' => $settings->iva_enabled ?? 1,
            'exchange_rate' => $settings->exchange_rate ?? 0,
            'payment_methods' => $settings->payment_methods ?? 'efectivo,tarjeta,mixto',
            'settings' => $settings,
            'printer' => $printer,
            'cajaAbierta' => $this->cajaModel->isCajaAbierta(),
            'categories' => $categories
        ];

        $this->view('pos/index', $data);
    }

    public function searchProduct() {
        $query = $_GET['q'] ?? '';
        $categoriaId = $_GET['categoria_id'] ?? null;

        // Special filter: más vendidos
        if ($categoriaId === 'mas-vendidos') {
            $sql = 'SELECT p.*, COUNT(dv.id) as ventas_count
                    FROM productos p
                    INNER JOIN detalle_ventas dv ON p.id = dv.id_producto
                    WHERE p.estado = 1 AND p.stock > 0';
            $params = [];
            if ($query) {
                $sql .= ' AND (p.nombre LIKE :q OR p.codigo_barras = :code OR p.codigo_interno = :code)';
                $params[':q'] = '%' . $query . '%';
                $params[':code'] = $query;
            }
            $sql .= ' GROUP BY p.id ORDER BY ventas_count DESC LIMIT 30';
            $this->db->query($sql);
            foreach ($params as $key => $val) {
                $this->db->bind($key, $val);
            }
            $products = $this->db->resultSet();
            echo json_encode($products);
            exit;
        }

        // Base query
        $sql = 'SELECT * FROM productos WHERE estado = 1 AND stock > 0';
        $params = [];

        if ($query) {
            $sql .= ' AND (nombre LIKE :q OR codigo_barras = :code OR codigo_interno = :code)';
            $params[':q'] = '%' . $query . '%';
            $params[':code'] = $query;
        }

        // Special filter: ofertas (bajo margen = buen precio)
        if ($categoriaId === 'ofertas') {
            $sql .= ' AND precio_venta <= precio_compra * 1.2';
        } elseif ($categoriaId && is_numeric($categoriaId)) {
            $sql .= ' AND id_categoria = :categoria_id';
            $params[':categoria_id'] = (int)$categoriaId;
        }

        $sql .= ' ORDER BY nombre ASC LIMIT 50';

        $this->db->query($sql);
        foreach ($params as $key => $val) {
            $this->db->bind($key, $val);
        }
        $products = $this->db->resultSet();
        echo json_encode($products);
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $jsonData = json_decode(file_get_contents('php://input'), true);
            if ($jsonData) {
                // CSRF validation
                if (!isset($jsonData['csrf_token']) || !validateCsrfToken($jsonData['csrf_token'])) {
                    echo json_encode(['status' => 'error', 'message' => 'CSRF validation failed']);
                    exit;
                }
                try {
                    $sale_id = $this->saleModel->saveSale($jsonData);
                    if ($sale_id) {
                        $autoPrint = $jsonData['auto_print'] ?? false;
                        if ($autoPrint) {
                            $this->printReceipt($sale_id);
                        }
                        $realInvoiceNumber = $this->saleModel->getInvoiceNumber($sale_id);
                        echo json_encode(['status' => 'success', 'sale_id' => $sale_id, 'invoiceNumber' => $realInvoiceNumber]);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Sale failed. ID: ' . var_export($sale_id, true)]);
                    }
                } catch (Exception $e) {
                    echo json_encode(['status' => 'error', 'message' => 'Exception: ' . $e->getMessage()]);
                }
                exit;
            }
        }
    }

    public function printReceipt($sale_id) {
        $this->db->query('SELECT * FROM impresoras WHERE activa = 1 LIMIT 1');
        $printer = $this->db->single();
        
        if (!$printer) {
            return ['success' => false, 'message' => 'No hay impresora configurada'];
        }

        $this->db->query('SELECT v.*, u.nombre as usuario_nombre, c.nombre as cliente_nombre, c.direccion as cliente_direccion, c.telefono as cliente_telefono
                          FROM ventas v
                          INNER JOIN usuarios u ON v.id_usuario = u.id
                          INNER JOIN clientes c ON v.id_cliente = c.id
                          WHERE v.id = :id');
        $this->db->bind(':id', $sale_id);
        $sale = $this->db->single();

        $this->db->query('SELECT dv.*, p.nombre as producto_nombre
                          FROM detalle_ventas dv
                          INNER JOIN productos p ON dv.id_producto = p.id
                          WHERE dv.id_venta = :id');
        $this->db->bind(':id', $sale_id);
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

        return ['success' => $success];
    }

    public function printLastReceipt() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $jsonData = json_decode(file_get_contents('php://input'), true);
            $token = $jsonData['csrf_token'] ?? '';
            if (!validateCsrfToken($token)) {
                echo json_encode(['success' => false, 'message' => 'CSRF validation failed']);
                exit;
            }
        }
        $this->db->query('SELECT MAX(id) as last_id FROM ventas');
        $row = $this->db->single();
        if ($row->last_id) {
            $result = $this->printReceipt($row->last_id);
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'message' => 'No hay ventas']);
        }
        exit;
    }

    public function getFrequentProducts() {
        $limit = $_GET['limit'] ?? 12;
        $this->db->query('SELECT p.*, COUNT(dv.id) as ventas_count
                          FROM productos p
                          INNER JOIN detalle_ventas dv ON p.id = dv.id_producto
                          WHERE p.estado = 1
                          GROUP BY p.id
                          ORDER BY ventas_count DESC, p.nombre ASC
                          LIMIT :limit');
        $this->db->bind(':limit', (int)$limit);
        $products = $this->db->resultSet();
        echo json_encode($products);
    }
}