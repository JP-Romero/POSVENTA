<?php $currentPage = 'products'; require APPROOT . '/views/inc/header.php'; ?>
<div class="row mb-3">
    <div class="col-md-4">
        <h1>Productos</h1>
    </div>
    <div class="col-md-8 text-end">
        <form action="<?= URLROOT ?>/products/printBarcodeBatch" method="POST" id="batchPrintForm" class="d-inline">
            <button type="submit" class="btn btn-info" id="btnBatchPrint" disabled>
                <i class="fa fa-print me-1"></i> Imprimir Etiquetas Seleccionadas
            </button>
            <a href="<?= URLROOT ?>/products/import" class="btn btn-outline-primary">
                <i class="fa fa-upload me-1"></i> Importar
            </a>
            <a href="<?= URLROOT ?>/products/export" class="btn btn-success">
                <i class="fa fa-file-excel"></i> Exportar CSV
            </a>
            <a href="<?= URLROOT ?>/products/add" class="btn btn-primary">
                <i class="fa fa-plus"></i> Nuevo Producto
            </a>
        </form>
    </div>
</div>
<?php flash('product_message'); ?>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table id="products-table" class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 40px;">
                        <input type="checkbox" id="selectAllProducts" class="form-check-input">
                    </th>
                    <th>Imagen</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Precio Venta</th>
                    <th>Stock</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['products'] as $product) : ?>
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" name="product_ids[]" value="<?= $product->id ?>" class="form-check-input product-checkbox" data-id="<?= $product->id ?>">
                        </td>
                        <td class="text-center">
                            <?php if($product->imagen) : ?>
                                <img src="<?= URLROOT ?>/img/products/<?= $product->imagen ?>" width="50" height="50" class="img-thumbnail">
                            <?php else : ?>
                                <span class="badge bg-secondary">Sin imagen</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $product->codigo_interno ?></td>
                        <td><?= $product->nombre ?></td>
                        <td><?= $product->categoria_nombre ?></td>
                        <td><?= fmt($product->precio_venta) ?></td>
                        <td>
                            <?php if($product->stock <= $product->stock_minimo) : ?>
                                <span class="badge bg-danger"><?= $product->stock ?></span>
                            <?php else : ?>
                                <span class="badge bg-success"><?= $product->stock ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($product->estado) : ?>
                                <span class="badge bg-primary">Activo</span>
                            <?php else : ?>
                                <span class="badge bg-warning text-dark">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= URLROOT ?>/products/edit/<?= $product->id ?>" class="btn btn-outline-warning" title="Editar">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a href="<?= URLROOT ?>/products/printBarcode/<?= $product->id ?>" class="btn btn-outline-info" title="Imprimir Etiqueta" target="_blank">
                                    <i class="fa fa-barcode"></i>
                                </a>
                                <?php if(isAdmin()) : ?>
                                <button type="button" class="btn btn-outline-danger delete-product" data-id="<?= $product->id ?>" data-name="<?= h($product->nombre) ?>" title="Eliminar">
                                    <i class="fa fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function () {
    const table = $('#products-table').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
        }
    });
    
    // Select all checkbox
    $('#selectAllProducts').on('change', function() {
        $('.product-checkbox').prop('checked', this.checked);
        updateBatchButton();
    });
    
    // Individual checkboxes
    $(document).on('change', '.product-checkbox', function() {
        updateBatchButton();
        // Update select all state
        const total = $('.product-checkbox').length;
        const checked = $('.product-checkbox:checked').length;
        $('#selectAllProducts').prop('checked', total === checked);
        $('#selectAllProducts').prop('indeterminate', checked > 0 && checked < total);
    });
    
    function updateBatchButton() {
        const count = $('.product-checkbox:checked').length;
        $('#btnBatchPrint').prop('disabled', count === 0)
            .html('<i class="fa fa-print me-1"></i> Imprimir Etiquetas (' + count + ')');
    }
    
    // Delete product confirmation
    $(document).on('click', '.delete-product', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        showConfirm('¿Eliminar el producto "' + name + '"?', function(result) {
            if (result) {
                $.post('<?= URLROOT ?>/products/delete/' + id, function(r) {
                    location.reload();
                });
            }
        });
    });
});
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>