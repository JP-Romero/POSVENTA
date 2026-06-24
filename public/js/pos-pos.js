// POS Ventas - Estilo SambaPOS
let cart = [];
const IVA_RATE = <?= $data['iva'] / 100 ?>;

// Focus search on load
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('search-input').focus();
    loadFrequentProducts();
});

// Search functionality with debounce
let searchTimeout;
document.getElementById('search-input').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const query = this.value.trim();
    
    if (query.length < 1) {
        hideSearchResults();
        return;
    }
    
    searchTimeout = setTimeout(() => {
        searchProducts(query);
    }, 300);
});

function searchProducts(query) {
    fetch('<?= URLROOT ?>/pos/searchProduct?q=' + encodeURIComponent(query))
        .then(res => res.json())
        .then(products => {
            const resultsEl = document.getElementById('search-results');
            if (products.length === 0) {
                resultsEl.innerHTML = '<div class="pos-search-item no-results">No se encontraron productos</div>';
                showSearchResults();
                return;
            }
            
            let html = '';
            products.forEach(p => {
                const stockClass = p.stock > 10 ? 'stock-high' : (p.stock > 0 ? 'stock-low' : 'stock-out');
                html += `
                    <div class="pos-search-item" onclick="addToCart(${JSON.stringify(p).replace(/"/g, '&quot;')})">
                        ${p.imagen ? `<img src="${p.imagen}" alt="${p.nombre}" onerror="this.style.display='none'">` : ''}
                        <div class="pos-search-item-info">
                            <div class="pos-search-item-name">${p.nombre}</div>
                            <div class="pos-search-item-stock ${stockClass}">Stock: ${p.stock}</div>
                        </div>
                        <div class="pos-search-item-price">$${parseFloat(p.precio_venta).toFixed(2)}</div>
                    </div>
                `;
            });
            resultsEl.innerHTML = html;
            showSearchResults();
        })
        .catch(err => {
            console.error('Error:', err);
            hideSearchResults();
        });
}

function showSearchResults() {
    document.getElementById('search-results').style.display = 'block';
}

function hideSearchResults() {
    document.getElementById('search-results').style.display = 'none';
}

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
    hideSearchResults();
    renderCart();
    document.getElementById('search-input').focus();
    
    Swal.fire({
        toast: true,
        position: 'top-start',
        icon: 'success',
        title: `${p.nombre} agregado`,
        showConfirmButton: false,
        timer: 1000
    });
}

