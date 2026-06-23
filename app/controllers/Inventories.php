<?php
class Inventories extends Controller {
    private $inventoryModel;
    private $productModel;

    public function __construct() {
        if (!isLoggedIn()) {
            redirect('users/login');
        }

        $this->inventoryModel = $this->model('Inventory');
        $this->productModel = $this->model('Product');
    }

    public function index() {
        $stock = $this->inventoryModel->getStockStatus();
        $data = ['stock' => $stock];
        $this->view('inventory/index', $data);
    }

    public function lowstock() {
        $lowStock = $this->inventoryModel->getLowStock();
        $data = ['lowStock' => $lowStock];
        $this->view('inventory/lowstock', $data);
    }

    public function kardex() {
        $product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;
        $movements = $this->inventoryModel->getKardex($product_id);
        $products = $this->productModel->getProducts();

        $data = [
            'movements' => $movements,
            'products' => $products,
            'selected_product' => $product_id
        ];
        $this->view('inventory/kardex', $data);
    }
}
