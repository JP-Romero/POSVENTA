<?php
  class Clients extends Controller {
    private $clientModel;

    public function __construct(){
      if(!isLoggedIn()){
        redirect('users/login');
      }

      $this->clientModel = $this->model('Client');
    }

    public function index(){
      $clients = $this->clientModel->getClients();
      $data = ['clients' => $clients];
      $this->view('clients/index', $data);
    }

    public function add(){
      if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $data = [
          'nombre' => trim($_POST['nombre']),
          'telefono' => trim($_POST['telefono']),
          'correo' => trim($_POST['correo']),
          'direccion' => trim($_POST['direccion']),
          'nombre_err' => ''
        ];

        if(empty($data['nombre'])){
          $data['nombre_err'] = 'Ingrese el nombre del cliente';
        }

        if(empty($data['nombre_err'])){
          if($this->clientModel->addClient($data)){
            flash('client_message', 'Cliente agregado correctamente');
            redirect('clients');
          } else {
            die('Algo salió mal');
          }
        } else {
          $this->view('clients/add', $data);
        }
      } else {
        $data = [
          'nombre' => '',
          'telefono' => '',
          'correo' => '',
          'direccion' => '',
          'nombre_err' => ''
        ];
        $this->view('clients/add', $data);
      }
    }

    public function edit($id){
      if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $data = [
          'id' => $id,
          'nombre' => trim($_POST['nombre']),
          'telefono' => trim($_POST['telefono']),
          'correo' => trim($_POST['correo']),
          'direccion' => trim($_POST['direccion']),
          'nombre_err' => ''
        ];

        if(empty($data['nombre'])){
          $data['nombre_err'] = 'Ingrese el nombre del cliente';
        }

        if(empty($data['nombre_err'])){
          if($this->clientModel->updateClient($data)){
            flash('client_message', 'Cliente actualizado correctamente');
            redirect('clients');
          } else {
            die('Algo salió mal');
          }
        } else {
          $this->view('clients/edit', $data);
        }
      } else {
        $client = $this->clientModel->getClientById($id);
        if(!$client) redirect('clients');

        $data = [
          'id' => $id,
          'nombre' => $client->nombre,
          'telefono' => $client->telefono,
          'correo' => $client->correo,
          'direccion' => $client->direccion,
          'nombre_err' => ''
        ];
        $this->view('clients/edit', $data);
      }
    }
    
    public function history($id){
      $client = $this->clientModel->getClientById($id);
      if(!$client) redirect('clients');
      
      $sales = $this->clientModel->getPurchaseHistory($id);
      
      $data = [
        'client' => $client,
        'sales' => $sales
      ];
      $this->view('clients/history', $data);
    }
    
    public function apiSaleDetails($id){
      header('Content-Type: application/json');
      
      $this->db->query('SELECT v.*, u.nombre as usuario_nombre
                        FROM ventas v
                        INNER JOIN usuarios u ON v.id_usuario = u.id
                        WHERE v.id = :id');
      $this->db->bind(':id', $id);
      $sale = $this->db->single();
      
      if (!$sale) {
        echo json_encode(['success' => false]);
        exit;
      }
      
      $this->db->query('SELECT dv.*, p.nombre as producto_nombre
                        FROM detalle_ventas dv
                        INNER JOIN productos p ON dv.id_producto = p.id
                        WHERE dv.id_venta = :id');
      $this->db->bind(':id', $id);
      $items = $this->db->resultSet();
      
      echo json_encode([
        'success' => true,
        'data' => (array)$sale,
        'items' => array_map(function($i) { return (array)$i; }, $items)
      ]);
      exit;
    }
  }
