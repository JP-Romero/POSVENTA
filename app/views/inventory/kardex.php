<?php $currentPage = 'kardex'; require APPROOT . '/views/inc/header.php'; ?>
<h1>Kardex de Inventario</h1>
  <div class="card shadow-sm mt-3">
    <div class="card-body">
        <form action="<?php echo URLROOT; ?>/inventories/kardex" method="get" class="row mb-4">
            <div class="col-md-8">
                <select name="product_id" class="form-select" aria-label="Seleccionar producto para filtrar">
                    <option value="">Todos los productos</option>
                    <?php foreach($data['products'] as $prod) : ?>
                        <option value="<?php echo $prod->id; ?>" <?php echo ($data['selected_product'] == $prod->id) ? 'selected' : ''; ?>><?php echo h($prod->nombre); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
            </div>
        </form>

        <div class="table-responsive">
        <table id="kardex-table" class="table table-hover mb-0">
            <thead class="table-light">
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
                        <td><?php echo h($mov->fecha); ?></td>
                        <td><?php echo h($mov->producto_nombre); ?></td>
                        <td>
                            <?php if($mov->tipo == 'Entrada') : ?>
                                <span class="badge bg-success">Entrada</span>
                            <?php else : ?>
                                <span class="badge bg-danger">Salida</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo h($mov->cantidad); ?></td>
                        <td><?php echo h($mov->motivo); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
  </div>

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
