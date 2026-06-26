<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura <?php echo $data['sale']->numero_factura; ?></title>
    <style>
        @page {
            margin: 0;
            padding: 0;
        }
        body { 
            font-family: 'Courier New', Courier, monospace; 
            padding: 10px; 
            margin: 0; 
            box-sizing: border-box; 
            background: #fff;
            width: 80mm; /* Ancho estándar de ticketera 80mm. Si es 58mm, el texto se ajustará si reducimos el font-size */
            max-width: 100%;
        }
        pre { 
            font-family: 'Courier New', Courier, monospace; 
            font-size: 12px; /* Letra pequeña para evitar saltos de línea indeseados en rollo */
            line-height: 1.2; 
            margin: 0; 
            white-space: pre-wrap; /* Permite que si algo es muy largo, haga salto en vez de cortarse */
            word-wrap: break-word;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body onload="window.print()">
<pre>
<div class="text-center">LIBRERÍA POS
Dirección: Calle Falsa 123
Teléfono: 123-4567

FACTURA: <?php echo $data['sale']->numero_factura; ?>
</div>
Cliente: <?php echo $data['sale']->cliente_nombre ?: 'Cliente General'; ?>

Fecha: <?php echo date('d/m/Y H:i', strtotime($data['sale']->fecha)); ?>

Vendedor: <?php echo $data['sale']->usuario_nombre; ?>


Descripción       Cant   Precio    Total
----------------------------------------
<?php foreach($data['details'] as $item) : ?>
<?php 
$desc = mb_substr($item->producto_nombre, 0, 17);
$desc = $desc . str_repeat(' ', max(0, 17 - mb_strlen($desc, 'UTF-8')));
$cant = str_pad($item->cantidad, 4, ' ', STR_PAD_LEFT);
$precio = str_pad('C$' . number_format($item->precio_venta, 2), 9, ' ', STR_PAD_LEFT);
$total = str_pad('C$' . number_format($item->cantidad * $item->precio_venta, 2), 9, ' ', STR_PAD_LEFT);
echo "{$desc} {$cant} {$precio} {$total}\n";
?>
<?php endforeach; ?>
----------------------------------------
Subtotal: C$<?php echo number_format($data['sale']->subtotal, 2); ?>

<?php if ($data['sale']->impuesto > 0): ?>
IVA:      C$<?php echo number_format($data['sale']->impuesto, 2); ?>

<?php endif; ?>
TOTAL:    C$<?php echo number_format($data['sale']->total, 2); ?>


Forma de Pago:
<?php
$s = $data['sale'];
$efectivo_aplicado = $s->pago_efectivo ?? 0;
$tarjeta = $s->pago_tarjeta ?? 0;
$dolar = $s->pago_dolar ?? 0;
$recibido = $s->efectivo_recibido ?? 0;
$cambio = $s->cambio ?? 0;

if ($recibido > 0) echo "- Billete entregado: C$" . number_format($recibido, 2) . "\n";
if ($efectivo_aplicado > 0) echo "- Efectivo aplicado: C$" . number_format($efectivo_aplicado, 2) . "\n";
if ($cambio > 0) echo "- Vuelto: C$" . number_format($cambio, 2) . "\n";
if ($tarjeta > 0) echo "- Tarjeta: C$" . number_format($tarjeta, 2) . "\n";
if ($dolar > 0) echo "- Dólar: C$" . number_format($dolar, 2) . " (" . number_format($s->total_dolares ?? 0, 2) . " USD)\n";
if ($recibido == 0 && $efectivo_aplicado == 0 && $tarjeta == 0 && $dolar == 0) {
    echo "- " . htmlspecialchars($s->metodo_pago) . "\n";
}
?>

<?php if ($recibido > 0 || $efectivo_aplicado > 0): ?>
Recibido en efec.: C$<?= number_format($recibido, 2) ?>

Aplicado en efec.: C$<?= number_format($efectivo_aplicado, 2) ?>

<?php endif; ?>
<?php if ($cambio > 0): ?>
Cambio entregado:  C$<?= number_format($cambio, 2) ?>

<?php endif; ?>
<?php if ($tarjeta > 0): ?>
Cubierto c/tarjeta: C$<?= number_format($tarjeta, 2) ?>

<?php endif; ?>

<?php if (($s->tasa_cambio ?? 0) > 0): ?>
Equivalente USD: &approx; $<?= number_format($s->total / $s->tasa_cambio, 2) ?>

(TC: <?= number_format($s->tasa_cambio, 2) ?>)
<?php endif; ?>

<div class="text-center">¡Gracias por su compra!</div>
</pre>
</body>
</html>
