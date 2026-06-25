<?php $currentPage = 'users'; require APPROOT . '/views/inc/header.php'; ?>
<?php flash('user_message'); ?>

<div class="row mb-3">
    <div class="col-md-6">
        <h1>Usuarios</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?= URLROOT ?>/users/add" class="btn btn-primary">
            <i class="fa fa-plus"></i> Nuevo Usuario
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table id="users-table" class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th style="width: 120px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['users'] as $user) : ?>
                    <tr>
                        <td><?= h($user->nombre) ?></td>
                        <td><?= h($user->usuario) ?></td>
                        <td>
                            <span class="badge bg-<?= $user->id_rol == 1 ? 'primary' : 'info' ?>">
                                <?= h($user->rol_nombre) ?>
                            </span>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-user-status" type="checkbox" 
                                    data-user-id="<?= $user->id ?>" 
                                    <?= $user->estado ? 'checked' : '' ?>>
                                <label class="form-check-label ms-1">
                                    <?= $user->estado ? 'Activo' : 'Inactivo' ?>
                                </label>
                            </div>
                        </td>
                        <td>
                            <a href="<?= URLROOT ?>/users/edit/<?= $user->id ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                <i class="fa fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('change', function(e) {
    const checkbox = e.target.closest('.toggle-user-status');
    if (checkbox) toggleUserStatus(checkbox);
});

function toggleUserStatus(checkbox) {
    const userId = checkbox.dataset.userId;
    const originalState = checkbox.checked;
    const label = checkbox.nextElementSibling;
    
    checkbox.disabled = true;
    label.textContent = 'Cambiando...';
    
    fetch('<?= URLROOT ?>/users/toggle/' + userId, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    })
    .then(r => r.json())
    .then(res => {
        checkbox.disabled = false;
        if (res.success) {
            checkbox.checked = !originalState;
            label.textContent = checkbox.checked ? 'Activo' : 'Inactivo';
            checkbox.parentElement.querySelector('.form-check-input').checked = checkbox.checked;
        } else {
            checkbox.checked = originalState;
            label.textContent = originalState ? 'Activo' : 'Inactivo';
            Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'No se pudo cambiar el estado' });
        }
    })
    .catch(() => {
        checkbox.disabled = false;
        checkbox.checked = originalState;
        label.textContent = originalState ? 'Activo' : 'Inactivo';
        Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudo conectar con el servidor' });
    });
}
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>