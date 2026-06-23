<?php
/**
 * ReceiptPrinter - Wrapper para impresoras térmicas ESC/POS
 * Requiere: mike42/escpos-php (composer require mike42/escpos-php)
 */
namespace App\Lib;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\Printer as EscposPrinter;

class ReceiptPrinter
{
    private $connector = null;
    private $printer = null;
    private $profile = null;
    private $config = [];
    private $paperWidth = 58; // mm
    
    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'type' => 'file', // file, network, windows
            'path' => '',     // ruta archivo (USB), IP:Puerto (red), nombre impresora (Windows)
            'paper_width' => 58,
            'encoding' => 'CP858',
        ], $config);
        
        $this->paperWidth = (int)$this->config['paper_width'];
        $this->profile = CapabilityProfile::load('simple');
    }
    
    /**
     * Crear conector según tipo
     */
    private function createConnector()
    {
        switch ($this->config['type']) {
            case 'network':
                if (empty($this->config['path'])) {
                    throw new \Exception('IP:Puerto requerido para impresora de red');
                }
                $parts = explode(':', $this->config['path']);
                $ip = $parts[0];
                $port = isset($parts[1]) ? (int)$parts[1] : 9100;
                return new NetworkPrintConnector($ip, $port);
                
            case 'windows':
                if (empty($this->config['path'])) {
                    throw new \Exception('Nombre de impresora requerido para Windows');
                }
                return new WindowsPrintConnector($this->config['path']);
                
            case 'file':
            default:
                // Para USB en Linux: /dev/usb/lp0
                // Para archivo: /tmp/receipt.txt
                $path = $this->config['path'] ?: '/tmp/posventa_receipt_' . date('Ymd_His') . '.txt';
                return new FilePrintConnector($path);
        }
    }
    
    /**
     * Inicializar impresora
     */
    public function init()
    {
        try {
            $this->connector = $this->createConnector();
            $this->printer = new EscposPrinter($this->connector, $this->profile);
            return true;
        } catch (\Exception $e) {
            error_log('ReceiptPrinter init error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cerrar conexión
     */
    public function close()
    {
        if ($this->printer) {
            try {
                $this->printer->close();
            } catch (\Exception $e) {
                error_log('ReceiptPrinter close error: ' . $e->getMessage());
            }
            $this->printer = null;
        }
    }
    
    /**
     * Imprimir ticket de venta
     */
    public function printSaleReceipt(array $sale, array $items, array $settings = [])
    {
        if (!$this->init()) return false;
        
        try {
            $p = $this->printer;
            $width = $this->paperWidth;
            $cols = $width >= 80 ? 48 : 32;
            
            // --- Header ---
            $p->setJustification(Printer::JUSTIFY_CENTER);
            
            if (!empty($settings['logo'])) {
                // Logo opcional - requiere imagen procesada
                // $p->bitImage(...);
            }
            
            $p->selectPrintMode(Printer::MODE_FONT_A | Printer::MODE_DOUBLE_WIDTH | Printer::MODE_DOUBLE_HEIGHT);
            $p->text($settings['nombre_negocio'] ?? 'POSVENTA' . "\n");
            $p->selectPrintMode();
            
            $p->text($settings['direccion'] ?? '' . "\n");
            if (!empty($settings['telefono'])) {
                $p->text("Tel: {$settings['telefono']}\n");
            }
            if (!empty($settings['ruc'])) {
                $p->text("RUC: {$settings['ruc']}\n");
            }
            
            $p->feed(1);
            $p->text(str_repeat('-', $cols) . "\n");
            
            // --- Info Factura ---
            $p->setJustification(Printer::JUSTIFY_LEFT);
            $p->text("Factura: {$sale['numero_factura']}\n");
            $p->text("Fecha: " . date('d/m/Y H:i', strtotime($sale['fecha'])) . "\n");
            $p->text("Cajero: {$sale['usuario_nombre']}\n");
            $p->text("Cliente: {$sale['cliente_nombre']}\n");
            $p->text(str_repeat('-', $cols) . "\n");
            
            // --- Header Items ---
            $p->setJustification(Printer::JUSTIFY_LEFT);
            if ($width >= 80) {
                $p->text(sprintf("%-20s %4s %8s %8s\n", 'Producto', 'Cant', 'P.U.', 'Subtot'));
            } else {
                $p->text(sprintf("%-14s %3s %7s %7s\n", 'Prod', 'C', 'P.U.', 'Subt'));
            }
            $p->text(str_repeat('-', $cols) . "\n");
            
            // --- Items ---
            foreach ($items as $item) {
                $nombre = $item['producto_nombre'] ?? 'Producto';
                $cant = $item['cantidad'];
                $precio = $item['precio_venta'];
                $subtotal = $cant * $precio;
                $desc = $item['descuento'] ?? 0;
                if ($desc > 0) $subtotal -= $desc;
                
                if ($width >= 80) {
                    $p->text(sprintf("%-20s %4d %8.2f %8.2f\n", 
                        mb_substr($nombre, 0, 20), $cant, $precio, $subtotal));
                } else {
                    $p->text(sprintf("%-14s %3d %7.2f %7.2f\n", 
                        mb_substr($nombre, 0, 14), $cant, $precio, $subtotal));
                }
                
                // Descuento si aplica
                if ($desc > 0) {
                    $p->text(sprintf("%*s Dto: -%.2f\n", $cols - 8, '', $desc));
                }
            }
            
            $p->text(str_repeat('-', $cols) . "\n");
            
            // --- Totales ---
            $p->setJustification(Printer::JUSTIFY_RIGHT);
            $p->text(sprintf("Subtotal: %10.2f\n", $sale['subtotal']));
            if ($sale['impuesto'] > 0) {
                $iva = ($settings['iva'] ?? 15);
                $p->text(sprintf("IVA (%d%%): %10.2f\n", $iva, $sale['impuesto']));
            }
            if (($sale['descuento'] ?? 0) > 0) {
                $p->text(sprintf("Descuento: %8.2f\n", $sale['descuento']));
            }
            
            $p->selectPrintMode(Printer::MODE_FONT_A | Printer::MODE_DOUBLE_WIDTH | Printer::MODE_DOUBLE_HEIGHT);
            $p->text(sprintf("TOTAL: %10.2f\n", $sale['total']));
            $p->selectPrintMode();
            
            $p->text(str_repeat('=', $cols) . "\n");
            
            // --- Pago ---
            $p->setJustification(Printer::JUSTIFY_LEFT);
            $p->text("Pago: {$sale['metodo_pago']}\n");
            
            if ($sale['metodo_pago'] === 'Efectivo' && !empty($sale['efectivo_recibido'])) {
                $cambio = $sale['efectivo_recibido'] - $sale['total'];
                $p->text(sprintf("Recibido: %10.2f\n", $sale['efectivo_recibido']));
                $p->text(sprintf("Cambio: %11.2f\n", $cambio));
            }
            
            $p->feed(1);
            
            // --- Footer ---
            $p->setJustification(Printer::JUSTIFY_CENTER);
            $p->text("¡Gracias por su compra!\n");
            $p->text("Vuelva pronto\n");
            $p->feed(1);
            
            // QR Code opcional
            if (!empty($settings['qr_url'])) {
                $p->qrCode($settings['qr_url'], Printer::QR_ECLEVEL_L, 3);
                $p->feed(1);
            }
            
            // Corte
            $p->cut();
            
            return true;
        } catch (\Exception $e) {
            error_log('ReceiptPrinter printSaleReceipt error: ' . $e->getMessage());
            return false;
        } finally {
            $this->close();
        }
    }
    
    /**
     * Imprimir ticket de compra
     */
    public function printPurchaseReceipt(array $purchase, array $items, array $settings = [])
    {
        if (!$this->init()) return false;
        
        try {
            $p = $this->printer;
            $width = $this->paperWidth;
            $cols = $width >= 80 ? 48 : 32;
            
            $p->setJustification(Printer::JUSTIFY_CENTER);
            $p->selectPrintMode(Printer::MODE_FONT_A | Printer::MODE_DOUBLE_WIDTH | Printer::MODE_DOUBLE_HEIGHT);
            $p->text("COMPRA\n");
            $p->selectPrintMode();
            $p->text($settings['nombre_negocio'] ?? 'POSVENTA' . "\n");
            $p->feed(1);
            
            $p->setJustification(Printer::JUSTIFY_LEFT);
            $p->text("Proveedor: {$purchase['proveedor_nombre']}\n");
            $p->text("Fecha: " . date('d/m/Y H:i', strtotime($purchase['fecha'])) . "\n");
            $p->text("Comprobante: {$purchase['comprobante']}\n");
            $p->text(str_repeat('-', $cols) . "\n");
            
            // Items
            if ($width >= 80) {
                $p->text(sprintf("%-20s %4s %8s %8s\n", 'Producto', 'Cant', 'P.U.', 'Subtot'));
            } else {
                $p->text(sprintf("%-14s %3s %7s %7s\n", 'Prod', 'C', 'P.U.', 'Subt'));
            }
            $p->text(str_repeat('-', $cols) . "\n");
            
            foreach ($items as $item) {
                $nombre = $item['producto_nombre'] ?? 'Producto';
                $cant = $item['cantidad'];
                $precio = $item['precio_compra'];
                $subtotal = $cant * $precio;
                
                if ($width >= 80) {
                    $p->text(sprintf("%-20s %4d %8.2f %8.2f\n", 
                        mb_substr($nombre, 0, 20), $cant, $precio, $subtotal));
                } else {
                    $p->text(sprintf("%-14s %3d %7.2f %7.2f\n", 
                        mb_substr($nombre, 0, 14), $cant, $precio, $subtotal));
                }
            }
            
            $p->text(str_repeat('-', $cols) . "\n");
            $p->setJustification(Printer::JUSTIFY_RIGHT);
            $p->selectPrintMode(Printer::MODE_FONT_A | Printer::MODE_DOUBLE_WIDTH);
            $p->text(sprintf("TOTAL: %10.2f\n", $purchase['total']));
            $p->selectPrintMode();
            
            $p->feed(2);
            $p->cut();
            
            return true;
        } catch (\Exception $e) {
            error_log('ReceiptPrinter printPurchaseReceipt error: ' . $e->getMessage());
            return false;
        } finally {
            $this->close();
        }
    }
    
    /**
     * Test print
     */
    public function testPrint()
    {
        if (!$this->init()) return false;
        
        try {
            $p = $this->printer;
            $p->setJustification(Printer::JUSTIFY_CENTER);
            $p->selectPrintMode(Printer::MODE_DOUBLE_WIDTH | Printer::MODE_DOUBLE_HEIGHT);
            $p->text("POSVENTA\n");
            $p->selectPrintMode();
            $p->text("Test de impresion\n");
            $p->text(date('d/m/Y H:i:s') . "\n");
            $p->feed(2);
            $p->cut();
            return true;
        } catch (\Exception $e) {
            error_log('ReceiptPrinter testPrint error: ' . $e->getMessage());
            return false;
        } finally {
            $this->close();
        }
    }
    
    /**
     * Factory: crear desde configuración BD
     */
    public static function fromDatabase(array $printerConfig)
    {
        return new self([
            'type' => $printerConfig['tipo'] ?? 'file',
            'path' => $printerConfig['conexion'] ?? '',
            'paper_width' => $printerConfig['ancho_papel'] ?? 58,
        ]);
    }
}