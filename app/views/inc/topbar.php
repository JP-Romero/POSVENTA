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
            <div class="dropdown position-relative">
                <button class="btn btn-outline-secondary btn-sm position-relative" id="notificationsToggle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notificaciones">
                    <i class="fa fa-bell"></i>
                    <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle" id="notificationBadge" style="display: none;">3</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notificationsToggle" style="min-width: 320px;">
                    <li class="dropdown-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Notificaciones</h6>
                        <a href="#" class="btn btn-sm btn-link p-0">Marcar todo</a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-start" href="#">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2 me-3">
                                <i class="fa fa-exclamation-triangle"></i>
                            </div>
                            <div>
                                <div class="fw-medium">Stock bajo detectado</div>
                                <small class="text-muted">5 productos por debajo del mínimo</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-start" href="#">
                            <div class="bg-info bg-opacity-10 text-info rounded-circle p-2 me-3">
                                <i class="fa fa-truck"></i>
                            </div>
                            <div>
                                <div class="fw-medium">Nueva compra registrada</div>
                                <small class="text-muted">Compra #C-0012 por C$ 15,000</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-start" href="#">
                            <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-3">
                                <i class="fa fa-user-plus"></i>
                            </div>
                            <div>
                                <div class="fw-medium">Nuevo cliente</div>
                                <small class="text-muted">Juan Pérez registrado</small>
                            </div>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-center" href="#">Ver todas las notificaciones</a></li>
                </ul>
            </div>
            
            <button class="btn btn-outline-secondary btn-sm" id="darkModeToggle" aria-label="Alternar modo oscuro" title="Modo oscuro">
                <i class="fa fa-moon"></i>
            </button>
            
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