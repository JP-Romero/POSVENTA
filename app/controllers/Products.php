<?php


  class Products extends Controller {
    private $productModel;
    private $categoryModel;
    private $providerModel;

    public function __construct(){
      if(!isLoggedIn()){
        redirect('users/login');
      }

      // Check for Admin for management actions
      $url = isset($_GET['url']) ? explode('/', rtrim($_GET['url'], '/')) : [];
      $method = isset($url[1]) ? $url[1] : 'index';

      if($method != 'index' && !isAdmin()){
        flash('access_error', 'No tiene permisos para realizar esta acción', 'alert alert-danger');
        redirect('products');
      }

      $this->productModel = $this->model('Product');
      $this->categoryModel = $this->model('Category');
      $this->providerModel = $this->model('Provider');
    }

    public function index(){
      $products = $this->productModel->getProducts();

      $data = [
        'products' => $products
      ];

      $this->view('products/index', $data);
    }

    public function add(){
      if($_SERVER['REQUEST_METHOD'] == 'POST'){
        // Sanitize
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $data = [
          'codigo_interno' => trim($_POST['codigo_interno']),
          'codigo_barras' => trim($_POST['codigo_barras']),
          'nombre' => trim($_POST['nombre']),
          'descripcion' => trim($_POST['descripcion']),
          'id_categoria' => trim($_POST['id_categoria']),
          'id_proveedor' => trim($_POST['id_proveedor']),
          'precio_compra' => trim($_POST['precio_compra']),
          'precio_venta' => trim($_POST['precio_venta']),
          'stock' => trim($_POST['stock']),
          'stock_minimo' => trim($_POST['stock_minimo']),
          'estado' => isset($_POST['estado']) ? 1 : 0,
          'imagen' => '',
          'nombre_err' => '',
          'categoria_err' => '',
          'proveedor_err' => '',
          'precio_compra_err' => '',
          'precio_venta_err' => '',
          'categories' => $this->categoryModel->getCategories(),
          'providers' => $this->providerModel->getProviders()
        ];

        // Validate
        if(empty($data['nombre'])){
          $data['nombre_err'] = 'Ingrese el nombre del producto';
        }
        if(empty($data['id_categoria'])){
          $data['categoria_err'] = 'Seleccione una categoría';
        }
        if(empty($data['id_proveedor'])){
          $data['proveedor_err'] = 'Seleccione un proveedor';
        }
        if(empty($data['precio_compra'])){
          $data['precio_compra_err'] = 'Ingrese el precio de compra';
        }
        if(empty($data['precio_venta'])){
          $data['precio_venta_err'] = 'Ingrese el precio de venta';
        }

        // Image upload handling
        if(!empty($_FILES['imagen']['name'])){
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            $file_type = $_FILES['imagen']['type'];

            if(in_array($file_type, $allowed_types)){
                $filename = time() . '_' . $_FILES['imagen']['name'];
                $target = APPROOT . '/../public/img/products/' . $filename;

                if(!file_exists(APPROOT . '/../public/img/products/')){
                    mkdir(APPROOT . '/../public/img/products/', 0777, true);
                }

                if(move_uploaded_file($_FILES['imagen']['tmp_name'], $target)){
                    $data['imagen'] = $filename;
                }
            } else {
                $data['nombre_err'] = 'Tipo de archivo no permitido';
            }
        }

        // Make sure no errors
        if(empty($data['nombre_err']) && empty($data['categoria_err']) && empty($data['proveedor_err']) && empty($data['precio_compra_err']) && empty($data['precio_venta_err'])){
          if($this->productModel->addProduct($data)){
            flash('product_message', 'Producto agregado correctamente');
            redirect('products');
          } else {
            flash('product_message', 'Error al agregar producto', 'alert alert-danger');
            redirect('products/add');
          }
        } else {
          $this->view('products/add', $data);
        }

      } else {
        $data = [
          'codigo_interno' => '',
          'codigo_barras' => '',
          'nombre' => '',
          'descripcion' => '',
          'id_categoria' => '',
          'id_proveedor' => '',
          'precio_compra' => '',
          'precio_venta' => '',
          'stock' => '0',
          'stock_minimo' => '5',
          'estado' => 1,
          'imagen' => '',
          'nombre_err' => '',
          'categoria_err' => '',
          'proveedor_err' => '',
          'precio_compra_err' => '',
          'precio_venta_err' => '',
          'categories' => $this->categoryModel->getCategories(),
          'providers' => $this->providerModel->getProviders()
        ];

        $this->view('products/add', $data);
      }
    }

    public function edit($id){
      if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $product = $this->productModel->getProductById($id);

        $data = [
          'id' => $id,
          'codigo_interno' => trim($_POST['codigo_interno']),
          'codigo_barras' => trim($_POST['codigo_barras']),
          'nombre' => trim($_POST['nombre']),
          'descripcion' => trim($_POST['descripcion']),
          'id_categoria' => trim($_POST['id_categoria']),
          'id_proveedor' => trim($_POST['id_proveedor']),
          'precio_compra' => trim($_POST['precio_compra']),
          'precio_venta' => trim($_POST['precio_venta']),
          'stock' => trim($_POST['stock']),
          'stock_minimo' => trim($_POST['stock_minimo']),
          'estado' => isset($_POST['estado']) ? 1 : 0,
          'imagen' => $product->imagen,
          'nombre_err' => '',
          'categoria_err' => '',
          'proveedor_err' => '',
          'categories' => $this->categoryModel->getCategories(),
          'providers' => $this->providerModel->getProviders()
        ];

        // Validate
        if(empty($data['nombre'])){
          $data['nombre_err'] = 'Ingrese el nombre del producto';
        }
        if(empty($data['id_categoria'])){
          $data['categoria_err'] = 'Seleccione una categoría';
        }
        if(empty($data['id_proveedor'])){
          $data['proveedor_err'] = 'Seleccione un proveedor';
        }

        if(!empty($_FILES['imagen']['name'])){
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            $file_type = $_FILES['imagen']['type'];

            if(in_array($file_type, $allowed_types)){
                $filename = time() . '_' . $_FILES['imagen']['name'];
                $target = APPROOT . '/../public/img/products/' . $filename;

                if(move_uploaded_file($_FILES['imagen']['tmp_name'], $target)){
                    // Delete old image if exists
                    if(!empty($product->imagen) && file_exists(APPROOT . '/../public/img/products/' . $product->imagen)){
                        unlink(APPROOT . '/../public/img/products/' . $product->imagen);
                    }
                    $data['imagen'] = $filename;
                }
            } else {
                $data['nombre_err'] = 'Tipo de archivo no permitido';
            }
        }

        if(empty($data['nombre_err']) && empty($data['categoria_err']) && empty($data['proveedor_err'])){
          if($this->productModel->updateProduct($data)){
            flash('product_message', 'Producto actualizado correctamente');
            redirect('products');
          } else {
            flash('product_message', 'Error al actualizar producto', 'alert alert-danger');
            redirect('products/edit/' . $id);
          }
        } else {
          $this->view('products/edit', $data);
        }

      } else {
        $product = $this->productModel->getProductById($id);

        if(!$product){
            redirect('products');
        }

        $data = [
          'id' => $id,
          'codigo_interno' => $product->codigo_interno,
          'codigo_barras' => $product->codigo_barras,
          'nombre' => $product->nombre,
          'descripcion' => $product->descripcion,
          'id_categoria' => $product->id_categoria,
          'id_proveedor' => $product->id_proveedor,
          'precio_compra' => $product->precio_compra,
          'precio_venta' => $product->precio_venta,
          'stock' => $product->stock,
          'stock_minimo' => $product->stock_minimo,
          'estado' => $product->estado,
          'imagen' => $product->imagen,
          'nombre_err' => '',
          'categoria_err' => '',
          'proveedor_err' => '',
          'categories' => $this->categoryModel->getCategories(),
          'providers' => $this->providerModel->getProviders()
        ];

        $this->view('products/edit', $data);
      }
    }

    public function delete($id){
      if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if(!isAdmin()){
            flash('product_message', 'No tiene permisos para eliminar productos', 'alert alert-danger');
            redirect('products');
        }

        $product = $this->productModel->getProductById($id);

        if($this->productModel->deleteProduct($id)){
          if(!empty($product->imagen) && file_exists(APPROOT . '/../public/img/products/' . $product->imagen)){
            unlink(APPROOT . '/../public/img/products/' . $product->imagen);
          }
          flash('product_message', 'Producto eliminado correctamente');
          redirect('products');
        } else {
          flash('product_message', 'Error al eliminar producto', 'alert alert-danger');
          redirect('products');
        }
      } else {
        redirect('products');
      }
    }

    public function export(){
        $products = $this->productModel->getProducts();
        $filename = "productos_" . date('Ymd') . ".csv";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        fputcsv($output, array('ID', 'Código Interno', 'Código Barras', 'Nombre', 'Categoría', 'Proveedor', 'Precio Compra', 'Precio Venta', 'Stock', 'Stock Mínimo', 'Estado'));

        foreach($products as $product){
            fputcsv($output, array(
                $product->id,
                $product->codigo_interno,
                $product->codigo_barras,
                $product->nombre,
                $product->categoria_nombre,
                $product->proveedor_nombre,
                $product->precio_compra,
                $product->precio_venta,
                $product->stock,
                $product->stock_minimo,
                $product->estado ? 'Activo' : 'Inactivo'
            ));
        }
        fclose($output);
        exit;
    }

    // Print single barcode label
    public function printBarcode($id){
        if (!isLoggedIn() || !isAdmin()) {
            redirect('users/login');
        }
        
        $product = $this->productModel->getProductById($id);
        if (!$product) {
            flash('product_message', 'Producto no encontrado', 'alert alert-danger');
            redirect('products');
        }
        
        require_once APPROOT . '/lib/BarcodeGenerator.php';
        $generator = new \App\Lib\BarcodeGenerator();
        
        $label = $generator->generateProductLabel([
            'id' => $product->id,
            'nombre' => $product->nombre,
            'codigo_barras' => $product->codigo_barras,
            'codigo_interno' => $product->codigo_interno,
            'precio_venta' => $product->precio_venta,
        ], [
            'business_name' => getConfig('nombre_negocio', 'POSVENTA'),
            'currency_symbol' => getConfig('moneda_simbolo', 'C$'),
        ]);
        
        $data = [
            'product' => $product,
            'label_base64' => $label
        ];
        
        $this->view('products/barcode', $data);
    }
    
    // Print barcode batch
    public function printBarcodeBatch(){
        if (!isLoggedIn() || !isAdmin()) {
            redirect('users/login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $ids = $_POST['product_ids'] ?? [];
            
            if (empty($ids)) {
                flash('product_message', 'Seleccione al menos un producto', 'alert alert-warning');
                redirect('products');
            }
            
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $this->productModel->db->query("SELECT * FROM productos WHERE id IN ($placeholders)");
            foreach ($ids as $i => $id) {
                $this->productModel->db->bind($i + 1, $id);
            }
            $products = $this->productModel->db->resultSet();
            
            require_once APPROOT . '/lib/BarcodeGenerator.php';
            $generator = new \App\Lib\BarcodeGenerator();
            
            $labels = $generator->generateBatchLabels($products, [
                'business_name' => getConfig('nombre_negocio', 'POSVENTA'),
                'currency_symbol' => getConfig('moneda_simbolo', 'C$'),
            ]);
            
            $data = [
                'labels' => $labels
            ];
            
            $this->view('products/barcode_batch', $data);
        } else {
            redirect('products');
        }
    }
    
    // Import products from Excel/CSV
    public function import(){
        if (!isLoggedIn() || !isAdmin()) {
            redirect('users/login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
                flash('product_message', 'Archivo no válido', 'alert alert-danger');
                redirect('products');
            }
            
            $file = $_FILES['import_file']['tmp_name'];
            $ext = strtolower(pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION));
            
            if (!in_array($ext, ['csv', 'xlsx', 'xls'])) {
                flash('product_message', 'Formato no soportado. Use CSV, XLSX o XLS', 'alert alert-danger');
                redirect('products');
            }
            
require_once APPROOT . '/../vendor/autoload.php';
            
            try {
                $spreadsheet = IOFactory::load($file);
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray(null, true, true, true);
                
                // Skip header row
                $header = array_shift($rows);
                
                // Map columns (flexible matching)
                $colMap = [];
                foreach ($header as $col => $val) {
                    $key = strtolower(trim($val));
                    if (strpos($key, 'codigo') !== false && strpos($key, 'interno') !== false) $colMap['codigo_interno'] = $col;
                    elseif (strpos($key, 'codigo') !== false && strpos($key, 'barras') !== false) $colMap['codigo_barras'] = $col;
                    elseif (strpos($key, 'nombre') !== false) $colMap['nombre'] = $col;
                    elseif (strpos($key, 'descripcion') !== false) $colMap['descripcion'] = $col;
                    elseif (strpos($key, 'categoria') !== false) $colMap['categoria'] = $col;
                    elseif (strpos($key, 'proveedor') !== false) $colMap['proveedor'] = $col;
                    elseif (strpos($key, 'precio') !== false && strpos($key, 'compra') !== false) $colMap['precio_compra'] = $col;
                    elseif (strpos($key, 'precio') !== false && strpos($key, 'venta') !== false) $colMap['precio_venta'] = $col;
                    elseif (strpos($key, 'stock') !== false && strpos($key, 'min') !== false) $colMap['stock_minimo'] = $col;
                    elseif (strpos($key, 'stock') !== false) $colMap['stock'] = $col;
                }
                
                // Get categories and providers for lookup
                $this->db->query('SELECT id, nombre FROM categorias');
                $cats = $this->db->resultSet();
                $catMap = [];
                foreach ($cats as $c) $catMap[strtolower($c->nombre)] = $c->id;
                
                $this->db->query('SELECT id, nombre FROM proveedores');
                $provs = $this->db->resultSet();
                $provMap = [];
                foreach ($provs as $p) $provMap[strtolower($p->nombre)] = $p->id;
                
                $imported = 0;
                $errors = [];
                
                foreach ($rows as $rowNum => $row) {
                    if (empty($row[$colMap['nombre'] ?? 'A'])) continue;
                    
                    $catName = strtolower(trim($row[$colMap['categoria'] ?? ''] ?? ''));
                    $provName = strtolower(trim($row[$colMap['proveedor'] ?? ''] ?? ''));
                    
                    $catId = $catMap[$catName] ?? 1;
                    $provId = $provMap[$provName] ?? 1;
                    
                    $data = [
                        'codigo_interno' => trim($row[$colMap['codigo_interno'] ?? ''] ?? ''),
                        'codigo_barras' => trim($row[$colMap['codigo_barras'] ?? ''] ?? ''),
                        'nombre' => trim($row[$colMap['nombre'] ?? ''] ?? ''),
                        'descripcion' => trim($row[$colMap['descripcion'] ?? ''] ?? ''),
                        'id_categoria' => $catId,
                        'id_proveedor' => $provId,
                        'precio_compra' => (float)($row[$colMap['precio_compra'] ?? ''] ?? 0),
                        'precio_venta' => (float)($row[$colMap['precio_venta'] ?? ''] ?? 0),
                        'stock' => (int)($row[$colMap['stock'] ?? ''] ?? 0),
                        'stock_minimo' => (int)($row[$colMap['stock_minimo'] ?? ''] ?? 5),
                        'estado' => 1,
                        'imagen' => ''
                    ];
                    
                    if ($this->productModel->addProduct($data)) {
                        $imported++;
                    } else {
                        $errors[] = "Fila " . ($rowNum + 2) . ": " . $data['nombre'];
                    }
                }
                
                $msg = "Importados: $imported productos";
                if ($errors) $msg .= ". Errores: " . count($errors);
                flash('product_message', $msg, empty($errors) ? 'alert alert-success' : 'alert alert-warning');
                
            } catch (\Exception $e) {
                flash('product_message', 'Error al importar: ' . $e->getMessage(), 'alert alert-danger');
            }
            
            redirect('products');
        }
        
        // GET: show import form
        $data = ['title' => 'Importar Productos'];
        $this->view('products/import', $data);
    }
    
    // Download import template
    public function importTemplate(){
        $filename = "plantilla_importacion_productos.csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['codigo_interno', 'codigo_barras', 'nombre', 'descripcion', 'categoria', 'proveedor', 'precio_compra', 'precio_venta', 'stock', 'stock_minimo']);
        fputcsv($output, ['PROD001', '7501234567890', 'Producto Ejemplo', 'Descripción del producto', 'Papelería', 'Proveedor Ejemplo', '10.00', '15.00', '100', '10']);
        fclose($output);
        exit;
    }
  }
