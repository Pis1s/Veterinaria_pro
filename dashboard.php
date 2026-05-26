<?php
require_once 'includes/auth.php';
require_once 'backend/conexion.php';
require_once 'includes/paths.php';
require_once 'includes/header.php';

function contar(mysqli $conexion, string $tabla): int {
    $res = $conexion->query("SELECT COUNT(*) AS total FROM $tabla");
    if ($res === false) {
        return 0;
    }
    $row = $res->fetch_assoc();
    return (int) ($row['total'] ?? 0);
}

$totalClientes = contar($conexion, 'clientes');
$totalMascotas = contar($conexion, 'mascotas');
$totalCitas = contar($conexion, 'citas');
$totalHistorial = contar($conexion, 'historial');

$citasHoy = $conexion->query(
    "SELECT citas.*, mascotas.nombre AS mascota, clientes.nombre AS cliente
     FROM citas
     INNER JOIN mascotas ON citas.id_mascota = mascotas.id
     INNER JOIN clientes ON mascotas.id_cliente = clientes.id
     WHERE citas.fecha = CURDATE() AND citas.estado IN ('pendiente', 'confirmada')
     ORDER BY citas.hora ASC
     LIMIT 5"
);

$tarjetas = [
    ['total' => $totalClientes, 'label' => 'Clientes', 'icon' => 'bi-people', 'class' => 'stat-primary', 'url' => app_url('views/clientes.php')],
    ['total' => $totalMascotas, 'label' => 'Mascotas', 'icon' => 'bi-emoji-smile', 'class' => 'stat-success', 'url' => app_url('views/mascotas.php')],
    ['total' => $totalCitas, 'label' => 'Citas', 'icon' => 'bi-calendar-check', 'class' => 'stat-warning', 'url' => app_url('views/citas.php')],
    ['total' => $totalHistorial, 'label' => 'Registros médicos', 'icon' => 'bi-journal-medical', 'class' => 'stat-info', 'url' => app_url('views/historial.php')],
];
?>

<div class="page-header mb-4">
    <h1 class="h3 mb-1">Panel de control</h1>
    <p class="text-muted mb-0">Bienvenido, <?= htmlspecialchars($_SESSION['nombre'] ?? 'Usuario') ?></p>
</div>

<div class="row g-3 mb-4">
    <?php foreach ($tarjetas as $t): ?>
    <div class="col-6 col-md-3">
        <a href="<?= htmlspecialchars($t['url']) ?>"
           class="stat-card stat-card-link <?= $t['class'] ?> text-decoration-none text-dark"
           title="Ver <?= htmlspecialchars($t['label']) ?>">
            <i class="bi <?= $t['icon'] ?> stat-icon"></i>
            <div>
                <div class="stat-number"><?= $t['total'] ?></div>
                <div class="stat-label"><?= htmlspecialchars($t['label']) ?></div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <a href="<?= app_url('views/clientes.php') ?>" class="quick-link card border-0 shadow-sm h-100 text-decoration-none">
            <div class="card-body text-center">
                <i class="bi bi-person-plus display-6 text-primary"></i>
                <h6 class="mt-2 mb-0 text-dark">Gestionar clientes</h6>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="<?= app_url('views/mascotas.php') ?>" class="quick-link card border-0 shadow-sm h-100 text-decoration-none">
            <div class="card-body text-center">
                <i class="bi bi-plus-circle display-6 text-success"></i>
                <h6 class="mt-2 mb-0 text-dark">Registrar mascota</h6>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="<?= app_url('views/citas.php') ?>" class="quick-link card border-0 shadow-sm h-100 text-decoration-none">
            <div class="card-body text-center">
                <i class="bi bi-calendar-plus display-6 text-warning"></i>
                <h6 class="mt-2 mb-0 text-dark">Agendar cita</h6>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="<?= app_url('views/historial.php') ?>" class="quick-link card border-0 shadow-sm h-100 text-decoration-none">
            <div class="card-body text-center">
                <i class="bi bi-clipboard2-pulse display-6 text-info"></i>
                <h6 class="mt-2 mb-0 text-dark">Historial clínico</h6>
            </div>
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 pt-3">
        <h5 class="mb-0"><i class="bi bi-clock me-2"></i>Citas de hoy</h5>
    </div>
    <div class="card-body p-0">
        <?php if ($citasHoy === false || $citasHoy->num_rows === 0): ?>
            <p class="text-muted p-4 mb-0">No hay citas programadas para hoy.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Hora</th>
                            <th>Mascota</th>
                            <th>Cliente</th>
                            <th>Motivo</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($c = $citasHoy->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars(substr($c['hora'], 0, 5)) ?></td>
                            <td><?= htmlspecialchars($c['mascota']) ?></td>
                            <td><?= htmlspecialchars($c['cliente']) ?></td>
                            <td><?= htmlspecialchars($c['motivo'] ?? '—') ?></td>
                            <td><span class="badge bg-<?= $c['estado'] === 'confirmada' ? 'success' : 'secondary' ?>"><?= htmlspecialchars($c['estado']) ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
