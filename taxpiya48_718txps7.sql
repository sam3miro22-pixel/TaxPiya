-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 27-05-2026 a las 15:54:01
-- Versión del servidor: 10.6.18-MariaDB
-- Versión de PHP: 8.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `taxpiya48_718txps7`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaciones`
--

CREATE TABLE `asignaciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `viaje_id` bigint(20) UNSIGNED NOT NULL,
  `conductor_id` bigint(20) UNSIGNED NOT NULL,
  `estado` enum('pendiente','aceptado','rechazado','expirado','cancelado_sistema','saltado') NOT NULL DEFAULT 'pendiente',
  `ofertado_at` datetime NOT NULL DEFAULT current_timestamp(),
  `expira_at` datetime NOT NULL,
  `respondido_at` datetime DEFAULT NULL,
  `motivo_rechazo` varchar(120) DEFAULT NULL,
  `distancia_m` int(10) UNSIGNED DEFAULT NULL,
  `eta_min` decimal(5,2) DEFAULT NULL,
  `radio_usado_m` int(10) UNSIGNED DEFAULT NULL,
  `metodo` enum('auto','manual') NOT NULL DEFAULT 'auto',
  `intento` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `prioridad` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_eventos`
--

CREATE TABLE `auditoria_eventos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `actor_user_id` int(25) DEFAULT NULL,
  `actor_rol` enum('admin','pasajero','conductor','sistema') NOT NULL DEFAULT 'sistema',
  `origen` enum('api','panel','app_pasajero','app_conductor','sistema','job') NOT NULL DEFAULT 'sistema',
  `accion` enum('create','update','delete','state_change','assign','login','logout','ajuste','notificacion','sos') NOT NULL,
  `tabla_objetivo` varchar(80) NOT NULL,
  `registro_pk` varchar(64) NOT NULL,
  `detalles` varchar(255) DEFAULT NULL,
  `viaje_id` bigint(20) UNSIGNED DEFAULT NULL,
  `conductor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `before_json` longtext DEFAULT NULL,
  `after_json` longtext DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `request_id` varchar(64) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones`
--

CREATE TABLE `calificaciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `viaje_id` bigint(20) UNSIGNED NOT NULL,
  `rater_id` int(25) NOT NULL,
  `rater_rol` enum('pasajero','conductor','admin') NOT NULL DEFAULT 'pasajero',
  `ratee_id` int(25) NOT NULL,
  `ratee_rol` enum('pasajero','conductor') NOT NULL,
  `puntuacion` tinyint(3) UNSIGNED NOT NULL,
  `comentario` varchar(500) DEFAULT NULL,
  `etiquetas_json` text DEFAULT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `moderado` tinyint(1) NOT NULL DEFAULT 0,
  `moderado_motivo` varchar(255) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ;

--
-- Volcado de datos para la tabla `calificaciones`
--

INSERT INTO `calificaciones` (`id`, `viaje_id`, `rater_id`, `rater_rol`, `ratee_id`, `ratee_rol`, `puntuacion`, `comentario`, `etiquetas_json`, `visible`, `moderado`, `moderado_motivo`, `ip`, `created_at`) VALUES
(15, 327, 52, 'pasajero', 46, 'conductor', 5, NULL, NULL, 1, 0, NULL, '181.174.230.42', '2026-04-20 17:20:57'),
(16, 362, 54, 'pasajero', 47, 'conductor', 5, NULL, NULL, 1, 0, NULL, '191.156.224.236', '2026-05-04 01:34:10'),
(17, 363, 54, 'pasajero', 47, 'conductor', 5, NULL, NULL, 1, 0, NULL, '191.156.224.236', '2026-05-04 01:37:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chat_mensajes`
--

CREATE TABLE `chat_mensajes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `viaje_id` bigint(20) UNSIGNED NOT NULL,
  `remitente_id` int(25) NOT NULL,
  `remitente_rol` enum('pasajero','conductor','admin','sistema') NOT NULL DEFAULT 'pasajero',
  `tipo` enum('text','system','quick','image','file','location') NOT NULL DEFAULT 'text',
  `mensaje` text DEFAULT NULL,
  `media_url` varchar(255) DEFAULT NULL,
  `media_tipo` varchar(60) DEFAULT NULL,
  `reply_to_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lat` decimal(9,6) DEFAULT NULL,
  `lng` decimal(9,6) DEFAULT NULL,
  `leido_por_pasajero_at` datetime DEFAULT NULL,
  `leido_por_conductor_at` datetime DEFAULT NULL,
  `moderado` tinyint(1) NOT NULL DEFAULT 0,
  `moderado_motivo` varchar(255) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `chat_mensajes`
--

INSERT INTO `chat_mensajes` (`id`, `viaje_id`, `remitente_id`, `remitente_rol`, `tipo`, `mensaje`, `media_url`, `media_tipo`, `reply_to_id`, `lat`, `lng`, `leido_por_pasajero_at`, `leido_por_conductor_at`, `moderado`, `moderado_motivo`, `ip`, `created_at`) VALUES
(1, 269, 45, 'pasajero', 'text', 'Ole se demora', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-16 18:37:45', 0, NULL, '181.174.230.42', '2026-04-16 18:36:45'),
(2, 269, 45, 'pasajero', 'text', 'O de uva', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-16 18:37:45', 0, NULL, '181.174.230.42', '2026-04-16 18:37:13'),
(3, 269, 46, 'conductor', 'text', 'Estoy Serca', NULL, NULL, NULL, NULL, NULL, '2026-04-16 18:37:20', NULL, 0, NULL, '181.174.230.42', '2026-04-16 18:37:18'),
(4, 276, 45, 'pasajero', 'text', 'Se demora', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-18 01:21:36', 0, NULL, '181.174.230.42', '2026-04-18 01:21:04'),
(5, 276, 47, 'conductor', 'text', 'Hola guapo', NULL, NULL, NULL, NULL, NULL, '2026-04-18 01:21:13', NULL, 0, NULL, '181.174.230.42', '2026-04-18 01:21:12'),
(6, 276, 45, 'pasajero', 'text', 'Estoy de afán amigó', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-18 01:21:36', 0, NULL, '181.174.230.42', '2026-04-18 01:21:15'),
(7, 290, 45, 'pasajero', 'text', 'Ole babilla 😂😂🤣🤣🤣🤣', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '181.174.230.42', '2026-04-18 01:43:20'),
(8, 309, 45, 'pasajero', 'text', 'Valla y duerma. Conchudo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '181.174.230.42', '2026-04-18 02:52:19'),
(9, 319, 46, 'conductor', 'text', 'Holis', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '181.174.230.42', '2026-04-19 03:01:52'),
(10, 316, 46, 'conductor', 'text', 'Hola edentioquese', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '181.174.230.42', '2026-04-19 03:06:05'),
(11, 314, 44, 'conductor', 'text', 'Hola donde están', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '186.102.83.20', '2026-04-20 22:47:08'),
(12, 353, 52, 'pasajero', 'text', 'Hola buenas', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-03 21:30:23', 0, NULL, '186.102.101.247', '2026-05-03 21:28:18'),
(13, 353, 46, 'conductor', 'text', 'Dónde estás', NULL, NULL, NULL, NULL, NULL, '2026-05-03 21:28:51', NULL, 0, NULL, '186.102.101.247', '2026-05-03 21:28:48'),
(14, 361, 54, 'pasajero', 'text', 'Ya salgo', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 01:26:04', 0, NULL, '191.156.224.236', '2026-05-04 01:24:52'),
(15, 361, 47, 'conductor', 'text', 'Bueno', NULL, NULL, NULL, NULL, NULL, '2026-05-04 01:25:49', NULL, 0, NULL, '191.156.224.236', '2026-05-04 01:25:38'),
(16, 362, 54, 'pasajero', 'text', 'Ya salgo envío', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 01:29:36', 0, NULL, '191.156.224.236', '2026-05-04 01:29:07'),
(17, 377, 52, 'pasajero', 'text', 'Hola buenos días', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-24 12:49:38', 0, NULL, '181.174.230.42', '2026-05-24 12:47:12'),
(18, 377, 46, 'conductor', 'text', 'Hola', NULL, NULL, NULL, NULL, NULL, '2026-05-24 12:49:04', NULL, 0, NULL, '181.174.230.42', '2026-05-24 12:49:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conductores`
--

CREATE TABLE `conductores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(25) NOT NULL,
  `estado_operitivo` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=activo,0=inactivo,2=suspendido',
  `disponible` tinyint(1) NOT NULL DEFAULT 0,
  `last_online_at` datetime DEFAULT NULL,
  `rating_promedio` decimal(3,2) DEFAULT NULL,
  `total_viajes` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `licencia_numero` varchar(64) DEFAULT NULL,
  `licencia_categoria` varchar(32) DEFAULT NULL,
  `licencia_expira` date DEFAULT NULL,
  `soat_numero` varchar(64) DEFAULT NULL,
  `soat_expira` date DEFAULT NULL,
  `seguro_numero` varchar(64) DEFAULT NULL,
  `verificacion_estado` enum('pendiente','verificado','rechazado') NOT NULL DEFAULT 'pendiente',
  `verificacion_nivel` tinyint(3) UNSIGNED DEFAULT 0 COMMENT '0=básico,1=docs,2=domicilio,3=full',
  `verificacion_notas` text DEFAULT NULL,
  `contacto_emergencia_nombre` varchar(120) DEFAULT NULL,
  `contacto_emergencia_telefono` varchar(50) DEFAULT NULL,
  `location_permission` enum('never','while_in_use','always') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `conductores`
--

INSERT INTO `conductores` (`id`, `user_id`, `estado_operitivo`, `disponible`, `last_online_at`, `rating_promedio`, `total_viajes`, `licencia_numero`, `licencia_categoria`, `licencia_expira`, `soat_numero`, `soat_expira`, `seguro_numero`, `verificacion_estado`, `verificacion_nivel`, `verificacion_notas`, `contacto_emergencia_nombre`, `contacto_emergencia_telefono`, `location_permission`, `created_at`, `updated_at`, `deleted_at`) VALUES
(27, 44, 1, 1, '2026-04-20 23:02:18', NULL, 0, '1083920258', 'C1', '2030-05-16', '3328287500', '2026-12-19', NULL, 'verificado', 0, NULL, NULL, NULL, NULL, '2026-04-16 00:53:47', '2026-05-12 13:53:23', NULL),
(28, 46, 1, 0, '2026-05-27 14:41:42', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'verificado', 0, NULL, 'Stiven', '3208254627', NULL, '2026-04-16 18:32:31', '2026-05-27 14:41:59', NULL),
(29, 47, 1, 0, '2026-05-04 01:37:42', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'verificado', 0, NULL, NULL, NULL, NULL, '2026-04-18 01:04:59', '2026-05-15 16:27:23', NULL),
(30, 48, 1, 0, '2026-05-11 20:52:23', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'verificado', 0, NULL, NULL, NULL, NULL, '2026-04-18 03:38:05', '2026-05-11 21:36:43', NULL),
(32, 55, 1, 0, NULL, NULL, 0, 'CPT220', 'B1', '2027-04-10', NULL, '2026-12-18', NULL, 'verificado', 0, NULL, NULL, NULL, NULL, '2026-05-11 18:17:44', '2026-05-11 18:17:44', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conductor_posiciones`
--

CREATE TABLE `conductor_posiciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `conductor_id` bigint(20) UNSIGNED NOT NULL,
  `viaje_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lat` decimal(9,6) NOT NULL,
  `lng` decimal(9,6) NOT NULL,
  `ubicacion` point NOT NULL,
  `precision_m` smallint(5) UNSIGNED DEFAULT NULL,
  `velocidad_kmh` decimal(5,1) DEFAULT NULL,
  `heading` smallint(5) UNSIGNED DEFAULT NULL,
  `origen` enum('fg','bg','manual','system') DEFAULT NULL,
  `provider` enum('gps','network','fused','unknown') DEFAULT 'unknown',
  `bateria` tinyint(3) UNSIGNED DEFAULT NULL,
  `app_estado` enum('active','background') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `received_at` timestamp NULL DEFAULT NULL
) ;

--
-- Disparadores `conductor_posiciones`
--
DELIMITER $$
CREATE TRIGGER `trg_conductor_posiciones_ai` AFTER INSERT ON `conductor_posiciones` FOR EACH ROW BEGIN
  INSERT INTO conductor_posicion_actual
    (conductor_id, viaje_id, lat, lng, ubicacion, precision_m, velocidad_kmh, heading, origen, provider, bateria, app_estado, created_at, actualizada_at)
  VALUES
    (NEW.conductor_id, NEW.viaje_id, NEW.lat, NEW.lng, NEW.ubicacion, NEW.precision_m, NEW.velocidad_kmh, NEW.heading, NEW.origen, NEW.provider, NEW.bateria, NEW.app_estado, NOW(), NOW())
  ON DUPLICATE KEY UPDATE
    viaje_id        = VALUES(viaje_id),
    lat             = VALUES(lat),
    lng             = VALUES(lng),
    ubicacion       = VALUES(ubicacion),
    precision_m     = VALUES(precision_m),
    velocidad_kmh   = VALUES(velocidad_kmh),
    heading         = VALUES(heading),
    origen          = VALUES(origen),
    provider        = VALUES(provider),
    bateria         = VALUES(bateria),
    app_estado      = VALUES(app_estado),
    actualizada_at  = NOW();
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_conductor_posiciones_bi` BEFORE INSERT ON `conductor_posiciones` FOR EACH ROW BEGIN
  IF NEW.ubicacion IS NULL THEN
    SET NEW.ubicacion = POINT(NEW.lng, NEW.lat);
  END IF;
  IF NEW.created_at IS NULL THEN
    SET NEW.created_at = CURRENT_TIMESTAMP();
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conductor_posicion_actual`
--

CREATE TABLE `conductor_posicion_actual` (
  `conductor_id` bigint(20) UNSIGNED NOT NULL,
  `viaje_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lat` decimal(9,6) NOT NULL,
  `lng` decimal(9,6) NOT NULL,
  `ubicacion` point NOT NULL,
  `precision_m` smallint(5) UNSIGNED DEFAULT NULL,
  `velocidad_kmh` decimal(5,1) DEFAULT NULL,
  `heading` smallint(5) UNSIGNED DEFAULT NULL,
  `origen` enum('fg','bg','manual','system') DEFAULT NULL,
  `provider` enum('gps','network','fused','unknown') DEFAULT 'unknown',
  `bateria` tinyint(3) UNSIGNED DEFAULT NULL,
  `app_estado` enum('active','background') DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `actualizada_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Volcado de datos para la tabla `conductor_posicion_actual`
--

