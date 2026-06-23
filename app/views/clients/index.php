<?php $currentPage = 'clients'; require APPROOT . '/views/inc/header.php'; ?>
<?php flash('client_message'); ?>

<div class="row mb-3">
    <div class="col-md-6">
        <h1>Clientes</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?= URLROOT ?>/clients/add" class="btn btn-primary">
            <i class="fa fa-plus"></i> Nuevo Cliente
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table id="clients-table" class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Dirección</th>
                    <th style="width: 150px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['clients'] as $client) : ?>
                    <tr>
                        <td><?= h($client->nombre) ?></td>
                        <td><?= h($client->telefono ?? '—') ?></td>
                        <td><?= h($client->correo ?? '—') ?></td>
                        <td><?= h($client->direccion ?? '—') ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= URLROOT ?>/clients/edit/<?= $client->id ?>" class="btn btn-outline-warning" title="Editar">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a href="<?= URLROOT ?>/clients/history/<?= $client->id ?>" class="btn btn-outline-info" title="Ver Historial">
                                    <i class="fa fa-history"></i>
                                </a>
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
    $('#clients-table').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
        }
    });
});
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>