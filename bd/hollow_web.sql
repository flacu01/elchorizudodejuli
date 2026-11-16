-- bd/hollow_web.sql
-- Estructura de la base "hollow_web". Se importa en phpMyAdmin DENTRO de la base ya creada.
-- Más adelante, cuando haya usuarios de prueba, se vuelve a exportar y reemplaza este archivo.

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `clave_hash` varchar(255) NOT NULL,
  `creado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_usuario` (`usuario`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
