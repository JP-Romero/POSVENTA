const CURRENCY = POSVENTA_CONFIG.CURRENCY_SYMBOL || 'C$';
const IVA_RATE = POSVENTA_CONFIG.IVA_RATE;
const IVA_ENABLED = POSVENTA_CONFIG.IVA_ENABLED;
const EXCHANGE_RATE = POSVENTA_CONFIG.EXCHANGE_RATE;
const PAYMENT_METHODS = POSVENTA_CONFIG.PAYMENT_METHODS.split(',').map(s => s.trim());
let cart = [];
let currentCategory = 'all';
let pendingPaymentMethod = null;

function fmt(n) { return CURRENCY + Number(n).toFixed(2); }
function fmtUsd(n) { return '$' + Number(n).toFixed(2); }

function getPayMethods() { return PAYMENT_METHODS; }

document.addEventListener('DOMContentLoaded', () => {
    if (!POSVENTA_CONFIG.cajaAbierta) {
        const aperturaModal = new bootstrap.Modal(document.getElementById('aperturaCajaModal'), {
            backdrop: 'static', keyboard: false
        });
        aperturaModal.show();
        document.getElementById('formAperturaCaja').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-abrir-caja');
            const orig = btn.innerHTML;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Procesando...';
            btn.disabled = true;
            const fd = new FormData(this);
            fd.append('csrf_token', POSVENTA_CONFIG.CSRF_TOKEN);
            fetch(POSVENTA_CONFIG.URLROOT + '/cierre/abrirCaja', {method: 'POST', body: fd})
                .then(r => r.json())
                .then(r => {
                    if (r.status === 'success') {
                        Swal.fire('Éxito', 'Turno abierto correctamente', 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', r.message || 'Error al abrir caja', 'error');
                        btn.innerHTML = orig; btn.disabled = false;
                    }
                })
                .catch(() => { Swal.fire('Error', 'Error de red', 'error'); btn.innerHTML = orig; btn.disabled = false; });
        });
    } else {
        loadProducts();
        renderPaymentButtons();
        updateTotal();
        document.getElementById('search-input').focus();
    }

    document.getElementById('btn-reimprimir').addEventListener('click', printLastReceipt);
    document.getElementById('btn-cancelar').addEventListener('click', clearCart);

    let searchTimeout;
    document.getElementById('search-input').addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => loadProducts(e.target.value), 300);
    });

    document.querySelector('.pos-categories').addEventListener('click', function(e) {
        const btn = e.target.closest('.pos-cat-btn');
        if (!btn) return;
        document.querySelectorAll('.pos-cat-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentCategory = btn.dataset.category;
        loadProducts(document.getElementById('search-input').value);
    });

    document.getElementById('products-grid').addEventListener('click', function(e) {
        const card = e.target.closest('.pos-product-card');
        if (!card) return;
        addToCart({id: card.dataset.id, nombre: card.dataset.nombre, precio_venta: card.dataset.precio});
    });

    document.getElementById('ticket-items').addEventListener('click', function(e) {
        const btn = e.target.closest('button');
        if (!btn) return;
        const el = btn.closest('.pos-ticket-item');
        if (!el) return;
        const idx = parseInt(el.dataset.index, 10);
        if (isNaN(idx) || idx < 0 || idx >= cart.length) return;
        if (btn.classList.contains('ticket-qty-plus')) {
            cart[idx].quantity++;
        } else if (btn.classList.contains('ticket-qty-minus')) {
            if (cart[idx].quantity <= 1) { cart.splice(idx, 1); }
            else { cart[idx].quantity--; }
        } else if (btn.classList.contains('ticket-item-remove')) {
            cart.splice(idx, 1);
        }
        renderCart();
        updateTotal();
    });

    document.getElementById('split-dolar')?.addEventListener('input', function() {
        const usd = parseFloat(this.value) || 0;
        document.getElementById('split-dolar-equiv').textContent = 'Equiv: ' + fmt(usd * EXCHANGE_RATE);
        validateSplit();
    });
    ['split-recibido', 'split-tarjeta', 'split-dolar'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', validateSplit);
    });
    document.getElementById('btn-confirm-split')?.addEventListener('click', confirmSplitPayment);

    // Payment confirmation modal
    document.getElementById('monto-recibido')?.addEventListener('input', calculateConfirmChange);
    document.getElementById('btn-confirm-payment')?.addEventListener('click', confirmPayment);
    document.getElementById('paymentConfirmModal')?.addEventListener('hidden.bs.modal', function() {
        pendingPaymentMethod = null;
        document.getElementById('monto-recibido').value = '';
        document.getElementById('confirm-error').style.display = 'none';
        document.getElementById('confirm-cambio').textContent = fmt(0);
    });
});

