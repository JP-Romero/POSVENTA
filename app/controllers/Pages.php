<?php
  class Pages extends Controller {
    public function __construct(){
      if(!isLoggedIn()){
        redirect('users/login');
      }
    }

    public function index(){
      $data = [
        'title' => 'Librería Pos',
      ];

      $this->view('pages/index', $data);
    }

    public function about(){
      $data = [
        'title' => 'Sobre Nosotros'
      ];

      $this->view('pages/about', $data);
    }
  }
