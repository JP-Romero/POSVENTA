</div>
    </main>

<!-- Confirm Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">¿Confirmar acción?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-4">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3 me-3">
                        <i class="fa fa-exclamation-triangle fa-lg"></i>
                    </div>
                    <p class="mb-0" id="confirmModalMessage">¿Está seguro de realizar esta acción?</p>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmModalYes">Sí, continuar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="<?php echo URLROOT; ?>/js/main.js"></script>
<script src="<?php echo URLROOT; ?>/js/barcode-handler.js"></script>
<script>
    lucide.createIcons();
    
    function updateClock() {
        const now = new Date();
        const hours = now.getHours();
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const seconds = now.getSeconds().toString().padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        const displayHours = hours % 12 || 12;
        const dateStr = now.toLocaleDateString('es-NI', { weekday: 'short', day: 'numeric', month: 'short' });
        const el = document.getElementById('clockDisplay');
        if (el) el.textContent = `${displayHours}:${minutes}:${seconds} ${ampm} - ${dateStr}`;
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>
<script>
// Modern confirm function using modal
window.showConfirm = function(message, callback) {
    var msgEl = document.getElementById('confirmModalMessage');
    var yesBtn = document.getElementById('confirmModalYes');
    var modalEl = document.getElementById('confirmModal');
    
    if (!msgEl || !yesBtn || !modalEl) {
        console.error('Modal elements not found');
        return;
    }
    
    msgEl.textContent = message;
    yesBtn.onclick = function() {
        callback(true);
        modalEl.classList.remove('show');
        modalEl.setAttribute('aria-hidden', 'true');
        modalEl.style.display = 'none';
        document.body.classList.remove('modal-open');
        var backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) backdrop.remove();
    };
    var modal = new bootstrap.Modal(modalEl);
    modal.show();
    return false;
};
</script>
</body>
</html>