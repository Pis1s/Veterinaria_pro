<?php
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/../includes/paths.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . app_url('index.html'));
    exit;
}

$correo = trim($_POST['correo'] ?? '');
$password = $_POST['contraseña'] ?? '';

if ($correo === '' || $password === '') {
    header('Location: ' . app_url('index.html') . '?error=campos');
    exit;
}

$stmt = $conexion->prepare(
    'SELECT id, nombre, correo, rol FROM usuarios WHERE correo = ? AND contraseña = MD5(?)'
);
$stmt->bind_param('ss', $correo, $password);
$stmt->execute();
$resultado = $stmt->get_result();

if ($row = $resultado->fetch_assoc()) {
    $_SESSION['usuario'] = $row['correo'];
    $_SESSION['nombre'] = $row['nombre'];
    $_SESSION['rol'] = $row['rol'];
    $_SESSION['usuario_id'] = $row['id'];
    header('Location: ' . app_url('dashboard.php'));
    exit;
}

header('Location: ' . app_url('index.html') . '?error=login');
exit;
