<?php $currentPage = 'pos'; require APPROOT . '/views/inc/header.php'; ?>

<link rel="stylesheet" href="<?= URLROOT ?>/css/pos-v2.css">

<div class="pos-v2-app">
    <header class="pos-header">
        <h1 class="visually-hidden">Punto de Venta</h1>
        <div class="pos-header-brand">
            <i class="fas fa-store"></i>
            <span>POSVENTA LIBRERÍA</span>
        </div>
        <div class="pos-header-info">
            <span class="pos-user"><i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['user_nombre'] ?? 'Usuario', ENT_QUOTES, 'UTF-8') ?></span>
            <span class="pos-invoice">#<?= $data['invoiceNumber'] ?></span>
        </div>
        <div class="pos-header-shortcuts">
            <kbd>F5</kbd> Buscar | <kbd>F12</kbd> Cobrar | <kbd>Esc</kbd> Cancelar
        </div>
    </header>

    <div class="pos-container">
        <aside class="pos-categories" aria-label="Categorías">
            <button class="pos-cat-btn active" data-category="all" title="Todo">
                <i class="fas fa-th-large"></i>
                <span>TODO</span>
            </button>
            <?php foreach($data['categories'] as $cat): ?>
            <button class="pos-cat-btn" data-category="<?= $cat->id ?>" title="<?= h($cat->nombre) ?>">
                <i class="fas fa-folder"></i>
                <span><?= h($cat->nombre) ?></span>
            </button>
            <?php endforeach; ?>
            <button class="pos-cat-btn" data-category="ofertas" title="Ofertas">
                <i class="fas fa-tags"></i>
                <span>OFERTAS</span>
            </button>
            <button class="pos-cat-btn" data-category="mas-vendidos" title="Más vendidos">
                <i class="fas fa-fire"></i>
                <span>HOT</span>
            </button>
        </aside>

        <div class="pos-products">
            <div class="pos-search-bar">
                <i class="fas fa-search pos-search-bar-icon"></i>
                <input type="text" id="search-input" class="pos-search-bar-input" placeholder="Buscar producto por nombre, código o ISBN..." autocomplete="off">
            </div>
            <div class="pos-products-grid" id="products-grid">
            </div>
        </div>

        <aside class="pos-ticket" aria-label="Ticket de venta">
            <div class="pos-ticket-header">
                <i class="fas fa-receipt"></i>
                <span>TICKET</span>
            </div>
            
            <div class="pos-ticket-items" id="ticket-items">
                <div class="empty-cart">Carrito vacío</div>
            </div>

            <div class="pos-ticket-footer">
                <div id="dolar-total-row" class="summary-row" style="display:none;font-size:0.8rem;color:var(--gray);padding-bottom:4px;">
                    <span>Total en USD</span>
                    <span id="total-dolar">$0.00</span>
                </div>
                <div class="pos-ticket-summary">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="subtotal">C$0.00</span>
                    </div>
                    <div class="summary-row" id="tax-row" style="display: <?= ($data['iva_enabled'] ?? 1) ? '' : 'none' ?>">
                        <span>IVA (<?= $data['iva'] ?? 15 ?>%)</span>
                        <span id="tax">C$0.00</span>
                    </div>
                    <div class="summary-row total-row">
                        <span>TOTAL</span>
                        <span class="total-amount" id="total-amount">C$0.00</span>
                    </div>
                </div>
                
                <div class="pos-client-row">
                    <select id="id_cliente" class="pos-client-select" aria-label="Seleccionar cliente">
                        <?php foreach($data['clients'] as $client) : ?>
                            <option value="<?= $client->id ?>"><?= $client->nombre ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="pos-btn-add-client" id="btn-add-client" title="Nuevo Cliente" aria-label="Agregar nuevo cliente">
                        <i class="fas fa-user-plus"></i>
                    </button>
                </div>

                <div class="pos-payment-buttons" id="payment-buttons">
                </div>

                <div class="pos-actions-bottom">
                    <button class="pos-btn-action" id="btn-reimprimir" aria-label="Reimprimir último ticket" title="Reimprimir">
                        <i class="fas fa-print" aria-hidden="true"></i>
                    </button>
                    <button class="pos-btn-action" id="btn-cancelar" aria-label="Cancelar venta y vaciar carrito" title="Cancelar">
                        <i class="fas fa-trash" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </aside>
    </div>
