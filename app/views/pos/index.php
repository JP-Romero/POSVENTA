<?php $currentPage = 'pos'; require APPROOT . '/views/inc/header.php'; ?>

<div class="pos-container">
    <div class="pos-toolbar">
        <div class="pos-toolbar-title">
            <i class="fas fa-cash-register"></i>
            <span>Punto de Venta</span>
        </div>
        <div class="pos-toolbar-actions">
            <button class="pos-toolbar-btn" onclick="focusSearch()" title="F5">
                <i class="fas fa-search"></i>
                <span>Buscar</span>
                <span class="pos-shortcut-hint">F5</span>
            </button>
            <button class="pos-toolbar-btn" onclick="document.getElementById('complete-sale').click()" title="F12">
                <i class="fas fa-check-circle"></i>
                <span>Vender</span>
                <span class="pos-shortcut-hint">F12</span>
            </button>
            <button class="pos-toolbar-btn" onclick="clearCart()" title="Escape">
                <i class="fas fa-trash"></i>
                <span>Limpiar</span>
                <span class="pos-shortcut-hint">Esc</span>
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-xl-9">
            <div class="card card-body bg-light">
                <div class="pos-search-wrapper">
                    <input type="text" id="search-input" class="form-control pos-search-input" placeholder="🔍 Escanear código o buscar producto... (F5 para enfocar)" autofocus autocomplete="off">
                    <div id="search-results" class="pos-search-results"></div>
                </div>

                <div class="table-responsive" style="height: calc(100vh - 420px); min-height: 350px;">
                    <table class="table pos-cart-table">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="fas fa-box me-2"></i>Producto</th>
                                <th width="100"><i class="fas fa-hashtag me-2"></i>Cant.</th>
                                <th><i class="fas fa-tag me-2"></i>Precio</th>
                                <th><i class="fas fa-calculator me-2"></i>Subtotal</th>
                                <th width="60"></th>
                            </tr>
                        </thead>
                        <tbody id="cart-body">
                        </tbody>
                    </table>
                </div>

                <div class="pos-frequent-products" id="frequent-products">
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-xl-3">
            <div class="pos-summary-panel">
                <h4 class="pos-summary-title">
                    <i class="fas fa-receipt"></i>
                    Resumen de Venta
                </h4>
                
                <div class="pos-summary-field">
                    <label class="pos-summary-label">Cliente:</label>
                    <select id="id_cliente" class="form-select pos-summary-control">
                        <?php foreach($data['clients'] as $client) : ?>
                            <option value="<?= $client->id ?>"><?= $client->nombre ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="pos-summary-field">
                    <label class="pos-summary-label">Factura:</label>
                    <input type="text" id="numero_factura" class="form-control pos-summary-control" value="<?= $data['invoiceNumber'] ?>" readonly>
                </div>
                
                <div class="pos-summary-field">
                    <label class="pos-summary-label">Método de Pago:</label>
                    <select id="metodo_pago" class="form-select pos-summary-control">
                        <option value="Efectivo">💵 Efectivo</option>
                        <option value="Transferencia">🏦 Transferencia</option>
                        <option value="Tarjeta">💳 Tarjeta</option>
                    </select>
                </div>

                <div class="pos-summary-divider"></div>

                <div class="pos-summary-row">
                    <span>Subtotal:</span>
                    <span id="summary-subtotal" class="fw-bold">$0.00</span>
                </div>
                <div class="pos-summary-row">
                    <span>IVA (<?= $data['iva'] ?>%):</span>
                    <span id="summary-tax" class="fw-bold">$0.00</span>
                </div>
                
                <div class="pos-summary-total-row">
                    <span class="pos-summary-total-label">TOTAL:</span>
                    <span class="pos-summary-total-amount" id="summary-total">$0.00</span>
                </div>

                <div class="pos-summary-divider"></div>

                <?php if ($data['printer']): ?>
                <div class="pos-printer-status success">
                    <i class="fa fa-print"></i> 
                    <strong><?= $data['printer']->nombre ?></strong> (<?= $data['printer']->ancho_papel ?>mm)
                </div>
                <?php else: ?>
                <div class="pos-printer-status warning">
                    <i class="fa fa-exclamation-triangle"></i> 
                    Sin impresora activa - Configure en Ajustes
                </div>
                <?php endif; ?>

                <div class="pos-auto-print-toggle">
                    <label class="form-check-label mb-0" for="auto_print">
                        <i class="fa fa-print me-2"></i>Imprimir automáticamente
                    </label>
                    <input class="form-check-input" type="checkbox" id="auto_print" checked>
                </div>

                <button id="complete-sale" class="pos-complete-btn">
                    <i class="fas fa-check-circle me-2"></i>COMPLETAR VENTA <span class="pos-shortcut-hint">F12</span>
                </button>
                
                <button class="pos-reprint-btn" onclick="printLastReceipt()">
                    <i class="fa fa-print me-2"></i> Reimprimir Último Ticket
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let cart = [];
const IVA_RATE = <?= $data['iva'] / 100 ?>;

