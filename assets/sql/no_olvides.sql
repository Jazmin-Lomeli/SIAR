-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-04-2023 a las 02:51:26
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `no_olvides`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `arduino`
--

CREATE TABLE `arduino` (
  `finger_status` varchar(15) NOT NULL,
  `finger_err` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `arduino`
--

INSERT INTO `arduino` (`finger_status`, `finger_err`) VALUES
('REGISTER', 'Todo_bien');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia`
--

CREATE TABLE `asistencia` (
  `fecha` date NOT NULL,
  `id_emp` int(11) NOT NULL,
  `entrada` time NOT NULL,
  `salida` time DEFAULT NULL,
  `observacion` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asistencia`
--

INSERT INTO `asistencia` (`fecha`, `id_emp`, `entrada`, `salida`, `observacion`) VALUES
('2023-01-02', 1, '08:02:00', '01:05:09', NULL),
('2023-01-03', 1, '08:02:00', '01:05:09', NULL),
('2023-01-04', 1, '08:02:00', '01:05:09', NULL),
('2023-01-05', 1, '08:01:00', '01:05:09', NULL),
('2023-01-06', 1, '08:15:00', '01:05:09', NULL),
('2022-12-07', 1, '08:14:59', '01:05:09', NULL),
('2022-12-27', 1, '08:14:59', '01:05:09', NULL),
('2022-12-28', 1, '00:00:00', '00:00:00', 'Enfermedad'),
('2022-12-29', 1, '08:20:00', '01:05:09', NULL),
('2023-01-09', 1, '08:17:00', '01:05:09', NULL),
('2023-01-10', 1, '08:00:00', '01:05:09', NULL),
('2023-01-11', 1, '08:30:00', '01:05:09', NULL),
('2023-01-12', 1, '00:00:00', '00:00:00', 'Enfermedad'),
('2023-01-13', 1, '08:00:00', '01:05:09', NULL),
('2023-01-16', 1, '08:08:00', '01:05:09', NULL),
('2023-01-17', 1, '00:00:00', '01:05:09', 'Enfermedad'),
('2023-01-20', 1, '08:12:00', '01:05:09', NULL),
('2023-01-23', 1, '08:12:00', '01:05:09', NULL),
('2023-03-31', 1, '02:15:18', NULL, NULL),
('2023-03-31', 2, '02:32:51', NULL, NULL),
('2023-03-31', 4, '02:39:13', NULL, NULL),
('2023-04-03', 2, '08:01:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `apellido` varchar(30) NOT NULL,
  `seg_apellido` varchar(30) NOT NULL,
  `huella` int(11) NOT NULL DEFAULT 0,
  `tipo` int(11) NOT NULL,
  `f_registro` date NOT NULL,
  `telefono` varchar(10) NOT NULL,
  `jornada` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `nombre`, `apellido`, `seg_apellido`, `huella`, `tipo`, `f_registro`, `telefono`, `jornada`) VALUES
(1, 'Jazmin Alejandra', 'Lomeli', 'Zermeño', 1, 2, '2022-06-20', '3781220818', 25),
(2, 'Sandra', 'lomeli', 'Zermeño', 1, 2, '2023-03-31', '3786565646', 20),
(4, 'Jovanna', 'Lomelini', 'Zermeña', 1, 3, '2023-03-31', '3786464646', 40),
(5, 'Pedro', 'Salcedo', 'Arambula', 0, 4, '2023-04-03', '3789564615', 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `huella`
--

CREATE TABLE `huella` (
  `id_emp` int(11) NOT NULL,
  `id_huella` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `huella`
--

INSERT INTO `huella` (`id_emp`, `id_huella`) VALUES
(1, 1),
(2, 2),
(4, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `huella_auxiliar`
--

CREATE TABLE `huella_auxiliar` (
  `id_a_manipular` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `huella_auxiliar`
--

INSERT INTO `huella_auxiliar` (`id_a_manipular`) VALUES
(0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recordatorios`
--

CREATE TABLE `recordatorios` (
  `r_nombre` varchar(30) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  `caracter` varchar(15) NOT NULL,
  `inicio` date NOT NULL,
  `fin` date NOT NULL,
  `id_recordatorio` int(11) NOT NULL,
  `r_tipo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `recordatorios`
--

INSERT INTO `recordatorios` (`r_nombre`, `descripcion`, `caracter`, `inicio`, `fin`, `id_recordatorio`, `r_tipo`) VALUES
('Sandra', 'Holi', 'Urgente', '2023-03-14', '2023-04-01', 3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_empleado`
--

CREATE TABLE `tipo_empleado` (
  `t_nombre` varchar(30) NOT NULL,
  `tipo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_empleado`
--

INSERT INTO `tipo_empleado` (`t_nombre`, `tipo`) VALUES
('General', 1),
('Profesor', 2),
('Administrativo', 3),
('Limpieza', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(10) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `ultimo_log` date DEFAULT NULL,
  `f_ingreso` date DEFAULT NULL,
  `cambio_contrasena` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `ultimo_log`, `f_ingreso`, `cambio_contrasena`) VALUES
(1, 'papita', '$2y$10$vHu94DsRAX2svpgj1V72Ge2DPPRFnMrDfi/j2I2CcQ1D4qixZ7Df2', '2023-04-03', '2023-08-10', '2023-03-30'),
(2, 'admin', '$2y$10$kqIHhc0PJdOk.S3PawMZrO0zaqVjkh9pgEuCu9ES4wsht4FLII5kW', '2023-03-30', '2023-03-09', NULL),
(3, 'root', '$2y$10$Z07s8b7dNkPSgwAVN.5hE.5ssU4XXda6GBSZBh5Htc/F0zeMZoRhe', NULL, '2023-03-30', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD KEY `asistencia_empleado` (`id_emp`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `empleado_tipo_empleado` (`tipo`);

--
-- Indices de la tabla `huella`
--
ALTER TABLE `huella`
  ADD PRIMARY KEY (`id_huella`),
  ADD KEY `empleado_huella` (`id_emp`);

--
-- Indices de la tabla `recordatorios`
--
ALTER TABLE `recordatorios`
  ADD PRIMARY KEY (`id_recordatorio`),
  ADD KEY `tipo_empleado_recordatorio` (`r_tipo`);

--
-- Indices de la tabla `tipo_empleado`
--
ALTER TABLE `tipo_empleado`
  ADD PRIMARY KEY (`tipo`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `huella`
--
ALTER TABLE `huella`
  MODIFY `id_huella` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `recordatorios`
--
ALTER TABLE `recordatorios`
  MODIFY `id_recordatorio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tipo_empleado`
--
ALTER TABLE `tipo_empleado`
  MODIFY `tipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD CONSTRAINT `asistencia_empleado` FOREIGN KEY (`id_emp`) REFERENCES `empleados` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `empleado_tipo_empleado` FOREIGN KEY (`tipo`) REFERENCES `tipo_empleado` (`tipo`);

--
-- Filtros para la tabla `huella`
--
ALTER TABLE `huella`
  ADD CONSTRAINT `empleado_huella` FOREIGN KEY (`id_emp`) REFERENCES `empleados` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `recordatorios`
--
ALTER TABLE `recordatorios`
  ADD CONSTRAINT `tipo_empleado_recordatorio` FOREIGN KEY (`r_tipo`) REFERENCES `tipo_empleado` (`tipo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
