<?php
/**
 * BarcodeGenerator - Generador de códigos de barras para etiquetas
 * Requiere: picqer/php-barcode-generator (composer require picqer/php-barcode-generator)
 */
namespace App\Lib;

use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;
use Picqer\Barcode\BarcodeGeneratorJPG;

class BarcodeGenerator
{
    private $generatorPNG;
    private $generatorSVG;
    private $generatorJPG;
    private $defaultType = 'C128'; // Code128 por defecto
    
    public function __construct()
    {
        $this->generatorPNG = new BarcodeGeneratorPNG();
        $this->generatorSVG = new BarcodeGeneratorSVG();
        $this->generatorJPG = new BarcodeGeneratorJPG();
    }
    
    /**
     * Generar código de barras como PNG (base64)
     */
    public function generatePNG(string $code, string $type = 'C128', int $width = 2, int $height = 50): string
    {
        try {
            switch (strtoupper($type)) {
                case 'EAN13':
                    if (strlen($code) !== 13) {
                        $code = $this->calculateEAN13Checksum($code);
                    }
                    return base64_encode($this->generatorPNG->getBarcode($code, $this->generatorPNG::TYPE_EAN_13, $width, $height));
                    
                case 'EAN8':
                    if (strlen($code) !== 8) {
                        $code = $this->calculateEAN8Checksum($code);
                    }
                    return base64_encode($this->generatorPNG->getBarcode($code, $this->generatorPNG::TYPE_EAN_8, $width, $height));
                    
                case 'UPCA':
                    if (strlen($code) !== 12) {
                        $code = $this->calculateUPCAChecksum($code);
                    }
                    return base64_encode($this->generatorPNG->getBarcode($code, $this->generatorPNG::TYPE_UPC_A, $width, $height));
                    
                case 'C39':
                case 'CODE39':
                    return base64_encode($this->generatorPNG->getBarcode($code, $this->generatorPNG::TYPE_CODE_39, $width, $height));
                    
                case 'C128':
                case 'CODE128':
                default:
                    return base64_encode($this->generatorPNG->getBarcode($code, $this->generatorPNG::TYPE_CODE_128, $width, $height));
            }
        } catch (\Exception $e) {
            error_log('BarcodeGenerator PNG error: ' . $e->getMessage());
            return '';
        }
    }
    
    /**
     * Generar código de barras como SVG
     */
    public function generateSVG(string $code, string $type = 'C128', int $width = 2, int $height = 50): string
    {
        try {
            switch (strtoupper($type)) {
                case 'EAN13':
                    if (strlen($code) !== 13) $code = $this->calculateEAN13Checksum($code);
                    return $this->generatorSVG->getBarcode($code, $this->generatorSVG::TYPE_EAN_13, $width, $height);
                case 'EAN8':
                    if (strlen($code) !== 8) $code = $this->calculateEAN8Checksum($code);
                    return $this->generatorSVG->getBarcode($code, $this->generatorSVG::TYPE_EAN_8, $width, $height);
                case 'UPCA':
                    if (strlen($code) !== 12) $code = $this->calculateUPCAChecksum($code);
                    return $this->generatorSVG->getBarcode($code, $this->generatorSVG::TYPE_UPC_A, $width, $height);
                case 'C39':
                case 'CODE39':
                    return $this->generatorSVG->getBarcode($code, $this->generatorSVG::TYPE_CODE_39, $width, $height);
                case 'C128':
                case 'CODE128':
                default:
                    return $this->generatorSVG->getBarcode($code, $this->generatorSVG::TYPE_CODE_128, $width, $height);
            }
        } catch (\Exception $e) {
            error_log('BarcodeGenerator SVG error: ' . $e->getMessage());
            return '';
        }
    }
    
    /**
     * Generar y guardar archivo PNG
     */
    public function savePNG(string $code, string $filepath, string $type = 'C128', int $width = 2, int $height = 50): bool
    {
        $png = $this->generatePNG($code, $type, $width, $height);
        if ($png) {
            $decoded = base64_decode($png);
            return file_put_contents($filepath, $decoded) !== false;
        }
        return false;
    }
    
