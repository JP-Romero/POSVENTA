<?php $currentPage = 'dashboard'; require APPROOT . '/views/inc/header.php'; ?>

<!-- Analytics Dashboard -->
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Panel de Control Analítico</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary">
                    Compartir
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                    <span data-feather="calendar"></span>
                    Esta semana
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="text-white-75 mb-1">Total Productos</h6>
                        </div>
                        <div class="icon-auto">
                            <i class="fa fa-boxes fa-2x"></i>
                        </div>
                    </div>
                    <div class="display-4 fw-bold mb-2"><?= number_format($total_products ?? 0) ?></div>
                    <div class="text-white-50 fs-6">Productos en inventario</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="text-white-75 mb-1">Stock Bajo</h6>
                        </div>
                        <div class="icon-auto">
                            <i class="fa fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                    <div class="display-4 fw-bold mb-2"><?= number_format($low_stock ?? 0) ?></div>
                    <div class="text-white-50 fs-6">Productos que requieren atención</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="text-white-75 mb-1">Ventas Diarias</h6>
                        </div>
                        <div class="icon-auto">
                            <i class="fa fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                    <div class="display-4 fw-bold mb-2"><?= number_format($daily_sales ?? 0, 0, ',', '.') ?></div>
                    <div class="text-white-50 fs-6">Ingresos de hoy</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="text-white-75 mb-1">Ventas Recientes</h6>
                        </div>
                        <div class="icon-auto">
                            <i class="fa fa-clock-rotate-left fa-2x"></i>
                        </div>
                    </div>
                    <div class="fs-5 fw-medium"><?= count($recent_sales ?? []) ?></div>
                    <div class="text-white-50 fs-6">Últimas transacciones</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Sales Trend -->
        <div class="col-xl-6 col-lg-6">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Tendencia de Ventas (Últimos 7 días)</h6>
                </div>
                <div class="card-body p-3">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
        <!-- Categories -->
        <div class="col-xl-6 col-lg-6">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Top 5 Categorías Más Vendidas</h6>
                </div>
                <div class="card-body p-3">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Distribución de Métodos de Pago (Hoy)</h6>
                </div>
                <div class="card-body p-3">
                    <canvas id="paymentChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Sales Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Ventas Recientes</h6>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="fs--1 text-uppercase ls-1">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Cliente</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Método Pago</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recent_sales)): ?>
                                    <?php foreach ($recent_sales as $index => $sale): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($sale->fecha)) ?></td>
                                            <td><?= htmlspecialchars($sale->cliente_nombre) ?></td>
                                            <td><?= number_format($sale->total, 0, ',', '.') ?></td>
                                            <td>
                                                <span class="badge bg-<?= 
                                                    $sale->metodo_pago === 'Efectivo' ? 'success' :
                                                    ($sale->metodo_pago === 'Tarjeta' ? 'primary' : 'info') 
                                                ?>">
                                                    <?= htmlspecialchars($sale->metodo_pago) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4">No hay ventas recientes</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sales Trend Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: <?php echo $chart_sales_labels ?? '[]'; ?>,
                datasets: [{
                    label: 'Ventas ($)',
                    data: <?php echo $chart_sales_data ?? '[]'; ?>,
                    borderColor: '#4361ee',
                    backgroundColor: 'rgba(67, 97, 238, 0.1)',
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: '#4361ee',
                    pointBorderColor: '#fff',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        },
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5],
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Category Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        const categoryChart = new Chart(categoryCtx, {
            type: 'bar',
            data: {
                labels: <?php echo $chart_cat_labels ?? '[]'; ?>,
                datasets: [{
                    label: 'Unidades Vendidas',
                    data: <?php echo $chart_cat_data ?? '[]'; ?>,
                    backgroundColor: [
                        '#4cc9f0',
                        '#f72585',
                        '#4cc9f0',
                        '#f72585',
                        '#4cc9f0'
                    ],
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5],
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Payment Method Chart
        const paymentCtx = document.getElementById('paymentChart').getContext('2d');
        const paymentChart = new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo $chart_pay_labels ?? '[]'; ?>,
                datasets: [{
                    label: 'Métodos de Pago',
                    data: <?php echo $chart_pay_data ?? '[]'; ?>,
                    backgroundColor: [
                        '#4cc9f0',
                        '#f72585',
                        '#4361ee',
                        '#f8961e'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    let value = context.formattedValue;
                                    label += '$' + value.toLocaleString();
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });
</script>