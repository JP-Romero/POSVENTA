<?php require APPROOT . '/views/inc/header_login.php'; ?>
      <div class="login-header">
        <div class="login-icon">
          <i class="fas fa-lock"></i>
        </div>
        <h3><?php echo SITENAME; ?></h3>
        <p>Nueva Contraseña</p>
      </div>
      <div class="login-body">
        <form action="<?= URLROOT ?>/users/reset/<?= h($data['token']) ?>" method="POST">
          <div class="mb-4">
            <label for="password" class="form-label">Nueva Contraseña</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-lock"></i></span>
              <input type="password" class="form-control form-control-lg <?= !empty($data['password_err']) ? 'is-invalid' : '' ?>" id="password" name="password" placeholder="Mínimo 6 caracteres" required minlength="6">
              <div class="invalid-feedback"><?= $data['password_err'] ?></div>
            </div>
          </div>
          
          <div class="mb-4">
            <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-lock"></i></span>
              <input type="password" class="form-control form-control-lg <?= !empty($data['confirm_err']) ? 'is-invalid' : '' ?>" id="confirm_password" name="confirm_password" placeholder="Repite la contraseña" required minlength="6">
              <div class="invalid-feedback"><?= $data['confirm_err'] ?></div>
            </div>
          </div>
          
          <button type="submit" class="btn btn-primary btn-lg w-100">
            <i class="fas fa-check me-2"></i> Restablecer Contraseña
          </button>
        </form>
        
        <div class="text-center mt-3">
          <a href="<?= URLROOT ?>/users/login" class="text-decoration-none">← Volver al Login</a>
        </div>
      </div>
      <div class="login-footer">
        &copy; <?php echo date('Y'); ?> <?php echo SITENAME; ?>. Todos los derechos reservados.
      </div>
    </div>
  </div>
<?php require APPROOT . '/views/inc/footer_login.php'; ?>