    /**
     * Generar etiqueta completa para producto (imagen compuesta)
     */
    public function generateProductLabel(array $product, array $options = []): string
    {
        $opts = array_merge([
            'width' => 384, // px para 58mm @ 203dpi ~ 462px, usamos 384 para 80mm
            'height' => 240,
            'font_size' => 12,
            'show_price' => true,
            'show_name' => true,
            'show_code' => true,
            'barcode_type' => 'C128',
            'margin' => 10,
            'currency_symbol' => 'C$',
        ], $options);
        
        // Crear imagen base
        $img = imagecreatetruecolor($opts['width'], $opts['height']);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        $gray = imagecolorallocate($img, 100, 100, 100);
        
        imagefill($img, 0, 0, $white);
        
        $y = $opts['margin'];
        $centerX = $opts['width'] / 2;
        
        // Nombre del negocio (opcional)
        if (!empty($opts['business_name'])) {
            $this->drawTextCenter($img, $opts['business_name'], $centerX, $y, 14, $black, true);
            $y += 20;
        }
        
        // Nombre del producto
        if ($opts['show_name'] && !empty($product['nombre'])) {
            $name = $this->wrapText($product['nombre'], $opts['width'] - 2 * $opts['margin'], $opts['font_size']);
            foreach ($name as $line) {
                $this->drawTextCenter($img, $line, $centerX, $y, $opts['font_size'], $black);
                $y += $opts['font_size'] + 4;
            }
            $y += 5;
        }
        
        // Código de barras
        $barcodeImg = $this->generateBarcodeImage($product['codigo_barras'] ?? $product['codigo_interno'] ?? '', 
            $opts['barcode_type'], $opts['width'] - 40, 60);
        
        if ($barcodeImg) {
            $bcX = ($opts['width'] - imagesx($barcodeImg)) / 2;
            imagecopy($img, $barcodeImg, $bcX, $y, 0, 0, imagesx($barcodeImg), imagesy($barcodeImg));
            $y += imagesy($barcodeImg) + 5;
            imagedestroy($barcodeImg);
        }
        
        // Código legible debajo del barcode
        if ($opts['show_code']) {
            $code = $product['codigo_barras'] ?? $product['codigo_interno'] ?? '';
            $this->drawTextCenter($img, $code, $centerX, $y, 10, $gray);
            $y += 18;
        }
        
        // Precio
        if ($opts['show_price'] && isset($product['precio_venta'])) {
            $price = $opts['currency_symbol'] . ' ' . number_format($product['precio_venta'], 2, ',', '.');
            $this->drawTextCenter($img, $price, $centerX, $y, 18, $black, true);
        }
        
        // Guardar a base64
        ob_start();
        imagepng($img);
        $data = ob_get_clean();
        imagedestroy($img);
        
        return base64_encode($data);
    }
    
    /**
     * Generar lote de etiquetas (para impresión múltiple)
     */
    public function generateBatchLabels(array $products, array $options = []): array
    {
        $labels = [];
        foreach ($products as $product) {
            $labels[] = [
                'id' => $product['id'],
                'label' => $this->generateProductLabel($product, $options)
            ];
        }
        return $labels;
    }
    
    /**
     * Generar imagen de código de barras simple (resource GD)
     */
    private function generateBarcodeImage(string $code, string $type, int $width, int $height)
    {
        $png = $this->generatePNG($code, $type, 2, 50);
        if (!$png) return false;
        
        $decoded = base64_decode($png);
        $source = imagecreatefromstring($decoded);
        if (!$source) return false;
        
        // Redimensionar
        $dest = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($dest, 255, 255, 255);
        imagefill($dest, 0, 0, $white);
        imagecopyresampled($dest, $source, 0, 0, 0, 0, $width, $height, imagesx($source), imagesy($source));
        imagedestroy($source);
        
        return $dest;
    }
    
    /**
     * Dibujar texto centrado
     */
    private function drawTextCenter($img, string $text, int $x, int $y, int $size, $color, bool $bold = false)
    {
        // Usar fuente TTF si está disponible, sino built-in
        $font = $this->getFontPath($bold);
        if ($font && file_exists($font)) {
            $bbox = imagettfbbox($size, 0, $font, $text);
            $textWidth = $bbox[2] - $bbox[0];
            imagettftext($img, $size, 0, $x - $textWidth / 2, $y, $color, $font, $text);
        } else {
            // Fallback a fuente built-in
            $charWidth = imagefontwidth(5) * ($size / 10);
            $textWidth = strlen($text) * $charWidth;
            imagestring($img, 5, $x - $textWidth / 2, $y, $text, $color);
        }
    }
    
    /**
     * Envolver texto largo
     */
    private function wrapText(string $text, int $maxWidth, int $fontSize): array
    {
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';
        
        foreach ($words as $word) {
            $testLine = $currentLine . ($currentLine ? ' ' : '') . $word;
            // Estimación simple
            $charWidth = imagefontwidth(5) * ($fontSize / 10);
            if (strlen($testLine) * $charWidth > $maxWidth) {
                $lines[] = $currentLine;
                $currentLine = $word;
            } else {
                $currentLine = $testLine;
            }
        }
        if ($currentLine) $lines[] = $currentLine;
        
        return $lines ?: [$text];
    }
    
    /**
     * Obtener ruta de fuente
     */
    private function getFontPath(bool $bold = false): string
    {
        $base = __DIR__ . '/../fonts/';
        return $base . ($bold ? 'DejaVuSans-Bold.ttf' : 'DejaVuSans.ttf');
    }
    
    // ===== Checksum Calculators =====
    
    private function calculateEAN13Checksum(string $code): string
    {
        $code = substr($code, 0, 12);
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += ($i % 2 === 0) ? (int)$code[$i] : (int)$code[$i] * 3;
        }
        $checksum = (10 - ($sum % 10)) % 10;
        return $code . $checksum;
    }
    
    private function calculateEAN8Checksum(string $code): string
    {
        $code = substr($code, 0, 7);
        $sum = 0;
        for ($i = 0; $i < 7; $i++) {
            $sum += ($i % 2 === 0) ? (int)$code[$i] * 3 : (int)$code[$i];
        }
        $checksum = (10 - ($sum % 10)) % 10;
        return $code . $checksum;
    }
    
    private function calculateUPCAChecksum(string $code): string
    {
        $code = substr($code, 0, 11);
        $sum = 0;
        for ($i = 0; $i < 11; $i++) {
            $sum += ($i % 2 === 0) ? (int)$code[$i] * 3 : (int)$code[$i];
        }
        $checksum = (10 - ($sum % 10)) % 10;
        return $code . $checksum;
    }
}