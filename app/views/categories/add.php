<?php $currentPage = 'categories'; require APPROOT . '/views/inc/header.php'; ?>
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Agregar Categoría</h5>
        <a href="<?php echo URLROOT; ?>/categories" class="btn btn-outline-secondary btn-sm"><i class="fa fa-arrow-left me-1"></i> Volver</a>
    </div>
    <div class="card-body">
        <p class="text-muted mb-4">Cree una nueva categoría para sus productos</p>
        <form action="<?php echo URLROOT; ?>/categories/add" method="post">
            <?= csrfField() ?>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre: <sup>*</sup></label>
                <input type="text" name="nombre" class="form-control <?php echo (!empty($data['nombre_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo h($data['nombre']); ?>">
                <span class="invalid-feedback"><?php echo h($data['nombre_err']); ?></span>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción:</label>
                <textarea name="descripcion" class="form-control"><?php echo h($data['descripcion']); ?></textarea>
            </div>
            <input type="submit" class="btn btn-success" value="Guardar">
        </form>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
