const { test, expect } = require('@playwright/test');
const { injectAxe, checkA11y } = require('axe-playwright');

test.describe('POS E2E & Accessibility Testing Suite', () => {
  
  test.beforeEach(async ({ page }) => {
    // 1. Simular Login
    await page.goto('http://localhost/POSVENTA/users/login');
    await page.fill('input[name="username"]', 'admin');
    await page.fill('input[name="password"]', '123456');
    await page.click('button[type="submit"]');
    
    // 2. Navegar al POS
    await page.goto('http://localhost/POSVENTA/pos');
    
    // 3. Inyectar Axe-core para validaciones de accesibilidad
    await injectAxe(page);
    
    // 4. Manejar "Apertura de Caja" si el modal bloquea la UI
    const modalApertura = page.locator('#aperturaCajaModal');
    if (await modalApertura.isVisible()) {
      await page.fill('#monto_apertura', '100.00');
      await page.click('#btn-abrir-caja');
      // Esperar a que la página recargue tras abrir el turno
      await page.waitForLoadState('networkidle');
    }
  });

  test('Validar adición al carrito y cálculo de totales', async ({ page }) => {
    // Esperar a que el grid de productos con Event Delegation esté listo
    await page.waitForSelector('.pos-product-card');
    
    const firstProduct = page.locator('.pos-product-card').first();
    const productPriceText = await firstProduct.locator('.pos-product-price').innerText();
    const productPrice = parseFloat(productPriceText.replace('$', ''));

    // Hacer click dos veces (Agregar 2 unidades)
    await firstProduct.click();
    await firstProduct.click();

    // Extraer totales del DOM
    const subtotalText = await page.innerText('#subtotal');
    const totalText = await page.innerText('#total-amount');

    // Cálculos esperados (Basados en IVA 15%)
    const expectedSubtotal = productPrice * 2;
    const expectedTax = expectedSubtotal * 0.15;
    const expectedTotal = expectedSubtotal + expectedTax;

    // Aserciones
    expect(subtotalText).toContain(expectedSubtotal.toFixed(2));
    expect(totalText).toContain(expectedTotal.toFixed(2));
  });

  test('Flujo de Cobro abre el Modal de Ticket (Preview)', async ({ page }) => {
    await page.waitForSelector('.pos-product-card');
    await page.locator('.pos-product-card').first().click();

    // Usar el Shortcut F12 para cobrar (Validando EventListener global)
    await page.keyboard.press('F12');

    // Validar que el modal de ticket preview se muestra en lugar del Toast
    const previewModal = page.locator('#previewTicketModal');
    await expect(previewModal).toBeVisible();

    // Validar que el iframe cargó la vista de la factura generada
    const iframe = page.locator('#iframeTicket');
    await expect(iframe).toHaveAttribute('src', /\/sales\/invoice\/\d+/);
  });

  test('Auditoría de Accesibilidad Automática (Axe-core)', async ({ page }) => {
    // Axe-core escaneará el DOM en busca de violaciones WCAG
    // (Ej: falta de aria-labels, contraste de color pobre, focus rings ausentes)
    await checkA11y(page, null, {
      detailedReport: true,
      detailedReportOptions: { html: true }
    });
  });
  
  test('Atributos ARIA presentes en botones de acción', async ({ page }) => {
    // Validar que los botones que usan FontAwesome ahora tengan aria-labels (A11Y Refactor)
    const btnCancelar = page.locator('#btn-cancelar');
    await expect(btnCancelar).toHaveAttribute('aria-label', 'Cancelar venta y vaciar carrito');
    
    const iconTrash = btnCancelar.locator('.fa-trash');
    await expect(iconTrash).toHaveAttribute('aria-hidden', 'true');
  });
});
