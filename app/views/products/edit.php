<?php $currentPage = 'products'; require APPROOT . '/views/inc/header.php'; ?>
<a href="<?php echo URLROOT; ?>/products" class="btn btn-light mb-3"><i class="fa fa-backward"></i> Volver</a>
  <div class="card card-body bg-light mt-2">
    <h2>Editar Producto</h2>
    <p>Modifique los datos del producto</p>
    <form action="<?php echo URLROOT; ?>/products/edit/<?php echo $data['id']; ?>" method="post" enctype="multipart/form-data">
      <div class="row">
        <div class="col-md-4 mb-3">
            <label for="codigo_interno" class="form-label">Código Interno:</label>
            <input type="text" name="codigo_interno" class="form-control" value="<?php echo $data['codigo_interno']; ?>">
        </div>
        <div class="col-md-4 mb-3">
            <label for="codigo_barras" class="form-label">Código de Barras:</label>
            <input type="text" name="codigo_barras" class="form-control" value="<?php echo $data['codigo_barras']; ?>">
        </div>
        <div class="col-md-4 mb-3">
            <label for="nombre" class="form-label">Nombre: <sup>*</sup></label>
            <input type="text" name="nombre" class="form-control <?php echo (!empty($data['nombre_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['nombre']; ?>">
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
            <label for="id_categoria" class="form-label">Categoría: <sup>*</sup></label>
            <select name="id_categoria" class="form-select">
                <?php foreach($data['categories'] as $cat) : ?>
                    <option value="<?php echo $cat->id; ?>" <?php echo ($data['id_categoria'] == $cat->id) ? 'selected' : ''; ?>><?php echo $cat->nombre; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label for="id_proveedor" class="form-label">Proveedor: <sup>*</sup></label>
            <select name="id_proveedor" class="form-select">
                <?php foreach($data['providers'] as $prov) : ?>
                    <option value="<?php echo $prov->id; ?>" <?php echo ($data['id_proveedor'] == $prov->id) ? 'selected' : ''; ?>><?php echo $prov->nombre; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
      </div>

      <div class="row">
        <div class="col-md-3 mb-3">
            <label for="precio_compra" class="form-label">Precio Compra:</label>
            <input type="number" step="0.01" name="precio_compra" class="form-control" value="<?php echo $data['precio_compra']; ?>">
        </div>
        <div class="col-md-3 mb-3">
            <label for="precio_venta" class="form-label">Precio Venta:</label>
            <input type="number" step="0.01" name="precio_venta" class="form-control" value="<?php echo $data['precio_venta']; ?>">
        </div>
        <div class="col-md-3 mb-3">
            <label for="stock" class="form-label">Stock:</label>
            <input type="number" name="stock" class="form-control" value="<?php echo $data['stock']; ?>">
        </div>
        <div class="col-md-3 mb-3">
            <label for="stock_minimo" class="form-label">Stock Mínimo:</label>
            <input type="number" name="stock_minimo" class="form-control" value="<?php echo $data['stock_minimo']; ?>">
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6 text-center">
            <?php if($data['imagen']) : ?>
                <img src="<?php echo URLROOT; ?>/img/products/<?php echo $data['imagen']; ?>" width="150" class="img-thumbnail">
                <p>Imagen actual</p>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <label for="imagen" class="form-label">Cambiar Imagen:</label>
            <input type="file" name="imagen" class="form-control">
            <div class="form-check mt-3">
                <input class="form-check-input" type="checkbox" name="estado" id="estado" <?php echo $data['estado'] ? 'checked' : ''; ?>>
                <label class="form-check-label" for="estado">Producto Activo</label>
            </div>
        </div>
      </div>

      <input type="submit" class="btn btn-success" value="Actualizar Producto">
    </form>
  </div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