function renderPaymentButtons() {
    const container = document.getElementById('payment-buttons');
    const methods = getPayMethods();
    const cfg = {
        efectivo: {icon: 'fa-money-bill-wave', text: 'COBRAR EFECTIVO', css: 'pos-btn-pay'},
        tarjeta:  {icon: 'fa-credit-card',      text: 'COBRAR TARJETA',  css: 'pos-btn-pay pos-btn-pay-card'},
        dolar:    {icon: 'fa-dollar-sign',       text: 'COBRAR DÓLAR',   css: 'pos-btn-pay pos-btn-pay-dollar'},
        mixto:    {icon: 'fa-split',             text: 'PAGO MIXTO',     css: 'pos-btn-action pos-btn-mixto'}
    };
    let html = '';
    const primary = methods.filter(m => m !== 'mixto');
    primary.forEach(m => {
        if (!cfg[m]) return;
        const initialAmount = m === 'dolar' ? fmtUsd(0) : fmt(0);
        html += `<button class="${cfg[m].css}" data-method="${m}" aria-label="${cfg[m].text}">
            <span class="btn-amount" id="btn-amount-${m}">${initialAmount}</span>
            <span class="btn-text"><i class="fas ${cfg[m].icon}" aria-hidden="true"></i> ${cfg[m].text}</span>
        </button>`;
    });
    if (methods.includes('mixto')) {
        html += `<button class="${cfg.mixto.css}" data-method="mixto" aria-label="${cfg.mixto.text}" style="margin-top:2px;">
            <i class="fas ${cfg.mixto.icon}" aria-hidden="true"></i> ${cfg.mixto.text}
        </button>`;
    }
    container.innerHTML = html;
    container.querySelectorAll('[data-method]').forEach(btn => {
        btn.addEventListener('click', () => handlePayment(btn.dataset.method));
    });
}

function loadProducts(query = '') {
    let url = POSVENTA_CONFIG.URLROOT + '/pos/searchProduct?q=' + encodeURIComponent(query);
    if (currentCategory !== 'all') url += '&categoria_id=' + encodeURIComponent(currentCategory);
    fetch(url)
        .then(r => r.json())
        .then(products => {
            const grid = document.getElementById('products-grid');
            let html = '';
            products.forEach(p => {
                const img = p.imagen
                    ? `<img src="${p.imagen}" alt="${p.nombre}" onerror="this.outerHTML='<div class=\\'pos-product-placeholder\\'><i class=\\'fas fa-book\\' aria-hidden=\\'true\\'></i></div>'">`
                    : '<div class="pos-product-placeholder"><i class="fas fa-book" aria-hidden="true"></i></div>';
                html += `<div class="pos-product-card" tabindex="0" role="button" aria-label="Agregar ${p.nombre} al carrito" data-id="${p.id}" data-nombre="${p.nombre}" data-precio="${p.precio_venta}">
                    ${img}
                    <div class="pos-product-name">${p.nombre}</div>
                    <div class="pos-product-price">${fmt(p.precio_venta)}</div>
                </div>`;
            });
            grid.innerHTML = html || '<div class="text-center p-4 text-muted">No se encontraron productos</div>';
        })
        .catch(() => { document.getElementById('products-grid').innerHTML = '<div class="text-center p-4 text-muted">Error al cargar productos</div>'; });
}

function addToCart(p) {
    const exists = cart.find(item => item.id == p.id);
    if (exists) { exists.quantity++; }
    else { cart.push({id: p.id, nombre: p.nombre, precio: parseFloat(p.precio_venta), quantity: 1}); }
    renderCart();
    updateTotal();
    showSuccessToast(p.nombre);
}

