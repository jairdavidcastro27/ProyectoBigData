-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-11-2024 a las 08:58:51
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tdah`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `distrito`
--

CREATE TABLE `distrito` (
  `idDistrito` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `distrito`
--

INSERT INTO `distrito` (`idDistrito`, `nombre`) VALUES
(1, 'Parcona'),
(2, 'Tinguiña');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pacientes`
--

CREATE TABLE `pacientes` (
  `idPaciente` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `edad` int(11) NOT NULL,
  `genero` enum('Masculino','Femenino') NOT NULL,
  `diagnostico` text DEFAULT NULL,
  `fecha` date NOT NULL,
  `tratamiento` text DEFAULT NULL,
  `medicamento` text DEFAULT NULL,
  `frecuenciaConsulta` varchar(300) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `idDistrito` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pacientes`
--

INSERT INTO `pacientes` (`idPaciente`, `nombre`, `edad`, `genero`, `diagnostico`, `fecha`, `tratamiento`, `medicamento`, `frecuenciaConsulta`, `observaciones`, `estado`, `idDistrito`) VALUES
(1, 'jair ', 23, 'Masculino', 'tdah severo', '2024-11-13', 'medicamento diario', 'doloflan', '2 veces por mes', 'distraido en la escuela', 'activo', NULL),
(2, 'juan', 21, 'Masculino', 'tdah', '2024-11-20', 'terapia psicologca', 'paracetamol', 'cada 6 meses', 'recuperacion leve a la distraccion', 'inactivo', NULL),
(3, 'wer', 32, 'Femenino', 'adad', '2024-11-21', 'adasd', 'adsasd', 'adsd', 'asdasd', 'activo', NULL),
(4, 'wer', 32, 'Femenino', 'adad', '2024-11-21', 'adasd', 'adsasd', 'adsd', 'asdasd', 'activo', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('doctor','informatico') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`, `role`) VALUES
(1, 'doc1', '1234', 'doctor'),
(2, 'doctor1', 'password', 'doctor'),
(3, 'info1', '1234', 'informatico');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `distrito`
--
ALTER TABLE `distrito`
  ADD PRIMARY KEY (`idDistrito`);

--
-- Indices de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  ADD PRIMARY KEY (`idPaciente`),
  ADD KEY `fk_distrito` (`idDistrito`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `distrito`
--
ALTER TABLE `distrito`
  MODIFY `idDistrito` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `idPaciente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `pacientes`
--
ALTER TABLE `pacientes`
  ADD CONSTRAINT `fk_distrito` FOREIGN KEY (`idDistrito`) REFERENCES `distrito` (`idDistrito`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
