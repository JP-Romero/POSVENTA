<?php $currentPage = 'users'; require APPROOT . '/views/inc/header.php'; ?>
   <div class="row">
     <div class="col-md-8 mx-auto">
       <div class="card card-body bg-light mt-5">
         <h2>Crear Usuario</h2>
         <form action="<?php echo URLROOT; ?>/users/add" method="post">
           <div class="mb-3">
             <label for="nombre" class="form-label">Nombre:</label>
             <input type="text" name="nombre" class="form-control" required>
           </div>
           <div class="mb-3">
             <label for="usuario" class="form-label">Usuario:</label>
             <input type="text" name="usuario" class="form-control" required>
           </div>
           <div class="mb-3">
             <label for="password" class="form-label">Contraseña:</label>
             <input type="password" name="password" class="form-control" required>
           </div>
           <div class="mb-3">
             <label for="id_rol" class="form-label">Rol:</label>
             <select name="id_rol" id="id_rol" class="form-select" required>
                 <?php foreach($data['roles'] as $rol) : ?>
                     <option value="<?php echo $rol->id; ?>"><?= h($rol->nombre) ?></option>
                 <?php endforeach; ?>
             </select>
           </div>
           
           <?php if (!empty($data['modules'])): ?>
           <hr>
           <h5>Permisos de Acceso</h5>
           <p class="text-muted small">Configure el acceso a cada módulo del sistema para este usuario.</p>
           
           <div id="permissions-section" class="row g-3" style="display: none;">
               <?php foreach($data['modules'] as $modulo => $nombre): ?>
                   <div class="col-md-6 col-lg-4">
                       <div class="form-check form-switch">
                           <input type="checkbox" class="form-check-input" id="perm_<?= $modulo ?>" name="perm_<?= $modulo ?>" checked>
                           <label class="form-check-label" for="perm_<?= $modulo ?>"><?= h($nombre) ?></label>
                       </div>
                   </div>
               <?php endforeach; ?>
           </div>
           <?php endif; ?>
           
           <input type="submit" value="Guardar" class="btn btn-success w-100 mt-3">
         </form>
       </div>
     </div>
   </div>

<script>
document.getElementById('id_rol').addEventListener('change', function() {
    const permSection = document.getElementById('permissions-section');
    if (this.value != 1 && permSection) {
        permSection.style.display = 'flex';
    } else {
        permSection.style.display = 'none';
        permSection?.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = true);
    }
});
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>