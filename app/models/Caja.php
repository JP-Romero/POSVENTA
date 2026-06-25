<?php
  class Caja {
    private $db;

    public function __construct(){
      $this->db = new Database;
    }

    // Registrar un movimiento de caja (entrada o salida)
    public function addMovimiento($data){
      $this->db->query('INSERT INTO movimientos_caja (id_usuario, tipo, concepto, monto) VALUES (:id_usuario, :tipo, :concepto, :monto)');
      $this->db->bind(':id_usuario', $_SESSION['user_id']);
      $this->db->bind(':tipo', $data['tipo']);
      $this->db->bind(':concepto', $data['concepto']);
      $this->db->bind(':monto', $data['monto']);

      return $this->db->execute();
    }

    // Obtener movimientos entre dos fechas (por defecto desde el último corte hasta ahora)
    public function getMovimientos($fecha_inicio, $fecha_fin = null){
      if ($fecha_fin == null) {
          $fecha_fin = date('Y-m-d H:i:s');
      }
      $this->db->query('SELECT mc.*, u.nombre as usuario FROM movimientos_caja mc INNER JOIN usuarios u ON mc.id_usuario = u.id WHERE mc.fecha >= :fecha_inicio AND mc.fecha <= :fecha_fin ORDER BY mc.fecha DESC');
      $this->db->bind(':fecha_inicio', $fecha_inicio);
      $this->db->bind(':fecha_fin', $fecha_fin);
      return $this->db->resultSet();
    }
    
    // Obtener el total de entradas y salidas para un rango de fechas
    public function getTotalesMovimientos($fecha_inicio, $fecha_fin = null){
      if ($fecha_fin == null) {
          $fecha_fin = date('Y-m-d H:i:s');
      }
      $this->db->query("SELECT 
                        SUM(CASE WHEN tipo = 'Entrada' THEN monto ELSE 0 END) as total_entradas,
                        SUM(CASE WHEN tipo = 'Salida' THEN monto ELSE 0 END) as total_salidas
                        FROM movimientos_caja 
                        WHERE fecha >= :fecha_inicio AND fecha <= :fecha_fin");
      $this->db->bind(':fecha_inicio', $fecha_inicio);
      $this->db->bind(':fecha_fin', $fecha_fin);
      return $this->db->single();
    }

    // Obtener el último corte de caja
    public function getUltimoCorte(){
      $this->db->query('SELECT * FROM cortes_caja ORDER BY fecha_corte DESC LIMIT 1');
      return $this->db->single();
    }

    // Guardar un nuevo corte de caja (Reporte Z)
    public function saveCorte($data){
      $this->db->query('INSERT INTO cortes_caja 
        (id_usuario, fecha_inicio, fecha_fin, ventas_brutas, descuentos, ventas_netas, 
        total_efectivo, total_tarjeta, total_transferencia, fondo_inicial, ingresos_caja, 
        egresos_caja, efectivo_esperado, efectivo_real, diferencia, tickets_emitidos, 
        ticket_promedio, primer_ticket, ultimo_ticket) 
        VALUES 
        (:id_usuario, :fecha_inicio, :fecha_fin, :ventas_brutas, :descuentos, :ventas_netas, 
        :total_efectivo, :total_tarjeta, :total_transferencia, :fondo_inicial, :ingresos_caja, 
        :egresos_caja, :efectivo_esperado, :efectivo_real, :diferencia, :tickets_emitidos, 
        :ticket_promedio, :primer_ticket, :ultimo_ticket)');
        
      $this->db->bind(':id_usuario', $_SESSION['user_id']);
      $this->db->bind(':fecha_inicio', $data['fecha_inicio']);
      $this->db->bind(':fecha_fin', $data['fecha_fin']);
      $this->db->bind(':ventas_brutas', $data['ventas_brutas']);
      $this->db->bind(':descuentos', $data['descuentos']);
      $this->db->bind(':ventas_netas', $data['ventas_netas']);
      $this->db->bind(':total_efectivo', $data['total_efectivo']);
      $this->db->bind(':total_tarjeta', $data['total_tarjeta']);
      $this->db->bind(':total_transferencia', $data['total_transferencia']);
      $this->db->bind(':fondo_inicial', $data['fondo_inicial']);
      $this->db->bind(':ingresos_caja', $data['ingresos_caja']);
      $this->db->bind(':egresos_caja', $data['egresos_caja']);
      $this->db->bind(':efectivo_esperado', $data['efectivo_esperado']);
      $this->db->bind(':efectivo_real', $data['efectivo_real']);
      $this->db->bind(':diferencia', $data['diferencia']);
      $this->db->bind(':tickets_emitidos', $data['tickets_emitidos']);
      $this->db->bind(':ticket_promedio', $data['ticket_promedio']);
      $this->db->bind(':primer_ticket', $data['primer_ticket']);
      $this->db->bind(':ultimo_ticket', $data['ultimo_ticket']);

      if($this->db->execute()){
        return $this->db->lastInsertId();
      } else {
        return false;
      }
    }
    
    // Obtener un corte específico
    public function getCorteById($id){
      $this->db->query('SELECT cc.*, u.nombre as usuario FROM cortes_caja cc INNER JOIN usuarios u ON cc.id_usuario = u.id WHERE cc.id = :id');
      $this->db->bind(':id', $id);
      return $this->db->single();
    }
    
    // Obtener historial de cortes
    public function getCortes($limit = 50){
      $this->db->query('SELECT cc.*, u.nombre as usuario FROM cortes_caja cc INNER JOIN usuarios u ON cc.id_usuario = u.id ORDER BY cc.fecha_corte DESC LIMIT :limit');
      $this->db->bind(':limit', $limit, 'int');
      return $this->db->resultSet();
    }
  }
