<?php $currentPage = 'providers'; require APPROOT . '/views/inc/header.php'; ?>
<?php flash('provider_message'); ?>

<div class="row mb-3">
    <div class="col-md-6">
        <h1>Proveedores</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?= URLROOT ?>/providers/add" class="btn btn-primary">
            <i class="fa fa-plus"></i> Nuevo Proveedor
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
        <table id="providers-table" class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Contacto</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th style="width: 150px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['providers'] as $provider) : ?>
                    <tr>
                        <td><?= h($provider->nombre) ?></td>
                        <td><?= h($provider->contacto ?? '—') ?></td>
                        <td><?= h($provider->telefono ?? '—') ?></td>
                        <td><?= h($provider->correo ?? '—') ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= URLROOT ?>/providers/edit/<?= $provider->id ?>" class="btn btn-outline-warning" title="Editar" aria-label="Editar proveedor">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a href="<?= URLROOT ?>/providers/history/<?= $provider->id ?>" class="btn btn-outline-info" title="Ver Historial" aria-label="Ver historial del proveedor">
                                    <i class="fa fa-history"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger delete-provider" data-id="<?= $provider->id ?>" data-name="<?= h($provider->nombre) ?>" title="Eliminar" aria-label="Eliminar proveedor">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $('#providers-table').length) {
        $('#providers-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
            }
        });
    }
    
    // Delete provider confirmation
    $(document).on('click', '.delete-provider', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        showConfirm('¿Eliminar el proveedor "' + name + '"?', function(result) {
            if (result) {
                $.post('<?= URLROOT ?>/providers/delete/' + id, { action: 'delete' }, function(r) {
                    if (r.success) {
                        location.reload();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: r.message || 'No se pudo eliminar' });
                    }
                }, 'json');
            }
        });
    });
});
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>