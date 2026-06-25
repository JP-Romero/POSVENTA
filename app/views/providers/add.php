<?php $currentPage = 'providers'; require APPROOT . '/views/inc/header.php'; ?>
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Agregar Proveedor</h5>
        <a href="<?php echo URLROOT; ?>/providers" class="btn btn-outline-secondary btn-sm"><i class="fa fa-arrow-left me-1"></i> Volver</a>
    </div>
    <div class="card-body">
        <form action="<?php echo URLROOT; ?>/providers/add" method="post">
            <?= csrfField() ?>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre: <sup>*</sup></label>
                <input type="text" name="nombre" class="form-control <?php echo (!empty($data['nombre_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo h($data['nombre']); ?>">
                <span class="invalid-feedback"><?php echo h($data['nombre_err']); ?></span>
            </div>
            <div class="mb-3">
                <label for="contacto" class="form-label">Persona de Contacto:</label>
                <input type="text" name="contacto" class="form-control" value="<?php echo h($data['contacto']); ?>">
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono:</label>
                <input type="text" name="telefono" class="form-control" value="<?php echo h($data['telefono']); ?>">
            </div>
            <div class="mb-3">
                <label for="correo" class="form-label">Correo Electrónico:</label>
                <input type="email" name="correo" class="form-control" value="<?php echo h($data['correo']); ?>">
            </div>
            <div class="mb-3">
                <label for="direccion" class="form-label">Dirección:</label>
                <textarea name="direccion" class="form-control"><?php echo h($data['direccion']); ?></textarea>
            </div>
            <input type="submit" class="btn btn-success" value="Guardar">
        </form>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
