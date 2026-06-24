<?php require APPROOT . '/views/inc/header.php'; ?>
  <div class="row mb-3">
    <div class="col-md-6">
      <h1>Historial de Ventas</h1>
    </div>
  </div>
  <div class="card card-body bg-light">
    <table id="sales-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Nro. Factura</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th>Total</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data['sales'] as $sale) : ?>
                <tr>
                    <td><?php echo $sale->fecha; ?></td>
                    <td><?php echo $sale->numero_factura; ?></td>
                    <td><?php echo $sale->cliente_nombre; ?></td>
                    <td><?php echo $sale->usuario_nombre; ?></td>
                    <td>$<?php echo number_format($sale->total, 2); ?></td>
                    <td>
                        <a href="<?php echo URLROOT; ?>/sales/invoice/<?php echo $sale->id; ?>" target="_blank" class="btn btn-info btn-sm">Ver Factura</a>
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
        $('#sales-table').DataTable({
            "order": [[0, "desc"]],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
            }
        });
    });
  </script>
<?php require APPROOT . '/views/inc/footer.php'; ?>
