-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-06-2025 a las 00:00:10
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
-- Base de datos: `registro`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividades`
--

CREATE TABLE `actividades` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `accion` varchar(255) NOT NULL,
  `detalle` text DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `actividades`
--

INSERT INTO `actividades` (`id`, `usuario_id`, `accion`, `detalle`, `fecha`) VALUES
(1, 1, 'Login', 'Usuario: admin', '2025-05-21 10:42:05'),
(2, 1, 'Login', 'Usuario: admin', '2025-05-22 09:34:24'),
(3, 1, 'Registrar Bien', 'Código: IACEB-01, Descripción: Escritorio de formica con los gavetas operativas, color marrón, Operativo 					\\r\\n', '2025-05-22 09:54:54'),
(4, 1, 'Transferir Bien', 'Código: IACEB-01, De: Presidencia (Presidencia) a Administración (Administracion)', '2025-05-22 09:56:06'),
(5, 1, 'Transferir Bien', 'Código: IACEB-01, De: Administración (Administracion) a Administración (Administracion)', '2025-05-22 09:56:47'),
(6, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Vista Web', '2025-05-22 09:57:45'),
(7, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2025-05, Vista Web', '2025-05-22 09:59:20'),
(8, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2025-05, Vista Web', '2025-05-22 09:59:28'),
(9, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Administración, Fecha: 2025-05, Vista Web', '2025-05-22 09:59:33'),
(10, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2025-05, Vista Web', '2025-05-22 09:59:54'),
(11, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2025-05, Vista Web', '2025-05-22 10:00:51'),
(12, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Vista Web', '2025-05-22 10:01:14'),
(13, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Vista Web', '2025-05-22 10:01:17'),
(14, 1, 'Login', 'Usuario: admin', '2025-05-22 10:02:41'),
(15, 1, 'Login', 'Usuario: admin', '2025-05-22 10:04:42'),
(16, 1, 'Login', 'Usuario: admin', '2025-05-22 18:35:25'),
(17, 1, 'Login', 'Usuario: admin', '2025-05-22 18:54:15'),
(18, 1, 'Login', 'Usuario: admin', '2025-05-22 19:03:24'),
(19, 1, 'Login', 'Usuario: admin', '2025-05-22 19:04:03'),
(20, 1, 'Login', 'Usuario: admin', '2025-05-22 19:23:35'),
(21, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:32:12'),
(22, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:32:21'),
(23, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:32:31'),
(24, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:32:44'),
(25, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:33:25'),
(26, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:33:39'),
(27, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:33:51'),
(28, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:33:52'),
(29, 1, 'Login', 'Usuario: admin', '2025-05-22 19:38:25'),
(30, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Vista Web', '2025-05-22 19:38:38'),
(31, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:38:46'),
(32, 1, 'Login', 'Usuario: admin', '2025-05-22 19:44:29'),
(33, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:44:38'),
(34, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:44:42'),
(35, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:46:30'),
(36, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:46:31'),
(37, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:46:32'),
(38, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:46:32'),
(39, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:48:32'),
(40, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:48:33'),
(41, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:48:34'),
(42, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:48:41'),
(43, 1, 'Login', 'Usuario: admin', '2025-05-22 19:49:15'),
(44, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:49:22'),
(45, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:49:30'),
(46, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:51:47'),
(47, 1, 'Login', 'Usuario: admin', '2025-05-22 19:53:18'),
(48, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:53:38'),
(49, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:53:48'),
(50, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:54:04'),
(51, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:54:04'),
(52, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:55:35'),
(53, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:55:36'),
(54, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:55:36'),
(55, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:55:37'),
(56, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:55:37'),
(57, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:55:37'),
(58, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:55:37'),
(59, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:55:38'),
(60, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:58:18'),
(61, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:58:18'),
(62, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:58:18'),
(63, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:58:19'),
(64, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:58:19'),
(65, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:58:19'),
(66, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:58:19'),
(67, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:58:19'),
(68, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 19:58:19'),
(69, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:00:01'),
(70, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:01:45'),
(71, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:02:11'),
(72, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:03:57'),
(73, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:04:04'),
(74, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:06:15'),
(75, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:09:01'),
(76, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:10:12'),
(77, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:11:38'),
(78, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:13:13'),
(79, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:13:14'),
(80, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:13:14'),
(81, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:13:14'),
(82, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:13:15'),
(83, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:13:15'),
(84, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:13:16'),
(85, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:13:16'),
(86, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:14:08'),
(87, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:17:10'),
(88, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:20:48'),
(89, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:20:49'),
(90, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:20:49'),
(91, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:20:50'),
(92, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:20:50'),
(93, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:21:32'),
(94, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:23:54'),
(95, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:24:45'),
(96, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:26:52'),
(97, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:26:53'),
(98, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:26:53'),
(99, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:26:53'),
(100, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:26:53'),
(101, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:26:53'),
(102, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:33:15'),
(103, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:36:09'),
(104, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:38:30'),
(105, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:38:30'),
(106, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:38:30'),
(107, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:38:30'),
(108, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:38:31'),
(109, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:38:31'),
(110, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:38:31'),
(111, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:38:31'),
(112, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:38:31'),
(113, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:38:32'),
(114, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:38:32'),
(115, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:38:32'),
(116, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:38:32'),
(117, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:38:33'),
(118, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:38:33'),
(119, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:39:15'),
(120, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:39:42'),
(121, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:40:15'),
(122, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:40:32'),
(123, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:44:47'),
(124, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:47:46'),
(125, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:47:46'),
(126, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:47:47'),
(127, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:47:47'),
(128, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:47:47'),
(129, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:47:48'),
(130, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:47:48'),
(131, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:47:48'),
(132, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:47:49'),
(133, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:47:49'),
(134, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:47:49'),
(135, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:47:50'),
(136, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:47:50'),
(137, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:48:30'),
(138, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:50:31'),
(139, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:50:50'),
(140, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:51:24'),
(141, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:51:40'),
(142, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:53:41'),
(143, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:53:42'),
(144, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:53:43'),
(145, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:53:43'),
(146, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:53:44'),
(147, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:54:19'),
(148, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:55:08'),
(149, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 20:57:53'),
(150, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 21:00:08'),
(151, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 21:06:07'),
(152, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 21:07:27'),
(153, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 21:07:28'),
(154, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 21:07:38'),
(155, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 21:07:53'),
(156, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 21:08:48'),
(157, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 21:08:48'),
(158, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 21:08:51'),
(159, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 21:08:55'),
(160, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 21:09:22'),
(161, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 21:09:39'),
(162, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 21:09:46'),
(163, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 21:09:59'),
(164, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 21:10:31'),
(165, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 21:12:08'),
(166, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 21:12:11'),
(167, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Patrimonio, Fecha: 2024-01, Vista Web', '2025-05-22 21:15:29'),
(168, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-22 21:15:35'),
(169, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, PDF', '2025-05-22 21:15:55'),
(170, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, PDF', '2025-05-22 21:16:17'),
(171, 1, 'Login', 'Usuario: admin', '2025-05-24 19:48:58'),
(172, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 19:51:05'),
(173, 1, 'Login', 'Usuario: admin', '2025-05-24 19:56:49'),
(174, 1, 'Login', 'Usuario: admin', '2025-05-24 20:09:41'),
(175, 1, 'Login', 'Usuario: admin', '2025-05-24 20:12:18'),
(176, 1, 'Login', 'Usuario: admin', '2025-05-24 20:13:48'),
(177, 1, 'Login', 'Usuario: admin', '2025-05-24 20:14:36'),
(178, 1, 'Login', 'Usuario: admin', '2025-05-24 20:16:18'),
(179, 1, 'Login', 'Usuario: admin', '2025-05-24 20:20:21'),
(180, 1, 'Login', 'Usuario: admin', '2025-05-24 20:22:34'),
(181, 1, 'Login', 'Usuario: admin', '2025-05-24 20:23:29'),
(182, 1, 'Login', 'Usuario: admin', '2025-05-24 20:25:12'),
(183, 1, 'Login', 'Usuario: admin', '2025-05-24 20:26:03'),
(184, 1, 'Login', 'Usuario: admin', '2025-05-24 20:28:00'),
(185, 1, 'Login', 'Usuario: admin', '2025-05-24 20:29:01'),
(186, 1, 'Login', 'Usuario: admin', '2025-05-24 20:33:19'),
(187, 1, 'Login', 'Usuario: admin', '2025-05-24 20:41:06'),
(188, 1, 'Login', 'Usuario: admin', '2025-05-24 20:47:41'),
(189, 1, 'Login', 'Usuario: admin', '2025-05-24 20:57:22'),
(190, 1, 'Login', 'Usuario: admin', '2025-05-24 21:00:26'),
(191, 1, 'Login', 'Usuario: admin', '2025-05-24 21:02:09'),
(192, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:05:41'),
(193, 1, 'Login', 'Usuario: admin', '2025-05-24 21:07:05'),
(194, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:07:39'),
(195, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:09:14'),
(196, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:10:50'),
(197, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:10:51'),
(198, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:11:46'),
(199, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:11:46'),
(200, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:11:47'),
(201, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:11:47'),
(202, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:12:07'),
(203, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:12:08'),
(204, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:12:08'),
(205, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:12:26'),
(206, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:12:27'),
(207, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:12:27'),
(208, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:12:36'),
(209, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:12:37'),
(210, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:12:46'),
(211, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:12:46'),
(212, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:12:56'),
(213, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:12:56'),
(214, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:12:59'),
(215, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:13:13'),
(216, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:13:21'),
(217, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:13:22'),
(218, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:13:22'),
(219, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:14:13'),
(220, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:15:12'),
(221, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:16:34'),
(222, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:16:34'),
(223, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:16:34'),
(224, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:16:34'),
(225, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:16:35'),
(226, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:16:35'),
(227, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:16:36'),
(228, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:16:46'),
(229, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:16:47'),
(230, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:16:52'),
(231, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:19:13'),
(232, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:19:14'),
(233, 1, 'Logout', 'Usuario: admin', '2025-05-24 21:29:32'),
(234, 1, 'Login', 'Usuario: admin', '2025-05-24 21:30:15'),
(235, 1, 'Login', 'Usuario: admin', '2025-05-24 21:35:59'),
(236, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-24 21:36:22'),
(237, 1, 'Login', 'Usuario: admin', '2025-05-25 17:02:53'),
(238, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Vista Web', '2025-05-25 17:03:04'),
(239, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, PDF', '2025-05-25 17:03:13'),
(240, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, PDF', '2025-05-25 17:07:16'),
(241, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: , Año: , Vista Web', '2025-05-25 17:12:01'),
(242, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: , Año: , Vista Web', '2025-05-25 17:12:02'),
(243, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: , Año: 2024, Vista Web', '2025-05-25 17:12:04'),
(244, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Año: 2024, Vista Web', '2025-05-25 17:29:00'),
(245, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Año: 2024, PDF', '2025-05-25 17:29:13'),
(246, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Año: 2024, Vista Web', '2025-05-25 17:31:32'),
(247, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Año: 2024, PDF', '2025-05-25 17:31:36'),
(248, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: , Año: , Vista Web', '2025-05-25 17:34:44'),
(249, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: , Año: 2024, Vista Web', '2025-05-25 17:34:47'),
(250, 1, 'Login', 'Usuario: admin', '2025-05-25 17:36:14'),
(251, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: , Año: , Vista Web', '2025-05-25 17:36:25'),
(252, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Año: , Vista Web', '2025-05-25 17:36:27'),
(253, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Administración, Fecha: 2024-01, Año: , PDF', '2025-05-25 17:36:31'),
(254, 1, 'Login', 'Usuario: admin', '2025-05-25 19:21:03'),
(255, 1, 'Editar Usuario', 'ID usuario editado: 1, Nombre: Administrador, Rol: admin', '2025-05-25 19:35:42'),
(256, 1, 'Registrar Bien', 'Código: IACEB-56, Descripción: Silla de visitante color gris, operativa.					\\r\\n', '2025-05-25 19:37:45'),
(257, 1, 'Registrar Bien', 'Código: IACEB-86, Descripción: Silla de visitante color gris', '2025-05-25 19:44:42'),
(258, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: , Año: , Vista Web', '2025-05-25 19:45:04'),
(259, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-05, Año: , Vista Web', '2025-05-25 19:45:09'),
(260, 1, 'Registrar Bien', 'Código: IACEB-20400, Descripción: Gavetero de madera, color marrón con 3 gavetas operativas y dos puertas con dos divisiones.', '2025-05-25 19:46:50'),
(261, 1, 'Registrar Bien', 'Código: IACEB-55661, Descripción: Amplificador de sonido, Marca: SONY, Modelo:STR-K870P, Serial: 9507941, color plateado.', '2025-05-25 19:48:35'),
(262, 1, 'Registrar Bien', 'Código: IACEB-1115, Descripción: Amplificador de sonido, Marca: SONY, Modelo:STR-K870P, Serial: 9507941, color plateado.', '2025-05-25 19:50:03'),
(263, 1, 'Registrar Bien', 'Código: IACEB-149, Descripción: Moto SKYGO, Modelo SG150,PLACA AE606A, Color GRIS, AÑO 2010', '2025-05-25 19:51:48'),
(264, 1, 'Registrar Bien', 'Código: IACEB-96, Descripción: Cafetera HAMILTON BEACH, Color Negra, sin serial.', '2025-05-25 19:52:51'),
(265, 1, 'Transferir Bien', 'Código: IACEB-01, De: Administración (Administracion) a Presidencia (Presidencia)', '2025-05-25 20:02:59'),
(266, 1, 'Transferir Bien', 'Código: IACEB-01, De: Presidencia (Presidencia) a Presidencia (Presidencia)', '2025-05-25 20:03:19'),
(267, 1, 'Transferir Bien', 'Código: IACEB-01, De: Presidencia (Presidencia) a Patrimonio (Patrimonio)', '2025-05-25 20:15:11'),
(268, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: , Año: , Vista Web', '2025-05-25 20:23:44'),
(269, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: , Año: 0, Vista Web', '2025-05-25 20:23:49'),
(270, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: , Año: 2024, Vista Web', '2025-05-25 20:23:54'),
(271, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: , Año: 0, Vista Web', '2025-05-25 20:24:39'),
(272, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 0000-00, Año: 0, Vista Web', '2025-05-25 20:24:46'),
(273, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: , Año: , Vista Web', '2025-05-25 20:27:26'),
(274, 1, 'Editar Bien', 'ID: 7', '2025-05-25 20:44:46'),
(275, 1, 'Editar Bien', 'ID: 2', '2025-05-25 20:45:00'),
(276, 1, 'Login', 'Usuario: admin', '2025-05-25 20:45:39'),
(277, 1, 'Editar Bien', 'ID: 2', '2025-05-25 20:46:55'),
(278, 1, 'Login', 'Usuario: admin', '2025-05-25 20:48:30'),
(279, 1, 'Login', 'Usuario: admin', '2025-05-25 20:53:59'),
(280, 1, 'Login', 'Usuario: admin', '2025-05-25 21:01:30'),
(281, 1, 'Login', 'Usuario: admin', '2025-05-25 21:10:47'),
(282, 1, 'Editar Bien', 'ID: 4', '2025-05-25 21:12:03'),
(283, 1, 'Editar Bien', 'ID: 4', '2025-05-25 21:12:16'),
(284, 1, 'Login', 'Usuario: admin', '2025-05-25 21:14:29'),
(285, 1, 'Login', 'Usuario: admin', '2025-05-25 21:20:37'),
(286, 1, 'Login', 'Usuario: admin', '2025-05-25 21:21:02'),
(287, 1, 'Login', 'Usuario: admin', '2025-05-25 21:23:31'),
(288, 1, 'Login', 'Usuario: admin', '2025-05-25 21:33:47'),
(289, 1, 'Reportar Faltante', 'Bien ID: 8, Responsable: Presidencia', '2025-05-25 21:41:11'),
(290, 1, 'Login', 'Usuario: admin', '2025-05-25 21:53:30'),
(291, 1, 'Login', 'Usuario: admin', '2025-05-25 21:55:37'),
(292, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: , Año: , Vista Web', '2025-05-25 21:58:29'),
(293, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 0000-00, Año: 0, Vista Web', '2025-05-25 21:58:34'),
(294, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 0000-00, Año: 0, Vista Web', '2025-05-25 21:59:12'),
(295, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 0000-00, Año: 0, Vista Web', '2025-05-25 21:59:17'),
(296, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-25 21:59:24'),
(297, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-05, Año: 0, Vista Web', '2025-05-25 21:59:26'),
(298, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-05, Año: 2024, Vista Web', '2025-05-25 21:59:29'),
(299, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-05, Año: 2024, Vista Web', '2025-05-25 21:59:31'),
(300, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: 2024-05, Año: 2024, Vista Web', '2025-05-25 21:59:35'),
(301, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Administración, Fecha: , Año: , Vista Web', '2025-05-25 21:59:38'),
(302, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: , Año: , Vista Web', '2025-05-25 21:59:42'),
(303, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-05, Año: 0, Vista Web', '2025-05-25 21:59:44'),
(304, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-05, Año: 2024, Vista Web', '2025-05-25 21:59:47'),
(305, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: , Año: , Vista Web', '2025-05-25 21:59:50'),
(306, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-25 21:59:52'),
(307, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-25 21:59:59'),
(308, 1, 'Login', 'Usuario: admin', '2025-05-25 22:00:51'),
(309, 1, 'Reincorporar Bien', 'ID: 3', '2025-05-25 22:00:57'),
(310, 1, 'Login', 'Usuario: admin', '2025-05-25 22:05:49'),
(311, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Patrimonio, Fecha: , Año: , Vista Web', '2025-05-25 22:05:54'),
(312, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Patrimonio, Fecha: 2024-01, Año: , Vista Web', '2025-05-25 22:05:58'),
(313, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-25 22:06:06'),
(314, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-25 22:06:13'),
(315, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-25 22:06:23'),
(316, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 0000-00, Año: 0, Vista Web', '2025-05-25 22:07:20'),
(317, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 0000-00, Año: 0, Vista Web', '2025-05-25 22:07:25'),
(318, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 0000-00, Año: 0, Vista Web', '2025-05-25 22:07:28'),
(319, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 0000-00, Año: 0, Vista Web', '2025-05-25 22:07:28'),
(320, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 0000-00, Año: 0, Vista Web', '2025-05-25 22:07:28'),
(321, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-25 22:07:41'),
(322, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-25 22:07:43'),
(323, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-25 22:07:43'),
(324, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-25 22:07:45'),
(325, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-25 22:07:45'),
(326, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-25 22:07:46'),
(327, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-25 22:07:49'),
(328, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-25 22:07:49'),
(329, 1, 'Login', 'Usuario: admin', '2025-05-25 22:08:04'),
(330, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: , Año: , Vista Web', '2025-05-25 22:08:14'),
(331, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-25 22:08:16'),
(332, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-25 22:08:26'),
(333, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: , Año: 0, Vista Web', '2025-05-25 22:08:30'),
(334, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-05, Año: , Vista Web', '2025-05-25 22:08:34'),
(335, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-25 22:09:03'),
(336, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-25 22:10:34'),
(337, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-25 22:10:37'),
(338, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-25 22:10:44'),
(339, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 2024, Vista Web', '2025-05-25 22:10:51'),
(340, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 2024, Vista Web', '2025-05-25 22:12:42'),
(341, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 2024, Vista Web', '2025-05-25 22:16:11'),
(342, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 2024, Vista Web', '2025-05-25 22:16:11'),
(343, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 2024, Vista Web', '2025-05-25 22:16:12'),
(344, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 2024, Vista Web', '2025-05-25 22:16:12'),
(345, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 2024, Vista Web', '2025-05-25 22:16:15'),
(346, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: 2024, Vista Web', '2025-05-25 22:16:27'),
(347, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: 2024, Vista Web', '2025-05-25 22:16:28'),
(348, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: 2024, Vista Web', '2025-05-25 22:16:34'),
(349, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: 2024, Vista Web', '2025-05-25 22:16:41'),
(350, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: , Año: , Vista Web', '2025-05-25 22:16:45'),
(351, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-25 22:16:55'),
(352, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-25 22:18:17'),
(353, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-05, Año: , Vista Web', '2025-05-25 22:18:22'),
(354, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: , Año: , Vista Web', '2025-05-25 22:18:39'),
(355, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-25 22:18:42'),
(356, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-05, Año: , Vista Web', '2025-05-25 22:18:56'),
(357, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-05, Año: , Vista Web', '2025-05-25 22:19:22'),
(358, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-05, Año: 0, Vista Web', '2025-05-25 22:19:24'),
(359, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-25 22:19:50'),
(360, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-25 22:21:36'),
(361, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-25 22:21:37'),
(362, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-25 22:21:37'),
(363, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-05, Año: , Vista Web', '2025-05-25 22:21:55'),
(364, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-05, Año: , Vista Web', '2025-05-25 22:24:03'),
(365, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-05, Año: , Vista Web', '2025-05-25 22:24:04'),
(366, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-05, Año: , Vista Web', '2025-05-25 22:24:04'),
(367, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-05, Año: , Vista Web', '2025-05-25 22:24:05'),
(368, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-05, Año: , Vista Web', '2025-05-25 22:25:25'),
(369, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-05, Año: , Vista Web', '2025-05-25 22:25:29'),
(370, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-05, Año: 0, Vista Web', '2025-05-25 22:25:32'),
(371, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: , Año: 0, Vista Web', '2025-05-25 22:25:35'),
(372, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: , Año: , Vista Web', '2025-05-25 22:27:40'),
(373, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 0000-00, Año: 0, Vista Web', '2025-05-25 22:27:45'),
(374, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 0000-00, Año: 0, Vista Web', '2025-05-25 22:28:32'),
(375, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 0000-00, Año: , Vista Web', '2025-05-25 22:33:18'),
(376, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 2024, Vista Web', '2025-05-25 22:33:33'),
(377, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: 2024, Vista Web', '2025-05-25 22:33:42'),
(378, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: 2024, PDF', '2025-05-25 22:33:54'),
(379, 1, 'Login', 'Usuario: admin', '2025-05-26 19:05:24'),
(380, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: , Año: , Vista Web', '2025-05-26 19:05:30'),
(381, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 19:05:33'),
(382, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 19:05:39'),
(383, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 19:05:43'),
(384, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: , Año: 0, Vista Web', '2025-05-26 19:05:46'),
(385, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:05:51'),
(386, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:08:53'),
(387, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:08:55'),
(388, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:08:57'),
(389, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:09:11'),
(390, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:13:16'),
(391, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 19:13:44'),
(392, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 19:13:53'),
(393, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: , Año: 0, Vista Web', '2025-05-26 19:14:00'),
(394, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:14:06'),
(395, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:14:26'),
(396, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:15:38'),
(397, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:15:39'),
(398, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:16:02'),
(399, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:17:13'),
(400, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:19:37'),
(401, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:21:25'),
(402, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:23:12'),
(403, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:23:13'),
(404, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:23:22'),
(405, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:24:58'),
(406, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:27:36'),
(407, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 19:29:02'),
(408, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 19:29:17'),
(409, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 19:29:52'),
(410, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 19:30:01'),
(411, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 19:35:24'),
(412, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 19:35:36'),
(413, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 19:36:55'),
(414, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 19:37:57'),
(415, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 19:38:32'),
(416, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 19:38:34'),
(417, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 19:38:50'),
(418, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 19:39:28'),
(419, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 19:39:45'),
(420, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 19:43:33'),
(421, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 19:45:55'),
(422, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 19:46:31'),
(423, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 19:48:10'),
(424, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: , Año: , Vista Web', '2025-05-26 19:48:15'),
(425, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: , Año: , Vista Web', '2025-05-26 19:48:16'),
(426, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 19:48:18'),
(427, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 19:48:24'),
(428, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 19:48:34'),
(429, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 19:48:44'),
(430, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: , Año: 0, Vista Web', '2025-05-26 19:48:48'),
(431, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:48:52'),
(432, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:50:10'),
(433, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:50:36'),
(434, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:51:00'),
(435, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:51:51'),
(436, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:52:15'),
(437, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:52:54'),
(438, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:53:08'),
(439, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:53:43'),
(440, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:53:52'),
(441, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:54:03'),
(442, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:54:45'),
(443, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:54:59'),
(444, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:55:01'),
(445, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:55:21'),
(446, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:55:36'),
(447, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:55:38'),
(448, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:55:39'),
(449, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:55:39'),
(450, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:56:23'),
(451, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:56:38'),
(452, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:56:51'),
(453, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 19:57:49'),
(454, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 0000-00, Año: 0, Vista Web', '2025-05-26 19:58:01'),
(455, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 0000-00, Año: 0, Vista Web', '2025-05-26 19:58:33'),
(456, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 0000-00, Año: 0, Vista Web', '2025-05-26 19:58:35'),
(457, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 0000-00, Año: 0, Vista Web', '2025-05-26 19:58:43'),
(458, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 0000-00, Año: 0, Vista Web', '2025-05-26 19:58:56'),
(459, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 0000-00, Año: 0, Vista Web', '2025-05-26 19:59:59'),
(460, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 0000-00, Año: 0, Vista Web', '2025-05-26 20:00:30'),
(461, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 0000-00, Año: 0, Vista Web', '2025-05-26 20:01:02'),
(462, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 0000-00, Año: 0, Vista Web', '2025-05-26 20:01:20'),
(463, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 0000-00, Año: 0, Vista Web', '2025-05-26 20:01:50'),
(464, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 0000-00, Año: 0, Vista Web', '2025-05-26 20:01:58'),
(465, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: , Año: , Vista Web', '2025-05-26 20:02:13'),
(466, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 20:02:15'),
(467, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 20:03:04'),
(468, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 20:03:24'),
(469, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 20:04:50'),
(470, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 20:05:20'),
(471, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 20:05:46'),
(472, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 20:06:24'),
(473, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 20:06:27'),
(474, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 20:06:27'),
(475, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 20:06:49'),
(476, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 20:07:30'),
(477, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: , Año: 0, Vista Web', '2025-05-26 20:07:46'),
(478, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 20:07:50'),
(479, 1, 'Generar Informe', 'Tipo: BM-3, Unidad: Presidencia, Fecha: 2024-01, Año: 0, Vista Web', '2025-05-26 20:08:30'),
(480, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: , Año: 0, Vista Web', '2025-05-26 20:08:35'),
(481, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 20:08:40'),
(482, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 20:09:45'),
(483, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 20:10:45'),
(484, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 20:11:06'),
(485, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 20:11:20'),
(486, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 20:11:47'),
(487, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 20:12:03'),
(488, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 20:12:34'),
(489, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 20:12:35'),
(490, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 20:12:35'),
(491, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 20:12:36'),
(492, 1, 'Generar Informe', 'Tipo: BM-2, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 20:12:36'),
(493, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 20:12:38'),
(494, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 20:12:38'),
(495, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 20:12:39'),
(496, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 20:12:39'),
(497, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 20:13:07'),
(498, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 20:13:21'),
(499, 1, 'Generar Informe', 'Tipo: BM-4, Unidad: Presidencia, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 20:13:52'),
(500, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Patrimonio, Fecha: , Año: , Vista Web', '2025-05-26 20:51:35'),
(501, 1, 'Generar Informe', 'Tipo: BM-1, Unidad: Patrimonio, Fecha: 2024-01, Año: , Vista Web', '2025-05-26 20:51:39'),
(502, 1, 'Transferir Bien', 'Código: IACEB-01, De: Patrimonio (Patrimonio) a Presidencia (Presidencia)', '2025-05-26 20:52:21'),
(503, 1, 'Login', 'Usuario: admin', '2025-05-26 20:53:56'),
(504, 1, 'Transferir Bien', 'Código: IACEB-01, De: Presidencia (Presidencia) a Patrimonio (Patrimonio)', '2025-05-26 20:54:27'),
(505, 1, 'Login', 'Usuario: admin', '2025-05-26 20:55:15'),
(506, 1, 'Transferir Bien', 'Código: IACEB-01, De: Patrimonio (Patrimonio) a Presidencia (Presidencia)', '2025-05-26 20:55:31'),
(507, 1, 'Login', 'Usuario: admin', '2025-05-26 20:56:23'),
(508, 1, 'Transferir Bien', 'Código: IACEB-01, De: Presidencia (Presidencia) a Patrimonio (Patrimonio)', '2025-05-26 20:58:41'),
(509, 1, 'Transferir Bien', 'Código: IACEB-01, De: Patrimonio (Patrimonio) a Presidencia (Presidencia)', '2025-05-26 21:06:44'),
(510, 1, 'Transferir Bien', 'Código: IACEB-01, De: Presidencia (Presidencia) a Patrimonio (Patrimonio)', '2025-05-26 21:12:03'),
(511, 1, 'Transferir Bien', 'Código: IACEB-01, De: Patrimonio (Patrimonio) a Patrimonio (Patrimonio)', '2025-05-26 21:16:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bienes_publicos`
--