function renderCart() {
    const container = document.getElementById('ticket-items');
    if (cart.length === 0) {
        container.innerHTML = '<div class="empty-cart">Carrito vacío<br><small>Escanee o seleccione productos</small></div>';
        return;
    }
    let html = '';
    cart.forEach((item, idx) => {
        const lineTotal = item.precio * item.quantity;
        html += `<div class="pos-ticket-item" data-index="${idx}">
            <div class="pos-ticket-item-info">
                <span class="pos-item-name">${item.nombre}</span>
                <span class="pos-item-price">${fmt(lineTotal)}</span>
            </div>
            <div class="pos-ticket-item-controls">
                <button class="ticket-item-remove" aria-label="Eliminar ${item.nombre}" title="Eliminar">&times;</button>
                <div class="ticket-qty-group">
                    <button class="ticket-qty-minus" aria-label="Disminuir cantidad de ${item.nombre}" title="Disminuir">&minus;</button>
                    <span class="ticket-qty">${item.quantity}</span>
                    <button class="ticket-qty-plus" aria-label="Aumentar cantidad de ${item.nombre}" title="Aumentar">+</button>
                </div>
            </div>
        </div>`;
    });
    container.innerHTML = html;
}

function updateTotal() {
    const subtotal = cart.reduce((s, i) => s + i.precio * i.quantity, 0);
    const tax = IVA_ENABLED ? subtotal * IVA_RATE : 0;
    const total = subtotal + tax;
    document.getElementById('subtotal').textContent = fmt(subtotal);
    document.getElementById('tax').textContent = fmt(tax);
    document.getElementById('total-amount').textContent = fmt(total);
    const dolarRow = document.getElementById('dolar-total-row');
    if (EXCHANGE_RATE > 0 && getPayMethods().includes('dolar')) {
        dolarRow.style.display = '';
        document.getElementById('total-dolar').textContent = fmtUsd(total / EXCHANGE_RATE);
    } else {
        dolarRow.style.display = 'none';
    }
    document.querySelectorAll('[id^="btn-amount-"]').forEach(el => {
        if (el.id === 'btn-amount-dolar' && EXCHANGE_RATE > 0) {
            el.textContent = fmtUsd(total / EXCHANGE_RATE);
        } else {
            el.textContent = fmt(total);
        }
    });
}

function clearCart() {
    if (!cart.length) return;
    Swal.fire({
        title: '¿Limpiar carrito?', icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280', confirmButtonText: 'Sí, limpiar'
    }).then(r => {
        if (r.isConfirmed) { cart = []; renderCart(); updateTotal(); }
    });
}

function showSuccessToast(name) {
    Swal.fire({toast: true, position: 'top', icon: 'success', title: name, showConfirmButton: false, timer: 600});
}

function handlePayment(type) {
    if (!cart.length) { Swal.fire({icon: 'warning', title: 'Carrito vacío', confirmButtonColor: '#2563eb'}); return; }
    if (type === 'mixto') { openSplitModal(); return; }
    if (type === 'tarjeta') {
        submitSaleWithPayment(type, getTotal(), 0, 0, 0, 0, 0, 0, 0, 0);
        return;
    }
    openPaymentModal(type);
}

function getPaymentData(type, recibido) {
    const subtotal = cart.reduce((s, i) => s + i.precio * i.quantity, 0);
    const tax = IVA_ENABLED ? subtotal * IVA_RATE : 0;
    const total = Number((subtotal + tax).toFixed(2));
    const cambio = Number((recibido - total).toFixed(2));
    let data = {
        id_cliente: document.getElementById('id_cliente').value,
        subtotal: Number(subtotal.toFixed(2)),
        impuesto: Number(tax.toFixed(2)),
        total,
        efectivo_recibido: recibido,
        cambio: cambio >= 0 ? cambio : 0,
        auto_print: true,
        items: cart.map(i => ({id_producto: i.id, cantidad: i.quantity, precio_venta: i.precio, descuento: 0}))
    };
    if (type === 'efectivo') {
        data.metodo_pago = 'Efectivo';
        data.pago_efectivo = total;
        data.pago_tarjeta = 0;
        data.pago_dolar = 0;
        data.pago_dolar_equiv = 0;
        data.total_dolares = 0;
        data.tasa_cambio = 0;
    } else if (type === 'dolar') {
        const totalDolares = Number((total / EXCHANGE_RATE).toFixed(2));
        data.metodo_pago = 'Dólar';
        data.pago_efectivo = 0;
        data.pago_tarjeta = 0;
        data.pago_dolar = total;
        data.pago_dolar_equiv = totalDolares;
        data.total_dolares = totalDolares;
        data.tasa_cambio = EXCHANGE_RATE;
    }
    return data;
}

