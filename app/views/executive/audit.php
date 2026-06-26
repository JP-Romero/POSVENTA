<?php require APPROOT . '/views/inc/header.php'; ?>


<main class="content p-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-primary"><i class="fa fa-shield-alt me-2"></i> Auditoría Financiera</h2>
            <p class="text-muted">Registro de Descuentos, Anomalías y Excepciones</p>
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

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 text-uppercase text-muted fw-bold">Fecha / Hora</th>
                            <th class="text-uppercase text-muted fw-bold">Factura</th>
                            <th class="text-uppercase text-muted fw-bold">Responsable</th>
                            <th class="text-uppercase text-muted fw-bold">Producto Afectado</th>
                            <th class="text-uppercase text-muted fw-bold text-center">Cantidad</th>
                            <th class="pe-4 text-uppercase text-muted fw-bold text-end">Descuento Otorgado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['logs'])): ?>
                        <tr>
                            <td colspan="6" class="text-center p-4 text-muted">No se encontraron registros de auditoría recientes.</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach($data['logs'] as $log): ?>
                            <tr>
                                <td class="ps-4"><?= date('d/m/Y H:i', strtotime($log->fecha)) ?></td>
                                <td><span class="badge bg-secondary"><?= $log->numero_factura ?></span></td>
                                <td class="fw-bold text-dark"><?= $log->usuario ?></td>
                                <td><?= $log->producto ?></td>
                                <td class="text-center"><?= $log->cantidad ?></td>
                                <td class="pe-4 text-end text-danger fw-bold">C$<?= number_format($log->descuento, 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
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
