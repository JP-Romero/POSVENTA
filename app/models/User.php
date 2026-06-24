<?php
  class User {
    private $db;

    public function __construct(){
      $this->db = new Database;
    }

    // Login User
    public function login($username, $password){
      $this->db->query('SELECT * FROM usuarios WHERE usuario = :username AND estado = 1');
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
      if(!empty($data['password'])){
        $this->db->query('UPDATE usuarios SET id_rol = :id_rol, nombre = :nombre, usuario = :usuario, password = :password WHERE id = :id');
        $this->db->bind(':password', $data['password']);
      } else {
        $this->db->query('UPDATE usuarios SET id_rol = :id_rol, nombre = :nombre, usuario = :usuario WHERE id = :id');
      }

      $this->db->bind(':id', $data['id']);
      $this->db->bind(':id_rol', $data['id_rol']);
      $this->db->bind(':nombre', $data['nombre']);
      $this->db->bind(':usuario', $data['usuario']);

      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }

    // Toggle status
    public function toggleStatus($id, $estado){
      $this->db->query('UPDATE usuarios SET estado = :estado WHERE id = :id');
      $this->db->bind(':id', $id);
      $this->db->bind(':estado', $estado);

      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }
  }
