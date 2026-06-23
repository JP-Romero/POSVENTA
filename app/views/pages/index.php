<?php $currentPage = 'dashboard'; require APPROOT . '/views/inc/header.php'; ?>
<div class="row mt-2">
    <div class="col-md-4 mb-4">
        <div class="card bg-primary text-white shadow">
            <div class="card-body">
                <h6 class="text-uppercase small">Ventas del Día</h6>
                <h2 class="fw-bold"><?= fmt($data['daily_sales']) ?></h2>
                <i class="fa fa-shopping-cart position-absolute bottom-0 end-0 p-3 opacity-25 fa-3x"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card bg-success text-white shadow">
            <div class="card-body">
                <h6 class="text-uppercase small">Productos en Stock</h6>
                <h2 class="fw-bold"><?= $data['total_products'] ?></h2>
                <i class="fa fa-boxes position-absolute bottom-0 end-0 p-3 opacity-25 fa-3x"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card bg-danger text-white shadow">
            <div class="card-body">
                <h6 class="text-uppercase small">Stock Bajo / Agotado</h6>
                <h2 class="fw-bold"><?= $data['low_stock'] ?></h2>
                <i class="fa fa-exclamation-triangle position-absolute bottom-0 end-0 p-3 opacity-25 fa-3x"></i>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Ventas Últimos 7 Días</h5>
                <small class="text-muted">Actualizado: <?= date('d/m/Y H:i') ?></small>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="80"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Top 5 Categorías</h5>
            </div>
            <div class="card-body">
                <canvas id="categoriesChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Ventas por Método de Pago (Hoy)</h5>
            </div>
            <div class="card-body">
                <canvas id="paymentChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Resumen Rápido</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="bg-light p-3 rounded">
                            <h6 class="text-muted mb-1">Ventas Mes</h6>
                            <h4 class="mb-0" id="monthSales">Cargando...</h4>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="bg-light p-3 rounded">
                            <h6 class="text-muted mb-1">Tickets Hoy</h6>
                            <h4 class="mb-0" id="todayTickets">Cargando...</h4>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="bg-light p-3 rounded">
                            <h6 class="text-muted mb-1">Promedio Ticket</h6>
                            <h4 class="mb-0" id="avgTicket">Cargando...</h4>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="bg-light p-3 rounded">
                            <h6 class="text-muted mb-1">Productos Vendidos</h6>
                            <h4 class="mb-0" id="productsSold">Cargando...</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Ventas Recientes</h5>
                <a href="<?= URLROOT ?>/sales" class="btn btn-sm btn-outline-primary">Ver todas</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Factura</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Método Pago</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['recent_sales'] as $sale) : ?>
                            <tr>
                                <td><?= $sale->numero_factura ?></td>
                                <td><?= $sale->cliente_nombre ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($sale->fecha)) ?></td>
                                <td><span class="badge bg-info text-dark"><?= $sale->metodo_pago ?></span></td>
                                <td class="text-end fw-bold"><?= fmt($sale->total) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Colores consistentes
    const colors = {
        primary: '#2563eb',
        success: '#059669',
        danger: '#dc2626',
        warning: '#d97706',
        info: '#0891b2',
        purple: '#7c3aed'
    };
    
    const chartColors = [
        colors.primary, colors.success, colors.warning, 
        colors.danger, colors.info, colors.purple
    ];
    
    const transparentColors = chartColors.map(c => c + '33');
    const borderColors = chartColors.map(c => c + 'CC');

    // Chart 1: Ventas últimos 7 días (Línea)
    new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: {
            labels: <?= $data['chart_sales_labels'] ?>,
            datasets: [{
                label: 'Ventas',
                data: <?= $data['chart_sales_data'] ?>,
                borderColor: colors.primary,
                backgroundColor: colors.primary + '1A',
                fill: true,
                tension: 0.3,
                pointBackgroundColor: colors.primary,
                pointBorderColor: '#fff',
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { callback: v => 'C$ ' + v.toLocaleString() } },
                x: { grid: { display: false } }
            }
        }
    });

    // Chart 2: Top 5 Categorías (Doughnut)
    new Chart(document.getElementById('categoriesChart'), {
        type: 'doughnut',
        data: {
            labels: <?= $data['chart_cat_labels'] ?>,
            datasets: [{
                data: <?= $data['chart_cat_data'] ?>,
                backgroundColor: chartColors,
                borderColor: borderColors,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { padding: 15, font: { size: 11 } } } }
        }
    });

    // Chart 3: Métodos de pago (Barra horizontal)
    new Chart(document.getElementById('paymentChart'), {
        type: 'bar',
        data: {
            labels: <?= $data['chart_pay_labels'] ?>,
            datasets: [{
                label: 'Total',
                data: <?= $data['chart_pay_data'] ?>,
                backgroundColor: chartColors,
                borderColor: borderColors,
                borderWidth: 2,
                borderRadius: 6
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, ticks: { callback: v => 'C$ ' + v.toLocaleString() } }
            }
        }
    });

    // Cargar resumen rápido via AJAX
    fetch('<?= URLROOT ?>/pages/apiDashboardSummary')
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                document.getElementById('monthSales').textContent = d.month_sales;
                document.getElementById('todayTickets').textContent = d.today_tickets;
                document.getElementById('avgTicket').textContent = d.avg_ticket;
                document.getElementById('productsSold').textContent = d.products_sold;
            }
        })
        .catch(() => {
            document.querySelectorAll('#monthSales, #todayTickets, #avgTicket, #productsSold')
                .forEach(el => el.textContent = '—');
        });
});
</script>
<?php require APPROOT . '/views/inc/footer.php'; ?>