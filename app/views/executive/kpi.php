<?php require APPROOT . '/views/inc/header.php'; ?>


<main class="content p-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-primary"><i class="fa fa-tachometer-alt me-2"></i> Indicadores KPIs</h2>
            <p class="text-muted">Indicadores Clave de Rendimiento (Mensual)</p>
        </div>
    </div>

    <!-- Navegación interna (Tabs) -->
    <ul class="nav nav-pills mb-4 executive-tabs bg-white p-2 rounded shadow-sm border">
        <li class="nav-item">
            <a class="nav-link <?= $data['active_tab'] == 'resume' ? 'active' : '' ?>" href="<?= URLROOT ?>/executive/resume"><i class="fa fa-chart-pie me-1"></i> Resumen</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data['active_tab'] == 'inventory' ? 'active' : '' ?>" href="<?= URLROOT ?>/executive/inventory"><i class="fa fa-boxes me-1"></i> Inventario</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data['active_tab'] == 'profitability' ? 'active' : '' ?>" href="<?= URLROOT ?>/executive/profitability"><i class="fa fa-dollar-sign me-1"></i> Rentabilidad</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data['active_tab'] == 'kpi' ? 'active' : '' ?>" href="<?= URLROOT ?>/executive/kpi"><i class="fa fa-tachometer-alt me-1"></i> KPIs</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data['active_tab'] == 'trends' ? 'active' : '' ?>" href="<?= URLROOT ?>/executive/trends"><i class="fa fa-chart-line me-1"></i> Tendencias</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data['active_tab'] == 'audit' ? 'active' : '' ?>" href="<?= URLROOT ?>/executive/audit"><i class="fa fa-shield-alt me-1"></i> Auditoría</a>
        </li>
    </ul>

    <div class="row g-4 mb-4">
        <!-- ROI -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 text-center p-4">
                <h6 class="text-muted text-uppercase fw-bold mb-3">Retorno Inversión (ROI)</h6>
                <div class="position-relative d-inline-block mx-auto mb-2" style="width: 120px; height: 120px;">
                    <svg viewBox="0 0 36 36" class="circular-chart blue">
                        <path class="circle-bg"
                        d="M18 2.0845
                            a 15.9155 15.9155 0 0 1 0 31.831
                            a 15.9155 15.9155 0 0 1 0 -31.831"
                        />
                        <path class="circle"
                        stroke-dasharray="<?= min(100, max(0, $data['kpis']['roi'])) ?>, 100"
                        d="M18 2.0845
                            a 15.9155 15.9155 0 0 1 0 31.831
                            a 15.9155 15.9155 0 0 1 0 -31.831"
                        />
                        <text x="18" y="20.35" class="percentage"><?= number_format($data['kpis']['roi'], 1) ?>%</text>
                    </svg>
                </div>
                <small class="text-muted">Utilidad / Costo de Ventas</small>
            </div>
        </div>

        <!-- Margen Bruto -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 text-center p-4">
                <h6 class="text-muted text-uppercase fw-bold mb-3">Margen Bruto</h6>
                <div class="position-relative d-inline-block mx-auto mb-2" style="width: 120px; height: 120px;">
                    <svg viewBox="0 0 36 36" class="circular-chart green">
                        <path class="circle-bg"
                        d="M18 2.0845
                            a 15.9155 15.9155 0 0 1 0 31.831
                            a 15.9155 15.9155 0 0 1 0 -31.831"
                        />
                        <path class="circle"
                        stroke-dasharray="<?= min(100, max(0, $data['kpis']['margen_bruto'])) ?>, 100"
                        d="M18 2.0845
                            a 15.9155 15.9155 0 0 1 0 31.831
                            a 15.9155 15.9155 0 0 1 0 -31.831"
                        />
                        <text x="18" y="20.35" class="percentage"><?= number_format($data['kpis']['margen_bruto'], 1) ?>%</text>
                    </svg>
                </div>
                <small class="text-muted">Utilidad / Ventas</small>
            </div>
        </div>

        <!-- Crecimiento Mensual -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 text-center p-4">
                <h6 class="text-muted text-uppercase fw-bold mb-3">Crecimiento Ventas</h6>
                <?php $crec = $data['kpis']['crecimiento_ventas']; ?>
                <div class="d-flex flex-column align-items-center justify-content-center h-100 mb-2">
                    <?php if($crec >= 0): ?>
                        <i class="fa fa-arrow-trend-up text-success mb-2" style="font-size: 3rem;"></i>
                        <h2 class="fw-bold text-success mb-0">+<?= number_format($crec, 1) ?>%</h2>
                    <?php else: ?>
                        <i class="fa fa-arrow-trend-down text-danger mb-2" style="font-size: 3rem;"></i>
                        <h2 class="fw-bold text-danger mb-0"><?= number_format($crec, 1) ?>%</h2>
                    <?php endif; ?>
                </div>
                <small class="text-muted">vs. Mes Anterior</small>
            </div>
        </div>

        <!-- Ticket Promedio KPI -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 text-center p-4">
                <h6 class="text-muted text-uppercase fw-bold mb-3">Ticket Promedio</h6>
                <div class="d-flex flex-column align-items-center justify-content-center h-100 mb-2">
                    <i class="fa fa-receipt text-primary mb-2" style="font-size: 3rem;"></i>
                    <h2 class="fw-bold text-primary mb-0">C$<?= number_format($data['kpis']['ticket_promedio'], 0) ?></h2>
                </div>
                <small class="text-muted">Promedio gastado por compra</small>
            </div>
        </div>
    </div>
</main>

<style>
.executive-tabs .nav-link { color: #495057; border-radius: 8px; font-weight: 500; }
.executive-tabs .nav-link.active { background-color: #f8f9fa; color: #0d6efd; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
.nav-pills .nav-link:hover { background-color: #f1f3f5; }

.circular-chart {
  display: block;
  margin: 0 auto;
  max-width: 80%;
  max-height: 250px;
}
.circle-bg {
  fill: none;
  stroke: #eee;
  stroke-width: 3.8;
}
.circle {
  fill: none;
  stroke-width: 2.8;
  stroke-linecap: round;
  animation: progress 1s ease-out forwards;
}
@keyframes progress {
  0% { stroke-dasharray: 0 100; }
}
.circular-chart.blue .circle { stroke: #0d6efd; }
.circular-chart.green .circle { stroke: #198754; }
.percentage {
  fill: #666;
  font-family: sans-serif;
  font-size: 0.5em;
  text-anchor: middle;
  font-weight: bold;
}
</style>

<?php require APPROOT . '/views/inc/footer.php'; ?>
