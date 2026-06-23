<?php $currentPage = 'clients'; require APPROOT . '/views/inc/header.php'; ?>

<div class="row mb-3">
    <div class="col-md-6">
        <h1>Historial de Compras: <?= h($data['client']->nombre) ?></h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?= URLROOT ?>/clients" class="btn btn-secondary"><i class="fa fa-backward"></i> Volver</a>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">Información del Cliente</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6"><strong>Teléfono:</strong> <?= h($data['client']->telefono ?? '—') ?></div>
            <div class="col-md-6"><strong>Correo:</strong> <?= h($data['client']->correo ?? '—') ?></div>
            <div class="col-md-6"><strong>Dirección:</strong> <?= h($data['client']->direccion ?? '—') ?></div>
            <div class="col-md-6"><strong>Registrado:</strong> <?= date('d/m/Y', strtotime($data['client']->fecha_registro)) ?></div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Compras Realizadas (<?= count($data['sales']) ?>)</h5>
    </div>
    <div class="card-body p-0">
        <?php if (empty($data['sales'])): ?>
            <div class="text-center py-5 text-muted">
                <i class="fa fa-receipt fa-3x mb-3"></i>
                <p>No hay compras registradas para este cliente</p>
            </div>
        <?php else: ?>
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Factura</th>
                        <th>Vendedor</th>
                        <th class="text-end">Total</th>
                        <th style="width: 80px;">Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data['sales'] as $sale): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($sale->fecha)) ?></td>
                        <td><?= $sale->numero_factura ?></td>
                        <td><?= h($sale->usuario_nombre) ?></td>
                        <td class="text-end fw-bold"><?= fmt($sale->total) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-info" onclick="showSaleDetails(<?= $sale->id ?>)">
                                <i class="fa fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Detalle Venta -->
<div class="modal fade" id="saleDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de Venta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="saleDetailContent">
                <div class="text-center py-4"><div class="spinner-border"></div></div>
            </div>
        </div>
    </div>
</div>

<script>
function showSaleDetails(saleId) {
    const modal = new bootstrap.Modal(document.getElementById('saleDetailModal'));
    const content = document.getElementById('saleDetailContent');
    content.innerHTML = '<div class="text-center py-4"><div class="spinner-border"></div></div>';
    modal.show();
    
    fetch('<?= URLROOT ?>/clients/apiSaleDetails/' + saleId)
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                let html = `
                    <div class="row mb-3">
                        <div class="col-6"><strong>Fecha:</strong> ${res.data.fecha}</div>
                        <div class="col-6"><strong>Factura:</strong> ${res.data.numero_factura}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6"><strong>Método Pago:</strong> ${res.data.metodo_pago}</div>
                        <div class="col-6"><strong>Vendedor:</strong> ${res.data.usuario_nombre}</div>
                    </div>
                    <table class="table table-sm">
                        <thead><tr><th>Producto</th><th class="text-center">Cant.</th><th class="text-end">P. Venta</th><th class="text-end">Subtotal</th></tr></thead>
                        <tbody>`;
                res.items.forEach(item => {
                    const subtotal = item.cantidad * item.precio_venta - (item.descuento || 0);
                    html += `<tr>
                        <td>${item.producto_nombre}</td>
                        <td class="text-center">${item.cantidad}</td>
                        <td class="text-end">${fmtMon(item.precio_venta)}</td>
                        <td class="text-end fw-bold">${fmtMon(subtotal)}</td>
                    </tr>`;
                });
                html += `</tbody></table>
                <div class="text-end fw-bold fs-5">Total: ${fmtMon(res.data.total)}</div>`;
                content.innerHTML = html;
            } else {
                content.innerHTML = '<div class="alert alert-danger">Error al cargar detalles</div>';
            }
        })
        .catch(() => content.innerHTML = '<div class="alert alert-danger">Error de conexión</div>');
}

function fmtMon(n) {
    const sym = '<?= getConfig("moneda_simbolo", "C$") ?>';
    n = parseFloat(n);
    if (n == 0) return sym + ' —';
    return sym + ' ' + n.toLocaleString('es-NI', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>