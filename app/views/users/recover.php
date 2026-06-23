<?php require APPROOT . '/views/inc/header_login.php'; ?>
<?php flash('register_success'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="card card-body bg-light mt-5">
            <?php if (empty($data['success'])): ?>
                <h2 class="text-center mb-4">Recuperar Contraseña</h2>
                <p class="text-center text-muted">Ingrese su correo electrónico para recibir un enlace de restablecimiento</p>
                
                <form action="<?= URLROOT ?>/users/recover" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                        <input type="email" class="form-control <?= !empty($data['email_err']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= h($data['email']) ?>" required>
                        <div class="invalid-feedback"><?= $data['email_err'] ?></div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Enviar Enlace</button>
                    </div>
                </form>
            <?php else: ?>
                <div class="text-center">
                    <div class="mb-3 text-success">
                        <i class="fa fa-check-circle fa-4x"></i>
                    </div>
                    <h4>¡Enlace enviado!</h4>
                    <p class="text-muted">Se ha enviado un enlace de restablecimiento a <strong><?= h($data['email']) ?></strong></p>
                    
                    <?php if (!empty($data['reset_link'])): ?>
                        <div class="alert alert-info mt-3">
                            <small><strong>Modo Desarrollo:</strong> Enlace directo: <br>
                            <a href="<?= h($data['reset_link']) ?>" target="_blank"><?= h($data['reset_link']) ?></a></small>
                        </div>
                    <?php endif; ?>
                    
                    <p class="text-muted small">Revise su bandeja de entrada (y spam). El enlace expira en 1 hora.</p>
                    <a href="<?= URLROOT ?>/users/login" class="btn btn-secondary mt-3">Volver al Login</a>
                </div>
            <?php endif; ?>
            
            <div class="text-center mt-3">
                <a href="<?= URLROOT ?>/users/login" class="text-decoration-none">← Volver al Login</a>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer_login.php'; ?>