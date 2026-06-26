<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 15mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 24px; color: #2c3e50; }
        .header .subtitle { font-size: 14px; color: #666; margin-top: 5px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
        .info-box { background: #f8f9fa; padding: 10px; border-radius: 4px; border: 1px solid #dee2e6; }
        .info-box label { font-weight: bold; font-size: 10px; text-transform: uppercase; color: #666; display: block; margin-bottom: 3px; }
        .info-box span { font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 10px; }
        th, td { border: 1px solid #dee2e6; padding: 8px; text-align: left; }
        th { background: #2c3e50; color: white; font-weight: 600; text-align: center; }
        td.text-center { text-align: center; }
        td.text-right { text-align: right; }
        .totals { float: right; width: 300px; }
        .total-row { display: flex; justify-content: space-between; padding: 5px 10px; border: 1px solid #dee2e6; margin-bottom: -1px; }
        .total-row.total { background: #2c3e50; color: white; font-weight: bold; font-size: 14px; border: 2px solid #2c3e50; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; border-top: 1px solid #dee2e6; padding-top: 15px; }
        .qr-code { float: right; width: 80px; height: 80px; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= h($data['settings']['nombre_negocio'] ?? 'POSVENTA') ?></h1>
        <div class="subtitle">
            <?= h($data['settings']['direccion'] ?? '') ?><br>
            Tel: <?= h($data['settings']['telefono'] ?? '') ?> | RUC: <?= h($data['settings']['ruc'] ?? '') ?>
        </div>
    </div>
    
    <div class="info-grid">
        <div class="info-box">
            <label>Factura</label>
            <span><?= $sale->numero_factura ?></span>
        </div>
        <div class="info-box">
            <label>Fecha</label>
            <span><?= date('d/m/Y H:i', strtotime($sale->fecha)) ?></span>
        </div>
        <div class="info-box">
            <label>Cliente</label>
            <span><?= h($sale->cliente_nombre) ?></span>
        </div>
        <div class="info-box">
            <label>Vendedor</label>
            <span><?= h($sale->usuario_nombre) ?></span>
        </div>
        <div class="info-box">
            <label>Dirección</label>
            <span><?= h($sale->cliente_direccion ?? '—') ?></span>
        </div>
        <div class="info-box">
            <label>Teléfono</label>
            <span><?= h($sale->cliente_telefono ?? '—') ?></span>
        </div>
        <div class="info-box">
            <label>Método Pago</label>
            <span>
                <?php
                $parts = [];
                if (($sale->pago_efectivo ?? 0) > 0) $parts[] = 'Efectivo $' . fmt($sale->pago_efectivo);
                if (($sale->pago_tarjeta ?? 0) > 0) $parts[] = 'Tarjeta $' . fmt($sale->pago_tarjeta);
                if (($sale->pago_dolar ?? 0) > 0) $parts[] = 'Dólar $' . fmt($sale->pago_dolar) . ' (' . fmt($sale->total_dolares ?? 0) . ' USD)';
                echo implode(' + ', $parts) ?: h($sale->metodo_pago);
                ?>
            </span>
        </div>
        <div class="info-box">
            <label>Estado</label>
            <span><span class="badge badge-success">Pagado</span></span>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 40%">Producto</th>
                <th style="width: 10%" class="text-center">Cant.</th>
                <th style="width: 15%" class="text-right">P. Unit.</th>
                <th style="width: 10%" class="text-center">Desc.</th>
                <th style="width: 20%" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; foreach($details as $item): ?>
            <tr>
                <td class="text-center"><?= $i++ ?></td>
                <td><?= h($item->producto_nombre) ?></td>
                <td class="text-center"><?= $item->cantidad ?></td>
                <td class="text-right"><?= fmt($item->precio_venta) ?></td>
                <td class="text-center"><?= fmt($item->descuento ?? 0) ?></td>
                <td class="text-right"><?= fmt(($item->cantidad * $item->precio_venta) - ($item->descuento ?? 0)) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="totals">
        <div class="total-row"><span>Subtotal</span><span><?= fmt($sale->subtotal) ?></span></div>
        <?php if ($sale->impuesto > 0): ?>
        <div class="total-row"><span>IVA (<?= $data['settings']['iva'] ?? 15 ?>%)</span><span><?= fmt($sale->impuesto) ?></span></div>
        <?php endif; ?>
        <?php if (($sale->descuento ?? 0) > 0): ?>
        <div class="total-row"><span>Descuento</span><span>-<?= fmt($sale->descuento) ?></span></div>
        <?php endif; ?>
        <div class="total-row total"><span>TOTAL</span><span><?= fmt($sale->total) ?></span></div>
        <?php if (($sale->efectivo_recibido ?? 0) > 0): ?>
        <div class="total-row"><span>Recibido</span><span><?= fmt($sale->efectivo_recibido) ?></span></div>
        <?php endif; ?>
        <?php if (($sale->cambio ?? 0) > 0): ?>
        <div class="total-row"><span>Cambio</span><span><?= fmt($sale->cambio) ?></span></div>
        <?php endif; ?>
    </div>
    
    <div class="footer">
        <p><strong>¡Gracias por su compra!</strong></p>
        <p><?= h($data['settings']['nombre_negocio'] ?? 'POSVENTA') ?> - <?= h($data['settings']['direccion'] ?? '') ?></p>
        <p>Documento generado automáticamente el <?= date('d/m/Y H:i') ?></p>
    </div>
</body>
</html>