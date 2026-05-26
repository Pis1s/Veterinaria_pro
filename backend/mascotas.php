<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/../includes/paths.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $especie = trim($_POST['especie'] ?? '');
    $raza = trim($_POST['raza'] ?? '');
    $edad = (int) ($_POST['edad'] ?? 0);
    $cliente = (int) ($_POST['cliente'] ?? 0);

    $stmt = $conexion->prepare(
        'INSERT INTO mascotas (nombre, especie, raza, edad, id_cliente) VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->bind_param('sssii', $nombre, $especie, $raza, $edad, $cliente);
    $stmt->execute();
    header('Location: ' . app_url('views/mascotas.php') . '?ok=1');
    exit;
}

if (isset($_GET['eliminar'])) {
    $id = (int) $_GET['eliminar'];
    $stmt = $conexion->prepare('DELETE FROM mascotas WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    header('Location: ' . app_url('views/mascotas.php'));
    exit;
}

header('Location: ' . app_url('views/mascotas.php'));
exit;
