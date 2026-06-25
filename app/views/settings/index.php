<?php $currentPage = 'settings'; require APPROOT . '/views/inc/header.php'; ?>
<?php flash('settings_message'); ?>

<div class="row">
    <div class="col-12">
        <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $data['activeTab'] === 'general' ? 'active' : '' ?>" 
                        id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                    <i class="fa fa-cog me-1"></i> General
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $data['activeTab'] === 'impresoras' ? 'active' : '' ?>" 
                        id="impresoras-tab" data-bs-toggle="tab" data-bs-target="#impresoras" type="button" role="tab">
                    <i class="fa fa-print me-1"></i> Impresoras
                </button>
            </li>
        </ul>
        
        <div class="tab-content" id="settingsTabsContent">
            <!-- General Tab -->
            <div class="tab-pane fade <?= $data['activeTab'] === 'general' ? 'show active' : '' ?>" id="general" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Configuración General</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?= URLROOT ?>/settings" method="POST" enctype="multipart/form-data">
                            <?= csrfField() ?>
                            <input type="hidden" name="action" value="update_general">
                            
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Logo</label>
                                <div class="col-sm-10">
                                    <?php if ($data['settings']->logo): ?>
                                        <div class="mb-2">
                                            <img src="<?= URLROOT ?>/img/logo/<?= $data['settings']->logo ?>" alt="Logo" style="max-height: 80px;" class="border rounded">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="logo" class="form-control" accept="image/*">
                                    <div class="form-text">Formatos: JPG, PNG, WebP. Máx 2MB.</div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <label for="nombre_negocio" class="col-sm-2 col-form-label">Nombre del Negocio <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="nombre_negocio" name="nombre_negocio" value="<?= h($data['settings']->nombre_negocio) ?>" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <label for="ruc" class="col-sm-2 col-form-label">RUC</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="ruc" name="ruc" value="<?= h($data['settings']->ruc) ?>">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <label for="direccion" class="col-sm-2 col-form-label">Dirección</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" id="direccion" name="direccion" rows="2"><?= h($data['settings']->direccion) ?></textarea>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <label for="telefono" class="col-sm-2 col-form-label">Teléfono</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="telefono" name="telefono" value="<?= h($data['settings']->telefono) ?>">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <label for="correo" class="col-sm-2 col-form-label">Correo</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" id="correo" name="correo" value="<?= h($data['settings']->correo) ?>">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <label for="iva" class="col-sm-2 col-form-label">IVA (%) <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="number" step="0.01" min="0" max="100" class="form-control" id="iva" name="iva" value="<?= $data['settings']->iva ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-sm-10 offset-sm-2">
                                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Impresoras Tab -->
            <div class="tab-pane fade <?= $data['activeTab'] === 'impresoras' ? 'show active' : '' ?>" id="impresoras" role="tabpanel">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Impresoras Configuradas</h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#printerModal">
                            <i class="fa fa-plus me-1"></i> Agregar Impresora
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Tipo</th>
                                        <th>Conexión</th>
                                        <th>Ancho</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['printers'] as $p): ?>
                                    <tr>
                                        <td><?= h($p->nombre) ?></td>
                                        <td><span class="badge bg-info"><?= h($p->tipo) ?></span></td>
                                        <td><code><?= h($p->conexion) ?></code></td>
                                        <td><?= (int)$p->ancho_papel ?> mm</td>
                                        <td>
                                            <?php if ($p->activa): ?>
                                                <span class="badge bg-success">Activa</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inactiva</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="editPrinter(<?= htmlspecialchars(json_encode($p)) ?>)">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-success" onclick="testPrinter(<?= $p->id ?>)">
                                                <i class="fa fa-print"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger delete-printer" data-id="<?= $p->id ?>" data-name="<?= h($p->nombre) ?>">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($data['printers'])): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No hay impresoras configuradas</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Ayuda -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Guía de Configuración</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h6><i class="fa fa-usb text-primary me-2"></i> USB (Linux/Mac)</h6>
                                <p class="small text-muted">Ruta del dispositivo: <code>/dev/usb/lp0</code> o <code>/dev/ttyUSB0</code></p>
                            </div>
                            <div class="col-md-4">
                                <h6><i class="fa fa-wifi text-success me-2"></i> Red (Ethernet/WiFi)</h6>
                                <p class="small text-muted">IP y puerto: <code>192.168.1.100:9100</code></p>
                            </div>
                            <div class="col-md-4">
                                <h6><i class="fa fa-bluetooth text-info me-2"></i> Windows</h6>
                                <p class="small text-muted">Nombre compartido: <code>EPSON TM-T20II</code></p>
                            </div>
                        </div>
                        <hr>
                        <h6>Notas importantes:</h6>
                        <ul class="small text-muted mb-0">
                            <li>En Windows, comparte la impresora y usa el nombre de compartición</li>
                            <li>En Linux, asegúrate de que el usuario www-data tenga permisos en /dev/usb/lp*</li>
                            <li>Solo una impresora puede estar "Activa" a la vez</li>
                            <li>Ancho de papel: 58mm (tickets) u 80mm (facturas)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Printer Modal -->
