<?php
// puntaje.php: POST guarda un puntaje (solo logueados) · GET devuelve el top 10.
$raiz = '';
require __DIR__ . '/includes/sesion.php';
require __DIR__ . '/conexion.php';
header('Content-Type: application/json; charset=utf-8');

function responder(int $codigo, array $datos): void {
    http_response_code($codigo);
    echo json_encode($datos);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = usuario_actual();
    if (!$u) responder(401, ['error' => 'Necesitás iniciar sesión.']);
    if (!csrf_validar($_SERVER['HTTP_X_CSRF'] ?? '')) responder(403, ['error' => 'Sesión expirada.']);

    $d = json_decode(file_get_contents('php://input'), true);
    $puntos = (int) ($d['puntos'] ?? -1);
    $kills  = (int) ($d['kills'] ?? -1);
    if ($puntos < 0 || $puntos > 100000 || $kills < 0 || $kills > 2000) {
        responder(400, ['error' => 'Datos inválidos.']);
    }
    try {
        $stmt = $conexion->prepare('INSERT INTO puntajes (usuario_id, puntos, kills) VALUES (?, ?, ?)');
        $stmt->bind_param('iii', $u['id'], $puntos, $kills);
        $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        responder(500, ['error' => 'No se pudo guardar. ¿Existe la tabla puntajes?']);
    }
}

try {
    $res = $conexion->query('SELECT u.usuario, MAX(p.puntos) AS puntos FROM puntajes p JOIN usuarios u ON u.id = p.usuario_id GROUP BY u.id, u.usuario ORDER BY puntos DESC LIMIT 10');
    responder(200, ['ranking' => $res->fetch_all(MYSQLI_ASSOC)]);
} catch (mysqli_sql_exception $e) {
    responder(500, ['error' => 'No se pudo leer el ranking. ¿Existe la tabla puntajes?']);
}
