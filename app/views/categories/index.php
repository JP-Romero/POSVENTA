<?php $currentPage = 'categories'; require APPROOT . '/views/inc/header.php'; ?>
<div class="row mb-3">
    <div class="col-md-6">
      <h1>Categorías</h1>
    </div>
    <div class="col-md-6 text-end">
      <a href="<?php echo URLROOT; ?>/categories/add" class="btn btn-primary">
        <i class="fa fa-plus"></i> Nueva Categoría
      </a>
    </div>
  </div>
  <?php flash('category_message'); ?>
  <div class="card card-body bg-light">
    <table id="categories-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data['categories'] as $category) : ?>
                <tr>
                    <td><?php echo $category->nombre; ?></td>
                    <td><?php echo $category->descripcion; ?></td>
                    <td>
                        <a href="<?php echo URLROOT; ?>/categories/edit/<?php echo $category->id; ?>" class="btn btn-warning btn-sm">Editar</a>
                        <button type="button" class="btn btn-danger btn-sm delete-category" data-id="<?php echo $category->id; ?>" data-name="<?php echo h($category->nombre); ?>">Eliminar</button>
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
        $('#categories-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
            }
        });
        
        // Delete category confirmation
        $(document).on('click', '.delete-category', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            showConfirm('¿Eliminar la categoría "' + name + '"?', function(result) {
                if (result) {
                    $.post('<?php echo URLROOT; ?>/categories/delete/' + id, function(r) {
                        location.reload();
                    });
                }
            });
        });
    });
  </script>
<?php require APPROOT . '/views/inc/footer.php'; ?>
