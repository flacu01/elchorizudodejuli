<?php
// comentar.php: recibe el formulario de comentarios, lo guarda en MySQL y vuelve al inicio.
$raiz = '';
require __DIR__ . '/includes/sesion.php';
require __DIR__ . '/conexion.php';

// Guarda un mensaje para mostrarlo en el inicio y redirige.
function volver(string $tipo, string $texto): void {
    $_SESSION['flash'] = ['tipo' => $tipo, 'texto' => $texto];
    header('Location: index.php#comentarios');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php#comentarios');
    exit;
}

$u = usuario_actual();
if (!$u) {
    volver('error', 'Ingresá a tu cuenta para comentar.');
}
if (!csrf_validar($_POST['csrf'] ?? '')) {
    volver('error', 'La sesión expiró. Recargá la página e intentá de nuevo.');
}
if (!empty($_POST['web'])) {                  // campo trampa para robots
    volver('error', 'No se pudo publicar el comentario.');
}

$texto = trim($_POST['texto'] ?? '');
$largo = mb_strlen($texto);
if ($largo < 3) {
    volver('error', 'Escribí tu comentario antes de publicar.');
}
if ($largo > 500) {
    volver('error', 'El comentario es muy largo (máximo 500 caracteres).');
}

// Una espera de 30 segundos entre comentarios para evitar spam.
$espera = 30 - (time() - ($_SESSION['ultimo_comentario'] ?? 0));
if ($espera > 0) {
    volver('error', 'Esperá ' . $espera . ' s antes de publicar otro comentario.');
}

try {
    $stmt = $conexion->prepare('INSERT INTO comentarios (usuario_id, texto) VALUES (?, ?)');
    $stmt->bind_param('is', $u['id'], $texto);
    $stmt->execute();
} catch (mysqli_sql_exception $e) {
    // Para ver el error real mientras probás, descomentá la línea de abajo y comentá la otra:
    // volver('error', 'Detalle: ' . $e->getMessage());
    volver('error', 'No se pudo publicar. Intentá de nuevo en un rato.');
}

$_SESSION['ultimo_comentario'] = time();
volver('ok', '¡Gracias! Tu comentario ya está publicado.');
