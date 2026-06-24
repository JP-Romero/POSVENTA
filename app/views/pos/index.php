<?php $currentPage = 'pos'; require APPROOT . '/views/inc/header.php'; ?>

<div class="pos-app">
    <header class="pos-header">
        <div class="pos-brand">
            <i class="fas fa-cash-register"></i>
            <span>POSVENTA</span>
        </div>
        <div class="pos-time"><?php echo date('H:i'); ?></div>
        <div class="pos-user">
            <span><?php echo $_SESSION['user_nombre'] ?? 'Usuario'; ?></span>
            <i class="fas fa-caret-down"></i>
        </div>
    </header>

    <div class="pos-layout">
        <div class="pos-left">
            <div class="pos-search-section">
                <input type="text" id="search-input" class="pos-search-input" 
                       placeholder="Escanear código o buscar producto... (F5)" 
                       autocomplete="off">
                <button class="pos-btn-clear" onclick="clearSearch()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="pos-categories">
                <button class="pos-cat-btn active">Todo</button>
                <button class="pos-cat-btn">Comida</button>
                <button class="pos-cat-btn">Bebidas</button>
                <button class="pos-cat-btn">Postres</button>
            </div>

            <div class="pos-products">
                <div id="frequent-products" class="pos-products-grid"></div>
            </div>

            <div id="search-results" class="pos-search-dropdown"></div>
        </div>

        <div class="pos-center">
            <div class="pos-cart-wrapper">
                <table class="pos-cart-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cant</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
                <tbody id="cart-body">
                </tbody>
            </div>
        </div>

        <div class="pos-right">
            <div class="pos-summary">
                <div class="pos-summary-row">
                    <span>Cliente:</span>
                    <select id="id_cliente" class="pos-select">
                        <?php foreach($data['clients'] as $client) : ?>
                            <option value="<?= $client->id ?>"><?= $client->nombre ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="pos-summary-row">
                    <span>Factura:</span>
                    <span class="pos-invoice">#<?= $data['invoiceNumber'] ?></span>
                </div>

                <div class="pos-summary-row">
                    <span>Subtotal:</span>
                    <span id="summary-subtotal" class="amount">$0.00</span>
                </div>

                <div class="pos-summary-row">
                    <span>IVA (<?= $data['iva'] ?>%):</span>
                    <span id="summary-tax" class="amount">$0.00</span>
                </div>

                <div class="pos-summary-row discount-row">
                    <span>Descuento:</span>
                    <input type="number" id="discount-percent" class="pos-input-discount" 
                           value="0" min="0" max="100" step="0.5">
                    <span>%</span>
                </div>

                <div class="pos-divider"></div>

                <div class="pos-summary-total">
                    <span>TOTAL:</span>
                    <span id="summary-total" class="total-amount">$0.00</span>
                </div>

                <div class="pos-actions">
                    <div class="pos-payment-methods">
                        <button class="pos-pay-btn active" data-method="Efectivo">💵 Efectivo</button>
                        <button class="pos-pay-btn" data-method="Tarjeta">💳 Tarjeta</button>
                        <button class="pos-pay-btn" data-method="Transferencia">🏦 Transferencia</button>
                    </div>

                    <button id="complete-sale" class="pos-btn-finalizar">
                        <i class="fas fa-check-circle"></i> FINALIZAR VENTA (F12)
                    </button>

                    <button class="pos-btn-reimprimir" onclick="printLastReceipt()">
                        <i class="fa fa-print"></i> Reimprimir Último Ticket
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let cart = [];
const IVA_RATE = <?= $data['iva'] / 100 ?>;
let selectedPayment = 'Efectivo';

document.querySelectorAll('.pos-pay-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.pos-pay-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        selectedPayment = this.dataset.method;
    });
});

function clearSearch() {
    document.getElementById('search-input').value = '';
    document.getElementById('search-results').style.display = 'none';
}
</script>
<script src="<?= URLROOT ?>/js/pos-pos.js"></script>

<?php require APPROOT . '/views/inc/footer.php'; ?>