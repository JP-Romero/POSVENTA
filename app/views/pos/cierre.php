<?php require APPROOT . '/views/inc/header.php'; ?>

<style>
/* Dark theme for Cierre similar to the requested images */
.cierre-container {
    background-color: #1a1a1a;
    color: #e0e0e0;
    min-height: calc(100vh - 60px);
    padding: 2rem;
    font-family: 'Inter', sans-serif;
}
.cierre-ticket {
    background-color: #121212;
    border: 1px solid #333;
    border-radius: 8px;
    max-width: 500px;
    margin: 0 auto;
    padding: 2rem;
    box-shadow: 0 10px 25px rgba(0,0,0,0.5);
}
.cierre-header {
    text-align: center;
    border-bottom: 1px dashed #555;
    padding-bottom: 1rem;
    margin-bottom: 1rem;
}
.cierre-title {
    font-weight: 700;
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
    color: #fff;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.cierre-section {
    margin-bottom: 1.5rem;
}
.cierre-section-title {
    background-color: #333;
    color: #f39c12; /* Accent color like the screenshots */
    padding: 0.3rem 0.5rem;
    font-weight: bold;
    font-size: 0.9rem;
    border-radius: 4px;
    margin-bottom: 0.8rem;
    text-transform: uppercase;
}
.cierre-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.4rem;
    font-size: 0.95rem;
}
.cierre-row.total {
    font-weight: bold;
    color: #fff;
    border-top: 1px solid #444;
    padding-top: 0.5rem;
    margin-top: 0.5rem;
}
.cierre-row.net {
    color: #2ecc71; /* Green for profit/net */
}
.cierre-row.expense {
    color: #e74c3c; /* Red for expenses */
}
.cierre-divider {
    border-top: 1px dashed #555;
    margin: 1rem 0;
}
.cierre-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 2rem;
}
.btn-cierre {
    background-color: #e74c3c;
    color: white;
    border: none;
    padding: 0.8rem 1.5rem;
    border-radius: 4px;
    font-weight: bold;
    cursor: pointer;
    flex: 1;
    transition: background 0.3s;
}
.btn-cierre:hover {
    background-color: #c0392b;
}
.btn-movimiento {
    background-color: #3498db;
    color: white;
    border: none;
    padding: 0.8rem 1.5rem;
    border-radius: 4px;
    font-weight: bold;
    cursor: pointer;
    flex: 1;
    transition: background 0.3s;
}
.btn-movimiento:hover {
    background-color: #2980b9;
}
</style>

