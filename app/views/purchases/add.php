<?php $currentPage = 'purchases'; require APPROOT . '/views/inc/header.php'; ?>
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Registrar Compra</h5>
                <a href="<?php echo URLROOT; ?>/purchases" class="btn btn-outline-secondary btn-sm"><i class="fa fa-arrow-left me-1"></i> Volver</a>
            </div>
            <div class="card-body">
                <div class="row mt-2">
                    <div class="col-md-4 mb-3">
                        <label for="id_proveedor" class="form-label">Proveedor: <sup>*</sup></label>
                        <select id="id_proveedor" class="form-select" aria-label="Seleccionar proveedor">
                            <option value="">Seleccione...</option>
                            <?php foreach($data['providers'] as $prov) : ?>
                                <option value="<?php echo $prov->id; ?>"><?php echo h($prov->nombre); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="comprobante" class="form-label">Nro. Comprobante:</label>
                        <input type="text" id="comprobante" class="form-control" aria-label="Número de comprobante">
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="producto" class="form-label">Producto:</label>
                        <select id="producto" class="form-select" aria-label="Seleccionar producto">
                            <option value="">Seleccione...</option>
                            <?php foreach($data['products'] as $prod) : ?>
                                <option value="<?php echo $prod->id; ?>" data-nombre="<?php echo h($prod->nombre); ?>" data-precio="<?php echo $prod->precio_compra; ?>"><?php echo h($prod->nombre); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="cantidad" class="form-label">Cantidad:</label>
                        <input type="number" id="cantidad" class="form-control" value="1" min="1">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="precio" class="form-label">Precio Compra:</label>
                        <input type="number" step="0.01" id="precio" class="form-control">
                    </div>
                    <div class="col-md-2 mb-3 pt-4">
                        <button id="add-product" class="btn btn-primary w-100">Agregar</button>
                    </div>
                </div>

                <div class="table-responsive">
                <table class="table table-bordered mt-4">
                    <thead class="table-dark">
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody id="purchase-details">
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total:</th>
                            <th id="total-amount"><?= fmt(0) ?></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
                </div>

                <div class="text-end mt-3">
                    <button id="save-purchase" class="btn btn-success btn-lg">Guardar Compra</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let items = [];

    document.getElementById('producto').addEventListener('change', function() {
        let option = this.options[this.selectedIndex];
        if(option.value) {
            document.getElementById('precio').value = option.getAttribute('data-precio');
        }
    });

    document.getElementById('add-product').addEventListener('click', function() {
        let select = document.getElementById('producto');
        let option = select.options[select.selectedIndex];
        let id = select.value;
        let nombre = option.getAttribute('data-nombre');
        let cantidad = parseInt(document.getElementById('cantidad').value);
        let precio = parseFloat(document.getElementById('precio').value);

        if(!id || isNaN(cantidad) || isNaN(precio) || cantidad <= 0) return;

        items.push({
            id_producto: id,
            nombre: nombre,
            cantidad: cantidad,
            precio_compra: precio
        });

        renderTable();
    });

    document.getElementById('purchase-details').addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-remove-item');
        if (btn) {
            const index = parseInt(btn.dataset.index);
            items.splice(index, 1);
            renderTable();
        }
    });

    function renderTable() {
        let tbody = document.getElementById('purchase-details');
        tbody.innerHTML = '';
        let total = 0;

        items.forEach((item, index) => {
            let subtotal = item.cantidad * item.precio_compra;
            total += subtotal;
            tbody.innerHTML += `
                <tr>
                    <td>${item.nombre}</td>
                    <td>${item.cantidad}</td>
                    <td>${fmtMoney(item.precio_compra)}</td>
                    <td>${fmtMoney(subtotal)}</td>
                    <td><button class="btn btn-danger btn-sm btn-remove-item" data-index="${index}" aria-label="Eliminar item"><i class="fa fa-trash"></i></button></td>
                </tr>
            `;
        });

        document.getElementById('total-amount').innerText = fmtMoney(total);
    }

    function fmtMoney(n) {
        return '<?= getConfig("moneda_simbolo", "C$") ?> ' + Number(n).toLocaleString('es-NI', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    document.getElementById('save-purchase').addEventListener('click', function() {
        let id_proveedor = document.getElementById('id_proveedor').value;
        let comprobante = document.getElementById('comprobante').value;
        let total = items.reduce((sum, item) => sum + (item.cantidad * item.precio_compra), 0);

        if(!id_proveedor || items.length === 0) {
            Swal.fire({ icon: 'warning', title: 'Campos incompletos', text: 'Por favor complete todos los campos' });
            return;
        }

        let data = {
            id_proveedor: id_proveedor,
            comprobante: comprobante,
            total: total,
            items: items,
            csrf_token: '<?= generateCsrfToken() ?>'
        };

        fetch('<?php echo URLROOT; ?>/purchases/add', {
            method: 'POST',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                Swal.fire({ icon: 'success', title: 'Compra registrada', timer: 2000, timerProgressBar: true }).then(() => {
                    window.location.href = '<?php echo URLROOT; ?>/purchases';
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Error al registrar compra' });
            }
        })
        .catch(() => {
            Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudo conectar con el servidor' });
        });
    });
</script>
<?php require APPROOT . '/views/inc/footer.php'; ?>