</div>

<!-- Modal Apertura de Caja -->
<div class="modal fade" id="aperturaCajaModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formAperturaCaja">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fa fa-cash-register me-2"></i> Apertura de Caja</h5>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted mb-4">Para iniciar el turno y realizar ventas, ingresa el monto del Fondo Inicial (efectivo base en caja).</p>
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Monto Inicial:</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text">C$</span>
                            <input type="number" class="form-control" name="monto" id="monto_apertura" step="0.01" min="0" required placeholder="0.00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="submit" class="btn btn-primary w-100" id="btn-abrir-caja">
                        <i class="fa fa-check-circle me-2"></i> Abrir Turno
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Nuevo Cliente Rápido -->
<div class="modal fade" id="nuevoClienteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fa fa-user-plus me-2"></i> Nuevo Cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formNuevoCliente">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre <sup>*</sup></label>
                        <input type="text" class="form-control" name="nombre" id="input-nuevo-cliente-nombre" required placeholder="Nombre del cliente">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" class="form-control" name="telefono" placeholder="Número de teléfono">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" name="correo" placeholder="correo@ejemplo.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <textarea class="form-control" name="direccion" rows="2" placeholder="Dirección"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btn-guardar-cliente-rapido">
                        <i class="fa fa-save me-1"></i> Guardar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Preview Ticket -->
<div class="modal fade" id="previewTicketModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white border-0">
                <h5 class="modal-title"><i class="fa fa-receipt me-2"></i> Ticket de Venta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="height: 400px;">
                <iframe id="iframeTicket" src="" style="width:100%; height:100%; border:none;"></iframe>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('iframeTicket').contentWindow.print()">
                    <i class="fa fa-print me-2"></i> Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Split Payment Modal -->
