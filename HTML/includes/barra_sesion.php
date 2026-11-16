<?php $u = usuario_actual(); ?>
<div class="barra_sesion">
  <?php if ($u): ?>
    <span>Hola, <strong><?= h($u['nombre']) ?></strong></span>
    <a href="<?= $raiz ?>logout.php">Salir</a>
  <?php else: ?>
    <a href="<?= $raiz ?>login.php">Ingresar</a>
    <a href="<?= $raiz ?>registro.php">Crear cuenta</a>
  <?php endif; ?>
</div>
