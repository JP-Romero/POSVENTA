// POS V2.0 - Hybrid Librería Design
let cart = [];
const IVA_RATE = POSVENTA_CONFIG.IVA_RATE;
let currentCategory = 'all';

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    // Verificar si la caja está abierta
    if (!POSVENTA_CONFIG.cajaAbierta) {
        const aperturaModal = new bootstrap.Modal(document.getElementById('aperturaCajaModal'), {
            backdrop: 'static',
            keyboard: false
        });
        aperturaModal.show();
        
        document.getElementById('formAperturaCaja').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-abrir-caja');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Procesando...';
            btn.disabled = true;
            
            const formData = new FormData(this);
            // Append CSRF since it's not in the form
            formData.append('csrf_token', POSVENTA_CONFIG.CSRF_TOKEN);
            
            fetch(POSVENTA_CONFIG.URLROOT + '/cierre/abrirCaja', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if(res.status === 'success') {
                    Swal.fire('Éxito', 'Turno abierto correctamente', 'success').then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', res.message || 'Error al abrir caja', 'error');
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }
            })
            .catch(err => {
                Swal.fire('Error', 'Error de red', 'error');
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            });
        });
    } else {
        loadProducts();
        updateTotal();
        document.getElementById('search-input').focus();
    }
});

// Load products
function loadProducts() {
    fetch(POSVENTA_CONFIG.URLROOT + '/pos/searchProduct?q=')
        .then(res => res.json())
        .then(products => {
            const grid = document.getElementById('products-grid');
            let html = '';
            
            products.forEach(p => {
                const imgHtml = p.imagen ? 
                    `<img src="${p.imagen}" alt="${p.nombre}" onerror="this.outerHTML='<div class=\'pos-product-placeholder\'><i class=\'fas fa-book\'></i></div>'">` :
                    '<div class="pos-product-placeholder"><i class="fas fa-book"></i></div>';
            
                html += `
                    <div class="pos-product-card" onclick="addToCart(${JSON.stringify(p).replace(/"/g, '&quot;')})">
                        ${imgHtml}
                        <div class="pos-product-name">${p.nombre}</div>
                        <div class="pos-product-price">$${parseFloat(p.precio_venta).toFixed(2)}</div>
                    </div>
                `;
            });
            
            grid.innerHTML = html;
        })
        .catch(err => {
            console.error('Error:', err);
            document.getElementById('products-grid').innerHTML = '<div style="text-align:center;color:var(--gray);padding:20px;">Error al cargar productos</div>';
        });
}

// Add to cart
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
    renderCart();
    updateTotal();
    showSuccessToast(p.nombre);
}

// Render cart
function renderCart() {
    const container = document.getElementById('ticket-items');
    
    if (cart.length === 0) {
        container.innerHTML = '<div class="empty-cart">Carrito vacío<br><small>Escanee o seleccione productos</small></div>';
        return;
    }
    
    let html = '';
    cart.forEach(item => {
        html += `
            <div class="pos-ticket-item">
                <span class="pos-item-name">${item.nombre} (x${item.quantity})</span>
                <span class="pos-item-price">$${(item.precio * item.quantity).toFixed(2)}</span>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Update totals
function updateTotal() {
    const subtotal = cart.reduce((sum, item) => sum + (item.precio * item.quantity), 0);
    const tax = subtotal * IVA_RATE;
    const total = subtotal + tax;
    
    document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('tax').textContent = '$' + tax.toFixed(2);
    document.querySelectorAll('.btn-amount, #total-amount').forEach(el => el.textContent = '$' + total.toFixed(2));
}

// Clear cart
function clearCart() {
    if(cart.length === 0) return;
    
    Swal.fire({
        title: '¿Limpiar carrito?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, limpiar'
    }).then(result => {
        if (result.isConfirmed) {
            cart = [];
            renderCart();
            updateTotal();
        }
    });
}

// Show success toast
function showSuccessToast(productName) {
    Swal.fire({
        toast: true,
        position: 'top',
        icon: 'success',
        title: productName,
        showConfirmButton: false,
        timer: 600
    });
}

// Handle payment
function handlePayment(type) {
    if(cart.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Carrito vacío',
            confirmButtonColor: '#2563eb'
        });
        return;
    }
    
    const subtotal = cart.reduce((sum, item) => sum + (item.precio * item.quantity), 0);
    const tax = subtotal * IVA_RATE;
    const total = subtotal + tax;
    
    const data = {
        id_cliente: document.getElementById('id_cliente').value,
        numero_factura: document.querySelector('.pos-invoice').textContent.replace('#', ''),
        metodo_pago: type === 'efectivo' ? 'Efectivo' : 'Tarjeta',
        subtotal: subtotal,
        impuesto: tax,
        total: total,
        auto_print: true,
        csrf_token: POSVENTA_CONFIG.CSRF_TOKEN,
        items: cart.map(item => ({
            id_producto: item.id,
            cantidad: item.quantity,
            precio_venta: item.precio,
            descuento: 0
        }))
    };
    
    const btn = document.getElementById('complete-sale');
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Procesando...';
    btn.disabled = true;
    
    fetch(POSVENTA_CONFIG.URLROOT + '/pos/save', {
        method: 'POST',
        body: JSON.stringify(data),
        headers: {'Content-Type': 'application/json'}
    })
    .then(res => res.json())
    .then(res => {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
        
        if(res.status === 'success') {
            cart = [];
            renderCart();
            updateTotal();
            document.getElementById('search-input').focus();
            
            if (res.invoiceNumber) {
                document.querySelector('.pos-invoice').textContent = '#' + res.invoiceNumber;
            }

            // Show Ticket Preview Modal instead of Toast
            const iframe = document.getElementById('iframeTicket');
            iframe.src = POSVENTA_CONFIG.URLROOT + '/sales/invoice/' + res.id;
            const previewModal = new bootstrap.Modal(document.getElementById('previewTicketModal'));
            previewModal.show();
        } else {
            Swal.fire('Error', res.message || 'No se pudo procesar la venta', 'error');
        }
    })
    .catch(err => {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
        Swal.fire('Error', 'Error de red', 'error');
    });
}

// Print last receipt
function printLastReceipt() {
    fetch(POSVENTA_CONFIG.URLROOT + '/pos/printLastReceipt', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({csrf_token: POSVENTA_CONFIG.CSRF_TOKEN})
    })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Ticket impreso',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
}

// Category click handlers
document.querySelectorAll('.pos-cat-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.pos-cat-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        currentCategory = this.dataset.category;
        loadProducts();
    });
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'F5' || e.key === 'f5') {
        e.preventDefault();
        document.getElementById('search-input').focus();
    }
    if (e.key === 'F12') {
        e.preventDefault();
        handlePayment('efectivo');
    }
    if (e.key === 'Escape') {
        clearCart();
    }
});