<?php $raiz = "../"; require __DIR__ . "/../includes/sesion.php"; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sobre nosotros · Los 57 consejos de Zote y Shakra</title>
  <meta name="description" content="Quiénes somos, cómo nació esta guía hecha por fans y cómo está construida.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700;900&family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/home.css">
  <script>document.documentElement.classList.add('js_activo')</script>
  <style>
    .sn_tarjeta { padding: 1.4rem; }
    .sn_tarjeta h3 { font-family: var(--f-title); font-size: 1.35rem; margin-bottom: .4rem; }
    .sn_tarjeta p { font-size: 1.1rem; color: var(--muted); }
    .sn_texto { max-width: 820px; margin: 0 auto; text-align: center; font-size: 1.2rem; }
    .sn_texto p + p { margin-top: 1rem; }
    .sn_equipo a { color: var(--accent); word-break: break-all; }
  </style>
</head>
<body class="pagina_inicio">
  <header class="cabecera_sitio">
    <nav class="nav_principal" aria-label="Principal">
      <?php require DIR_INCLUDES . "/barra_sesion.php"; ?>
      <ul>
        <li class="nav_item_izquierda"><a href="hollow.php">Hollow Knight</a></li>
        <li class="nav_item_centro"><a href="../index.php" class="nav_enlace_inicio">✦ Inicio ✦</a></li>
        <li class="nav_item_centro"><a href="sobre-nosotros.php" class="nav_enlace_activo">Sobre nosotros</a></li>
        <li class="nav_item_derecha"><a href="silksong.php">Silksong</a></li>
      </ul>
    </nav>
    <div class="barra_progreso" id="progress"></div>
  </header>
  <main>
    <section class="hero_inicio">
      <p class="etiqueta_superior_titulo">✦ Quiénes somos ✦</p>
      <div class="titulo_hero_inicio"><h1>Sobre nosotros</h1></div>
      <p class="texto_intro_hero_inicio">
        Somos Facundo y Valentino, dos fans de Hollow Knight que armaron esta guía para recorrer Hallownest y Pharloom
        de la mano de los dos personajes con más personalidad (y más opiniones) de la saga.
      </p>
    </section>

    <section class="bloque_seccion" id="proyecto">
      <h2 class="titulo_seccion aparicion_scroll">El proyecto</h2>
      <div class="sn_texto aparicion_scroll">
        <p><strong>Los 57 consejos de Zote y Shakra</strong> es una guía hecha por fans, para fans. En Hallownest te acompaña el todopoderoso
          Zote con sus sublimes preceptos, y en Pharloom te guía Shakra, la cartógrafa, con lo que anotó en sus mapas.</p>
        <p>Dos reinos, dos guías y dos formas muy distintas de aconsejar: la de Zote, con muchísimo ego; la de Shakra, de exploradora a exploradora.</p>
      </div>
    </section>

    <section class="estadisticas_inicio panel_general aparicion_scroll" aria-label="Números del proyecto">
      <div class="estadistica_item"><span class="estadistica_numero">2</span><span class="estadistica_texto">reinos</span></div>
      <div class="estadistica_item"><span class="estadistica_numero">2</span><span class="estadistica_texto">guías</span></div>
      <div class="estadistica_item"><span class="estadistica_numero">57</span><span class="estadistica_texto">consejos en total</span></div>
    </section>

    <section class="bloque_seccion" id="que-encontraras">
      <h2 class="titulo_seccion aparicion_scroll">¿Qué vas a encontrar?</h2>
      <p class="descripcion_seccion aparicion_scroll">Todo lo que tiene la página, en pocas palabras.</p>
      <div class="guias_inicio">
        <article class="sn_tarjeta panel_general aparicion_scroll"><h3>Dos guías, dos reinos</h3><p>27 consejos de Zote para Hallownest y 30 de Shakra para Pharloom, cada uno con su estilo.</p></article>
        <article class="sn_tarjeta panel_general aparicion_scroll"><h3>Consejo al azar</h3><p>¿No sabes por dónde empezar? Deja que el destino (o Zote) elija un consejo por ti.</p></article>
        <article class="sn_tarjeta panel_general aparicion_scroll"><h3>Galería de los reinos</h3><p>Un lugar para ver las mejores imágenes de Hallownest y Pharloom.</p></article>
        <article class="sn_tarjeta panel_general aparicion_scroll"><h3>Cuentas y comentarios</h3><p>Crea tu cuenta para dejar tu opinión y leer lo que piensan otros viajeros.</p></article>
      </div>
    </section>

    <section class="bloque_seccion" id="tecnologia">
      <h2 class="titulo_seccion aparicion_scroll">Cómo está hecho</h2>
      <div class="sn_texto aparicion_scroll">
        <p>La página está programada a mano con las herramientas que aprendimos en el proceso. Las cuentas y los comentarios se guardan en una base de datos propia.</p>
        <div class="etiquetas_tarjeta_guia" style="justify-content:center">
          <span>HTML</span><span>CSS</span><span>JavaScript</span><span>PHP</span><span>MySQL</span><span>XAMPP</span><span>Git y GitHub</span>
        </div>
      </div>
    </section>

    <section class="bloque_seccion sn_equipo" id="equipo">
      <h2 class="titulo_seccion aparicion_scroll">El equipo</h2>
      <p class="descripcion_seccion aparicion_scroll">Las dos personas detrás de la guía.</p>
      <div class="guias_inicio">
        <article class="tarjeta_guia tarjeta_guia_zote panel_general aparicion_scroll">
          <div>
            <h3>Facundo</h3>
            <p>Fan de Hollow Knight y co-creador de esta guía.</p>
            <p><a href="mailto:rodriguez.facundonehuen@gmail.com">rodriguez.facundonehuen@gmail.com</a></p>
          </div>
        </article>
        <article class="tarjeta_guia tarjeta_guia_shakra panel_general aparicion_scroll">
          <div>
            <h3>Valentino</h3>
            <p>Fan de Hollow Knight y co-creador de esta guía.</p>
            <p><a href="mailto:bustavalentino@gmail.com">bustavalentino@gmail.com</a></p>
          </div>
        </article>
      </div>
    </section>

    <section class="bloque_seccion" id="aviso" style="text-align:center">
      <h2 class="titulo_seccion aparicion_scroll">Aviso importante</h2>
      <div class="sn_texto aparicion_scroll">
        <p>Hollow Knight y Hollow Knight: Silksong son juegos de Team Cherry; las imágenes y los personajes pertenecen a sus creadores.
          Este es un proyecto sin fines de lucro, hecho con cariño por fans.</p>
        <p>¿Encontraste un error o tienes una sugerencia? Escríbenos a alguno de los correos de arriba.</p>
      </div>
    </section>
  </main>

  <footer class="pie_sitio oni">
    <div class="pie_cuadricula">
      <div class="pie_columna pie_columna_marca">
        <h4 class="pie_logo">Los 57 consejos<br>de Zote y Shakra</h4>
        <p>Una guía hecha por fans, para fans. Dos reinos, dos guías y muchísimo ego (el de Zote, sobre todo). Hollow Knight y Hollow Knight: Silksong son creaciones de Team Cherry.</p>
        <div class="pie_insignias"><span>27 consejos de Zote</span><span>30 de Shakra</span></div>
      </div>
      <div class="pie_columna">
        <h5>Navegar</h5>
        <ul>
          <li><a href="../index.php">Inicio</a></li>
          <li><a href="sobre-nosotros.php">Sobre nosotros</a></li>
          <li><a href="hollow.php">Hollow Knight</a></li>
          <li><a href="silksong.php">Silksong</a></li>
          <li><a href="#" data-random>Consejo al azar ✦</a></li>
        </ul>
      </div>
      <div class="pie_columna">
        <h5>Los reinos</h5>
        <ul>
          <li><a href="hollow.php">Hallownest · con Zote</a></li>
          <li><a href="silksong.php">Pharloom · con Shakra</a></li>
        </ul>
        <p class="pie_frase">“Mi victoria está escrita en las estrellas.”<br>— Zote</p>
      </div>
      <div class="pie_columna">
        <h5>Contacto</h5>
        <ul>
          <li><a href="mailto:rodriguez.facundonehuen@gmail.com">rodriguez.facundonehuen@gmail.com</a></li>
          <li><a href="mailto:valenbusta151@gmail.com">valenbusta151@gmail.com</a></li>
        </ul>
        <p style="margin-top:.9rem">¿Encontraste un error o tienes una sugerencia? Escríbenos.</p>
      </div>
    </div>
    <div class="pie_legal">
      <div class="pie_legal_izquierda">
        <p>Todos los personajes y marcas registradas pertenecen a sus respectivos propietarios.</p>
        <p>Esto es un proyecto sin fines de lucro.</p>
      </div>
      <div class="pie_legal_derecha">
        <button class="pie_boton_volver" type="button">Volver arriba ↑</button>
        <p>© <span class="year">2026</span> · Hecho con cariño por fans</p>
      </div>
    </div>
  </footer>
  <button class="boton_subir" id="to-top" type="button" aria-label="Volver arriba">↑</button>

  <script src="../js/script.js"></script>
</body>
</html>
