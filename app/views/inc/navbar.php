<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-3">
  <div class="container">
      <a class="navbar-brand" href="<?php echo URLROOT; ?>"><?php echo SITENAME; ?></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link" href="<?php echo URLROOT; ?>">Inicio</a>
          </li>
          <?php if(isset($_SESSION['user_id'])) : ?>
            <?php if(isAdmin()) : ?>
              <li class="nav-item">
                <a class="nav-link" href="<?php echo URLROOT; ?>/users">Usuarios</a>
              </li>
            <?php endif; ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo URLROOT; ?>/categories">Categorías</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo URLROOT; ?>/providers">Proveedores</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo URLROOT; ?>/clients">Clientes</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo URLROOT; ?>/products">Productos</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo URLROOT; ?>/purchases">Compras</a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="inventoryDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Inventario
              </a>
              <ul class="dropdown-menu" aria-labelledby="inventoryDropdown">
                <li><a class="dropdown-item" href="<?php echo URLROOT; ?>/inventories">Existencias</a></li>
                <li><a class="dropdown-item" href="<?php echo URLROOT; ?>/inventories/kardex">Kardex</a></li>
              </ul>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo URLROOT; ?>/pos">POS</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo URLROOT; ?>/sales">Ventas</a>
            </li>
            <?php if(isAdmin()) : ?>
              <li class="nav-item">
                <a class="nav-link" href="<?php echo URLROOT; ?>/reports">Reportes</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="<?php echo URLROOT; ?>/settings">Configuración</a>
              </li>
            <?php endif; ?>
          <?php endif; ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo URLROOT; ?>/pages/about">Sobre Nosotros</a>
          </li>
        </ul>

        <ul class="navbar-nav ms-auto align-items-center">
          <li class="nav-item me-3">
              <button id="theme-toggle" class="btn btn-outline-light btn-sm rounded-circle">
                  <i class="fa fa-moon"></i>
              </button>
          </li>
          <?php if(isset($_SESSION['user_id'])) : ?>
            <li class="nav-item">
              <span class="nav-link text-white">Bienvenido, <?php echo $_SESSION['user_nombre']; ?></span>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo URLROOT; ?>/users/logout">Salir</a>
            </li>
          <?php else : ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo URLROOT; ?>/users/login">Login</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
