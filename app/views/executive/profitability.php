<?php require APPROOT . '/views/inc/header.php'; ?>


<main class="content p-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-primary"><i class="fa fa-dollar-sign me-2"></i> Rentabilidad</h2>
            <p class="text-muted">Análisis de Utilidad por Entidad</p>
        </div>
        <form method="GET" class="d-flex gap-2 align-items-center">
            <select name="entity" class="form-select">
                <option value="producto" <?= $data['entity'] == 'producto' ? 'selected' : '' ?>>Por Producto</option>
                <option value="categoria" <?= $data['entity'] == 'categoria' ? 'selected' : '' ?>>Por Categoría</option>
                <option value="vendedor" <?= $data['entity'] == 'vendedor' ? 'selected' : '' ?>>Por Vendedor</option>
                <option value="cliente" <?= $data['entity'] == 'cliente' ? 'selected' : '' ?>>Por Cliente</option>
            </select>
            <input type="date" name="start" class="form-control" value="<?= $data['start'] ?>">
            <input type="date" name="end" class="form-control" value="<?= $data['end'] ?>">
            <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i></button>
            <button type="button" class="btn btn-success" onclick="exportTableToCSV('rentabilidad.csv')"><i class="fa fa-file-excel"></i></button>
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

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0" id="profitabilityTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 text-uppercase text-muted fw-bold">Entidad (<?= ucfirst($data['entity']) ?>)</th>
                            <th class="text-uppercase text-muted fw-bold text-center">Cant. Vendida</th>
                            <th class="text-uppercase text-muted fw-bold text-end">Ventas Netas</th>
                            <th class="text-uppercase text-muted fw-bold text-end">Costo Total</th>
                            <th class="text-uppercase text-muted fw-bold text-end">Descuentos</th>
                            <th class="pe-4 text-uppercase text-muted fw-bold text-end text-success">Utilidad Neta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['report'])): ?>
                        <tr>
                            <td colspan="6" class="text-center p-4 text-muted">No hay datos en este período.</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach($data['report'] as $row): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-dark"><?= $row->nombre ?></td>
                                <td class="text-center"><?= number_format($row->cantidad_vendida) ?></td>
                                <td class="text-end">C$<?= number_format($row->ventas, 2) ?></td>
                                <td class="text-end text-danger">C$<?= number_format($row->costo, 2) ?></td>
                                <td class="text-end text-warning">C$<?= number_format($row->descuentos, 2) ?></td>
                                <td class="pe-4 text-end text-success fw-bold">C$<?= number_format($row->utilidad, 2) ?></td>
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

<script>
function downloadCSV(csv, filename) {
    var csvFile;
    var downloadLink;
    csvFile = new Blob(["\uFEFF"+csv], {type: "text/csv;charset=utf-8;"});
    downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
}

function exportTableToCSV(filename) {
    var csv = [];
    var rows = document.querySelectorAll("table tr");
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("td, th");
        for (var j = 0; j < cols.length; j++) 
            row.push('"' + cols[j].innerText.replace(/"/g, '""') + '"');
        csv.push(row.join(","));        
    }
    downloadCSV(csv.join("\n"), filename);
}
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>
