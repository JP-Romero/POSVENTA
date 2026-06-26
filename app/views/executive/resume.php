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

    <!-- Ventas por Periodo -->
<div class="row g-3 mb-4">
<div class="col-12"><h5 class="fw-bold text-muted mb-3"><i class="fa fa-calendar-alt me-2"></i>Ventas por Periodo</h5></div>
<?php $periods = [
["l"=>"Hoy","d"=>$data["hoy"],"c"=>"linear-gradient(135deg,#667eea,#764ba2)"],
["l"=>"Semana","d"=>$data["semana"],"c"=>"linear-gradient(135deg,#f093fb,#f5576c)"],
["l"=>"Mes","d"=>$data["summary"],"c"=>"linear-gradient(135deg,#4facfe,#00f2fe)"],
["l"=>"Año","d"=>$data["anio"],"c"=>"linear-gradient(135deg,#43e97b,#38f9d7)"]
]; ?>
<?php foreach($periods as $p): ?>
<div class="col-6 col-md-3">
<div class="card border-0 shadow-sm rounded-4 h-100 text-white" style="background:<?=$p["c"]?>">
<div class="card-body p-3 text-center">
<small class="text-white-50 text-uppercase fw-bold"><?=$p["l"]?></small>
<h4 class="fw-bold text-white mb-0">C$<?=number_format($p["d"]["ventas_netas"],0)?></h4>
<small class="text-white-50"><?=$p["d"]["tickets"]?> tickets</small>
</div></div></div>
<?php endforeach; ?>
</div>

<!-- Indicadores Financieros -->
<div class="row g-3 mb-4">
<div class="col-12"><h5 class="fw-bold text-muted mb-3"><i class="fa fa-chart-line me-2"></i>Indicadores Financieros (Mes)</h5></div>
<?php $s = $data["summary"];
$cards = [
["l"=>"Ventas Netas","v"=>"C$".number_format($s["ventas_netas"],2),"s"=>"Ingresos reales","c"=>"linear-gradient(135deg,#0d6efd,#0a58ca)","i"=>"shopping-cart"],
["l"=>"Costo Ventas","v"=>"C$".number_format($s["costo_ventas"],2),"s"=>number_format($s["costo_porcentaje"],1)."% de ventas","c"=>"linear-gradient(135deg,#dc3545,#b02a37)","i"=>"coins"],
["l"=>"Utilidad Bruta","v"=>"C$".number_format($s["utilidad_bruta"],2),"s"=>"Ventas - Costos - Desc.","c"=>"linear-gradient(135deg,#198754,#146c43)","i"=>"chart-line"],
["l"=>"Margen Comercial","v"=>number_format($s["margen_comercial"],2)."%","s"=>"Ganancia / Ventas","c"=>"linear-gradient(135deg,#0dcaf0,#0aa2c0)","i"=>"percentage"]
];
foreach($cards as $c): ?>
<div class="col-xl-3 col-md-6">
<div class="card border-0 shadow-sm rounded-4 h-100 text-white" style="background:<?=$c["c"]?>">
<div class="card-body p-4">
<div class="d-flex justify-content-between align-items-center mb-3">
<h6 class="text-white-50 mb-0 fw-bold text-uppercase"><?=$c["l"]?></h6>
<div class="bg-white bg-opacity-25 rounded-circle p-2" style="width:40px;height:40px"><i class="fa fa-<?=$c["i"]?> text-white"></i></div></div>
<h3 class="fw-bold mb-1"><?=$c["v"]?></h3>
<p class="mb-0 text-white-50 small"><?=$c["s"]?></p>
</div></div></div>
<?php endforeach; ?>
</div>

<!-- Indicadores Secundarios -->
<div class="row g-3 mb-4">
<?php $sec = [
["i"=>"ticket-alt","c"=>"text-primary","v"=>"C$".number_format($s["ticket_promedio"],2),"l"=>"Ticket Promedio"],
["i"=>"users","c"=>"text-success","v"=>number_format($s["clientes_atendidos"]),"l"=>"Clientes Atendidos"],
["i"=>"box-open","c"=>"text-warning","v"=>number_format($s["productos_vendidos"]),"l"=>"Productos Vendidos"],
["i"=>"arrow-".($s["crecimiento"]>=0?"up":"down"),"c"=>$s["crecimiento"]>=0?"text-success":"text-danger",
"v"=>($s["crecimiento"]>=0?"+":"").number_format($s["crecimiento"],1)."%","l"=>"vs. Periodo Anterior"]
]; ?>
<?php foreach($sec as $s2): ?>
<div class="col-md-3">
<div class="card border-0 shadow-sm rounded-4 h-100">
<div class="card-body p-3 text-center">
<div class="<?=$s2["c"]?> mb-2"><i class="fa fa-<?=$s2["i"]?> fa-3x"></i></div>
<h2 class="fw-bold <?=$s2["c"]?>"><?=$s2["v"]?></h2>
<p class="text-muted mb-0 fw-bold text-uppercase small"><?=$s2["l"]?></p>
</div></div></div>
<?php endforeach; ?>
</div>

<!-- KPIs Rapidos -->
<div class="row g-3">
<div class="col-12"><h5 class="fw-bold text-muted mb-3"><i class="fa fa-bolt me-2"></i>KPIs Rapidos</h5></div>
<?php $kpis = [
["v"=>"C$".number_format($s["descuentos_totales"],0),"l"=>"Descuentos","c"=>"text-warning"],
["v"=>number_format($s["costo_porcentaje"],1)."%","l"=>"Costo %","c"=>"text-danger"],
["v"=>number_format($s["tickets"]),"l"=>"Tickets","c"=>"text-primary"],
["v"=>"C$".number_format($s["utilidad_neta"],0),"l"=>"Utilidad Neta","c"=>"text-success"]
]; ?>
<?php foreach($kpis as $k): ?>
<div class="col-6 col-md-3">
<div class="card border-0 shadow-sm rounded-4 bg-light">
<div class="card-body p-3 text-center">
<small class="text-muted text-uppercase fw-bold"><?=$k["l"]?></small>
<h4 class="fw-bold <?=$k["c"]?> mb-0"><?=$k["v"]?></h4>
</div></div></div>
<?php endforeach; ?>
</div></main>

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
