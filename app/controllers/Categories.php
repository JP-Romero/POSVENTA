<?php
  class Categories extends Controller {
    private $categoryModel;

    public function __construct(){
      if(!isLoggedIn()){
        redirect('users/login');
      }

      // Admin only for critical actions, but let's assume all category management is admin for now
      if(!isAdmin()){
        flash('access_error', 'No tiene permisos para acceder a este módulo', 'alert alert-danger');
        redirect('pages/index');
      }

      $this->categoryModel = $this->model('Category');
    }

    public function index(){
      $categories = $this->categoryModel->getCategories();

      $data = [
        'categories' => $categories
      ];

      $this->view('categories/index', $data);
    }

    public function add(){
      if($_SERVER['REQUEST_METHOD'] == 'POST'){
        // Sanitize
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $data = [
          'nombre' => trim($_POST['nombre']),
          'descripcion' => trim($_POST['descripcion']),
          'nombre_err' => ''
        ];

        // Validate
        if(empty($data['nombre'])){
          $data['nombre_err'] = 'Por favor ingrese el nombre de la categoría';
        }

        // Make sure no errors
        if(empty($data['nombre_err'])){
          if($this->categoryModel->addCategory($data)){
            flash('category_message', 'Categoría agregada correctamente');
            redirect('categories');
          } else {
            flash('category_message', 'Error al agregar categoría', 'alert alert-danger');
            redirect('categories/add');
          }
        } else {
          $this->view('categories/add', $data);
        }

      } else {
        $data = [
          'nombre' => '',
          'descripcion' => '',
          'nombre_err' => ''
        ];

        $this->view('categories/add', $data);
      }
    }

    public function edit($id){
      if($_SERVER['REQUEST_METHOD'] == 'POST'){
        // Sanitize
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $data = [
          'id' => $id,
          'nombre' => trim($_POST['nombre']),
          'descripcion' => trim($_POST['descripcion']),
          'nombre_err' => ''
        ];

        // Validate
        if(empty($data['nombre'])){
          $data['nombre_err'] = 'Por favor ingrese el nombre de la categoría';
        }

        // Make sure no errors
        if(empty($data['nombre_err'])){
          if($this->categoryModel->updateCategory($data)){
            flash('category_message', 'Categoría actualizada correctamente');
            redirect('categories');
          } else {
            flash('category_message', 'Error al actualizar categoría', 'alert alert-danger');
            redirect('categories/edit/' . $id);
          }
        } else {
          $this->view('categories/edit', $data);
        }

      } else {
        // Get existing category
        $category = $this->categoryModel->getCategoryById($id);

        if(!$category){
            redirect('categories');
        }

        $data = [
          'id' => $id,
          'nombre' => $category->nombre,
          'descripcion' => $category->descripcion,
          'nombre_err' => ''
        ];

        $this->view('categories/edit', $data);
      }
    }

    public function delete($id){
      if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if($this->categoryModel->deleteCategory($id)){
          flash('category_message', 'Categoría eliminada correctamente');
          redirect('categories');
        } else {
          flash('category_message', 'Error al eliminar categoría', 'alert alert-danger');
          redirect('categories');
        }
      } else {
        redirect('categories');
      }
    }
  }