CREATE TABLE `bienes_publicos` (
  `id` int(11) NOT NULL,
  `codigo_unico` varchar(50) NOT NULL,
  `tipo_bien` enum('Mueble','Inmueble') NOT NULL,
  `subcategoria` varchar(100) DEFAULT NULL,
  `descripcion` text NOT NULL,
  `fecha_adquisicion` date NOT NULL,
  `precio_adquisicion` decimal(12,2) DEFAULT NULL,
  `estado_conservacion` enum('Optimo','Regular','Deteriorado') NOT NULL,
  `ubicacion` varchar(100) DEFAULT NULL,
  `responsable_patrimonial` varchar(255) NOT NULL,
  `notas` text DEFAULT NULL,
  `documento_soporte` varchar(255) DEFAULT NULL,
  `cantidad` int(11) DEFAULT 1,
  `estado` varchar(20) DEFAULT 'Incorporado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `bienes_publicos`
--

-- Elimina de los INSERT los campos que no existen en la estructura actual de la tabla (por ejemplo, campos como fechas adicionales, valor_mercado, etc.)
INSERT INTO `bienes_publicos` (`id`, `codigo_unico`, `tipo_bien`, `subcategoria`, `descripcion`, `fecha_adquisicion`, `precio_adquisicion`, `estado_conservacion`, `ubicacion`, `responsable_patrimonial`, `notas`, `documento_soporte`, `cantidad`, `estado`) VALUES
(1, 'IACEB-01', 'Mueble', '2-01: Máquinas y equipos de oficina', 'Escritorio de formica con los gavetas operativas, color marrón, Operativo', '2024-01-15', 50.00, 'Optimo', 'Patrimonio', 'Patrimonio', NULL, 'documentos/IACEB-01_1747922094.pdf', 1, 'Incorporado'),
(2, 'IACEB-56', 'Mueble', '2-01: Máquinas y equipos de oficina', 'Silla de visitante color gris, operativa.', '2024-01-08', 20.00, 'Optimo', 'Presidencia', 'Presidencia', NULL, '', 1, 'Incorporado'),
(3, 'IACEB-86', 'Mueble', '2-01: Máquinas y equipos de oficina', 'Silla de visitante color gris', '2024-05-11', 20.00, 'Optimo', 'Presidencia', 'Presidencia', NULL, '', 1, 'Incorporado'),
(4, 'IACEB-20400', 'Mueble', '2-01: Máquinas y equipos de oficina', 'Gavetero de madera, color marrón con 3 gavetas operativas y dos puertas con dos divisiones.', '2024-01-09', 60.00, 'Optimo', 'Presidencia', 'Presidencia', NULL, '', 1, 'Incorporado'),
(5, 'IACEB-55661', 'Mueble', '2-01: Máquinas y equipos de oficina', 'Amplificador de sonido, Marca: SONY, Modelo:STR-K870P, Serial: 9507941, color plateado.', '2024-01-25', 100.00, 'Optimo', 'Presidencia', 'Presidencia', NULL, '', 1, 'Incorporado'),
(6, 'IACEB-1115', 'Mueble', '2-01: Máquinas y equipos de oficina', 'Amplificador de sonido, Marca: SONY, Modelo:STR-K870P, Serial: 9507941, color plateado.', '2024-01-26', 90.00, 'Optimo', 'Presidencia', 'Presidencia', NULL, '', 1, 'Incorporado'),
(7, 'IACEB-149', 'Mueble', '2-04: Equipos de transporte', 'Moto SKYGO, Modelo SG150,PLACA AE606A, Color GRIS, AÑO 2010', '2024-01-31', 8.90, 'Optimo', 'Presidencia', 'Presidencia', NULL, '', 1, 'Incorporado'),
(8, 'IACEB-96', 'Mueble', '2-01: Máquinas y equipos de oficina', 'Cafetera HAMILTON BEACH, Color Negra, sin serial.', '0000-00-00', 3.90, 'Optimo', 'Presidencia', 'Presidencia', NULL, '', 1, 'En investigación');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `faltantes`
--

CREATE TABLE `faltantes` (
  `id` int(11) NOT NULL,
  `bien_id` int(11) NOT NULL,
  `fecha_reporte` date NOT NULL,
  `cantidad_faltante` int(11) NOT NULL,
  `causa_probable` text DEFAULT NULL,
  `investigacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `faltantes`
--

INSERT INTO `faltantes` (`id`, `bien_id`, `fecha_reporte`, `cantidad_faltante`, `causa_probable`, `investigacion`) VALUES
(1, 8, '2025-05-25', 1, 'Prueba de reportar bienes faltantes', 'Presidencia');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos`
--

CREATE TABLE `movimientos` (
  `id` int(11) NOT NULL,
  `bien_id` int(11) NOT NULL,
  `tipo_movimiento` enum('Incorporación','Desincorporación','Transferencia','Reincorporación','Reporte') NOT NULL,
  `fecha` date NOT NULL,
  `cantidad` int(11) NOT NULL,
  `documento_soporte` varchar(255) DEFAULT NULL,
  `responsable` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `movimientos`
--

-- Elimina de los INSERT los campos que no existen en la estructura actual de la tabla (por ejemplo, detalle)
INSERT INTO `movimientos` (`id`, `bien_id`, `tipo_movimiento`, `fecha`, `cantidad`, `documento_soporte`, `responsable`) VALUES
(1, 1, 'Incorporación', '2025-05-22', 1, 'documentos/IACEB-01_1747922094.pdf', 'Presidencia'),
(6, 2, 'Incorporación', '2025-05-25', 1, '', 'Presidencia'),
(7, 3, 'Incorporación', '2025-05-25', 1, '', 'Presidencia'),
(8, 4, 'Incorporación', '2025-05-25', 1, '', 'Presidencia'),
(9, 5, 'Incorporación', '2025-05-25', 1, '', 'Presidencia'),
(10, 6, 'Incorporación', '2025-05-25', 1, '', 'Presidencia'),
(11, 7, 'Incorporación', '2025-05-25', 1, '', 'Presidencia'),
(12, 8, 'Incorporación', '2025-05-25', 1, '', 'Presidencia'),
(17, 1, 'Transferencia', '2025-05-25', 1, NULL, 'Presidencia'),
(18, 1, 'Transferencia', '2025-05-25', 1, NULL, 'Patrimonio'),
(20, 1, 'Transferencia', '2025-05-26', 1, NULL, 'Patrimonio'),
(21, 1, 'Transferencia', '2025-05-26', 1, NULL, 'Presidencia'),
(22, 1, 'Transferencia', '2025-05-26', 1, NULL, 'Presidencia'),
(23, 1, 'Transferencia', '2025-05-26', 1, NULL, 'Patrimonio'),
(24, 1, 'Transferencia', '2025-05-26', 1, NULL, 'Patrimonio'),
(25, 1, 'Transferencia', '2025-05-26', 1, NULL, 'Presidencia'),
(26, 1, 'Transferencia', '2025-05-26', 1, NULL, 'Presidencia'),
(27, 1, 'Transferencia', '2025-05-26', 1, NULL, 'Patrimonio'),
(28, 1, 'Transferencia', '2025-05-26', 1, NULL, 'Patrimonio'),
(29, 1, 'Transferencia', '2025-05-26', 1, NULL, 'Presidencia'),
(30, 1, 'Transferencia', '2025-05-26', 1, NULL, 'Presidencia'),
(31, 1, 'Transferencia', '2025-05-26', 1, NULL, 'Patrimonio'),
(32, 1, 'Transferencia', '2025-05-26', 1, NULL, 'Patrimonio'),
(33, 1, 'Transferencia', '2025-05-26', 1, NULL, 'Patrimonio');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `rol` enum('admin','usuario') NOT NULL DEFAULT 'usuario',
  `clave_accion` varchar(255) NOT NULL,
  `cargo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `clave`, `nombre`, `rol`, `clave_accion`, `cargo`) VALUES
(1, 'admin', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'Administrador', 'admin', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 'Administrador del Sistema');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_actividades_usuario_id_uniq` (`usuario_id`);

--
-- Indices de la tabla `bienes_publicos`
--
ALTER TABLE `bienes_publicos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_unico` (`codigo_unico`);

--
-- Indices de la tabla `faltantes`
--
ALTER TABLE `faltantes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bien_id` (`bien_id`);

--
-- Indices de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bien_id` (`bien_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividades`
--
ALTER TABLE `actividades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=522;

--
-- AUTO_INCREMENT de la tabla `bienes_publicos`
--
ALTER TABLE `bienes_publicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `faltantes`
--
ALTER TABLE `faltantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD CONSTRAINT `fk_actividades_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_actividades_usuario_id_uniq` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `faltantes`
--
ALTER TABLE `faltantes`
  ADD CONSTRAINT `faltantes_ibfk_1` FOREIGN KEY (`bien_id`) REFERENCES `bienes_publicos` (`id`);

--
-- Filtros para la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD CONSTRAINT `movimientos_ibfk_1` FOREIGN KEY (`bien_id`) REFERENCES `bienes_publicos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
