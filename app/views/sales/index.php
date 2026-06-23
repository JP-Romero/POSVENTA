<?php $currentPage = 'sales'; require APPROOT . '/views/inc/header.php'; ?>
<?php flash('sales_message'); ?>

<div class="row mb-3">
    <div class="col-md-6">
        <h1>Historial de Ventas</h1>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table id="sales-table" class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Fecha</th>
                    <th>Nro. Factura</th>
                    <th>Cliente</th>
                    <th>Vendedor</th>
                    <th>Total</th>
                    <th style="width: 120px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['sales'] as $sale) : ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($sale->fecha)) ?></td>
                        <td><strong><?= $sale->numero_factura ?></strong></td>
                        <td><?= $sale->cliente_nombre ?></td>
                        <td><?= $sale->usuario_nombre ?></td>
                        <td class="fw-bold"><?= fmt($sale->total) ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= URLROOT ?>/sales/invoice/<?= $sale->id ?>" target="_blank" class="btn btn-outline-info" title="Ver Factura HTML">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="<?= URLROOT ?>/sales/invoicePdf/<?= $sale->id ?>" target="_blank" class="btn btn-outline-danger" title="Descargar PDF">
                                    <i class="fa fa-file-pdf"></i>
                                </a>
                                <a href="<?= URLROOT ?>/sales/printReceipt/<?= $sale->id ?>" class="btn btn-outline-success" title="Imprimir Ticket Térmico">
                                    <i class="fa fa-print"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

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