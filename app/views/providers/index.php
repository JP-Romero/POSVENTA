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
                                <a href="<?= URLROOT ?>/providers/edit/<?= $provider->id ?>" class="btn btn-outline-warning" title="Editar">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a href="<?= URLROOT ?>/providers/history/<?= $provider->id ?>" class="btn btn-outline-info" title="Ver Historial">
                                    <i class="fa fa-history"></i>
                                </a>
                                <form class="d-inline" action="<?= URLROOT ?>/providers/delete/<?= $provider->id ?>" method="post" onsubmit="return confirm('¿Está seguro de eliminar este proveedor?')">
                                    <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#providers-table').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
        }
    });
});
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>