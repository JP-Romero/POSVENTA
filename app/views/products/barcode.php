<?php $currentPage = 'products'; require APPROOT . '/views/inc/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Etiqueta: <?= h($data['product']->nombre) ?></h5>
                <div>
                    <button class="btn btn-sm btn-outline-primary" onclick="window.print()">
                        <i class="fa fa-print me-1"></i> Imprimir
                    </button>
                    <a href="<?= URLROOT ?>/products" class="btn btn-sm btn-secondary">Volver</a>
                </div>
            </div>
            <div class="card-body text-center p-4">
                <div class="mb-3" style="max-width: 300px; margin: 0 auto;">
                    <img src="data:image/png;base64,<?= $data['label_base64'] ?>" alt="Etiqueta" class="img-fluid border rounded">
                </div>
                <div class="mt-3">
                    <small class="text-muted">Escanee para verificar</small>
                </div>
            </div>
        </div>
        
        <div class="alert alert-info">
            <h6><i class="fa fa-info-circle me-1"></i> Instrucciones de Impresión</h6>
            <ul class="mb-0 small">
                <li>Use papel térmico adhesivo 58mm u 80mm</li>
                <li>Configure la impresora en "Tamaño real" (100%)</li>
                <li>Desactive "Ajustar a página" en opciones de impresión</li>
                <li>Para impresora térmica: use el botón "Imprimir" del navegador</li>
            </ul>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>