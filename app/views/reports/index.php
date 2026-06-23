<?php $currentPage = 'reports'; require APPROOT . '/views/inc/header.php'; ?>
<?php flash('report_message'); ?>

<div class="row mb-3">
    <div class="col-md-6">
        <h1>Reportes y Estadísticas</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?= URLROOT ?>/reports/exportSales?fecha_inicio=<?= $data['fecha_inicio'] ?>&fecha_fin=<?= $data['fecha_fin'] ?>" class="btn btn-success">
            <i class="fa fa-file-excel me-1"></i> Exportar Ventas (CSV)
        </a>
        <a href="<?= URLROOT ?>/reports/exportInventory" class="btn btn-outline-primary">
            <i class="fa fa-file-excel me-1"></i> Exportar Inventario (CSV)
        </a>
        <a href="<?= URLROOT ?>/reports/exportPurchases?fecha_inicio=<?= $data['fecha_inicio'] ?>&fecha_fin=<?= $data['fecha_fin'] ?>" class="btn btn-outline-info">
            <i class="fa fa-file-excel me-1"></i> Exportar Compras (CSV)
        </a>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <ul class="nav nav-tabs" id="reportsTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="ventas-tab" data-bs-toggle="tab" data-bs-target="#ventas" type="button" role="tab">Ventas</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="inventario-tab" data-bs-toggle="tab" data-bs-target="#inventario" type="button" role="tab">Inventario</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="compras-tab" data-bs-toggle="tab" data-bs-target="#compras" type="button" role="tab">Compras</button>
            </li>
        </ul>
    </div>
    <div class="tab-content" id="reportsTabContent">
        <!-- Ventas Tab -->
        <div class="tab-pane fade show active" id="ventas" role="tabpanel" aria-labelledby="ventas-tab">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-primary text-white shadow">
                        <div class="card-body">
                            <h5 class="card-title">Ventas del Día</h5>
                            <h2 class="display-4 fw-bold"><?= fmt($data['day_sales']) ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-success text-white shadow">
                        <div class="card-body">
                            <h5 class="card-title">Ventas del Mes</h5>
                            <h2 class="display-4 fw-bold"><?= fmt($data['month_sales']) ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Ventas Últimos 7 Días</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="salesWeekChart" height="80"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Ventas por Método de Pago</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="paymentMethodChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Productos Más Vendidos (Top 5)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="bestSellersChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Inventario Tab -->
        <div class="tab-pane fade" id="inventario" role="tabpanel" aria-labelledby="inventario-tab">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-danger text-white shadow">
                        <div class="card-body">
                            <h5 class="card-title">Productos con Stock Bajo</h5>
                            <h2 class="display-4 fw-bold"><?= count($data['low_stock']) ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-warning text-dark shadow">
                        <div class="card-body">
                            <h5 class="card-title">Productos Agotados</h5>
                            <h2 class="display-4 fw-bold"><?= count($data['out_of_stock']) ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th>Stock Actual</th>
                                    <th>Stock Mínimo</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($data['low_stock']) && empty($data['out_of_stock'])): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4">No hay productos con stock bajo o agotados</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($data['low_stock'] as $item): ?>
                                        <tr class="table-danger">
                                            <td><?= h($item->nombre) ?></td>
                                            <td><?= h($item->categoria_nombre) ?></td>
                                            <td class="fw-danger"><?= $item->stock ?></td>
                                            <td><?= $item->stock_minimo ?></td>
                                            <td><span class="badge bg-danger">Stock Bajo</span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php foreach($data['out_of_stock'] as $item): ?>
                                        <tr class="table-dark">
                                            <td><?= h($item->nombre) ?></td>
                                            <td><?= h($item->categoria_nombre) ?></td>
                                            <td class="fw-dark">0</td>
                                            <td><?= $item->stock_minimo ?></td>
                                            <td><span class="badge bg-dark">Agotado</span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Compras Tab -->
        <div class="tab-pane fade" id="compras" role="tabpanel" aria-labelledby="compras-tab">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-info text-white shadow">
                        <div class="card-body">
                            <h5 class="card-title">Compras del Mes</h5>
                            <?php 
                            $this->db->query('SELECT SUM(total) as total FROM compras WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())');
                            $month_purchases = $this->db->single()->total ?? 0;
                            ?>
                            <h2 class="display-4 fw-bold"><?= fmt($month_purchases) ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light text-dark shadow">
                        <div class="card-body">
                            <h5 class="card-title">Compras Últimos 30 Días</h5>
                            <?php 
                            $this->db->query('SELECT SUM(total) as total FROM compras WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)');
                            $last30_purchases = $this->db->single()->total ?? 0;
                            ?>
                            <h2 class="display-4 fw-bold"><?= fmt($last30_purchases) ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Compras por Proveedor (Top 5)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="purchasesByProviderChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const colors = {
        primary: '#2563eb',
        success: '#059669',
        danger: '#dc2626',
        warning: '#d97706',
        info: '#0891b2'
    };
    
    const chartColors = [
        colors.primary, colors.success, colors.warning, 
        colors.danger, colors.info, '#7c3aed'
    ];
    
    const transparentColors = chartColors.map(c => c + '33');
    const borderColors = chartColors.map(c => c + 'CC');
    
    // Chart 1: Ventas últimos 7 días
    const salesWeekLabels = <?= json_encode(array_column($data['sales_week'], 'dia')) ?>;
    const salesWeekData = <?= json_encode(array_column($data['sales_week'], 'total')) ?>;
    new Chart(document.getElementById('salesWeekChart'), {
        type: 'line',
        data: {
            labels: salesWeekLabels.map(d => {
                const date = new Date(d);
                return date.toLocaleDateString('es-NI', { month: 'short', day: 'numeric' });
            }),
            datasets: [{
                label: 'Ventas',
                data: salesWeekData,
                borderColor: colors.primary,
                backgroundColor: colors.primary + '1A',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { callback: v => 'C$ ' + v.toLocaleString() } }
            }
        }
    });
    
    // Chart 2: Métodos de pago
    const paymentLabels = <?= json_encode(array_column($data['sales_by_payment'], 'metodo_pago')) ?>;
    const paymentData = <?= json_encode(array_column($data['sales_by_payment'], 'total')) ?>;
    new Chart(document.getElementById('paymentMethodChart'), {
        type: 'doughnut',
        data: {
            labels: paymentLabels,
            datasets: [{
                data: paymentData,
                backgroundColor: chartColors,
                borderColor: borderColors,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
    
    // Chart 3: Más vendidos
    const bestSellerLabels = <?= json_encode(array_column($data['best_sellers'], 'nombre')) ?>;
    const bestSellerData = <?= json_encode(array_column($data['best_sellers'], 'total_sold')) ?>;
    new Chart(document.getElementById('bestSellersChart'), {
        type: 'bar',
        data: {
            labels: bestSellerLabels,
            datasets: [{
                label: 'Cantidad Vendida',
                data: bestSellerData,
                backgroundColor: colors.success,
                borderColor: colors.success + 'CC'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
    
    // Chart 4: Compras por proveedor
    const providerLabels = <?= json_encode(array_column($data['purchases_by_provider'], 'proveedor')) ?>;
    const providerData = <?= json_encode(array_column($data['purchases_by_provider'], 'total')) ?>;
    new Chart(document.getElementById('purchasesByProviderChart'), {
        type: 'bar',
        data: {
            labels: providerLabels,
            datasets: [{
                label: 'Total Comprado',
                data: providerData,
                backgroundColor: colors.info,
                borderColor: colors.info + 'CC'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>