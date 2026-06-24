<?php require APPROOT . '/views/inc/header.php'; ?>
  <div class="row mb-3">
    <div class="col-md-6">
      <h1>Clientes</h1>
    </div>
    <div class="col-md-6 text-end">
      <a href="<?php echo URLROOT; ?>/clients/add" class="btn btn-primary">
        <i class="fa fa-plus"></i> Nuevo Cliente
      </a>
    </div>
  </div>
  <?php flash('client_message'); ?>
  <div class="card card-body bg-light">
    <table id="clients-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Dirección</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data['clients'] as $client) : ?>
                <tr>
                    <td><?php echo $client->nombre; ?></td>
                    <td><?php echo $client->telefono; ?></td>
                    <td><?php echo $client->correo; ?></td>
                    <td><?php echo $client->direccion; ?></td>
                    <td>
                        <a href="<?php echo URLROOT; ?>/clients/edit/<?php echo $client->id; ?>" class="btn btn-warning btn-sm">Editar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
  </div>

  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
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
