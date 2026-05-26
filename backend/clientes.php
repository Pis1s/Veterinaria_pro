<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/../includes/paths.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $correo = trim($_POST['correo'] ?? '');

    $stmt = $conexion->prepare(
        'INSERT INTO clientes (nombre, telefono, direccion, correo) VALUES (?, ?, ?, ?)'
    );
    $stmt->bind_param('ssss', $nombre, $telefono, $direccion, $correo);
    $stmt->execute();
    header('Location: ' . app_url('views/clientes.php') . '?ok=1');
    exit;
}

if (isset($_GET['eliminar'])) {
    $id = (int) $_GET['eliminar'];
    $stmt = $conexion->prepare('DELETE FROM clientes WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    header('Location: ' . app_url('views/clientes.php'));
    exit;
}

header('Location: ' . app_url('views/clientes.php'));
exit;
