<?php require APPROOT . '/views/inc/header_login.php'; ?>
      <div class="login-header">
        <div class="login-icon">
          <i class="fas fa-book"></i>
        </div>
        <h3><?php echo SITENAME; ?></h3>
        <p>Sistema de Ventas e Inventario</p>
      </div>
      <div class="login-body">
        <?php flash('register_success'); ?>
        <form action="<?php echo URLROOT; ?>/users/login" method="post">
          <div class="mb-3">
            <label class="form-label">Usuario</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-user"></i></span>
              <input type="text" name="usuario" class="form-control form-control-lg <?php echo (!empty($data['usuario_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['usuario']; ?>" placeholder="Ingresa tu usuario" required autofocus>
              <span class="invalid-feedback"><?php echo $data['usuario_err']; ?></span>
            </div>
          </div>
          <div class="mb-4">
            <label class="form-label">Contraseña</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-lock"></i></span>
              <input type="password" name="password" class="form-control form-control-lg <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['password']; ?>" placeholder="Ingresa tu contraseña" required id="password">
              <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                <i class="fas fa-eye" id="toggleIcon"></i>
              </button>
              <span class="invalid-feedback"><?php echo $data['password_err']; ?></span>
            </div>
          </div>
          <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="remember" name="remember">
              <label class="form-check-label" for="remember">Recordarme</label>
            </div>
            <a href="<?php echo URLROOT; ?>/users/recover" class="text-decoration-none" style="color: #667eea; font-size: 13px;">¿Olvidaste tu contraseña?</a>
          </div>
          <button type="submit" class="btn btn-primary btn-lg w-100">
            <i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión
          </button>
        </form>
      </div>
      <div class="login-footer">
        &copy; <?php echo date('Y'); ?> <?php echo SITENAME; ?>. Todos los derechos reservados.
      </div>
    </div>
  </div>
<?php require APPROOT . '/views/inc/footer_login.php'; ?>
