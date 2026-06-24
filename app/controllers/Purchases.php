<?php
class Purchases extends Controller {
    private $purchaseModel;
    private $providerModel;
    private $productModel;

    public function __construct() {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        
        $this->purchaseModel = $this->model('Purchase');
        $this->providerModel = $this->model('Provider');
        $this->productModel = $this->model('Product');
    }

    public function index() {
        if(!canAccess('purchases')){
            flash('access_error', 'No tiene permisos para acceder a este módulo', 'alert alert-danger');
            redirect('pages/index');
        }
        
        $purchases = $this->purchaseModel->getPurchases();
        $data = ['purchases' => $purchases];
        $this->view('purchases/index', $data);
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // This would normally handle the AJAX post or a complex form post
            // For simplicity in this MVC, we assume a JSON post or a structured array
            $jsonData = json_decode(file_get_contents('php://input'), true);

            if ($jsonData) {
                if ($this->purchaseModel->savePurchase($jsonData)) {
                    echo json_encode(['status' => 'success']);
                } else {
                    echo json_encode(['status' => 'error']);
                }
                exit;
            }
        }

        $providers = $this->providerModel->getProviders();
        $products = $this->productModel->getProducts();

        $data = [
            'providers' => $providers,
            'products' => $products
        ];

        $this->view('purchases/add', $data);
    }
}