function focusSearch() {
    const input = document.getElementById('search-input');
    input.focus();
    input.select();
}

function clearCart() {
    if (cart.length === 0) return;
    
    Swal.fire({
        title: '¿Limpiar carrito?',
        text: 'Se eliminarán todos los productos del carrito actual',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, limpiar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            cart = [];
            renderCart();
            focusSearch();
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Carrito limpiado',
                showConfirmButton: false,
                timer: 2000
            });
        }
    });
}

document.getElementById('search-input').addEventListener('input', function() {
    let q = this.value.trim();
    if(q.length < 1) {
        document.getElementById('search-results').style.display = 'none';
        return;
    }

    fetch('<?= URLROOT ?>/pos/searchProduct?q=' + encodeURIComponent(q))
        .then(res => {
            if (!res.ok) throw new Error('Error en la búsqueda');
            return res.json();
        })
        .then(products => {
            if (products.length === 0) {
                document.getElementById('search-results').innerHTML = '<div class="pos-search-item"><span class="text-muted">No se encontraron productos</span></div>';
                document.getElementById('search-results').style.display = 'block';
                return;
            }
            
            let html = '';
            products.forEach(p => {
                const stockClass = p.stock > 10 ? 'text-success' : (p.stock > 0 ? 'text-warning' : 'text-danger');
                const stockIcon = p.stock > 10 ? 'fa-check-circle' : (p.stock > 0 ? 'fa-exclamation-triangle' : 'fa-times-circle');
                html += `<div class="pos-search-item" onclick="addToCart(${JSON.stringify(p).replace(/"/g, '&quot;')})">
                    ${p.imagen ? `<img src="${p.imagen}" alt="${p.nombre}" onerror="this.style.display='none'">` : ''}
                    <div class="pos-search-item-info">
                        <div class="pos-search-item-name">${p.nombre}</div>
                        <div class="pos-search-item-stock ${stockClass}"><i class="fa ${stockIcon} me-1"></i>Stock: ${p.stock}</div>
                    </div>
                    <div class="pos-search-item-price">$${parseFloat(p.precio_venta).toFixed(2)}</div>
                </div>`;
            });
            document.getElementById('search-results').innerHTML = html;
            document.getElementById('search-results').style.display = 'block';
        })
        .catch(err => {
            console.error('Error:', err);
            document.getElementById('search-results').innerHTML = '<div class="pos-search-item text-danger">Error al buscar productos</div>';
            document.getElementById('search-results').style.display = 'block';
        });
});

document.addEventListener('click', function(e) {
    if (!e.target.closest('.pos-search-wrapper')) {
        document.getElementById('search-results').style.display = 'none';
    }
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
            quantity: 1,
            imagen: p.imagen || null
        });
    }
    document.getElementById('search-input').value = '';
    document.getElementById('search-results').style.display = 'none';
    renderCart();
    setTimeout(() => focusSearch(), 50);
    
    Swal.fire({
        toast: true,
        position: 'top-start',
        icon: 'success',
        title: `${p.nombre} agregado`,
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true
    });
}

