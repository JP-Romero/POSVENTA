<?php $currentPage = 'pos'; require APPROOT . '/views/inc/header.php'; ?>

<link rel="stylesheet" href="<?= URLROOT ?>/css/pos-v2.css">

<div class="pos-v2-app">
    <header class="pos-header">
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
        <aside class="pos-categories">
            <button class="pos-cat-btn active" data-category="all" title="Todo">
                <i class="fas fa-th-large"></i>
                <span>TODO</span>
            </button>
            <button class="pos-cat-btn" data-category="libros" title="Libros">
                <i class="fas fa-book"></i>
                <span>LIBROS</span>
            </button>
            <button class="pos-cat-btn" data-category="papel" title="Papelería">
                <i class="fas fa-file-alt"></i>
                <span>PAPEL</span>
            </button>
            <button class="pos-cat-btn" data-category="ofertas" title="Ofertas">
                <i class="fas fa-tags"></i>
                <span>OFERTAS</span>
            </button>
            <button class="pos-cat-btn" data-category="mas-vendidos" title="Más vendidos">
                <i class="fas fa-fire"></i>
                <span>HOT</span>
            </button>
            
            <div class="pos-search-box">
                <i class="fas fa-search pos-search-icon"></i>
                <input type="text" id="search-input" class="pos-search-input" placeholder="ISBN/Código...">
            </div>
        </aside>

        <main class="pos-products">
            <div class="pos-products-grid" id="products-grid">
            </div>
        </main>

        <aside class="pos-ticket">
            <div class="pos-ticket-header">
                <i class="fas fa-receipt"></i>
                <span>TICKET</span>
            </div>
            
            <div class="pos-ticket-items" id="ticket-items">
                <div class="empty-cart">Carrito vacío</div>
            </div>

            <div class="pos-ticket-footer">
                <div class="pos-ticket-summary">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="subtotal">$0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>IVA (15%)</span>
                        <span id="tax">$0.00</span>
                    </div>
                    <div class="summary-row total-row">
                        <span>TOTAL</span>
                        <span class="total-amount" id="total-amount">$0.00</span>
                    </div>
                </div>
                
                <select id="id_cliente" class="pos-client-select">
                    <?php foreach($data['clients'] as $client) : ?>
                        <option value="<?= $client->id ?>"><?= $client->nombre ?></option>
                    <?php endforeach; ?>
                </select>

                <button class="pos-btn-pay" id="complete-sale">
                    <span class="btn-amount">$0.00</span>
                    <span class="btn-text">COBRAR EFECTIVO</span>
                </button>

                <div class="pos-actions-bottom">
                    <button class="pos-btn-action" onclick="handlePayment('tarjeta')" title="Cobrar con tarjeta">
                        <i class="fas fa-credit-card"></i>
                    </button>
                    <button class="pos-btn-action" onclick="printLastReceipt()" title="Reimprimir">
                        <i class="fas fa-print"></i>
                    </button>
                    <button class="pos-btn-action" onclick="clearCart()" title="Cancelar">
                        <i class="fas fa-trash"></i>
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
                            <span class="input-group-text">$</span>
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

<!-- Modal Preview Ticket -->
<div class="modal fade" id="previewTicketModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white border-0">
                <h5 class="modal-title"><i class="fa fa-receipt me-2"></i> Ticket de Venta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="height: 400px;">
                <iframe id="iframeTicket" src="" style="width:100%; height:100%; border:none;"></iframe>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('iframeTicket').contentWindow.print()">
                    <i class="fa fa-print me-2"></i> Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const POSVENTA_CONFIG = {
    URLROOT: '<?= URLROOT ?>',
    IVA_RATE: <?= ($data['iva'] ?? 15) / 100 ?>,
    CSRF_TOKEN: '<?= generateCsrfToken() ?>',
    cajaAbierta: <?= isset($data['cajaAbierta']) && $data['cajaAbierta'] ? 'true' : 'false' ?>
};
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= URLROOT ?>/js/pos-v2.js"></script>

<?php require APPROOT . '/views/inc/footer.php'; ?>