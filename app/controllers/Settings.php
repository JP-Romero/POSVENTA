<?php
class Settings extends Controller {
    public function __construct() {
        if (!isLoggedIn() || !isAdmin()) {
            redirect('users/login');
        }
        $this->db = new Database;
    }

    public function index() {
        $tab = $_GET['tab'] ?? 'general';
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                flash('settings_message', 'Error de seguridad: token inválido. Intente nuevamente.', 'alert alert-danger');
                redirect('settings?tab=' . ($_GET['tab'] ?? 'general'));
                return;
            }
            $action = $_POST['action'] ?? 'update_general';
            
            switch ($action) {
                case 'update_general':
                    $ivaEnabled = isset($_POST['iva_enabled']) ? 1 : 0;
                    $paymentMethods = [];
                    if (isset($_POST['pm_efectivo'])) $paymentMethods[] = 'efectivo';
                    if (isset($_POST['pm_tarjeta'])) $paymentMethods[] = 'tarjeta';
                    if (isset($_POST['pm_dolar'])) $paymentMethods[] = 'dolar';
                    if (isset($_POST['pm_mixto'])) $paymentMethods[] = 'mixto';

                    $this->db->query('UPDATE configuracion SET nombre_negocio = :nombre, ruc = :ruc, direccion = :direccion, telefono = :telefono, correo = :correo, iva = :iva, iva_enabled = :iva_enabled, exchange_rate = :exchange_rate, payment_methods = :payment_methods WHERE id = 1');
                    $this->db->bind(':nombre', $_POST['nombre_negocio']);
                    $this->db->bind(':ruc', $_POST['ruc']);
                    $this->db->bind(':direccion', $_POST['direccion']);
                    $this->db->bind(':telefono', $_POST['telefono']);
                    $this->db->bind(':correo', $_POST['correo']);
                    $this->db->bind(':iva', $_POST['iva']);
                    $this->db->bind(':iva_enabled', $ivaEnabled);
                    $this->db->bind(':exchange_rate', str_replace(',', '.', $_POST['exchange_rate']));
                    $this->db->bind(':payment_methods', implode(',', $paymentMethods));
                    
                    // Handle logo upload (MIME validated server-side)
                    if (!empty($_FILES['logo']['name'])) {
                        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $file_type = finfo_file($finfo, $_FILES['logo']['tmp_name']);
                        finfo_close($finfo);
                        if (in_array($file_type, $allowed)) {
                            $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                            $filename = 'logo_' . time() . '.' . $ext;
                            $target = APPROOT . '/../public/img/logo/' . $filename;
                            
                            if (!file_exists(APPROOT . '/../public/img/logo/')) {
                                mkdir(APPROOT . '/../public/img/logo/', 0777, true);
                            }
                            
                            if (move_uploaded_file($_FILES['logo']['tmp_name'], $target)) {
                                // Delete old logo
                                $this->db->query('SELECT logo FROM configuracion WHERE id = 1');
                                $old = $this->db->single();
                                if ($old->logo && file_exists(APPROOT . '/../public/img/logo/' . $old->logo)) {
                                    unlink(APPROOT . '/../public/img/logo/' . $old->logo);
                                }
                                $this->db->query('UPDATE configuracion SET logo = :logo WHERE id = 1');
                                $this->db->bind(':logo', $filename);
                                $this->db->execute();
                            }
                        }
                    }
                    
                    if($this->db->execute()) {
                        flash('settings_message', 'Configuración general actualizada');
                    }
                    redirect('settings?tab=general');
                    break;
                    
                case 'add_printer':
                    $this->db->query('INSERT INTO impresoras (nombre, tipo, conexion, ancho_papel, activa) VALUES (:nombre, :tipo, :conexion, :ancho, :activa)');
                    $this->db->bind(':nombre', $_POST['imp_nombre']);
                    $this->db->bind(':tipo', $_POST['imp_tipo']);
                    $this->db->bind(':conexion', $_POST['imp_conexion']);
                    $this->db->bind(':ancho', (int)$_POST['imp_ancho']);
                    $this->db->bind(':activa', isset($_POST['imp_activa']) ? 1 : 0);
                    
                    // Desactivar otras si esta es activa
                    if (isset($_POST['imp_activa'])) {
                        $this->db->query('UPDATE impresoras SET activa = 0 WHERE id != LAST_INSERT_ID()');
                        $this->db->execute();
                    }
                    
                    if($this->db->execute()) {
                        flash('settings_message', 'Impresora agregada');
                    }
                    redirect('settings?tab=impresoras');
                    break;
                    
                case 'update_printer':
                    $this->db->query('UPDATE impresoras SET nombre = :nombre, tipo = :tipo, conexion = :conexion, ancho_papel = :ancho, activa = :activa WHERE id = :id');
                    $this->db->bind(':id', (int)$_POST['imp_id']);
                    $this->db->bind(':nombre', $_POST['imp_nombre']);
                    $this->db->bind(':tipo', $_POST['imp_tipo']);
                    $this->db->bind(':conexion', $_POST['imp_conexion']);
                    $this->db->bind(':ancho', (int)$_POST['imp_ancho']);
                    $this->db->bind(':activa', isset($_POST['imp_activa']) ? 1 : 0);
                    
                    if (isset($_POST['imp_activa'])) {
                        $this->db->query('UPDATE impresoras SET activa = 0 WHERE id != :id');
                        $this->db->bind(':id', (int)$_POST['imp_id']);
                        $this->db->execute();
                    }
                    
                    if($this->db->execute()) {
                        flash('settings_message', 'Impresora actualizada');
                    }
                    redirect('settings?tab=impresoras');
                    break;
                    
                case 'delete_printer':
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $this->db->query('DELETE FROM impresoras WHERE id = :id');
                        $this->db->bind(':id', (int)$_POST['imp_id']);
                        if($this->db->execute()) {
                            flash('settings_message', 'Impresora eliminada');
                        }
                    }
                    redirect('settings?tab=impresoras');
                    break;
                    
                case 'test_printer':
                    $this->db->query('SELECT * FROM impresoras WHERE id = :id');
                    $this->db->bind(':id', (int)$_POST['imp_id']);
                    $printer = $this->db->single();
                    
                    if ($printer) {
                        require_once APPROOT . '/lib/ReceiptPrinter.php';
                        $rp = new \App\Lib\ReceiptPrinter([
                            'type' => strtolower($printer->tipo),
                            'path' => $printer->conexion,
                            'paper_width' => $printer->ancho_papel,
                        ]);
                        if ($rp->testPrint()) {
                            echo json_encode(['success' => true, 'message' => 'Test de impresión enviado']);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Error en impresión']);
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Impresora no encontrada']);
                    }
                    exit;
            }
        }
        
        // Get settings
        $this->db->query('SELECT * FROM configuracion WHERE id = 1');
        $settings = $this->db->single();
        
        // Get printers
        $this->db->query('SELECT * FROM impresoras ORDER BY activa DESC, nombre ASC');
        $printers = $this->db->resultSet();
        
        $data = [
            'settings' => $settings,
            'printers' => $printers,
            'activeTab' => $tab
        ];
        
        $this->view('settings/index', $data);
    }
}