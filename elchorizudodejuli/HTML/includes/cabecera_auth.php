<?php /* Cabecera común de login.php y registro.php. Usa $titulo y $raiz. */ ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($titulo) ?> · Los 57 consejos de Zote y Shakra</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700;900&family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/home.css">
</head>
<body class="pagina_inicio">
  <header class="cabecera_sitio">
    <nav class="nav_principal" aria-label="Principal">
      <?php require DIR_INCLUDES . '/barra_sesion.php'; ?>
      <ul>
        <li class="nav_item_izquierda"><a href="pages/hollow.php">Hollow Knight</a></li>
        <li class="nav_item_centro"><a href="index.php" class="nav_enlace_inicio">✦ Inicio ✦</a></li>
        <li class="nav_item_derecha"><a href="pages/silksong.php">Silksong</a></li>
      </ul>
    </nav>
  </header>
  <main>
