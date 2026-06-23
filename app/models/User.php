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
  }
