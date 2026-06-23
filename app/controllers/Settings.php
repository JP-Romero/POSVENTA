<?php
class Settings extends Controller {
    public function __construct() {
        if (!isLoggedIn() || !isAdmin()) {
            redirect('users/login');
        }
        $this->db = new Database;
    }

    public function index() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $this->db->query('UPDATE configuracion SET nombre_negocio = :nombre, ruc = :ruc, direccion = :direccion, telefono = :telefono, correo = :correo, iva = :iva WHERE id = 1');
            $this->db->bind(':nombre', $_POST['nombre_negocio']);
            $this->db->bind(':ruc', $_POST['ruc']);
            $this->db->bind(':direccion', $_POST['direccion']);
            $this->db->bind(':telefono', $_POST['telefono']);
            $this->db->bind(':correo', $_POST['correo']);
            $this->db->bind(':iva', $_POST['iva']);

            if($this->db->execute()) {
                flash('settings_message', 'Configuración actualizada');
                redirect('settings');
            }
        }

        $this->db->query('SELECT * FROM configuracion WHERE id = 1');
        $settings = $this->db->single();

        $data = ['settings' => $settings];
        $this->view('settings/index', $data);
    }
}
