<?php
  class Client {
    private $db;

    public function __construct(){
      $this->db = new Database;
    }

    public function getClients(){
      $this->db->query('SELECT * FROM clientes ORDER BY nombre ASC');
      return $this->db->resultSet();
    }

    public function getClientById($id){
      $this->db->query('SELECT * FROM clientes WHERE id = :id');
      $this->db->bind(':id', $id);
      return $this->db->single();
    }

    public function addClient($data){
      $this->db->query('INSERT INTO clientes (nombre, telefono, correo, direccion) VALUES (:nombre, :telefono, :correo, :direccion)');
      $this->db->bind(':nombre', $data['nombre']);
      $this->db->bind(':telefono', $data['telefono']);
      $this->db->bind(':correo', $data['correo']);
      $this->db->bind(':direccion', $data['direccion']);

      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }

    public function updateClient($data){
      $this->db->query('UPDATE clientes SET nombre = :nombre, telefono = :telefono, correo = :correo, direccion = :direccion WHERE id = :id');
      $this->db->bind(':id', $data['id']);
      $this->db->bind(':nombre', $data['nombre']);
      $this->db->bind(':telefono', $data['telefono']);
      $this->db->bind(':correo', $data['correo']);
      $this->db->bind(':direccion', $data['direccion']);

      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }
  }
