<?php require APPROOT . '/views/inc/header.php'; ?>
  <div class="row mb-3">
    <div class="col-md-6">
      <h1>Usuarios</h1>
    </div>
    <div class="col-md-6 text-end">
      <a href="<?php echo URLROOT; ?>/users/add" class="btn btn-primary">
        <i class="fa fa-plus"></i> Nuevo Usuario
      </a>
    </div>
  </div>
  <?php flash('user_message'); ?>
  <div class="card card-body bg-light">
    <table id="users-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data['users'] as $user) : ?>
                <tr>
                    <td><?php echo $user->nombre; ?></td>
                    <td><?php echo $user->usuario; ?></td>
                    <td><?php echo $user->rol_nombre; ?></td>
                    <td>
                        <?php if($user->estado) : ?>
                            <span class="badge bg-success">Activo</span>
                        <?php else : ?>
                            <span class="badge bg-danger">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?php echo URLROOT; ?>/users/edit/<?php echo $user->id; ?>" class="btn btn-warning btn-sm">Editar</a>
                        <?php if($user->id != $_SESSION['user_id']) : ?>
                            <a href="<?php echo URLROOT; ?>/users/toggle/<?php echo $user->id; ?>" class="btn btn-<?php echo $user->estado ? 'secondary' : 'info'; ?> btn-sm">
                                <?php echo $user->estado ? 'Desactivar' : 'Activar'; ?>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
  </div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
