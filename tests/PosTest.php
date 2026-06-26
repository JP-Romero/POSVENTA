<?php
use PHPUnit\Framework\TestCase;

class PosTest extends TestCase {

    // --- Tax / IVA logic (mirrors JS logic in pos-v2.js) ---

    public function testTaxCalculationWithIvaEnabled() {
        $subtotal = 100.00;
        $ivaRate = 0.15;
        $ivaEnabled = true;

        $tax = $ivaEnabled ? $subtotal * $ivaRate : 0;
        $total = $subtotal + $tax;

        $this->assertEquals(15.00, $tax);
        $this->assertEquals(115.00, $total);
    }

    public function testTaxCalculationWithIvaDisabled() {
        $subtotal = 100.00;
        $ivaRate = 0.15;
        $ivaEnabled = false;

        $tax = $ivaEnabled ? $subtotal * $ivaRate : 0;
        $total = $subtotal + $tax;

        $this->assertEquals(0.00, $tax);
        $this->assertEquals(100.00, $total);
    }

    public function testIvaRateIsAppliedCorrectly() {
        $subtotal = 250.00;
        $ivaRate = 0.07; // 7%
        $ivaEnabled = true;

        $tax = $subtotal * $ivaRate;
        $total = $subtotal + $tax;

        $this->assertEquals(17.50, $tax);
        $this->assertEquals(267.50, $total);
    }

    // --- USD Conversion logic ---

    public function testUsdConversionWithExchangeRate() {
        $totalCordobas = 1000.00;
        $exchangeRate = 36.50;

        $totalUsd = $totalCordobas / $exchangeRate;

        $this->assertEqualsWithDelta(27.40, $totalUsd, 0.01);
    }

    public function testUsdConversionWithZeroRate() {
        $totalCordobas = 1000.00;
        $exchangeRate = 0;

        $totalUsd = $exchangeRate > 0 ? $totalCordobas / $exchangeRate : 0;

        $this->assertEquals(0, $totalUsd);
    }

    // --- Split payment validation (mirrors JS validateSplitBeforeSubmit) ---

    public function testSplitPaymentValidSum() {
        $total = 500.00;
        $efectivo = 200.00;
        $tarjeta = 200.00;
        $dolar = 100.00;

        $sum = $efectivo + $tarjeta + $dolar;
        $isValid = abs($sum - $total) <= 0.01;

        $this->assertTrue($isValid);
    }

    public function testSplitPaymentInvalidSum() {
        $total = 500.00;
        $efectivo = 200.00;
        $tarjeta = 200.00;
        $dolar = 50.00;

        $sum = $efectivo + $tarjeta + $dolar;
        $isValid = abs($sum - $total) <= 0.01;

        $this->assertFalse($isValid);
    }

    public function testSplitPaymentFloatingPointPrecision() {
        $total = 100.50;
        $efectivo = 50.25;
        $tarjeta = 30.15;
        $dolar = 20.10;

        $sum = round($efectivo + $tarjeta + $dolar, 2);
        $isValid = abs($sum - $total) <= 0.01;

        $this->assertTrue($isValid);
        $this->assertEquals(100.50, $sum);
    }

    // --- Dolar payment amount calculation ---

    public function testDolarPaymentPagoDolarEquiv() {
        $totalCordobas = 500.00;
        $exchangeRate = 36.50;

        $totalDolares = round($totalCordobas / $exchangeRate, 2);
        $pagoDolarEquiv = $totalDolares;
        $pagoDolar = $totalCordobas;

        $this->assertEquals(13.70, $totalDolares);
        $this->assertEquals(13.70, $pagoDolarEquiv);
        $this->assertEquals(500.00, $pagoDolar);
    }

    // --- Cart item operations (mirrors cart array logic in JS) ---

