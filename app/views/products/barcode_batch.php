<?php $currentPage = 'products'; require APPROOT . '/views/inc/header.php'; ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Impresión Lote de Etiquetas</h2>
            <div>
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="fa fa-print me-1"></i> Imprimir Todas
                </button>
                <a href="<?= URLROOT ?>/products" class="btn btn-outline-secondary">Volver</a>
            </div>
        </div>
        
        <div class="alert alert-info">
            <h6><i class="fa fa-info-circle me-1"></i> Instrucciones</h6>
            <ul class="mb-0 small">
                <li>Cada etiqueta ocupa una "página" para impresión en rollo térmico</li>
                <li>Use <kbd>Ctrl</kbd> + <kbd>P</kbd> para imprimir</li>
                <li>Configure "Más configuraciones" → "Márgenes: Ninguno" → "Escala: 100%"</li>
            </ul>
        </div>
        
        <div class="row g-3">
            <?php foreach($data['labels'] as $label): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card h-100 border-0 shadow-sm" style="page-break-inside: avoid;">
                    <div class="card-body text-center p-3" style="min-height: 280px;">
                        <img src="data:image/png;base64,<?= $label['label'] ?>" alt="Etiqueta" class="img-fluid mb-2" style="max-height: 200px;">
                        <small class="text-muted d-block">ID: <?= $label['id'] ?></small>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Hidden print version - solo etiquetas sin chrome -->
        <div id="printVersion" style="display: none;">
            <?php foreach($data['labels'] as $label): ?>
            <div class="label-page" style="page-break-after: always; text-align: center; padding: 10px;">
                <img src="data:image/png;base64,<?= $label['label'] ?>" alt="Etiqueta" style="max-width: 100%; height: auto;">
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
@media print {
    .card-header, .btn, .alert, .no-print { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
    .card-body { padding: 0 !important; min-height: auto !important; }
    .col-6, .col-md-4, .col-lg-3 { 
        page-break-inside: avoid;
        break-inside: avoid;
    }
    body { background: white !important; }
    #printVersion { display: block !important; }
    .page-content { padding: 0 !important; }
}
</style>

<script>
// Auto-focus print on load (optional)
// window.onload = () => window.print();
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>