function submitSaleWithPayment(type, total, pagoEfectivo, pagoTarjeta, pagoDolar, pagoDolarEquiv, totalDolares, tasaCambio, efectivoRecibido, cambio) {
    const subtotal = cart.reduce((s, i) => s + i.precio * i.quantity, 0);
    const tax = IVA_ENABLED ? subtotal * IVA_RATE : 0;
    submitSale({
        id_cliente: document.getElementById('id_cliente').value,
        metodo_pago: type === 'tarjeta' ? 'Tarjeta' : type,
        subtotal: Number(subtotal.toFixed(2)),
        impuesto: Number(tax.toFixed(2)),
        total,
        pago_efectivo: pagoEfectivo,
        pago_tarjeta: pagoTarjeta,
        pago_dolar: pagoDolar,
        pago_dolar_equiv: pagoDolarEquiv,
        total_dolares: totalDolares,
        tasa_cambio: tasaCambio,
        efectivo_recibido: efectivoRecibido,
        cambio: cambio,
        auto_print: true,
        items: cart.map(i => ({id_producto: i.id, cantidad: i.quantity, precio_venta: i.precio, descuento: 0}))
    });
}

function openPaymentModal(method) {
    pendingPaymentMethod = method;
    const total = getTotal();
    document.getElementById('paymentConfirmTitle').textContent = method === 'dolar' ? 'Cobrar Dólar' : 'Cobrar Efectivo';
    document.getElementById('confirm-total-display').textContent = fmt(total);
    const usdRow = document.getElementById('confirm-total-usd-row');
    if (method === 'dolar' && EXCHANGE_RATE > 0) {
        usdRow.style.display = '';
        document.getElementById('confirm-total-usd').textContent = fmtUsd(total / EXCHANGE_RATE);
        document.getElementById('label-monto-recibido').textContent = 'Monto recibido ($)';
        document.getElementById('monto-recibido-prefix').textContent = '$';
    } else {
        usdRow.style.display = 'none';
        document.getElementById('label-monto-recibido').textContent = 'Monto recibido (C$)';
        document.getElementById('monto-recibido-prefix').textContent = 'C$';
    }
    document.getElementById('monto-recibido').value = '';
    document.getElementById('confirm-cambio').textContent = fmt(0);
    document.getElementById('confirm-error').style.display = 'none';
    new bootstrap.Modal(document.getElementById('paymentConfirmModal')).show();
    document.getElementById('monto-recibido').focus();
}

function calculateConfirmChange() {
    const method = pendingPaymentMethod;
    if (!method) return;
    const total = getTotal();
    const raw = parseFloat(document.getElementById('monto-recibido').value) || 0;
    let recibidoEnCordobas = raw;
    if (method === 'dolar' && EXCHANGE_RATE > 0) {
        recibidoEnCordobas = raw * EXCHANGE_RATE;
    }
    const cambio = Number((recibidoEnCordobas - total).toFixed(2));
    const errEl = document.getElementById('confirm-error');
    if (cambio < 0) {
        errEl.style.display = '';
        document.getElementById('confirm-cambio').textContent = fmt(0);
    } else {
        errEl.style.display = 'none';
        document.getElementById('confirm-cambio').textContent = fmt(cambio);
    }
}

function confirmPayment() {
    const method = pendingPaymentMethod;
    if (!method) return;
    const total = getTotal();
    const raw = parseFloat(document.getElementById('monto-recibido').value) || 0;
    let recibidoEnCordobas = raw;
    if (method === 'dolar' && EXCHANGE_RATE > 0) {
        recibidoEnCordobas = raw * EXCHANGE_RATE;
    }
    if (recibidoEnCordobas < total) {
        Swal.fire('Error', 'El monto recibido debe ser igual o mayor al total.', 'error');
        return;
    }
    const data = getPaymentData(method, recibidoEnCordobas);
    bootstrap.Modal.getInstance(document.getElementById('paymentConfirmModal')).hide();
    submitSale(data);
}

