<?php
// conexion.php: se sube a GitHub. Está pensado para XAMPP con la configuración por defecto.
// Si la base "hollow_web" o sus tablas no existen, las crea sola leyendo bd/hollow_web.sql.
// Así alcanza con encender Apache y MySQL.

$baseDatos = "hollow_web";

// Combinaciones (puerto, clave) que se prueban en orden. La primera es XAMPP por defecto.
$intentos = [
    [3306, ""],      // XAMPP
    [3306, "root"],  // MAMP en Windows
    [3307, ""],      // XAMPP si el puerto 3306 estaba ocupado
    [8889, "root"],  // MAMP en Mac
];
$tablas = ["usuarios", "comentarios", "puntajes"];

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conexion = null;
foreach ($intentos as [$puerto, $clave]) {
    try {
        $c = mysqli_init();
        $c->options(MYSQLI_OPT_CONNECT_TIMEOUT, 2);
        $c->real_connect("127.0.0.1", "root", $clave, "", $puerto);
        $conexion = $c;
        break;
    } catch (mysqli_sql_exception $e) {
        // probamos la siguiente combinación
    }
}

if (!$conexion) {
    http_response_code(500);
    die("No se pudo conectar a MySQL. Revisá que MySQL esté encendido (verde) en el panel de XAMPP.");
}

try {
    $conexion->set_charset("utf8mb4");
    $conexion->query("CREATE DATABASE IF NOT EXISTS `$baseDatos` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    $conexion->select_db($baseDatos);

    // ¿Faltan tablas? Entonces se importa bd/hollow_web.sql (usa CREATE TABLE IF NOT EXISTS).
    $existentes = [];
    $res = $conexion->query("SHOW TABLES");
    while ($fila = $res->fetch_row()) { $existentes[] = $fila[0]; }

    if (array_diff($tablas, $existentes)) {
        $archivo = __DIR__ . "/../bd/hollow_web.sql";
        if (!is_file($archivo)) {
            die("Falta el archivo bd/hollow_web.sql en el proyecto.");
        }
        $conexion->multi_query(file_get_contents($archivo));
        do {
            if ($r = $conexion->store_result()) { $r->free(); }
        } while ($conexion->more_results() && $conexion->next_result());
    }
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    die("No se pudo preparar la base de datos. Detalle: " . $e->getMessage());
}
