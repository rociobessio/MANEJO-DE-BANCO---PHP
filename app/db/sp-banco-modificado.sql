-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-11-2023 a las 04:24:39
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
(1, 'Deposito mal facturado.', 1000, 3, 100004, 'Deposito'),
(2, 'Se genero mal el deposito.', 3000, 3, 100004, 'Deposito'),
(3, 'La extraccion fue hecha incorrectamente.', 1250, 5, 100005, 'Retiro'),
(4, 'Extraccion mal realizada.', 2900, 4, 100006, 'Retiro'),
(5, 'Extraccion mal realizada.', 1000, 3, 100004, 'Retiro'),
(6, 'Deposito mal realizado.', 2000, 8, 100007, 'Deposito'),
(7, 'Deposito mal realizado.', 500, 6, 100006, 'Deposito'),
(8, 'Extraccion mal realizado.', 500, 3, 100004, 'Retiro');

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
  `urlImagen` varchar(200) NOT NULL,
  `nroDocumento` varchar(25) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `tipoDocumento` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Contendra la info de las cuentas existentes';

--
-- Volcado de datos para la tabla `cuentas`
--

INSERT INTO `cuentas` (`idCuenta`, `tipoCuenta`, `moneda`, `saldo`, `estado`, `urlImagen`, `nroDocumento`, `nombre`, `apellido`, `tipoDocumento`, `email`) VALUES
(100001, 'CA$', '$', 5000, 0, './ImagenesDeCuentas/2023/CA$_2594.jpg', '278193102', '', '', '', 'gaston@gmail.com'),
(100002, 'CAU$S', 'U$S', 18500, 0, './ImagenesDeCuentas/2023/CAU$S_3220.jpg', '45013997', '', '', '', 'mariano@gmail.com'),
(100003, 'CCU$S', 'U$S', 3900, 1, './ImagenesDeCuentas/2023/CCU$S_3574.jpg', '	 3102930123', '', '', '', 'lucas@gmail.com'),
(100004, 'CC$', '$', 35500, 0, './ImagenesDeCuentas/2023/CC$_7205.jpg', '	 44789123', '', '', '', 'renatab@outlook.com.ar'),
(100005, 'CA$', '$', 44250, 1, './ImagenesDeCuentas/2023/CA$_1872.jpg', '	 44789123', '', '', '', 'lucrecia@yahoo.com.ar'),
(100006, 'CAU$S', 'U$S', 59500, 1, './ImagenesDeCuentas/2023/CAU$S_8555.jpg', '348291032', '', '', '', 'nicop@outlook.com.ar'),
(100007, 'CCU$S', 'U$S', 98000, 0, './ImagenesDeCuentas/2023/CCU$S_565.jpg', '37182031', '', '', '', 'javier@gmail.com'),
(100008, 'CCU$S', 'U$S', 100000, 1, './ImagenesDeCuentas/2023/CCU$S_7383.jpg', '37182031', 'rocio', 'bessio', 'DNI', 'rociobessio@gmail.com');

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
  `moneda` varchar(10) NOT NULL,
  `nroOperacion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Contendra todos los depositos realizados';

--
-- Volcado de datos para la tabla `depositos`
--

INSERT INTO `depositos` (`idDeposito`, `numeroCuenta`, `tipoCuenta`, `importe`, `fechaDeposito`, `moneda`, `nroOperacion`) VALUES
(1, 100002, 'CAU$S', 1000, '2023-11-25 00:00:00', 'U$S', 930123),
(2, 100005, 'CA$', 7000, '2023-11-25 00:00:00', '$', 839120),
(3, 100004, 'CC$', 10000, '2023-11-25 00:00:00', '$', 123943),
(4, 100003, 'CCU$S', 5000, '2023-11-25 00:00:00', 'U$S', 684023),
(5, 100002, 'CAU$S', 18000, '2023-11-25 00:00:00', 'U$S', 381941),
(6, 100006, 'CAU$S', 12000, '2023-11-26 00:00:00', 'U$S', 489512),
(7, 100007, 'CCU$S', 2000, '2023-11-27 00:00:00', 'U$S', 759313),
(8, 100007, 'CCU$S', 2000, '2023-11-27 00:00:00', 'U$S', 689034),
(9, 100006, 'CAU$S', 2000, '2023-11-27 00:00:00', 'U$S', 312401),
(10, 100006, 'CAU$S', 2000, '2023-11-27 00:00:00', 'U$S', 418432),
(11, 100005, 'CA$', 10000, '2023-11-28 00:00:00', '$', 469079),
(12, 100005, 'CA$', 10000, '2023-11-28 00:00:00', '$', 997747),
(13, 100005, 'CA$', 10000, '2023-11-28 00:00:00', '$', 663485);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logsacceso`
--

