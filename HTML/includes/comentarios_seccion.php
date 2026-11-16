<?php
// includes/comentarios_seccion.php
// Dibuja el formulario y la lista de comentarios. Se incluye desde index.php.
require_once __DIR__ . '/../conexion.php';

$u = usuario_actual();
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

function hace_cuanto(int $segundos, string $fecha): string {
    if ($segundos < 60)    return 'hace un momento';
    if ($segundos < 3600)  return 'hace ' . floor($segundos / 60) . ' min';
    if ($segundos < 86400) return 'hace ' . floor($segundos / 3600) . ' h';
    return $fecha;
}

$comentarios = [];
$errorCarga = false;
try {
    // La diferencia de tiempo se calcula en MySQL para evitar problemas de zona horaria.
    $res = $conexion->query(
        "SELECT c.texto, u.usuario,
                TIMESTAMPDIFF(SECOND, c.creado, NOW()) AS segundos,
                DATE_FORMAT(c.creado, '%d/%m/%Y') AS fecha
         FROM comentarios c
         JOIN usuarios u ON u.id = c.usuario_id
         ORDER BY c.creado DESC, c.id DESC
         LIMIT 100"
    );
    $comentarios = $res->fetch_all(MYSQLI_ASSOC);
} catch (mysqli_sql_exception $e) {
    $errorCarga = true;
}

function mensaje_flash(?array $flash): void {
    if (!$flash) return;
    $clase = $flash['tipo'] === 'ok' ? 'comentarios_mensaje_ok' : 'comentarios_mensaje_error';
    echo '<p class="comentarios_mensaje_formulario ' . $clase . '" role="status">' . h($flash['texto']) . '</p>';
}
?>
<div class="comentarios_contenedor">

  <?php if ($u): ?>
    <form class="comentarios_formulario panel_general" method="post" action="comentar.php">
      <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
      <label for="c-texto">Comentar como <?= h($u['nombre']) ?></label>
      <textarea id="c-texto" name="texto" required maxlength="500" rows="4" placeholder="¿Qué te pareció la guía?"></textarea>
      <input type="text" name="web" class="comentarios_campo_trampa" tabindex="-1" autocomplete="off" aria-hidden="true">
      <div class="comentarios_formulario_pie">
        <span id="char-count">0 / 500</span>
        <button class="boton_general" type="submit">Publicar</button>
      </div>
      <?php mensaje_flash($flash); ?>
    </form>
    <script>
      (function () {
        var t = document.getElementById('c-texto'), c = document.getElementById('char-count');
        if (t && c) t.addEventListener('input', function () { c.textContent = t.value.length + ' / 500'; });
      })();
    </script>
  <?php else: ?>
    <div class="comentarios_formulario panel_general">
      <p>Para dejar un comentario necesitás una cuenta.
        <a href="login.php" style="color: var(--accent)">Ingresar</a> o
        <a href="registro.php" style="color: var(--accent)">crear cuenta</a>.</p>
      <?php mensaje_flash($flash); ?>
    </div>
  <?php endif; ?>

  <div class="comentarios_lista" aria-live="polite">
    <?php if ($errorCarga): ?>
      <p class="comentarios_vacio">No se pudieron cargar los comentarios. Revisá que exista la tabla <code>comentarios</code> en la base.</p>
    <?php elseif (!$comentarios): ?>
      <p class="comentarios_vacio">Todavía no hay comentarios. ¡Sé el primero en opinar!</p>
    <?php else: ?>
      <?php foreach ($comentarios as $c): ?>
        <article class="comentario_item panel_general">
          <div class="comentario_avatar"><?= h(mb_strtoupper(mb_substr($c['usuario'], 0, 1))) ?></div>
          <div class="comentario_cuerpo">
            <div class="comentario_encabezado">
              <strong><?= h($c['usuario']) ?></strong>
              <time><?= h(hace_cuanto((int) $c['segundos'], $c['fecha'])) ?></time>
            </div>
            <p><?= nl2br(h($c['texto'])) ?></p>
          </div>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
