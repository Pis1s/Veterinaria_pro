<?php
require_once '../includes/auth.php';
require_once '../backend/conexion.php';
require_once '../includes/datos_lista.php';
require_once '../includes/paths.php';
require_once '../includes/header.php';

$clientes = datos_listar($conexion, 'SELECT * FROM clientes ORDER BY nombre ASC');
$errorDb = datos_error_consulta($conexion);
?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">Clientes</h1>
        <p class="text-muted mb-0">Registro de dueños de mascotas</p>
    </div>
    <span class="badge bg-primary fs-6"><?= count($clientes) ?> registrados</span>
</div>

<?php if (isset($_GET['ok'])): ?>
<div class="alert alert-success alert-dismissible fade show">
    Cliente registrado correctamente.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($errorDb): ?>
<div class="alert alert-danger">No se pudieron cargar los clientes: <?= htmlspecialchars($errorDb) ?></div>
<?php endif; ?>

<section class="table-card mb-4" id="seccion-clientes" aria-labelledby="tituloListaClientes">
    <div class="list-section-header">
        <h2 class="h5 mb-0" id="tituloListaClientes">
            <i class="bi bi-table me-2"></i>Clientes registrados
        </h2>
        <span class="text-muted small"><?= count($clientes) ?> en total</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-striped mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Dirección</th>
                    <th class="text-end" width="140">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($clientes) === 0): ?>
                <tr>
                    <td colspan="6" class="text-muted text-center py-5">
                        <i class="bi bi-inbox display-6 d-block mb-2 opacity-50"></i>
                        No hay clientes registrados. Use el formulario de abajo para agregar uno.
                    </td>
                </tr>
                <?php else: foreach ($clientes as $row): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td class="fw-semibold"><?= htmlspecialchars($row['nombre']) ?></td>
                    <td><?= htmlspecialchars($row['telefono'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($row['correo'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($row['direccion'] ?? '—') ?></td>
                    <td class="text-end text-nowrap">
                        <button type="button" class="btn btn-outline-primary btn-sm"
                                data-bs-toggle="modal" data-bs-target="#modalVerCliente"
                                data-id="<?= (int) $row['id'] ?>"
                                data-nombre="<?= htmlspecialchars($row['nombre'], ENT_QUOTES) ?>"
                                data-telefono="<?= htmlspecialchars($row['telefono'] ?? '', ENT_QUOTES) ?>"
                                data-correo="<?= htmlspecialchars($row['correo'] ?? '', ENT_QUOTES) ?>"
                                data-direccion="<?= htmlspecialchars($row['direccion'] ?? '', ENT_QUOTES) ?>">
                            <i class="bi bi-eye"></i> Ver
                        </button>
                        <a href="<?= app_url('backend/clientes.php') ?>?eliminar=<?= (int) $row['id'] ?>"
                           class="btn btn-outline-danger btn-sm"
                           onclick="return confirm('¿Eliminar este cliente y sus mascotas?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</section>

<div class="form-card">
    <h5 class="mb-3"><i class="bi bi-person-plus me-2"></i>Nuevo cliente</h5>
    <form method="POST" action="<?= app_url('backend/clientes.php') ?>" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nombre *</label>
            <input name="nombre" class="form-control" placeholder="Nombre completo" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Teléfono</label>
            <input name="telefono" class="form-control" placeholder="555-0000">
        </div>
        <div class="col-md-6">
            <label class="form-label">Correo</label>
            <input type="email" name="correo" class="form-control" placeholder="correo@ejemplo.com">
        </div>
        <div class="col-md-6">
            <label class="form-label">Dirección</label>
            <input name="direccion" class="form-control" placeholder="Dirección">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Guardar</button>
        </div>
    </form>
</div>

<div class="modal fade" id="modalVerCliente" tabindex="-1" aria-labelledby="modalVerClienteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalVerClienteLabel">Detalle del cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0 detail-list">
                    <dt class="col-sm-4">ID</dt>
                    <dd class="col-sm-8" id="verClienteId">—</dd>
                    <dt class="col-sm-4">Nombre</dt>
                    <dd class="col-sm-8" id="verClienteNombre">—</dd>
                    <dt class="col-sm-4">Teléfono</dt>
                    <dd class="col-sm-8" id="verClienteTelefono">—</dd>
                    <dt class="col-sm-4">Correo</dt>
                    <dd class="col-sm-8" id="verClienteCorreo">—</dd>
                    <dt class="col-sm-4">Dirección</dt>
                    <dd class="col-sm-8" id="verClienteDireccion">—</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('modalVerCliente')?.addEventListener('show.bs.modal', function (e) {
    const b = e.relatedTarget;
    if (!b) return;
    const v = (x) => x || '—';
    document.getElementById('verClienteId').textContent = b.dataset.id || '—';
    document.getElementById('verClienteNombre').textContent = v(b.dataset.nombre);
    document.getElementById('verClienteTelefono').textContent = v(b.dataset.telefono);
    document.getElementById('verClienteCorreo').textContent = v(b.dataset.correo);
    document.getElementById('verClienteDireccion').textContent = v(b.dataset.direccion);
});
</script>

<?php require_once '../includes/footer.php'; ?>