<div class="modal fade" id="splitPaymentModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fa fa-split me-2"></i> Pago Mixto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Total a pagar:</label>
                    <div class="h4 mb-0" id="split-total-display">C$0.00</div>
                    <small class="text-muted" id="split-total-usd-display">≈ $0.00 USD</small>
                </div>
                <hr>
                
                <div class="mb-3">
                    <label class="form-label" for="split-recibido">Monto recibido en efectivo (C$)</label>
                    <div class="input-group">
                        <span class="input-group-text">C$</span>
                        <input type="number" class="form-control" id="split-recibido" step="0.01" min="0" placeholder="Ej. 200.00">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="split-tarjeta">Monto en tarjeta (C$)</label>
                    <div class="input-group">
                        <span class="input-group-text">C$</span>
                        <input type="number" class="form-control" id="split-tarjeta" step="0.01" min="0" placeholder="Ej. 309.00">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="split-dolar">Monto recibido en Dólares ($)</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" id="split-dolar" step="0.01" min="0" placeholder="0.00">
                    </div>
                    <div class="form-text" id="split-dolar-equiv">Equiv: C$0.00</div>
                </div>

                <div class="mt-3 p-3 bg-light rounded border">
                    <p class="mb-1 d-flex justify-content-between"><span>Efectivo aplicado:</span> <strong id="split-aplicado-efectivo">C$0.00</strong></p>
                    <p class="mb-1 d-flex justify-content-between"><span>Restante por cubrir:</span> <strong id="split-restante">C$0.00</strong></p>
                    <p class="mb-0 text-success d-flex justify-content-between"><span>Cambio a entregar:</span> <strong id="split-cambio">C$0.00</strong></p>
                </div>
                
                <div id="split-error" class="text-danger small mt-2" style="display:none;">El monto recibido no es suficiente para cubrir el total.</div>
                <div id="split-success" class="text-success small mt-2" style="display:none;">Montos correctos, listo para cobrar.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-confirm-split">
                    <i class="fa fa-check me-1"></i> Confirmar Pago
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Confirmation Modal -->
<div class="modal fade" id="paymentConfirmModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fa fa-money-bill-wave me-2"></i> <span id="paymentConfirmTitle">Cobrar Efectivo</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Total a pagar:</label>
                    <div class="h4 mb-0" id="confirm-total-display">C$0.00</div>
                    <div id="confirm-total-usd-row" class="text-muted small" style="display:none;">
                        Total USD: <span id="confirm-total-usd">$0.00</span>
                    </div>
                </div>
                <hr>
                <div class="mb-3">
                    <label class="form-label fw-bold" id="label-monto-recibido">Monto recibido (C$)</label>
                    <div class="input-group">
                        <span class="input-group-text" id="monto-recibido-prefix">C$</span>
                        <input type="number" class="form-control" id="monto-recibido" step="0.01" min="0" placeholder="0.00" aria-label="Monto recibido">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Cambio a entregar:</label>
                    <div class="h3 text-success mb-0" id="confirm-cambio">C$0.00</div>
                </div>
                <div id="confirm-error" class="text-danger small" style="display:none;">El monto recibido debe ser igual o mayor al total.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-confirm-payment">
                    <i class="fa fa-check me-1"></i> Confirmar Pago
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const POSVENTA_CONFIG = {
    URLROOT: '<?= URLROOT ?>',
    CURRENCY_SYMBOL: 'C$',
    IVA_RATE: <?= ($data['iva'] ?? 15) / 100 ?>,
    IVA_ENABLED: <?= ($data['iva_enabled'] ?? 1) ? 'true' : 'false' ?>,
    EXCHANGE_RATE: <?= (float)($data['exchange_rate'] ?? 0) ?>,
    PAYMENT_METHODS: '<?= addslashes($data['payment_methods'] ?? 'efectivo,tarjeta') ?>',
    CSRF_TOKEN: '<?= generateCsrfToken() ?>',
    cajaAbierta: <?= isset($data['cajaAbierta']) && $data['cajaAbierta'] ? 'true' : 'false' ?>
};
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= URLROOT ?>/js/pos-v2.js?v=<?= time() + 2 ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnAddClient = document.getElementById('btn-add-client');
    const modalNuevoCliente = new bootstrap.Modal(document.getElementById('nuevoClienteModal'));
    const formNuevoCliente = document.getElementById('formNuevoCliente');
    const selectCliente = document.getElementById('id_cliente');
    const btnGuardar = document.getElementById('btn-guardar-cliente-rapido');

    btnAddClient.addEventListener('click', function() {
        document.getElementById('input-nuevo-cliente-nombre').value = '';
        formNuevoCliente.querySelectorAll('input, textarea').forEach(el => {
            if (el.name !== 'nombre') el.value = '';
        });
        modalNuevoCliente.show();
        document.getElementById('input-nuevo-cliente-nombre').focus();
    });

    formNuevoCliente.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Guardando...';

        fetch(POSVENTA_CONFIG.URLROOT + '/clients/quickAdd', {
            method: 'POST',
            body: JSON.stringify(data),
            headers: { 'Content-Type': 'application/json' }
        })
        .then(res => res.json())
        .then(res => {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = '<i class="fa fa-save me-1"></i> Guardar Cliente';

            if (res.success) {
                const opt = document.createElement('option');
                opt.value = res.client.id;
                opt.textContent = res.client.nombre;
                opt.selected = true;
                selectCliente.appendChild(opt);
                modalNuevoCliente.hide();
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: 'success',
                    title: 'Cliente creado',
                    showConfirmButton: false,
                    timer: 1000
                });
            } else {
                Swal.fire('Error', res.message || 'No se pudo crear el cliente', 'error');
            }
        })
        .catch(err => {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = '<i class="fa fa-save me-1"></i> Guardar Cliente';
            Swal.fire('Error', 'Error de red', 'error');
        });
    });
});
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>