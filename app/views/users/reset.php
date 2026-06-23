<?php require APPROOT . '/views/inc/header.php'; ?>
<?php flash('register_success'); ?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2 class="text-center mb-4">Restablecer Contraseña</h2>
            <p class="text-center text-muted">Ingrese su nueva contraseña</p>
            
            <form action="<?= URLROOT ?>/users/reset/<?= h($data['token']) ?>" method="POST">
                <div class="mb-3">
                    <label for="password" class="form-label">Nueva Contraseña <span class="text-danger">*</span></label>
                    <input type="password" class="form-control <?= !empty($data['password_err']) ? 'is-invalid' : '' ?>" id="password" name="password" required minlength="6">
                    <div class="invalid-feedback"><?= $data['password_err'] ?></div>
                </div>
                
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                    <input type="password" class="form-control <?= !empty($data['confirm_err']) ? 'is-invalid' : '' ?>" id="confirm_password" name="confirm_password" required minlength="6">
                    <div class="invalid-feedback"><?= $data['confirm_err'] ?></div>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-success btn-lg">Restablecer Contraseña</button>
                </div>
            </form>
            
            <div class="text-center mt-3">
                <a href="<?= URLROOT ?>/users/login" class="text-decoration-none">← Volver al Login</a>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>