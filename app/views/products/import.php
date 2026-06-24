<?php require APPROOT . '/views/inc/header.php'; ?>
  <a href="<?php echo URLROOT; ?>/products" class="btn btn-light mb-3"><i class="fa fa-backward"></i> Volver</a>
  <div class="card card-body bg-light mt-2">
    <h2>Importar Productos (CSV)</h2>
    <p>Seleccione un archivo CSV para importar productos de forma masiva.</p>
    <div class="alert alert-info">
        <strong>Formato esperado:</strong><br>
        codigo_interno, codigo_barras, nombre, descripcion, id_categoria, id_proveedor, precio_compra, precio_venta, stock, stock_minimo
    </div>
    <form action="<?php echo URLROOT; ?>/products/import" method="post" enctype="multipart/form-data">
      <div class="mb-3">
        <label for="csv_file" class="form-label">Archivo CSV:</label>
        <input type="file" name="csv_file" class="form-control" accept=".csv" required>
      </div>
      <input type="submit" class="btn btn-success" value="Importar">
    </form>
  </div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
