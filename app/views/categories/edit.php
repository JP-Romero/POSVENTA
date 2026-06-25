<?php $currentPage = 'categories'; require APPROOT . '/views/inc/header.php'; ?>
  <a href="<?php echo URLROOT; ?>/categories" class="btn btn-light mb-3"><i class="fa fa-backward"></i> Volver</a>
  <div class="card card-body bg-light mt-2">
    <h2>Editar Categoría</h2>
    <p>Modifique los datos de la categoría</p>
    <form action="<?php echo URLROOT; ?>/categories/edit/<?php echo $data['id']; ?>" method="post">
      <?= csrfField() ?>
      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre: <sup>*</sup></label>
        <input type="text" name="nombre" class="form-control form-control-lg <?php echo (!empty($data['nombre_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['nombre']; ?>">
        <span class="invalid-feedback"><?php echo $data['nombre_err']; ?></span>
      </div>
      <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción:</label>
        <textarea name="descripcion" class="form-control form-control-lg"><?php echo $data['descripcion']; ?></textarea>
      </div>
      <input type="submit" class="btn btn-success" value="Actualizar">
    </form>
  </div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
