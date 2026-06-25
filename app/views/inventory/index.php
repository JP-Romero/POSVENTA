<?php $currentPage = 'inventories'; require APPROOT . '/views/inc/header.php'; ?>
<h1>Control de Inventario</h1>
  <?php flash('inventory_message'); ?>
  <div class="card shadow-sm mt-3">
    <div class="card-body p-0">
        <div class="table-responsive">
        <table id="inventory-table" class="table table-hover mb-0">
            <thead class="table-light">
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
                        <td><?php echo h($item->codigo_interno); ?></td>
                        <td><?php echo h($item->nombre); ?></td>
                        <td><?php echo h($item->categoria_nombre); ?></td>
                        <td>
                            <?php if($item->stock <= $item->stock_minimo) : ?>
                                <span class="badge bg-danger"><?php echo $item->stock; ?></span>
                            <?php else : ?>
                                <span class="badge bg-success"><?php echo $item->stock; ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo h($item->stock_minimo); ?></td>
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
    </div>
  </div>

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
