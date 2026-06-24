<?php require APPROOT . '/views/inc/header.php'; ?>
  <div class="row mt-2">
    <div class="col-md-4 mb-4">
        <div class="card bg-primary text-white shadow">
            <div class="card-body">
                <h6 class="text-uppercase small">Ventas del Día</h6>
                <h2 class="fw-bold">$<?php echo number_format($data['daily_sales'], 2); ?></h2>
                <i class="fa fa-shopping-cart position-absolute bottom-0 end-0 p-3 opacity-25 fa-3x"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card bg-success text-white shadow">
            <div class="card-body">
                <h6 class="text-uppercase small">Productos en Stock</h6>
                <h2 class="fw-bold"><?php echo $data['total_products']; ?></h2>
                <i class="fa fa-boxes position-absolute bottom-0 end-0 p-3 opacity-25 fa-3x"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card bg-danger text-white shadow">
            <div class="card-body">
                <h6 class="text-uppercase small">Stock Bajo / Agotado</h6>
                <h2 class="fw-bold"><?php echo $data['low_stock']; ?></h2>
                <i class="fa fa-exclamation-triangle position-absolute bottom-0 end-0 p-3 opacity-25 fa-3x"></i>
            </div>
        </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Ventas Recientes</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Factura</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['recent_sales'] as $sale) : ?>
                            <tr>
                                <td><?php echo $sale->numero_factura; ?></td>
                                <td><?php echo $sale->cliente_nombre; ?></td>
                                <td><?php echo $sale->fecha; ?></td>
                                <td>$<?php echo number_format($sale->total, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white text-center">
                <a href="<?php echo URLROOT; ?>/sales" class="small text-decoration-none">Ver todas las ventas</a>
            </div>
        </div>
    </div>
  </div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
