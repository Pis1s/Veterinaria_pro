<?php
require_once __DIR__ . '/config.php';

$conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conexion->set_charset('utf8mb4');

if ($conexion->connect_error) {
    http_response_code(503);
    die(
        'No se pudo conectar a la base de datos Veterinaria_Pro. '
        . 'Verifique que MySQL esté iniciado en XAMPP e importe database/veterinaria_pro.sql. '
        . 'Detalle: ' . htmlspecialchars($conexion->connect_error)
    );
}
