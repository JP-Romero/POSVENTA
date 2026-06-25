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
  <div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
        <table id="purchases-table" class="table table-hover mb-0">
            <thead class="table-light">
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
                        <td><?php echo h($purchase->fecha); ?></td>
                        <td><?php echo h($purchase->proveedor_nombre); ?></td>
                        <td><?php echo h($purchase->comprobante); ?></td>
                        <td class="fw-bold"><?php echo fmt($purchase->total); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
  </div>

  <script>
    $(document).ready(function () {
        $('#purchases-table').DataTable({
            "order": [[0, "desc"]],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
            }
        });
    });
  </script>
<?php require APPROOT . '/views/inc/footer.php'; ?>
