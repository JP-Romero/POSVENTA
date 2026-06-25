<?php
class Users extends Controller {
    private $userModel;
    private $db;

    public function __construct(){
      $this->userModel = $this->model('User');
      $this->db = new Database;
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
                // Give default permissions to non-admin users
                if ($data['id_rol'] != 1) {
                    $userId = $this->db->lastInsertId();
                    $modules = $this->userModel->getModules();
                    $permissions = [];
                    foreach ($modules as $modulo => $nombre) {
                        $permissions[$modulo] = isset($_POST['perm_' . $modulo]) ? 1 : 0;
                    }
                    $this->userModel->updateUserPermissions($userId, $permissions);
                }
                flash('user_message', 'Usuario creado correctamente');
                redirect('users');
            }
        } else {
            $roles = $this->userModel->getRoles();
            $modules = $this->userModel->getModules();
            $data = ['roles' => $roles, 'modules' => $modules];
            $this->view('users/add', $data);
        }
    }

    public function edit($id){
        if(!isLoggedIn() || !isAdmin()){
            redirect('users/login');
        }
        
        // Prevent self-deactivation
        if ((int)$id === (int)$_SESSION['user_id']) {
            flash('user_message', 'No puede editar su propio usuario desde aquí', 'alert alert-warning');
            redirect('users');
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'id' => $id,
                'id_rol' => trim($_POST['id_rol']),
                'nombre' => trim($_POST['nombre']),
                'usuario' => trim($_POST['usuario']),
                'estado' => isset($_POST['estado']) ? 1 : 0,
                'password' => !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : '',
                'nombre_err' => '',
                'usuario_err' => '',
            ];

            // Validate
            if(empty($data['nombre'])){
                $data['nombre_err'] = 'Ingrese el nombre';
            }
            if(empty($data['usuario'])){
                $data['usuario_err'] = 'Ingrese el usuario';
            }

            // Check username unique (excluding current)
            $this->db->query('SELECT id FROM usuarios WHERE usuario = :usuario AND id != :id');
            $this->db->bind(':usuario', $data['usuario']);
            $this->db->bind(':id', $id);
            if ($this->db->rowCount() > 0) {
                $data['usuario_err'] = 'El nombre de usuario ya existe';
            }

            if(empty($data['nombre_err']) && empty($data['usuario_err'])){
                if($this->userModel->updateUser($data)){
                    // Update permissions if provided (for non-admin users)
                    if ($data['id_rol'] != 1) {
                        $permissions = [];
                        foreach ($this->userModel->getModules() as $modulo => $nombre) {
                            $permissions[$modulo] = isset($_POST['perm_' . $modulo]) ? 1 : 0;
                        }
                        $this->userModel->updateUserPermissions($id, $permissions);
                    }
                    flash('user_message', 'Usuario actualizado correctamente');
                    redirect('users');
                } else {
                    flash('user_message', 'Error al actualizar usuario', 'alert alert-danger');
                    redirect('users/edit/' . $id);
                }
            } else {
                $roles = $this->userModel->getRoles();
                $data['roles'] = $roles;
                $data['permissions'] = $this->userModel->getUserPermissions($id);
                $data['modules'] = $this->userModel->getModules();
                $this->view('users/edit', $data);
            }
        } else {
            $user = $this->userModel->getUserById($id);
            if(!$user){
                redirect('users');
            }
            
            $roles = $this->userModel->getRoles();
            $permissions = $this->userModel->getUserPermissions($id);
            $modules = $this->userModel->getModules();
            
            $data = [
                'id' => $id,
                'id_rol' => $user->id_rol,
                'nombre' => $user->nombre,
                'usuario' => $user->usuario,
                'estado' => $user->estado,
                'nombre_err' => '',
                'usuario_err' => '',
                'roles' => $roles,
                'permissions' => $permissions,
                'modules' => $modules
            ];
            $this->view('users/edit', $data);
        }
    }

    public function toggle($id){
        if(!isLoggedIn() || !isAdmin()){
            redirect('users/login');
        }
        
        // Prevent self-deactivation
        if ((int)$id === (int)$_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'No puede desactivar su propio usuario']);
            exit;
        }
        
        if ($this->userModel->toggleStatus($id)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al cambiar estado']);
        }
        exit;
    }

    public function login(){
      if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = $_POST['password'];

        $data =[
          'usuario' => trim($usuario),
          'password' => trim($password),
          'usuario_err' => '',
          'password_err' => '',
        ];

        if(empty($data['usuario'])){
          $data['usuario_err'] = 'Por favor ingrese su usuario';
        }
        if(empty($data['password'])){
          $data['password_err'] = 'Por favor ingrese su contraseña';
        }

        if($this->userModel->findUserByUsername($data['usuario'])){
        } else {
          $data['usuario_err'] = 'Usuario no encontrado';
        }

        if(empty($data['usuario_err']) && empty($data['password_err'])){
          $loggedInUser = $this->userModel->login($data['usuario'], $data['password']);

          if($loggedInUser){
            if (!$loggedInUser->estado) {
                $data['password_err'] = 'Usuario desactivado. Contacte al administrador.';
                $this->view('users/login', $data);
                return;
            }
            $this->createUserSession($loggedInUser);
          } else {
            $data['password_err'] = 'Contraseña incorrecta';
            $this->view('users/login', $data);
          }
        } else {
          $this->view('users/login', $data);
        }
      } else {
        $data =[
          'usuario' => '',
          'password' => '',
          'usuario_err' => '',
          'password_err' => '',
        ];
        $this->view('users/login', $data);
      }
    }

    public function createUserSession($user){
      session_regenerate_id(true);
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

    // Recuperar contraseña - Paso 1: Solicitar email
    public function recover(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $email = trim($_POST['email'] ?? '');
            
            $data = [
                'email' => $email,
                'email_err' => '',
                'success' => false
            ];
            
            if (empty($email)) {
                $data['email_err'] = 'Ingrese su correo electrónico';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'Correo inválido';
            } else {
                $user = $this->userModel->getUserByEmail($email);
                if ($user) {
                    // Generar token
                    $token = bin2hex(random_bytes(32));
                    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                    
                    // Limpiar tokens viejos
                    $this->userModel->db->query('DELETE FROM password_resets WHERE email = :email');
                    $this->userModel->db->bind(':email', $email);
                    $this->userModel->db->execute();
                    
                    // Crear nuevo token
                    if ($this->userModel->createResetToken($email, $token, $expires)) {
                        $resetLink = URLROOT . '/users/reset/' . $token;
                        // En producción: enviar email
                        // Por ahora: log a archivo
                        $logMsg = "[".date('Y-m-d H:i:s')."] Password reset for $email: $resetLink\n";
                        file_put_contents(APPROOT . '/../storage/logs/password_resets.log', $logMsg, FILE_APPEND);
                        
                        $data['success'] = true;
                        $data['reset_link'] = $resetLink; // Solo para desarrollo
                    }
                }
                // Siempre mostramos éxito por seguridad (no revelar si email existe)
                $data['success'] = true;
            }
            
            $this->view('users/recover', $data);
        } else {
            $data = ['email' => '', 'email_err' => '', 'success' => false];
            $this->view('users/recover', $data);
        }
    }

    // Recuperar contraseña - Paso 2: Formulario nueva contraseña
    public function reset($token = ''){
        $reset = $this->userModel->validateResetToken($token);
        
        if (!$reset) {
            flash('register_success', 'Token inválido o expirado', 'alert alert-danger');
            redirect('users/recover');
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            
            $data = [
                'token' => $token,
                'password_err' => '',
                'confirm_err' => ''
            ];
            
            if (empty($password)) {
                $data['password_err'] = 'Ingrese la nueva contraseña';
            } elseif (strlen($password) < 6) {
                $data['password_err'] = 'Mínimo 6 caracteres';
            }
            
            if ($password !== $confirm) {
                $data['confirm_err'] = 'Las contraseñas no coinciden';
            }
            
            if (empty($data['password_err']) && empty($data['confirm_err'])) {
                if ($this->userModel->resetPassword($reset->email, $password)) {
                    $this->userModel->useResetToken($token);
                    flash('register_success', 'Contraseña actualizada. Ya puede iniciar sesión.', 'alert alert-success');
                    redirect('users/login');
                } else {
                    $data['password_err'] = 'Error al actualizar contraseña';
                }
            }
            
            $this->view('users/reset', $data);
        } else {
            $data = ['token' => $token, 'password_err' => '', 'confirm_err' => ''];
            $this->view('users/reset', $data);
        }
    }
}