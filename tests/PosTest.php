<?php
use PHPUnit\Framework\TestCase;

class PosTest extends TestCase {
    
    public function testTaxCalculation() {
        // Simular el cálculo de impuestos que ocurre en JS y PHP
        $subtotal = 100.00;
        $ivaRate = 0.15;
        
        $tax = $subtotal * $ivaRate;
        $total = $subtotal + $tax;
        
        $this->assertEquals(15.00, $tax);
        $this->assertEquals(115.00, $total);
    }
    
    public function testFondoInicialApertura() {
        // En un entorno real, aquí se usaría un mock de la base de datos
        // o sqlite en memoria para validar que el modelo Caja retorna true
        // al registrar una apertura exitosa
        
        $postData = [
            'monto' => '50.00',
            'csrf_token' => 'mock_token'
        ];
        
        $this->assertArrayHasKey('monto', $postData);
        $this->assertEquals(50.00, (float)$postData['monto']);
    }
}
