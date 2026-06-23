<?php require APPROOT . '/views/inc/header_login.php'; ?>
      <div class="login-header">
        <div class="login-icon">
          <i class="fas fa-key"></i>
        </div>
        <h3><?php echo SITENAME; ?></h3>
        <p>Recuperar Contraseña</p>
      </div>
      <div class="login-body">
        <?php flash('register_success'); ?>
        <?php if (empty($data['success'])): ?>
          <form action="<?= URLROOT ?>/users/recover" method="POST">
            <div class="mb-4">
              <label class="form-label">Correo Electrónico</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" class="form-control form-control-lg <?= !empty($data['email_err']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= h($data['email']) ?>" placeholder="Ingresa tu correo" required>
                <div class="invalid-feedback"><?= $data['email_err'] ?></div>
              </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-lg w-100">
              <i class="fas fa-paper-plane me-2"></i> Enviar Enlace
            </button>
          </form>
        <?php else: ?>
          <div class="text-center">
            <div class="mb-3 text-success">
              <i class="fa fa-check-circle fa-4x"></i>
            </div>
            <h4 class="mb-3">¡Enlace enviado!</h4>
            <p class="text-muted mb-3">Se ha enviado un enlace de restablecimiento a <strong><?= h($data['email']) ?></strong></p>
            
            <?php if (!empty($data['reset_link'])): ?>
              <div class="alert alert-info mt-3">
                <small><strong>Modo Desarrollo:</strong> Enlace directo: <br>
                <a href="<?= h($data['reset_link']) ?>" target="_blank"><?= h($data['reset_link']) ?></a></small>
              </div>
            <?php endif; ?>
            
            <p class="text-muted small mb-4">Revise su bandeja de entrada (y spam). El enlace expira en 1 hora.</p>
            <a href="<?= URLROOT ?>/users/login" class="btn btn-secondary w-100">Volver al Login</a>
          </div>
        <?php endif; ?>
      </div>
      <div class="login-footer">
        &copy; <?php echo date('Y'); ?> <?php echo SITENAME; ?>. Todos los derechos reservados.
      </div>
    </div>
  </div>
<?php require APPROOT . '/views/inc/footer_login.php'; ?>