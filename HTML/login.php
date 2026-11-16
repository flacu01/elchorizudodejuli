<?php
$raiz = '';
require __DIR__ . '/includes/sesion.php';
require __DIR__ . '/conexion.php';

if (usuario_actual()) { header('Location: index.php'); exit; }

$error = '';
$dato  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dato  = trim($_POST['usuario'] ?? '');
    $clave = $_POST['clave'] ?? '';

    if (!csrf_validar($_POST['csrf'] ?? '')) {
        $error = 'La sesión expiró. Recargá la página e intentá de nuevo.';
    } else {
        // Se busca por usuario o por correo, con consulta preparada (evita inyección SQL).
        $stmt = $conexion->prepare('SELECT id, usuario, clave_hash FROM usuarios WHERE usuario = ? OR email = ? LIMIT 1');
        $stmt->bind_param('ss', $dato, $dato);
        $stmt->execute();
        $fila = $stmt->get_result()->fetch_assoc();

        if ($fila && password_verify($clave, $fila['clave_hash'])) {
            session_regenerate_id(true);               // nueva sesión al entrar
            $_SESSION['usuario_id']     = (int) $fila['id'];
            $_SESSION['usuario_nombre'] = $fila['usuario'];
            $destino = destino_seguro($_SESSION['volver'] ?? '');
            unset($_SESSION['volver']);
            header('Location: ' . $destino);
            exit;
        }
        // Mismo mensaje si falla el usuario o la clave: no damos pistas.
        $error = 'Usuario o contraseña incorrectos.';
    }
}

$titulo = 'Ingresar';
require DIR_INCLUDES . '/cabecera_auth.php';
?>
    <section class="bloque_seccion">
      <h2 class="titulo_seccion">Ingresar</h2>
      <p class="descripcion_seccion">Entrá para leer los consejos de Zote y Shakra.</p>
      <div class="auth_contenedor">
        <form class="comentarios_formulario panel_general" method="post" action="login.php" novalidate>
          <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">

          <label for="usuario">Usuario o correo</label>
          <input id="usuario" type="text" name="usuario" required maxlength="100" value="<?= h($dato) ?>" autocomplete="username">

          <label for="clave">Contraseña</label>
          <input id="clave" type="password" name="clave" required autocomplete="current-password">

          <?php if ($error): ?>
            <p class="comentarios_mensaje_formulario comentarios_mensaje_error"><?= h($error) ?></p>
          <?php endif; ?>

          <div class="comentarios_formulario_pie">
            <a href="registro.php">Crear cuenta</a>
            <button class="boton_general" type="submit">Ingresar</button>
          </div>
        </form>
      </div>
    </section>
<?php require DIR_INCLUDES . '/pie_auth.php'; ?>
