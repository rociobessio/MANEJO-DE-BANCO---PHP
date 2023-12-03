-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-12-2023 a las 23:54:17
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
  `ajusteSobre` varchar(15) NOT NULL,
  `nroOperacion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Los ajustes podrán ser correcciones sobre retiros o deposito';

--
-- Volcado de datos para la tabla `ajustes`
--

INSERT INTO `ajustes` (`idAjuste`, `motivoAjuste`, `ajusteMonto`, `numeroBuscado`, `numeroCuenta`, `ajusteSobre`, `nroOperacion`) VALUES
(1, 'Deposito mal facturado.', 1000, 3, 100004, 'Deposito', 810321),
(2, 'Se genero mal el deposito.', 3000, 3, 100004, 'Deposito', 473832),
(3, 'La extraccion fue hecha incorrectamente.', 1250, 5, 100005, 'Retiro', 3194812),
(4, 'Extraccion mal realizada.', 2900, 4, 100006, 'Retiro', 312541),
(5, 'Extraccion mal realizada.', 1000, 3, 100004, 'Retiro', 647321),
(6, 'Deposito mal realizado.', 2000, 8, 100007, 'Deposito', 946382),
(7, 'Deposito mal realizado.', 500, 6, 100006, 'Deposito', 312394),
(8, 'Extraccion mal realizado.', 500, 3, 100004, 'Retiro', 5437654),
(9, 'Extraccion mal realizada.', 10000, 10, 100009, 'Retiro', 1298641),
(10, 'Deposito mal realizado.', 2000, 6, 100006, 'Deposito', 3406323),
(11, 'Deposito mal realizado.', 2000, 14, 100009, 'Deposito', 9489234),
(12, 'Deposito mal realizado.', 5000, 13, 100005, 'Deposito', 892872),
(13, 'Deposito mal ejecutado.', 2000, 17, 100008, 'Deposito', 170533),
(14, 'Deposito mal realizado.', 10000, 16, 100008, 'Deposito', 81831);

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
(100001, 'CA$', '$', 5000, 0, './ImagenesDeCuentas/2023/CA$_2594.jpg', '278193102', 'gaston', 'ramirez', 'LC', 'gaston@gmail.com'),
(100002, 'CAU$S', 'U$S', 18500, 0, './ImagenesDeCuentas/2023/CAU$S_3220.jpg', '45013997', 'mariano', 'martinez', 'DNI', 'mariano@gmail.com'),
(100003, 'CCU$S', 'U$S', 3900, 1, './ImagenesDeCuentas/2023/CCU$S_3574.jpg', '	 3102930123', 'lucas', 'tapiar', 'LI', 'lucas@gmail.com'),
(100004, 'CC$', '$', 35500, 0, './ImagenesDeCuentas/2023/CC$_7205.jpg', '	 44789123', 'renata', 'bustamante', 'DNI', 'renatab@outlook.com.ar'),
(100005, 'CA$', '$', 39250, 1, './ImagenesDeCuentas/2023/CA$_1872.jpg', '	 44789123', 'lucrecia', 'montesinos', 'LC', 'lucrecia@yahoo.com.ar'),
(100006, 'CAU$S', 'U$S', 57500, 1, './ImagenesDeCuentas/2023/CAU$S_8555.jpg', '348291032', 'nicolas', 'perez', 'DNI', 'nicop@outlook.com.ar'),
(100007, 'CCU$S', 'U$S', 98000, 0, './ImagenesDeCuentas/2023/CCU$S_565.jpg', '37182031', 'javier', 'justinano', 'LC', 'javier@gmail.com'),
(100008, 'CCU$S', 'U$S', 183000, 1, './ImagenesDeCuentas/2023/CCU$S_7383.jpg', '37182031', 'rocio', 'bessio', 'DNI', 'rociobessio@gmail.com'),
(100009, 'CAU$S', 'U$S', 27000, 1, './ImagenesDeCuentas/2023/CAU$S_6510.jpg', '3489210', 'miranda', 'sergi', 'DNI', 'miranda@gmail.com'),
(100010, 'CC$', '$', 180000, 1, './ImagenesDeCuentas/2023/CC$_2748.jpg', '3589402', 'Cristina', 'Alvarez', 'LC', 'cristina@yahoo.com.ar');

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
(13, 100005, 'CA$', 10000, '2023-11-28 00:00:00', '$', 663485),
(14, 100009, 'CAU$S', 2000, '2023-12-02 00:00:00', 'U$S', 557472),
(15, 100009, 'CAU$S', 4000, '2023-12-02 00:00:00', 'U$S', 57886),
(16, 100008, 'CCU$S', 90000, '2023-12-03 00:00:00', 'U$S', 20970),
(17, 100008, 'CCU$S', 2000, '2023-12-03 00:00:00', 'U$S', 549915),
(18, 100008, 'CCU$S', 3000, '2023-12-03 00:00:00', 'U$S', 513831);

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
(24, 1, 'Traer Depositos', '2023-11-28 00:00:00'),
(25, 1, 'Traer Depositos', '2023-11-29 00:00:00'),
(26, 9, 'Logueo', '2023-12-02 00:00:00'),
(27, 1, 'Logueo', '2023-12-02 00:00:00'),
(28, 1, 'Traer Cuentas', '2023-12-02 00:00:00'),
(29, 1, 'Traer Cuenta', '2023-12-02 00:00:00'),
(30, 1, 'Traer Depositos', '2023-12-02 00:00:00'),
(31, 9, 'Logueo', '2023-12-02 00:00:00'),
(32, 2, 'Logueo', '2023-12-02 00:00:00'),
(33, 9, 'Traer Usuarios', '2023-12-02 00:00:00'),
(34, 11, 'Logueo', '2023-12-02 00:00:00'),
(35, 11, 'Traer Cuentas', '2023-12-02 00:00:00'),
(36, 11, 'Traer Cuentas', '2023-12-02 00:00:00'),
(37, 11, 'Logueo', '2023-12-02 00:00:00'),
(38, 11, 'Traer Cuenta', '2023-12-02 00:00:00'),
(39, 11, 'Traer Cuenta', '2023-12-02 00:00:00'),
(40, 11, 'Traer Cuenta', '2023-12-02 00:00:00'),
(41, 11, 'Traer Cuentas', '2023-12-02 00:00:00'),
(42, 11, 'Traer Cuenta', '2023-12-02 00:00:00'),
(43, 11, 'Logueo', '2023-12-02 00:00:00'),
(44, 11, 'Consultar Cuenta', '2023-12-02 00:00:00'),
(45, 9, 'Traer Usuarios', '2023-12-02 00:00:00'),
(46, 11, 'Logueo', '2023-12-02 00:00:00'),
(47, 11, 'Traer Usuario', '2023-12-02 00:00:00'),
(48, 11, 'Traer Usuarios', '2023-12-02 00:00:00'),
(49, 5, 'Logueo', '2023-12-02 00:00:00'),
(50, 5, 'Traer Depositos', '2023-12-02 00:00:00'),
(51, 5, 'Traer Deposito', '2023-12-02 00:00:00'),
(52, 11, 'Traer Cuentas', '2023-12-02 00:00:00'),
(53, 11, 'Traer Cuentas', '2023-12-02 00:00:00'),
(54, 5, 'Traer Depositos', '2023-12-02 00:00:00'),
(55, 11, 'Traer Cuentas', '2023-12-02 00:00:00'),
(56, 11, 'Traer Cuentas', '2023-12-02 00:00:00'),
(57, 5, 'Traer Depositos', '2023-12-02 00:00:00'),
(58, 5, 'Traer Deposito', '2023-12-02 00:00:00'),
(59, 11, 'Traer Cuentas', '2023-12-02 00:00:00'),
(60, 9, 'Logueo', '2023-12-02 00:00:00'),
(61, 9, 'Traer Ajustes', '2023-12-02 00:00:00'),
(62, 11, 'Traer Cuentas', '2023-12-02 00:00:00'),
(63, 9, 'Traer Ajustes', '2023-12-02 00:00:00'),
(64, 5, 'Traer Depositos', '2023-12-02 00:00:00'),
(65, 11, 'Traer Cuentas', '2023-12-02 00:00:00'),
(66, 5, 'Traer Depositos', '2023-12-02 00:00:00'),
(67, 11, 'Traer Cuentas', '2023-12-02 00:00:00'),
(68, 11, 'Traer Cuentas', '2023-12-02 00:00:00'),
(69, 5, 'Logueo', '2023-12-02 00:00:00'),
(70, 9, 'Logueo', '2023-12-03 00:00:00'),
(71, 9, 'Traer Ajustes', '2023-12-03 00:00:00'),
(72, 5, 'Logueo', '2023-12-03 00:00:00'),
(73, 5, 'Traer Depositos', '2023-12-03 00:00:00'),
(74, 11, 'Logueo', '2023-12-03 00:00:00'),
(75, 11, 'Traer Cuentas', '2023-12-03 00:00:00'),
(76, 9, 'Logueo', '2023-12-03 00:00:00'),
(77, 11, 'Traer Cuentas', '2023-12-03 00:00:00'),
(78, 11, 'Logueo', '2023-12-03 00:00:00'),
(79, 11, 'Cargar Cuenta', '2023-12-03 00:00:00'),
(80, 5, 'Logueo', '2023-12-03 00:00:00'),
(81, 5, '20970', '2023-12-03 00:00:00'),
(82, 5, '549915', '2023-12-03 00:00:00'),
(83, 5, '513831', '2023-12-03 00:00:00'),
(84, 9, 'Logueo', '2023-12-03 00:00:00'),
(85, 9, '659544', '2023-12-03 00:00:00'),
(86, 9, '170533', '2023-12-03 00:00:00'),
(87, 9, '81831', '2023-12-03 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logstransacciones`
--

