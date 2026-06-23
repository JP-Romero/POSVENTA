<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura <?php echo $data['sale']->numero_factura; ?></title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .details { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .details th, .details td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .totals { float: right; width: 200px; }
        .totals div { display: flex; justify-content: space-between; padding: 5px 0; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">Imprimir</button>
        <button onclick="window.close()">Cerrar</button>
        <hr>
    </div>

    <div class="header">
        <h1>LIBRERÍA POS</h1>
        <p>Dirección: Calle Falsa 123</p>
        <p>Teléfono: 123-4567</p>
        <h2>FACTURA: <?php echo $data['sale']->numero_factura; ?></h2>
    </div>

    <div style="margin-bottom: 20px;">
        <strong>Cliente:</strong> <?php echo $data['sale']->cliente_nombre; ?><br>
        <strong>Dirección:</strong> <?php echo $data['sale']->cliente_direccion; ?><br>
        <strong>Fecha:</strong> <?php echo $data['sale']->fecha; ?><br>
        <strong>Vendedor:</strong> <?php echo $data['sale']->usuario_nombre; ?>
    </div>

    <table class="details">
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Cant.</th>
                <th>Precio</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data['details'] as $item) : ?>
                <tr>
                    <td><?php echo $item->producto_nombre; ?></td>
                    <td><?php echo $item->cantidad; ?></td>
                    <td>$<?php echo number_format($item->precio_venta, 2); ?></td>
                    <td>$<?php echo number_format($item->cantidad * $item->precio_venta, 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="totals">
        <div><span>Subtotal:</span> <span>$<?php echo number_format($data['sale']->subtotal, 2); ?></span></div>
        <div><span>IVA:</span> <span>$<?php echo number_format($data['sale']->impuesto, 2); ?></span></div>
        <div style="font-weight: bold; font-size: 1.2em; border-top: 2px solid #000; margin-top: 5px; padding-top: 10px;">
            <span>TOTAL:</span> <span>$<?php echo number_format($data['sale']->total, 2); ?></span>
        </div>
    </div>
</body>
</html>
