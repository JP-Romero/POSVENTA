<?php $currentPage = 'users'; require APPROOT . '/views/inc/header.php'; ?>
<?php flash('user_message'); ?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-body bg-light mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Editar Usuario</h2>
                <a href="<?= URLROOT ?>/users" class="btn btn-secondary"><i class="fa fa-backward"></i> Volver</a>
            </div>
            
            <form action="<?= URLROOT ?>/users/edit/<?= $data['id'] ?>" method="POST">
                <?= csrfField() ?>
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= !empty($data['nombre_err']) ? 'is-invalid' : '' ?>" id="nombre" name="nombre" value="<?= h($data['nombre']) ?>" required>
                    <div class="invalid-feedback"><?= $data['nombre_err'] ?></div>
                </div>
                
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= !empty($data['usuario_err']) ? 'is-invalid' : '' ?>" id="usuario" name="usuario" value="<?= h($data['usuario']) ?>" required>
                    <div class="invalid-feedback"><?= $data['usuario_err'] ?></div>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Nueva Contraseña (dejar vacío para no cambiar)</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Mínimo 6 caracteres">
                    <div class="form-text">Deje en blanco para mantener la contraseña actual</div>
                </div>
                
                <div class="mb-3">
                    <label for="id_rol" class="form-label">Rol <span class="text-danger">*</span></label>
                    <select class="form-select" id="id_rol" name="id_rol" required>
                        <?php foreach($data['roles'] as $role): ?>
                            <option value="<?= $role->id ?>" <?= $data['id_rol'] == $role->id ? 'selected' : '' ?>>
                                <?= h($role->nombre) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-3 form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="estado" name="estado" <?= $data['estado'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="estado">Activo</label>
                </div>
                
                <?php if ($data['id_rol'] != 1 && !empty($data['modules'])): ?>
                <hr>
                <h5 class="mb-3">Permisos de Acceso</h5>
                <p class="text-muted small">Configure el acceso a cada módulo del sistema para este usuario.</p>
                
                <div class="row g-3">
                    <?php foreach($data['modules'] as $modulo => $nombre): ?>
                        <?php $acceso = $data['permissions'][$modulo] ?? 1; ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="perm_<?= $modulo ?>" name="perm_<?= $modulo ?>" <?= $acceso ? 'checked' : '' ?>>
                                <label class="form-check-label" for="perm_<?= $modulo ?>">
                                    <?= h($nombre) ?>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
                    <a href="<?= URLROOT ?>/users" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('id_rol').addEventListener('change', function() {
    const permSection = document.querySelector('.row.g-3');
    if (this.value == 1) {
        permSection?.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = true);
    }
});
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>