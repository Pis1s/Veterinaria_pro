<?php
require_once '../includes/auth.php';
require_once '../backend/conexion.php';
require_once '../includes/datos_lista.php';
require_once '../includes/paths.php';
require_once '../includes/header.php';

$clientesSelect = datos_listar($conexion, 'SELECT id, nombre FROM clientes ORDER BY nombre ASC');
$mascotas = datos_listar(
    $conexion,
    'SELECT mascotas.*, clientes.nombre AS dueno
     FROM mascotas
     INNER JOIN clientes ON mascotas.id_cliente = clientes.id
     ORDER BY mascotas.nombre ASC'
);
$errorDb = datos_error_consulta($conexion);
?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">Mascotas</h1>
        <p class="text-muted mb-0">Pacientes registrados en la clínica</p>
    </div>
    <span class="badge bg-success fs-6"><?= count($mascotas) ?> registradas</span>
</div>

<?php if (isset($_GET['ok'])): ?>
<div class="alert alert-success alert-dismissible fade show">
    Mascota registrada correctamente.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($errorDb): ?>
<div class="alert alert-danger">No se pudieron cargar las mascotas: <?= htmlspecialchars($errorDb) ?></div>
<?php endif; ?>

<section class="table-card mb-4" id="seccion-mascotas" aria-labelledby="tituloListaMascotas">
    <div class="list-section-header">
        <h2 class="h5 mb-0" id="tituloListaMascotas">
            <i class="bi bi-table me-2"></i>Mascotas registradas
        </h2>
        <span class="text-muted small"><?= count($mascotas) ?> en total</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-striped mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Mascota</th>
                    <th>Especie</th>
                    <th>Raza</th>
                    <th>Edad</th>
                    <th>Dueño</th>
                    <th class="text-end" width="140">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($mascotas) === 0): ?>
                <tr>
                    <td colspan="7" class="text-muted text-center py-5">
                        <i class="bi bi-inbox display-6 d-block mb-2 opacity-50"></i>
                        No hay mascotas registradas.
                    </td>
                </tr>
                <?php else: foreach ($mascotas as $row): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td class="fw-semibold"><?= htmlspecialchars($row['nombre']) ?></td>
                    <td><?= htmlspecialchars($row['especie']) ?></td>
                    <td><?= htmlspecialchars($row['raza'] ?? '—') ?></td>
                    <td><?= (int) $row['edad'] ?> años</td>
                    <td><?= htmlspecialchars($row['dueno']) ?></td>
                    <td class="text-end text-nowrap">
                        <button type="button" class="btn btn-outline-primary btn-sm"
                                data-bs-toggle="modal" data-bs-target="#modalVerMascota"
                                data-id="<?= (int) $row['id'] ?>"
                                data-nombre="<?= htmlspecialchars($row['nombre'], ENT_QUOTES) ?>"
                                data-especie="<?= htmlspecialchars($row['especie'], ENT_QUOTES) ?>"
                                data-raza="<?= htmlspecialchars($row['raza'] ?? '', ENT_QUOTES) ?>"
                                data-edad="<?= (int) $row['edad'] ?>"
                                data-dueno="<?= htmlspecialchars($row['dueno'], ENT_QUOTES) ?>">
                            <i class="bi bi-eye"></i> Ver
                        </button>
                        <a href="<?= app_url('backend/mascotas.php') ?>?eliminar=<?= (int) $row['id'] ?>"
                           class="btn btn-outline-danger btn-sm"
                           onclick="return confirm('¿Eliminar esta mascota?')">
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
    <h5 class="mb-3"><i class="bi bi-plus-circle me-2"></i>Nueva mascota</h5>
    <?php if (count($clientesSelect) === 0): ?>
    <div class="alert alert-warning mb-0">
        Primero debe registrar al menos un <a href="clientes.php">cliente</a>.
    </div>
    <?php else: ?>
    <form method="POST" action="<?= app_url('backend/mascotas.php') ?>" class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Nombre *</label>
            <input name="nombre" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Especie *</label>
            <input name="especie" class="form-control" placeholder="Perro, Gato..." required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Raza</label>
            <input name="raza" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Edad (años)</label>
            <input type="number" name="edad" class="form-control" min="0" value="0">
        </div>
        <div class="col-md-8">
            <label class="form-label">Dueño *</label>
            <select name="cliente" class="form-select" required>
                <?php foreach ($clientesSelect as $c): ?>
                <option value="<?= (int) $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i> Guardar</button>
        </div>
    </form>
    <?php endif; ?>
</div>

<div class="modal fade" id="modalVerMascota" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de la mascota</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0 detail-list">
                    <dt class="col-sm-4">ID</dt>
                    <dd class="col-sm-8" id="verMascotaId">—</dd>
                    <dt class="col-sm-4">Nombre</dt>
                    <dd class="col-sm-8" id="verMascotaNombre">—</dd>
                    <dt class="col-sm-4">Especie</dt>
                    <dd class="col-sm-8" id="verMascotaEspecie">—</dd>
                    <dt class="col-sm-4">Raza</dt>
                    <dd class="col-sm-8" id="verMascotaRaza">—</dd>
                    <dt class="col-sm-4">Edad</dt>
                    <dd class="col-sm-8" id="verMascotaEdad">—</dd>
                    <dt class="col-sm-4">Dueño</dt>
                    <dd class="col-sm-8" id="verMascotaDueno">—</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('modalVerMascota')?.addEventListener('show.bs.modal', function (e) {
    const b = e.relatedTarget;
    if (!b) return;
    const v = (x) => x || '—';
    document.getElementById('verMascotaId').textContent = b.dataset.id || '—';
    document.getElementById('verMascotaNombre').textContent = v(b.dataset.nombre);
    document.getElementById('verMascotaEspecie').textContent = v(b.dataset.especie);
    document.getElementById('verMascotaRaza').textContent = v(b.dataset.raza);
    document.getElementById('verMascotaEdad').textContent = (b.dataset.edad !== undefined && b.dataset.edad !== '') ? b.dataset.edad + ' años' : '—';
    document.getElementById('verMascotaDueno').textContent = v(b.dataset.dueno);
});
</script>

<?php require_once '../includes/footer.php'; ?>
