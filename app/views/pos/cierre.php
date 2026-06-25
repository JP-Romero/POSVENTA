<?php require APPROOT . '/views/inc/header.php'; ?>

<style>
/* Dark theme for Cierre similar to the requested images */
.cierre-container {
    background-color: var(--dark-bg-primary, #1a1a1a);
    color: var(--dark-text-primary, #e0e0e0);
    min-height: calc(100vh - 60px);
    padding: 2rem;
    font-family: 'Inter', sans-serif;
}
.cierre-ticket {
    background-color: var(--dark-bg-secondary, #121212);
    border: 1px solid var(--dark-border-color, #333);
    border-radius: 8px;
    max-width: 500px;
    margin: 0 auto;
    padding: 2rem;
    box-shadow: 0 10px 25px rgba(0,0,0,0.5);
}
.cierre-header {
    text-align: center;
    border-bottom: 1px dashed var(--dark-text-muted, #555);
    padding-bottom: 1rem;
    margin-bottom: 1rem;
}
.cierre-title {
    font-weight: 700;
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
    color: var(--dark-text-inverse, #fff);
    text-transform: uppercase;
    letter-spacing: 1px;
}
.cierre-section {
    margin-bottom: 1.5rem;
}
.cierre-section-title {
    background-color: var(--dark-bg-tertiary, #333);
    color: var(--bs-warning, #f39c12);
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
    color: var(--dark-text-inverse, #fff);
    border-top: 1px solid var(--dark-border-color, #444);
    padding-top: 0.5rem;
    margin-top: 0.5rem;
}
.cierre-row.net {
    color: var(--bs-success, #2ecc71);
}
.cierre-row.expense {
    color: var(--bs-danger, #e74c3c);
}
.cierre-divider {
    border-top: 1px dashed var(--dark-text-muted, #555);
    margin: 1rem 0;
}
.cierre-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 2rem;
}
.btn-cierre {
    background-color: var(--bs-danger, #e74c3c);
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
    background-color: var(--bs-danger-darker, #c0392b);
}
.btn-movimiento {
    background-color: var(--bs-primary, #3498db);
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
    background-color: var(--bs-primary-darker, #2980b9);
}
</style>

<div class="row mb-3">
    <div class="col-md-6">
        <h1>Reportes y Estadísticas</h1>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <ul class="nav nav-tabs" id="reportsTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link" href="<?= URLROOT ?>/reports">Ventas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= URLROOT ?>/reports">Inventario</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= URLROOT ?>/reports">Compras</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="<?= URLROOT ?>/cierre">Cierre de Caja</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= URLROOT ?>/estadisticas">Estadísticas</a>
            </li>
        </ul>
    </div>
    <div class="card-body p-0">
        <div class="cierre-container">
            <div class="cierre-ticket">
                <div class="cierre-header">
            <div class="cierre-title">TICKET DE CIERRE DE CAJA (REPORTE Z)</div>
            <div><?= getConfig('nombre_negocio', 'POSVENTA') ?></div>
            <div style="font-size: 0.85rem; color: #aaa;"><?= getConfig('direccion', '') ?></div>
            <div style="margin-top: 0.5rem; font-size: 0.9rem;">
                Fecha: <?= date('d/m/Y H:i', strtotime($data['fecha_fin'])) ?>
            </div>
            <div style="font-size: 0.9rem;">Cajero: <?= htmlspecialchars($_SESSION['user_nombre'] ?? 'Usuario', ENT_QUOTES, 'UTF-8') ?></div>
        </div>

        <div class="cierre-section">
            <div class="cierre-section-title">1. RESUMEN DE VENTAS</div>
            <div class="cierre-row">
                <span>Ventas Brutas:</span>
                <span><?= fmt(($data['resumen']->ventas_netas ?? 0) + ($data['resumen']->descuentos ?? 0)) ?></span>
            </div>
            <div class="cierre-row expense">
                <span>(-) Devoluciones/Descuentos:</span>
                <span><?= fmt(-($data['resumen']->descuentos ?? 0)) ?></span>
            </div>
            <div class="cierre-row total net">
                <span>VENTAS NETAS:</span>
                <span><?= fmt($data['resumen']->ventas_netas ?? 0) ?></span>
            </div>
        </div>

        <div class="cierre-section">
            <div class="cierre-section-title">2. VENTAS POR CATEGORÍA</div>
            <?php if(!empty($data['categorias'])): ?>
                <?php foreach($data['categorias'] as $cat): ?>
                <div class="cierre-row">
                    <span><?= h($cat->categoria) ?>:</span>
                    <span><?= fmt($cat->total) ?></span>
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
                <span><?= fmt($data['pagos']['Efectivo']) ?></span>
            </div>
            <div class="cierre-row">
                <span>Tarjeta (Débito/Crédito):</span>
                <span><?= fmt($data['pagos']['Tarjeta']) ?></span>
            </div>
            <div class="cierre-row">
                <span>Transferencia/QR:</span>
                <span><?= fmt($data['pagos']['Transferencia']) ?></span>
            </div>
            <div class="cierre-row total">
                <span>TOTAL RECAUDADO:</span>
                <span><?= fmt($data['resumen']->ventas_netas ?? 0) ?></span>
            </div>
        </div>

        <div class="cierre-section">
            <div class="cierre-section-title">4. MOVIMIENTOS DE EFECTIVO</div>
            <div class="cierre-row">
                <span>(+) Fondo de Caja Inicial:</span>
                <span><?= fmt($data['fondo_inicial']) ?></span>
            </div>
            <div class="cierre-row">
                <span>(+) Ventas en Efectivo:</span>
                <span><?= fmt($data['pagos']['Efectivo']) ?></span>
            </div>
            <?php foreach($data['listaMovimientos'] as $mov): ?>
                <?php if ($mov->concepto != 'Fondo Inicial' && $mov->concepto != 'Fondo de Caja Inicial'): ?>
                <div class="cierre-row <?= $mov->tipo == 'Salida' ? 'expense' : 'net' ?>">
                    <span><?= $mov->tipo == 'Entrada' ? '(+)' : '(-)' ?> <?= h($mov->concepto) ?>:</span>
                    <span><?= ($mov->tipo == 'Salida' ? '-' : '') ?><?= fmt($mov->monto, false) ?></span>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
            
            <div class="cierre-row total net" style="border-top: 1px dashed #555; padding-top: 0.5rem;">
                <span>EFECTIVO ESPERADO EN CAJA:</span>
                <span><?= fmt($data['efectivo_esperado']) ?></span>
            </div>
        </div>

        <div class="cierre-section">
            <div class="cierre-section-title">5. ESTADÍSTICAS DEL DÍA</div>
            <div class="cierre-row">
                <span>Tickets Emitidos:</span>
                <span><?= (int)($data['resumen']->tickets_emitidos ?? 0) ?></span>
            </div>
            <div class="cierre-row">
                <span>Ticket Promedio:</span>
                <span><?= fmt($data['resumen']->tickets_emitidos > 0 ? $data['resumen']->ventas_netas / $data['resumen']->tickets_emitidos : 0) ?></span>
            </div>
            <div class="cierre-row">
                <span>Primer Ticket:</span>
                <span><?= h($data['resumen']->primer_ticket ?? 'N/A') ?></span>
            </div>
            <div class="cierre-row">
                <span>Último Ticket:</span>
                <span><?= h($data['resumen']->ultimo_ticket ?? 'N/A') ?></span>
            </div>
        </div>

        <form action="<?= URLROOT ?>/cierre/cerrarTurno" method="POST" id="form-cierre">
            <?= csrfField() ?>
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
        </div>
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
            <form action="<?= URLROOT ?>/cierre/registrarMovimiento" method="POST">
                <?= csrfField() ?>
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
        Swal.fire({
            title: '¿Cerrar la caja?',
            text: 'Esto iniciará un nuevo turno.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, cerrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (!result.isConfirmed) return;
            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Caja cerrada', text: 'Reporte Z #' + data.id });
                    window.location.href = '<?= URLROOT ?>/pos';
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Hubo un error al cerrar la caja' });
                }
            });
        });
    });
});
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>
