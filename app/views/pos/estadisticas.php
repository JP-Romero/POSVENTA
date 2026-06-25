<?php require APPROOT . '/views/inc/header.php'; ?>

<style>
/* Dark theme for Estadisticas */
.stats-container {
    background-color: var(--dark-bg-primary, #1a1a1a);
    color: var(--dark-text-primary, #e0e0e0);
    min-height: calc(100vh - 60px);
    padding: 2rem;
    font-family: 'Inter', sans-serif;
}
.stats-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}
.stats-title {
    font-weight: 700;
    font-size: 1.5rem;
    color: var(--dark-text-inverse, #fff);
    margin: 0;
}
.stats-tabs {
    display: flex;
    gap: 0.5rem;
    background: var(--dark-bg-secondary, #222);
    padding: 0.5rem;
    border-radius: 8px;
    overflow-x: auto;
}
.stats-tab {
    background: transparent;
    border: none;
    color: var(--dark-text-muted, #aaa);
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
}
.stats-tab.active {
    background: var(--bs-danger, #e74c3c);
    color: #fff;
}
.stats-tab:hover:not(.active) {
    background: var(--dark-bg-tertiary, #333);
    color: var(--dark-text-inverse, #fff);
}
.stats-card {
    background: var(--dark-bg-tertiary, #252525);
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.3);
}
.stats-card-title {
    color: var(--bs-warning, #f39c12);
    font-weight: bold;
    font-size: 1.1rem;
    margin-bottom: 1rem;
    text-transform: uppercase;
    border-bottom: 1px solid var(--dark-border-color, #444);
    padding-bottom: 0.5rem;
}
.stats-row {
    display: flex;
    justify-content: space-between;
    padding: 0.8rem 0;
    border-bottom: 1px solid var(--dark-border-subtle, #333);
}
.stats-row:last-child {
    border-bottom: none;
}
.stats-val {
    color: var(--bs-success, #2ecc71);
    font-weight: bold;
}
.btn-print {
    background: var(--bs-primary, #3498db);
    color: white;
    border: none;
    padding: 0.6rem 1.2rem;
    border-radius: 4px;
    font-weight: bold;
}
.btn-print:hover {
    background: var(--bs-primary-darker, #2980b9);
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
                <a class="nav-link" href="<?= URLROOT ?>/cierre">Cierre de Caja</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="<?= URLROOT ?>/estadisticas">Estadísticas</a>
            </li>
        </ul>
    </div>
    <div class="card-body p-0">
        <div class="stats-container">
            <div class="stats-header">
        <h1 class="stats-title">ESTADÍSTICAS</h1>
        <button class="btn-print" id="btn-imprimir">
            <i class="fas fa-print"></i> Imprimir Reporte
        </button>
    </div>

    <div class="stats-tabs mb-4" id="stats-tabs">
        <button class="stats-tab active" data-periodo="hoy">Hoy</button>
        <button class="stats-tab" data-periodo="semana">Semana</button>
        <button class="stats-tab" data-periodo="mes">Mes</button>
        <button class="stats-tab" data-periodo="3meses">3 Meses</button>
        <button class="stats-tab" data-periodo="6meses">6 Meses</button>
        <button class="stats-tab" data-periodo="anio">1 Año</button>
    </div>

    <div class="row">
        <!-- Resumen General -->
        <div class="col-md-6">
            <div class="stats-card">
                <div class="stats-card-title">RESUMEN GENERAL</div>
                <div class="stats-row">
                    <span>Total recaudado:</span>
                    <span class="stats-val" id="val-total">$0.00</span>
                </div>
                <div class="stats-row">
                    <span>Tickets cobrados:</span>
                    <span class="stats-val text-warning" id="val-tickets">0</span>
                </div>
                <div class="stats-row">
                    <span>Piezas vendidas:</span>
                    <span class="stats-val text-info" id="val-piezas">0</span>
                </div>
            </div>

            <!-- Categorias -->
            <div class="stats-card">
                <div class="stats-card-title">VENTAS POR CATEGORÍA</div>
                <div id="categorias-container">
                    <!-- Dynamic content -->
                </div>
            </div>
        </div>

        <!-- Mas Vendidos -->
        <div class="col-md-6">
            <div class="stats-card">
                <div class="stats-card-title">MÁS VENDIDOS</div>
                <div id="mas-vendidos-container">
                    <!-- Dynamic content -->
                </div>
            </div>
        </div>
    </div>
</div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPeriodo = 'hoy';

    function loadStats(periodo) {
        fetch(`<?= URLROOT ?>/estadisticas/obtenerDatos?periodo=${periodo}`)
            .then(res => res.json())
            .then(data => {
                // Update Resumen
                document.getElementById('val-total').textContent = '$' + parseFloat(data.resumen.total_recaudado).toFixed(2);
                document.getElementById('val-tickets').textContent = data.resumen.tickets_cobrados;
                document.getElementById('val-piezas').textContent = data.resumen.piezas_vendidas;

                // Update Categorias
                const catContainer = document.getElementById('categorias-container');
                catContainer.innerHTML = '';
                if(data.categorias && data.categorias.length > 0) {
                    data.categorias.forEach(cat => {
                        catContainer.innerHTML += `
                            <div class="stats-row">
                                <span>${cat.categoria} (${cat.piezas} pzas):</span>
                                <span class="stats-val">$${parseFloat(cat.total).toFixed(2)}</span>
                            </div>
                        `;
                    });
                } else {
                    catContainer.innerHTML = '<div class="text-muted py-2">No hay datos</div>';
                }

                // Update Mas Vendidos
                const prodContainer = document.getElementById('mas-vendidos-container');
                prodContainer.innerHTML = '';
                if(data.mas_vendidos && data.mas_vendidos.length > 0) {
                    data.mas_vendidos.forEach(prod => {
                        prodContainer.innerHTML += `
                            <div class="stats-row flex-column align-items-start">
                                <div>${prod.producto}</div>
                                <div class="w-100 d-flex justify-content-between mt-1" style="font-size:0.85rem">
                                    <span class="text-warning">${prod.cantidad} pzas</span>
                                    <span class="stats-val">$${parseFloat(prod.total).toFixed(2)}</span>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    prodContainer.innerHTML = '<div class="text-muted py-2">No hay datos</div>';
                }
            });
    }

    // Tab clicks
    const tabs = document.querySelectorAll('.stats-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            currentPeriodo = this.dataset.periodo;
            loadStats(currentPeriodo);
        });
    });

    // Initial load
    loadStats(currentPeriodo);

    // TODO: Implement Print functionality connecting to a controller endpoint
    document.getElementById('btn-imprimir').addEventListener('click', function() {
        Swal.fire({ icon: 'info', title: 'Impresión', text: 'Funcionalidad de impresión en desarrollo...' });
        // Here we would call an endpoint like /estadisticas/imprimir?periodo=hoy
    });
});
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>
