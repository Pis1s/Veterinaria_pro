<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/../includes/paths.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mascota = (int) ($_POST['mascota'] ?? 0);
    $fecha = $_POST['fecha'] ?? '';
    $hora = $_POST['hora'] ?? '';
    $motivo = trim($_POST['motivo'] ?? '');
    $estado = $_POST['estado'] ?? 'pendiente';

    $stmt = $conexion->prepare(
        'INSERT INTO citas (id_mascota, fecha, hora, motivo, estado) VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->bind_param('issss', $mascota, $fecha, $hora, $motivo, $estado);
    $stmt->execute();
    header('Location: ' . app_url('views/citas.php') . '?ok=1');
    exit;
}

if (isset($_GET['eliminar'])) {
    $id = (int) $_GET['eliminar'];
    $stmt = $conexion->prepare('DELETE FROM citas WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    header('Location: ' . app_url('views/citas.php'));
    exit;
}

if (isset($_GET['estado'], $_GET['id'])) {
    $id = (int) $_GET['id'];
    $estado = $_GET['estado'];
    $permitidos = ['pendiente', 'confirmada', 'completada', 'cancelada'];
    if (in_array($estado, $permitidos, true)) {
        $stmt = $conexion->prepare('UPDATE citas SET estado = ? WHERE id = ?');
        $stmt->bind_param('si', $estado, $id);
        $stmt->execute();
    }
    header('Location: ' . app_url('views/citas.php'));
    exit;
}

header('Location: ' . app_url('views/citas.php'));
exit;
