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
    
    // Get purchase history for provider
    public function getPurchaseHistory($id){
      $this->db->query('SELECT c.*, u.nombre as usuario_nombre
                        FROM compras c
                        INNER JOIN usuarios u ON c.id_usuario = u.id
                        WHERE c.id_proveedor = :id
                        ORDER BY c.fecha DESC');
      $this->db->bind(':id', $id);
      return $this->db->resultSet();
    }
    
    // Get purchase details for a specific purchase
    public function getPurchaseDetails($purchaseId){
      $this->db->query('SELECT dc.*, p.nombre as producto_nombre
                        FROM detalle_compras dc
                        INNER JOIN productos p ON dc.id_producto = p.id
                        WHERE dc.id_compra = :id');
      $this->db->bind(':id', $purchaseId);
      return $this->db->resultSet();
    }
  }
