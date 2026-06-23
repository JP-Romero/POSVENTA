<?php
  class Product {
    private $db;

    public function __construct(){
      $this->db = new Database;
    }

    // Get all products with category and provider names
    public function getProducts(){
      $this->db->query('SELECT p.*, c.nombre as categoria_nombre, pr.nombre as proveedor_nombre
                        FROM productos p
                        INNER JOIN categorias c ON p.id_categoria = c.id
                        INNER JOIN proveedores pr ON p.id_proveedor = pr.id
                        ORDER BY p.nombre ASC');
      return $this->db->resultSet();
    }

    // Get product by ID
    public function getProductById($id){
      $this->db->query('SELECT * FROM productos WHERE id = :id');
      $this->db->bind(':id', $id);
      return $this->db->single();
    }

    // Add product
    public function addProduct($data){
      $this->db->query('INSERT INTO productos (codigo_interno, codigo_barras, nombre, descripcion, id_categoria, id_proveedor, precio_compra, precio_venta, stock, stock_minimo, imagen, estado)
                        VALUES (:codigo_interno, :codigo_barras, :nombre, :descripcion, :id_categoria, :id_proveedor, :precio_compra, :precio_venta, :stock, :stock_minimo, :imagen, :estado)');

      $this->db->bind(':codigo_interno', $data['codigo_interno']);
      $this->db->bind(':codigo_barras', $data['codigo_barras']);
      $this->db->bind(':nombre', $data['nombre']);
      $this->db->bind(':descripcion', $data['descripcion']);
      $this->db->bind(':id_categoria', $data['id_categoria']);
      $this->db->bind(':id_proveedor', $data['id_proveedor']);
      $this->db->bind(':precio_compra', $data['precio_compra']);
      $this->db->bind(':precio_venta', $data['precio_venta']);
      $this->db->bind(':stock', $data['stock']);
      $this->db->bind(':stock_minimo', $data['stock_minimo']);
      $this->db->bind(':imagen', $data['imagen']);
      $this->db->bind(':estado', $data['estado']);

      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }

    // Update product
    public function updateProduct($data){
      $this->db->query('UPDATE productos SET codigo_interno = :codigo_interno, codigo_barras = :codigo_barras, nombre = :nombre, descripcion = :descripcion, id_categoria = :id_categoria, id_proveedor = :id_proveedor, precio_compra = :precio_compra, precio_venta = :precio_venta, stock = :stock, stock_minimo = :stock_minimo, imagen = :imagen, estado = :estado WHERE id = :id');

      $this->db->bind(':id', $data['id']);
      $this->db->bind(':codigo_interno', $data['codigo_interno']);
      $this->db->bind(':codigo_barras', $data['codigo_barras']);
      $this->db->bind(':nombre', $data['nombre']);
      $this->db->bind(':descripcion', $data['descripcion']);
      $this->db->bind(':id_categoria', $data['id_categoria']);
      $this->db->bind(':id_proveedor', $data['id_proveedor']);
      $this->db->bind(':precio_compra', $data['precio_compra']);
      $this->db->bind(':precio_venta', $data['precio_venta']);
      $this->db->bind(':stock', $data['stock']);
      $this->db->bind(':stock_minimo', $data['stock_minimo']);
      $this->db->bind(':imagen', $data['imagen']);
      $this->db->bind(':estado', $data['estado']);

      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }

    // Delete product
    public function deleteProduct($id){
      $this->db->query('DELETE FROM productos WHERE id = :id');
      $this->db->bind(':id', $id);

      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }
  }
