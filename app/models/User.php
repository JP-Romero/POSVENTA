<?php
  class User {
    private $db;

    public function __construct(){
      $this->db = new Database;
    }

    // Login User
    public function login($username, $password){
      $this->db->query('SELECT * FROM usuarios WHERE usuario = :username');
      $this->db->bind(':username', $username);

      $row = $this->db->single();

      if($row){
        $hashed_password = $row->password;
        if(password_verify($password, $hashed_password)){
          return $row;
        } else {
          return false;
        }
      } else {
        return false;
      }
    }

    // Find user by username
    public function findUserByUsername($username){
      $this->db->query('SELECT * FROM usuarios WHERE usuario = :username');
      $this->db->bind(':username', $username);

      $row = $this->db->single();

      // Check row
      if($this->db->rowCount() > 0){
        return true;
      } else {
        return false;
      }
    }

    // Get User by ID
    public function getUserById($id){
      $this->db->query('SELECT u.*, r.nombre as rol_nombre
                        FROM usuarios u
                        INNER JOIN roles r ON u.id_rol = r.id
                        WHERE u.id = :id');
      $this->db->bind(':id', $id);

      $row = $this->db->single();

      return $row;
    }

    // Get all users
    public function getUsers(){
      $this->db->query('SELECT u.*, r.nombre as rol_nombre
                        FROM usuarios u
                        INNER JOIN roles r ON u.id_rol = r.id');
      return $this->db->resultSet();
    }

    // Add user
    public function addUser($data){
      $this->db->query('INSERT INTO usuarios (id_rol, nombre, usuario, password, estado) VALUES (:id_rol, :nombre, :usuario, :password, :estado)');
      $this->db->bind(':id_rol', $data['id_rol']);
      $this->db->bind(':nombre', $data['nombre']);
      $this->db->bind(':usuario', $data['usuario']);
      $this->db->bind(':password', $data['password']);
      $this->db->bind(':estado', $data['estado']);

      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }

    // Get roles
    public function getRoles(){
      $this->db->query('SELECT * FROM roles');
      return $this->db->resultSet();
    }
    
    // Update user
    public function updateUser($data){
      if (!empty($data['password'])) {
        $this->db->query('UPDATE usuarios SET id_rol = :id_rol, nombre = :nombre, usuario = :usuario, password = :password, estado = :estado WHERE id = :id');
        $this->db->bind(':password', $data['password']);
      } else {
        $this->db->query('UPDATE usuarios SET id_rol = :id_rol, nombre = :nombre, usuario = :usuario, estado = :estado WHERE id = :id');
      }
      $this->db->bind(':id', $data['id']);
      $this->db->bind(':id_rol', $data['id_rol']);
      $this->db->bind(':nombre', $data['nombre']);
      $this->db->bind(':usuario', $data['usuario']);
      $this->db->bind(':estado', $data['estado']);
      
      return $this->db->execute();
    }
    
    // Get user permissions
    public function getUserPermissions($userId){
      $this->db->query('SELECT modulo, acceso FROM usuario_permisos WHERE id_usuario = :id_usuario');
      $this->db->bind(':id_usuario', $userId);
      $results = $this->db->resultSet();
      
      $permissions = [];
      foreach ($results as $row) {
        $permissions[$row->modulo] = (int)$row->acceso;
      }
      return $permissions;
    }
    
    // Get all modules
    public function getModules(){
      return [
        'products' => 'Productos',
        'categories' => 'Categorías',
        'providers' => 'Proveedores',
        'purchases' => 'Compras',
        'sales' => 'Ventas',
        'inventory' => 'Inventario',
        'reports' => 'Reportes',
        'settings' => 'Configuración',
        'users' => 'Usuarios'
      ];
    }
    
    // Update user permissions
    public function updateUserPermissions($userId, $permissions){
      // Delete existing permissions
      $this->db->query('DELETE FROM usuario_permisos WHERE id_usuario = :id_usuario');
      $this->db->bind(':id_usuario', $userId);
      $this->db->execute();
      
      // Insert new permissions
      $this->db->query('INSERT INTO usuario_permisos (id_usuario, modulo, acceso) VALUES (:id_usuario, :modulo, :acceso)');
      foreach ($permissions as $modulo => $acceso) {
        $this->db->bind(':id_usuario', $userId);
        $this->db->bind(':modulo', $modulo);
        $this->db->bind(':acceso', $acceso ? 1 : 0);
        $this->db->execute();
      }
      return true;
    }
    
    // Check user module access
    public function canAccessModule($userId, $module){
      $this->db->query('SELECT acceso FROM usuario_permisos WHERE id_usuario = :id_usuario AND modulo = :modulo');
      $this->db->bind(':id_usuario', $userId);
      $this->db->bind(':modulo', $module);
      $result = $this->db->single();
      return $result ? (bool)$result->acceso : false;
    }
    
    // Toggle user status
    public function toggleStatus($id){
      $this->db->query('UPDATE usuarios SET estado = CASE WHEN estado = 1 THEN 0 ELSE 1 END WHERE id = :id');
      $this->db->bind(':id', $id);
      return $this->db->execute();
    }
    
    // Get user by email (for password reset)
    public function getUserByEmail($email){
      $this->db->query('SELECT * FROM usuarios WHERE correo = :email');
      $this->db->bind(':email', $email);
      return $this->db->single();
    }
    
    // Create password reset token
    public function createResetToken($email, $token, $expires){
      $this->db->query('INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires)');
      $this->db->bind(':email', $email);
      $this->db->bind(':token', $token);
      $this->db->bind(':expires', $expires);
      return $this->db->execute();
    }
    
    // Validate reset token
    public function validateResetToken($token){
      $this->db->query('SELECT * FROM password_resets WHERE token = :token AND used = 0 AND expires_at > NOW()');
      $this->db->bind(':token', $token);
      return $this->db->single();
    }
    
    // Mark token as used
    public function useResetToken($token){
      $this->db->query('UPDATE password_resets SET used = 1 WHERE token = :token');
      $this->db->bind(':token', $token);
      return $this->db->execute();
    }
    
    // Reset password
    public function resetPassword($email, $newPassword){
      $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
      $this->db->query('UPDATE usuarios SET password = :pass WHERE correo = :email');
      $this->db->bind(':pass', $hashed);
      $this->db->bind(':email', $email);
      return $this->db->execute();
    }
  }
