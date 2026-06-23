<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="row">
    <!-- POS Area -->
    <div class="col-md-8">
        <div class="card card-body bg-light">
            <div class="mb-3">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-primary text-white"><i class="fa fa-barcode"></i></span>
                    <input type="text" id="search-input" class="form-control" placeholder="Escanear código o buscar producto...">
                </div>
                <div id="search-results" class="list-group position-absolute w-100 z-3" style="display:none; max-height: 200px; overflow-y: auto;"></div>
            </div>

            <div class="table-responsive" style="height: 400px;">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Producto</th>
                            <th width="100">Cant.</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="cart-body">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Summary Area -->
    <div class="col-md-4">
        <div class="card card-body bg-dark text-white shadow">
            <h4 class="text-center mb-4">Resumen de Venta</h4>
            <div class="mb-3">
                <label class="form-label text-muted small">Cliente:</label>
                <select id="id_cliente" class="form-select bg-secondary text-white border-0">
                    <?php foreach($data['clients'] as $client) : ?>
                        <option value="<?php echo $client->id; ?>"><?php echo $client->nombre; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted small">Factura:</label>
                <input type="text" id="numero_factura" class="form-control bg-secondary text-white border-0" value="<?php echo $data['invoiceNumber']; ?>" readonly>
            </div>
            <div class="mb-4">
                <label class="form-label text-muted small">Método de Pago:</label>
                <select id="metodo_pago" class="form-select bg-secondary text-white border-0">
                    <option value="Efectivo">Efectivo</option>
                    <option value="Transferencia">Transferencia</option>
                    <option value="Tarjeta">Tarjeta</option>
                </select>
            </div>

            <hr class="border-secondary">

            <div class="d-flex justify-content-between mb-2">
                <span>Subtotal:</span>
                <span id="summary-subtotal">$0.00</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span>IVA (<?php echo $data['iva']; ?>%):</span>
                <span id="summary-tax">$0.00</span>
            </div>
            <div class="d-flex justify-content-between mb-4">
                <h3 class="fw-bold">TOTAL:</h3>
                <h3 class="fw-bold text-success" id="summary-total">$0.00</h3>
            </div>

            <button id="complete-sale" class="btn btn-success btn-lg w-100 py-3 fw-bold">COMPLETAR VENTA (F12)</button>
        </div>
    </div>
</div>

<script>
    let cart = [];
    const IVA_RATE = <?php echo ($data['iva'] / 100); ?>;

    document.getElementById('search-input').addEventListener('input', function() {
        let q = this.value;
        if(q.length < 2) {
            document.getElementById('search-results').style.display = 'none';
            return;
        }

        fetch('<?php echo URLROOT; ?>/pos/searchProduct?q=' + q)
            .then(res => res.json())
            .then(products => {
                let html = '';
                products.forEach(p => {
                    html += `<a href="#" class="list-group-item list-group-item-action" onclick="addToCart(${JSON.stringify(p).replace(/"/g, '&quot;')})">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">${p.nombre}</h6>
                            <small class="text-primary fw-bold">$${p.precio_venta}</small>
                        </div>
                        <small>Stock: ${p.stock}</small>
                    </a>`;
                });
                document.getElementById('search-results').innerHTML = html;
                document.getElementById('search-results').style.display = 'block';
            });
    });

    function addToCart(p) {
        let exists = cart.find(item => item.id === p.id);
        if(exists) {
            exists.quantity++;
        } else {
            cart.push({
                id: p.id,
                nombre: p.nombre,
                precio: parseFloat(p.precio_venta),
                quantity: 1
            });
        }
        document.getElementById('search-input').value = '';
        document.getElementById('search-results').style.display = 'none';
        renderCart();
    }

    function renderCart() {
        let html = '';
        let subtotal = 0;
        cart.forEach((item, index) => {
            let itemSubtotal = item.precio * item.quantity;
            subtotal += itemSubtotal;
            html += `<tr>
                <td>${item.nombre}</td>
                <td><input type="number" class="form-control form-control-sm" value="${item.quantity}" onchange="updateQty(${index}, this.value)"></td>
                <td>$${item.precio.toFixed(2)}</td>
                <td>$${itemSubtotal.toFixed(2)}</td>
                <td><button class="btn btn-outline-danger btn-sm" onclick="removeFromCart(${index})">&times;</button></td>
            </tr>`;
        });
        document.getElementById('cart-body').innerHTML = html;

        let tax = subtotal * IVA_RATE;
        let total = subtotal + tax;

        document.getElementById('summary-subtotal').innerText = '$' + subtotal.toFixed(2);
        document.getElementById('summary-tax').innerText = '$' + tax.toFixed(2);
        document.getElementById('summary-total').innerText = '$' + total.toFixed(2);
    }

    function updateQty(index, val) {
        cart[index].quantity = parseInt(val);
        if(cart[index].quantity <= 0) removeFromCart(index);
        renderCart();
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        renderCart();
    }

    document.getElementById('complete-sale').addEventListener('click', function() {
        if(cart.length === 0) return alert('El carrito está vacío');

        let subtotal = cart.reduce((sum, item) => sum + (item.precio * item.quantity), 0);
        let tax = subtotal * IVA_RATE;
        let total = subtotal + tax;

        let data = {
            id_cliente: document.getElementById('id_cliente').value,
            numero_factura: document.getElementById('numero_factura').value,
            metodo_pago: document.getElementById('metodo_pago').value,
            subtotal: subtotal,
            impuesto: tax,
            total: total,
            items: cart.map(item => ({
                id_producto: item.id,
                cantidad: item.quantity,
                precio_venta: item.precio,
                descuento: 0
            }))
        };

        fetch('<?php echo URLROOT; ?>/pos/save', {
            method: 'POST',
            body: JSON.stringify(data),
            headers: {'Content-Type': 'application/json'}
        })
        .then(res => res.json())
        .then(res => {
            if(res.status === 'success') {
                alert('Venta completada con éxito');
                window.location.reload();
            } else {
                alert('Error al procesar la venta');
            }
        });
    });
</script>
<?php require APPROOT . '/views/inc/footer.php'; ?>
