<?php require APPROOT . '/views/inc/header.php'; ?>


<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<main class="content p-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-primary"><i class="fa fa-chart-line me-2"></i> Tendencias</h2>
            <p class="text-muted">Gráficos Financieros y Evolución del Negocio</p>
        </div>
        <form id="filterForm" class="d-flex gap-2">
            <input type="date" name="start" id="start" class="form-control" value="<?= $data['start'] ?>">
            <input type="date" name="end" id="end" class="form-control" value="<?= $data['end'] ?>">
            <button type="submit" class="btn btn-primary"><i class="fa fa-sync-alt"></i> Actualizar</button>
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

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4">Comparativa de Ventas, Costos y Utilidad Bruta</h5>
            <div style="height: 400px; width: 100%;">
                <canvas id="trendsChart"></canvas>
            </div>
        </div>
    </div>
</main>

<style>
.executive-tabs .nav-link { color: #495057; border-radius: 8px; font-weight: 500; }
.executive-tabs .nav-link.active { background-color: #f8f9fa; color: #0d6efd; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
.nav-pills .nav-link:hover { background-color: #f1f3f5; }
</style>

<script>
let trendsChart = null;

async function loadChartData() {
    const start = document.getElementById('start').value;
    const end = document.getElementById('end').value;
    
    try {
        const response = await fetch(`<?= URLROOT ?>/executive/apiChartData?start=${start}&end=${end}`);
        const data = await response.json();

        if (trendsChart) {
            trendsChart.destroy();
        }

        const ctx = document.getElementById('trendsChart').getContext('2d');
        trendsChart = new Chart(ctx, {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                if (context.parsed.y !== null) label += 'C$' + context.parsed.y.toLocaleString();
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    } catch (e) {
        console.error("Error loading chart data", e);
    }
}

document.getElementById('filterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    loadChartData();
});

// Load on init
document.addEventListener('DOMContentLoaded', loadChartData);
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>
