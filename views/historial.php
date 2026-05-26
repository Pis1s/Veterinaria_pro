<?php
require_once '../includes/auth.php';
require_once '../backend/conexion.php';
require_once '../includes/datos_lista.php';
require_once '../includes/paths.php';
require_once '../includes/header.php';

$mascotasSelect = datos_listar(
    $conexion,
    'SELECT mascotas.id, mascotas.nombre, clientes.nombre AS cliente
     FROM mascotas
     INNER JOIN clientes ON mascotas.id_cliente = clientes.id
     ORDER BY mascotas.nombre ASC'
);

$registros = datos_listar(
    $conexion,
    'SELECT historial.*, mascotas.nombre AS mascota, clientes.nombre AS cliente
     FROM historial
     INNER JOIN mascotas ON historial.id_mascota = mascotas.id
     INNER JOIN clientes ON mascotas.id_cliente = clientes.id
     ORDER BY historial.fecha DESC, historial.id DESC'
);

$errorDb = datos_error_consulta($conexion);
?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">Historial médico</h1>
        <p class="text-muted mb-0">Consultas, diagnósticos y tratamientos</p>
    </div>
    <span class="badge bg-info text-dark fs-6"><?= count($registros) ?> registros</span>
</div>

<?php if (isset($_GET['ok'])): ?>
<div class="alert alert-success alert-dismissible fade show">
    Registro médico guardado.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($errorDb): ?>
<div class="alert alert-danger">No se pudo cargar el historial: <?= htmlspecialchars($errorDb) ?></div>
<?php endif; ?>

<section class="table-card mb-4" id="seccion-historial" aria-labelledby="tituloListaHistorial">
    <div class="list-section-header">
        <h2 class="h5 mb-0" id="tituloListaHistorial">
            <i class="bi bi-table me-2"></i>Registros médicos
        </h2>
        <span class="text-muted small"><?= count($registros) ?> en total</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-striped mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Mascota</th>
                    <th>Cliente</th>
                    <th>Descripción</th>
                    <th>Diagnóstico</th>
                    <th class="text-end" width="140">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($registros) === 0): ?>
                <tr>
                    <td colspan="7" class="text-muted text-center py-5">
                        <i class="bi bi-journal-x display-6 d-block mb-2 opacity-50"></i>
                        Sin registros en el historial clínico.
                    </td>
                </tr>
                <?php else: foreach ($registros as $row): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['fecha']) ?></td>
                    <td class="fw-semibold"><?= htmlspecialchars($row['mascota']) ?></td>
                    <td><?= htmlspecialchars($row['cliente']) ?></td>
                    <td class="text-truncate-cell" title="<?= htmlspecialchars($row['descripcion']) ?>">
                        <?php
                        $desc = $row['descripcion'];
                        echo htmlspecialchars(strlen($desc) > 60 ? substr($desc, 0, 60) . '…' : $desc);
                        ?>
                    </td>
                    <td><?= htmlspecialchars($row['diagnostico'] ?? '—') ?></td>
                    <td class="text-end text-nowrap">
                        <button type="button" class="btn btn-outline-primary btn-sm"
                                data-bs-toggle="modal" data-bs-target="#modalVerHistorial"
                                data-id="<?= (int) $row['id'] ?>"
                                data-fecha="<?= htmlspecialchars($row['fecha'], ENT_QUOTES) ?>"
                                data-mascota="<?= htmlspecialchars($row['mascota'], ENT_QUOTES) ?>"
                                data-cliente="<?= htmlspecialchars($row['cliente'], ENT_QUOTES) ?>"
                                data-descripcion="<?= htmlspecialchars($row['descripcion'], ENT_QUOTES) ?>"
                                data-diagnostico="<?= htmlspecialchars($row['diagnostico'] ?? '', ENT_QUOTES) ?>"
                                data-tratamiento="<?= htmlspecialchars($row['tratamiento'] ?? '', ENT_QUOTES) ?>">
                            <i class="bi bi-eye"></i> Ver
                        </button>
                        <a href="<?= app_url('backend/historial.php') ?>?eliminar=<?= (int) $row['id'] ?>"
                           class="btn btn-outline-danger btn-sm"
                           onclick="return confirm('¿Eliminar este registro?')">
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
    <h5 class="mb-3"><i class="bi bi-clipboard2-pulse me-2"></i>Nuevo registro</h5>
    <?php if (count($mascotasSelect) === 0): ?>
    <div class="alert alert-warning mb-0">
        Registre una <a href="mascotas.php">mascota</a> para añadir historial clínico.
    </div>
    <?php else: ?>
    <form method="POST" action="<?= app_url('backend/historial.php') ?>" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Mascota *</label>
            <select name="mascota" class="form-select" required>
                <?php foreach ($mascotasSelect as $m): ?>
                <option value="<?= (int) $m['id'] ?>">
                    <?= htmlspecialchars($m['nombre']) ?> — <?= htmlspecialchars($m['cliente']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Fecha *</label>
            <input type="date" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="col-12">
            <label class="form-label">Descripción / motivo de consulta *</label>
            <textarea name="descripcion" class="form-control" rows="2" required></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Diagnóstico</label>
            <input name="diagnostico" class="form-control" placeholder="Diagnóstico veterinario">
        </div>
        <div class="col-md-6">
            <label class="form-label">Tratamiento</label>
            <input name="tratamiento" class="form-control" placeholder="Medicación, indicaciones...">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-info text-dark"><i class="bi bi-save me-1"></i> Guardar</button>
        </div>
    </form>
    <?php endif; ?>
</div>

<div class="modal fade" id="modalVerHistorial" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle del registro médico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0 detail-list">
                    <dt class="col-sm-3">ID</dt>
                    <dd class="col-sm-9" id="verHistId">—</dd>
                    <dt class="col-sm-3">Fecha</dt>
                    <dd class="col-sm-9" id="verHistFecha">—</dd>
                    <dt class="col-sm-3">Mascota</dt>
                    <dd class="col-sm-9" id="verHistMascota">—</dd>
                    <dt class="col-sm-3">Cliente</dt>
                    <dd class="col-sm-9" id="verHistCliente">—</dd>
                    <dt class="col-sm-3">Descripción</dt>
                    <dd class="col-sm-9" id="verHistDescripcion">—</dd>
                    <dt class="col-sm-3">Diagnóstico</dt>
                    <dd class="col-sm-9" id="verHistDiagnostico">—</dd>
                    <dt class="col-sm-3">Tratamiento</dt>
                    <dd class="col-sm-9" id="verHistTratamiento">—</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('modalVerHistorial')?.addEventListener('show.bs.modal', function (e) {
    const b = e.relatedTarget;
    if (!b) return;
    const v = (x) => x || '—';
    document.getElementById('verHistId').textContent = b.dataset.id || '—';
    document.getElementById('verHistFecha').textContent = v(b.dataset.fecha);
    document.getElementById('verHistMascota').textContent = v(b.dataset.mascota);
    document.getElementById('verHistCliente').textContent = v(b.dataset.cliente);
    document.getElementById('verHistDescripcion').textContent = v(b.dataset.descripcion);
    document.getElementById('verHistDiagnostico').textContent = v(b.dataset.diagnostico);
    document.getElementById('verHistTratamiento').textContent = v(b.dataset.tratamiento);
});
</script>

<?php require_once '../includes/footer.php'; ?>
