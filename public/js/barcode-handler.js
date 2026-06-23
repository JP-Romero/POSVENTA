// Barcode Handler - POSVENTA
// Utilidad para manejo de scanners HID (actúan como teclado)

class BarcodeHandler {
    constructor(options = {}) {
        this.inputSelector = options.inputSelector || '#search-input, #codigo_barras, [data-barcode-input]';
        this.callback = options.callback || null;
        this.minLength = options.minLength || 3;
        this.timeWindow = options.timeWindow || 200; // ms entre caracteres
        this.buffer = '';
        this.lastKeyTime = 0;
        this.isScanning = false;
        this.targetInput = null;
        
        this.init();
    }
    
    init() {
        document.addEventListener('keydown', this.onKeyDown.bind(this), true);
        
        // Auto-focus en inputs de código de barras
        document.querySelectorAll(this.inputSelector).forEach(input => {
            input.addEventListener('focus', () => this.targetInput = input);
            input.addEventListener('blur', () => {
                if (this.targetInput === input) this.targetInput = null;
            });
        });
    }
    
    onKeyDown(e) {
        // Ignorar teclas de modificación
        if (e.ctrlKey || e.altKey || e.metaKey) return;
        
        const now = Date.now();
        const isEnter = e.key === 'Enter';
        const isTab = e.key === 'Tab';
        const isPrintable = e.key.length === 1;
        
        // Detectar inicio de escaneo (primer caracter printable después de pausa)
        if (isPrintable) {
            if (now - this.lastKeyTime > this.timeWindow) {
                this.buffer = '';
                this.isScanning = true;
            }
            this.buffer += e.key;
            this.lastKeyTime = now;
        }
        
        // Fin de escaneo: Enter o Tab
        if ((isEnter || isTab) && this.isScanning && this.buffer.length >= this.minLength) {
            e.preventDefault();
            e.stopPropagation();
            
            const code = this.buffer.trim();
            this.buffer = '';
            this.isScanning = false;
            
            // Validar formato básico (EAN13, Code128, etc.)
            if (this.isValidBarcode(code)) {
                this.handleScan(code);
            }
        }
        
        // Si no es printable ni enter/tab, resetear
        if (!isPrintable && !isEnter && !isTab) {
            this.buffer = '';
            this.isScanning = false;
        }
    }
    
    isValidBarcode(code) {
        // EAN-13: 13 dígitos
        // EAN-8: 8 dígitos
        // UPC-A: 12 dígitos
        // Code128: alfanumérico, longitud variable
        // Code39: alfanumérico con * inicio/fin
        return /^[\d\w\-\*]{3,}$/.test(code);
    }
    
    handleScan(code) {
        // Si hay callback personalizado, usarlo
        if (this.callback) {
            this.callback(code, this.targetInput);
            return;
        }
        
        // Comportamiento por defecto: llenar input enfocado
        if (this.targetInput) {
            this.targetInput.value = code;
            this.targetInput.dispatchEvent(new Event('input', { bubbles: true }));
            this.targetInput.dispatchEvent(new Event('change', { bubbles: true }));
            
            // Trigger búsqueda si es el input del POS
            if (this.targetInput.id === 'search-input') {
                this.targetInput.dispatchEvent(new Event('keydown', { bubbles: true, key: 'Enter' }));
            }
        }
    }
    
    // Método estático para inicialización rápida
    static init(options) {
        return new BarcodeHandler(options);
    }
    
    // Simular escaneo para testing
    static simulate(code, inputSelector = '#search-input') {
        const input = document.querySelector(inputSelector);
        if (input) {
            input.value = code;
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }
}

// Auto-inicializar si hay inputs con data-barcode-input
document.addEventListener('DOMContentLoaded', () => {
    const inputs = document.querySelectorAll('[data-barcode-input], #search-input, #codigo_barras');
    if (inputs.length > 0) {
        window.barcodeHandler = new BarcodeHandler();
    }
});

// Exportar para uso en módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = BarcodeHandler;
}