<?php $currentPage = 'inventories'; require APPROOT . '/views/inc/header.php'; ?>
<h1>Control de Inventario</h1>
  <?php flash('inventory_message'); ?>
  <div class="card card-body bg-light mt-3">
    <table id="inventory-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Stock Actual</th>
                <th>Stock Mínimo</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data['stock'] as $item) : ?>
                <tr>
                    <td><?php echo $item->codigo_interno; ?></td>
                    <td><?php echo $item->nombre; ?></td>
                    <td><?php echo $item->categoria_nombre; ?></td>
                    <td>
                        <?php if($item->stock <= $item->stock_minimo) : ?>
                            <span class="badge bg-danger"><?php echo $item->stock; ?></span>
                        <?php else : ?>
                            <span class="badge bg-success"><?php echo $item->stock; ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $item->stock_minimo; ?></td>
                    <td>
                        <?php if($item->stock == 0) : ?>
                            <span class="text-danger fw-bold">Agotado</span>
                        <?php elseif($item->stock <= $item->stock_minimo) : ?>
                            <span class="text-warning fw-bold">Bajo Stock</span>
                        <?php else : ?>
                            <span class="text-success fw-bold">Normal</span>
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
        $('#inventory-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
            }
        });
    });
  </script>
<?php require APPROOT . '/views/inc/footer.php'; ?>
