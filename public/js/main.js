// Main JS file - POSVENTA
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide flash messages
    const flashMsg = document.getElementById('msg-flash');
    if (flashMsg) {
        setTimeout(() => {
            flashMsg.style.transition = 'opacity 0.5s';
            flashMsg.style.opacity = '0';
            setTimeout(() => flashMsg.remove(), 500);
        }, 5000);
    }
    
    // Confirm delete forms
    document.querySelectorAll('form[onclick*="confirm"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('¿Está seguro de realizar esta acción?')) {
                e.preventDefault();
            }
        });
    });
    
    // Initialize DataTables with Spanish language
    if (typeof $.fn.DataTable !== 'undefined') {
        $.extend(true, $.fn.DataTable.defaults, {
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
            },
            responsive: true,
            pageLength: 25
        });
    }
});

// Barcode scanner handler for HID devices
window.BarcodeScanner = {
    buffer: '',
    timeout: null,
    lastScan: 0,
    
    init: function(inputSelector, callback) {
        const input = document.querySelector(inputSelector);
        if (!input) return;
        
        input.addEventListener('keydown', function(e) {
            const now = Date.now();
            
            // Reset buffer if too much time passed (new scan)
            if (now - window.BarcodeScanner.lastScan > 200) {
                window.BarcodeScanner.buffer = '';
            }
            window.BarcodeScanner.lastScan = now;
            
            // Ignore modifier keys
            if (e.key.length > 1 && e.key !== 'Enter' && e.key !== 'Tab') return;
            
            if (e.key === 'Enter' || e.key === 'Tab') {
                e.preventDefault();
                const code = window.BarcodeScanner.buffer.trim();
                window.BarcodeScanner.buffer = '';
                if (code.length >= 3 && callback) {
                    callback(code);
                }
            } else {
                window.BarcodeScanner.buffer += e.key;
            }
        });
        
        // Focus input on page load for scanner
        setTimeout(() => input.focus(), 100);
    },
    
    // Simulate scan for testing
    simulate: function(code, inputSelector) {
        const input = document.querySelector(inputSelector);
        if (input) {
            input.value = code;
            input.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }
};