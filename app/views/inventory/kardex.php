<?php require APPROOT . '/views/inc/header.php'; ?>
  <h1>Kardex de Inventario</h1>
  <div class="card card-body bg-light mt-3">
    <form action="<?php echo URLROOT; ?>/inventories/kardex" method="get" class="row mb-4">
        <div class="col-md-8">
            <select name="product_id" class="form-select">
                <option value="">Todos los productos</option>
                <?php foreach($data['products'] as $prod) : ?>
                    <option value="<?php echo $prod->id; ?>" <?php echo ($data['selected_product'] == $prod->id) ? 'selected' : ''; ?>><?php echo $prod->nombre; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
        </div>
    </form>

    <table id="kardex-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Producto</th>
                <th>Tipo</th>
                <th>Cantidad</th>
                <th>Motivo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data['movements'] as $mov) : ?>
                <tr>
                    <td><?php echo $mov->fecha; ?></td>
                    <td><?php echo $mov->producto_nombre; ?></td>
                    <td>
                        <?php if($mov->tipo == 'Entrada') : ?>
                            <span class="badge bg-success">Entrada</span>
                        <?php else : ?>
                            <span class="badge bg-danger">Salida</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $mov->cantidad; ?></td>
                    <td><?php echo $mov->motivo; ?></td>
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
        $('#kardex-table').DataTable({
            "order": [[0, "desc"]],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
            }
        });
    });
  </script>
<?php require APPROOT . '/views/inc/footer.php'; ?>
