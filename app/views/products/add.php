<?php $currentPage = 'products'; require APPROOT . '/views/inc/header.php'; ?>
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Agregar Producto</h5>
        <a href="<?php echo URLROOT; ?>/products" class="btn btn-outline-secondary btn-sm"><i class="fa fa-arrow-left me-1"></i> Volver</a>
    </div>
    <div class="card-body">
        <p class="text-muted mb-4">Complete los datos para registrar un nuevo producto</p>
        <form action="<?php echo URLROOT; ?>/products/add" method="post" enctype="multipart/form-data">
            <?= csrfField() ?>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="codigo_interno" class="form-label">Código Interno:</label>
                    <input type="text" name="codigo_interno" class="form-control" value="<?php echo h($data['codigo_interno']); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="codigo_barras" class="form-label">Código de Barras:</label>
                    <input type="text" name="codigo_barras" class="form-control" value="<?php echo h($data['codigo_barras']); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="nombre" class="form-label">Nombre: <sup>*</sup></label>
                    <input type="text" name="nombre" class="form-control <?php echo (!empty($data['nombre_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo h($data['nombre']); ?>">
                    <span class="invalid-feedback"><?php echo h($data['nombre_err']); ?></span>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="id_categoria" class="form-label">Categoría: <sup>*</sup></label>
                    <select name="id_categoria" class="form-select <?php echo (!empty($data['categoria_err'])) ? 'is-invalid' : ''; ?>">
                        <option value="">Seleccione...</option>
                        <?php foreach($data['categories'] as $cat) : ?>
                            <option value="<?php echo $cat->id; ?>" <?php echo ($data['id_categoria'] == $cat->id) ? 'selected' : ''; ?>><?php echo h($cat->nombre); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="invalid-feedback"><?php echo h($data['categoria_err']); ?></span>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="id_proveedor" class="form-label">Proveedor: <sup>*</sup></label>
                    <select name="id_proveedor" class="form-select <?php echo (!empty($data['proveedor_err'])) ? 'is-invalid' : ''; ?>">
                        <option value="">Seleccione...</option>
                        <?php foreach($data['providers'] as $prov) : ?>
                            <option value="<?php echo $prov->id; ?>" <?php echo ($data['id_proveedor'] == $prov->id) ? 'selected' : ''; ?>><?php echo h($prov->nombre); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="invalid-feedback"><?php echo h($data['proveedor_err']); ?></span>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="precio_compra" class="form-label">Precio Compra: <sup>*</sup></label>
                    <input type="number" step="0.01" name="precio_compra" class="form-control <?php echo (!empty($data['precio_compra_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo h($data['precio_compra']); ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="precio_venta" class="form-label">Precio Venta: <sup>*</sup></label>
                    <input type="number" step="0.01" name="precio_venta" class="form-control <?php echo (!empty($data['precio_venta_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo h($data['precio_venta']); ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="stock" class="form-label">Stock Inicial:</label>
                    <input type="number" name="stock" class="form-control" value="<?php echo h($data['stock']); ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="stock_minimo" class="form-label">Stock Mínimo:</label>
                    <input type="number" name="stock_minimo" class="form-control" value="<?php echo h($data['stock_minimo']); ?>">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="descripcion" class="form-label">Descripción:</label>
                    <textarea name="descripcion" class="form-control"><?php echo h($data['descripcion']); ?></textarea>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="imagen" class="form-label">Imagen:</label>
                    <input type="file" name="imagen" class="form-control" accept="image/*">
                </div>
                <div class="col-md-6 pt-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="estado" id="estado" checked>
                        <label class="form-check-label" for="estado">Producto Activo</label>
                    </div>
                </div>
            </div>

            <input type="submit" class="btn btn-success" value="Guardar Producto">
        </form>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
