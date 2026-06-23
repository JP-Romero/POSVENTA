<?php $currentPage = 'users'; require APPROOT . '/views/inc/header.php'; ?>
  <div class="row">
    <div class="col-md-6 mx-auto">
      <div class="card card-body bg-light mt-5">
        <h2>Crear Usuario</h2>
        <form action="<?php echo URLROOT; ?>/users/add" method="post">
          <div class="mb-3">
            <label for="nombre" class="form-label">Nombre:</label>
            <input type="text" name="nombre" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="usuario" class="form-label">Usuario:</label>
            <input type="text" name="usuario" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Contraseña:</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="id_rol" class="form-label">Rol:</label>
            <select name="id_rol" class="form-select" required>
                <?php foreach($data['roles'] as $rol) : ?>
                    <option value="<?php echo $rol->id; ?>"><?php echo $rol->nombre; ?></option>
                <?php endforeach; ?>
            </select>
          </div>
          <input type="submit" value="Guardar" class="btn btn-success w-100">
        </form>
      </div>
    </div>
  </div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