<div class="modal fade" id="printerModal" tabindex="-1" aria-labelledby="printerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= URLROOT ?>/settings?tab=impresoras" method="POST" id="printerForm">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="add_printer" id="printerAction">
                <input type="hidden" name="imp_id" id="printerId">
                <div class="modal-header">
                    <h5 class="modal-title" id="printerModalLabel">Agregar Impresora</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="imp_nombre" id="imp_nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo <span class="text-danger">*</span></label>
                        <select class="form-select" name="imp_tipo" id="imp_tipo" required>
                            <option value="USB">USB (Archivo/Dispositivo)</option>
                            <option value="RED">Red (IP:Puerto)</option>
                            <option value="WINDOWS">Windows (Impresora Compartida)</option>
                            <option value="BLUETOOTH">Bluetooth</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Conexión <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="imp_conexion" id="imp_conexion" required placeholder="Ej: /dev/usb/lp0, 192.168.1.100:91:9100, NombreImpresora">
                        <div class="form-text" id="conexionHelp">Ruta del dispositivo, IP:puerto, o nombre de impresora compartida</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Ancho Papel</label>
                            <select class="form-select" name="imp_ancho" id="imp_ancho">
                                <option value="58">58 mm</option>
                                <option value="80">80 mm</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="imp_activa" id="imp_activa" value="1">
                                <label class="form-check-label" for="imp_activa">Impresora predeterminada (activa)</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editPrinter(p) {
    document.getElementById('printerModalLabel').textContent = 'Editar Impresora';
    document.getElementById('printerAction').value = 'update_printer';
    document.getElementById('printerId').value = p.id;
    document.getElementById('imp_nombre').value = p.nombre;
    document.getElementById('imp_tipo').value = p.tipo;
    document.getElementById('imp_conexion').value = p.conexion;
    document.getElementById('imp_ancho').value = p.ancho_papel;
    document.getElementById('imp_activa').checked = p.activa == 1;
    
    new bootstrap.Modal(document.getElementById('printerModal')).show();
}

function testPrinter(id) {
    const btn = event.target.closest('button');
    const original = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
    btn.disabled = true;
    
    const formData = new FormData();
    formData.append('action', 'test_printer');
    formData.append('imp_id', id);
    
    fetch('<?= URLROOT ?>/settings?tab=impresoras', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        btn.innerHTML = original;
        btn.disabled = false;
        if (res.success) {
            alert('✅ ' + res.message);
        } else {
            alert('❌ ' + res.message);
        }
    })
    .catch(() => {
        btn.innerHTML = original;
        btn.disabled = false;
        alert('❌ Error de conexión');
    });
}

// Reset modal on close
document.getElementById('printerModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('printerModalLabel').textContent = 'Agregar Impresora';
    document.getElementById('printerAction').value = 'add_printer';
    document.getElementById('printerId').value = '';
    document.getElementById('printerForm').reset();
});

// Delete printer confirmation
document.addEventListener('click', function(e) {
    if (e.target.closest('.delete-printer')) {
        const btn = e.target.closest('.delete-printer');
        const id = btn.dataset.id;
        const name = btn.dataset.name;
        showConfirm('¿Eliminar la impresora "' + name + '"?', function(result) {
            if (result) {
                const formData = new FormData();
                formData.append('action', 'delete_printer');
                formData.append('imp_id', id);
                fetch('<?= URLROOT ?>/settings?tab=impresoras', {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        btn.closest('tr').remove();
                    } else {
                        alert('Error: ' + (res.message || 'No se pudo eliminar'));
                    }
                })
                .catch(() => alert('Error de conexión'));
            }
        });
    }
});
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>