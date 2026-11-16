<?php
// conexion.php: datos reales de ESTA PC. NO se sube a GitHub (está en .gitignore).
$servidor  = "localhost";
$usuario   = "root";
$clave     = "";          // XAMPP: "" · MAMP: "root"
$baseDatos = "hollow_web";
$puerto    = 3306;        // el que figura en phpMyAdmin

// Si algo falla al conectar, mostramos un mensaje claro en vez de un error 500 en blanco.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conexion = new mysqli($servidor, $usuario, $clave, $baseDatos, $puerto);
    $conexion->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    die("No se pudo conectar a la base de datos. Revisá conexion.php (clave, puerto y nombre de la base) y que MySQL esté encendido.");
}
