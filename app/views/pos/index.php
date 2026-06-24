<?php $currentPage = 'pos'; require APPROOT . '/views/inc/header.php'; ?>

<link rel="stylesheet" href="<?= URLROOT ?>/css/pos-v2.css">

<div class="pos-v2-app">
    <div class="pos-v2-container">
        <!-- Panel Categorías - Icon Only -->
        <div class="pos-panel pos-categories">
            <div class="pos-panel-header">
                <i class="fas fa-tags"></i>
            </div>
            <div class="pos-categories-list">
                <button class="pos-cat-btn active" data-category="all" title="Todo">
                    <i class="fas fa-th-large"></i>
                </button>
                <button class="pos-cat-btn" data-category="libros" title="Libros" style="color: #fbbf24;">
                    <i class="fas fa-book"></i>
                </button>
                <button class="pos-cat-btn" data-category="papel" title="Papelería" style="color: #60a5fa;">
                    <i class="fas fa-file-alt"></i>
                </button>
                <button class="pos-cat-btn" data-category="ofertas" title="Ofertas" style="color: #f87171;">
                    <i class="fas fa-tags"></i>
                </button>
                <button class="pos-cat-btn" data-category="mas-vendidos" title="Más vendidos" style="color: #c084fc;">
                    <i class="fas fa-fire"></i>
                </button>
            </div>
            
            <div class="pos-search-box">
                <i class="fas fa-search pos-search-icon"></i>
                <input type="text" id="search-input" class="pos-search-input" 
                       placeholder="ISBN...">
            </div>
        </div>

        <!-- Panel Productos -->
        <div class="pos-panel pos-products">
            <div class="pos-panel-header">
                <i class="fas fa-box"></i>
                <span>PRODUCTOS</span>
            </div>
            <div class="pos-products-grid" id="products-grid">
            </div>
        </div>

        <!-- Panel Ticket - Compact -->
        <div class="pos-panel pos-ticket">
            <div class="pos-ticket-header">
                <div>
                    <i class="fas fa-receipt"></i>
                    <span>TICKET</span>
                </div>
                <div class="pos-invoice">#<?= $data['invoiceNumber'] ?></div>
            </div>

            <div class="pos-ticket-items" id="ticket-items">
            </div>

            <div class="pos-ticket-footer">
                <div class="pos-ticket-total">
                    <span>TOTAL:</span>
                    <span class="total-amount" id="total-amount">$0.00</span>
                </div>
                
<select id="id_cliente" class="pos-client-select">
                    <?php foreach($data['clients'] as $client) : ?>
                        <option value="<?= $client->id ?>"><?= $client->nombre ?></option>
                    <?php endforeach; ?>
                </select>

                <button class="pos-btn-pay" id="complete-sale">
                    <span class="btn-amount">$0.00</span>
                    <span class="btn-text">COBRAR</span>
                </button>

                <div class="pos-actions-bottom">
                    <button class="pos-btn-action" onclick="printLastReceipt()" title="Reimprimir">
                        <i class="fas fa-print"></i>
                    </button>
                    <button class="pos-btn-action" onclick="clearCart()" title="Cancelar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= URLROOT ?>/js/pos-v2.js"></script>

<?php require APPROOT . '/views/inc/footer.php'; ?>