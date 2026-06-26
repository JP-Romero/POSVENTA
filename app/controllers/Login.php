<?php
/**
 * Login.php - Controlador de Autenticación
 * Maneja el proceso de inicio de sesión
 */

class Login extends Controller {
    private $userModel;
    private $db;

    public function __construct(){
        $this->userModel = $this->model('User');
        $this->db = new Database;
    }

    public function index(){
        // Redirigir si ya está autenticado
        if(isLoggedIn()){
            redirect('pages/index');
        }

        // Procesar formulario de login
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Sanitizar datos de entrada
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'usuario' => trim($_POST['usuario']),
                'password' => trim($_POST['password']),
                'usuario_err' => '',
                'password_err' => ''
            ];

            // Validar entradas
            if(empty($data['usuario'])){
                $data['usuario_err'] = 'Ingrese el nombre de usuario';
            }
            if(empty($data['password'])){
                $data['password_err'] = 'Ingrese la contraseña';
            }

            // Validar credenciales
            if(empty($data['usuario_err']) && empty($data['password_err'])){
                // Verificar usuario
                if($this->userModel->findUserByUsername($data['usuario'])){
                    // Usuario encontrado, verificar contraseña
                    $loggedInUser = $this->userModel->login($data['usuario'], $data['password']);
                    if($loggedInUser){
                        // Credenciales correctas
                        $this->createUserSession($loggedInUser);
                    } else {
                        // Contraseña incorrecta
                        $data['password_err'] = 'Contraseña incorrecta';
                        $this->view('users/login', $data);
                    }
                } else {
                    // Usuario no encontrado
                    $data['usuario_err'] = 'Usuario no encontrado';
                    $this->view('users/login', $data);
                }
            } else {
                // Mostrar formulario con errores de validación
                $this->view('users/login', $data);
            }
        } else {
            // Mostrar formulario de login (GET request)
            $data = [
                'usuario' => '',
                'password' => '',
                'usuario_err' => '',
                'password_err' => ''
            ];
            $this->view('users/login', $data);
        }
    }

    // Crear sesión de usuario
    private function createUserSession($user){
        $_SESSION['user_id'] = $user->id;
        $_SESSION['usuario'] = $user->usuario;
        $_SESSION['nombre'] = $user->nombre;
        $_SESSION['id_rol'] = $user->id_rol;
        $_SESSION['rol_nombre'] = $user->rol_nombre ?? '';
        $_SESSION['logged_in'] = true;
        
        // Redirigir según el rol
        if($user->id_rol == 1){
            redirect('pages/index'); // Admin
        } else {
            redirect('pages/index'); // Usuario regular
        }
    }
}