CREATE TABLE `logstransacciones` (
  `idLogTransaccion` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `nroOperacion` int(11) NOT NULL,
  `idCuenta` int(11) NOT NULL,
  `fechaTransaccion` datetime NOT NULL,
  `sobre` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Log de las transacciones realizadas.';

--
-- Volcado de datos para la tabla `logstransacciones`
--

INSERT INTO `logstransacciones` (`idLogTransaccion`, `idUsuario`, `nroOperacion`, `idCuenta`, `fechaTransaccion`, `sobre`) VALUES
(1, 5, 663485, 100005, '2023-11-28 22:06:22', 'DEPOSITO'),
(2, 5, 79894, 100003, '2023-11-28 22:17:01', 'RETIRO'),
(3, 5, 557472, 100009, '2023-12-02 14:17:26', 'DEPOSITO'),
(4, 5, 57886, 100009, '2023-12-02 14:21:22', 'DEPOSITO'),
(5, 5, 571990, 100009, '2023-12-02 14:24:11', 'RETIRO'),
(6, 9, 892872, 100005, '2023-12-03 12:59:29', 'AJUSTE'),
(9, 5, 513831, 100008, '2023-12-03 19:48:14', 'DEPOSITO'),
(10, 9, 170533, 100008, '2023-12-03 19:50:40', 'AJUSTE'),
(11, 9, 81831, 100008, '2023-12-03 19:53:06', 'AJUSTE');

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
(9, 100003, 'CCU$S', 1000, '2023-11-28 00:00:00', 'U$S', 79894),
(10, 100009, 'CAU$S', 10000, '2023-12-02 00:00:00', 'U$S', 571990);

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
(10, 'admin@gmail.com', 'Admin', '123admin'),
(11, 'administrador@admin.com', 'Admin', '123admin');

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
  MODIFY `idAjuste` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `cuentas`
--
ALTER TABLE `cuentas`
  MODIFY `idCuenta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100011;

--
-- AUTO_INCREMENT de la tabla `depositos`
--
ALTER TABLE `depositos`
  MODIFY `idDeposito` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `logsacceso`
--
ALTER TABLE `logsacceso`
  MODIFY `idLog` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT de la tabla `logstransacciones`
--
ALTER TABLE `logstransacciones`
  MODIFY `idLogTransaccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `retiros`
--
ALTER TABLE `retiros`
  MODIFY `idRetiro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
