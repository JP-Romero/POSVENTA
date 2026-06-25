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
  <div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
        <table id="categories-table" class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th style="width: 150px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['categories'] as $category) : ?>
                    <tr>
                        <td><?php echo h($category->nombre); ?></td>
                        <td><?php echo h($category->descripcion); ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo URLROOT; ?>/categories/edit/<?php echo $category->id; ?>" class="btn btn-outline-warning" title="Editar" aria-label="Editar categoría">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger delete-category" data-id="<?php echo $category->id; ?>" data-name="<?php echo h($category->nombre); ?>" title="Eliminar" aria-label="Eliminar categoría">
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
