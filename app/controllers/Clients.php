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
  }
