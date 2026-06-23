<?php
  class Category {
    private $db;

    public function __construct(){
      $this->db = new Database;
    }

    // Get all categories
    public function getCategories(){
      $this->db->query('SELECT * FROM categorias ORDER BY nombre ASC');
      return $this->db->resultSet();
    }

    // Get category by ID
    public function getCategoryById($id){
      $this->db->query('SELECT * FROM categorias WHERE id = :id');
      $this->db->bind(':id', $id);
      return $this->db->single();
    }

    // Add category
    public function addCategory($data){
      $this->db->query('INSERT INTO categorias (nombre, descripcion) VALUES (:nombre, :descripcion)');
      $this->db->bind(':nombre', $data['nombre']);
      $this->db->bind(':descripcion', $data['descripcion']);

      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }

    // Update category
    public function updateCategory($data){
      $this->db->query('UPDATE categorias SET nombre = :nombre, descripcion = :descripcion WHERE id = :id');
      $this->db->bind(':id', $data['id']);
      $this->db->bind(':nombre', $data['nombre']);
      $this->db->bind(':descripcion', $data['descripcion']);

      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }

    // Delete category
    public function deleteCategory($id){
      $this->db->query('DELETE FROM categorias WHERE id = :id');
      $this->db->bind(':id', $id);

      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }
  }
