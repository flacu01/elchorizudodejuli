# Los 57 consejos de Zote y Shakra

Sitio web de fans sobre Hollow Knight y Silksong, donde Zote y Shakra
dan sus consejos a quien viaje por Hallownest y Pharloom.

## Secciones
- **Inicio**: presentación, guías, galería de los reinos y comentarios.
- **Hollow Knight**: consejos de Zote en Hallownest.
- **Silksong**: consejos de Shakra en Pharloom.
- **Créditos**: quién hizo el sitio.

## Tecnologías
HTML, CSS, JavaScript, PHP y MySQL (login con sesiones).

Hecho por Facundo Rodriguez y Valentino Bustamante

## Cómo correrlo en tu PC (XAMPP)
1. Clonar el repo dentro de `htdocs` (o bajar el ZIP y renombrar la carpeta a `elchorizudodejuli`):
   `git clone https://github.com/flacu01/elchorizudodejuli.git`
2. Encender **Apache** y **MySQL** en el panel de XAMPP.
3. Abrir `http://localhost/elchorizudodejuli/HTML/index.php`.

La primera vez, `HTML/conexion.php` crea sola la base `hollow_web` y sus tablas a partir de `bd/hollow_web.sql`.
Si tu MySQL usa otra clave o puerto, ajustá la lista `$intentos` en ese archivo.
