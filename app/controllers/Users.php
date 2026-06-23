<?php
class Users extends Controller {
    private $userModel;

    public function __construct(){
      $this->userModel = $this->model('User');
    }

    public function index(){
        if(!isLoggedIn() || !isAdmin()){
            redirect('users/login');
        }
        $users = $this->userModel->getUsers();
        $data = ['users' => $users];
        $this->view('users/index', $data);
    }

    public function add(){
        if(!isLoggedIn() || !isAdmin()){
            redirect('users/login');
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'id_rol' => trim($_POST['id_rol']),
                'nombre' => trim($_POST['nombre']),
                'usuario' => trim($_POST['usuario']),
                'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                'estado' => 1
            ];

            if($this->userModel->addUser($data)){
                flash('user_message', 'Usuario creado correctamente');
                redirect('users');
            }
        } else {
            $roles = $this->userModel->getRoles();
            $data = ['roles' => $roles];
            $this->view('users/add', $data);
        }
    }

    public function login(){
      // Check for POST
      if($_SERVER['REQUEST_METHOD'] == 'POST'){
        // Process form
        // Sanitize POST data
        $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = $_POST['password']; // Do NOT sanitize passwords

        // Init data
        $data =[
          'usuario' => trim($usuario),
          'password' => trim($password),
          'usuario_err' => '',
          'password_err' => '',
        ];

        // Validate Usuario
        if(empty($data['usuario'])){
          $data['usuario_err'] = 'Por favor ingrese su usuario';
        }

        // Validate Password
        if(empty($data['password'])){
          $data['password_err'] = 'Por favor ingrese su contraseña';
        }

        // Check for user/usuario
        if($this->userModel->findUserByUsername($data['usuario'])){
          // User found
        } else {
          // User not found
          $data['usuario_err'] = 'Usuario no encontrado';
        }

        // Make sure errors are empty
        if(empty($data['usuario_err']) && empty($data['password_err'])){
          // Validated
          // Check and set logged in user
          $loggedInUser = $this->userModel->login($data['usuario'], $data['password']);

          if($loggedInUser){
            // Create Session
            $this->createUserSession($loggedInUser);
          } else {
            $data['password_err'] = 'Contraseña incorrecta';

            $this->view('users/login', $data);
          }
        } else {
          // Load view with errors
          $this->view('users/login', $data);
        }


      } else {
        // Init data
        $data =[
          'usuario' => '',
          'password' => '',
          'usuario_err' => '',
          'password_err' => '',
        ];

        // Load view
        $this->view('users/login', $data);
      }
    }

    public function createUserSession($user){
      $_SESSION['user_id'] = $user->id;
      $_SESSION['user_usuario'] = $user->usuario;
      $_SESSION['user_nombre'] = $user->nombre;
      $_SESSION['user_rol'] = $user->id_rol;
      redirect('pages/index');
    }

    public function logout(){
      unset($_SESSION['user_id']);
      unset($_SESSION['user_usuario']);
      unset($_SESSION['user_nombre']);
      unset($_SESSION['user_rol']);
      session_destroy();
      redirect('users/login');
    }

    public function recover(){
      $data = [
        'title' => 'Recuperar Contraseña'
      ];
      $this->view('users/recover', $data);
    }
  }
