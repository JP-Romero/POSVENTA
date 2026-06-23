<?php require APPROOT . '/views/inc/header_login.php'; ?>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-body bg-light mt-5">
        <?php flash('register_success'); ?>
        <h2 class="text-center">Iniciar Sesión</h2>
        <p class="text-center">Por favor ingrese sus credenciales</p>
        <form action="<?php echo URLROOT; ?>/users/login" method="post">
          <div class="mb-3">
            <label for="usuario" class="form-label">Usuario: <sup>*</sup></label>
            <input type="text" name="usuario" class="form-control form-control-lg <?php echo (!empty($data['usuario_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['usuario']; ?>">
            <span class="invalid-feedback"><?php echo $data['usuario_err']; ?></span>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Contraseña: <sup>*</sup></label>
            <input type="password" name="password" class="form-control form-control-lg <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['password']; ?>">
            <span class="invalid-feedback"><?php echo $data['password_err']; ?></span>
          </div>
          <div class="row mb-3">
            <div class="col">
              <input type="submit" value="Entrar" class="btn btn-success w-100 btn-lg">
            </div>
          </div>
          <div class="row">
            <div class="col text-center">
              <a href="<?php echo URLROOT; ?>/users/recover" class="text-decoration-none">¿Olvidó su contraseña?</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php require APPROOT . '/views/inc/footer_login.php'; ?>
