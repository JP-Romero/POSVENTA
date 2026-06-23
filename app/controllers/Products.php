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
            die('Algo salió mal');
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
            die('Algo salió mal');
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
          die('Algo salió mal');
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

    public function import(){
        if(!isAdmin()){
            redirect('products');
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_FILES['csv_file']['name'])){
            $file = $_FILES['csv_file']['tmp_name'];
            $handle = fopen($file, "r");

            // Skip first row (header)
            fgetcsv($handle);

            $success = 0;
            while(($data = fgetcsv($handle, 1000, ",")) !== FALSE){
                // ID, Código Interno, Código Barras, Nombre, ID_Categoría, ID_Proveedor, Precio Compra, Precio Venta, Stock, Stock Mínimo
                // Assuming CSV structure: codigo_interno, codigo_barras, nombre, descripcion, id_categoria, id_proveedor, precio_compra, precio_venta, stock, stock_minimo
                $prodData = [
                    'codigo_interno' => $data[0],
                    'codigo_barras' => $data[1],
                    'nombre' => $data[2],
                    'descripcion' => $data[3],
                    'id_categoria' => $data[4],
                    'id_proveedor' => $data[5],
                    'precio_compra' => $data[6],
                    'precio_venta' => $data[7],
                    'stock' => $data[8],
                    'stock_minimo' => $data[9],
                    'estado' => 1,
                    'imagen' => ''
                ];

                if($this->productModel->addProduct($prodData)){
                    $success++;
                }
            }
            fclose($handle);
            flash('product_message', "Se importaron $success productos correctamente");
            redirect('products');
        }

        $this->view('products/import');
    }
  }