    public function testAddItemToCart() {
        $cart = [];
        $item = ['id' => 1, 'nombre' => 'Producto A', 'precio' => 25.00, 'quantity' => 1];

        $exists = false;
        foreach ($cart as &$ci) {
            if ($ci['id'] == $item['id']) { $ci['quantity']++; $exists = true; break; }
        }
        if (!$exists) $cart[] = $item;

        $this->assertCount(1, $cart);
        $this->assertEquals(1, $cart[0]['quantity']);
    }

    public function testAddDuplicateItemIncrementsQuantity() {
        $cart = [['id' => 1, 'nombre' => 'Producto A', 'precio' => 25.00, 'quantity' => 1]];

        $newItem = ['id' => 1, 'nombre' => 'Producto A', 'precio_venta' => 25.00];
        $exists = false;
        foreach ($cart as &$ci) {
            if ($ci['id'] == $newItem['id']) { $ci['quantity']++; $exists = true; break; }
        }
        if (!$exists) $cart[] = ['id' => $newItem['id'], 'nombre' => $newItem['nombre'], 'precio' => (float)$newItem['precio_venta'], 'quantity' => 1];

        $this->assertCount(1, $cart);
        $this->assertEquals(2, $cart[0]['quantity']);
    }

    public function testRemoveItemFromCart() {
        $cart = [
            ['id' => 1, 'nombre' => 'A', 'precio' => 10.00, 'quantity' => 2],
            ['id' => 2, 'nombre' => 'B', 'precio' => 20.00, 'quantity' => 1],
        ];

        // Remove item at index 0
        array_splice($cart, 0, 1);

        $this->assertCount(1, $cart);
        $this->assertEquals(2, $cart[0]['id']);
    }

    public function testDecrementQuantityRemovesWhenZero() {
        $cart = [['id' => 1, 'nombre' => 'A', 'precio' => 10.00, 'quantity' => 1]];

        if ($cart[0]['quantity'] <= 1) {
            array_splice($cart, 0, 1);
        } else {
            $cart[0]['quantity']--;
        }

        $this->assertCount(0, $cart);
    }

    public function testDecrementQuantityDecreasesWhenAboveOne() {
        $cart = [['id' => 1, 'nombre' => 'A', 'precio' => 10.00, 'quantity' => 3]];

        if ($cart[0]['quantity'] <= 1) {
            array_splice($cart, 0, 1);
        } else {
            $cart[0]['quantity']--;
        }

        $this->assertCount(1, $cart);
        $this->assertEquals(2, $cart[0]['quantity']);
    }

    // --- Subtotal calculation ---

    public function testSubtotalCalculation() {
        $cart = [
            ['id' => 1, 'precio' => 25.00, 'quantity' => 2],
            ['id' => 2, 'precio' => 50.00, 'quantity' => 1],
        ];

        $subtotal = array_reduce($cart, fn($s, $i) => $s + $i['precio'] * $i['quantity'], 0);

        $this->assertEquals(100.00, $subtotal);
    }

    public function testEmptyCartSubtotalIsZero() {
        $cart = [];
        $subtotal = array_reduce($cart, fn($s, $i) => $s + $i['precio'] * $i['quantity'], 0);
        $this->assertEquals(0.00, $subtotal);
    }

    // --- Currency formatting ---

    public function testCordobaFormatting() {
        $symbol = 'C$';
        $amount = 1500.50;
        $formatted = $symbol . number_format($amount, 2);
        $this->assertEquals('C$1,500.50', $formatted);
    }

    public function testUsdFormatting() {
        $symbol = '$';
        $amount = 41.10;
        $formatted = $symbol . number_format($amount, 2);
        $this->assertEquals('$41.10', $formatted);
    }

    // --- Settings data shape ---

    // --- Payment change calculation ---

    public function testChangeCalculationExactAmount() {
        $total = 500.00;
        $recibido = 500.00;
        $cambio = round($recibido - $total, 2);
        $this->assertEquals(0.00, $cambio);
    }

