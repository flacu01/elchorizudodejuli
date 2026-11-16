<?php
// includes/sesion.php
// Se incluye al principio de cada página. Inicia la sesión y define funciones útiles.

define('DIR_INCLUDES', __DIR__);

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

// Escapa texto antes de mostrarlo en HTML (evita que alguien inyecte código).
function h(string $texto): string {
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

// Devuelve los datos del usuario logueado, o null si no hay nadie.
function usuario_actual(): ?array {
    if (!isset($_SESSION['usuario_id'])) {
        return null;
    }
    return ['id' => $_SESSION['usuario_id'], 'nombre' => $_SESSION['usuario_nombre']];
}

// Si no hay sesión, manda a login.php y recuerda a qué página quería entrar.
function requerir_login(string $raiz, string $pagina): void {
    if (usuario_actual() === null) {
        $_SESSION['volver'] = $pagina;
        header('Location: ' . $raiz . 'login.php');
        exit;
    }
}

// Solo permite volver a páginas conocidas del sitio.
function destino_seguro(string $destino): string {
    $permitidos = ['index.php', 'pages/hollow.php', 'pages/silksong.php'];
    return in_array($destino, $permitidos, true) ? $destino : 'index.php';
}

// Token anti-CSRF: asegura que el formulario lo envió nuestra propia página.
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
function csrf_validar($token): bool {
    return isset($_SESSION['csrf']) && is_string($token) && hash_equals($_SESSION['csrf'], $token);
}
