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

$citas = datos_listar(
    $conexion,
    'SELECT citas.*, mascotas.nombre AS mascota, clientes.nombre AS cliente
     FROM citas
     INNER JOIN mascotas ON citas.id_mascota = mascotas.id
     INNER JOIN clientes ON mascotas.id_cliente = clientes.id
     ORDER BY citas.fecha DESC, citas.hora DESC'
);

$errorDb = datos_error_consulta($conexion);

$estadoBadge = [
    'pendiente' => 'secondary',
    'confirmada' => 'success',
    'completada' => 'primary',
    'cancelada' => 'danger',
];
?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">Citas</h1>
        <p class="text-muted mb-0">Agenda de consultas veterinarias</p>
    </div>
    <span class="badge bg-warning text-dark fs-6"><?= count($citas) ?> citas</span>
</div>

<?php if (isset($_GET['ok'])): ?>
<div class="alert alert-success alert-dismissible fade show">
    Cita agendada correctamente.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($errorDb): ?>
<div class="alert alert-danger">No se pudieron cargar las citas: <?= htmlspecialchars($errorDb) ?></div>
<?php endif; ?>

<section class="table-card mb-4" id="seccion-citas" aria-labelledby="tituloListaCitas">
    <div class="list-section-header">
        <h2 class="h5 mb-0" id="tituloListaCitas">
            <i class="bi bi-table me-2"></i>Citas agendadas
        </h2>
        <span class="text-muted small"><?= count($citas) ?> en total</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-striped mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Mascota</th>
                    <th>Cliente</th>
                    <th>Motivo</th>
                    <th>Estado</th>
                    <th class="text-end" width="200">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($citas) === 0): ?>
                <tr>
                    <td colspan="8" class="text-muted text-center py-5">
                        <i class="bi bi-calendar-x display-6 d-block mb-2 opacity-50"></i>
                        No hay citas registradas.
                    </td>
                </tr>
                <?php else: foreach ($citas as $row):
                    $badge = $estadoBadge[$row['estado']] ?? 'secondary';
                    $hora = substr((string) $row['hora'], 0, 5);
                ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['fecha']) ?></td>
                    <td><?= htmlspecialchars($hora) ?></td>
                    <td class="fw-semibold"><?= htmlspecialchars($row['mascota']) ?></td>
                    <td><?= htmlspecialchars($row['cliente']) ?></td>
                    <td><?= htmlspecialchars($row['motivo'] ?? '—') ?></td>
                    <td><span class="badge bg-<?= $badge ?>"><?= htmlspecialchars($row['estado']) ?></span></td>
                    <td class="text-end text-nowrap">
                        <button type="button" class="btn btn-outline-primary btn-sm"
                                data-bs-toggle="modal" data-bs-target="#modalVerCita"
                                data-id="<?= (int) $row['id'] ?>"
                                data-fecha="<?= htmlspecialchars($row['fecha'], ENT_QUOTES) ?>"
                                data-hora="<?= htmlspecialchars($hora, ENT_QUOTES) ?>"
                                data-mascota="<?= htmlspecialchars($row['mascota'], ENT_QUOTES) ?>"
                                data-cliente="<?= htmlspecialchars($row['cliente'], ENT_QUOTES) ?>"
                                data-motivo="<?= htmlspecialchars($row['motivo'] ?? '', ENT_QUOTES) ?>"
                                data-estado="<?= htmlspecialchars($row['estado'], ENT_QUOTES) ?>">
                            <i class="bi bi-eye"></i> Ver
                        </button>
                        <?php if ($row['estado'] === 'pendiente'): ?>
                        <a href="<?= app_url('backend/citas.php') ?>?id=<?= (int) $row['id'] ?>&estado=confirmada" class="btn btn-outline-success btn-sm" title="Confirmar"><i class="bi bi-check-lg"></i></a>
                        <?php endif; ?>
                        <?php if (in_array($row['estado'], ['pendiente', 'confirmada'], true)): ?>
                        <a href="<?= app_url('backend/citas.php') ?>?id=<?= (int) $row['id'] ?>&estado=completada" class="btn btn-outline-primary btn-sm" title="Completar"><i class="bi bi-check2-all"></i></a>
                        <a href="<?= app_url('backend/citas.php') ?>?id=<?= (int) $row['id'] ?>&estado=cancelada" class="btn btn-outline-secondary btn-sm" title="Cancelar"><i class="bi bi-x-lg"></i></a>
                        <?php endif; ?>
                        <a href="<?= app_url('backend/citas.php') ?>?eliminar=<?= (int) $row['id'] ?>"
                           class="btn btn-outline-danger btn-sm"
                           onclick="return confirm('¿Eliminar esta cita?')">
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
    <h5 class="mb-3"><i class="bi bi-calendar-plus me-2"></i>Nueva cita</h5>
    <?php if (count($mascotasSelect) === 0): ?>
    <div class="alert alert-warning mb-0">
        Registre una <a href="mascotas.php">mascota</a> antes de agendar citas.
    </div>
    <?php else: ?>
    <form method="POST" action="<?= app_url('backend/citas.php') ?>" class="row g-3">
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
        <div class="col-md-3">
            <label class="form-label">Fecha *</label>
            <input type="date" name="fecha" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Hora *</label>
            <input type="time" name="hora" class="form-control" required>
        </div>
        <div class="col-md-8">
            <label class="form-label">Motivo</label>
            <input name="motivo" class="form-control" placeholder="Consulta, vacuna, control...">
        </div>
        <div class="col-md-4">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select">
                <option value="pendiente">Pendiente</option>
                <option value="confirmada">Confirmada</option>
            </select>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-warning text-dark"><i class="bi bi-save me-1"></i> Agendar</button>
        </div>
    </form>
    <?php endif; ?>
</div>

<div class="modal fade" id="modalVerCita" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de la cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0 detail-list">
                    <dt class="col-sm-4">ID</dt>
                    <dd class="col-sm-8" id="verCitaId">—</dd>
                    <dt class="col-sm-4">Fecha</dt>
                    <dd class="col-sm-8" id="verCitaFecha">—</dd>
                    <dt class="col-sm-4">Hora</dt>
                    <dd class="col-sm-8" id="verCitaHora">—</dd>
                    <dt class="col-sm-4">Mascota</dt>
                    <dd class="col-sm-8" id="verCitaMascota">—</dd>
                    <dt class="col-sm-4">Cliente</dt>
                    <dd class="col-sm-8" id="verCitaCliente">—</dd>
                    <dt class="col-sm-4">Motivo</dt>
                    <dd class="col-sm-8" id="verCitaMotivo">—</dd>
                    <dt class="col-sm-4">Estado</dt>
                    <dd class="col-sm-8" id="verCitaEstado">—</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('modalVerCita')?.addEventListener('show.bs.modal', function (e) {
    const b = e.relatedTarget;
    if (!b) return;
    const v = (x) => x || '—';
    document.getElementById('verCitaId').textContent = b.dataset.id || '—';
    document.getElementById('verCitaFecha').textContent = v(b.dataset.fecha);
    document.getElementById('verCitaHora').textContent = v(b.dataset.hora);
    document.getElementById('verCitaMascota').textContent = v(b.dataset.mascota);
    document.getElementById('verCitaCliente').textContent = v(b.dataset.cliente);
    document.getElementById('verCitaMotivo').textContent = v(b.dataset.motivo);
    document.getElementById('verCitaEstado').textContent = v(b.dataset.estado);
});
</script>

<?php require_once '../includes/footer.php'; ?>