INSERT INTO `conductor_posicion_actual` (`conductor_id`, `viaje_id`, `lat`, `lng`, `ubicacion`, `precision_m`, `velocidad_kmh`, `heading`, `origen`, `provider`, `bateria`, `app_estado`, `created_at`, `actualizada_at`) VALUES
(27, NULL, 1.841307, -76.044028, 0x000000000101000000ae7da603fe75fd3f4d26135cd10253c0, NULL, 0.0, 227, NULL, 'unknown', NULL, NULL, '2026-04-16 01:43:21', '2026-05-06 01:50:48'),
(28, NULL, 1.847593, -76.064895, 0x00000000010100000081f0fcfdbd8ffd3f22ab5b3d270453c0, NULL, 7.0, 188, NULL, 'unknown', NULL, NULL, '2026-04-16 18:32:43', '2026-05-27 14:41:55'),
(29, NULL, 1.858052, -76.041365, 0x0000000001010000000762348694bafd3f28cd8bb8a50253c0, NULL, 0.0, 213, NULL, 'unknown', NULL, NULL, '2026-04-18 01:11:23', '2026-05-15 16:27:18'),
(30, NULL, 1.843818, -76.066384, 0x0000000001010000006a78c4324780fd3ff15defa33f0453c0, NULL, 0.0, 122, NULL, 'unknown', NULL, NULL, '2026-04-18 17:45:39', '2026-05-11 21:36:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos_conductor`
--

CREATE TABLE `documentos_conductor` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `conductor_id` bigint(20) UNSIGNED NOT NULL,
  `tipo` enum('licencia','soat','tecnomecanica','seguro_extracontractual','antecedentes','otro') NOT NULL DEFAULT 'licencia',
  `numero` varchar(80) DEFAULT NULL,
  `emisor` varchar(120) DEFAULT NULL,
  `expedido_at` date DEFAULT NULL,
  `expira_at` date DEFAULT NULL,
  `archivo_url` varchar(255) DEFAULT NULL,
  `archivo_mime` varchar(80) DEFAULT NULL,
  `archivo_size_kb` int(10) UNSIGNED DEFAULT NULL,
  `hash_sha256` char(64) DEFAULT NULL,
  `estado_verificacion` enum('pendiente','verificado','rechazado') NOT NULL DEFAULT 'pendiente',
  `verificado_por` int(25) DEFAULT NULL,
  `verificado_at` datetime DEFAULT NULL,
  `rechazo_motivo` varchar(255) DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llamadas`
--

CREATE TABLE `llamadas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `viaje_id` bigint(20) UNSIGNED NOT NULL,
  `llamador_user_id` int(25) NOT NULL,
  `llamador_rol` enum('pasajero','conductor','admin','sistema') NOT NULL DEFAULT 'pasajero',
  `receptor_user_id` int(25) DEFAULT NULL,
  `receptor_rol` enum('pasajero','conductor','admin') DEFAULT NULL,
  `tipo` enum('native','proxy') NOT NULL DEFAULT 'native',
  `provider` enum('native','twilio','vonage','infobip','other') NOT NULL DEFAULT 'native',
  `provider_call_id` varchar(100) DEFAULT NULL,
  `provider_room_id` varchar(100) DEFAULT NULL,
  `caller_phone_snapshot` varchar(30) DEFAULT NULL,
  `callee_phone_snapshot` varchar(30) DEFAULT NULL,
  `proxy_number` varchar(30) DEFAULT NULL,
  `masked` tinyint(1) NOT NULL DEFAULT 0,
  `estado` enum('iniciado','marcando','conectada','finalizada','rechazada','ocupado','no_contesta','fallo','cancelada') NOT NULL DEFAULT 'iniciado',
  `call_start_at` datetime NOT NULL DEFAULT current_timestamp(),
  `ring_start_at` datetime DEFAULT NULL,
  `connected_at` datetime DEFAULT NULL,
  `ended_at` datetime DEFAULT NULL,
  `duracion_seg` int(10) UNSIGNED DEFAULT NULL,
  `dispositivo_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `idempotencia` varchar(64) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(2, '2019_08_19_000000_create_failed_jobs_table', 1),
(3, '2019_12_14_000001_create_personal_access_tokens_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notas_operacion`
--

CREATE TABLE `notas_operacion` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `entity_type` enum('viaje','conductor','sos','wallet','documento','usuario','vehiculo','tarifa','otro') NOT NULL DEFAULT 'otro',
  `entity_id` bigint(20) UNSIGNED NOT NULL,
  `viaje_id` bigint(20) UNSIGNED DEFAULT NULL,
  `conductor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` int(25) DEFAULT NULL,
  `titulo` varchar(120) DEFAULT NULL,
  `nota` text NOT NULL,
  `adjunto_url` varchar(255) DEFAULT NULL,
  `adjunto_mime` varchar(80) DEFAULT NULL,
  `visibilidad` enum('operacion','finanzas','admin','soporte','general') NOT NULL DEFAULT 'operacion',
  `pinned` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(25) NOT NULL,
  `created_by_rol` enum('admin','operador','auditor','sistema') NOT NULL DEFAULT 'admin',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(25) NOT NULL,
  `viaje_id` bigint(20) UNSIGNED DEFAULT NULL,
  `canal` enum('push','app','sms','email') NOT NULL DEFAULT 'push',
  `proveedor` enum('fcm','apns','onesignal','twilio','sendgrid','local') DEFAULT 'fcm',
  `titulo` varchar(140) DEFAULT NULL,
  `cuerpo` varchar(500) DEFAULT NULL,
  `data_json` text DEFAULT NULL,
  `device_token_snapshot` varchar(255) DEFAULT NULL,
  `estado` enum('pendiente','enviado','entregado','abierto','fallido','cancelado') NOT NULL DEFAULT 'pendiente',
  `programada_at` datetime DEFAULT NULL,
  `enviada_at` datetime DEFAULT NULL,
  `entregada_at` datetime DEFAULT NULL,
  `abierta_at` datetime DEFAULT NULL,
  `fallida_at` datetime DEFAULT NULL,
  `provider_message_id` varchar(100) DEFAULT NULL,
  `error_code` varchar(60) DEFAULT NULL,
  `error_message` varchar(255) DEFAULT NULL,
  `idempotencia` varchar(64) DEFAULT NULL,
  `prioridad` enum('alta','media','baja') NOT NULL DEFAULT 'media',
  `origen_evento` varchar(80) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL,
  `permission` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `permissions`
--

INSERT INTO `permissions` (`permission_id`, `permission`, `role_id`) VALUES
(645, 'home/index', 1),
(646, 'account/index', 1),
(647, 'account/edit', 1),
(648, 'users/view', 1),
(649, 'users/add', 1),
(650, 'users/edit', 1),
(651, 'users/delete', 1),
(652, 'users/importdata', 1),
(653, 'users/index', 1),
(654, 'asignaciones/index', 1),
(655, 'asignaciones/view', 1),
(656, 'asignaciones/add', 1),
(657, 'asignaciones/edit', 1),
(658, 'asignaciones/delete', 1),
(659, 'auditoriaeventos/index', 1),
(660, 'auditoriaeventos/view', 1),
(661, 'auditoriaeventos/add', 1),
(662, 'auditoriaeventos/edit', 1),
(663, 'auditoriaeventos/delete', 1),
(664, 'calificaciones/index', 1),
(665, 'calificaciones/view', 1),
(666, 'calificaciones/add', 1),
(667, 'calificaciones/edit', 1),
(668, 'calificaciones/delete', 1),
(669, 'chatmensajes/index', 1),
(670, 'chatmensajes/view', 1),
(671, 'chatmensajes/add', 1),
(672, 'chatmensajes/edit', 1),
(673, 'chatmensajes/delete', 1),
(674, 'conductores/index', 1),
(675, 'conductores/view', 1),
(676, 'conductores/add', 1),
(677, 'conductores/edit', 1),
(678, 'conductores/delete', 1),
(679, 'conductorposicionactual/index', 1),
(680, 'conductorposicionactual/view', 1),
(681, 'conductorposicionactual/add', 1),
(682, 'conductorposicionactual/edit', 1),
(683, 'conductorposicionactual/delete', 1),
(684, 'conductorposiciones/index', 1),
(685, 'conductorposiciones/view', 1),
(686, 'conductorposiciones/add', 1),
(687, 'conductorposiciones/edit', 1),
(688, 'conductorposiciones/delete', 1),
(689, 'documentosconductor/index', 1),
(690, 'documentosconductor/view', 1),
(691, 'documentosconductor/add', 1),
(692, 'documentosconductor/edit', 1),
(693, 'documentosconductor/delete', 1),
(694, 'llamadas/index', 1),
(695, 'llamadas/view', 1),
(696, 'llamadas/add', 1),
(697, 'llamadas/edit', 1),
(698, 'llamadas/delete', 1),
(699, 'notasoperacion/index', 1),
(700, 'notasoperacion/view', 1),
(701, 'notasoperacion/add', 1),
(702, 'notasoperacion/edit', 1),
(703, 'notasoperacion/delete', 1),
(704, 'notificaciones/index', 1),
(705, 'notificaciones/view', 1),
(706, 'notificaciones/add', 1),
(707, 'notificaciones/edit', 1),
(708, 'notificaciones/delete', 1),
(709, 'permissions/index', 1),
(710, 'permissions/view', 1),
(711, 'permissions/add', 1),
(712, 'permissions/edit', 1),
(713, 'permissions/delete', 1),
(714, 'pushtokens/index', 1),
(715, 'pushtokens/view', 1),
(716, 'pushtokens/add', 1),
(717, 'pushtokens/edit', 1),
(718, 'pushtokens/delete', 1),
(719, 'roles/index', 1),
(720, 'roles/view', 1),
(721, 'roles/add', 1),
(722, 'roles/edit', 1),
(723, 'roles/delete', 1),
(724, 'sosincidentes/index', 1),
(725, 'sosincidentes/view', 1),
(726, 'sosincidentes/add', 1),
(727, 'sosincidentes/edit', 1),
(728, 'sosincidentes/delete', 1),
(729, 'tarifas/index', 1),
(730, 'tarifas/view', 1),
(731, 'tarifas/add', 1),
(732, 'tarifas/edit', 1),
(733, 'tarifas/delete', 1),
(734, 'usuariodispositivos/index', 1),
(735, 'usuariodispositivos/view', 1),
(736, 'usuariodispositivos/add', 1),
(737, 'usuariodispositivos/edit', 1),
(738, 'usuariodispositivos/delete', 1),
(739, 'vehiculos/index', 1),
(740, 'vehiculos/view', 1),
(741, 'vehiculos/add', 1),
(742, 'vehiculos/edit', 1),
(743, 'vehiculos/delete', 1),
(744, 'viajeestadoslog/index', 1),
(745, 'viajeestadoslog/view', 1),
(746, 'viajeestadoslog/add', 1),
(747, 'viajeestadoslog/edit', 1),
(748, 'viajeestadoslog/delete', 1),
(749, 'viajes/index', 1),
(750, 'viajes/view', 1),
(751, 'viajes/add', 1),
(752, 'viajes/edit', 1),
(753, 'viajes/delete', 1),
(754, 'walletmovimientos/index', 1),
(755, 'walletmovimientos/view', 1),
(756, 'walletmovimientos/add', 1),
(757, 'walletmovimientos/edit', 1),
(758, 'walletmovimientos/delete', 1),
(759, 'walletsaldos/index', 1),
(760, 'walletsaldos/view', 1),
(761, 'walletsaldos/add', 1),
(762, 'walletsaldos/edit', 1),
(763, 'walletsaldos/delete', 1),
(764, 'home/index', 2),
(765, 'account/index', 2),
(766, 'account/edit', 2),
(767, 'chatmensajes/index', 2),
(768, 'chatmensajes/view', 2),
(769, 'chatmensajes/add', 2),
(770, 'chatmensajes/edit', 2),
(771, 'chatmensajes/delete', 2),
(772, 'pushtokens/index', 2),
(773, 'pushtokens/view', 2),
(774, 'pushtokens/add', 2),
(775, 'pushtokens/edit', 2),
(776, 'pushtokens/delete', 2),
(777, 'home/index', 3),
(778, 'account/index', 3),
(779, 'account/edit', 3),
(780, 'asignaciones/index', 3),
(781, 'asignaciones/view', 3),
(782, 'asignaciones/add', 3),
(783, 'asignaciones/edit', 3),
(784, 'asignaciones/delete', 3),
(785, 'auditoriaeventos/index', 3),
(786, 'auditoriaeventos/view', 3),
(787, 'auditoriaeventos/add', 3),
(788, 'auditoriaeventos/edit', 3),
(789, 'auditoriaeventos/delete', 3),
(790, 'calificaciones/index', 3),
(791, 'calificaciones/view', 3),
(792, 'calificaciones/add', 3),
(793, 'calificaciones/edit', 3),
(794, 'calificaciones/delete', 3),
(795, 'chatmensajes/index', 3),
(796, 'chatmensajes/view', 3),
(797, 'chatmensajes/add', 3),
(798, 'chatmensajes/edit', 3),
(799, 'chatmensajes/delete', 3),
(800, 'conductores/index', 3),
(801, 'conductores/view', 3),
(802, 'conductores/add', 3),
(803, 'conductores/edit', 3),
(804, 'conductores/delete', 3),
(805, 'conductorposicionactual/index', 3),
(806, 'conductorposicionactual/view', 3),
(807, 'conductorposicionactual/add', 3),
(808, 'conductorposicionactual/edit', 3),
(809, 'conductorposicionactual/delete', 3),
(810, 'conductorposiciones/index', 3),
(811, 'conductorposiciones/view', 3),
(812, 'conductorposiciones/add', 3),
(813, 'conductorposiciones/edit', 3),
(814, 'conductorposiciones/delete', 3),
(815, 'documentosconductor/index', 3),
(816, 'documentosconductor/view', 3),
(817, 'documentosconductor/add', 3),
(818, 'documentosconductor/edit', 3),
(819, 'documentosconductor/delete', 3),
(820, 'llamadas/index', 3),
(821, 'llamadas/view', 3),
(822, 'llamadas/add', 3),
(823, 'llamadas/edit', 3),
(824, 'llamadas/delete', 3),
(825, 'notasoperacion/index', 3),
(826, 'notasoperacion/view', 3),
(827, 'notasoperacion/add', 3),
(828, 'notasoperacion/edit', 3),
(829, 'notasoperacion/delete', 3),
(830, 'notificaciones/index', 3),
(831, 'notificaciones/view', 3),
(832, 'notificaciones/add', 3),
(833, 'notificaciones/edit', 3),
(834, 'notificaciones/delete', 3),
(835, 'permissions/index', 3),
(836, 'permissions/view', 3),
(837, 'permissions/add', 3),
(838, 'permissions/edit', 3),
(839, 'permissions/delete', 3),
(840, 'pushtokens/index', 3),
(841, 'pushtokens/view', 3),
(842, 'pushtokens/add', 3),
(843, 'pushtokens/edit', 3),
(844, 'pushtokens/delete', 3),
(845, 'roles/index', 3),
(846, 'roles/view', 3),
(847, 'roles/add', 3),
(848, 'roles/edit', 3),
(849, 'roles/delete', 3),
(850, 'sosincidentes/index', 3),
(851, 'sosincidentes/view', 3),
(852, 'sosincidentes/add', 3),
(853, 'sosincidentes/edit', 3),
(854, 'sosincidentes/delete', 3),
(855, 'tarifas/index', 3),
(856, 'tarifas/view', 3),
(857, 'tarifas/add', 3),
(858, 'tarifas/edit', 3),
(859, 'tarifas/delete', 3),
(860, 'users/index', 3),
(861, 'usuariodispositivos/index', 3),
(862, 'usuariodispositivos/view', 3),
(863, 'usuariodispositivos/add', 3),
(864, 'usuariodispositivos/edit', 3),
(865, 'usuariodispositivos/delete', 3),
(866, 'vehiculos/index', 3),
(867, 'vehiculos/view', 3),
(868, 'vehiculos/add', 3),
(869, 'vehiculos/edit', 3),
(870, 'vehiculos/delete', 3),
(871, 'viajeestadoslog/index', 3),
(872, 'viajeestadoslog/view', 3),
(873, 'viajeestadoslog/add', 3),
(874, 'viajeestadoslog/edit', 3),
(875, 'viajeestadoslog/delete', 3),
(876, 'viajes/index', 3),
(877, 'viajes/view', 3),
(878, 'viajes/add', 3),
(879, 'viajes/edit', 3),
(880, 'viajes/delete', 3),
(881, 'walletmovimientos/index', 3),
(882, 'walletmovimientos/view', 3),
(883, 'walletmovimientos/add', 3),
(884, 'walletmovimientos/edit', 3),
(885, 'walletmovimientos/delete', 3),
(886, 'walletsaldos/index', 3),
(887, 'walletsaldos/view', 3),
(888, 'walletsaldos/add', 3),
(889, 'walletsaldos/edit', 3),
(890, 'walletsaldos/delete', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `push_tokens`
--

CREATE TABLE `push_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dispositivo_id` bigint(20) UNSIGNED NOT NULL,
  `provider` enum('fcm','apns','webpush') NOT NULL DEFAULT 'fcm',
  `token` varchar(255) NOT NULL,
  `token_hash` char(64) DEFAULT NULL,
  `estado` enum('valido','invalidado','revocado','expirado') NOT NULL DEFAULT 'valido',
  `scope` enum('prod','dev','test') NOT NULL DEFAULT 'prod',
  `ultimo_uso_at` datetime DEFAULT NULL,
  `invalidado_at` datetime DEFAULT NULL,
  `motivo_invalidez` text DEFAULT NULL,
  `idempotencia` varchar(64) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `push_tokens`
--

INSERT INTO `push_tokens` (`id`, `dispositivo_id`, `provider`, `token`, `token_hash`, `estado`, `scope`, `ultimo_uso_at`, `invalidado_at`, `motivo_invalidez`, `idempotencia`, `created_at`, `updated_at`) VALUES
(68, 60, 'fcm', 'c_M6r2HlSf2c6FiSKtvlIX:APA91bFoJx_fWO7jkYA89Jywd_-6LQdkBM7d2jywmmvuEo58aim5VlnEbOQt6KhT-yZ3fkl2CErdgC8_G-KEYQnuPohpm83XPKK0kYuHTufWGScLahtWD6E', '4fa3d2b916e75660040d183e0b48fc2f7e1e4f6b49b153f7841b41773482cf35', 'invalidado', 'prod', '2026-04-18 03:33:53', '2026-04-23 16:22:58', 'fcm:{\n  \"error\": {\n    \"code\": 404,\n    \"message\": \"NotRegistered\",\n    \"status\": \"NOT_FOUND\",\n    \"details\": [\n      {\n        \"@type\": \"type.googleapis.com/google.firebase.fcm.v1.FcmError\",\n        \"errorCode\": \"UNREGISTERED\"\n      }\n    ]\n  }\n}\n', NULL, '2026-04-15 23:50:26', '2026-04-23 20:22:58'),
(69, 59, 'fcm', 'eppA1VWqRSOuDahPbSr_pJ:APA91bEG0Q5bKbdmn4mo0FbBwrgYFLQtDY8ueJ1_HIxA79ccuXfirNKeB03YyeZWPlO7RZqj6PYuiT5zHfMZwAjdGPAgAf8BsL8g3LEZV8K0m8Q2PsQFGkg', 'd6b1918638c18f26093aac490b60f15bda0abbdf131380e999fffefce51a4518', 'valido', 'prod', '2026-05-16 14:40:31', NULL, NULL, NULL, '2026-04-16 01:56:25', '2026-05-16 18:40:31'),
(70, 57, 'fcm', 'c_M6r2HlSf2c6FiSKtvlIX:APA91bEbnmFWoQev2PEEEKoxWSOw8rQSxUUwV580EiqtJmqBhfDUGNVsALt6ip8SFAR2I138jZgYQfh6gHprmzeGOQfBnBl3aZZYmUaKF4X0M1BjgoScxjo', 'e7863de2d920849e97dc97d9bf23defbc271ce9bafd4fb1d6ade849fb925b3ac', 'valido', 'prod', '2026-05-23 10:12:57', NULL, NULL, NULL, '2026-04-19 20:14:40', '2026-05-23 14:12:57'),
(71, 61, 'fcm', 'c2e_1rVyTWmPBTDTy0PZ1g:APA91bHrmH1ZOqkXj0KXKRGiZ26hf1F060HeL6XGLVa8ZLqnJ-1xqaPmyoiNmSOBLO6-9y_TIZzt9oDvlTy3ROY7-ULplnf0NYu9S2KXrJi-pqlAh9i0rd4', '1007bd0249137b86022248e0fe038f2eafc98a971c344f96cae1b28404f657cc', 'valido', 'prod', '2026-05-27 14:20:21', NULL, NULL, NULL, '2026-04-20 14:47:08', '2026-05-27 18:20:21'),
(72, 62, 'fcm', 'cQoBpaq7RJGVrVerdSwuhU:APA91bFvcgxYMorpuRnwTIIc-JNkMeK54r9gYnc-Vc-8cEVRuwAVpx6COiM--rYzhwJuj9LOn2WEZ-AEvGGdJwFF4UL1KEicV6NbMvwQvcayqYGiiPewGxQ', '477e2eb7acc0ab76e573b4a04b8f7619d6d9a29ae08ba427f9691fa5c20b5bf7', 'valido', 'prod', '2026-04-23 01:00:28', NULL, NULL, NULL, '2026-04-23 01:00:28', '2026-04-23 05:00:28'),
(73, 63, 'fcm', 'dg8jcOhVRDifw53rlvFR7n:APA91bExykbOZOfl3SWvYOefMawKfswYmp6gqbUc5-jtg33gMw_9nZvJyZbbpKDqBdiNNkF1_HFKtPN3OxXvZxOzJi5SFm-kcDeEfNSRd12fXKqxQmmgc30', '7100d1ebe7999b48b821383d3698700b3d7689e13255d003899141b5a7df80e4', 'valido', 'prod', '2026-04-23 16:21:27', NULL, NULL, NULL, '2026-04-23 16:21:27', '2026-04-23 20:21:27'),
(74, 64, 'fcm', 'fxk2Z4XERyCSXgDvlVxfJq:APA91bE-YVcSBMWE_DHl2rhYY_WefYx4TXYSTZVK4yLg2TSCJ3XpaYeCPBoMQstpvrPlQhF9nHiTz2G2acQPjwg0DuZjmhDGo7ZSQugRdzYTiEj8ptEWhO8', '277ea93a8e58d5754618d1e145905f3fa7c0c678bd4d530879abd508ef70fe04', 'valido', 'prod', '2026-05-04 01:27:50', NULL, NULL, NULL, '2026-05-04 01:27:50', '2026-05-04 05:27:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'Admin'),
(2, 'Pasajero'),
(3, 'Conductor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sos_incidentes`
--

CREATE TABLE `sos_incidentes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `viaje_id` bigint(20) UNSIGNED DEFAULT NULL,
  `actor_tipo` enum('pasajero','conductor','admin','sistema') NOT NULL DEFAULT 'pasajero',
  `actor_user_id` int(25) DEFAULT NULL,
  `conductor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `categoria` enum('seguridad','accidente','salud','acoso','vehiculo','otro') DEFAULT 'seguridad',
  `severidad` enum('alta','media','baja') NOT NULL DEFAULT 'alta',
  `estado` enum('abierto','en_progreso','resuelto','cerrado','descartado') NOT NULL DEFAULT 'abierto',
  `descripcion` text DEFAULT NULL,
  `telefono_contacto` varchar(50) DEFAULT NULL,
  `lat` decimal(9,6) DEFAULT NULL,
  `lng` decimal(9,6) DEFAULT NULL,
  `ubicacion` point DEFAULT NULL,
  `operador_id` int(25) DEFAULT NULL,
  `asignado_at` datetime DEFAULT NULL,
  `reconocido_at` datetime DEFAULT NULL,
  `atendido_at` datetime DEFAULT NULL,
  `resuelto_at` datetime DEFAULT NULL,
  `cerrado_at` datetime DEFAULT NULL,
  `nivel_escalamiento` tinyint(3) UNSIGNED DEFAULT 0,
  `sla_minutos` int(10) UNSIGNED DEFAULT 0,
  `breach_at` datetime DEFAULT NULL,
  `contacto_inicial` enum('llamada','chat','push','sms','otro') DEFAULT NULL,
  `contacto_resultado` enum('exitoso','no_contesta','numero_invalido','no_procede') DEFAULT NULL,
  `evidencia_url` varchar(255) DEFAULT NULL,
  `notas_operacion` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarifas`
--

CREATE TABLE `tarifas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `scope` enum('global','ciudad','zona','ruta') NOT NULL DEFAULT 'global',
  `ciudad` varchar(80) DEFAULT NULL,
  `categoria` enum('taxi','taxi_electrico','taxi_van','taxi_plus','movilidad_reducida') NOT NULL DEFAULT 'taxi',
  `horario` enum('todo_dia','diurno','nocturno','fin_semana','festivo') NOT NULL DEFAULT 'todo_dia',
  `origen_ref` varchar(120) DEFAULT NULL,
  `destino_ref` varchar(120) DEFAULT NULL,
  `moneda` char(3) NOT NULL DEFAULT 'COP',
  `monto_fijo` decimal(12,2) NOT NULL,
  `recargo_nocturno` decimal(12,2) DEFAULT NULL,
  `recargo_festivo` decimal(12,2) DEFAULT NULL,
  `recargo_aeropuerto` decimal(12,2) DEFAULT NULL,
  `incluye_peajes` tinyint(1) NOT NULL DEFAULT 0,
  `minutos_espera_incluidos` smallint(5) UNSIGNED DEFAULT 0,
  `valor_minuto_espera` decimal(12,2) DEFAULT NULL,
  `vigente_desde` date NOT NULL,
  `vigente_hasta` date DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `prioridad` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `version` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Volcado de datos para la tabla `tarifas`
--

INSERT INTO `tarifas` (`id`, `nombre`, `descripcion`, `scope`, `ciudad`, `categoria`, `horario`, `origen_ref`, `destino_ref`, `moneda`, `monto_fijo`, `recargo_nocturno`, `recargo_festivo`, `recargo_aeropuerto`, `incluye_peajes`, `minutos_espera_incluidos`, `valor_minuto_espera`, `vigente_desde`, `vigente_hasta`, `activa`, `prioridad`, `version`, `created_at`, `updated_at`) VALUES
(4, 'Taxi Pitalito Base (Demo)', 'Tarifa demo para pruebas en Pitalito', 'ciudad', 'Pitalito', 'taxi', 'todo_dia', NULL, NULL, 'COP', 12000.00, 2000.00, 2000.00, 5.00, 0, 2, 300.00, '2025-01-01', NULL, 1, 1, 1, '2025-09-26 19:10:48', '2026-04-18 01:42:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(25) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefono` varchar(125) NOT NULL,
  `fotoperfil` longblob DEFAULT NULL,
  `remember_token` varchar(125) DEFAULT NULL,
  `estado` int(25) DEFAULT NULL,
  `user_role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `password`, `email`, `telefono`, `fotoperfil`, `remember_token`, `estado`, `user_role_id`) VALUES
(2, 'admin-taxpiya', '$2y$10$TD8mk.GznxI0o9zNsL7t1eIEax2qRBlm2Lwg9lDoHy6HXsiqkUn92', 'soporte@taxpiya.com', '300100100', 0x75706c6f6164732f66696c65732f65333736653438322d626336302d343961622d623436632d3237616335383738313436652e706e67, 'NIOnzaN3IbL621bX8ZG5ndoj5ozI0Yy1WcI6MCDcCZfXG6eacJRQygu6gGmX', 1, 1),
(3, 'VICTOR CASTILLO', '$2y$10$Z/aVxmdrx8lMFPxt3qCDjeknfg/LugzblS8KeZrtxHxeP3YbCcRBy', 'ridevs.js@gmail.com', '3017954934', 0x75706c6f6164732f66696c65732f35396631633235312d336238642d346435362d616538332d3930333266363232393862362e6a7067, 'UbcyiRXRIvnJFjLFiDIbec3vOhTnhkepyaU72cc8UCjtoFfTPjdITT7ow6jR', 1, 2),
(5, 'Medardo', '$2y$10$oEWYTuWDfD1gT46EhDSqi.PwkamI7/Xc21.snXWBbT840.KmYTZRG', 'medardo.0319@gmail.com', '3124959199', 0x75706c6f6164732f66696c65732f32333363623164302d346336622d346366322d626363392d3139663664383338366264332e6a7067, 'wnJOUO3XEJJFMqhzZJBAajxXoSBCnquq9rnwZRApkdXeezlcvaPRQ3Jfffa6', 1, 2),
(44, 'WILLIAM STEVEN ROJAS LOPEZ', '$2y$10$sgIofGDzwCCHDG1OMscF6utGLJzE9J9a4HB2YXURryq/DvGNGJZYC', 'orvejake@gmail.com', '3208254627', 0x75706c6f6164732f66696c65732f34353066333364652d383162352d343065312d626631362d3637336465626433623439352e6a7067, 'wZ4bWiR13twYMqVwRPOMy45YB34hrGhwLBdUSzBB19sDGAx5slJqa5IxgOBh', 1, 3),
(45, 'STEVEN ROJAS', '$2y$10$dHzDqQiTnQ2yULfiq7pDW.KVd2jXDSGEPG41svft21FdHN6GFZNKK', 'stiven14186@gmail.com', '3208254628', 0x75706c6f6164732f66696c65732f34346361666466632d656131662d343565382d386265362d6163646232643066653834382e6a7067, NULL, 1, 2),
(46, 'WILLIAM ROJAS ZAMBRANO', '$2y$10$g9PdowmyCLby7LzCd6yDzOhsumn1k.nfXSI3CUt6wex/axBMDWVGu', 'williamrojaszambrano8@gmail.com', '3248552977', 0x75706c6f6164732f66696c65732f38623134393438312d333832372d343564642d386266382d3336633539643239633233312e6a7067, NULL, 1, 3),
(47, 'Edison Mendez bohorques', '$2y$10$cyxaer.glX3WHY7GXtSSOum.iS481.ac0ATvv4LVw0Lua/OhpuAMK', 'edison041314@gmail.com', '3166065505', 0x75706c6f6164732f66696c65732f31353663313230612d616136322d343038652d626637612d3263333761316461323163662e6a7067, 'RlAbdUEWLihBJcfpGD2XKYuFRiVDlLJaPeoRsSgDU5svxXvrhAeGwNkgXVpL', 1, 3),
(48, 'EDWARD CASTILLO', '$2y$10$duK3jlxTY.49vQyXCx6K1Om4VIPcNCK/BlrUT7Q2YCAJe52EqtHT.', '2345edwuard4523@gmail.com', '3015611128', 0x75706c6f6164732f66696c65732f35346331316231352d613063662d346262302d396135332d6339396436353137333665372e6a7067, 'RGBPH12TV1gaCNxG2D7hXKiFTV3utbnPs9iOc2lbAY0aYE5eyMXxymHWq2Z4', 1, 3),
(49, 'WilliamR', '$2y$10$MoN6SZ7hTccO3uAS1vJxSOCTcwzrKJUsVhWBrvRqYekG0wuZH/g4i', 'williamrojaszambrano30@gmail.com', '3202885916', 0x75706c6f6164732f66696c65732f62626131633931612d313661362d343638652d623665632d3539633837343462333737652e6a7067, 'WwyvgWyY3MCJSiHqHDlGbqeluBUWYynJDTuwDZ8sar5htuQCmoSMMlDh779f', 1, 2),
(50, 'Nasly Rojas', '$2y$10$WQQYE7D0hNBbJCfaxoyYI.h6YVyY.3rCDLUjM3NO6g5L2VKYENREC', 'naslyrojaslopez097@gmail.com', '3176558720', NULL, 'ln6yNiPdYWRdh7XYeERnBQ3G6TV4HHvfkGqavzpd7qUlVrKkR3wZ2A5yNqBL', NULL, 2),
(51, 'María López', '$2y$10$baI1Koi7Qp2qC4Ui.nsD0.jdzlCnDAhaPdR8o1fFZswXa0EqTaoV.', 'marialopezartunduaga@gmail.com', '3125156198', NULL, 'BlbieSsrDW6B8WS5Uc0UuKfo2zR8lCvSq3cvKImEvR370QfFlf9nRjgl9Ttm', NULL, 2),
(52, 'William Rojas', '$2y$10$r4W84b6i19OmLay75lij1.S.KKRbXDfdw5ZYpXIdGhUYWhJqJ8xmC', 'rojaszambranowilliam@gmail.com', '3115350595', NULL, 'vvtYjGEY2UwLFEGbLqve3fcNFLtoaVlktdLydBhFbJ7uZQsnTUQrvRxu5q0F', NULL, 2),
(53, 'Ana Karolina Imbachi', '$2y$10$QTLEt5y/eKbFssVjmj2IduvfLG7dAqjTKKMxgX/eWvAHlR7KdXc/G', 'imbachigalindez123@gmail.com', '3133223811', NULL, '9SIno549ppSQTmdUNEaIUWrn3lhNjm07OBdIkrvseWsCGRuNkSPq96SjI21J', NULL, 2),
(54, 'Fredy', '$2y$10$dgPZnCT4CrQ4EMOk0wLPru7yPriePkPBeRpTLR5v28wwhn7ul3dR2', 'fredymauricio497@gmail.com', '3229372556', 0x75706c6f6164732f66696c65732f63626163386162332d336234362d343336352d623231352d3766396235646431333233322e6a7067, 'dKhPwxbnwcGftBiMAatuVB1yc4kwJ7BpiLelJjy9YLo2A3hncoJvm6ZvzVfW', 1, 2),
(55, 'demoprueba', '$2y$10$NQL/55wsgL5qyMQqHTbn.OQNs4dr9GXFBJc/8vDhx641RlmA1ggZO', 'demo@gmail.com', '3000000000', 0x75706c6f6164732f66696c65732f61393330346164652d306664612d346163302d616334632d3063303765636661396134622e706e67, NULL, 1, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_dispositivos`
--

CREATE TABLE `usuario_dispositivos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(25) NOT NULL,
  `device_uuid` varchar(128) NOT NULL,
  `plataforma` enum('android','ios','web') NOT NULL,
  `app_version` varchar(20) DEFAULT NULL,
  `os_version` varchar(30) DEFAULT NULL,
  `idioma` char(5) DEFAULT 'es-CO',
  `zona_horaria` varchar(40) DEFAULT NULL,
  `fabricante` varchar(60) DEFAULT NULL,
  `modelo` varchar(80) DEFAULT NULL,
  `notificaciones_activas` tinyint(1) NOT NULL DEFAULT 1,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `is_emulador` tinyint(1) NOT NULL DEFAULT 0,
  `root_jailbreak` tinyint(1) NOT NULL DEFAULT 0,
  `installed_at` datetime DEFAULT NULL,
  `last_seen_at` datetime DEFAULT NULL,
  `last_ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario_dispositivos`
--

INSERT INTO `usuario_dispositivos` (`id`, `user_id`, `device_uuid`, `plataforma`, `app_version`, `os_version`, `idioma`, `zona_horaria`, `fabricante`, `modelo`, `notificaciones_activas`, `activo`, `is_emulador`, `root_jailbreak`, `installed_at`, `last_seen_at`, `last_ip`, `created_at`, `updated_at`) VALUES
(57, 5, '3e7af6cec6d01867', 'android', NULL, NULL, 'es-CO', NULL, NULL, NULL, 1, 1, 0, 0, '2026-04-15 23:50:26', '2026-05-23 10:12:57', '186.102.44.82', '2026-04-16 03:50:26', '2026-05-23 14:12:57'),
(58, 44, '3e7af6cec6d01867', 'android', NULL, NULL, 'es-CO', NULL, NULL, NULL, 1, 1, 0, 0, '2026-04-16 00:49:20', '2026-05-06 01:50:48', '181.174.230.42', '2026-04-16 04:49:20', '2026-05-06 01:50:48'),
(59, 45, '0fb46b5ec2bd2f0b', 'android', NULL, NULL, 'es-CO', NULL, NULL, NULL, 1, 1, 0, 0, '2026-04-16 01:56:25', '2026-05-16 14:40:31', '181.174.230.42', '2026-04-16 05:56:25', '2026-05-16 18:40:31'),
(60, 48, '3e7af6cec6d01867', 'android', NULL, NULL, 'es-CO', NULL, NULL, NULL, 1, 1, 0, 0, '2026-04-18 03:33:53', '2026-05-11 21:36:38', '186.102.10.12', '2026-04-18 07:33:53', '2026-05-11 21:36:38'),
(61, 52, 'ae7c1286a34abd1f', 'android', NULL, NULL, 'es-CO', NULL, NULL, NULL, 1, 1, 0, 0, '2026-04-20 14:47:08', '2026-05-27 14:20:21', '191.156.157.160', '2026-04-20 18:47:08', '2026-05-27 18:20:21'),
(62, 53, '362fd6ec91aa24d3', 'android', NULL, NULL, 'es-CO', NULL, NULL, NULL, 1, 1, 0, 0, '2026-04-23 01:00:28', '2026-04-23 01:00:28', '191.156.59.208', '2026-04-23 05:00:28', '2026-04-23 05:00:28'),
(63, 50, '47d44ea7640ed476', 'android', NULL, NULL, 'es-CO', NULL, NULL, NULL, 1, 1, 0, 0, '2026-04-23 16:21:27', '2026-04-23 16:21:27', '181.174.230.42', '2026-04-23 20:21:27', '2026-04-23 20:21:27'),
(64, 54, '6f246aaf59b33764', 'android', NULL, NULL, 'es-CO', NULL, NULL, NULL, 1, 1, 0, 0, '2026-05-04 01:27:50', '2026-05-04 01:27:50', '191.156.224.236', '2026-05-04 05:27:50', '2026-05-04 05:27:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculos`
--

CREATE TABLE `vehiculos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `conductor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `placa` varchar(12) NOT NULL,
  `vin` varchar(32) DEFAULT NULL,
  `marca` varchar(60) DEFAULT NULL,
  `linea` varchar(60) DEFAULT NULL,
  `modelo_anio` smallint(5) UNSIGNED DEFAULT NULL,
  `color` varchar(40) DEFAULT NULL,
  `categoria` enum('taxi','taxi_electrico','taxi_van','taxi_plus','movilidad_reducida') NOT NULL DEFAULT 'taxi',
  `asientos` tinyint(3) UNSIGNED DEFAULT 4,
  `soat_numero` varchar(64) DEFAULT NULL,
  `soat_expira` date DEFAULT NULL,
  `tecnomecanica_expira` date DEFAULT NULL,
  `seguro_extracontractual_numero` varchar(64) DEFAULT NULL,
  `seguro_extracontractual_expira` date DEFAULT NULL,
  `estado_vehiculo` enum('activo','inactivo','suspendido') NOT NULL DEFAULT 'activo',
  `verificacion_estado` enum('pendiente','verificado','rechazado') NOT NULL DEFAULT 'pendiente',
  `verificacion_notas` text DEFAULT NULL,
  `foto_principal` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `vehiculos`
--

INSERT INTO `vehiculos` (`id`, `conductor_id`, `placa`, `vin`, `marca`, `linea`, `modelo_anio`, `color`, `categoria`, `asientos`, `soat_numero`, `soat_expira`, `tecnomecanica_expira`, `seguro_extracontractual_numero`, `seguro_extracontractual_expira`, `estado_vehiculo`, `verificacion_estado`, `verificacion_notas`, `foto_principal`, `created_at`, `updated_at`, `deleted_at`) VALUES
(30, 27, 'VZF503', 'MALAM51BAEM399059', 'HYUNDAY', 'I10 GL', 2014, 'AMARILLO', 'taxi', 4, '3328287500', '2026-12-19', '2026-08-21', '2000658269', '2026-09-25', 'activo', 'verificado', NULL, NULL, '2026-04-16 00:57:49', '2026-04-16 00:57:49', NULL),
(31, 28, 'VZF830', NULL, 'Hyundai', 'Grand i10', 2024, 'Amarillo', 'taxi', 4, NULL, NULL, NULL, NULL, NULL, 'activo', 'verificado', NULL, NULL, '2026-04-16 18:48:21', '2026-04-16 18:48:21', NULL),
(32, 29, 'VZF391', NULL, NULL, NULL, NULL, NULL, 'taxi', 4, NULL, NULL, NULL, NULL, NULL, 'activo', 'verificado', NULL, NULL, '2026-04-18 01:13:22', '2026-04-18 01:13:22', NULL),
(33, 30, 'UID814', NULL, NULL, NULL, NULL, NULL, 'taxi', 4, NULL, NULL, NULL, NULL, NULL, 'activo', 'verificado', NULL, NULL, '2026-04-18 03:40:03', '2026-04-18 03:40:03', NULL),
(34, 32, 'cpt220', NULL, NULL, NULL, NULL, NULL, 'taxi', 4, NULL, NULL, NULL, NULL, NULL, 'activo', 'verificado', NULL, NULL, '2026-05-11 18:18:08', '2026-05-11 18:18:08', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `viajes`
--

CREATE TABLE `viajes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pasajero_id` int(25) NOT NULL,
  `conductor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `vehiculo_id` bigint(20) UNSIGNED DEFAULT NULL,
  `origen_lat` decimal(9,6) NOT NULL,
  `origen_lng` decimal(9,6) NOT NULL,
  `origen_ubicacion` point NOT NULL,
  `origen_texto` varchar(255) DEFAULT NULL,
  `destino_lat` decimal(9,6) DEFAULT NULL,
  `destino_lng` decimal(9,6) DEFAULT NULL,
  `destino_ubicacion` point DEFAULT NULL,
  `destino_texto` varchar(255) DEFAULT NULL,
  `estado` enum('buscando','asignado','en_camino','llego','iniciado','terminado','cancelado_pasajero','cancelado_conductor','no_show','fallo_localizacion') NOT NULL DEFAULT 'buscando',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `asignado_at` datetime DEFAULT NULL,
  `aceptar_hasta` datetime DEFAULT NULL,
  `aceptado_at` datetime DEFAULT NULL,
  `en_camino_at` datetime DEFAULT NULL,
  `llego_at` datetime DEFAULT NULL,
  `iniciado_at` datetime DEFAULT NULL,
  `terminado_at` datetime DEFAULT NULL,
  `cancelado_at` datetime DEFAULT NULL,
  `cancelado_por` enum('pasajero','conductor','sistema') DEFAULT NULL,
  `cancelacion_motivo` varchar(255) DEFAULT NULL,
  `metodo_asignacion` enum('auto','manual') NOT NULL DEFAULT 'auto',
  `radio_busqueda_m` int(10) UNSIGNED DEFAULT NULL,
  `eta_min_estimada` decimal(5,2) DEFAULT NULL,
  `distancia_km_estimada` decimal(6,2) DEFAULT NULL,
  `duracion_min_estimada` decimal(6,2) DEFAULT NULL,
  `distancia_km_real` decimal(6,2) DEFAULT NULL,
  `duracion_min_real` decimal(6,2) DEFAULT NULL,
  `tarifa_id` bigint(20) UNSIGNED DEFAULT NULL,
  `moneda` char(3) NOT NULL DEFAULT 'COP',
  `tarifa_aplicada` decimal(12,2) DEFAULT NULL,
  `valor_pagado` decimal(12,2) DEFAULT NULL,
  `pago_registrado` tinyint(1) NOT NULL DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Volcado de datos para la tabla `viajes`
--

INSERT INTO `viajes` (`id`, `pasajero_id`, `conductor_id`, `vehiculo_id`, `origen_lat`, `origen_lng`, `origen_ubicacion`, `origen_texto`, `destino_lat`, `destino_lng`, `destino_ubicacion`, `destino_texto`, `estado`, `created_at`, `asignado_at`, `aceptar_hasta`, `aceptado_at`, `en_camino_at`, `llego_at`, `iniciado_at`, `terminado_at`, `cancelado_at`, `cancelado_por`, `cancelacion_motivo`, `metodo_asignacion`, `radio_busqueda_m`, `eta_min_estimada`, `distancia_km_estimada`, `duracion_min_estimada`, `distancia_km_real`, `duracion_min_real`, `tarifa_id`, `moneda`, `tarifa_aplicada`, `valor_pagado`, `pago_registrado`, `updated_at`) VALUES
(267, 45, NULL, NULL, 1.844973, -76.048898, 0x0000000001010000001fd55526210353c022e417b90285fd3f, 'Dg 3 Sur #1-68 B, Pitalito, Huila, Colombia', 1.841577, -76.042788, 0x000000000101000000bd6a0a09bd0253c0114361061977fd3f, 'Paraiso, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-16 01:57:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-16 01:57:40', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 8200.00, NULL, 0, '2026-04-16 05:57:40'),
(268, 45, NULL, NULL, 1.844973, -76.048898, 0x0000000001010000001fd55526210353c022e417b90285fd3f, 'Dg 3 Sur #1-68 B, Pitalito, Huila, Colombia', 1.841577, -76.042788, 0x000000000101000000bd6a0a09bd0253c0114361061977fd3f, 'Paraiso, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-16 02:01:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-16 02:05:35', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 8200.00, NULL, 0, '2026-04-16 06:05:35'),
(269, 45, 28, NULL, 1.841259, -76.043946, 0x00000000010100000024f2b803d00253c09258f734cc75fd3f, 'Cl. 2c Sur # 8E-15, Pitalito, Huila, Colombia', 1.845493, -76.049119, 0x0000000001010000003bc8ebc1240353c06536c8242387fd3f, 'Hospital San Antonio de Pitalito, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-16 18:35:55', '2026-04-16 18:36:14', NULL, '2026-04-16 18:36:14', NULL, NULL, NULL, NULL, '2026-04-16 18:37:42', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 8200.00, NULL, 0, '2026-04-16 22:37:42'),
(270, 45, 28, 31, 1.841259, -76.043946, 0x00000000010100000024f2b803d00253c09258f734cc75fd3f, 'Cl. 2c Sur # 8E-15, Pitalito, Huila, Colombia', 1.842355, -76.045073, 0x00000000010100000001000078e20253c017603c12497afd3f, 'Cl. 2c Sur # 5E-49, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-16 18:48:47', '2026-04-16 18:48:54', NULL, '2026-04-16 18:48:54', NULL, NULL, NULL, NULL, '2026-04-16 18:49:10', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 8200.00, NULL, 0, '2026-04-16 22:49:10'),
(271, 5, NULL, NULL, 1.840818, -76.049268, 0x0000000001010000004e8b1535270353c02d3e1746fd73fd3f, 'Cl. 3F Sur # 4-3, Pitalito, Huila, Colombia', 1.841542, -76.043871, 0x0000000001010000009613e1c6ce0253c05547b577f476fd3f, 'Cl. 2c Sur # 7E-36, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 00:33:52', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-18 00:34:07', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 8200.00, NULL, 0, '2026-04-18 04:34:07'),
(272, 5, NULL, NULL, 1.840818, -76.049268, 0x0000000001010000004e8b1535270353c02d3e1746fd73fd3f, 'Cl. 3F Sur # 4-3, Pitalito, Huila, Colombia', 1.841779, -76.041102, 0x0000000001010000007982fc69a10253c0fc1d9b23ed77fd3f, 'Cl. 1a #31, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 00:34:21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-18 00:34:30', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 8200.00, NULL, 0, '2026-04-18 04:34:30'),
(273, 45, NULL, NULL, 1.841277, -76.043947, 0x00000000010100000042507f07d00253c08e1546c4de75fd3f, 'Cl. 2c Sur # 8E-15, Pitalito, Huila, Colombia', 1.849585, -76.065950, 0x000000000101000000016c4084380453c060a692a6e697fd3f, 'Éxito Pitalito, Carrera 15, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 01:14:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-18 01:16:14', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 8200.00, NULL, 0, '2026-04-18 05:16:14'),
(274, 45, NULL, NULL, 1.841277, -76.043947, 0x00000000010100000042507f07d00253c08e1546c4de75fd3f, 'Cl. 2c Sur # 8E-15, Pitalito, Huila, Colombia', 1.849585, -76.065950, 0x000000000101000000016c4084380453c060a692a6e697fd3f, 'Éxito Pitalito, Carrera 15, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 01:16:20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-18 01:17:05', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 8200.00, NULL, 0, '2026-04-18 05:17:05'),
(275, 45, 27, 30, 1.841277, -76.043947, 0x00000000010100000042507f07d00253c08e1546c4de75fd3f, 'Cl. 2c Sur # 8E-15, Pitalito, Huila, Colombia', 1.849585, -76.065950, 0x000000000101000000016c4084380453c060a692a6e697fd3f, 'Éxito Pitalito, Carrera 15, Pitalito, Huila, Colombia', 'terminado', '2026-04-18 01:17:11', '2026-04-18 01:17:30', NULL, '2026-04-18 01:17:30', NULL, '2026-04-17 21:18:31', NULL, '2026-04-17 21:19:34', NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 8200.00, NULL, 0, '2026-04-18 01:19:34'),
(276, 45, 29, 32, 1.841278, -76.043970, 0x00000000010100000060217365d00253c02ec55565df75fd3f, 'Cl. 2c Sur #805, Pitalito, Huila, Colombia', 1.864860, -76.032450, 0x00000000010100000055302aa9130253c09fe5797077d6fd3f, 'Cra. 4 #1-31, Pitalito, Huila, Colombia', 'terminado', '2026-04-18 01:20:35', '2026-04-18 01:20:50', NULL, '2026-04-18 01:20:50', NULL, NULL, NULL, '2026-04-17 21:21:59', NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 8200.00, NULL, 0, '2026-04-18 01:21:59'),
(277, 45, 28, 31, 1.841284, -76.043947, 0x0000000001010000004e716605d00253c0e3288aa1e675fd3f, 'Cl. 2c Sur # 8E-15, Pitalito, Huila, Colombia', 1.865217, -76.032692, 0x0000000001010000003bf6589f170253c00f4d8f5cedd7fd3f, 'Cajero ATH Terminal Transportes Pitalito - Banco Popular, Carrera 4, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 01:22:41', '2026-04-18 01:22:47', NULL, '2026-04-18 01:22:47', NULL, NULL, NULL, NULL, '2026-04-18 01:22:49', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 8200.00, NULL, 0, '2026-04-18 05:22:49'),
(278, 5, NULL, NULL, 1.838945, -76.049543, 0x0000000001010000007f854bb62b0353c09fd4fec4516cfd3f, 'Cra. 6A # 4 SUR-56, Pitalito, Huila, Colombia', 1.840703, -76.045280, 0x000000000101000000014e8bdfe50253c07e05c97d8473fd3f, 'Cra. 6 Esté # 3CS-3, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 01:23:27', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-18 01:23:30', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 8200.00, NULL, 0, '2026-04-18 05:23:30'),
(279, 45, NULL, NULL, 1.841284, -76.043947, 0x0000000001010000004e716605d00253c0e3288aa1e675fd3f, 'Cl. 2c Sur # 8E-15, Pitalito, Huila, Colombia', 1.865217, -76.032692, 0x0000000001010000003bf6589f170253c00f4d8f5cedd7fd3f, 'Cajero ATH Terminal Transportes Pitalito - Banco Popular, Carrera 4, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 01:23:41', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-18 01:24:21', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 8200.00, NULL, 0, '2026-04-18 05:24:21'),
(280, 45, NULL, NULL, 1.841284, -76.043947, 0x0000000001010000004e716605d00253c0e3288aa1e675fd3f, 'Cl. 2c Sur # 8E-15, Pitalito, Huila, Colombia', 1.865217, -76.032692, 0x0000000001010000003bf6589f170253c00f4d8f5cedd7fd3f, 'Cajero ATH Terminal Transportes Pitalito - Banco Popular, Carrera 4, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 01:24:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-18 01:31:01', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 8200.00, NULL, 0, '2026-04-18 05:31:01'),
(281, 45, 27, 30, 1.841284, -76.043947, 0x0000000001010000004e716605d00253c0e3288aa1e675fd3f, 'Cl. 2c Sur # 8E-15, Pitalito, Huila, Colombia', 1.865217, -76.032692, 0x0000000001010000003bf6589f170253c00f4d8f5cedd7fd3f, 'Cajero ATH Terminal Transportes Pitalito - Banco Popular, Carrera 4, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 01:31:18', '2026-04-18 01:31:19', NULL, '2026-04-18 01:31:19', NULL, NULL, NULL, NULL, '2026-04-18 01:31:33', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 8200.00, NULL, 0, '2026-04-18 05:31:33'),
(282, 45, NULL, NULL, 1.851564, -76.026222, 0x000000000101000000adf3fda0ad0153c03c3aca1601a0fd3f, 'VX2F+JG Pitalito, Huila, Colombia', 1.865217, -76.032692, 0x0000000001010000003bf6589f170253c00f4d8f5cedd7fd3f, 'Cajero ATH Terminal Transportes Pitalito - Banco Popular, Carrera 4, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 01:31:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-18 01:32:04', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 8200.00, NULL, 0, '2026-04-18 05:32:04'),
(283, 45, 27, 30, 1.841284, -76.043945, 0x00000000010100000013b5d9fdcf0253c0e3288aa1e675fd3f, 'Cl. 2c Sur # 8E-15, Pitalito, Huila, Colombia', 1.865217, -76.032692, 0x0000000001010000003bf6589f170253c00f4d8f5cedd7fd3f, 'Cajero ATH Terminal Transportes Pitalito - Banco Popular, Carrera 4, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 01:32:31', '2026-04-18 01:32:38', NULL, '2026-04-18 01:32:38', NULL, NULL, NULL, NULL, '2026-04-18 01:32:56', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 8200.00, NULL, 0, '2026-04-18 05:32:56'),
(284, 45, NULL, NULL, 1.852250, -76.026180, 0x000000000101000000bdf3fdecac0153c07cba84b6d0a2fd3f, 'VX2F+VG Pitalito, Huila, Colombia', 1.865217, -76.032692, 0x0000000001010000003bf6589f170253c00f4d8f5cedd7fd3f, 'Cajero ATH Terminal Transportes Pitalito - Banco Popular, Carrera 4, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 01:33:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-18 01:33:14', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 8200.00, NULL, 0, '2026-04-18 05:33:14'),
(285, 45, 27, 30, 1.852324, -76.036860, 0x000000000101000000985aa3eb5b0253c05a57617b1ea3fd3f, 'Cl. 15 # 4E-26, Pitalito, Huila, Colombia', 1.863697, -76.034611, 0x000000000101000000f74d7211370253c0016928c2b3d1fd3f, 'Carrera 2, 100 mt antes del terminal, Pitalito, Huila, Colombia', 'terminado', '2026-04-18 01:36:30', '2026-04-18 13:34:52', NULL, '2026-04-18 13:34:52', NULL, '2026-04-18 09:38:58', NULL, '2026-04-18 09:39:31', NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 8200.00, NULL, 0, '2026-04-18 13:39:31'),
(286, 45, 29, 32, 1.848537, -76.038924, 0x000000000101000000011b21bc7d0253c0bfdcf48c9b93fd3f, 'Cl. 10 # 5E-21, Pitalito, Huila, Colombia', 1.861794, -76.036831, 0x0000000001010000000375dd6f5b0253c035085b90e8c9fd3f, 'Cra. 4a #2-52, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 01:37:14', '2026-04-18 01:37:21', NULL, '2026-04-18 01:37:21', NULL, NULL, NULL, NULL, '2026-04-18 01:37:42', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 8200.00, NULL, 0, '2026-04-18 05:37:42'),
(287, 45, 27, 30, 1.841090, -76.048343, 0x0000000001010000008c00010f180353c0c628e5241b75fd3f, 'Dg 3 Sur # 3E-3, Pitalito, Huila, Colombia', 1.858768, -76.051391, 0x000000000101000000623ad9fb490353c00a95b9fa83bdfd3f, 'Cra. 14 # 7-75, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 01:39:47', '2026-04-18 01:40:04', NULL, '2026-04-18 01:40:04', NULL, NULL, NULL, NULL, '2026-04-18 01:40:16', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 8200.00, NULL, 0, '2026-04-18 05:40:16'),
(288, 45, NULL, NULL, 1.838772, -76.046906, 0x0000000001010000009780cb81000353c04cbdec1c9c6bfd3f, 'Calle 3H Sur # 7A-23 Este, Pitalito, Huila, Colombia', 1.858768, -76.051391, 0x000000000101000000623ad9fb490353c00a95b9fa83bdfd3f, 'Cra. 14 # 7-75, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 01:41:44', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-18 01:41:56', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 1200.00, NULL, 0, '2026-04-18 05:41:56'),
(289, 45, 29, 32, 1.838772, -76.046906, 0x0000000001010000009780cb81000353c04cbdec1c9c6bfd3f, 'Calle 3H Sur # 7A-23 Este, Pitalito, Huila, Colombia', 1.858768, -76.051391, 0x000000000101000000623ad9fb490353c00a95b9fa83bdfd3f, 'Cra. 14 # 7-75, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 01:42:42', '2026-04-18 01:42:56', NULL, '2026-04-18 01:42:56', NULL, NULL, NULL, NULL, '2026-04-18 01:42:56', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-18 05:42:56'),
(290, 45, 29, 32, 1.841199, -76.045638, 0x000000000101000000930080bceb0253c099bba4988c75fd3f, 'Cl. 3c Sur # 6AE-3, Pitalito, Huila, Colombia', 1.858768, -76.051391, 0x000000000101000000623ad9fb490353c00a95b9fa83bdfd3f, 'Cra. 14 # 7-75, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 01:43:01', '2026-04-18 01:43:06', NULL, '2026-04-18 01:43:06', NULL, NULL, NULL, NULL, '2026-04-18 01:43:51', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-18 05:43:51'),
(291, 5, 27, 30, 1.837628, -76.041337, 0x0000000001010000008eb3ca44a50253c0ec1500d0ec66fd3f, 'Cra. 10ª # 3A SUR-51, Pitalito, Huila, Colombia', 1.847626, -76.043275, 0x000000000101000000213e8b05c50253c0eb0089a9e08ffd3f, 'Cra. 6 Esté # 4-67, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 01:47:22', '2026-04-18 01:47:23', NULL, '2026-04-18 01:47:23', NULL, '2026-04-17 21:47:32', NULL, NULL, '2026-04-18 01:48:38', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-18 05:48:38'),
(292, 5, NULL, NULL, 1.837628, -76.041337, 0x0000000001010000008eb3ca44a50253c0ec1500d0ec66fd3f, 'Cra. 10ª # 3A SUR-51, Pitalito, Huila, Colombia', 1.842779, -76.038521, 0x0000000001010000004102921f770253c01819901f067cfd3f, 'Cra. 6 Esté # 4A-6, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 01:48:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-18 01:48:53', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-18 05:48:53'),
(293, 5, 29, 32, 1.841275, -76.043961, 0x00000000010100000054d27943d00253c00c570740dc75fd3f, 'Cl. 2c Sur #805, Pitalito, Huila, Colombia', 1.839789, -76.042161, 0x000000000101000000645af9c2b20253c0419fd0edc66ffd3f, 'Cl. 2c Sur # 10E-5, Pitalito, Huila, Colombia', 'terminado', '2026-04-18 01:49:15', '2026-04-18 01:50:33', NULL, '2026-04-18 01:50:33', NULL, '2026-04-17 21:52:04', NULL, '2026-04-17 21:52:23', NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-18 01:52:23'),
(294, 5, NULL, NULL, 1.841279, -76.043965, 0x000000000101000000368bbc51d00253c040fcfcf7e075fd3f, 'Cl. 2c Sur #805, Pitalito, Huila, Colombia', 1.838961, -76.043100, 0x0000000001010000001a6d7c27c20253c087bff7d0626cfd3f, 'Cra 8 BE # 3A SUR-51, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 01:50:12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-18 01:51:09', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-18 05:51:09'),
(295, 45, 28, 31, 1.841274, -76.043960, 0x000000000101000000add5c33cd00253c06ca7f79edb75fd3f, 'Cl. 2c Sur #805, Pitalito, Huila, Colombia', 1.843871, -76.035866, 0x000000000101000000f93f9fa14b0253c04ee48f327f80fd3f, 'RXV7+GM Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 01:52:51', '2026-04-18 01:52:57', NULL, '2026-04-18 01:52:57', NULL, '2026-04-17 21:53:00', NULL, NULL, '2026-04-18 01:53:16', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-18 05:53:16'),
(296, 45, 27, 30, 1.841274, -76.043960, 0x000000000101000000add5c33cd00253c06ca7f79edb75fd3f, 'Cl. 2c Sur #805, Pitalito, Huila, Colombia', 1.844824, -76.039973, 0x0000000001010000001e404eeb8e0253c0ba30739c6684fd3f, 'Cra 8 BE # 5-17, Pitalito, Huila, Colombia', 'terminado', '2026-04-18 01:53:35', '2026-04-18 01:53:41', NULL, '2026-04-18 01:53:41', NULL, '2026-04-17 21:53:45', NULL, '2026-04-17 21:53:58', NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-18 01:53:58'),
(297, 45, 28, 31, 1.841277, -76.043947, 0x00000000010100000019d1d105d00253c0be3dbe73de75fd3f, 'Cl. 2c Sur # 8E-15, Pitalito, Huila, Colombia', 1.846093, -76.042076, 0x000000000101000000bed34d60b10253c0957bcd089989fd3f, 'Barber shop el gato, Cra 5 Este #3-56, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 02:39:21', '2026-04-18 02:39:26', NULL, '2026-04-18 02:39:26', NULL, NULL, NULL, NULL, '2026-04-18 02:39:33', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-18 06:39:33'),
(298, 45, 28, 31, 1.841277, -76.043947, 0x00000000010100000019d1d105d00253c0be3dbe73de75fd3f, 'Cl. 2c Sur # 8E-15, Pitalito, Huila, Colombia', 1.850304, -76.041099, 0x0000000001010000009c13135ca10253c0c36cc229d89afd3f, 'Cra. 1A Bis #13-27, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 02:39:48', '2026-04-18 02:39:51', NULL, '2026-04-18 02:39:51', NULL, NULL, NULL, NULL, '2026-04-18 02:39:59', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-18 06:39:59'),
(299, 45, NULL, NULL, 1.841277, -76.043947, 0x00000000010100000019d1d105d00253c0be3dbe73de75fd3f, 'Cl. 2c Sur # 8E-15, Pitalito, Huila, Colombia', 1.851278, -76.041664, 0x000000000101000000cb137c9eaa0253c05ab43529d59efd3f, 'Cl. 10 # 1-35, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 02:41:04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-18 02:41:10', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-18 06:41:10'),
(300, 45, NULL, NULL, 1.841277, -76.043947, 0x00000000010100000019d1d105d00253c0be3dbe73de75fd3f, 'Cl. 2c Sur # 8E-15, Pitalito, Huila, Colombia', 1.853729, -76.049054, 0x00000000010100000099132bb2230353c065ad0e06e0a8fd3f, 'Cl 5 # 8-1, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 02:41:34', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-18 02:41:41', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-18 06:41:41'),
(301, 45, NULL, NULL, 1.840229, -76.044621, 0x00000000010100000001111310db0253c04f7fd8279471fd3f, 'Cra. 8 Este #3c - 05 Sur, Pitalito, Huila, Colombia', 1.853729, -76.049054, 0x00000000010100000099132bb2230353c065ad0e06e0a8fd3f, 'Cl 5 # 8-1, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 02:41:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-18 02:41:55', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-18 06:41:55'),
(302, 45, NULL, NULL, 1.837722, -76.047361, 0x0000000001010000000b9141f8070353c0f0599b104f67fd3f, 'Cra. 9 Este #3 - 04 SUR, Pitalito, Huila, Colombia', 1.853729, -76.049054, 0x00000000010100000099132bb2230353c065ad0e06e0a8fd3f, 'Cl 5 # 8-1, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 02:42:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-18 02:43:52', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-18 06:43:52'),
(303, 45, NULL, NULL, 1.833018, -76.042592, 0x000000000101000000301181d2b90253c020b9e64b0a54fd3f, 'Km 1 vía Vereda El Macal, Pitalito, Huila, Colombia', 1.853729, -76.049054, 0x00000000010100000099132bb2230353c065ad0e06e0a8fd3f, 'Cl 5 # 8-1, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 02:44:11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-18 02:44:20', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-18 06:44:20'),
(304, 45, 29, 32, 1.851197, -76.034189, 0x0000000001010000003091bc28300253c0f676741f819efd3f, 'Carrera 8E # 18-7, Pitalito, Huila, Colombia', 1.853729, -76.049054, 0x00000000010100000099132bb2230353c065ad0e06e0a8fd3f, 'Cl 5 # 8-1, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 02:44:36', '2026-04-18 02:44:45', NULL, '2026-04-18 02:44:45', NULL, '2026-04-17 22:44:47', NULL, NULL, '2026-04-18 02:45:02', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-18 06:45:02'),
(305, 45, NULL, NULL, 1.848112, -76.031833, 0x0000000001010000003591c98d090253c0cda72753de91fd3f, 'RXX9+67 Pitalito, Huila, Colombia', 1.853729, -76.049054, 0x00000000010100000099132bb2230353c065ad0e06e0a8fd3f, 'Cl 5 # 8-1, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 02:45:15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-18 02:47:48', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-18 06:47:48'),
(306, 45, NULL, NULL, 1.858757, -76.038982, 0x0000000001010000000e9114af7e0253c0a39ff42078bdfd3f, 'Cl. 20 A # 3-79, Pitalito, Huila, Colombia', 1.853729, -76.049054, 0x00000000010100000099132bb2230353c065ad0e06e0a8fd3f, 'Cl 5 # 8-1, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 02:48:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-18 02:48:06', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-18 06:48:06'),
(307, 45, NULL, NULL, 1.845969, -76.043118, 0x000000000101000000fd10d872c20253c0424d7bf11689fd3f, 'Cl. 3 # 4E-48, Pitalito, Huila, Colombia', 1.853729, -76.049054, 0x00000000010100000099132bb2230353c065ad0e06e0a8fd3f, 'Cl 5 # 8-1, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 02:48:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-18 02:48:33', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-18 06:48:33'),
(308, 45, 29, 32, 1.847192, -76.044945, 0x00000000010100000004912e60e00253c04ea5fbc6188efd3f, 'Cra. 1B # 2-73, Pitalito, Huila, Colombia', 1.853729, -76.049054, 0x00000000010100000099132bb2230353c065ad0e06e0a8fd3f, 'Cl 5 # 8-1, Pitalito, Huila, Colombia', 'terminado', '2026-04-18 02:48:40', '2026-04-18 02:48:58', NULL, '2026-04-18 02:48:58', NULL, '2026-04-17 22:49:13', NULL, '2026-04-17 22:49:24', NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-18 02:49:24'),
(309, 45, 29, 32, 1.851085, -76.036600, 0x000000000101000000f7999ba6570253c0d8e44cd00a9efd3f, 'Cra. 1B # 14-21, Pitalito, Huila, Colombia', 1.848601, -76.043372, 0x0000000001010000000b00009cc60253c05334969bde93fd3f, 'Cra. 1A Bis #5-40, Pitalito, Huila, Colombia', 'terminado', '2026-04-18 02:51:50', '2026-04-18 02:52:04', NULL, '2026-04-18 02:52:04', NULL, '2026-04-17 22:52:18', NULL, '2026-04-17 22:52:19', NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-18 02:52:19'),
(310, 49, 28, 31, 1.841176, -76.044015, 0x00000000010100000083818c25d10253c07db9aa477575fd3f, 'Calle 2c Sur #805, Pitalito, Huila, Colombia', 1.849585, -76.065950, 0x000000000101000000016c4084380453c060a692a6e697fd3f, 'Cra. 15 #19A 31, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 12:38:14', '2026-04-18 12:38:25', NULL, '2026-04-18 12:38:25', NULL, '2026-04-18 08:39:11', NULL, NULL, '2026-04-18 12:40:39', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-18 16:40:39'),
(311, 49, 28, 31, 1.841176, -76.044015, 0x00000000010100000083818c25d10253c07db9aa477575fd3f, 'Calle 2c Sur #805, Pitalito, Huila, Colombia', 1.847284, -76.039362, 0x000000000101000000f37aa1e7840253c0c3f8360f7a8efd3f, 'Cl. 4 # 5A-63, Pitalito, Huila, Colombia', 'terminado', '2026-04-18 12:42:22', '2026-04-18 12:43:02', NULL, '2026-04-18 12:43:02', NULL, NULL, NULL, '2026-04-18 08:44:40', NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-18 12:44:40'),
(312, 50, 28, 31, 1.841282, -76.043950, 0x0000000001010000005aa95615d00253c0fc5c2338e475fd3f, 'Cl. 2c Sur # 8-05 este, Pitalito, Huila, Colombia', 1.850237, -76.064175, 0x000000000101000000228e75711b0453c086a3f501929afd3f, 'San Antonio Plaza, Neiva-San Agustín, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-18 23:09:33', '2026-04-18 23:09:47', NULL, '2026-04-18 23:09:47', NULL, NULL, NULL, NULL, '2026-04-18 23:11:08', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-19 03:11:08'),
(313, 45, 29, 32, 1.845388, -76.046396, 0x0000000001010000001b792e27f80253c055de480fb586fd3f, 'Cl. 2a Sur #2e-93 a 2e-1, Pitalito, Huila, Colombia', 1.838070, -76.043744, 0x0000000001010000004a79a2b1cc0253c058097e4ebc68fd3f, 'Cra. 10ª # 3E SUR-3, Pitalito, Huila, Colombia', 'terminado', '2026-04-19 00:09:18', '2026-04-25 00:38:47', NULL, '2026-04-25 00:38:47', NULL, '2026-04-24 20:38:55', NULL, '2026-04-24 20:39:00', NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-25 00:39:00'),
(314, 45, 27, 30, 1.846117, -76.059350, 0x00000000010100000051b93862cc0353c0e7c6aca9b189fd3f, 'Cra. 3 # 12 SUR-82, Pitalito, Huila, Colombia', 1.838070, -76.043744, 0x0000000001010000004a79a2b1cc0253c058097e4ebc68fd3f, 'Cra. 10ª # 3E SUR-3, Pitalito, Huila, Colombia', 'terminado', '2026-04-19 00:10:47', '2026-04-20 22:46:38', NULL, '2026-04-20 22:46:38', NULL, '2026-04-20 18:55:50', NULL, '2026-04-20 18:58:18', NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-20 22:58:18'),
(315, 45, 29, 32, 1.846117, -76.059350, 0x00000000010100000051b93862cc0353c0e7c6aca9b189fd3f, 'Cra. 3 # 12 SUR-82, Pitalito, Huila, Colombia', 1.838070, -76.043744, 0x0000000001010000004a79a2b1cc0253c058097e4ebc68fd3f, 'Cra. 10ª # 3E SUR-3, Pitalito, Huila, Colombia', 'terminado', '2026-04-19 00:12:44', '2026-04-19 00:21:49', NULL, '2026-04-19 00:21:49', NULL, '2026-04-18 20:21:54', NULL, '2026-04-18 20:21:57', NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-19 00:21:57'),
(316, 45, 28, 31, 1.846117, -76.059350, 0x00000000010100000051b93862cc0353c0e7c6aca9b189fd3f, 'Cra. 3 # 12 SUR-82, Pitalito, Huila, Colombia', 1.838070, -76.043744, 0x0000000001010000004a79a2b1cc0253c058097e4ebc68fd3f, 'Cra. 10ª # 3E SUR-3, Pitalito, Huila, Colombia', 'terminado', '2026-04-19 00:13:34', '2026-04-19 03:05:03', NULL, '2026-04-19 03:05:03', NULL, '2026-04-18 23:08:15', NULL, '2026-04-18 23:09:15', NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-19 03:09:15'),
(317, 45, 30, 33, 1.842083, -76.066929, 0x0000000001010000001bb9348f480453c04c32800d2c79fd3f, 'Cl. 23a Sur # 2E-154, Pitalito, Huila, Colombia', 1.838812, -76.041923, 0x0000000001010000004479fedcae0253c0b6d27500c66bfd3f, 'Cl. 3A Sur # 10E-66, Pitalito, Huila, Colombia', 'asignado', '2026-04-19 00:16:09', '2026-05-11 20:52:23', NULL, '2026-05-11 20:52:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-12 00:52:23'),
(318, 45, 27, 30, 1.842083, -76.066929, 0x0000000001010000001bb9348f480453c04c32800d2c79fd3f, 'Cl. 23a Sur # 2E-154, Pitalito, Huila, Colombia', 1.838812, -76.041923, 0x0000000001010000004479fedcae0253c0b6d27500c66bfd3f, 'Cl. 3A Sur # 10E-66, Pitalito, Huila, Colombia', 'terminado', '2026-04-19 00:17:54', '2026-04-20 22:59:44', NULL, '2026-04-20 22:59:44', NULL, '2026-04-20 19:01:12', NULL, '2026-04-20 19:02:18', NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-20 23:02:18'),
(319, 45, 28, 31, 1.843182, -76.061626, 0x00000000010100000034b902aff10353c012b09c89ac7dfd3f, 'Cl. 15 Sur # 1E-94, Pitalito, Huila, Colombia', 1.838812, -76.041923, 0x0000000001010000004479fedcae0253c0b6d27500c66bfd3f, 'Cl. 3A Sur # 10E-66, Pitalito, Huila, Colombia', 'asignado', '2026-04-19 00:18:29', '2026-04-19 03:01:17', NULL, '2026-04-19 03:01:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-19 07:01:17'),
(320, 52, NULL, NULL, 4.654970, -74.117837, 0x00000000010100000060c595a38a8752c08434227db09e1240, 'Terminal pitalito', 1.845493, -76.049119, 0x0000000001010000003bc8ebc1240353c06536c8242387fd3f, 'Hospital San Antonio de Pitalito, Pitalito, Huila, Colombia', 'buscando', '2026-04-20 14:50:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-20 14:50:47'),
(321, 52, NULL, NULL, 4.654970, -74.117837, 0x00000000010100000060c595a38a8752c08434227db09e1240, 'Terminal pitalito', 1.845493, -76.049119, 0x0000000001010000003bc8ebc1240353c06536c8242387fd3f, 'Hospital San Antonio de Pitalito, Pitalito, Huila, Colombia', 'buscando', '2026-04-20 14:56:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-20 14:56:54'),
(322, 52, NULL, NULL, 4.654970, -74.117837, 0x00000000010100000060c595a38a8752c08434227db09e1240, 'Terminal pitalito', 1.845493, -76.049119, 0x0000000001010000003bc8ebc1240353c06536c8242387fd3f, 'Hospital San Antonio de Pitalito, Pitalito, Huila, Colombia', 'buscando', '2026-04-20 14:58:01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-20 14:58:01'),
(323, 52, NULL, NULL, 4.654970, -74.117837, 0x00000000010100000060c595a38a8752c08434227db09e1240, 'Terminal pitalito', 1.845493, -76.049119, 0x0000000001010000003bc8ebc1240353c06536c8242387fd3f, 'Hospital San Antonio de Pitalito, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-20 15:01:44', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-20 15:01:47', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-20 19:01:47'),
(324, 45, NULL, NULL, 1.841239, -76.048990, 0x000000000101000000c5b4caa7220353c0732d5a80b675fd3f, 'Cra. 6 Esté # 3FS-17, Pitalito, Huila, Colombia', 1.853276, -76.039038, 0x000000000101000000100000987f0253c06e256c0c05a7fd3f, 'Cl. 14 #3e-57 a 3e, 1, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-20 16:54:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-20 16:54:27', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-20 20:54:27'),
(325, 52, NULL, NULL, 1.841220, -76.048948, 0x000000000101000000b9ad88f5210353c006e973ffa275fd3f, 'Cra. 6 Esté # 3FS-9, Pitalito, Huila, Colombia', 1.854305, -76.036333, 0x000000000101000000ea5a7f46530253c051da38323babfd3f, 'Cra. 1A # 17-78, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-20 16:57:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-20 16:57:48', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-20 20:57:48'),
(326, 52, NULL, NULL, 1.841220, -76.048948, 0x000000000101000000b9ad88f5210353c006e973ffa275fd3f, 'Cra. 6 Esté # 3FS-9, Pitalito, Huila, Colombia', 1.854305, -76.036333, 0x000000000101000000ea5a7f46530253c051da38323babfd3f, 'Cra. 1A # 17-78, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-20 17:00:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-20 17:00:53', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-20 21:00:53'),
(327, 52, 28, 31, 1.841220, -76.048948, 0x000000000101000000b9ad88f5210353c006e973ffa275fd3f, 'Cra. 6 Esté # 3FS-9, Pitalito, Huila, Colombia', 1.854305, -76.036333, 0x000000000101000000ea5a7f46530253c051da38323babfd3f, 'Cra. 1A # 17-78, Pitalito, Huila, Colombia', 'terminado', '2026-04-20 17:19:48', '2026-04-20 17:20:00', NULL, '2026-04-20 17:20:00', NULL, '2026-04-20 13:20:20', NULL, '2026-04-20 13:20:35', NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-20 17:20:35'),
(328, 52, NULL, NULL, 2.573350, -75.793910, 0x000000000101000000e97de36bcff252c0f54a598638960440, 'Vereda los laureles', 1.845012, -76.050274, 0x000000000101000000def41db2370353c008e753c72a85fd3f, 'Hospital Departamental san Antonio de, Pitalito, Huila, Colombia', 'buscando', '2026-04-20 22:23:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-20 22:23:07'),
(329, 52, NULL, NULL, 2.573350, -75.793910, 0x000000000101000000e97de36bcff252c0f54a598638960440, NULL, 2.136605, -76.109006, 0x000000000101000000d0343ff4f90653c0b649b348c4170140, '4VPR+J9 La Argentina, Huila, Colombia', 'cancelado_pasajero', '2026-04-20 22:25:28', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-20 22:25:30', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-21 02:25:30'),
(330, 52, NULL, NULL, 2.573350, -75.793910, 0x000000000101000000e97de36bcff252c0f54a598638960440, NULL, 2.136605, -76.109006, 0x000000000101000000d0343ff4f90653c0b649b348c4170140, '4VPR+J9 La Argentina, Huila, Colombia', 'cancelado_pasajero', '2026-04-20 22:27:22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-20 22:27:37', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-21 02:27:37'),
(331, 52, NULL, NULL, 2.573350, -75.793910, 0x000000000101000000e97de36bcff252c0f54a598638960440, NULL, 2.136605, -76.109006, 0x000000000101000000d0343ff4f90653c0b649b348c4170140, '4VPR+J9 La Argentina, Huila, Colombia', 'cancelado_pasajero', '2026-04-20 22:41:22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-20 22:41:35', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-21 02:41:35'),
(332, 52, NULL, NULL, 1.846302, -76.063085, 0x000000000101000000fb37d494090453c08f86f82c748afd3f, 'Cra. 3 #17 Sur, Tv. 3A Sur #17 Sur-77, Pitalito, Huila, Colombia', 1.841764, -76.063069, 0x0000000001010000008969ea50090453c086a62dd1dd77fd3f, 'RWRP+PQ Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-21 16:58:04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-21 16:58:33', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-21 20:58:33'),
(333, 52, NULL, NULL, 1.846302, -76.063085, 0x000000000101000000fb37d494090453c08f86f82c748afd3f, 'Cra. 3 #17 Sur, Tv. 3A Sur #17 Sur-77, Pitalito, Huila, Colombia', 1.841764, -76.063069, 0x0000000001010000008969ea50090453c086a62dd1dd77fd3f, 'RWRP+PQ Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-21 16:59:15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-21 16:59:17', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-21 20:59:17'),
(334, 52, NULL, NULL, 1.846302, -76.063085, 0x000000000101000000fb37d494090453c08f86f82c748afd3f, 'Cra. 3 #17 Sur, Tv. 3A Sur #17 Sur-77, Pitalito, Huila, Colombia', 1.841764, -76.063069, 0x0000000001010000008969ea50090453c086a62dd1dd77fd3f, 'Hospital sanantonio', 'cancelado_pasajero', '2026-04-21 16:59:58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-21 17:31:41', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-21 21:31:41'),
(335, 45, NULL, NULL, 1.841285, -76.043956, 0x0000000001010000000ddefc2bd00253c054b02193e775fd3f, 'Cl. 2c Sur #805, Pitalito, Huila, Colombia', 1.851646, -76.046827, 0x000000000101000000feffff35ff0253c00c4334f157a0fd3f, 'Parque Principal José Hilario López, Cl 6 #480, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-21 17:31:11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-21 17:31:23', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-21 21:31:23'),
(336, 52, NULL, NULL, 1.849252, -76.064599, 0x000000000101000000b9bd9664220453c09caa84488996fd3f, 'Cra. 15 # 19B-26, Pitalito, Huila, Colombia', 1.841341, -76.064548, 0x000000000101000000133d308c210453c0c6e52ae12176fd3f, 'Cl. 19 Sur #4 24 este, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-21 17:31:58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-21 17:32:07', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-21 21:32:07'),
(337, 52, NULL, NULL, 1.841284, -76.043951, 0x000000000101000000ef682d16d00253c073a1f2afe575fd3f, 'Cl. 2c Sur #805, Pitalito, Huila, Colombia', 1.864943, -76.032515, 0x0000000001010000001f0000b8140253c09c110c9bced6fd3f, 'Cra. 4 #31 – 15, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-21 17:32:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-21 17:32:37', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-21 21:32:37'),
(338, 52, 28, 31, 1.841284, -76.043951, 0x000000000101000000ef682d16d00253c073a1f2afe575fd3f, 'Cl. 2c Sur #805, Pitalito, Huila, Colombia', 1.850102, -76.048865, 0x000000000101000000e8ffff9b200353c0f3d492c9049afd3f, 'Cra. 5 #2-42, Pitalito, Huila, Colombia', 'terminado', '2026-04-21 17:33:49', '2026-04-21 17:34:06', NULL, '2026-04-21 17:34:06', NULL, '2026-04-21 13:51:52', NULL, '2026-04-21 13:52:02', NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-21 17:52:02'),
(339, 52, 28, 31, 1.864673, -76.032688, 0x0000000001010000008fddaa90170253c0219793abb3d5fd3f, 'Cra. 4 #1-31, Pitalito, Huila, Colombia', 1.857587, -76.034725, 0x0000000001010000000c0000ee380253c08cb9692dadb8fd3f, 'Cra. 1A #2045, Pitalito, Huila, Colombia', 'terminado', '2026-04-21 19:17:42', '2026-04-21 19:17:53', NULL, '2026-04-21 19:17:53', NULL, '2026-04-21 15:18:16', NULL, '2026-04-21 15:18:39', NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-21 19:18:39'),
(340, 52, NULL, NULL, 1.854447, -76.037407, 0x000000000101000000af4912df640253c04ecd8a4dd0abfd3f, 'Cra. 1c # 17-12, Pitalito, Huila, Colombia', 1.846992, -76.041505, 0x000000000101000000dfffff05a80253c0200239fc478dfd3f, 'C.P, Cra 5 Este #5a-09, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-23 01:00:42', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-23 01:01:01', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-23 05:01:01'),
(341, 52, 28, 31, 1.851646, -76.046827, 0x000000000101000000feffff35ff0253c00c4334f157a0fd3f, 'Parque Principal José Hilario López, Cl 6 #480, Pitalito, Huila, Colombia', 1.847713, -76.054777, 0x000000000101000000e086c278810353c08dfdb4743b90fd3f, 'a 3-235, Cl. 7 Sur #3-1, Pitalito, Huila, Colombia', 'terminado', '2026-04-23 01:41:44', '2026-04-23 01:42:05', NULL, '2026-04-23 01:42:05', NULL, '2026-04-22 21:42:21', NULL, '2026-04-22 21:42:31', NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-23 01:42:31'),
(342, 50, NULL, NULL, 1.841281, -76.043982, 0x0000000001010000004208c897d00253c026c86361e375fd3f, 'Cl. 2c Sur #805, Pitalito, Huila, Colombia', 1.848560, -76.043330, 0x0000000001010000000cbbdeecc50253c09e5c5320b393fd3f, 'Cra. 1A Bis #5-40, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-23 16:22:58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-23 16:23:20', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-23 20:23:20'),
(343, 52, NULL, NULL, 1.841291, -76.043969, 0x000000000101000000d7828362d00253c02dc25e72ed75fd3f, 'Cl. 2c Sur #805, Pitalito, Huila, Colombia', 1.847182, -76.050679, 0x000000000101000000b626fd533e0353c0f4a75ed40e8efd3f, '#3-1 a, Cra. 3, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-23 16:24:08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-23 16:24:40', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-23 20:24:40'),
(344, 52, NULL, NULL, 1.841291, -76.043969, 0x000000000101000000d7828362d00253c02dc25e72ed75fd3f, 'Cl. 2c Sur #805, Pitalito, Huila, Colombia', 1.847182, -76.050679, 0x000000000101000000b626fd533e0353c0f4a75ed40e8efd3f, '#3-1 a, Cra. 3, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-23 16:25:28', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-23 16:25:55', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-23 20:25:55'),
(345, 52, NULL, NULL, 1.841291, -76.043969, 0x000000000101000000d7828362d00253c02dc25e72ed75fd3f, 'Cl. 2c Sur #805, Pitalito, Huila, Colombia', 1.847182, -76.050679, 0x000000000101000000b626fd533e0353c0f4a75ed40e8efd3f, '#3-1 a, Cra. 3, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-23 16:27:03', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-23 16:27:35', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-23 20:27:35'),
(346, 52, 28, 31, 1.846405, -76.048815, 0x0000000001010000007923f3c81f0353c0edbb22f8df8afd3f, 'Cra. 2 # 2 SUR-46, Pitalito, Huila, Colombia', 1.864450, -76.047192, 0x000000000101000000fdffff2f050353c0de833361c9d4fd3f, 'Cra. 15 #15-109, Pasto, Pitalito, Nariño, Colombia', 'terminado', '2026-04-25 00:29:26', '2026-04-25 00:29:36', NULL, '2026-04-25 00:29:36', NULL, '2026-04-24 20:30:31', NULL, '2026-04-24 20:33:24', NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-25 00:33:24'),
(347, 52, 28, 31, 1.852592, -76.045590, 0x00000000010100000094c151f2ea0253c0ea74c53137a4fd3f, 'Cra. 4 # 7-29, Pitalito, Huila, Colombia', 1.854070, -76.066911, 0x00000000010100000021000046480453c0b168e51c45aafd3f, 'VW3M+J6 Pitalito, Huila, Colombia', 'terminado', '2026-04-25 01:37:46', '2026-04-27 00:33:17', NULL, '2026-04-27 00:33:17', NULL, '2026-04-26 20:33:29', NULL, '2026-04-26 20:33:42', NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-27 00:33:42'),
(348, 52, 28, 31, 1.852592, -76.045590, 0x00000000010100000094c151f2ea0253c0ea74c53137a4fd3f, 'Cra. 4 # 7-29, Pitalito, Huila, Colombia', 1.854070, -76.066911, 0x00000000010100000021000046480453c0b168e51c45aafd3f, 'VW3M+J6 Pitalito, Huila, Colombia', 'terminado', '2026-04-25 01:38:27', '2026-04-27 00:31:43', NULL, '2026-04-27 00:31:43', NULL, '2026-04-26 20:32:00', NULL, '2026-04-26 20:33:08', NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-27 00:33:08'),
(349, 52, NULL, NULL, 1.854300, -76.040823, 0x000000000101000000ce5bd0d69c0253c0025b6fe536abfd3f, 'Cl. 17 # 13-23, Pitalito, Huila, Colombia', 1.844741, -76.047513, 0x0000000001010000000c0000760a0353c0c0700eb60e84fd3f, 'Cl. 3 Sur #1-34, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-27 00:29:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27 00:32:24', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-27 04:32:24'),
(350, 52, NULL, NULL, 1.841413, -76.043417, 0x000000000101000000ff9f6f57c70253c07a9b2ca66d76fd3f, 'Cl. 2 Sur # 8E-45, Pitalito, Huila, Colombia', 1.844741, -76.047513, 0x0000000001010000000c0000760a0353c0c0700eb60e84fd3f, 'Cl. 3 Sur #1-34, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-27 01:17:03', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27 01:18:04', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-27 05:18:04'),
(351, 52, 28, 31, 1.841413, -76.043417, 0x000000000101000000ff9f6f57c70253c07a9b2ca66d76fd3f, 'Cl. 2 Sur # 8E-45, Pitalito, Huila, Colombia', 1.844741, -76.047513, 0x0000000001010000000c0000760a0353c0c0700eb60e84fd3f, 'Cl. 3 Sur #1-34, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-04-27 01:18:16', '2026-04-27 01:18:24', NULL, '2026-04-27 01:18:24', NULL, '2026-04-26 21:18:32', NULL, NULL, '2026-04-27 01:18:54', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-04-27 05:18:54'),
(352, 52, 28, 31, 1.841295, -76.043933, 0x000000000101000000fb2df0cbcf0253c0c0b74489f175fd3f, 'Cl. 2c Sur # 8E-15, Pitalito, Huila, Colombia', 1.856836, -76.065538, 0x000000000101000000060000c6310453c0856150179ab5fd3f, 'VW4M+PQ Pitalito, Huila, Colombia', 'terminado', '2026-05-01 01:04:09', '2026-05-01 01:04:29', NULL, '2026-05-01 01:04:29', NULL, '2026-04-30 21:04:40', NULL, '2026-04-30 21:04:52', NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-01 01:04:52'),
(353, 52, 28, 31, 1.841065, -76.048272, 0x000000000101000000a1342fe2160353c0bef6cc920075fd3f, 'Dg 3 Sur # 3E-42, Pitalito, Huila, Colombia', 1.851646, -76.046827, 0x000000000101000000feffff35ff0253c00c4334f157a0fd3f, 'Parque Principal José Hilario López, Cl 6 #480, Pitalito, Huila, Colombia', 'terminado', '2026-05-03 21:27:15', '2026-05-03 21:27:29', NULL, '2026-05-03 21:27:29', NULL, '2026-05-03 17:27:53', NULL, '2026-05-03 17:30:29', NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-03 21:30:29'),
(354, 52, NULL, NULL, 1.841263, -76.043976, 0x0000000001010000006654747fd00253c0b9837de0cf75fd3f, 'Cl. 2c Sur #805, Pitalito, Huila, Colombia', 1.851646, -76.046827, 0x000000000101000000feffff35ff0253c00c4334f157a0fd3f, 'Parque Principal José Hilario López, Cl 6 #480, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-03 22:00:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-03 22:02:07', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-04 02:02:07'),
(355, 52, NULL, NULL, 1.841263, -76.043976, 0x0000000001010000006654747fd00253c0b9837de0cf75fd3f, 'Cl. 2c Sur #805, Pitalito, Huila, Colombia', 1.851646, -76.046827, 0x000000000101000000feffff35ff0253c00c4334f157a0fd3f, 'Parque Principal José Hilario López, Cl 6 #480, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-03 22:02:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-03 22:02:45', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-04 02:02:45'),
(356, 52, NULL, NULL, 1.841263, -76.043976, 0x0000000001010000006654747fd00253c0b9837de0cf75fd3f, 'Cl. 2c Sur #805, Pitalito, Huila, Colombia', 1.851646, -76.046827, 0x000000000101000000feffff35ff0253c00c4334f157a0fd3f, 'Parque Principal José Hilario López, Cl 6 #480, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-03 22:03:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 00:06:05', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-04 04:06:05'),
(357, 52, NULL, NULL, 1.842896, -76.045572, 0x000000000101000000fcffffa4ea0253c089f587b6807cfd3f, 'Cl. 3 Sur #4e-84, Pitalito, Huila, Colombia', 1.834318, -76.056461, 0x000000000101000000220000109d0353c0f68fb4585d59fd3f, 'RWPV+2V, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-04 00:15:11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 00:16:06', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-04 04:16:06'),
(358, 52, NULL, NULL, 1.851646, -76.046848, 0x000000000101000000f6ffff8fff0253c00c4334f157a0fd3f, 'Parque Principal José Hilario López, Cl 6 #480, Pitalito, Huila, Colombia', 1.839379, -76.048222, 0x00000000010100000010000010160353c0588e339e186efd3f, 'Cl. 3A Sur # 7E-1, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-04 00:52:12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 01:00:13', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-04 05:00:13'),
(359, 54, NULL, NULL, 1.846164, -76.060766, 0x000000000101000000f16cea97e30353c068ffa8f2e289fd3f, 'Tv. 3A Sur # 13 SUR-29, Pitalito, Huila, Colombia', 1.859797, -76.048912, 0x000000000101000000a7b8605f210353c0ee563b2fbac1fd3f, 'CAI Cálamo, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-04 01:19:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 01:20:08', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-04 05:20:08'),
(360, 54, 29, 32, 1.846164, -76.060766, 0x000000000101000000f16cea97e30353c068ffa8f2e289fd3f, 'Tv. 3A Sur # 13 SUR-29, Pitalito, Huila, Colombia', 1.859797, -76.048912, 0x000000000101000000a7b8605f210353c0ee563b2fbac1fd3f, 'CAI Cálamo, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-04 01:20:17', '2026-05-04 01:20:40', NULL, '2026-05-04 01:20:40', NULL, '2026-05-03 21:20:58', NULL, NULL, '2026-05-04 01:21:32', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-04 05:21:32'),
(361, 54, 29, 32, 1.846164, -76.060766, 0x000000000101000000f16cea97e30353c068ffa8f2e289fd3f, 'Tv. 3A Sur # 13 SUR-29, Pitalito, Huila, Colombia', 1.859797, -76.048912, 0x000000000101000000a7b8605f210353c0ee563b2fbac1fd3f, 'CAI Cálamo, Pitalito, Huila, Colombia', 'terminado', '2026-05-04 01:22:30', '2026-05-04 01:22:37', NULL, '2026-05-04 01:22:37', NULL, '2026-05-03 21:22:52', NULL, '2026-05-03 21:26:08', NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-04 01:26:08');
INSERT INTO `viajes` (`id`, `pasajero_id`, `conductor_id`, `vehiculo_id`, `origen_lat`, `origen_lng`, `origen_ubicacion`, `origen_texto`, `destino_lat`, `destino_lng`, `destino_ubicacion`, `destino_texto`, `estado`, `created_at`, `asignado_at`, `aceptar_hasta`, `aceptado_at`, `en_camino_at`, `llego_at`, `iniciado_at`, `terminado_at`, `cancelado_at`, `cancelado_por`, `cancelacion_motivo`, `metodo_asignacion`, `radio_busqueda_m`, `eta_min_estimada`, `distancia_km_estimada`, `duracion_min_estimada`, `distancia_km_real`, `duracion_min_real`, `tarifa_id`, `moneda`, `tarifa_aplicada`, `valor_pagado`, `pago_registrado`, `updated_at`) VALUES
(362, 54, 29, 32, 1.846454, -76.060041, 0x000000000101000000d3da34b6d70353c04ffd288f138bfd3f, 'Tv. 3 # 11-141, Pitalito, Huila, Colombia', 1.856418, -76.046393, 0x000000000101000000e6913f18f80253c01415bbc7e3b3fd3f, 'Pitalito, Huila, Colombia', 'terminado', '2026-05-04 01:28:22', '2026-05-04 01:28:31', NULL, '2026-05-04 01:28:31', NULL, '2026-05-03 21:28:39', NULL, '2026-05-03 21:29:46', NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-04 01:29:46'),
(363, 54, 29, 32, 1.846404, -76.059889, 0x000000000101000000c3781739d50353c0b2193b3cdf8afd3f, 'Tv. 3 # 11-141, Pitalito, Huila, Colombia', 1.856418, -76.046393, 0x000000000101000000e6913f18f80253c01415bbc7e3b3fd3f, 'Pitalito, Huila, Colombia', 'terminado', '2026-05-04 01:36:43', '2026-05-04 01:36:53', NULL, '2026-05-04 01:36:53', NULL, '2026-05-03 21:37:16', NULL, '2026-05-03 21:37:42', NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-04 01:37:42'),
(364, 52, 28, 31, 1.861130, -76.049597, 0x000000000101000000541fa3972c0353c0f3ab394030c7fd3f, 'Cl. 10 # 14-31, Pitalito, Huila, Colombia', 1.851646, -76.046827, 0x000000000101000000feffff35ff0253c00c4334f157a0fd3f, 'Parque Principal José Hilario López, Cl 6 #480, Pitalito, Huila, Colombia', 'terminado', '2026-05-05 11:33:57', '2026-05-05 11:34:13', NULL, '2026-05-05 11:34:13', NULL, '2026-05-05 07:34:42', NULL, '2026-05-05 08:28:10', NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-05 12:28:10'),
(365, 45, NULL, NULL, 1.863601, -76.046151, 0x000000000101000000499ff724f40253c0e3426b394fd1fd3f, 'Cra. 14A # 16A-11, Pitalito, Huila, Colombia', 1.838627, -76.042223, 0x000000000101000000335f4cc9b30253c0f33672a4036bfd3f, 'Carrera 10Ae Bis # 3A SUR-19, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-06 01:51:56', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-06 01:52:22', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-06 05:52:22'),
(366, 45, NULL, NULL, 1.863601, -76.046151, 0x000000000101000000499ff724f40253c0e3426b394fd1fd3f, 'Cra. 14A # 16A-11, Pitalito, Huila, Colombia', 1.833190, -76.042270, 0x000000000101000000515f108cb40253c0e04755f9be54fd3f, 'RXM5+73 Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-06 01:52:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-06 01:52:40', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-06 05:52:40'),
(367, 45, NULL, NULL, 1.851182, -76.042156, 0x000000000101000000649fceadb20253c0d56c9d1e719efd3f, 'Cl. 9 # 1-92, Pitalito, Huila, Colombia', 1.833190, -76.042270, 0x000000000101000000515f108cb40253c0e04755f9be54fd3f, 'RXM5+73 Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-06 01:52:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-06 01:52:54', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-06 05:52:54'),
(368, 45, NULL, NULL, 1.846868, -76.041659, 0x000000000101000000361fc788aa0253c052dd1038c58cfd3f, 'Cl. 5a # 4-85, Pitalito, Huila, Colombia', 1.833190, -76.042270, 0x000000000101000000515f108cb40253c0e04755f9be54fd3f, 'RXM5+73 Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-06 01:53:01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-06 01:53:09', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-06 05:53:09'),
(369, 52, NULL, NULL, 1.843714, -76.036809, 0x000000000101000000110c4b155b0253c0abdfbef1d97ffd3f, 'Cl 6 # 11AE-23, Pitalito, Huila, Colombia', 1.844741, -76.047513, 0x0000000001010000000c0000760a0353c0c0700eb60e84fd3f, 'Cl. 3 Sur #1-34, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-06 01:58:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-06 01:59:57', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-06 05:59:57'),
(370, 52, 28, 31, 1.852772, -76.045310, 0x000000000101000000a20bea5be60253c0e84427f0f3a4fd3f, 'Cra. 4 #777, Pitalito, Huila, Colombia', 1.848580, -76.043351, 0x00000000010100000014000042c60253c0e19d951ec893fd3f, 'Cra. 1A Bis #5-40, Pitalito, Huila, Colombia', 'asignado', '2026-05-10 14:05:27', '2026-05-10 14:05:51', NULL, '2026-05-10 14:05:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-10 18:05:51'),
(371, 52, NULL, NULL, 1.852694, -76.045289, 0x000000000101000000c0f68205e60253c0adb717e2a2a4fd3f, 'Cra. 4 # 7-60, Pitalito, Huila, Colombia', 1.859045, -76.054509, 0x000000000101000000170000127d0353c069ed6a5fa6befd3f, 'Cl 5 # 16A-23, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-12 14:46:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-12 14:47:01', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-12 18:47:01'),
(372, 52, 28, 31, 1.852694, -76.045289, 0x000000000101000000c0f68205e60253c0adb717e2a2a4fd3f, 'Cra. 4 # 7-60, Pitalito, Huila, Colombia', 1.859045, -76.054509, 0x000000000101000000170000127d0353c069ed6a5fa6befd3f, 'Cl 5 # 16A-23, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-12 14:47:16', '2026-05-12 14:47:25', NULL, '2026-05-12 14:47:25', NULL, '2026-05-12 10:47:38', NULL, NULL, '2026-05-12 14:49:06', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-12 18:49:06'),
(373, 52, 28, 31, 1.852694, -76.045289, 0x000000000101000000c0f68205e60253c0adb717e2a2a4fd3f, 'Cra. 4 # 7-6hospital San Antonio0, Pitalito, Huila, Colombia', 1.830457, -76.043329, 0x0000000001010000001c0000e8c50253c0d3cbe06a8d49fd3f, 'Km 1 vía Vereda El Macal, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-12 15:19:49', '2026-05-12 15:20:03', NULL, '2026-05-12 15:20:03', NULL, '2026-05-12 11:20:11', NULL, NULL, '2026-05-12 15:20:22', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-12 19:20:22'),
(374, 52, NULL, NULL, 1.849575, -76.065965, 0x000000000101000000906b43c5380453c0b9fc87f4db97fd3f, 'Cra. 15 #19A 31, Pitalito, Huila, Colombia', 1.847464, -76.070559, 0x0000000001010000000f00000a840453c0a3b75eba368ffd3f, 'Cl. 25 Sur #2-23, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-15 12:03:58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-15 13:54:31', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-15 17:54:31'),
(375, 52, NULL, NULL, 1.849575, -76.065965, 0x000000000101000000906b43c5380453c0b9fc87f4db97fd3f, 'Cra. 15 #19A 31, Pitalito, Huila, Colombia', 1.847464, -76.070559, 0x0000000001010000000f00000a840453c0a3b75eba368ffd3f, 'Cl. 25 Sur #2-23, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-15 13:55:03', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-15 13:55:05', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-15 17:55:05'),
(376, 52, NULL, NULL, 1.845188, -76.048912, 0x00000000010100000013f9895e210353c0af63b72ae485fd3f, 'Cl. 3 Sur #101, Pitalito, Huila, Colombia', 1.843411, -76.036828, 0x000000000101000000230000625b0253c02f25026f9c7efd3f, 'Cl 6 # 11AE-45, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-18 15:24:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-18 15:24:58', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-18 19:24:58'),
(377, 52, 28, 31, 1.841289, -76.043949, 0x0000000001010000007e0c0c0fd00253c08198dfc4eb75fd3f, 'Cl. 2c Sur # 8E-15, Pitalito, Huila, Colombia', 1.852654, -76.045464, 0x000000000101000000e0ffffe2e80253c0e8a9c4e378a4fd3f, 'Cra. 4 #7- 39, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-24 12:44:28', '2026-05-24 12:44:46', NULL, '2026-05-24 12:44:46', NULL, '2026-05-24 08:45:17', NULL, NULL, '2026-05-24 21:50:02', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-25 01:50:02'),
(378, 52, NULL, NULL, 1.845062, -76.062810, 0x00000000010100000027f73b14050453c076a2c96a5f85fd3f, 'Cra. 2A Este # 17-14, Pitalito, Huila, Colombia', 1.850231, -76.064658, 0x000000000101000000feffff5b230453c0ca798eb78b9afd3f, 'Carrera 15 # 19A 01 Sur Administración, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-24 21:51:39', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-24 21:52:00', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-25 01:52:00'),
(379, 52, 28, 31, 1.845062, -76.062810, 0x00000000010100000027f73b14050453c076a2c96a5f85fd3f, 'Cra. 2A Este # 17-14, Pitalito, Huila, Colombia', 1.850231, -76.064658, 0x000000000101000000feffff5b230453c0ca798eb78b9afd3f, 'Carrera 15 # 19A 01 Sur Administración, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-24 21:52:06', '2026-05-24 21:52:29', NULL, '2026-05-24 21:52:29', NULL, NULL, NULL, NULL, '2026-05-24 21:52:53', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-25 01:52:53'),
(380, 52, NULL, NULL, 1.841438, -76.048415, 0x0000000001010000000b00003a190353c0d543f6808776fd3f, 'Dg 3 Sur #2-27 Este, Pitalito, Huila, Colombia', 1.836913, -76.044188, 0x000000000101000000e6fffff7d30253c0750e0e73fe63fd3f, 'calle 3 g sur 10_03este la predera, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-27 13:38:39', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-27 13:38:48', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-27 17:38:48'),
(381, 52, NULL, NULL, 1.841438, -76.048415, 0x0000000001010000000b00003a190353c0d543f6808776fd3f, 'Dg 3 Sur #2-27 Este, Pitalito, Huila, Colombia', 1.836913, -76.044188, 0x000000000101000000e6fffff7d30253c0750e0e73fe63fd3f, 'calle 3 g sur 10_03este la predera, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-27 14:04:13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-27 14:04:53', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-27 18:04:53'),
(382, 52, NULL, NULL, 1.841438, -76.048415, 0x0000000001010000000b00003a190353c0d543f6808776fd3f, 'Dg 3 Sur #2-27 Este, Pitalito, Huila, Colombia', 1.836913, -76.044188, 0x000000000101000000e6fffff7d30253c0750e0e73fe63fd3f, 'calle 3 g sur 10_03este la predera, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-27 14:05:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-27 14:05:43', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-27 18:05:43'),
(383, 52, NULL, NULL, 1.841438, -76.048415, 0x0000000001010000000b00003a190353c0d543f6808776fd3f, 'Dg 3 Sur #2-27 Este, Pitalito, Huila, Colombia', 1.836913, -76.044188, 0x000000000101000000e6fffff7d30253c0750e0e73fe63fd3f, 'calle 3 g sur 10_03este la predera, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-27 14:05:59', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-27 14:06:42', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-27 18:06:42'),
(384, 52, NULL, NULL, 1.841438, -76.048415, 0x0000000001010000000b00003a190353c0d543f6808776fd3f, 'Dg 3 Sur #2-27 Este, Pitalito, Huila, Colombia', 1.836913, -76.044188, 0x000000000101000000e6fffff7d30253c0750e0e73fe63fd3f, 'calle 3 g sur 10_03este la predera, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-27 14:06:56', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-27 14:14:26', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-27 18:14:26'),
(385, 52, NULL, NULL, 1.841438, -76.048415, 0x0000000001010000000b00003a190353c0d543f6808776fd3f, 'Dg 3 Sur #2-27 Este, Pitalito, Huila, Colombia', 1.836913, -76.044188, 0x000000000101000000e6fffff7d30253c0750e0e73fe63fd3f, 'calle 3 g sur 10_03este la predera, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-27 14:14:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-27 14:14:54', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-27 18:14:54'),
(386, 52, NULL, NULL, 1.841438, -76.048415, 0x0000000001010000000b00003a190353c0d543f6808776fd3f, 'Dg 3 Sur #2-27 galeria municipalEste, Pitalito, Huila, Colombia', 1.836913, -76.044188, 0x000000000101000000e6fffff7d30253c0750e0e73fe63fd3f, 'calle 3 g sur 10_03este la predera, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-27 14:15:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-27 14:16:01', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-27 18:16:01'),
(387, 52, NULL, NULL, 1.841438, -76.048415, 0x0000000001010000000b00003a190353c0d543f6808776fd3f, 'Dg 3 Sur #2-27 galeria municipalEste, Pitalito, Huila, Colombia', 1.836913, -76.044188, 0x000000000101000000e6fffff7d30253c0750e0e73fe63fd3f, 'calle 3 g sur 10_03este la predera, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-27 14:16:24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-27 14:16:36', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-27 18:16:36'),
(388, 52, NULL, NULL, 1.841438, -76.048415, 0x0000000001010000000b00003a190353c0d543f6808776fd3f, 'Dg 3 Sur #2-27 galeria municipalEste, Pitalito, Huila, Colombia', 1.835857, -76.047087, 0x000000000101000000c4af107a030353c06df845eeab5ffd3f, 'RXP3+85 Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-27 14:17:03', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-27 14:17:18', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-27 18:17:18'),
(389, 52, NULL, NULL, 1.841142, -76.046200, 0x000000000101000000e26329eff40253c0aea40ae25075fd3f, 'Cl. 3d Sur # 5AE-11, Pitalito, Huila, Colombia', 1.835857, -76.047087, 0x000000000101000000c4af107a030353c06df845eeab5ffd3f, 'RXP3+85 Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-27 14:17:58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-27 14:18:10', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-27 18:18:10'),
(390, 52, NULL, NULL, 1.841142, -76.046200, 0x000000000101000000e26329eff40253c0aea40ae25075fd3f, 'Cl. 3d Sur # 5AE-11, Pitalito, Huila, Colombia', 1.835857, -76.047087, 0x000000000101000000c4af107a030353c06df845eeab5ffd3f, 'RXP3+85 Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-27 14:18:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-27 14:18:53', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-27 18:18:53'),
(391, 52, 28, 31, 1.860743, -76.049497, 0x000000000101000000a3f034f42a0353c093043cc49ac5fd3f, 'Cl. 10 # 14-23, Pitalito, Huila, Colombia', 1.855421, -76.041634, 0x000000000101000000f3ffff21aa0253c085bbc7decdaffd3f, 'Cl. 13A #3-02, Pitalito, Huila, Colombia', 'cancelado_pasajero', '2026-05-27 14:20:48', '2026-05-27 14:21:12', NULL, '2026-05-27 14:21:12', NULL, '2026-05-27 10:41:15', NULL, NULL, '2026-05-27 14:41:42', 'pasajero', 'cancelado_pasajero', 'auto', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'COP', 12000.00, NULL, 0, '2026-05-27 18:41:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `viaje_estados_log`
--

CREATE TABLE `viaje_estados_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `viaje_id` bigint(20) UNSIGNED NOT NULL,
  `from_estado` enum('buscando','asignado','en_camino','llego','iniciado','terminado','cancelado_pasajero','cancelado_conductor','no_show','fallo_localizacion') DEFAULT NULL,
  `to_estado` enum('buscando','asignado','en_camino','llego','iniciado','terminado','cancelado_pasajero','cancelado_conductor','no_show','fallo_localizacion') NOT NULL,
  `actor_tipo` enum('pasajero','conductor','sistema','admin') NOT NULL DEFAULT 'sistema',
  `actor_id` int(25) DEFAULT NULL,
  `motivo_codigo` enum('flujo','aceptado','arribo','inicio','fin','cancelado_pasajero','cancelado_conductor','no_show','timeout_aceptar','reasignacion','fallo_localizacion') DEFAULT 'flujo',
  `motivo_texto` varchar(255) DEFAULT NULL,
  `app_origen` enum('api','panel','app_pasajero','app_conductor','sistema') DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `viaje_estados_log`
--

INSERT INTO `viaje_estados_log` (`id`, `viaje_id`, `from_estado`, `to_estado`, `actor_tipo`, `actor_id`, `motivo_codigo`, `motivo_texto`, `app_origen`, `ip`, `created_at`) VALUES
(593, 267, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '186.102.114.249', '2026-04-16 01:57:23'),
(594, 267, 'buscando', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '186.102.114.249', '2026-04-16 01:57:40'),
(595, 268, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '186.102.114.249', '2026-04-16 02:01:37'),
(596, 268, 'buscando', 'buscando', 'conductor', 44, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '186.102.114.249', '2026-04-16 02:01:59'),
(597, 268, 'buscando', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '186.102.114.249', '2026-04-16 02:05:35'),
(598, 269, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-16 18:35:55'),
(599, 269, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-16 18:36:14'),
(600, 269, 'asignado', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-16 18:37:42'),
(601, 270, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-16 18:48:47'),
(602, 270, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-16 18:48:54'),
(603, 270, 'asignado', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-16 18:49:10'),
(604, 271, NULL, 'buscando', 'pasajero', 5, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '152.205.4.58', '2026-04-18 00:33:52'),
(605, 271, 'buscando', 'cancelado_pasajero', 'pasajero', 5, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '152.205.4.58', '2026-04-18 00:34:07'),
(606, 272, NULL, 'buscando', 'pasajero', 5, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '152.205.4.58', '2026-04-18 00:34:21'),
(607, 272, 'buscando', 'cancelado_pasajero', 'pasajero', 5, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '152.205.4.58', '2026-04-18 00:34:30'),
(608, 273, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 01:14:33'),
(609, 273, 'buscando', 'buscando', 'conductor', 47, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-04-18 01:14:50'),
(610, 273, 'buscando', 'buscando', 'conductor', 44, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-04-18 01:14:56'),
(611, 273, 'buscando', 'buscando', 'conductor', 46, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-04-18 01:16:06'),
(612, 273, 'buscando', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 01:16:14'),
(613, 274, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 01:16:20'),
(614, 274, 'buscando', 'buscando', 'conductor', 47, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-04-18 01:16:41'),
(615, 274, 'buscando', 'buscando', 'conductor', 44, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-04-18 01:16:42'),
(616, 274, 'buscando', 'buscando', 'conductor', 46, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-04-18 01:16:44'),
(617, 274, 'buscando', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 01:17:05'),
(618, 275, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 01:17:11'),
(619, 275, 'buscando', 'buscando', 'conductor', 47, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-04-18 01:17:28'),
(620, 275, 'buscando', 'asignado', 'conductor', 44, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-18 01:17:30'),
(621, 275, 'llego', 'iniciado', 'pasajero', 45, 'inicio', 'Pasajero confirmó que abordó', 'app_pasajero', '181.174.230.42', '2026-04-18 01:18:42'),
(622, 275, 'iniciado', 'terminado', 'conductor', 44, 'fin', 'Viaje finalizado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-18 01:19:34'),
(623, 276, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 01:20:35'),
(624, 276, 'buscando', 'buscando', 'conductor', 46, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-04-18 01:20:39'),
(625, 276, 'buscando', 'buscando', 'conductor', 44, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-04-18 01:20:41'),
(626, 276, 'buscando', 'asignado', 'conductor', 47, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-18 01:20:50'),
(627, 276, 'asignado', 'iniciado', 'pasajero', 45, 'inicio', 'Pasajero confirmó que abordó', 'app_pasajero', '181.174.230.42', '2026-04-18 01:21:33'),
(628, 276, 'iniciado', 'terminado', 'conductor', 47, 'fin', 'Viaje finalizado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-18 01:21:59'),
(629, 277, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 01:22:41'),
(630, 277, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-18 01:22:47'),
(631, 277, 'asignado', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 01:22:49'),
(632, 278, NULL, 'buscando', 'pasajero', 5, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '152.205.4.58', '2026-04-18 01:23:27'),
(633, 278, 'buscando', 'cancelado_pasajero', 'pasajero', 5, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '152.205.4.58', '2026-04-18 01:23:30'),
(634, 279, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 01:23:41'),
(635, 279, 'buscando', 'buscando', 'conductor', 46, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-04-18 01:24:01'),
(636, 279, 'buscando', 'buscando', 'conductor', 47, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-04-18 01:24:03'),
(637, 279, 'buscando', 'buscando', 'conductor', 44, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-04-18 01:24:11'),
(638, 279, 'buscando', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 01:24:21'),
(639, 280, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 01:24:32'),
(640, 280, 'buscando', 'buscando', 'conductor', 47, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-04-18 01:24:53'),
(641, 280, 'buscando', 'buscando', 'conductor', 44, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-04-18 01:24:53'),
(642, 280, 'buscando', 'buscando', 'conductor', 46, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-04-18 01:24:54'),
(643, 280, 'buscando', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 01:31:01'),
(644, 281, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 01:31:18'),
(645, 281, 'buscando', 'asignado', 'conductor', 44, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-18 01:31:19'),
(646, 281, 'asignado', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 01:31:33'),
(647, 282, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 01:31:55'),
(648, 282, 'buscando', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 01:32:04'),
(649, 283, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 01:32:31'),
(650, 283, 'buscando', 'asignado', 'conductor', 44, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-18 01:32:38'),
(651, 283, 'asignado', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 01:32:56'),
(652, 284, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 01:33:05'),
(653, 284, 'buscando', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 01:33:14'),
(654, 285, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 01:36:31'),
(655, 286, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 01:37:14'),
(656, 286, 'buscando', 'asignado', 'conductor', 47, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-18 01:37:21'),
(657, 286, 'asignado', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 01:37:42'),
(658, 287, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 01:39:47'),
(659, 287, 'buscando', 'asignado', 'conductor', 44, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-18 01:40:04'),
(660, 287, 'asignado', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 01:40:16'),
(661, 288, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 01:41:44'),
(662, 288, 'buscando', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 01:41:56'),
(663, 289, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 01:42:42'),
(664, 289, 'buscando', 'asignado', 'conductor', 47, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-18 01:42:56'),
(665, 289, 'asignado', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 01:42:56'),
(666, 290, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 01:43:01'),
(667, 290, 'buscando', 'asignado', 'conductor', 47, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-18 01:43:06'),
(668, 290, 'asignado', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 01:43:51'),
(669, 291, NULL, 'buscando', 'pasajero', 5, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '152.205.4.58', '2026-04-18 01:47:22'),
(670, 291, 'buscando', 'asignado', 'conductor', 44, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-18 01:47:23'),
(671, 291, 'llego', 'cancelado_pasajero', 'pasajero', 5, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '152.205.4.58', '2026-04-18 01:48:38'),
(672, 292, NULL, 'buscando', 'pasajero', 5, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '152.205.4.58', '2026-04-18 01:48:45'),
(673, 292, 'buscando', 'cancelado_pasajero', 'pasajero', 5, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '152.205.4.58', '2026-04-18 01:48:53'),
(674, 293, NULL, 'buscando', 'pasajero', 5, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '152.205.4.58', '2026-04-18 01:49:15'),
(675, 294, NULL, 'buscando', 'pasajero', 5, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '152.205.4.58', '2026-04-18 01:50:12'),
(676, 293, 'buscando', 'buscando', 'conductor', 44, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-04-18 01:50:29'),
(677, 293, 'buscando', 'asignado', 'conductor', 47, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-18 01:50:33'),
(678, 294, 'buscando', 'buscando', 'conductor', 44, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-04-18 01:50:37'),
(679, 294, 'buscando', 'cancelado_pasajero', 'pasajero', 5, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '152.205.4.58', '2026-04-18 01:51:09'),
(680, 293, 'llego', 'terminado', 'conductor', 47, 'fin', 'Viaje finalizado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-18 01:52:23'),
(681, 295, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 01:52:51'),
(682, 295, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-18 01:52:57'),
(683, 295, 'llego', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 01:53:16'),
(684, 296, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 01:53:35'),
(685, 296, 'buscando', 'asignado', 'conductor', 44, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-18 01:53:41'),
(686, 296, 'llego', 'terminado', 'conductor', 44, 'fin', 'Viaje finalizado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-18 01:53:58'),
(687, 297, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 02:39:21'),
(688, 297, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-18 02:39:26'),
(689, 297, 'asignado', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 02:39:33'),
(690, 298, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 02:39:48'),
(691, 298, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-18 02:39:51'),
(692, 298, 'asignado', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 02:39:59'),
(693, 299, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 02:41:04'),
(694, 299, 'buscando', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 02:41:10'),
(695, 300, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 02:41:34'),
(696, 300, 'buscando', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 02:41:41'),
(697, 285, 'buscando', 'buscando', 'conductor', 47, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '186.102.121.36', '2026-04-18 02:41:47'),
(698, 301, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 02:41:50'),
(699, 301, 'buscando', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 02:41:55'),
(700, 302, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 02:42:23'),
(701, 302, 'buscando', 'buscando', 'conductor', 44, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-04-18 02:42:46'),
(702, 302, 'buscando', 'buscando', 'conductor', 46, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-04-18 02:42:49'),
(703, 302, 'buscando', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 02:43:52'),
(704, 303, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 02:44:11'),
(705, 303, 'buscando', 'buscando', 'conductor', 46, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-04-18 02:44:19'),
(706, 303, 'buscando', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 02:44:20'),
(707, 304, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 02:44:36'),
(708, 304, 'buscando', 'asignado', 'conductor', 47, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '186.102.121.36', '2026-04-18 02:44:45'),
(709, 304, 'llego', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 02:45:02'),
(710, 305, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 02:45:15'),
(711, 305, 'buscando', 'buscando', 'conductor', 47, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '186.102.121.36', '2026-04-18 02:45:39'),
(712, 305, 'buscando', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 02:47:48'),
(713, 306, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 02:48:00'),
(714, 306, 'buscando', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 02:48:06'),
(715, 307, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 02:48:29'),
(716, 307, 'buscando', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 02:48:33'),
(717, 308, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 02:48:40'),
(718, 308, 'buscando', 'asignado', 'conductor', 47, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '186.102.121.36', '2026-04-18 02:48:58'),
(719, 308, 'llego', 'terminado', 'conductor', 47, 'fin', 'Viaje finalizado por el conductor', 'app_conductor', '186.102.121.36', '2026-04-18 02:49:24'),
(720, 309, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 02:51:50'),
(721, 309, 'buscando', 'asignado', 'conductor', 47, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '186.102.121.36', '2026-04-18 02:52:04'),
(722, 309, 'llego', 'terminado', 'conductor', 47, 'fin', 'Viaje finalizado por el conductor', 'app_conductor', '186.102.121.36', '2026-04-18 02:52:19'),
(723, 310, NULL, 'buscando', 'pasajero', 49, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 12:38:14'),
(724, 310, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-18 12:38:25'),
(725, 310, 'llego', 'cancelado_pasajero', 'pasajero', 49, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 12:40:39'),
(726, 311, NULL, 'buscando', 'pasajero', 49, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 12:42:22'),
(727, 311, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-18 12:43:02'),
(728, 311, 'asignado', 'iniciado', 'pasajero', 49, 'inicio', 'Pasajero confirmó que abordó', 'app_pasajero', '181.174.230.42', '2026-04-18 12:43:41'),
(729, 311, 'iniciado', 'terminado', 'conductor', 46, 'fin', 'Viaje finalizado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-18 12:44:40'),
(730, 285, 'buscando', 'asignado', 'conductor', 44, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '186.102.72.136', '2026-04-18 13:34:52'),
(731, 285, 'llego', 'terminado', 'conductor', 44, 'fin', 'Viaje finalizado por el conductor', 'app_conductor', '186.102.72.136', '2026-04-18 13:39:31'),
(732, 312, NULL, 'buscando', 'pasajero', 50, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-18 23:09:33'),
(733, 312, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-18 23:09:47'),
(734, 312, 'asignado', 'cancelado_pasajero', 'pasajero', 50, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-18 23:11:08'),
(735, 313, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-19 00:09:18'),
(736, 313, 'buscando', 'buscando', 'conductor', 46, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-04-19 00:10:16'),
(737, 314, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-19 00:10:47'),
(738, 315, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '152.205.6.96', '2026-04-19 00:12:44'),
(739, 313, 'buscando', 'buscando', 'conductor', 44, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '152.205.6.96', '2026-04-19 00:13:11'),
(740, 316, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '152.205.6.96', '2026-04-19 00:13:34'),
(741, 317, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '152.205.6.96', '2026-04-19 00:16:09'),
(742, 318, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '152.205.6.96', '2026-04-19 00:17:54'),
(743, 319, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '152.205.6.96', '2026-04-19 00:18:29'),
(744, 318, 'buscando', 'buscando', 'conductor', 47, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '186.102.94.157', '2026-04-19 00:18:55'),
(745, 317, 'buscando', 'buscando', 'conductor', 47, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '186.102.94.157', '2026-04-19 00:19:19'),
(746, 319, 'buscando', 'buscando', 'conductor', 47, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '186.102.94.157', '2026-04-19 00:19:42'),
(747, 316, 'buscando', 'buscando', 'conductor', 47, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '186.102.94.157', '2026-04-19 00:21:41'),
(748, 315, 'buscando', 'asignado', 'conductor', 47, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '186.102.94.157', '2026-04-19 00:21:49'),
(749, 315, 'llego', 'terminado', 'conductor', 47, 'fin', 'Viaje finalizado por el conductor', 'app_conductor', '186.102.94.157', '2026-04-19 00:21:57'),
(750, 314, 'buscando', 'buscando', 'conductor', 47, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '186.102.94.157', '2026-04-19 00:22:19'),
(751, 319, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-19 03:01:17'),
(752, 316, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-19 03:05:03'),
(753, 316, 'llego', 'terminado', 'conductor', 46, 'fin', 'Viaje finalizado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-19 03:09:15'),
(754, 314, 'buscando', 'buscando', 'conductor', 46, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '152.205.5.6', '2026-04-20 11:59:02'),
(755, 320, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '152.205.5.6', '2026-04-20 14:50:47'),
(756, 321, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '152.205.5.6', '2026-04-20 14:56:54'),
(757, 322, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '152.205.5.6', '2026-04-20 14:58:01'),
(758, 323, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '152.205.5.6', '2026-04-20 15:01:44'),
(759, 323, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '152.205.5.6', '2026-04-20 15:01:47'),
(760, 324, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.59', '2026-04-20 16:54:17'),
(761, 324, 'buscando', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.59', '2026-04-20 16:54:27'),
(762, 325, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '152.205.5.6', '2026-04-20 16:57:32'),
(763, 325, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '152.205.5.6', '2026-04-20 16:57:48'),
(764, 326, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '152.205.5.6', '2026-04-20 17:00:46'),
(765, 326, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '152.205.5.6', '2026-04-20 17:00:53'),
(766, 327, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-20 17:19:48'),
(767, 327, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-20 17:20:00'),
(768, 327, 'llego', 'terminado', 'conductor', 46, 'fin', 'Viaje finalizado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-20 17:20:35'),
(769, 328, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.228.4', '2026-04-20 22:23:07'),
(770, 329, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.228.4', '2026-04-20 22:25:28'),
(771, 329, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.228.4', '2026-04-20 22:25:30'),
(772, 330, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.228.4', '2026-04-20 22:27:22'),
(773, 330, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.228.4', '2026-04-20 22:27:37'),
(774, 331, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.228.4', '2026-04-20 22:41:22'),
(775, 331, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.228.4', '2026-04-20 22:41:35'),
(776, 314, 'buscando', 'asignado', 'conductor', 44, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '186.102.83.20', '2026-04-20 22:46:38'),
(777, 314, 'llego', 'terminado', 'conductor', 44, 'fin', 'Viaje finalizado por el conductor', 'app_conductor', '186.102.83.20', '2026-04-20 22:58:18'),
(778, 318, 'buscando', 'asignado', 'conductor', 44, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '186.102.83.20', '2026-04-20 22:59:44'),
(779, 318, 'llego', 'terminado', 'conductor', 44, 'fin', 'Viaje finalizado por el conductor', 'app_conductor', '186.102.83.20', '2026-04-20 23:02:18'),
(780, 317, 'buscando', 'buscando', 'conductor', 44, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '186.102.83.20', '2026-04-20 23:02:26'),
(781, 332, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '186.102.36.6', '2026-04-21 16:58:04'),
(782, 332, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '186.102.36.6', '2026-04-21 16:58:33'),
(783, 333, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '186.102.36.6', '2026-04-21 16:59:15'),
(784, 333, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '186.102.36.6', '2026-04-21 16:59:17'),
(785, 334, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '186.102.36.6', '2026-04-21 16:59:58'),
(786, 335, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '186.102.4.55', '2026-04-21 17:31:11'),
(787, 335, 'buscando', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '186.102.4.55', '2026-04-21 17:31:23'),
(788, 334, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-21 17:31:41'),
(789, 336, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-21 17:31:58'),
(790, 336, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-21 17:32:07'),
(791, 337, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-21 17:32:29'),
(792, 337, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-21 17:32:37'),
(793, 338, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-21 17:33:49'),
(794, 338, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-21 17:34:06'),
(795, 338, 'llego', 'terminado', 'conductor', 46, 'fin', 'Viaje finalizado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-21 17:52:02'),
(796, 339, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '186.102.60.143', '2026-04-21 19:17:42'),
(797, 339, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '186.102.60.143', '2026-04-21 19:17:53'),
(798, 339, 'llego', 'terminado', 'conductor', 46, 'fin', 'Viaje finalizado por el conductor', 'app_conductor', '186.102.60.143', '2026-04-21 19:18:39'),
(799, 340, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '186.102.105.11', '2026-04-23 01:00:42'),
(800, 340, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '186.102.105.11', '2026-04-23 01:01:01'),
(801, 341, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '186.102.105.11', '2026-04-23 01:41:44'),
(802, 341, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '186.102.105.11', '2026-04-23 01:42:05'),
(803, 341, 'llego', 'terminado', 'conductor', 46, 'fin', 'Viaje finalizado por el conductor', 'app_conductor', '186.102.105.11', '2026-04-23 01:42:31'),
(804, 342, NULL, 'buscando', 'pasajero', 50, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-23 16:22:58'),
(805, 342, 'buscando', 'cancelado_pasajero', 'pasajero', 50, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-23 16:23:20'),
(806, 343, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-23 16:24:08'),
(807, 343, 'buscando', 'buscando', 'conductor', 46, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-04-23 16:24:20'),
(808, 343, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-23 16:24:40'),
(809, 344, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-23 16:25:28'),
(810, 344, 'buscando', 'buscando', 'conductor', 46, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-04-23 16:25:36'),
(811, 344, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-23 16:25:55'),
(812, 345, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-23 16:27:03'),
(813, 345, 'buscando', 'buscando', 'conductor', 46, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-04-23 16:27:11'),
(814, 345, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-23 16:27:35'),
(815, 313, 'buscando', 'buscando', 'conductor', 48, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '186.102.94.86', '2026-04-24 00:10:33'),
(816, 346, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '186.102.22.193', '2026-04-25 00:29:26'),
(817, 346, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '186.102.22.193', '2026-04-25 00:29:36'),
(818, 346, 'llego', 'iniciado', 'pasajero', 52, 'inicio', 'Pasajero confirmó que abordó', 'app_pasajero', '186.102.22.193', '2026-04-25 00:31:47'),
(819, 346, 'iniciado', 'terminado', 'conductor', 46, 'fin', 'Viaje finalizado por el conductor', 'app_conductor', '186.102.22.193', '2026-04-25 00:33:24'),
(820, 317, 'buscando', 'buscando', 'conductor', 46, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '186.102.22.193', '2026-04-25 00:35:24'),
(821, 313, 'buscando', 'asignado', 'conductor', 47, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '186.102.77.224', '2026-04-25 00:38:47'),
(822, 313, 'llego', 'terminado', 'conductor', 47, 'fin', 'Viaje finalizado por el conductor', 'app_conductor', '186.102.77.224', '2026-04-25 00:39:00'),
(823, 347, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '186.102.110.171', '2026-04-25 01:37:46'),
(824, 348, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '186.102.110.171', '2026-04-25 01:38:27'),
(825, 349, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '186.102.5.99', '2026-04-27 00:29:00'),
(826, 349, 'buscando', 'buscando', 'conductor', 46, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '186.102.5.99', '2026-04-27 00:31:32'),
(827, 348, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '186.102.5.99', '2026-04-27 00:31:43'),
(828, 349, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '186.102.5.99', '2026-04-27 00:32:24'),
(829, 348, 'llego', 'terminado', 'conductor', 46, 'fin', 'Viaje finalizado por el conductor', 'app_conductor', '186.102.5.99', '2026-04-27 00:33:08'),
(830, 347, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '186.102.5.99', '2026-04-27 00:33:17'),
(831, 347, 'llego', 'terminado', 'conductor', 46, 'fin', 'Viaje finalizado por el conductor', 'app_conductor', '186.102.5.99', '2026-04-27 00:33:42'),
(832, 350, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-27 01:17:03'),
(833, 350, 'buscando', 'buscando', 'conductor', 46, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-04-27 01:17:49'),
(834, 350, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-27 01:18:04'),
(835, 351, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-04-27 01:18:16'),
(836, 351, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '181.174.230.42', '2026-04-27 01:18:24'),
(837, 351, 'llego', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-04-27 01:18:54'),
(838, 352, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-05-01 01:04:09'),
(839, 352, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '181.174.230.42', '2026-05-01 01:04:29'),
(840, 352, 'llego', 'terminado', 'conductor', 46, 'fin', 'Viaje finalizado por el conductor', 'app_conductor', '181.174.230.42', '2026-05-01 01:04:52'),
(841, 353, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '186.102.101.247', '2026-05-03 21:27:15'),
(842, 353, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '186.102.101.247', '2026-05-03 21:27:29'),
(843, 353, 'llego', 'terminado', 'conductor', 46, 'fin', 'Viaje finalizado por el conductor', 'app_conductor', '186.102.101.247', '2026-05-03 21:30:29'),
(844, 354, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-05-03 22:00:43'),
(845, 354, 'buscando', 'buscando', 'conductor', 46, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-05-03 22:01:06'),
(846, 354, 'buscando', 'buscando', 'conductor', 44, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.59', '2026-05-03 22:01:09'),
(847, 354, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-05-03 22:02:07'),
(848, 355, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-05-03 22:02:18'),
(849, 355, 'buscando', 'buscando', 'conductor', 46, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-05-03 22:02:25'),
(850, 355, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-05-03 22:02:45'),
(851, 356, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-05-03 22:03:32'),
(852, 356, 'buscando', 'buscando', 'conductor', 44, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.59', '2026-05-03 22:03:53'),
(853, 356, 'buscando', 'buscando', 'conductor', 46, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-05-03 22:03:54'),
(854, 356, 'buscando', 'buscando', 'conductor', 47, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '191.156.224.236', '2026-05-04 00:01:36'),
(855, 356, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '186.102.92.247', '2026-05-04 00:06:05'),
(856, 357, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '186.102.92.247', '2026-05-04 00:15:11'),
(857, 357, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '186.102.92.247', '2026-05-04 00:16:06'),
(858, 358, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '186.102.92.247', '2026-05-04 00:52:12'),
(859, 358, 'buscando', 'buscando', 'conductor', 46, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '186.102.92.247', '2026-05-04 00:52:37'),
(860, 358, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '186.102.92.247', '2026-05-04 01:00:13'),
(861, 359, NULL, 'buscando', 'pasajero', 54, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '191.156.224.236', '2026-05-04 01:19:05'),
(862, 359, 'buscando', 'buscando', 'conductor', 47, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '191.156.224.236', '2026-05-04 01:19:45'),
(863, 359, 'buscando', 'cancelado_pasajero', 'pasajero', 54, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '191.156.224.236', '2026-05-04 01:20:08'),
(864, 360, NULL, 'buscando', 'pasajero', 54, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '191.156.224.236', '2026-05-04 01:20:17'),
(865, 360, 'buscando', 'asignado', 'conductor', 47, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '191.156.224.236', '2026-05-04 01:20:40'),
(866, 360, 'llego', 'cancelado_pasajero', 'pasajero', 54, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '191.156.224.236', '2026-05-04 01:21:32'),
(867, 361, NULL, 'buscando', 'pasajero', 54, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '191.156.224.236', '2026-05-04 01:22:30'),
(868, 361, 'buscando', 'asignado', 'conductor', 47, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '191.156.224.236', '2026-05-04 01:22:37'),
(869, 361, 'llego', 'terminado', 'conductor', 47, 'fin', 'Viaje finalizado por el conductor', 'app_conductor', '191.156.224.236', '2026-05-04 01:26:08'),
(870, 362, NULL, 'buscando', 'pasajero', 54, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '191.156.224.236', '2026-05-04 01:28:22'),
(871, 362, 'buscando', 'asignado', 'conductor', 47, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '191.156.224.236', '2026-05-04 01:28:31'),
(872, 362, 'llego', 'terminado', 'conductor', 47, 'fin', 'Viaje finalizado por el conductor', 'app_conductor', '191.156.224.236', '2026-05-04 01:29:46'),
(873, 363, NULL, 'buscando', 'pasajero', 54, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '191.156.224.236', '2026-05-04 01:36:43'),
(874, 363, 'buscando', 'asignado', 'conductor', 47, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '191.156.224.236', '2026-05-04 01:36:53'),
(875, 363, 'llego', 'iniciado', 'pasajero', 54, 'inicio', 'Pasajero confirmó que abordó', 'app_pasajero', '191.156.224.236', '2026-05-04 01:37:26'),
(876, 363, 'iniciado', 'terminado', 'conductor', 47, 'fin', 'Viaje finalizado por el conductor', 'app_conductor', '191.156.224.236', '2026-05-04 01:37:42'),
(877, 364, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '186.102.18.116', '2026-05-05 11:33:57'),
(878, 364, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '186.102.18.116', '2026-05-05 11:34:13'),
(879, 364, 'llego', 'terminado', 'conductor', 46, 'fin', 'Viaje finalizado por el conductor', 'app_conductor', '186.102.18.116', '2026-05-05 12:28:10'),
(880, 365, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-05-06 01:51:56'),
(881, 365, 'buscando', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-05-06 01:52:22'),
(882, 366, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-05-06 01:52:35'),
(883, 366, 'buscando', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-05-06 01:52:40'),
(884, 367, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-05-06 01:52:46'),
(885, 367, 'buscando', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-05-06 01:52:54'),
(886, 368, NULL, 'buscando', 'pasajero', 45, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-05-06 01:53:01'),
(887, 368, 'buscando', 'cancelado_pasajero', 'pasajero', 45, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-05-06 01:53:09'),
(888, 369, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-05-06 01:58:54'),
(889, 369, 'buscando', 'buscando', 'conductor', 46, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '181.174.230.42', '2026-05-06 01:59:03'),
(890, 369, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-05-06 01:59:57'),
(891, 370, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '186.102.45.61', '2026-05-10 14:05:27'),
(892, 370, 'buscando', 'buscando', 'conductor', 46, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '186.102.45.61', '2026-05-10 14:05:50'),
(893, 370, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '186.102.45.61', '2026-05-10 14:05:51'),
(894, 317, 'buscando', 'asignado', 'conductor', 48, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '186.102.67.195', '2026-05-11 20:52:23'),
(895, 371, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '186.102.37.141', '2026-05-12 14:46:25'),
(896, 371, 'buscando', 'buscando', 'conductor', 46, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '186.102.37.141', '2026-05-12 14:46:48'),
(897, 371, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '186.102.37.141', '2026-05-12 14:47:01'),
(898, 372, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '186.102.37.141', '2026-05-12 14:47:16'),
(899, 372, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '186.102.37.141', '2026-05-12 14:47:25'),
(900, 372, 'llego', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '186.102.37.141', '2026-05-12 14:49:06'),
(901, 373, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '186.102.37.141', '2026-05-12 15:19:49'),
(902, 373, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '186.102.37.141', '2026-05-12 15:20:03'),
(903, 373, 'llego', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '186.102.37.141', '2026-05-12 15:20:22'),
(904, 374, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '186.102.24.126', '2026-05-15 12:03:58'),
(905, 374, 'buscando', 'buscando', 'conductor', 46, 'flujo', 'Conductor rechazó la solicitud', 'app_conductor', '186.102.24.126', '2026-05-15 12:04:19'),
(906, 374, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-05-15 13:54:31'),
(907, 375, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-05-15 13:55:03'),
(908, 375, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '181.174.230.42', '2026-05-15 13:55:05'),
(909, 376, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '186.102.11.150', '2026-05-18 15:24:37'),
(910, 376, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '186.102.11.150', '2026-05-18 15:24:58'),
(911, 377, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '181.174.230.42', '2026-05-24 12:44:28'),
(912, 377, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '181.174.230.42', '2026-05-24 12:44:46'),
(913, 377, 'llego', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '186.102.33.31', '2026-05-24 21:50:02'),
(914, 378, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '186.102.33.31', '2026-05-24 21:51:39'),
(915, 378, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '186.102.33.31', '2026-05-24 21:52:00'),
(916, 379, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '186.102.33.31', '2026-05-24 21:52:06'),
(917, 379, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '186.102.33.31', '2026-05-24 21:52:29'),
(918, 379, 'asignado', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '186.102.33.31', '2026-05-24 21:52:53'),
(919, 380, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '191.156.157.160', '2026-05-27 13:38:39'),
(920, 380, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '191.156.157.160', '2026-05-27 13:38:48'),
(921, 381, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '191.156.157.160', '2026-05-27 14:04:13'),
(922, 381, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '191.156.157.160', '2026-05-27 14:04:53'),
(923, 382, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '191.156.157.160', '2026-05-27 14:05:00'),
(924, 382, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '191.156.157.160', '2026-05-27 14:05:43');
INSERT INTO `viaje_estados_log` (`id`, `viaje_id`, `from_estado`, `to_estado`, `actor_tipo`, `actor_id`, `motivo_codigo`, `motivo_texto`, `app_origen`, `ip`, `created_at`) VALUES
(925, 383, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '191.156.157.160', '2026-05-27 14:05:59'),
(926, 383, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '191.156.157.160', '2026-05-27 14:06:42'),
(927, 384, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '191.156.157.160', '2026-05-27 14:06:56'),
(928, 384, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '191.156.157.160', '2026-05-27 14:14:26'),
(929, 385, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '191.156.157.160', '2026-05-27 14:14:46'),
(930, 385, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '191.156.157.160', '2026-05-27 14:14:54'),
(931, 386, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '191.156.157.160', '2026-05-27 14:15:53'),
(932, 386, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '191.156.157.160', '2026-05-27 14:16:01'),
(933, 387, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '191.156.157.160', '2026-05-27 14:16:24'),
(934, 387, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '191.156.157.160', '2026-05-27 14:16:36'),
(935, 388, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '191.156.157.160', '2026-05-27 14:17:03'),
(936, 388, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '191.156.157.160', '2026-05-27 14:17:18'),
(937, 389, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '191.156.157.160', '2026-05-27 14:17:58'),
(938, 389, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '191.156.157.160', '2026-05-27 14:18:10'),
(939, 390, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '191.156.157.160', '2026-05-27 14:18:46'),
(940, 390, 'buscando', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '191.156.157.160', '2026-05-27 14:18:53'),
(941, 391, NULL, 'buscando', 'pasajero', 52, 'flujo', 'Solicitud de viaje creada', 'app_pasajero', '191.156.157.160', '2026-05-27 14:20:48'),
(942, 391, 'buscando', 'asignado', 'conductor', 46, 'aceptado', 'Viaje aceptado por el conductor', 'app_conductor', '191.156.157.160', '2026-05-27 14:21:12'),
(943, 391, 'llego', 'cancelado_pasajero', 'pasajero', 52, 'cancelado_pasajero', 'cancelado_pasajero', 'app_pasajero', '191.156.157.160', '2026-05-27 14:41:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wallet_movimientos`
--

CREATE TABLE `wallet_movimientos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `conductor_id` bigint(20) UNSIGNED NOT NULL,
  `viaje_id` bigint(20) UNSIGNED DEFAULT NULL,
  `admin_user_id` int(25) DEFAULT NULL,
  `sentido` enum('credito','debito') NOT NULL,
  `motivo` enum('recarga','ajuste','debito_asignacion','debito_aceptacion','debito_inicio','debito_termino','reversa','bono','penalidad') NOT NULL DEFAULT 'ajuste',
  `monto` decimal(12,2) NOT NULL,
  `moneda` char(3) NOT NULL DEFAULT 'COP',
  `saldo_antes` decimal(12,2) DEFAULT NULL,
  `saldo_despues` decimal(12,2) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `referencia_externa` varchar(64) DEFAULT NULL,
  `idempotencia` varchar(64) DEFAULT NULL,
  `anulado` tinyint(1) NOT NULL DEFAULT 0,
  `anulado_por` int(25) DEFAULT NULL,
  `anulado_motivo` varchar(255) DEFAULT NULL,
  `anulado_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ;

--
-- Volcado de datos para la tabla `wallet_movimientos`
--

INSERT INTO `wallet_movimientos` (`id`, `conductor_id`, `viaje_id`, `admin_user_id`, `sentido`, `motivo`, `monto`, `moneda`, `saldo_antes`, `saldo_despues`, `descripcion`, `referencia_externa`, `idempotencia`, `anulado`, `anulado_por`, `anulado_motivo`, `anulado_at`, `created_at`) VALUES
(2, 32, NULL, 3, 'credito', 'recarga', 100000.00, 'COP', NULL, NULL, 'Recarga manual desde panel administrativo', NULL, 'recarga_admin_32_1778523722_3', 0, NULL, NULL, NULL, '2026-05-11 18:22:02'),
(3, 30, NULL, 2, 'credito', 'recarga', 100000.00, 'COP', NULL, NULL, 'Recarga manual desde panel administrativo', NULL, 'recarga_admin_30_1778527803_2', 0, NULL, NULL, NULL, '2026-05-11 19:30:03'),
(4, 28, NULL, 2, 'credito', 'recarga', 20000000.00, 'COP', NULL, NULL, 'Recarga manual desde panel administrativo', NULL, 'recarga_admin_28_1778595274_2', 0, NULL, NULL, NULL, '2026-05-12 14:14:34'),
(5, 28, NULL, 2, 'credito', 'recarga', 100000.00, 'COP', NULL, NULL, 'Recarga manual desde panel administrativo', NULL, 'recarga_admin_28_1778595346_2', 0, NULL, NULL, NULL, '2026-05-12 14:15:46'),
(6, 29, NULL, 2, 'credito', 'recarga', 200000.00, 'COP', NULL, NULL, 'Recarga manual desde panel administrativo', NULL, 'recarga_admin_29_1778595571_2', 0, NULL, NULL, NULL, '2026-05-12 14:19:31'),
(7, 27, NULL, 2, 'credito', 'recarga', 200000.00, 'COP', NULL, NULL, 'Recarga manual desde panel administrativo', NULL, 'recarga_admin_27_1778595786_2', 0, NULL, NULL, NULL, '2026-05-12 14:23:06');

--
-- Disparadores `wallet_movimientos`
--
DELIMITER $$
CREATE TRIGGER `trg_wallet_movimientos_ai` AFTER INSERT ON `wallet_movimientos` FOR EACH ROW BEGIN
  DECLARE delta DECIMAL(12,2);

  IF NEW.sentido = 'credito' THEN
    SET delta = NEW.monto;
  ELSE
    SET delta = -NEW.monto;
  END IF;

  INSERT INTO wallet_saldos
    (
      conductor_id,
      saldo_actual,
      saldo_reservado,
      min_operativo,
      moneda,
      last_movimiento_id,
      last_movimiento_at,
      created_at,
      updated_at
    )
  VALUES
    (
      NEW.conductor_id,
      delta,
      0.00,
      0.00,
      NEW.moneda,
      NEW.id,
      NOW(),
      NOW(),
      NOW()
    )
  ON DUPLICATE KEY UPDATE
    saldo_actual = saldo_actual + delta,
    last_movimiento_id = NEW.id,
    last_movimiento_at = NOW(),
    updated_at = NOW();
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wallet_saldos`
--

CREATE TABLE `wallet_saldos` (
  `conductor_id` bigint(20) UNSIGNED NOT NULL,
  `saldo_actual` decimal(12,2) NOT NULL DEFAULT 0.00,
  `saldo_reservado` decimal(12,2) NOT NULL DEFAULT 0.00,
  `min_operativo` decimal(12,2) NOT NULL DEFAULT 0.00,
  `moneda` char(3) NOT NULL DEFAULT 'COP',
  `last_movimiento_id` bigint(20) UNSIGNED DEFAULT NULL,
  `last_movimiento_at` datetime DEFAULT NULL,
  `bloqueado` tinyint(1) NOT NULL DEFAULT 0,
  `motivo_bloqueo` varchar(120) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Volcado de datos para la tabla `wallet_saldos`
--

INSERT INTO `wallet_saldos` (`conductor_id`, `saldo_actual`, `saldo_reservado`, `min_operativo`, `moneda`, `last_movimiento_id`, `last_movimiento_at`, `bloqueado`, `motivo_bloqueo`, `created_at`, `updated_at`) VALUES
(27, 200000.00, 0.00, 0.00, 'COP', 7, '2026-05-12 10:23:06', 0, NULL, '2026-05-12 10:23:06', '2026-05-12 14:23:06'),
(28, 20100000.00, 0.00, 0.00, 'COP', 5, '2026-05-12 10:15:46', 0, NULL, '2026-05-12 10:14:34', '2026-05-12 14:15:46'),
(29, 200000.00, 0.00, 0.00, 'COP', 6, '2026-05-12 10:19:31', 0, NULL, '2026-05-12 10:19:31', '2026-05-12 14:19:31'),
(30, 100000.00, 0.00, 0.00, 'COP', 3, '2026-05-11 15:30:03', 0, NULL, '2026-05-11 15:30:03', '2026-05-11 19:30:03'),
(32, 100000.00, 0.00, 0.00, 'COP', 2, '2026-05-11 14:22:02', 0, NULL, '2026-05-11 14:22:02', '2026-05-11 18:22:02');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asignaciones`
--
ALTER TABLE `asignaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_viaje_conductor` (`viaje_id`,`conductor_id`),
  ADD KEY `idx_asig_viaje_estado` (`viaje_id`,`estado`),
  ADD KEY `idx_asig_conductor_estado` (`conductor_id`,`estado`),
  ADD KEY `idx_asig_expira` (`expira_at`);

--
-- Indices de la tabla `auditoria_eventos`
--
ALTER TABLE `auditoria_eventos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_aud_request` (`request_id`),
  ADD KEY `idx_aud_tabla_registro_time` (`tabla_objetivo`,`registro_pk`,`created_at`),
  ADD KEY `idx_aud_actor_time` (`actor_user_id`,`created_at`),
  ADD KEY `idx_aud_viaje` (`viaje_id`),
  ADD KEY `idx_aud_conductor` (`conductor_id`),
  ADD KEY `idx_aud_accion_time` (`accion`,`created_at`);

--
-- Indices de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_calif_viaje_rater` (`viaje_id`,`rater_id`),
  ADD KEY `idx_calif_ratee` (`ratee_id`,`created_at`),
  ADD KEY `idx_calif_viaje` (`viaje_id`),
  ADD KEY `idx_calif_score_time` (`puntuacion`,`created_at`),
  ADD KEY `calif_rater_fk` (`rater_id`);

--
-- Indices de la tabla `chat_mensajes`
--
ALTER TABLE `chat_mensajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_chat_viaje_time` (`viaje_id`,`created_at`),
  ADD KEY `idx_chat_remitente` (`remitente_id`,`viaje_id`),
  ADD KEY `idx_chat_tipo` (`tipo`),
  ADD KEY `idx_chat_latlng` (`lat`,`lng`),
  ADD KEY `chat_reply_fk` (`reply_to_id`);

--
-- Indices de la tabla `conductores`
--
ALTER TABLE `conductores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `conductores_user_id_unique` (`user_id`),
  ADD KEY `idx_conductores_estado` (`estado_operitivo`,`disponible`),
  ADD KEY `idx_conductores_licencia_expira` (`licencia_expira`),
  ADD KEY `idx_conductores_soat_expira` (`soat_expira`);

--
-- Indices de la tabla `conductor_posiciones`
--
ALTER TABLE `conductor_posiciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pos_conductor_time` (`conductor_id`,`created_at`),
  ADD SPATIAL KEY `sp_pos_ubicacion` (`ubicacion`),
  ADD KEY `idx_pos_viaje` (`viaje_id`);

--
-- Indices de la tabla `conductor_posicion_actual`
--
ALTER TABLE `conductor_posicion_actual`
  ADD PRIMARY KEY (`conductor_id`),
  ADD KEY `idx_cpa_viaje` (`viaje_id`),
  ADD SPATIAL KEY `sp_cpa_ubicacion` (`ubicacion`);

--
-- Indices de la tabla `documentos_conductor`
--
ALTER TABLE `documentos_conductor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_doc_conductor_tipo_num` (`conductor_id`,`tipo`,`numero`),
  ADD KEY `idx_doc_conductor` (`conductor_id`),
  ADD KEY `idx_doc_expira` (`expira_at`),
  ADD KEY `idx_doc_estado` (`estado_verificacion`),
  ADD KEY `doc_verificado_por_fk` (`verificado_por`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `llamadas`
--
ALTER TABLE `llamadas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_llamada_idem` (`idempotencia`),
  ADD KEY `idx_llamada_viaje_time` (`viaje_id`,`call_start_at`),
  ADD KEY `idx_llamada_llamador` (`llamador_user_id`,`call_start_at`),
  ADD KEY `idx_llamada_receptor` (`receptor_user_id`,`call_start_at`),
  ADD KEY `idx_llamada_estado` (`estado`),
  ADD KEY `llamada_dispositivo_fk` (`dispositivo_id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `notas_operacion`
--
ALTER TABLE `notas_operacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notas_entity_time` (`entity_type`,`entity_id`,`created_at`),
  ADD KEY `idx_notas_viaje` (`viaje_id`),
  ADD KEY `idx_notas_conductor` (`conductor_id`),
  ADD KEY `idx_notas_user` (`user_id`),
  ADD KEY `idx_notas_pinned` (`pinned`),
  ADD KEY `idx_notas_author_time` (`created_by`,`created_at`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_notif_idempotencia` (`idempotencia`),
  ADD KEY `idx_notif_user_time` (`user_id`,`created_at`),
  ADD KEY `idx_notif_viaje` (`viaje_id`),
  ADD KEY `idx_notif_estado` (`estado`),
  ADD KEY `idx_notif_programada` (`programada_at`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indices de la tabla `push_tokens`
--
ALTER TABLE `push_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_push_token` (`token`),
  ADD UNIQUE KEY `uniq_push_disp_token_provider` (`dispositivo_id`,`token`,`provider`),
  ADD UNIQUE KEY `uniq_push_idempotencia` (`idempotencia`),
  ADD KEY `idx_push_hash` (`token_hash`),
  ADD KEY `idx_push_disp_estado` (`dispositivo_id`,`estado`),
  ADD KEY `idx_push_provider` (`provider`),
  ADD KEY `idx_push_ultimo_uso` (`ultimo_uso_at`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indices de la tabla `sos_incidentes`
--
ALTER TABLE `sos_incidentes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sos_estado_time` (`estado`,`created_at`),
  ADD KEY `idx_sos_severidad` (`severidad`),
  ADD KEY `idx_sos_viaje` (`viaje_id`),
  ADD KEY `idx_sos_actor` (`actor_tipo`,`actor_user_id`),
  ADD KEY `idx_sos_conductor` (`conductor_id`),
  ADD KEY `idx_sos_operador` (`operador_id`),
  ADD KEY `idx_sos_latlng` (`lat`,`lng`),
  ADD KEY `sos_actor_user_fk` (`actor_user_id`);

--
-- Indices de la tabla `tarifas`
--
ALTER TABLE `tarifas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_nombre_vigencia` (`nombre`,`vigente_desde`),
  ADD KEY `idx_tarifas_activa` (`activa`),
  ADD KEY `idx_tarifas_scope_ctx` (`scope`,`ciudad`,`categoria`,`horario`,`activa`),
  ADD KEY `idx_tarifas_vigencia` (`vigente_desde`,`vigente_hasta`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_role_id` (`user_role_id`);

--
-- Indices de la tabla `usuario_dispositivos`
--
ALTER TABLE `usuario_dispositivos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_usuario_device` (`user_id`,`device_uuid`,`plataforma`),
  ADD KEY `idx_disp_user` (`user_id`),
  ADD KEY `idx_disp_plat_activo` (`plataforma`,`activo`),
  ADD KEY `idx_disp_last_seen` (`last_seen_at`);

--
-- Indices de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehiculos_placa_unique` (`placa`),
  ADD KEY `idx_vehiculos_conductor` (`conductor_id`),
  ADD KEY `idx_vehiculos_docs_expiran` (`soat_expira`,`tecnomecanica_expira`),
  ADD KEY `idx_vehiculos_verificacion` (`verificacion_estado`);

--
-- Indices de la tabla `viajes`
--
ALTER TABLE `viajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_viajes_estado_time` (`estado`,`created_at`),
  ADD KEY `idx_viajes_pasajero` (`pasajero_id`,`created_at`),
  ADD KEY `idx_viajes_conductor_estado` (`conductor_id`,`estado`),
  ADD KEY `idx_viajes_aceptar_hasta` (`aceptar_hasta`),
  ADD SPATIAL KEY `sp_viajes_origen` (`origen_ubicacion`),
  ADD KEY `viajes_vehiculo_fk` (`vehiculo_id`),
  ADD KEY `idx_viajes_tarifa` (`tarifa_id`);

--
-- Indices de la tabla `viaje_estados_log`
--
ALTER TABLE `viaje_estados_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_log_viaje_time` (`viaje_id`,`created_at`),
  ADD KEY `idx_log_to_estado` (`to_estado`),
  ADD KEY `idx_log_actor` (`actor_tipo`,`actor_id`),
  ADD KEY `log_actor_user_fk` (`actor_id`);

--
-- Indices de la tabla `wallet_movimientos`
--
ALTER TABLE `wallet_movimientos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_wallet_idempotencia` (`idempotencia`),
  ADD KEY `idx_wallet_conductor_time` (`conductor_id`,`created_at`),
  ADD KEY `idx_wallet_viaje` (`viaje_id`),
  ADD KEY `idx_wallet_admin` (`admin_user_id`),
  ADD KEY `idx_wallet_anulado` (`anulado`),
  ADD KEY `wallet_anulado_por_fk` (`anulado_por`);

--
-- Indices de la tabla `wallet_saldos`
--
ALTER TABLE `wallet_saldos`
  ADD PRIMARY KEY (`conductor_id`),
  ADD KEY `idx_ws_last_mov` (`last_movimiento_at`),
  ADD KEY `ws_last_mov_fk` (`last_movimiento_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asignaciones`
--
ALTER TABLE `asignaciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `auditoria_eventos`
--
ALTER TABLE `auditoria_eventos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `chat_mensajes`
--
ALTER TABLE `chat_mensajes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `conductores`
--
ALTER TABLE `conductores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `conductor_posiciones`
--
ALTER TABLE `conductor_posiciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `documentos_conductor`
--
ALTER TABLE `documentos_conductor`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `llamadas`
--
ALTER TABLE `llamadas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `notas_operacion`
--
ALTER TABLE `notas_operacion`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=891;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `push_tokens`
--
ALTER TABLE `push_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `sos_incidentes`
--
ALTER TABLE `sos_incidentes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tarifas`
--
ALTER TABLE `tarifas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT de la tabla `usuario_dispositivos`
--
ALTER TABLE `usuario_dispositivos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `viajes`
--
ALTER TABLE `viajes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `viaje_estados_log`
--
ALTER TABLE `viaje_estados_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=944;

--
-- AUTO_INCREMENT de la tabla `wallet_movimientos`
--
ALTER TABLE `wallet_movimientos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asignaciones`
--
ALTER TABLE `asignaciones`
  ADD CONSTRAINT `asig_conductor_fk` FOREIGN KEY (`conductor_id`) REFERENCES `conductores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `asig_viaje_fk` FOREIGN KEY (`viaje_id`) REFERENCES `viajes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `auditoria_eventos`
--
ALTER TABLE `auditoria_eventos`
  ADD CONSTRAINT `aud_actor_user_fk` FOREIGN KEY (`actor_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `aud_conductor_fk` FOREIGN KEY (`conductor_id`) REFERENCES `conductores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `aud_viaje_fk` FOREIGN KEY (`viaje_id`) REFERENCES `viajes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD CONSTRAINT `calif_ratee_fk` FOREIGN KEY (`ratee_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `calif_rater_fk` FOREIGN KEY (`rater_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `calif_viaje_fk` FOREIGN KEY (`viaje_id`) REFERENCES `viajes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `chat_mensajes`
--
ALTER TABLE `chat_mensajes`
  ADD CONSTRAINT `chat_remitente_fk` FOREIGN KEY (`remitente_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `chat_reply_fk` FOREIGN KEY (`reply_to_id`) REFERENCES `chat_mensajes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `chat_viaje_fk` FOREIGN KEY (`viaje_id`) REFERENCES `viajes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `conductores`
--
ALTER TABLE `conductores`
  ADD CONSTRAINT `conductores_users_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `conductor_posiciones`
--
ALTER TABLE `conductor_posiciones`
  ADD CONSTRAINT `posiciones_conductor_fk` FOREIGN KEY (`conductor_id`) REFERENCES `conductores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `posiciones_viaje_fk` FOREIGN KEY (`viaje_id`) REFERENCES `viajes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `conductor_posicion_actual`
--
ALTER TABLE `conductor_posicion_actual`
  ADD CONSTRAINT `cpa_conductor_fk` FOREIGN KEY (`conductor_id`) REFERENCES `conductores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cpa_viaje_fk` FOREIGN KEY (`viaje_id`) REFERENCES `viajes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `documentos_conductor`
--
ALTER TABLE `documentos_conductor`
  ADD CONSTRAINT `doc_conductor_fk` FOREIGN KEY (`conductor_id`) REFERENCES `conductores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `doc_verificado_por_fk` FOREIGN KEY (`verificado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `llamadas`
--
ALTER TABLE `llamadas`
  ADD CONSTRAINT `llamada_dispositivo_fk` FOREIGN KEY (`dispositivo_id`) REFERENCES `usuario_dispositivos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `llamada_llamador_fk` FOREIGN KEY (`llamador_user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `llamada_receptor_fk` FOREIGN KEY (`receptor_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `llamada_viaje_fk` FOREIGN KEY (`viaje_id`) REFERENCES `viajes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `notas_operacion`
--
ALTER TABLE `notas_operacion`
  ADD CONSTRAINT `nota_conductor_fk` FOREIGN KEY (`conductor_id`) REFERENCES `conductores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `nota_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `nota_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `nota_viaje_fk` FOREIGN KEY (`viaje_id`) REFERENCES `viajes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `notificaciones_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notificaciones_viaje_fk` FOREIGN KEY (`viaje_id`) REFERENCES `viajes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `push_tokens`
--
ALTER TABLE `push_tokens`
  ADD CONSTRAINT `push_dispositivo_fk` FOREIGN KEY (`dispositivo_id`) REFERENCES `usuario_dispositivos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `sos_incidentes`
--
ALTER TABLE `sos_incidentes`
  ADD CONSTRAINT `sos_actor_user_fk` FOREIGN KEY (`actor_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sos_conductor_fk` FOREIGN KEY (`conductor_id`) REFERENCES `conductores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sos_operador_fk` FOREIGN KEY (`operador_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sos_viaje_fk` FOREIGN KEY (`viaje_id`) REFERENCES `viajes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`user_role_id`) REFERENCES `roles` (`role_id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `usuario_dispositivos`
--
ALTER TABLE `usuario_dispositivos`
  ADD CONSTRAINT `usuario_dispositivos_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD CONSTRAINT `vehiculos_conductor_fk` FOREIGN KEY (`conductor_id`) REFERENCES `conductores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `viajes`
--
ALTER TABLE `viajes`
  ADD CONSTRAINT `viajes_conductor_fk` FOREIGN KEY (`conductor_id`) REFERENCES `conductores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `viajes_pasajero_fk` FOREIGN KEY (`pasajero_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `viajes_tarifa_fk` FOREIGN KEY (`tarifa_id`) REFERENCES `tarifas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `viajes_vehiculo_fk` FOREIGN KEY (`vehiculo_id`) REFERENCES `vehiculos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `viaje_estados_log`
--
ALTER TABLE `viaje_estados_log`
  ADD CONSTRAINT `log_actor_user_fk` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `log_viaje_fk` FOREIGN KEY (`viaje_id`) REFERENCES `viajes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `wallet_movimientos`
--
ALTER TABLE `wallet_movimientos`
  ADD CONSTRAINT `wallet_admin_fk` FOREIGN KEY (`admin_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `wallet_anulado_por_fk` FOREIGN KEY (`anulado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `wallet_conductor_fk` FOREIGN KEY (`conductor_id`) REFERENCES `conductores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `wallet_viaje_fk` FOREIGN KEY (`viaje_id`) REFERENCES `viajes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `wallet_saldos`
--
ALTER TABLE `wallet_saldos`
  ADD CONSTRAINT `ws_conductor_fk` FOREIGN KEY (`conductor_id`) REFERENCES `conductores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ws_last_mov_fk` FOREIGN KEY (`last_movimiento_id`) REFERENCES `wallet_movimientos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
