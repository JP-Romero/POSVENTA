<aside id="sidebar" class="sidebar" role="navigation" aria-label="Menú principal">
    <div class="sidebar-header">
        <a href="<?= URLROOT ?>" class="sidebar-brand d-flex align-items-center px-3">
            <i class="fa fa-store-alt me-2 fs-4" style="color: #fbbf24;"></i>
            <span class="fw-bold fs-5">POSVENTA</span>
        </a>
        <button class="btn btn-sm btn-outline-light ms-auto me-2 d-md-none" id="sidebarClose" aria-label="Cerrar menú">
            <i class="fa fa-times"></i>
        </button>
    </div>
    
    <div class="sidebar-divider"></div>
    
    <nav class="sidebar-nav flex-grow-1 px-2">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= (isset($currentPage) && $currentPage === 'dashboard') ? 'active' : '' ?>" href="<?= URLROOT ?>/pages/dashboard">
                    <i class="fa fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <?php if(isAdmin()) : ?>
            <li class="nav-item">
                <a class="nav-link <?= (isset($currentPage) && $currentPage === 'users') ? 'active' : '' ?>" href="<?= URLROOT ?>/users">
                    <i class="fa fa-users-cog"></i>
                    <span>Usuarios</span>
                </a>
            </li>
            <?php endif; ?>
            
            <li class="nav-item">
                <a class="nav-link <?= (isset($currentPage) && $currentPage === 'categories') ? 'active' : '' ?>" href="<?= URLROOT ?>/categories">
                    <i class="fa fa-tags"></i>
                    <span>Categorías</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= (isset($currentPage) && $currentPage === 'products') ? 'active' : '' ?>" href="<?= URLROOT ?>/products">
                    <i class="fa fa-box"></i>
                    <span>Productos</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= (isset($currentPage) && $currentPage === 'providers') ? 'active' : '' ?>" href="<?= URLROOT ?>/providers">
                    <i class="fa fa-truck"></i>
                    <span>Proveedores</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= (isset($currentPage) && $currentPage === 'clients') ? 'active' : '' ?>" href="<?= URLROOT ?>/clients">
                    <i class="fa fa-users"></i>
                    <span>Clientes</span>
                </a>
            </li>
            
            <?php if(isAdmin()) : ?>
            <li class="nav-item">
                <a class="nav-link <?= (isset($currentPage) && $currentPage === 'purchases') ? 'active' : '' ?>" href="<?= URLROOT ?>/purchases">
                    <i class="fa fa-shopping-cart"></i>
                    <span>Compras</span>
                </a>
            </li>
            <?php endif; ?>
            
            <li class="nav-item">
                <a class="nav-link dropdown-toggle <?= (isset($currentPage) && in_array($currentPage, ['inventories', 'kardex'])) ? 'active' : '' ?>" 
                   href="#" id="inventoryDropdown" role="button" data-bs-toggle="collapse" data-bs-target="#inventoryCollapse" aria-expanded="false">
                    <i class="fa fa-warehouse"></i>
                    <span>Inventario</span>
                    <i class="fa fa-chevron-down ms-auto"></i>
                </a>
                <div class="collapse" id="inventoryCollapse">
                    <ul class="nav flex-column ps-4">
                        <li class="nav-item">
                            <a class="nav-link <?= (isset($currentPage) && $currentPage === 'inventories') ? 'active' : '' ?>" href="<?= URLROOT ?>/inventories">
                                <i class="fa fa-boxes"></i>
                                <span>Existencias</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (isset($currentPage) && $currentPage === 'kardex') ? 'active' : '' ?>" href="<?= URLROOT ?>/inventories/kardex">
                                <i class="fa fa-list-alt"></i>
                                <span>Kardex</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= (isset($currentPage) && $currentPage === 'pos') ? 'active' : '' ?>" href="<?= URLROOT ?>/pos">
                    <i class="fa fa-cash-register"></i>
                    <span>Punto de Venta</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= (isset($currentPage) && $currentPage === 'caja') ? 'active' : '' ?>" href="<?= URLROOT ?>/caja">
                    <i class="fa fa-lock"></i>
                    <span>Cierre de Caja</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= (isset($currentPage) && $currentPage === 'estadisticas') ? 'active' : '' ?>" href="<?= URLROOT ?>/estadisticas">
                    <i class="fa fa-chart-line"></i>
                    <span>Estadísticas</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= (isset($currentPage) && $currentPage === 'sales') ? 'active' : '' ?>" href="<?= URLROOT ?>/sales">
                    <i class="fa fa-receipt"></i>
                    <span>Ventas</span>
                </a>
            </li>
            
            <?php if(isAdmin()) : ?>
            <li class="nav-item">
                <a class="nav-link <?= (isset($currentPage) && $currentPage === 'reports') ? 'active' : '' ?>" href="<?= URLROOT ?>/reports">
                    <i class="fa fa-chart-bar"></i>
                    <span>Reportes</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= (isset($currentPage) && $currentPage === 'settings') ? 'active' : '' ?>" href="<?= URLROOT ?>/settings">
                    <i class="fa fa-cog"></i>
                    <span>Configuración</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
    
    <div class="sidebar-footer p-3">
        <div class="user-info d-flex align-items-center">
            <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                <?= strtoupper(substr($_SESSION['user_nombre'] ?? 'U', 0, 1)) ?>
            </div>
            <div class="flex-grow-1 min-w-0">
                <div class="fw-medium text-truncate" style="font-size: 0.95rem;"><?= $_SESSION['user_nombre'] ?? 'Usuario' ?></div>
                <small class="text-muted"><?= ($_SESSION['user_rol'] ?? 2) == 1 ? 'Administrador' : 'Cajero' ?></small>
            </div>
        </div>
        <div class="mt-3 d-grid">
            <a href="<?= URLROOT ?>/users/logout" class="btn btn-outline-danger btn-sm">
                <i class="fa fa-sign-out-alt me-1"></i> Salir
            </a>
        </div>
    </div>
</aside>

<div class="sidebar-overlay d-md-none" id="sidebarOverlay" aria-hidden="true"></div>

<script>
// Sidebar toggle
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const closeBtn = document.getElementById('sidebarClose');
    
    // Close sidebar on overlay click
    overlay?.addEventListener('click', function() {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
        document.body.classList.remove('sidebar-open');
    });
    
    // Close sidebar on close button
    closeBtn?.addEventListener('click', function() {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
        document.body.classList.remove('sidebar-open');
    });
});
</script>