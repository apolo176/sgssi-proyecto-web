-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: db
-- Tiempo de generación: 10-10-2025 a las 17:08:38
-- Versión del servidor: 10.8.2-MariaDB-1:10.8.2+maria~focal
-- Versión de PHP: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Base de datos: `database`

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `usuarios`
-- --------------------------------------------------------

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` VARCHAR(50) DEFAULT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `dni` VARCHAR(20) NOT NULL,
  `telefono` int(8) NOT NULL,
  `fechaNacimiento` date NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `contrasena` VARCHAR(255) NOT NULL,
  `login_fallidos` int NOT NULL DEFAULT 0,
  `momento_login` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=7;

-- Volcado de datos para la tabla `usuarios`
INSERT INTO `usuarios` (`id`, `usuario`, `nombre`, `dni`, `telefono`, `fechaNacimiento`, `email`, `contrasena`, `login_fallidos`, `momento_login`) VALUES
(1, '', 'mikel', '', 0, '0000-00-00', '', '', 0, NULL),
(2, '', 'aitor', '', 0, '0000-00-00', '', '', 0, NULL);

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `videojuegos`
-- --------------------------------------------------------

CREATE TABLE `videojuegos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `genero` VARCHAR(50) NOT NULL,
  `fechaLanzamiento` date NOT NULL,
  `precioSalida` decimal(11,2) NOT NULL,
  `notaMetacritic` decimal(4,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=6;

-- Volcado de datos para la tabla `videojuegos`
INSERT INTO `videojuegos` (`id`, `nombre`, `genero`, `fechaLanzamiento`, `precioSalida`, `notaMetacritic`) VALUES
(1, 'Minecraft', 'Sandbox', '2011-11-18', 19.95, 8.30),
(2, 'Grand Theft Auto 5', 'Mundo abierto', '2013-09-17', 49.99, 8.50);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