CREATE TABLE `logsacceso` (
  `idLog` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `accion` varchar(50) NOT NULL,
  `fechaAccion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Contendra los logs de los accesos al consumir algun recurso';

--
-- Volcado de datos para la tabla `logsacceso`
--

INSERT INTO `logsacceso` (`idLog`, `idUsuario`, `accion`, `fechaAccion`) VALUES
(1, 1, 'Logueo', '2023-11-28 00:00:00'),
(2, 9, 'Logueo', '2023-11-28 00:00:00'),
(3, 9, 'Traer Usuarios', '2023-11-28 00:00:00'),
(4, 1, 'Logueo', '2023-11-28 00:00:00'),
(5, 1, 'Traer Cuentas', '2023-11-28 00:00:00'),
(6, 1, 'Traer Cuenta', '2023-11-28 00:00:00'),
(7, 9, 'Logueo', '2023-11-28 00:00:00'),
(8, 9, 'Baja Cuenta', '2023-11-28 00:00:00'),
(9, 9, 'Baja Cuenta', '2023-11-28 00:00:00'),
(10, 1, 'Consultar Cuenta', '2023-11-28 00:00:00'),
(11, 1, 'Total depositado por tipo cuenta y fecha', '2023-11-28 00:00:00'),
(12, 1, 'Depositos por usuario', '2023-11-28 00:00:00'),
(13, 1, 'Depositos por usuario', '2023-11-28 00:00:00'),
(14, 1, 'Depositos por tipo cuenta', '2023-11-28 00:00:00'),
(15, 1, 'Depositos entre fechas sort nombre', '2023-11-28 00:00:00'),
(16, 1, 'Depositos por moneda', '2023-11-28 00:00:00'),
(17, 1, 'Retiros por moneda', '2023-11-28 00:00:00'),
(18, 1, 'Retiros por tipo cuenta', '2023-11-28 00:00:00'),
(19, 1, 'Retiros y depositos por usuario', '2023-11-28 00:00:00'),
(20, 1, 'Retiros y depositos por usuario', '2023-11-28 00:00:00'),
(21, 1, 'Logueo', '2023-11-28 00:00:00'),
(22, 1, 'Traer Depositos', '2023-11-28 00:00:00'),
(23, 5, 'Logueo', '2023-11-28 00:00:00'),
(24, 1, 'Traer Depositos', '2023-11-28 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logstransacciones`
--

CREATE TABLE `logstransacciones` (
  `idLogTransaccion` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `nroOperacion` int(11) NOT NULL,
  `fechaTransaccion` datetime NOT NULL,
  `sobre` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Log de las transacciones realizadas.';

--
-- Volcado de datos para la tabla `logstransacciones`
--

INSERT INTO `logstransacciones` (`idLogTransaccion`, `idUsuario`, `nroOperacion`, `fechaTransaccion`, `sobre`) VALUES
(1, 5, 663485, '2023-11-28 22:06:22', 'DEPOSITO'),
(2, 5, 79894, '2023-11-28 22:17:01', 'RETIRO');

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
  `moneda` varchar(10) NOT NULL,
  `nroOperacion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='La tabla contendrá todos los retiros realizados en el banco.';

--
-- Volcado de datos para la tabla `retiros`
--

INSERT INTO `retiros` (`idRetiro`, `numeroCuenta`, `tipoCuenta`, `importeRetiro`, `fechaExtraccion`, `moneda`, `nroOperacion`) VALUES
(1, 100002, 'CAU$S', 2500, '2023-11-25 00:00:00', 'U$S', 849201),
(2, 100002, 'CAU$S', 500, '2023-11-25 00:00:00', 'U$S', 849312),
(3, 100004, 'CC$', 2000, '2023-11-25 00:00:00', '$', 529432),
(4, 100003, 'CCU$S', 9000, '2023-11-25 00:00:00', 'U$S', 9530213),
(5, 100005, 'CA$', 3000, '2023-11-25 00:00:00', '$', 483912),
(6, 100006, 'CAU$S', 5000, '2023-11-26 00:00:00', 'U$S', 483923),
(7, 100007, 'CCU$S', 4000, '2023-11-27 00:00:00', 'U$S', 934012),
(8, 100003, 'CCU$S', 4000, '2023-11-27 00:00:00', 'U$S', 5453234),
(9, 100003, 'CCU$S', 1000, '2023-11-28 00:00:00', 'U$S', 79894);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `idUsuario` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `rol` varchar(15) NOT NULL,
  `clave` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='La tabla contendra la informacion sobre los usuarios.';

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`idUsuario`, `email`, `rol`, `clave`) VALUES
(1, 'rocibessio@gmail.com', 'Operador', '123rocio'),
(2, 'admin@gmail.com', 'Operador', '123admin'),
(3, 'charlygarcia@yahoo.com.ar', 'Supervisor', '123charly'),
(4, 'josemart@yahoo.com.ar', 'Supervisor', '123josefina'),
(5, 'martinrami@gmail.com', 'Cajero', '123martin'),
(6, 'sabririch@gmail.com', 'Cajero', '123sabrina'),
(7, 'mirbrid@gmail.com', 'Operador', '123miriam'),
(8, 'niki@yahoo.com.ar', 'Cajero', '123nicolas'),
(9, 'esteban@yahoo.com.ar', 'Supervisor', '123esteban'),
(10, 'val@gmail.com', 'Supervisor', '123valentina');

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
-- Indices de la tabla `logsacceso`
--
ALTER TABLE `logsacceso`
  ADD PRIMARY KEY (`idLog`);

--
-- Indices de la tabla `logstransacciones`
--
ALTER TABLE `logstransacciones`
  ADD PRIMARY KEY (`idLogTransaccion`);

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
  MODIFY `idAjuste` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `cuentas`
--
ALTER TABLE `cuentas`
  MODIFY `idCuenta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100009;

--
-- AUTO_INCREMENT de la tabla `depositos`
--
ALTER TABLE `depositos`
  MODIFY `idDeposito` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `logsacceso`
--
ALTER TABLE `logsacceso`
  MODIFY `idLog` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `logstransacciones`
--
ALTER TABLE `logstransacciones`
  MODIFY `idLogTransaccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `retiros`
--
ALTER TABLE `retiros`
  MODIFY `idRetiro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
