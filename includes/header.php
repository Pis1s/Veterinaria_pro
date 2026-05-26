<?php
require_once __DIR__ . '/paths.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= app_url('css/estilos.css') ?>" rel="stylesheet">
</head>
<body class="app-body">
<nav class="navbar navbar-expand-lg navbar-dark app-navbar">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?= app_url('dashboard.php') ?>">
            <i class="bi bi-heart-pulse-fill me-2"></i>Veterinaria Pro
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?= app_url('dashboard.php') ?>"><i class="bi bi-grid me-1"></i>Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= app_url('views/clientes.php') ?>"><i class="bi bi-people me-1"></i>Clientes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= app_url('views/mascotas.php') ?>"><i class="bi bi-emoji-smile me-1"></i>Mascotas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= app_url('views/citas.php') ?>"><i class="bi bi-calendar-check me-1"></i>Citas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= app_url('views/historial.php') ?>"><i class="bi bi-journal-medical me-1"></i>Historial</a>
                </li>
            </ul>
            <span class="navbar-text text-white-50 me-3 d-none d-md-inline">
                <?= htmlspecialchars($_SESSION['nombre'] ?? $_SESSION['usuario'] ?? '') ?>
            </span>
            <a href="<?= app_url('backend/logout.php') ?>" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right"></i> Salir
            </a>
        </div>
    </div>
</nav>
<main class="container py-4">