function renderCart() {
    let html = '';
    let subtotal = 0;
    
    if (cart.length === 0) {
        html = '<tr><td colspan="5" class="text-center text-muted py-5"><i class="fas fa-shopping-cart fa-3x mb-3 opacity-25"></i><br>El carrito está vacío<br><small class="opacity-50">Escanea o busca productos para comenzar</small></td></tr>';
    } else {
        cart.forEach((item, index) => {
            let itemSubtotal = item.precio * item.quantity;
            subtotal += itemSubtotal;
            html += `<tr class="animate-fade-in">
                <td>
                    <div class="d-flex align-items-center gap-2">
                        ${item.imagen ? `<img src="${item.imagen}" width="32" height="32" style="object-fit:cover;border-radius:4px" onerror="this.style.display='none'">` : '<div class="bg-secondary" style="width:32px;height:32px;border-radius:4px"></div>'}
                        <span class="fw-medium">${item.nombre}</span>
                    </div>
                </td>
                <td><input type="number" class="form-control form-control-sm pos-cart-qty-input" value="${item.quantity}" onchange="updateQty(${index}, this.value)" min="1"></td>
                <td class="fw-medium">$${item.precio.toFixed(2)}</td>
                <td class="fw-bold text-primary">$${itemSubtotal.toFixed(2)}</td>
                <td><button class="btn btn-outline-danger btn-sm pos-cart-remove-btn" onclick="removeFromCart(${index})" title="Eliminar"><i class="fas fa-times"></i></button></td>
            </tr>`;
        });
    }
    
    document.getElementById('cart-body').innerHTML = html;

    let tax = subtotal * IVA_RATE;
    let total = subtotal + tax;

    document.getElementById('summary-subtotal').innerText = '$' + subtotal.toFixed(2);
    document.getElementById('summary-tax').innerText = '$' + tax.toFixed(2);
    document.getElementById('summary-total').innerText = '$' + total.toFixed(2);
}

function updateQty(index, val) {
    const qty = parseInt(val);
    if (isNaN(qty) || qty <= 0) {
        removeFromCart(index);
        return;
    }
    cart[index].quantity = qty;
    renderCart();
}

function removeFromCart(index) {
    const productName = cart[index].nombre;
    cart.splice(index, 1);
    renderCart();
    
    Swal.fire({
        toast: true,
        position: 'top-start',
        icon: 'info',
        title: `${productName} eliminado`,
        showConfirmButton: false,
        timer: 1500
    });
}

document.getElementById('complete-sale').addEventListener('click', function() {
    if(cart.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Carrito vacío',
            text: 'Agrega productos antes de completar la venta',
            confirmButtonColor: '#2563eb'
        });
        return;
    }

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
        auto_print: document.getElementById('auto_print').checked,
        items: cart.map(item => ({
            id_producto: item.id,
            cantidad: item.quantity,
            precio_venta: item.precio,
            descuento: 0
        }))
    };

    const btn = this;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Procesando...';
    btn.disabled = true;

    fetch('<?= URLROOT ?>/pos/save', {
        method: 'POST',
        body: JSON.stringify(data),
        headers: {'Content-Type': 'application/json'}
    })
    .then(res => {
        if (!res.ok) throw new Error('Error al procesar la venta');
        return res.json();
    })
    .then(res => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        if(res.status === 'success') {
            btn.classList.add('just-completed');
            setTimeout(() => btn.classList.remove('just-completed'), 1000);
            
            Swal.fire({
                icon: 'success',
                title: '¡Venta completada!',
                text: `Total: $${total.toFixed(2)}`,
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
            
            cart = [];
            renderCart();
            focusSearch();
            
            if (res.invoiceNumber) {
                document.getElementById('numero_factura').value = res.invoiceNumber;
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: res.message || 'No se pudo completar la venta',
                confirmButtonColor: '#dc2626'
            });
        }
    })
    .catch((err) => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'Verifica tu conexión e intenta nuevamente',
            confirmButtonColor: '#dc2626'
        });
        console.error('Error:', err);
    });
});

