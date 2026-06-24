<?php require APPROOT . '/views/inc/header.php'; ?>
  <div class="row">
    <div class="col-md-6 mx-auto">
      <div class="card card-body bg-light mt-5">
        <h2>Editar Usuario</h2>
        <form action="<?php echo URLROOT; ?>/users/edit/<?php echo $data['user']->id; ?>" method="post">
          <div class="mb-3">
            <label for="nombre" class="form-label">Nombre:</label>
            <input type="text" name="nombre" class="form-control" value="<?php echo $data['user']->nombre; ?>" required>
          </div>
          <div class="mb-3">
            <label for="usuario" class="form-label">Usuario:</label>
            <input type="text" name="usuario" class="form-control" value="<?php echo $data['user']->usuario; ?>" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Nueva Contraseña (dejar en blanco para no cambiar):</label>
            <input type="password" name="password" class="form-control">
          </div>
          <div class="mb-3">
            <label for="id_rol" class="form-label">Rol:</label>
            <select name="id_rol" class="form-select" required>
                <?php foreach($data['roles'] as $rol) : ?>
                    <option value="<?php echo $rol->id; ?>" <?php echo ($data['user']->id_rol == $rol->id) ? 'selected' : ''; ?>><?php echo $rol->nombre; ?></option>
                <?php endforeach; ?>
            </select>
          </div>
          <div class="d-flex justify-content-between">
              <input type="submit" value="Actualizar" class="btn btn-success">
              <a href="<?php echo URLROOT; ?>/users" class="btn btn-secondary">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