<div class="cierre-container">
    <div class="cierre-ticket">
        <div class="cierre-header">
            <div class="cierre-title">TICKET DE CIERRE DE CAJA (REPORTE Z)</div>
            <div><?= getConfig('nombre_negocio', 'POSVENTA') ?></div>
            <div style="font-size: 0.85rem; color: #aaa;"><?= getConfig('direccion', '') ?></div>
            <div style="margin-top: 0.5rem; font-size: 0.9rem;">
                Fecha: <?= date('d/m/Y H:i', strtotime($data['fecha_fin'])) ?>
            </div>
            <div style="font-size: 0.9rem;">Cajero: <?= $_SESSION['user_name'] ?></div>
        </div>

        <div class="cierre-section">
            <div class="cierre-section-title">1. RESUMEN DE VENTAS</div>
            <div class="cierre-row">
                <span>Ventas Brutas:</span>
                <span>$<?= number_format($data['resumen']->ventas_netas + $data['resumen']->descuentos ?? 0, 2) ?></span>
            </div>
            <div class="cierre-row expense">
                <span>(-) Devoluciones/Descuentos:</span>
                <span>-$<?= number_format($data['resumen']->descuentos ?? 0, 2) ?></span>
            </div>
            <div class="cierre-row total net">
                <span>VENTAS NETAS:</span>
                <span>$<?= number_format($data['resumen']->ventas_netas ?? 0, 2) ?></span>
            </div>
        </div>

        <div class="cierre-section">
            <div class="cierre-section-title">2. VENTAS POR CATEGORÍA</div>
            <?php if(!empty($data['categorias'])): ?>
                <?php foreach($data['categorias'] as $cat): ?>
                <div class="cierre-row">
                    <span><?= $cat->categoria ?>:</span>
                    <span>$<?= number_format($cat->total, 2) ?></span>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="cierre-row"><span>Sin ventas</span></div>
            <?php endif; ?>
        </div>

        <div class="cierre-section">
            <div class="cierre-section-title">3. MÉTODOS DE PAGO</div>
            <div class="cierre-row">
                <span>Efectivo:</span>
                <span>$<?= number_format($data['pagos']['Efectivo'], 2) ?></span>
            </div>
            <div class="cierre-row">
                <span>Tarjeta (Débito/Crédito):</span>
                <span>$<?= number_format($data['pagos']['Tarjeta'], 2) ?></span>
            </div>
            <div class="cierre-row">
                <span>Transferencia/QR:</span>
                <span>$<?= number_format($data['pagos']['Transferencia'], 2) ?></span>
            </div>
            <div class="cierre-row total">
                <span>TOTAL RECAUDADO:</span>
                <span>$<?= number_format($data['resumen']->ventas_netas ?? 0, 2) ?></span>
            </div>
        </div>

        <div class="cierre-section">
            <div class="cierre-section-title">4. MOVIMIENTOS DE EFECTIVO</div>
            <div class="cierre-row">
                <span>(+) Fondo de Caja Inicial:</span>
                <span>$<?= number_format($data['fondo_inicial'], 2) ?></span>
            </div>
            <div class="cierre-row">
                <span>(+) Ventas en Efectivo:</span>
                <span>$<?= number_format($data['pagos']['Efectivo'], 2) ?></span>
            </div>
            <?php foreach($data['listaMovimientos'] as $mov): ?>
                <?php if ($mov->concepto != 'Fondo Inicial' && $mov->concepto != 'Fondo de Caja Inicial'): ?>
                <div class="cierre-row <?= $mov->tipo == 'Salida' ? 'expense' : 'net' ?>">
                    <span><?= $mov->tipo == 'Entrada' ? '(+)' : '(-)' ?> <?= $mov->concepto ?>:</span>
                    <span><?= $mov->tipo == 'Salida' ? '-' : '' ?>$<?= number_format($mov->monto, 2) ?></span>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
            
            <div class="cierre-row total net" style="border-top: 1px dashed #555; padding-top: 0.5rem;">
                <span>EFECTIVO ESPERADO EN CAJA:</span>
                <span>$<?= number_format($data['efectivo_esperado'], 2) ?></span>
            </div>
        </div>

        <div class="cierre-section">
            <div class="cierre-section-title">5. ESTADÍSTICAS DEL DÍA</div>
            <div class="cierre-row">
                <span>Tickets Emitidos:</span>
                <span><?= $data['resumen']->tickets_emitidos ?? 0 ?></span>
            </div>
            <div class="cierre-row">
                <span>Ticket Promedio:</span>
                <span>$<?= number_format(($data['resumen']->tickets_emitidos > 0 ? $data['resumen']->ventas_netas / $data['resumen']->tickets_emitidos : 0), 2) ?></span>
            </div>
            <div class="cierre-row">
                <span>Primer Ticket:</span>
                <span><?= $data['resumen']->primer_ticket ?? 'N/A' ?></span>
            </div>
            <div class="cierre-row">
                <span>Último Ticket:</span>
                <span><?= $data['resumen']->ultimo_ticket ?? 'N/A' ?></span>
            </div>
        </div>

        <form action="<?= URLROOT ?>/caja/cerrarTurno" method="POST" id="form-cierre">
            <div class="cierre-section" style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 4px;">
                <div class="cierre-section-title" style="margin-bottom: 0.5rem;">AUDITORÍA DE CAJA (Arqueo)</div>
                <div class="form-group mb-2">
                    <label style="font-size: 0.9rem;">Efectivo Real Contado:</label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark text-white border-secondary">$</span>
                        <input type="number" step="0.01" id="efectivo_real" name="efectivo_real" class="form-control bg-dark text-white border-secondary" required placeholder="0.00">
                    </div>
                </div>
                <div class="cierre-row" style="margin-top: 0.5rem;">
                    <span>Diferencia (Sobrante/Faltante):</span>
                    <span id="diferencia-texto">$0.00</span>
                </div>
                <input type="hidden" id="efectivo_esperado" name="efectivo_esperado" value="<?= $data['efectivo_esperado'] ?>">
            </div>

            <div class="cierre-actions">
                <button type="button" class="btn-movimiento" data-bs-toggle="modal" data-bs-target="#movimientoModal">
                    <i class="fas fa-exchange-alt"></i> Movimiento
                </button>
                <button type="submit" class="btn-cierre">
                    <i class="fas fa-lock"></i> Cerrar Caja
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Movimiento -->
<div class="modal fade" id="movimientoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">Registrar Movimiento de Caja</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= URLROOT ?>/caja/registrarMovimiento" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Tipo de Movimiento</label>
                        <select name="tipo" class="form-select bg-dark text-white border-secondary" required>
                            <option value="Salida">Salida (Gasto, Retiro, Proveedor)</option>
                            <option value="Entrada">Entrada (Fondo inicial, Ingreso extra)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Concepto / Descripción</label>
                        <input type="text" name="concepto" class="form-control bg-dark text-white border-secondary" required placeholder="Ej. Pago de Hojas, Fondo Inicial">
                    </div>
                    <div class="mb-3">
                        <label>Monto</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark text-white border-secondary">$</span>
                            <input type="number" step="0.01" name="monto" class="form-control bg-dark text-white border-secondary" required min="0.01">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const esperado = parseFloat(document.getElementById('efectivo_esperado').value);
    const inputReal = document.getElementById('efectivo_real');
    const difTexto = document.getElementById('diferencia-texto');

    inputReal.addEventListener('input', function() {
        const real = parseFloat(this.value) || 0;
        const dif = real - esperado;
        difTexto.textContent = '$' + dif.toFixed(2);
        if (dif < 0) {
            difTexto.style.color = '#e74c3c'; // Faltante
        } else if (dif > 0) {
            difTexto.style.color = '#f1c40f'; // Sobrante
        } else {
            difTexto.style.color = '#2ecc71'; // Balanceado
        }
    });

    // Handle form submission via fetch to get the ID and print
    const formCierre = document.getElementById('form-cierre');
    formCierre.addEventListener('submit', function(e) {
        e.preventDefault();
        if(!confirm('¿Estás seguro de que deseas cerrar la caja? Esto iniciará un nuevo turno.')) return;
        
        const formData = new FormData(this);
        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                // TODO: Llamar a imprimir el reporte Z si es necesario
                alert('Caja cerrada correctamente. Reporte Z generado con ID: #' + data.id);
                window.location.href = '<?= URLROOT ?>/pos';
            } else {
                alert('Hubo un error al cerrar la caja');
            }
        });
    });
});
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>