function renderCart() {
    const tbody = document.getElementById('cart-body');
    let html = '';
    let subtotal = 0;
    
    if (cart.length === 0) {
        html = `
            <tr>
                <td colspan="5" class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <div>Carrito vacío</div>
                    <small>Escanee o busque productos</small>
                </td>
            </tr>
        `;
    } else {
        cart.forEach((item, index) => {
            const itemSubtotal = item.precio * item.quantity;
            subtotal += itemSubtotal;
            html += `
                <tr>
                    <td>
                        <div class="cart-item">
                            ${item.imagen ? `<img src="${item.imagen}" alt="${item.nombre}" onerror="this.style.display='none'">` : ''}
                            <span>${item.nombre}</span>
                        </div>
                    </td>
                    <td>
                        <input type="number" class="qty-input" value="${item.quantity}" 
                               onchange="updateQty(${index}, this.value)" min="1">
                    </td>
                    <td>$${item.precio.toFixed(2)}</td>
                    <td><strong>$${itemSubtotal.toFixed(2)}</strong></td>
                    <td>
                        <button class="btn-remove" onclick="removeFromCart(${index})" title="Eliminar">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    
    tbody.innerHTML = html;
    updateSummary(subtotal);
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
        timer: 1000
    });
}

function updateSummary(subtotal) {
    const discountPercent = parseFloat(document.getElementById('discount-percent').value) || 0;
    const discount = subtotal * (discountPercent / 100);
    const tax = (subtotal - discount) * IVA_RATE;
    const total = subtotal - discount + tax;
    
    document.getElementById('summary-subtotal').innerText = '$' + subtotal.toFixed(2);
    document.getElementById('summary-tax').innerText = '$' + tax.toFixed(2);
    document.getElementById('summary-total').innerText = '$' + total.toFixed(2);
}

// Load frequent products
function loadFrequentProducts() {
    fetch('<?= URLROOT ?>/pos/getFrequentProducts?limit=12')
        .then(res => res.json())
        .then(products => {
            const container = document.getElementById('frequent-products');
            if (products.length === 0) return;
            
            let html = '';
            products.forEach(p => {
                html += `
                    <div class="pos-product-card" onclick="addToCart(${JSON.stringify(p).replace(/"/g, '&quot;')})">
                        ${p.imagen ? `<img src="${p.imagen}" alt="${p.nombre}" onerror="this.style.display='none'">` : '<div class="no-image">📦</div>'}
                        <div class="product-name">${p.nombre}</div>
                        <div class="product-price">$${parseFloat(p.precio_venta).toFixed(2)}</div>
                    </div>
                `;
            });
            container.innerHTML = html;
        })
        .catch(err => console.error('Error loading products:', err));
}

// Complete sale
document.getElementById('complete-sale').addEventListener('click', function() {
    if(cart.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Carrito vacío',
            text: 'Agregue productos antes de completar la venta',
            confirmButtonColor: '#2563eb'
        });
        return;
    }
    
    const discountPercent = parseFloat(document.getElementById('discount-percent').value) || 0;
    const subtotal = cart.reduce((sum, item) => sum + (item.precio * item.quantity), 0);
    const discount = subtotal * (discountPercent / 100);
    const tax = (subtotal - discount) * IVA_RATE;
    const total = subtotal - discount + tax;
    
    const data = {
        id_cliente: document.getElementById('id_cliente').value,
        numero_factura: document.getElementById('numero_factura').value,
        metodo_pago: selectedPayment,
        subtotal: subtotal,
        impuesto: tax,
        total: total,
        descuento: discount,
        auto_print: document.getElementById('auto_print')?.checked ?? true,
        items: cart.map(item => ({
            id_producto: item.id,
            cantidad: item.quantity,
            precio_venta: item.precio,
            descuento: 0
        }))
    };
    
    const btn = this;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Procesando...';
    btn.disabled = true;
    
    fetch('<?= URLROOT ?>/pos/save', {
        method: 'POST',
        body: JSON.stringify(data),
        headers: {'Content-Type': 'application/json'}
    })
    .then(res => res.json())
    .then(res => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        if(res.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: '¡Venta completada!',
                text: `Total: $${total.toFixed(2)}`,
                timer: 2000,
                showConfirmButton: false
            });
            
            cart = [];
            renderCart();
            document.getElementById('search-input').focus();
            
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
    .catch(err => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'Verifique su conexión e intente nuevamente',
            confirmButtonColor: '#dc2626'
        });
    });
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'F1') {
        e.preventDefault();
        showHelp();
    }
    if (e.key === 'F5') {
        e.preventDefault();
        document.getElementById('search-input').focus();
    }
    if (e.key === 'F12') {
        e.preventDefault();
        document.getElementById('complete-sale').click();
    }
    if (e.key === 'Escape') {
        e.preventDefault();
        hideSearchResults();
        document.getElementById('search-input').value = '';
        if (cart.length > 0) {
            Swal.fire({
                title: '¿Limpiar carrito?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, limpiar'
            }).then(result => {
                if (result.isConfirmed) {
                    cart = [];
                    renderCart();
                }
            });
        }
    }
});

function showHelp() {
    Swal.fire({
        title: 'Atajos de Teclado',
        html: `
            <div class="text-start">
                <p><kbd>F1</kbd> - Esta ayuda</p>
                <p><kbd>F5</kbd> - Enfocar búsqueda</p>
                <p><kbd>F12</kbd> - Completar venta</p>
                <p><kbd>Esc</kbd> - Limpiar búsqueda/carrito</p>
            </div>
        `,
        confirmButtonText: 'Cerrar',
        confirmButtonColor: '#2563eb'
    });
}

function printLastReceipt() {
    fetch('<?= URLROOT ?>/pos/printLastReceipt', { method: 'POST' })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Ticket enviado a impresora',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        });
}

// Click outside to hide search
document.addEventListener('click', function(e) {
    if (!e.target.closest('.pos-left')) {
        hideSearchResults();
    }
});