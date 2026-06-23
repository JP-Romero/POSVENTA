<?php require APPROOT . '/views/inc/header.php'; ?>
  <div class="row mb-3">
    <div class="col-md-6">
      <h1>Proveedores</h1>
    </div>
    <div class="col-md-6 text-end">
      <a href="<?php echo URLROOT; ?>/providers/add" class="btn btn-primary">
        <i class="fa fa-plus"></i> Nuevo Proveedor
      </a>
    </div>
  </div>
  <?php flash('provider_message'); ?>
  <div class="card card-body bg-light">
    <table id="providers-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Contacto</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data['providers'] as $provider) : ?>
                <tr>
                    <td><?php echo $provider->nombre; ?></td>
                    <td><?php echo $provider->contacto; ?></td>
                    <td><?php echo $provider->telefono; ?></td>
                    <td><?php echo $provider->correo; ?></td>
                    <td>
                        <a href="<?php echo URLROOT; ?>/providers/edit/<?php echo $provider->id; ?>" class="btn btn-warning btn-sm">Editar</a>
                        <form class="d-inline" action="<?php echo URLROOT; ?>/providers/delete/<?php echo $provider->id; ?>" method="post">
                            <input type="submit" value="Eliminar" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este proveedor?')">
                        </form>
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
        $('#providers-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
            }
        });
    });
  </script>
<?php require APPROOT . '/views/inc/footer.php'; ?>
