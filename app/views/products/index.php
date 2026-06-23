<?php require APPROOT . '/views/inc/header.php'; ?>
  <div class="row mb-3">
    <div class="col-md-4">
      <h1>Productos</h1>
    </div>
    <div class="col-md-8 text-end">
      <a href="<?php echo URLROOT; ?>/products/export" class="btn btn-success">
        <i class="fa fa-file-excel"></i> Exportar CSV
      </a>
      <a href="<?php echo URLROOT; ?>/products/add" class="btn btn-primary">
        <i class="fa fa-plus"></i> Nuevo Producto
      </a>
    </div>
  </div>
  <?php flash('product_message'); ?>
  <div class="card card-body bg-light">
    <table id="products-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>Imagen</th>
                <th>Código</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Precio Venta</th>
                <th>Stock</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data['products'] as $product) : ?>
                <tr>
                    <td class="text-center">
                        <?php if($product->imagen) : ?>
                            <img src="<?php echo URLROOT; ?>/img/products/<?php echo $product->imagen; ?>" width="50" height="50" class="img-thumbnail">
                        <?php else : ?>
                            <span class="badge bg-secondary">Sin imagen</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $product->codigo_interno; ?></td>
                    <td><?php echo $product->nombre; ?></td>
                    <td><?php echo $product->categoria_nombre; ?></td>
                    <td>$<?php echo number_format($product->precio_venta, 2); ?></td>
                    <td>
                        <?php if($product->stock <= $product->stock_minimo) : ?>
                            <span class="badge bg-danger"><?php echo $product->stock; ?></span>
                        <?php else : ?>
                            <span class="badge bg-success"><?php echo $product->stock; ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($product->estado) : ?>
                            <span class="badge bg-primary">Activo</span>
                        <?php else : ?>
                            <span class="badge bg-warning text-dark">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?php echo URLROOT; ?>/products/edit/<?php echo $product->id; ?>" class="btn btn-warning btn-sm">Editar</a>
                        <?php if(isAdmin()) : ?>
                        <form class="d-inline" action="<?php echo URLROOT; ?>/products/delete/<?php echo $product->id; ?>" method="post">
                            <input type="submit" value="Eliminar" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este producto?')">
                        </form>
                        <?php endif; ?>
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
        $('#products-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
            }
        });
    });
  </script>
<?php require APPROOT . '/views/inc/footer.php'; ?>
