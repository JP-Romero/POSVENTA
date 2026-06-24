// POS V2.0 - Hybrid Librería Design
let cart = [];
const IVA_RATE = <?= $data['iva'] / 100 ?>;
let currentCategory = 'all';

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadProducts();
    updateTotal();
    document.getElementById('search-input').focus();
});

// Load products
function loadProducts() {
    fetch('<?= URLROOT ?>/pos/searchProduct?q=')
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
    
    fetch('<?= URLROOT ?>/pos/save', {
        method: 'POST',
        body: JSON.stringify(data),
        headers: {'Content-Type': 'application/json'}
    })
    .then(res => res.json())
    .then(res => {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
        
        if(res.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: '¡Venta completada!',
                text: 'Total: $' + total.toFixed(2),
                timer: 2000,
                showConfirmButton: false
            });
            
            cart = [];
            renderCart();
            updateTotal();
            document.getElementById('search-input').focus();
            
            if (res.invoiceNumber) {
                document.querySelector('.pos-invoice').textContent = '#' + res.invoiceNumber;
            }
        }
    });
}

// Print last receipt
function printLastReceipt() {
    fetch('<?= URLROOT ?>/pos/printLastReceipt', { method: 'POST' })
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