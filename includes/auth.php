<?php
require_once __DIR__ . '/paths.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header('Location: ' . app_url('index.html'));
    exit;
}
