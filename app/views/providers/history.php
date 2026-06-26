<?php $currentPage = 'providers'; require APPROOT . '/views/inc/header.php'; ?>

<div class="row mb-3">
    <div class="col-md-6">
        <h1>Historial de Compras: <?= h($data['provider']->nombre) ?></h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?= URLROOT ?>/providers" class="btn btn-outline-secondary"><i class="fa fa-backward"></i> Volver</a>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">Información del Proveedor</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6"><strong>Contacto:</strong> <?= h($data['provider']->contacto ?? '—') ?></div>
            <div class="col-md-6"><strong>Teléfono:</strong> <?= h($data['provider']->telefono ?? '—') ?></div>
            <div class="col-md-6"><strong>Correo:</strong> <?= h($data['provider']->correo ?? '—') ?></div>
            <div class="col-md-6"><strong>Dirección:</strong> <?= h($data['provider']->direccion ?? '—') ?></div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Compras Realizadas (<?= count($data['purchases']) ?>)</h5>
    </div>
    <div class="card-body p-0">
        <?php if (empty($data['purchases'])): ?>
            <div class="text-center py-5 text-muted">
                <i class="fa fa-shopping-cart fa-3x mb-3"></i>
                <p>No hay compras registradas para este proveedor</p>
            </div>
        <?php else: ?>
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Comprobante</th>
                        <th>Usuario</th>
                        <th class="text-end">Total</th>
                        <th style="width: 80px;">Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data['purchases'] as $purchase): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($purchase->fecha)) ?></td>
                        <td><?= h($purchase->comprobante ?? '—') ?></td>
                        <td><?= h($purchase->usuario_nombre) ?></td>
                        <td class="text-end fw-bold"><?= fmt($purchase->total) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-info btn-purchase-detail" data-purchase-id="<?= $purchase->id ?>">
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

<!-- Modal Detalle Compra -->
<div class="modal fade" id="purchaseDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de Compra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="purchaseDetailContent">
                <div class="text-center py-4"><div class="spinner-border"></div></div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-purchase-detail');
    if (btn) showPurchaseDetails(btn.dataset.purchaseId);
});

function showPurchaseDetails(purchaseId) {
    const modal = new bootstrap.Modal(document.getElementById('purchaseDetailModal'));
    const content = document.getElementById('purchaseDetailContent');
    content.innerHTML = '<div class="text-center py-4"><div class="spinner-border"></div></div>';
    modal.show();
    
    fetch('<?= URLROOT ?>/providers/apiPurchaseDetails/' + purchaseId)
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                let html = `
                    <div class="row mb-3">
                        <div class="col-6"><strong>Fecha:</strong> ${res.data.fecha}</div>
                        <div class="col-6"><strong>Comprobante:</strong> ${res.data.comprobante || '—'}</div>
                    </div>
                    <table class="table table-sm">
                        <thead><tr><th>Producto</th><th class="text-center">Cant.</th><th class="text-end">P. Compra</th><th class="text-end">Subtotal</th></tr></thead>
                        <tbody>`;
                res.items.forEach(item => {
                    html += `<tr>
                        <td>${item.producto_nombre}</td>
                        <td class="text-center">${item.cantidad}</td>
                        <td class="text-end">${fmtMon(item.precio_compra)}</td>
                        <td class="text-end fw-bold">${fmtMon(item.cantidad * item.precio_compra)}</td>
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

// Helper para formatear moneda en JS
function fmtMon(n) {
    const sym = '<?= getConfig("moneda_simbolo", "C$") ?>';
    n = parseFloat(n);
    if (n == 0) return sym + ' —';
    return sym + ' ' + n.toLocaleString('es-NI', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>