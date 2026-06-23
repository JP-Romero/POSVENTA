<?php require APPROOT . '/views/inc/header.php'; ?>
  <h1>Configuración del Sistema</h1>
  <?php flash('settings_message'); ?>
  <div class="card card-body bg-light mt-3 shadow-sm">
    <form action="<?php echo URLROOT; ?>/settings" method="post">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nombre del Negocio:</label>
                <input type="text" name="nombre_negocio" class="form-control" value="<?php echo $data['settings']->nombre_negocio; ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">RUC / NIT:</label>
                <input type="text" name="ruc" class="form-control" value="<?php echo $data['settings']->ruc; ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Teléfono:</label>
                <input type="text" name="telefono" class="form-control" value="<?php echo $data['settings']->telefono; ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Correo Electrónico:</label>
                <input type="email" name="correo" class="form-control" value="<?php echo $data['settings']->correo; ?>">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Dirección:</label>
            <textarea name="direccion" class="form-control"><?php echo $data['settings']->direccion; ?></textarea>
        </div>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">IVA (%):</label>
                <input type="number" step="0.01" name="iva" class="form-control" value="<?php echo $data['settings']->iva; ?>">
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-lg mt-3">Guardar Cambios</button>
    </form>
  </div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
