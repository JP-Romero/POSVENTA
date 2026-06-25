<?php $currentPage = 'products'; require APPROOT . '/views/inc/header.php'; ?>
<?php flash('product_message'); ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Importar Productos</h5>
                <a href="<?= URLROOT ?>/products" class="btn btn-sm btn-secondary">Volver</a>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fa fa-info-circle me-2"></i>Formato de Archivo</h6>
                    <ul class="mb-0 small">
                        <li>Formatos soportados: <strong>CSV, XLSX, XLS</strong></li>
                        <li>La primera fila debe contener los encabezados</li>
                        <li>Columnas reconocidas (orden no importa):</li>
                        <ul class="mb-0">
                            <li><code>codigo_interno</code> - Código interno del producto</li>
                            <li><code>codigo_barras</code> - Código de barras (EAN13, UPC, etc.)</li>
                            <li><code>nombre</code> - Nombre del producto <strong>(requerido)</strong></li>
                            <li><code>descripcion</code> - Descripción</li>
                            <li><code>categoria</code> - Nombre de la categoría (debe existir)</li>
                            <li><code>proveedor</code> - Nombre del proveedor (debe existir)</li>
                            <li><code>precio_compra</code> - Precio de compra</li>
                            <li><code>precio_venta</code> - Precio de venta</li>
                            <li><code>stock</code> - Stock inicial</li>
                            <li><code>stock_minimo</code> - Stock mínimo</li>
                        </ul>
                    </ul>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <a href="<?= URLROOT ?>/products/importTemplate" class="btn btn-outline-primary w-100">
                            <i class="fa fa-download me-1"></i> Descargar Plantilla CSV
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="<?= URLROOT ?>/products/export" class="btn btn-outline-success w-100">
                            <i class="fa fa-file-excel me-1"></i> Exportar Actual (CSV)
                        </a>
                    </div>
                </div>
                
                <form action="<?= URLROOT ?>/products/import" method="POST" enctype="multipart/form-data">
                    <?= csrfField() ?>
                    <div class="mb-3">
                        <label for="import_file" class="form-label">Archivo a Importar <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="import_file" name="import_file" accept=".csv,.xlsx,.xls" required>
                        <div class="form-text">Máximo 2MB. Se omitirán filas con nombre vacío.</div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-upload me-1"></i> Importar Productos
                        </button>
                        <a href="<?= URLROOT ?>/products" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>