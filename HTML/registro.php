<?php
$raiz = '';
require __DIR__ . '/includes/sesion.php';
require __DIR__ . '/conexion.php';

if (usuario_actual()) { header('Location: index.php'); exit; }

$errores = [];
$usuario = '';
$email   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $email   = strtolower(trim($_POST['email'] ?? ''));
    $clave   = $_POST['clave'] ?? '';
    $clave2  = $_POST['clave2'] ?? '';

    if (!csrf_validar($_POST['csrf'] ?? '')) {
        $errores[] = 'La sesión expiró. Recargá la página e intentá de nuevo.';
    }
    if (!preg_match('/^[A-Za-z0-9_]{3,20}$/', $usuario)) {
        $errores[] = 'El usuario debe tener de 3 a 20 caracteres: letras, números o guion bajo.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
        $errores[] = 'Escribí un correo válido.';
    }
    if (strlen($clave) < 8) {
        $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
    }
    if ($clave !== $clave2) {
        $errores[] = 'Las contraseñas no coinciden.';
    }

    if (!$errores) {
        $hash = password_hash($clave, PASSWORD_DEFAULT);   // nunca se guarda la clave real
        try {
            $stmt = $conexion->prepare('INSERT INTO usuarios (usuario, email, clave_hash) VALUES (?, ?, ?)');
            $stmt->bind_param('sss', $usuario, $email, $hash);
            $stmt->execute();

            // Registro correcto: dejamos al usuario logueado.
            session_regenerate_id(true);
            $_SESSION['usuario_id']     = $conexion->insert_id;
            $_SESSION['usuario_nombre'] = $usuario;
            header('Location: index.php');
            exit;
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062) {       // 1062 = dato duplicado (UNIQUE)
                $errores[] = 'Ese usuario o ese correo ya está registrado.';
            } else {
                $errores[] = 'No se pudo crear la cuenta. Intentá de nuevo.';
            }
        }
    }
}

$titulo = 'Crear cuenta';
require DIR_INCLUDES . '/cabecera_auth.php';
?>
    <section class="bloque_seccion">
      <h2 class="titulo_seccion">Crear cuenta</h2>
      <p class="descripcion_seccion">Registrate para entrar a las guías de Zote y Shakra.</p>
      <div class="auth_contenedor">
        <form class="comentarios_formulario panel_general" method="post" action="registro.php" novalidate>
          <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">

          <label for="usuario">Usuario</label>
          <input id="usuario" type="text" name="usuario" required maxlength="20" value="<?= h($usuario) ?>" autocomplete="username">

          <label for="email">Correo</label>
          <input id="email" type="email" name="email" required maxlength="100" value="<?= h($email) ?>" autocomplete="email">

          <label for="clave">Contraseña</label>
          <input id="clave" type="password" name="clave" required minlength="8" autocomplete="new-password">
          <p class="comentarios_nota_campo">Mínimo 8 caracteres.</p>

          <label for="clave2">Repetir contraseña</label>
          <input id="clave2" type="password" name="clave2" required minlength="8" autocomplete="new-password">

          <?php foreach ($errores as $e): ?>
            <p class="comentarios_mensaje_formulario comentarios_mensaje_error"><?= h($e) ?></p>
          <?php endforeach; ?>

          <div class="comentarios_formulario_pie">
            <a href="login.php">Ya tengo cuenta</a>
            <button class="boton_general" type="submit">Crear cuenta</button>
          </div>
        </form>
      </div>
    </section>
<?php require DIR_INCLUDES . '/pie_auth.php'; ?>
