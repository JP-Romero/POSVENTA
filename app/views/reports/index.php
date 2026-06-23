<?php require APPROOT . '/views/inc/header.php'; ?>
  <h1>Reportes y Estadísticas</h1>

  <div class="row mt-4">
    <div class="col-md-6 mb-4">
        <div class="card bg-primary text-white shadow">
            <div class="card-body">
                <h5 class="card-title">Ventas del Día</h5>
                <h2 class="display-4 fw-bold">$<?php echo number_format($data['day_sales'], 2); ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card bg-success text-white shadow">
            <div class="card-body">
                <h5 class="card-title">Ventas del Mes</h5>
                <h2 class="display-4 fw-bold">$<?php echo number_format($data['month_sales'], 2); ?></h2>
            </div>
        </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
        <div class="card card-body bg-light">
            <h5 class="mb-4">Productos Más Vendidos</h5>
            <canvas id="bestSellersChart"></canvas>
        </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const ctx = document.getElementById('bestSellersChart');
    const data = <?php echo json_encode($data['best_sellers']); ?>;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(item => item.nombre),
            datasets: [{
                label: 'Cantidad Vendida',
                data: data.map(item => item.total_sold),
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
  </script>
<?php require APPROOT . '/views/inc/footer.php'; ?>
