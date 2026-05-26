<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/../includes/paths.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mascota = (int) ($_POST['mascota'] ?? 0);
    $descripcion = trim($_POST['descripcion'] ?? '');
    $diagnostico = trim($_POST['diagnostico'] ?? '');
    $tratamiento = trim($_POST['tratamiento'] ?? '');
    $fecha = $_POST['fecha'] ?? date('Y-m-d');

    $stmt = $conexion->prepare(
        'INSERT INTO historial (id_mascota, descripcion, diagnostico, tratamiento, fecha) VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->bind_param('issss', $mascota, $descripcion, $diagnostico, $tratamiento, $fecha);
    $stmt->execute();
    header('Location: ' . app_url('views/historial.php') . '?ok=1');
    exit;
}

if (isset($_GET['eliminar'])) {
    $id = (int) $_GET['eliminar'];
    $stmt = $conexion->prepare('DELETE FROM historial WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    header('Location: ' . app_url('views/historial.php'));
    exit;
}

header('Location: ' . app_url('views/historial.php'));
exit;
