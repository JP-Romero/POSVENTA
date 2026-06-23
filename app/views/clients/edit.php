<?php $currentPage = 'clients'; require APPROOT . '/views/inc/header.php'; ?>
  <a href="<?php echo URLROOT; ?>/clients" class="btn btn-light mb-3"><i class="fa fa-backward"></i> Volver</a>
  <div class="card card-body bg-light mt-2">
    <h2>Editar Cliente</h2>
    <form action="<?php echo URLROOT; ?>/clients/edit/<?php echo $data['id']; ?>" method="post">
      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre: <sup>*</sup></label>
        <input type="text" name="nombre" class="form-control <?php echo (!empty($data['nombre_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['nombre']; ?>">
        <span class="invalid-feedback"><?php echo $data['nombre_err']; ?></span>
      </div>
      <div class="mb-3">
        <label for="telefono" class="form-label">Teléfono:</label>
        <input type="text" name="telefono" class="form-control" value="<?php echo $data['telefono']; ?>">
      </div>
      <div class="mb-3">
        <label for="correo" class="form-label">Correo Electrónico:</label>
        <input type="email" name="correo" class="form-control" value="<?php echo $data['correo']; ?>">
      </div>
      <div class="mb-3">
        <label for="direccion" class="form-label">Dirección:</label>
        <textarea name="direccion" class="form-control"><?php echo $data['direccion']; ?></textarea>
      </div>
      <input type="submit" class="btn btn-success" value="Actualizar">
    </form>
  </div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
