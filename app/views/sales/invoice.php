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
<body>
<pre>
<?php
$s = $data['sale'];
$details = $data['details'];
$subtitle = $s->numero_factura;
$client = $s->cliente_nombre ?: 'Cliente General';
$date = date('d/m/Y H:i', strtotime($s->fecha));
$vendor = $s->usuario_nombre;
$subtotal = $s->subtotal;
$impuesto = $s->impuesto ?? 0;
$descuento = max(0, ($subtotal + $impuesto) - $s->total);
$total = $s->total;
$recibido = $s->efectivo_recibido ?? 0;
$cambio = $recibido - $total;
$metodo = $s->metodo_pago ?? '';
$itemCount = count($details);

function padCenter($str, $width) {
    $len = mb_strlen($str, 'UTF-8');
    if ($len >= $width) return $str;
    $pad = $width - $len;
    $left = floor($pad/2);
    return str_repeat(' ', $left) . $str . str_repeat(' ', $pad - $left);
}
?>
LIBRERÍA POS
Dirección: Calle Falsa 123
Teléfono: 123-4567

FACTURA: <?= htmlspecialchars($subtitle) ?>
Cliente: <?= htmlspecialchars($client) ?>
Fecha: <?= htmlspecialchars($date) ?>
Vendedor: <?= htmlspecialchars($vendor) ?>

<?php echo str_pad('Descripción', 20) . ' ' . str_pad('Cant', 4, ' ', STR_PAD_LEFT) . ' ' . str_pad('Precio', 9, ' ', STR_PAD_LEFT) . ' ' . str_pad('Total', 9, ' ', STR_PAD_LEFT); ?>
<?php echo str_repeat('-', 45); ?>
<?php foreach($details as $item): ?>
<?php
$desc = mb_substr($item->producto_nombre, 0, 20);
$desc = $desc . str_repeat(' ', max(0, 20 - mb_strlen($desc, 'UTF-8')));
$cant = str_pad($item->cantidad, 4, ' ', STR_PAD_LEFT);
$precio = str_pad('C$' . number_format($item->precio_venta, 2), 9, ' ', STR_PAD_LEFT);
$tot = str_pad('C$' . number_format($item->cantidad * $item->precio_venta, 2), 9, ' ', STR_PAD_LEFT);
echo "{$desc} {$cant} {$precio} {$tot}\n";
?>
<?php endforeach; ?>
<?php echo str_repeat('-', 45); ?>
Subtotal: C$<?= number_format($subtotal, 2) ?>
Descuento: C$<?= number_format($descuento, 2) ?>
Impuesto: C$<?= number_format($impuesto, 2) ?>
<?php echo str_repeat('-', 45); ?>
TOTAL:<?= str_repeat(' ', max(0, 45 - strlen('TOTAL:') - strlen('C$' . number_format($total,2)))) ?>C$<?= number_format($total, 2) ?>
<?php echo str_repeat('-', 45); ?>
Método Pago: <?= htmlspecialchars($metodo) ?>
Recibido: C$<?= number_format($recibido, 2) ?>
Cambio: C$<?= number_format($cambio, 2) ?>
<?php echo str_repeat('-', 45); ?>
Artículos vendidos: <?= $itemCount ?>


<?php
$lines = [
    '¡Gracias por su compra!',
    'Conserve este comprobante',
    '',
    'www.libreriapos.com'
];
foreach($lines as $line) {
    if ($line === '') {
        echo "\n";
        continue;
    }
    echo padCenter($line, 45) . "\n";
}
?>
<?php echo str_repeat('=', 45); ?>
</pre>
</body>
</html>
