<?php
/**
 * Ruta base de la app en el servidor web (ej. /Veterinaria_pro).
 */
function app_base_path(): string
{
    $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $leaf = basename($dir);
    if ($leaf === 'views' || $leaf === 'backend') {
        $dir = dirname($dir);
    }
    return rtrim($dir, '/');
}

function app_url(string $path = ''): string
{
    $base = app_base_path();
    $path = ltrim(str_replace('\\', '/', $path), '/');
    return ($base === '' ? '' : $base) . ($path !== '' ? '/' . $path : '');
}
