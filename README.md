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
1. Copiar esta carpeta dentro de `htdocs` (ej: `C:\xampp\htdocs\elchorizudodejuli`).
2. Encender Apache y MySQL en XAMPP.
3. En phpMyAdmin crear la base `hollow_web` (cotejamiento `utf8mb4_general_ci`) e importar `bd/hollow_web.sql`.
4. Copiar `HTML/conexion.ejemplo.php` como `HTML/conexion.php` y ajustar clave y puerto.
5. Abrir `http://localhost/elchorizudodejuli/HTML/index.php`.
