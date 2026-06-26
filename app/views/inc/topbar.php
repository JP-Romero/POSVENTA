<header id="topbar" class="topbar no-sidebar" role="banner">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <a href="<?= URLROOT ?>" class="btn btn-primary btn-sm d-flex align-items-center gap-2 fw-semibold" title="Ir al inicio">
                <i class="fa fa-home"></i>
                <span class="d-none d-sm-inline">Inicio</span>
            </a>
            <h4 class="mb-0 fw-bold text-primary d-none d-sm-block">POSVENTA</h4>
        </div>
        
        <div class="d-flex align-items-center gap-2">
            <div id="clockDisplay" class="fw-bold" style="font-family: monospace; font-size: 1.1rem;"></div>
            <?php
            $dbNotification = new Database;
            // Obtener productos agotados (stock <= 0)
            $dbNotification->query("SELECT id, nombre, codigo_barras FROM productos WHERE stock <= 0 AND estado = 1 LIMIT 5");
            $agotados = $dbNotification->resultSet();
            $totalRealAgotados = count($agotados);
            if ($totalRealAgotados > 0) {
                // Obtener total real si supera el límite de 5
                $dbNotification->query("SELECT COUNT(*) as total FROM productos WHERE stock <= 0 AND estado = 1");
                $totalRealAgotados = $dbNotification->single()->total;
            }
            ?>
            <div class="dropdown position-relative">
                <button class="btn btn-outline-secondary btn-sm position-relative" id="notificationsToggle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notificaciones">
                    <i class="fa fa-bell"></i>
                    <?php if($totalRealAgotados > 0): ?>
                    <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle" id="notificationBadge"><?= $totalRealAgotados ?></span>
                    <?php endif; ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notificationsToggle" style="min-width: 320px;">
                    <li class="dropdown-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Productos Agotados</h6>
                        <?php if($totalRealAgotados > 0): ?>
                        <span class="badge bg-danger"><?= $totalRealAgotados ?> Alertas</span>
                        <?php endif; ?>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <?php if($totalRealAgotados > 0): ?>
                        <?php foreach($agotados as $prod): ?>
                        <li>
                            <a class="dropdown-item d-flex align-items-start" href="<?= URLROOT ?>/products">
                                <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="fa fa-exclamation-circle"></i>
                                </div>
                                <div>
                                    <div class="fw-medium text-wrap" style="max-width: 220px; font-size: 0.9rem;"><?= htmlspecialchars($prod->nombre) ?></div>
                                    <small class="text-muted">Cód: <?= htmlspecialchars($prod->codigo_barras) ?> | Sin stock</small>
                                </div>
                            </a>
                        </li>
                        <?php endforeach; ?>
                        <?php if($totalRealAgotados > 5): ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center text-primary fw-semibold py-2" href="<?= URLROOT ?>/products">Ver los <?= $totalRealAgotados ?> productos</a></li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li>
                            <div class="dropdown-item text-center text-muted py-3">
                                <i class="fa fa-check-circle text-success fs-4 mb-2 d-block"></i>
                                Todo al día.<br>No hay productos agotados.
                            </div>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            
            
            <div class="dropdown ms-2">
                <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <?= strtoupper(substr($_SESSION['user_nombre'] ?? 'U', 0, 1)) ?>
                    </div>
                    <span class="d-none d-sm-inline"><?= $_SESSION['user_nombre'] ?? 'Usuario' ?></span>
                    <i class="fa fa-chevron-down d-none d-sm-inline"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                    <li class="dropdown-header">
                        <div class="fw-medium"><?= $_SESSION['user_nombre'] ?? 'Usuario' ?></div>
                        <small class="text-muted"><?= ($_SESSION['user_rol'] ?? 2) == 1 ? 'Administrador' : 'Cajero' ?></small>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?= URLROOT ?>/users/logout"><i class="fa fa-sign-out-alt me-2"></i> Cerrar sesión</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>