function openSplitModal() {
    const total = getTotal();
    document.getElementById('split-total-display').textContent = fmt(total);
    if (EXCHANGE_RATE > 0) {
        document.getElementById('split-total-usd-display').textContent = '≈ ' + fmtUsd(total / EXCHANGE_RATE) + ' USD';
    }
    document.getElementById('split-recibido').value = '';
    document.getElementById('split-tarjeta').value = '';
    document.getElementById('split-dolar').value = '';
    document.getElementById('split-dolar-equiv').textContent = 'Equiv: ' + fmt(0);
    document.getElementById('split-aplicado-efectivo').textContent = fmt(0);
    document.getElementById('split-restante').textContent = fmt(total);
    document.getElementById('split-cambio').textContent = fmt(0);
    document.getElementById('split-error').style.display = 'none';
    document.getElementById('split-success').style.display = 'none';
    new bootstrap.Modal(document.getElementById('splitPaymentModal')).show();
}

function getTotal() {
    const s = cart.reduce((sum, i) => sum + i.precio * i.quantity, 0);
    return Number((s + (IVA_ENABLED ? s * IVA_RATE : 0)).toFixed(2));
}

function validateSplit() {
    const total = getTotal();
    const recibido = parseFloat(document.getElementById('split-recibido').value) || 0;
    const tarjeta = parseFloat(document.getElementById('split-tarjeta').value) || 0;
    const dolar = parseFloat(document.getElementById('split-dolar').value) || 0;
    
    const dolarEquiv = EXCHANGE_RATE > 0 ? Number((dolar * EXCHANGE_RATE).toFixed(2)) : 0;
    
    // Primero, cubrimos el total con tarjeta y dólares
    let restantePrevio = total - tarjeta - dolarEquiv;
    // El efectivo aplicado es lo que falta por cubrir, limitado por lo que recibimos
    let efectivoAplicado = Math.min(recibido, Math.max(restantePrevio, 0));
    
    const aplicadoTotal = Number((efectivoAplicado + tarjeta + dolarEquiv).toFixed(2));
    
    let restante = total - aplicadoTotal;
    // El cambio es todo el excedente del dinero físico entregado (efectivo y dólares)
    let totalFisicoEntregado = recibido + dolarEquiv;
    let efectivoNecesarioFisico = Math.max(total - tarjeta, 0);
    let cambio = totalFisicoEntregado > efectivoNecesarioFisico ? totalFisicoEntregado - efectivoNecesarioFisico : 0;

    document.getElementById('split-aplicado-efectivo').textContent = fmt(efectivoAplicado);
    document.getElementById('split-restante').textContent = fmt(Math.max(restante, 0));
    document.getElementById('split-cambio').textContent = fmt(cambio);

    const err = document.getElementById('split-error'), ok = document.getElementById('split-success');
    if (recibido === 0 && tarjeta === 0 && dolar === 0) { err.style.display = 'none'; ok.style.display = 'none'; return; }
    if (restante > 0.01) { err.style.display = ''; ok.style.display = 'none'; }
    else { err.style.display = 'none'; ok.style.display = ''; }
}

