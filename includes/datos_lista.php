<?php
/**
 * Ejecuta una consulta SELECT y devuelve filas o array vacío.
 *
 * @return array<int, array<string, mixed>>
 */
function datos_listar(mysqli $conexion, string $sql): array
{
    $res = $conexion->query($sql);
    if ($res === false) {
        return [];
    }
    $filas = [];
    while ($row = $res->fetch_assoc()) {
        $filas[] = $row;
    }
    return $filas;
}

function datos_error_consulta(mysqli $conexion): ?string
{
    $err = $conexion->error;
    return ($err !== '') ? $err : null;
}
