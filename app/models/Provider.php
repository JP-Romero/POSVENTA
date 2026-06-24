<?php
  class Provider {
    private $db;

    public function __construct(){
      $this->db = new Database;
    }

    public function getProviders(){
      $this->db->query('SELECT * FROM proveedores ORDER BY nombre ASC');
      return $this->db->resultSet();
    }

    public function getProviderById($id){
      $this->db->query('SELECT * FROM proveedores WHERE id = :id');
      $this->db->bind(':id', $id);
      return $this->db->single();
    }

    public function addProvider($data){
      $this->db->query('INSERT INTO proveedores (nombre, contacto, telefono, correo, direccion) VALUES (:nombre, :contacto, :telefono, :correo, :direccion)');
      $this->db->bind(':nombre', $data['nombre']);
      $this->db->bind(':contacto', $data['contacto']);
      $this->db->bind(':telefono', $data['telefono']);
      $this->db->bind(':correo', $data['correo']);
      $this->db->bind(':direccion', $data['direccion']);

      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }

    public function updateProvider($data){
      $this->db->query('UPDATE proveedores SET nombre = :nombre, contacto = :contacto, telefono = :telefono, correo = :correo, direccion = :direccion WHERE id = :id');
      $this->db->bind(':id', $data['id']);
      $this->db->bind(':nombre', $data['nombre']);
      $this->db->bind(':contacto', $data['contacto']);
      $this->db->bind(':telefono', $data['telefono']);
      $this->db->bind(':correo', $data['correo']);
      $this->db->bind(':direccion', $data['direccion']);

      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }

    public function deleteProvider($id){
      $this->db->query('DELETE FROM proveedores WHERE id = :id');
      $this->db->bind(':id', $id);

      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }
  }
