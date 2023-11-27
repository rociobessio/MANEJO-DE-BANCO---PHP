-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-11-2023 a las 23:42:08
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sp-banco-modificado`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ajustes`
--

CREATE TABLE `ajustes` (
  `idAjuste` int(11) NOT NULL,
  `motivoAjuste` varchar(100) NOT NULL,
  `ajusteMonto` float NOT NULL,
  `numeroBuscado` int(11) NOT NULL,
  `numeroCuenta` int(11) NOT NULL,
  `ajusteSobre` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Los ajustes podrán ser correcciones sobre retiros o deposito';

--
-- Volcado de datos para la tabla `ajustes`
--

INSERT INTO `ajustes` (`idAjuste`, `motivoAjuste`, `ajusteMonto`, `numeroBuscado`, `numeroCuenta`, `ajusteSobre`) VALUES
(1, 'Deposito mal facturado.', 1000, 3, 4, 'Deposito'),
(2, 'Se genero mal el deposito.', 3000, 3, 4, 'Deposito'),
(3, 'La extraccion fue hecha incorrectamente.', 1250, 5, 5, 'Retiro'),
(4, 'Extraccion mal realizada.', 2900, 4, 3, 'Retiro');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas`
--

CREATE TABLE `cuentas` (
  `idCuenta` int(11) NOT NULL,
  `tipoCuenta` varchar(12) NOT NULL,
  `moneda` varchar(10) NOT NULL,
  `saldo` float NOT NULL,
  `estado` tinyint(1) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `urlImagen` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Contendra la info de las cuentas existentes';

--
-- Volcado de datos para la tabla `cuentas`
--

INSERT INTO `cuentas` (`idCuenta`, `tipoCuenta`, `moneda`, `saldo`, `estado`, `idUsuario`, `urlImagen`) VALUES
(1, 'CA$', '$', 5000, 0, 3, './ImagenesDeCuentas/2023/CA$_2594.jpg'),
(2, 'CAU$S', 'U$S', 18500, 1, 1, './ImagenesDeCuentas/2023/CAU$S_3220.jpg'),
(3, 'CCU$S', 'U$S', 8900, 1, 4, './ImagenesDeCuentas/2023/CCU$S_3574.jpg'),
(4, 'CC$', '$', 34000, 1, 5, './ImagenesDeCuentas/2023/CC$_7205.jpg'),
(5, 'CA$', '$', 14250, 1, 5, './ImagenesDeCuentas/2023/CA$_1872.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `depositos`
--

CREATE TABLE `depositos` (
  `idDeposito` int(11) NOT NULL,
  `numeroCuenta` int(11) NOT NULL,
  `tipoCuenta` varchar(12) NOT NULL,
  `importe` float NOT NULL,
  `fechaDeposito` datetime NOT NULL,
  `moneda` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Contendra todos los depositos realizados';

--
-- Volcado de datos para la tabla `depositos`
--

INSERT INTO `depositos` (`idDeposito`, `numeroCuenta`, `tipoCuenta`, `importe`, `fechaDeposito`, `moneda`) VALUES
(1, 2, 'CAU$S', 1000, '2023-11-25 00:00:00', 'U$S'),
(2, 5, 'CA$', 7000, '2023-11-25 00:00:00', '$'),
(3, 4, 'CC$', 10000, '2023-11-25 00:00:00', '$'),
(4, 3, 'CCU$S', 5000, '2023-11-25 00:00:00', 'U$S'),
(5, 2, 'CAU$S', 18000, '2023-11-25 00:00:00', 'U$S');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `retiros`
--

CREATE TABLE `retiros` (
  `idRetiro` int(11) NOT NULL,
  `numeroCuenta` int(11) NOT NULL,
  `tipoCuenta` varchar(12) NOT NULL,
  `importeRetiro` float NOT NULL,
  `fechaExtraccion` datetime NOT NULL,
  `moneda` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='La tabla contendrá todos los retiros realizados en el banco.';

--
-- Volcado de datos para la tabla `retiros`
--

INSERT INTO `retiros` (`idRetiro`, `numeroCuenta`, `tipoCuenta`, `importeRetiro`, `fechaExtraccion`, `moneda`) VALUES
(1, 2, 'CAU$S', 2500, '2023-11-25 00:00:00', 'U$S'),
(2, 2, 'CAU$S', 500, '2023-11-25 00:00:00', 'U$S'),
(3, 4, 'CC$', 2000, '2023-11-25 00:00:00', '$'),
(4, 3, 'CCU$S', 9000, '2023-11-25 00:00:00', 'U$S'),
(5, 5, 'CA$', 3000, '2023-11-25 00:00:00', '$');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `idUsuario` int(11) NOT NULL,
  `nombre` varchar(70) NOT NULL,
  `apellido` varchar(70) NOT NULL,
  `tipoDocumento` varchar(10) NOT NULL,
  `numeroDocumento` varchar(25) NOT NULL,
  `email` varchar(50) NOT NULL,
  `rol` varchar(15) NOT NULL,
  `clave` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='La tabla contendra la informacion sobre los usuarios.';

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`idUsuario`, `nombre`, `apellido`, `tipoDocumento`, `numeroDocumento`, `email`, `rol`, `clave`) VALUES
(1, 'Rocio', 'Bessio', 'DNI', '45013997', 'rocibessio@gmail.com', 'Cliente', '123rocio'),
(2, 'Admin', 'Admin', 'DNI', '0101010101', 'admin@gmail.com', 'Admin', '123admin'),
(3, 'Carlos', 'Garcia', 'DNI', '278193102', 'charlygarcia@yahoo.com.ar', 'Cliente', '123charly'),
(4, 'Josefina', 'Martinez', 'LC', '3102930123', 'josemart@yahoo.com.ar', 'Cliente', '123josefina'),
(5, 'Martin', 'Ramirez', 'LC', '44789123', 'martinrami@gmail.com', 'Cliente', '123martin');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `ajustes`
--
ALTER TABLE `ajustes`
  ADD PRIMARY KEY (`idAjuste`);

--
-- Indices de la tabla `cuentas`
--
ALTER TABLE `cuentas`
  ADD PRIMARY KEY (`idCuenta`);

--
-- Indices de la tabla `depositos`
--
ALTER TABLE `depositos`
  ADD PRIMARY KEY (`idDeposito`);

--
-- Indices de la tabla `retiros`
--
ALTER TABLE `retiros`
  ADD PRIMARY KEY (`idRetiro`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`idUsuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `ajustes`
--
ALTER TABLE `ajustes`
  MODIFY `idAjuste` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `cuentas`
--
ALTER TABLE `cuentas`
  MODIFY `idCuenta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `depositos`
--
ALTER TABLE `depositos`
  MODIFY `idDeposito` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `retiros`
--
ALTER TABLE `retiros`
  MODIFY `idRetiro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