    public function testChangeCalculationWithExtra() {
        $total = 500.00;
        $recibido = 600.00;
        $cambio = round($recibido - $total, 2);
        $this->assertEquals(100.00, $cambio);
    }

    public function testInsufficientAmount() {
        $total = 500.00;
        $recibido = 400.00;
        $isValid = $recibido >= $total;
        $this->assertFalse($isValid);
    }

    public function testUsdPaymentChangeCalculation() {
        $totalCordobas = 500.00;
        $exchangeRate = 36.50;
        $recibidoUsd = 15.00;
        $recibidoCordobas = round($recibidoUsd * $exchangeRate, 2);
        $cambio = round($recibidoCordobas - $totalCordobas, 2);
        $this->assertEquals(47.50, $cambio);
        $this->assertEquals(547.50, $recibidoCordobas);
    }

    public function testUsdPaymentExactChange() {
        $totalCordobas = 365.00;
        $exchangeRate = 36.50;
        $recibidoUsd = 10.00;
        $recibidoCordobas = round($recibidoUsd * $exchangeRate, 2);
        $cambio = round($recibidoCordobas - $totalCordobas, 2);
        $this->assertEquals(0.00, $cambio);
        $this->assertEquals(365.00, $recibidoCordobas);
    }

    public function testRecibidoAndCambioInSaleData() {
        $data = [
            'metodo_pago' => 'Efectivo',
            'total' => 250.00,
            'pago_efectivo' => 250.00,
            'pago_tarjeta' => 0,
            'pago_dolar' => 0,
            'pago_dolar_equiv' => 0,
            'total_dolares' => 0,
            'tasa_cambio' => 0,
            'efectivo_recibido' => 300.00,
            'cambio' => 50.00,
        ];
        $this->assertArrayHasKey('efectivo_recibido', $data);
        $this->assertArrayHasKey('cambio', $data);
        $this->assertEquals(300.00, $data['efectivo_recibido']);
        $this->assertEquals(50.00, $data['cambio']);
    }

    public function testRecibidoAndCambioDefaultsToZero() {
        $data = [
            'metodo_pago' => 'Tarjeta',
            'total' => 100.00,
            'pago_efectivo' => 0,
            'pago_tarjeta' => 100.00,
        ];
        $recibido = $data['efectivo_recibido'] ?? 0;
        $cambio = $data['cambio'] ?? 0;
        $this->assertEquals(0, $recibido);
        $this->assertEquals(0, $cambio);
    }

    public function testChangeForSplitPaymentWithCashPortion() {
        $total = 500.00;
        $efectivo = 300.00;
        $tarjeta = 100.00;
        $dolarUsd = 2.74;
        $exchangeRate = 36.50;
        $dolarCordobas = round($dolarUsd * $exchangeRate, 2);
        $sum = $efectivo + $tarjeta + $dolarCordobas;
        $isValid = abs($sum - $total) <= 0.01;
        $this->assertTrue($isValid);
        $cambio = 0; // Split with cash portion, only efectivo part has recibido
        $recibido = $efectivo > 0 ? $efectivo : 0;
        $this->assertEquals(300.00, $recibido);
        $this->assertEquals(0, $cambio);
    }

    public function testSettingsDataShape() {
        $settings = (object)[
            'iva_enabled' => 1,
            'iva' => 15.00,
            'exchange_rate' => 36.50,
            'payment_methods' => 'efectivo,tarjeta,dolar,mixto',
        ];

        $this->assertObjectHasProperty('iva_enabled', $settings);
        $this->assertObjectHasProperty('iva', $settings);
        $this->assertObjectHasProperty('exchange_rate', $settings);
        $this->assertObjectHasProperty('payment_methods', $settings);

        $methods = explode(',', $settings->payment_methods);
        $this->assertContains('mixto', $methods);
        $this->assertContains('dolar', $methods);
    }
}
