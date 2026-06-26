<?php require APPROOT . '/views/inc/header.php'; ?>


<main class="content p-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-primary"><i class="fa fa-briefcase me-2"></i> Centro Ejecutivo</h2>
            <p class="text-muted">Resumen Ejecutivo y Métricas Principales</p>
        </div>
        <form method="GET" class="d-flex gap-2">
            <input type="date" name="start" class="form-control" value="<?= $data['start'] ?>">
            <input type="date" name="end" class="form-control" value="<?= $data['end'] ?>">
            <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i></button>
        </form>
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

    <!-- Tarjetas Ejecutivas -->
    <div class="row g-4 mb-4">
        <!-- Ventas Netas -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-gradient-primary text-white" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-white-50 mb-0 fw-bold text-uppercase">Ventas Netas</h6>
                        <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fa fa-shopping-cart text-white"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1">C$<?= number_format($data['summary']['ventas_netas'], 2) ?></h3>
                    <p class="mb-0 text-white-50 small">Ingresos reales (sin descuentos)</p>
                </div>
            </div>
        </div>

        <!-- Costo de Ventas -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-gradient-danger text-white" style="background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-white-50 mb-0 fw-bold text-uppercase">Costo de Ventas</h6>
                        <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fa fa-file-invoice-dollar text-white"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1">C$<?= number_format($data['summary']['costo_ventas'], 2) ?></h3>
                    <p class="mb-0 text-white-50 small">Costo histórico de productos vendidos</p>
                </div>
            </div>
        </div>

        <!-- Utilidad Bruta -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-gradient-success text-white" style="background: linear-gradient(135deg, #198754 0%, #146c43 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-white-50 mb-0 fw-bold text-uppercase">Utilidad Bruta</h6>
                        <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fa fa-chart-line text-white"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1">C$<?= number_format($data['summary']['utilidad_bruta'], 2) ?></h3>
                    <p class="mb-0 text-white-50 small">Ventas - Costos - Descuentos</p>
                </div>
            </div>
        </div>

        <!-- Margen Comercial -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-gradient-info text-white" style="background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-white-50 mb-0 fw-bold text-uppercase">Margen Comercial</h6>
                        <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fa fa-percentage text-white"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1"><?= number_format($data['summary']['margen_comercial'], 2) ?>%</h3>
                    <p class="mb-0 text-white-50 small">Porcentaje de ganancia sobre ventas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Otros Indicadores -->
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 text-center">
                    <div class="text-primary mb-2"><i class="fa fa-ticket-alt fa-3x"></i></div>
                    <h2 class="fw-bold text-dark">C$<?= number_format($data['summary']['ticket_promedio'], 2) ?></h2>
                    <p class="text-muted mb-0 fw-bold text-uppercase small">Ticket Promedio</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 text-center">
                    <div class="text-success mb-2"><i class="fa fa-users fa-3x"></i></div>
                    <h2 class="fw-bold text-dark"><?= number_format($data['summary']['clientes_atendidos']) ?></h2>
                    <p class="text-muted mb-0 fw-bold text-uppercase small">Clientes Atendidos</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 text-center">
                    <div class="text-warning mb-2"><i class="fa fa-box-open fa-3x"></i></div>
                    <h2 class="fw-bold text-dark"><?= number_format($data['summary']['productos_vendidos']) ?></h2>
                    <p class="text-muted mb-0 fw-bold text-uppercase small">Productos Vendidos</p>
                </div>
            </div>
        </div>
    </div>

</main>

<style>
.executive-tabs .nav-link {
    color: #495057;
    border-radius: 8px;
    font-weight: 500;
}
.executive-tabs .nav-link.active {
    background-color: #f8f9fa;
    color: #0d6efd;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
.nav-pills .nav-link:hover {
    background-color: #f1f3f5;
}
</style>

<?php require APPROOT . '/views/inc/footer.php'; ?>
