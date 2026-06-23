<?php $currentPage = 'purchases'; require APPROOT . '/views/inc/header.php'; ?>
<div class="row mb-3">
    <div class="col-md-6">
      <h1>Compras</h1>
    </div>
    <div class="col-md-6 text-end">
      <a href="<?php echo URLROOT; ?>/purchases/add" class="btn btn-primary">
        <i class="fa fa-plus"></i> Registrar Compra
      </a>
    </div>
  </div>
  <?php flash('purchase_message'); ?>
  <div class="card card-body bg-light">
    <table id="purchases-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Proveedor</th>
                <th>Comprobante</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data['purchases'] as $purchase) : ?>
                <tr>
                    <td><?php echo $purchase->fecha; ?></td>
                    <td><?php echo $purchase->proveedor_nombre; ?></td>
                    <td><?php echo $purchase->comprobante; ?></td>
                    <td>$<?php echo number_format($purchase->total, 2); ?></td>
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
        $('#purchases-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
            }
        });
    });
  </script>
<?php require APPROOT . '/views/inc/footer.php'; ?>