function confirmSplitPayment() {
    if (!validateSplitBeforeSubmit()) return;
    const total = getTotal();
    const recibido = parseFloat(document.getElementById('split-recibido').value) || 0;
    const tarjeta = parseFloat(document.getElementById('split-tarjeta').value) || 0;
    const dolar = parseFloat(document.getElementById('split-dolar').value) || 0;
    
    const dolarEquiv = EXCHANGE_RATE > 0 ? Number((dolar * EXCHANGE_RATE).toFixed(2)) : 0;
    const totalDolares = EXCHANGE_RATE > 0 ? Number((dolarEquiv / EXCHANGE_RATE).toFixed(2)) : 0;
    
    let restantePrevio = total - tarjeta - dolarEquiv;
    let efectivoAplicado = Math.min(recibido, Math.max(restantePrevio, 0));
    
    let totalFisicoEntregado = recibido + dolarEquiv;
    let efectivoNecesarioFisico = Math.max(total - tarjeta, 0);
    let cambio = totalFisicoEntregado > efectivoNecesarioFisico ? totalFisicoEntregado - efectivoNecesarioFisico : 0;
    
    bootstrap.Modal.getInstance(document.getElementById('splitPaymentModal')).hide();
    const s = cart.reduce((sum, i) => sum + i.precio * i.quantity, 0);
    const tax = IVA_ENABLED ? s * IVA_RATE : 0;
    submitSale({
        id_cliente: document.getElementById('id_cliente').value, metodo_pago: 'Mixto',
        subtotal: Number(s.toFixed(2)), impuesto: Number(tax.toFixed(2)), total,
        pago_efectivo: Number(efectivoAplicado.toFixed(2)), pago_tarjeta: Number(tarjeta.toFixed(2)),
        pago_dolar: Number(dolarEquiv.toFixed(2)), pago_dolar_equiv: Number(dolarEquiv.toFixed(2)),
        total_dolares: totalDolares, tasa_cambio: EXCHANGE_RATE,
        efectivo_recibido: recibido, cambio: Number(cambio.toFixed(2)),
        auto_print: true,
        items: cart.map(i => ({id_producto: i.id, cantidad: i.quantity, precio_venta: i.precio, descuento: 0}))
    });
}

function validateSplitBeforeSubmit() {
    const total = getTotal();
    const recibido = parseFloat(document.getElementById('split-recibido').value) || 0;
    const tarjeta = parseFloat(document.getElementById('split-tarjeta').value) || 0;
    const dolar = parseFloat(document.getElementById('split-dolar').value) || 0;
    
    const dolarEquiv = EXCHANGE_RATE > 0 ? Number((dolar * EXCHANGE_RATE).toFixed(2)) : 0;
    const totalAportado = recibido + tarjeta + dolarEquiv;
    
    if (totalAportado < total - 0.01) {
        Swal.fire('Error', 'El monto recibido no es suficiente para cubrir el total.', 'error');
        return false;
    }
    return true;
}

function submitSale(data) {
    data.csrf_token = POSVENTA_CONFIG.CSRF_TOKEN;
    data.numero_factura = document.querySelector('.pos-invoice').textContent.replace('#', '');
    const btn = document.querySelector('[data-method].pos-btn-pay') || document.getElementById('payment-buttons').querySelector('button');
    const orig = btn ? btn.innerHTML : '';
    if (btn) { btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Procesando...'; btn.disabled = true; }
    fetch(POSVENTA_CONFIG.URLROOT + '/pos/save', {method: 'POST', body: JSON.stringify(data), headers: {'Content-Type': 'application/json'}})
        .then(r => r.json())
        .then(r => {
            if (btn) { btn.innerHTML = orig; btn.disabled = false; }
            if (r.status === 'success') {
                cart = []; renderCart(); updateTotal();
                document.getElementById('search-input').focus();
                if (r.invoiceNumber) document.querySelector('.pos-invoice').textContent = '#' + r.invoiceNumber;
                const iframe = document.getElementById('iframeTicket');
                let root = window.location.pathname.replace(/\/pos\/?$/, '');
                iframe.src = window.location.origin + root + '/sales/invoice/' + r.sale_id;
                new bootstrap.Modal(document.getElementById('previewTicketModal')).show();
            } else {
                Swal.fire('Error', r.message || 'No se pudo procesar la venta', 'error');
            }
        })
        .catch(() => {
            if (btn) { btn.innerHTML = orig; btn.disabled = false; }
            Swal.fire('Error', 'Error de red', 'error');
        });
}

function printLastReceipt() {
    fetch(POSVENTA_CONFIG.URLROOT + '/pos/printLastReceipt', {
        method: 'POST', headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({csrf_token: POSVENTA_CONFIG.CSRF_TOKEN})
    }).then(r => r.json()).then(r => {
        if (r.success) Swal.fire({toast: true, position: 'top-end', icon: 'success', title: 'Ticket impreso', showConfirmButton: false, timer: 1500});
    });
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'F5' || e.key === 'f5') { e.preventDefault(); document.getElementById('search-input').focus(); }
    if (e.key === 'F12') { e.preventDefault(); handlePayment('efectivo'); }
    if (e.key === 'Escape') { clearCart(); }
});
