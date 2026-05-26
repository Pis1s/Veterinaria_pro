<?php
session_start();
require_once __DIR__ . '/../includes/paths.php';
session_destroy();
header('Location: ' . app_url('index.html'));
exit;
