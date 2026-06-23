<?php
  class Providers extends Controller {
    private $providerModel;

    public function __construct(){
      if(!isLoggedIn()){
        redirect('users/login');
      }

      if(!isAdmin()){
        flash('access_error', 'No tiene permisos para acceder a este módulo', 'alert alert-danger');
        redirect('pages/index');
      }

      $this->providerModel = $this->model('Provider');
    }

    public function index(){
      $providers = $this->providerModel->getProviders();
      $data = ['providers' => $providers];
      $this->view('providers/index', $data);
    }

    public function add(){
      if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $data = [
          'nombre' => trim($_POST['nombre']),
          'contacto' => trim($_POST['contacto']),
          'telefono' => trim($_POST['telefono']),
          'correo' => trim($_POST['correo']),
          'direccion' => trim($_POST['direccion']),
          'nombre_err' => ''
        ];

        if(empty($data['nombre'])){
          $data['nombre_err'] = 'Ingrese el nombre del proveedor';
        }

        if(empty($data['nombre_err'])){
          if($this->providerModel->addProvider($data)){
            flash('provider_message', 'Proveedor agregado correctamente');
            redirect('providers');
          } else {
            die('Algo salió mal');
          }
        } else {
          $this->view('providers/add', $data);
        }
      } else {
        $data = [
          'nombre' => '',
          'contacto' => '',
          'telefono' => '',
          'correo' => '',
          'direccion' => '',
          'nombre_err' => ''
        ];
        $this->view('providers/add', $data);
      }
    }

    public function edit($id){
      if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $data = [
          'id' => $id,
          'nombre' => trim($_POST['nombre']),
          'contacto' => trim($_POST['contacto']),
          'telefono' => trim($_POST['telefono']),
          'correo' => trim($_POST['correo']),
          'direccion' => trim($_POST['direccion']),
          'nombre_err' => ''
        ];

        if(empty($data['nombre'])){
          $data['nombre_err'] = 'Ingrese el nombre del proveedor';
        }

        if(empty($data['nombre_err'])){
          if($this->providerModel->updateProvider($data)){
            flash('provider_message', 'Proveedor actualizado correctamente');
            redirect('providers');
          } else {
            die('Algo salió mal');
          }
        } else {
          $this->view('providers/edit', $data);
        }
      } else {
        $provider = $this->providerModel->getProviderById($id);
        if(!$provider) redirect('providers');

        $data = [
          'id' => $id,
          'nombre' => $provider->nombre,
          'contacto' => $provider->contacto,
          'telefono' => $provider->telefono,
          'correo' => $provider->correo,
          'direccion' => $provider->direccion,
          'nombre_err' => ''
        ];
        $this->view('providers/edit', $data);
      }
    }

    public function delete($id){
      if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if($this->providerModel->deleteProvider($id)){
          flash('provider_message', 'Proveedor eliminado correctamente');
          redirect('providers');
        } else {
          die('Algo salió mal');
        }
      } else {
        redirect('providers');
      }
    }
    
    public function history($id){
      $provider = $this->providerModel->getProviderById($id);
      if(!$provider) redirect('providers');
      
      $purchases = $this->providerModel->getPurchaseHistory($id);
      
      $data = [
        'provider' => $provider,
        'purchases' => $purchases
      ];
      $this->view('providers/history', $data);
    }
    
    public function apiPurchaseDetails($id){
      header('Content-Type: application/json');
      
      $this->db->query('SELECT * FROM compras WHERE id = :id');
      $this->db->bind(':id', $id);
      $purchase = $this->db->single();
      
      if (!$purchase) {
        echo json_encode(['success' => false]);
        exit;
      }
      
      $this->db->query('SELECT dc.*, p.nombre as producto_nombre
                        FROM detalle_compras dc
                        INNER JOIN productos p ON dc.id_producto = p.id
                        WHERE dc.id_compra = :id');
      $this->db->bind(':id', $id);
      $items = $this->db->resultSet();
      
      echo json_encode([
        'success' => true,
        'data' => (array)$purchase,
        'items' => array_map(function($i) { return (array)$i; }, $items)
      ]);
      exit;
    }
  }
