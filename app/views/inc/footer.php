</div>
    </main>

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
</body>
</html>