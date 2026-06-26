<?php require APPROOT . '/views/inc/header.php'; ?>


<main class="content p-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-primary"><i class="fa fa-boxes me-2"></i> Capital e Inventario</h2>
            <p class="text-muted">Valoración y Estado del Inventario</p>
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
        <!-- Capital Invertido -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-gradient-secondary text-white" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%);">
                <div class="card-body p-4 text-center">
                    <div class="bg-white bg-opacity-25 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fa fa-coins text-white fa-2x"></i>
                    </div>
                    <h5 class="text-white-50 text-uppercase fw-bold mb-2">Capital Invertido (Costo)</h5>
                    <h2 class="fw-bold mb-0">C$<?= number_format($data['analysis']['capital_invertido'], 2) ?></h2>
                </div>
            </div>
        </div>

        <!-- Valor Comercial -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-gradient-primary text-white" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
                <div class="card-body p-4 text-center">
                    <div class="bg-white bg-opacity-25 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fa fa-tags text-white fa-2x"></i>
                    </div>
                    <h5 class="text-white-50 text-uppercase fw-bold mb-2">Valor Comercial (Venta)</h5>
                    <h2 class="fw-bold mb-0">C$<?= number_format($data['analysis']['valor_comercial'], 2) ?></h2>
                </div>
            </div>
        </div>

        <!-- Ganancia Potencial -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-gradient-success text-white" style="background: linear-gradient(135deg, #198754 0%, #146c43 100%);">
                <div class="card-body p-4 text-center">
                    <div class="bg-white bg-opacity-25 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fa fa-hand-holding-usd text-white fa-2x"></i>
                    </div>
                    <h5 class="text-white-50 text-uppercase fw-bold mb-2">Ganancia Potencial</h5>
                    <h2 class="fw-bold mb-0">C$<?= number_format($data['analysis']['ganancia_potencial'], 2) ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Indicadores de Estado -->
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3 text-center">
                    <h6 class="text-muted fw-bold text-uppercase mb-2">Inventario Disponible</h6>
                    <h3 class="fw-bold text-dark mb-0"><?= number_format($data['analysis']['inventario_disponible']) ?> <small class="text-muted fs-6">unidades</small></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3 text-center">
                    <h6 class="text-muted fw-bold text-uppercase mb-2">Inventario Comprometido</h6>
                    <h3 class="fw-bold text-warning mb-0"><?= number_format($data['analysis']['comprometido']) ?> <small class="text-muted fs-6">unidades</small></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3 text-center">
                    <h6 class="text-muted fw-bold text-uppercase mb-2">Rotación (Mensual)</h6>
                    <h3 class="fw-bold text-info mb-0"><?= number_format($data['analysis']['rotacion'], 2) ?>x</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3 text-center">
                    <h6 class="text-muted fw-bold text-uppercase mb-2">Sin Movimiento (30d)</h6>
                    <h3 class="fw-bold text-danger mb-0"><?= number_format($data['analysis']['sin_movimiento']) ?> <small class="text-muted fs-6">productos</small></h3>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.executive-tabs .nav-link { color: #495057; border-radius: 8px; font-weight: 500; }
.executive-tabs .nav-link.active { background-color: #f8f9fa; color: #0d6efd; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
.nav-pills .nav-link:hover { background-color: #f1f3f5; }
</style>

<?php require APPROOT . '/views/inc/footer.php'; ?>