function printLastReceipt() {
    const btn = event.target.closest('button');
    const original = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Imprimiendo...';
    btn.disabled = true;

    fetch('<?= URLROOT ?>/pos/printLastReceipt', { method: 'POST' })
        .then(res => {
            if (!res.ok) throw new Error('Error al imprimir');
            return res.json();
        })
        .then(res => {
            btn.innerHTML = original;
            btn.disabled = false;
            
            if (res.success) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Ticket enviado a impresora',
                    showConfirmButton: false,
                    timer: 2000
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'No se pudo imprimir',
                    text: res.message || 'Verifica la configuración de la impresora',
                    confirmButtonColor: '#f59e0b'
                });
            }
        })
        .catch((err) => {
            btn.innerHTML = original;
            btn.disabled = false;
            
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo comunicar con la impresora',
                confirmButtonColor: '#dc2626'
            });
            console.error('Error:', err);
        });
}

document.addEventListener('keydown', function(e) {
    const tagName = document.activeElement.tagName.toLowerCase();
    const isInput = tagName === 'input' || tagName === 'select' || tagName === 'textarea';
    
    if (e.key === 'F1') {
        e.preventDefault();
        Swal.fire({
            title: 'Atajos de Teclado',
            html: `
                <div class="text-start">
                    <p><kbd>F1</kbd> - Esta ayuda</p>
                    <p><kbd>F2</kbd> - Enfocar búsqueda</p>
                    <p><kbd>F5</kbd> - Enfocar búsqueda</p>
                    <p><kbd>F12</kbd> - Completar venta</p>
                    <p><kbd>Escape</kbd> - Limpiar búsqueda/carrito</p>
                    <p><kbd>Delete</kbd> - Eliminar último ítem</p>
                    <p><kbd>Enter</kbd> - Agregar producto (en búsqueda)</p>
                </div>
            `,
            confirmButtonText: 'Cerrar',
            confirmButtonColor: '#2563eb'
        });
    }
    
    if (e.key === 'F2') {
        e.preventDefault();
        focusSearch();
    }
    
    if (e.key === 'F5' && !isInput) {
        e.preventDefault();
        focusSearch();
    }
    
    if (e.key === 'F12') {
        e.preventDefault();
        document.getElementById('complete-sale').click();
    }
    
    if (e.key === 'Escape') {
        e.preventDefault();
        if (document.getElementById('search-results').style.display === 'block') {
            document.getElementById('search-results').style.display = 'none';
            document.getElementById('search-input').value = '';
        } else if (cart.length > 0) {
            clearCart();
        }
        focusSearch();
    }
    
    if (e.key === 'Delete' && !isInput && cart.length > 0) {
        e.preventDefault();
        removeFromCart(cart.length - 1);
    }
    
    if (e.key === 'Enter' && isInput && document.getElementById('search-input') === document.activeElement) {
        const firstResult = document.querySelector('.pos-search-item');
        if (firstResult) {
            e.preventDefault();
            firstResult.click();
        }
    }
});

function loadFrequentProducts() {
    fetch('<?= URLROOT ?>/pos/getFrequentProducts?limit=12')
        .then(res => res.json())
        .then(products => {
            if (products.length === 0) {
                document.getElementById('frequent-products').style.display = 'none';
                return;
            }
            let html = '';
            products.forEach(p => {
                html += `<div class="pos-frequent-item" onclick="addToCart(${JSON.stringify(p).replace(/"/g, '&quot;')})">
                    ${p.imagen ? `<img src="${p.imagen}" alt="${p.nombre}" class="pos-frequent-item-img" onerror="this.style.display='none'">` : '<div class="pos-frequent-item-img bg-secondary"></div>'}
                    <div class="pos-frequent-item-name">${p.nombre}</div>
                    <div class="pos-frequent-item-price">$${parseFloat(p.precio_venta).toFixed(2)}</div>
                </div>`;
            });
            document.getElementById('frequent-products').innerHTML = html;
            document.getElementById('frequent-products').style.display = 'grid';
        })
        .catch(err => console.error('Error loading frequent products:', err));
}

focusSearch();
renderCart();
loadFrequentProducts();
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>