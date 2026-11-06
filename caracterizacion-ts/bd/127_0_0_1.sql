-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-10-2026 a las 17:43:11
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bd_caracterizacion_tecnico`
--
CREATE DATABASE IF NOT EXISTS `bd_caracterizacion_tecnico` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `bd_caracterizacion_tecnico`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividades_extracurriculares`
--

CREATE TABLE `actividades_extracurriculares` (
  `id_ficha` int(11) NOT NULL,
  `educacion_complementaria_desc` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `educacion_comp_dias` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `educacion_comp_horario` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deporte_entrenamiento` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deporte_dias` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deporte_horario` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trabajo_ocupacion` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trabajo_dias` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trabajo_horario` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `actividades_extracurriculares`
--

INSERT INTO `actividades_extracurriculares` (`id_ficha`, `educacion_complementaria_desc`, `educacion_comp_dias`, `educacion_comp_horario`, `deporte_entrenamiento`, `deporte_dias`, `deporte_horario`, `trabajo_ocupacion`, `trabajo_dias`, `trabajo_horario`) VALUES
(20, 'Robotica escolar', 'Sábados', '8:00 AM - 11:00 AM', 'Natación', 'Martes y Jueves', '4:00 PM - 6:00 PM', NULL, NULL, NULL),
(21, NULL, NULL, NULL, 'Voleibol', 'Lunes y Miércoles', '3:30 PM - 5:30 PM', NULL, NULL, NULL),
(22, 'Inglés básico', 'Viernes', '2:00 PM - 4:00 PM', 'Fútbol', 'Sábados', '9:00 AM - 11:00 AM', NULL, NULL, NULL),
(23, 'Pintura y Dibujo', 'Sábados', '9:00 AM - 12:00 PM', NULL, NULL, NULL, NULL, NULL, NULL),
(24, NULL, NULL, NULL, NULL, NULL, NULL, 'Apoyo en granja familiar', 'Fines de semana', '7:00 AM - 11:00 AM'),
(25, 'Música (Guitarra)', 'Martes y Jueves', '3:00 PM - 5:00 PM', 'Patinaje', 'Sábados', '8:00 AM - 10:00 AM', NULL, NULL, NULL),
(26, NULL, NULL, NULL, 'Fútbol de salón', 'Miércoles y Viernes', '4:00 PM - 6:00 PM', NULL, NULL, NULL),
(27, 'Francés', 'Sábados', '8:00 AM - 12:00 PM', 'Tenis de mesa', 'Lunes y Miércoles', '4:00 PM - 5:30 PM', NULL, NULL, NULL),
(28, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 'Programación Web', 'Sábados', '2:00 PM - 5:00 PM', 'Ajedrez', 'Jueves', '3:00 PM - 5:00 PM', NULL, NULL, NULL),
(30, NULL, NULL, NULL, 'Baloncesto', 'Martes y Jueves', '4:00 PM - 6:00 PM', NULL, NULL, NULL),
(31, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(32, NULL, NULL, NULL, 'Fútbol', 'Martes y Viernes', '3:30 PM - 5:30 PM', 'Atención en panadería', 'Sábados', '2:00 PM - 6:00 PM'),
(33, 'Danza Folclórica', 'Lunes y Miércoles', '3:00 PM - 5:00 PM', NULL, NULL, NULL, NULL, NULL, NULL),
(34, 'Modelado 3D', 'Sábados', '9:00 AM - 12:00 PM', 'Natación', 'Viernes', '4:00 PM - 6:00 PM', NULL, NULL, NULL),
(35, NULL, NULL, NULL, 'Taekwondo', 'Lunes y Jueves', '5:00 PM - 6:30 PM', NULL, NULL, NULL),
(36, NULL, NULL, NULL, 'Atletismo', 'Lunes a Jueves', '4:00 PM - 6:00 PM', NULL, NULL, NULL),
(37, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(38, NULL, NULL, NULL, 'Fútbol', 'Sábados', '8:00 AM - 10:00 AM', NULL, NULL, NULL),
(39, 'Inglés Intermedio', 'Martes y Jueves', '4:00 PM - 6:00 PM', 'Gimnasia', 'Miércoles y Viernes', '3:00 PM - 5:00 PM', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `catalogos_caracterizacion`
--

CREATE TABLE `catalogos_caracterizacion` (
  `id_catalogo` int(11) NOT NULL,
  `categoria` enum('Discapacidad','Trastorno Aprendizaje','Excepcionalidad','Grupo Poblacional') COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `catalogos_caracterizacion`
--

INSERT INTO `catalogos_caracterizacion` (`id_catalogo`, `categoria`, `descripcion`) VALUES
(1, 'Discapacidad', 'Auditiva Castellano Oral'),
(2, 'Discapacidad', 'Auditiva Lenguaje Señas'),
(3, 'Discapacidad', 'Sordoceguera'),
(4, 'Discapacidad', 'Visual'),
(5, 'Discapacidad', 'Intelectual'),
(6, 'Discapacidad', 'Física'),
(7, 'Discapacidad', 'Psicosocial'),
(8, 'Discapacidad', 'Múltiple'),
(9, 'Discapacidad', 'Trastorno Autista TEA'),
(10, 'Discapacidad', 'Otra'),
(11, 'Trastorno Aprendizaje', 'Lectura'),
(12, 'Trastorno Aprendizaje', 'Escritural'),
(13, 'Trastorno Aprendizaje', 'Cálculo'),
(14, 'Trastorno Aprendizaje', 'Ortografía'),
(15, 'Trastorno Aprendizaje', 'Conducta'),
(16, 'Trastorno Aprendizaje', 'De habla / Lenguaje'),
(17, 'Excepcionalidad', 'Tecnología'),
(18, 'Excepcionalidad', 'Liderazgo Social'),
(19, 'Excepcionalidad', 'Ciencias de la Naturaleza'),
(20, 'Excepcionalidad', 'Artes y Letras'),
(21, 'Excepcionalidad', 'Actividad Física'),
(22, 'Excepcionalidad', 'Ciencias Sociales y Humanas'),
(23, 'Grupo Poblacional', 'Comunidad Blanca'),
(24, 'Grupo Poblacional', 'Comunidad Mestiza'),
(25, 'Grupo Poblacional', 'Comunidad Rural'),
(26, 'Grupo Poblacional', 'Afrocolombianidad'),
(27, 'Grupo Poblacional', 'Grupo Indígenas'),
(28, 'Grupo Poblacional', 'Comunidad LGTBI'),
(29, 'Grupo Poblacional', 'Desplazado'),
(30, 'Grupo Poblacional', 'Víctima Conflicto Armado'),
(31, 'Grupo Poblacional', 'Hijo de Desmovilizado'),
(32, 'Grupo Poblacional', 'Asentamiento Subnormal'),
(33, 'Grupo Poblacional', 'Asistido Fundación/ICBF');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos_familiares_hogar`
--

CREATE TABLE `datos_familiares_hogar` (
  `id_ficha` int(11) NOT NULL,
  `padre_nombres` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `padre_escolaridad` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `padre_edad` tinyint(3) UNSIGNED DEFAULT NULL,
  `padre_ocupacion` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `padre_tipo_ocupacion` enum('Independiente','Empleado') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `madre_nombres` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `madre_escolaridad` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `madre_edad` tinyint(3) UNSIGNED DEFAULT NULL,
  `madre_ocupacion` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `madre_tipo_ocupacion` enum('Independiente','Empleado') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ingresos_hogar` enum('Menos de 1 SMLV','Entre 1 y 2 SMLV','Más de 2 SMLV') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenencia_vivienda` enum('Propia','Arrendada','De familiares','Otro') COLLATE utf8mb4_unicode_ci NOT NULL,
  `convive_con` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `es_hijo_unico` tinyint(1) NOT NULL DEFAULT 0,
  `hermanos_en_colegio` tinyint(1) NOT NULL DEFAULT 0,
  `total_personas_hogar` tinyint(3) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `datos_familiares_hogar`
--

INSERT INTO `datos_familiares_hogar` (`id_ficha`, `padre_nombres`, `padre_escolaridad`, `padre_edad`, `padre_ocupacion`, `padre_tipo_ocupacion`, `madre_nombres`, `madre_escolaridad`, `madre_edad`, `madre_ocupacion`, `madre_tipo_ocupacion`, `ingresos_hogar`, `tenencia_vivienda`, `convive_con`, `es_hijo_unico`, `hermanos_en_colegio`, `total_personas_hogar`) VALUES
(20, 'Carlos Gómez', 'Universitaria', 44, 'Ingeniero', 'Empleado', 'Andrea Silva', 'Universitaria', 40, 'Contadora', 'Empleado', 'Más de 2 SMLV', 'Propia', 'Mis padres y hermanos', 0, 1, 4),
(21, 'Wilson Ramírez', 'Secundaria', 41, 'Conductor', 'Empleado', 'Martha Parra', 'Secundaria', 38, 'Esteticista', 'Independiente', 'Entre 1 y 2 SMLV', 'Arrendada', 'Hermanos y uno de mis padres', 0, 1, 5),
(22, 'Jorge Castro', 'Técnico', 43, 'Mecánico', 'Independiente', 'Liliana Vargas', 'Secundaria', 39, 'Hogar', 'Independiente', 'Entre 1 y 2 SMLV', 'Propia', 'Mis padres y hermanos', 0, 0, 4),
(23, 'Fernando Ortiz', 'Secundaria', 39, 'Vendedor', 'Empleado', 'Sonia Lasso', 'Técnico', 36, 'Cajera', 'Empleado', 'Entre 1 y 2 SMLV', 'Arrendada', 'Mis padres y hermanos', 1, 0, 3),
(24, 'Pedro Medina', 'Primaria', 48, 'Agricultor', 'Independiente', 'Rosa Trujillo', 'Primaria', 45, 'Hogar', 'Independiente', 'Menos de 1 SMLV', 'De familiares', 'Padres, hermanos y otros familiares', 0, 1, 6),
(25, 'Gustavo Morales', 'Universitaria', 46, 'Administrador', 'Empleado', 'Claudia Chávarro', 'Técnico', 42, 'Secretaria', 'Empleado', 'Más de 2 SMLV', 'Propia', 'Mis padres y hermanos', 0, 0, 4),
(26, 'Gonzalo Gutiérrez', 'Secundaria', 40, 'Comerciante', 'Independiente', 'Sandra Perdomo', 'Secundaria', 37, 'Hogar', 'Independiente', 'Entre 1 y 2 SMLV', 'Arrendada', 'Mis padres y hermanos', 0, 1, 5),
(27, 'Álvaro Navia', 'Universitaria', 49, 'Docente', 'Empleado', 'Luz Mery Tovar', 'Universitaria', 46, 'Enfermera', 'Empleado', 'Más de 2 SMLV', 'Propia', 'Mis padres y hermanos', 1, 0, 3),
(28, 'José Mosquera', 'Primaria', 42, 'Construcción', 'Independiente', 'Rocío Rivas', 'Primaria', 39, 'Servicios Varios', 'Empleado', 'Menos de 1 SMLV', 'Otro', 'Padres, hermanos y otros familiares', 0, 1, 7),
(29, 'Edgar Beltrán', 'Técnico', 45, 'Técnico Electricista', 'Independiente', 'Monika Cerquera', 'Universitaria', 41, 'Abogada', 'Empleado', 'Más de 2 SMLV', 'Propia', 'Mis padres y hermanos', 0, 0, 4),
(30, 'César Garzón', 'Secundaria', 42, 'Guarda de Seguridad', 'Empleado', 'Patricia Peña', 'Secundaria', 38, 'Hogar', 'Independiente', 'Entre 1 y 2 SMLV', 'Arrendada', 'Hermanos y uno de mis padres', 0, 1, 4),
(31, 'Héctor Cardozo', 'Primaria', 47, 'Ebanista', 'Independiente', 'Nohora Hermosa', 'Secundaria', 43, 'Comerciante', 'Independiente', 'Menos de 1 SMLV', 'De familiares', 'Únicamente otros familiares', 0, 0, 5),
(32, 'Jaime Cabrera', 'Secundaria', 50, 'Conductor Taxista', 'Independiente', 'Yolanda Mota', 'Primaria', 46, 'Hogar', 'Independiente', 'Entre 1 y 2 SMLV', 'Arrendada', 'Hermanos y uno de mis padres', 0, 1, 5),
(33, 'Raúl Rivera', 'Técnico', 41, 'Plomero', 'Independiente', 'Gloria Bahamón', 'Secundaria', 38, 'Auxiliar de Cocina', 'Empleado', 'Entre 1 y 2 SMLV', 'Arrendada', 'Mis padres y hermanos', 0, 0, 4),
(34, 'Mauricio Delgado', 'Universitaria', 43, 'Contador', 'Independiente', 'Diana Cuéllar', 'Universitaria', 40, 'Docente', 'Empleado', 'Más de 2 SMLV', 'Propia', 'Mis padres y hermanos', 1, 0, 3),
(35, 'Mario Fierro', 'Secundaria', 44, 'Mecánico', 'Independiente', 'Yuly Losada', 'Secundaria', 40, 'Hogar', 'Independiente', 'Entre 1 y 2 SMLV', 'De familiares', 'Padres, hermanos y otros familiares', 0, 1, 6),
(36, 'Hernán Sánchez', 'Secundaria', 48, 'Comerciante', 'Independiente', 'Alicia Andrade', 'Secundaria', 45, 'Hogar', 'Independiente', 'Entre 1 y 2 SMLV', 'Propia', 'Mis padres y hermanos', 0, 1, 5),
(37, 'Willian Ospina', 'Primaria', 42, 'Jardinero', 'Independiente', 'Cielo Bonilla', 'Primaria', 38, 'Servicios Domésticos', 'Empleado', 'Menos de 1 SMLV', 'Arrendada', 'Hermanos y uno de mis padres', 0, 0, 4),
(38, 'Jairo Meléndez', 'Secundaria', 39, 'Ayudante de Taller', 'Empleado', 'Yolanda Silva', 'Primaria', 36, 'Hogar', 'Independiente', 'Menos de 1 SMLV', 'Arrendada', 'Padres, hermanos y otros familiares', 0, 1, 6),
(39, 'Óscar Polanía', 'Universitaria', 47, 'Arquitecto', 'Independiente', 'Claudia Lara', 'Universitaria', 43, 'Diseñadora', 'Independiente', 'Más de 2 SMLV', 'Propia', 'Mis padres y hermanos', 0, 1, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiantes`
--

CREATE TABLE `estudiantes` (
  `id_estudiante` int(11) NOT NULL,
  `tipo_documento` enum('RC','TI','CC','Otro') COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_documento` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombres_apellidos` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `edad` tinyint(3) UNSIGNED NOT NULL,
  `grupo_sanguineo` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_estudiante` enum('Antiguo','Antiguo Repitente','Nuevo','Nuevo Repitente') COLLATE utf8mb4_unicode_ci NOT NULL,
  `colegio_procedencia` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion_residencia` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barrio` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comuna` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estrato` tinyint(3) UNSIGNED NOT NULL,
  `servicio_salud` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono_fijo` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono_movil` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_padres` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tel_movil_padre` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tel_movil_madre` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `creado_en` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `estudiantes`
--

INSERT INTO `estudiantes` (`id_estudiante`, `tipo_documento`, `numero_documento`, `nombres_apellidos`, `fecha_nacimiento`, `edad`, `grupo_sanguineo`, `tipo_estudiante`, `colegio_procedencia`, `direccion_residencia`, `barrio`, `comuna`, `estrato`, `servicio_salud`, `telefono_fijo`, `telefono_movil`, `email_padres`, `tel_movil_padre`, `tel_movil_madre`, `activo`, `creado_en`) VALUES
(3, 'RC', '1117854210', 'el patorse', '2013-02-08', 13, 'B+', 'Antiguo Repitente', NULL, 'Manzana D Casa 12', 'Los Mártires', 'Comuna 8', 1, 'Asmet Salud', '6088765432', '3201478523', NULL, '190123912312', '3219632587', 1, '2026-09-04 13:46:03'),
(10, 'TI', '1080123456', 'Sofía Valentina Rojas Meza', '2012-05-14', 14, 'A+', 'Antiguo', NULL, 'Calle 10 # 5-20', 'Centro', 'Comuna 1', 3, 'Nueva EPS', '6088711223', '3112233445', 'padres.sofia@gmail.com', '3104455667', '3127788990', 1, '2026-10-16 12:41:46'),
(11, 'TI', '1080987654', 'Mateo Stiven Benavides Ortiz', '2011-09-22', 15, 'O+', 'Nuevo', 'I.E. INEM Julián Motta Salas', 'Carrera 7 # 12-45', 'Altico', 'Comuna 2', 2, 'Sanitas', NULL, '3156677889', 'mateo.padres@hotmail.com', '3189900112', '3192233445', 1, '2026-10-16 12:41:46'),
(12, 'TI', '1080456789', 'Valeria Ximena Torres Castro', '2013-12-03', 13, 'O-', 'Antiguo', NULL, 'Avenida 26 # 4-50', 'Las Granjas', 'Comuna 3', 3, 'Coomeva', '6088733445', '3167788992', 'valeria.torres@gmail.com', '3141122334', '3135566778', 1, '2026-10-16 12:41:46'),
(20, 'TI', '1081000001', 'Juan David Gómez Silva', '2011-03-15', 15, 'O+', 'Antiguo', NULL, 'Calle 18 # 12-30', 'Tenerife', 'Comuna 3', 3, 'Sanitas', '6088712301', '3102000001', 'fam.gomez@gmail.com', '3113000001', '3124000001', 1, '2026-10-16 12:46:40'),
(21, 'TI', '1081000002', 'María José Ramírez Parra', '2012-07-20', 14, 'A+', 'Antiguo', NULL, 'Carrera 15 # 8-45', 'Mártires', 'Comuna 8', 2, 'Asmet Salud', NULL, '3102000002', 'fam.ramirez@gmail.com', '3113000002', '3124000002', 1, '2026-10-16 12:46:40'),
(22, 'TI', '1081000003', 'Danna Milena Campos Castañeda', '2010-11-05', 15, 'B+', 'Nuevo', 'I.E. Oliverio Lara Borrero', 'Calle 2 G SUR # 22-10', 'Cándido Leguízamo', 'Comuna 1', 3, 'Nueva EPS', '6088712303', '3102000003', 'fam.castro@gmail.com', '3113000003', '3124000003', 1, '2026-10-16 12:46:40'),
(23, 'TI', '1081000004', 'Camila Alejandra Ortiz Lasso', '2013-01-18', 13, 'O-', 'Antiguo', NULL, 'Carrera 30 # 19-50', 'Alfonso López', 'Comuna 6', 2, 'Coosalud', NULL, '3102000004', 'fam.ortiz@gmail.com', '3113000004', '3124000004', 0, '2026-10-16 12:46:40'),
(24, 'TI', '1081000005', 'Samuel Esteban Medina Trujillo', '2012-09-30', 14, 'AB+', 'Antiguo Repitente', NULL, 'Calle 8 # 38-12', 'Las Granjas', 'Comuna 2', 2, 'Salud Total', '6088712305', '3102000005', 'fam.medina@gmail.com', '3113000005', '3124000005', 1, '2026-10-16 12:46:40'),
(25, 'TI', '1081000006', 'Isabella Morales Chávarro', '2011-04-12', 15, 'A-', 'Antiguo', NULL, 'Calle 25 # 5A-20', 'Sevilla', 'Comuna 3', 3, 'Sura', '6088712306', '3102000006', 'fam.morales@gmail.com', '3113000006', '3124000006', 1, '2026-10-16 12:46:40'),
(26, 'TI', '1081000007', 'Daniel Santiago Gutiérrez Perdomo', '2013-08-25', 13, 'O+', 'Nuevo', 'I.E. Gabriel García Márquez', 'Carrera 5 # 11-08', 'Centro', 'Comuna 1', 3, 'Nueva EPS', NULL, '3102000007', 'fam.gutierrez@gmail.com', '3113000007', '3124000007', 1, '2026-10-16 12:46:40'),
(27, 'TI', '1081000008', 'Valeria Sofia Navia Tovar', '2010-02-14', 16, 'O+', 'Antiguo', NULL, 'Calle 19 # 28-15', 'Prados del Norte', 'Comuna 2', 4, 'Sanitas', '6088712308', '3102000008', 'fam.navia@gmail.com', '3113000008', '3124000008', 1, '2026-10-16 12:46:40'),
(28, 'TI', '1081000009', 'Emmanuel David Mosquera Rivas', '2012-12-01', 13, 'B-', 'Nuevo', 'I.E. Santa Librada', 'Manzana C Casa 4', 'Las Palmas', 'Comuna 10', 1, 'Asmet Salud', NULL, '3102000009', 'fam.mosquera@gmail.com', '3113000009', '3124000009', 1, '2026-10-16 12:46:40'),
(29, 'TI', '1081000010', 'Luciana Beltrán Cerquera', '2011-06-19', 15, 'O+', 'Antiguo', NULL, 'Carrera 22 # 7-30', 'Ipanema', 'Comuna 7', 4, 'Coomeva', '6088712310', '3102000010', 'fam.beltran@gmail.com', '3113000010', '3124000010', 1, '2026-10-16 12:46:40'),
(30, 'TI', '1081000011', 'Nicolás Alejandro Garzón Peña', '2013-05-03', 13, 'O+', 'Antiguo', NULL, 'Calle 21 # 10-40', 'Quirinal', 'Comuna 3', 3, 'Salud Total', NULL, '3102000011', 'fam.garzon@gmail.com', '3113000011', '3124000011', 1, '2026-10-16 12:46:40'),
(31, 'TI', '1081000012', 'Salomé Cardozo Hermosa', '2012-10-10', 14, 'A+', 'Antiguo', NULL, 'Carrera 1W # 28-14', 'Inmaculada', 'Comuna 9', 1, 'Coosalud', '6088712312', '3102000012', 'fam.cardozo@gmail.com', '3113000012', '3124000012', 1, '2026-10-16 12:46:40'),
(32, 'TI', '1081000013', 'Tomás Eduardo Cabrera Mota', '2010-09-17', 16, 'O+', 'Antiguo Repitente', NULL, 'Calle 15 SUR # 10-05', 'Timanco', 'Comuna 6', 2, 'Nueva EPS', NULL, '3102000013', 'fam.cabrera@gmail.com', '3113000013', '3124000013', 1, '2026-10-16 12:46:40'),
(33, 'TI', '1081000014', 'Gabriela Rivera Bahamón', '2011-01-29', 15, 'AB-', 'Antiguo', NULL, 'Carrera 12 # 18A-22', 'Diego de Ospina', 'Comuna 4', 2, 'Asmet Salud', '6088712314', '3102000014', 'fam.rivera@gmail.com', '3113000014', '3124000014', 1, '2026-10-16 12:46:40'),
(34, 'TI', '1081000015', 'Matías José Delgado Cuéllar', '2013-03-08', 13, 'O-', 'Nuevo', 'I.E. Ceinar', 'Calle 6 # 14-35', 'Toma', 'Comuna 4', 3, 'Sanitas', NULL, '3102000015', 'fam.delgado@gmail.com', '3113000015', '3124000015', 1, '2026-10-16 12:46:40'),
(35, 'TI', '1081000016', 'Antonella Fierro Losada', '2012-04-22', 14, 'O+', 'Antiguo', NULL, 'Carrera 40 # 15-80', 'El Vergel', 'Comuna 5', 3, 'Coosalud', '6088712316', '3102000016', 'fam.fierro@gmail.com', '3113000016', '3124000016', 1, '2026-10-16 12:46:40'),
(36, 'TI', '1081000017', 'Jerónimo Sánchez Andrade', '2010-08-11', 16, 'A+', 'Antiguo', NULL, 'Calle 11 # 1G-20', 'San Martín', 'Comuna 7', 2, 'Nueva EPS', NULL, '3102000017', 'fam.sanchez@gmail.com', '3113000017', '3124000017', 0, '2026-10-16 12:46:40'),
(37, 'TI', '1081000018', 'Mariana Ospina Bonilla', '2011-11-30', 14, 'O+', 'Nuevo Repitente', 'I.E. Técnico Superior', 'Carrera 7A # 25-10', 'Cándido', 'Comuna 1', 3, 'Sura', '6088712318', '3102000018', 'fam.ospina@gmail.com', '3113000018', '3124000018', 1, '2026-10-16 12:46:40'),
(38, 'TI', '1081000019', 'Thiago Alejandro Meléndez Silva', '2013-07-07', 13, 'B+', 'Antiguo', NULL, 'Manzana E Casa 18', 'Acapulco', 'Comuna 8', 1, 'Asmet Salud', NULL, '3102000019', 'fam.melendez@gmail.com', '3113000019', '3124000019', 1, '2026-10-16 12:46:40'),
(39, 'TI', '1081000020', 'Guadalupe Polanía Lara', '2012-02-17', 14, 'O+', 'Antiguo', NULL, 'Calle 20 # 35-12', 'Buganviles', 'Comuna 5', 3, 'Sanitas', '6088712320', '3102000020', 'fam.polania@gmail.com', '3113000020', '3124000020', 1, '2026-10-16 12:46:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fichas_caracterizacion`
--

CREATE TABLE `fichas_caracterizacion` (
  `id_ficha` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `id_usuario_registro` int(11) NOT NULL,
  `fecha_diligenciamiento` date NOT NULL,
  `acudiente_nombre` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `acudiente_parentesco` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `medio_transporte` enum('Servicio Público','Vehículo familiar','Bicicleta','Motocicleta personal','Caminando','Pago Ruta') COLLATE utf8mb4_unicode_ci NOT NULL,
  `acompanamiento_jornada_contraria` enum('Hermanos','Uno de mis padres','Familiares','Otros no familiares','Normalmente solo') COLLATE utf8mb4_unicode_ci NOT NULL,
  `observaciones_orientacion` text COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `fichas_caracterizacion`
--

INSERT INTO `fichas_caracterizacion` (`id_ficha`, `id_estudiante`, `id_curso`, `id_usuario_registro`, `fecha_diligenciamiento`, `acudiente_nombre`, `acudiente_parentesco`, `medio_transporte`, `acompanamiento_jornada_contraria`, `observaciones_orientacion`) VALUES
(3, 3, 3, 3, '2026-02-15', 'Rosa Elena Charry', 'Abuela', 'Caminando', 'Normalmente solo', 'Vive en zona de asentamiento, se prioriza remisión a programas de bienestar y refrigerio.'),
(10, 10, 1, 1, '2026-02-10', 'Carmen Elena Meza', 'Madre', 'Caminando', 'Uno de mis padres', 'Estudiante con buen rendimiento académico y alto liderazgo.'),
(11, 11, 2, 2, '2026-02-12', 'Héctor Fabio Benavides', 'Padre', 'Servicio Público', 'Normalmente solo', 'Estudiante nuevo, requiere adaptación al modelo técnico.'),
(12, 12, 3, 3, '2026-02-15', 'Martha Lucía Castro', 'Madre', 'Bicicleta', 'Hermanos', 'Manifiesta interés por actividades deportivas y extracurriculares.'),
(20, 20, 1, 1, '2026-02-10', 'Carlos Gómez', 'Padre', 'Vehículo familiar', 'Uno de mis padres', 'Estudiante disciplinado.'),
(21, 21, 1, 1, '2026-02-10', 'Martha Parra', 'Madre', 'Servicio Público', 'Hermanos', 'Participa activamente en clase.'),
(22, 22, 1, 2, '2026-02-11', 'Jorge Castro', 'Padre', 'Motocicleta personal', 'Familiares', 'Adaptación positiva al cambio de colegio.'),
(23, 23, 1, 2, '2026-02-11', 'Sonia Lasso', 'Madre', 'Caminando', 'Uno de mis padres', 'Sin novedades particulares.'),
(24, 24, 2, 1, '2026-02-12', 'Pedro Medina', 'Padre', 'Servicio Público', 'Normalmente solo', 'Se sugiere refuerzo en área de matemáticas.'),
(25, 25, 2, 1, '2026-02-12', 'Claudia Chávarro', 'Madre', 'Pago Ruta', 'Uno de mis padres', 'Muestra talento en áreas artísticas.'),
(26, 26, 2, 2, '2026-02-13', 'Gonzalo Gutiérrez', 'Padre', 'Caminando', 'Familiares', 'Acudiente asiste puntualmente a citaciones.'),
(27, 27, 2, 3, '2026-02-13', 'Luz Mery Tovar', 'Madre', 'Bicicleta', 'Uno de mis padres', 'Excelente desempeño académico.'),
(28, 28, 3, 1, '2026-02-14', 'Rocío Rivas', 'Madre', 'Servicio Público', 'Normalmente solo', 'Familia en proceso de reubicación.'),
(29, 29, 3, 2, '2026-02-14', 'Edgar Beltrán', 'Padre', 'Vehículo familiar', 'Uno de mis padres', 'Asiste a cursos extracurriculares.'),
(30, 30, 3, 2, '2026-02-15', 'Patricia Peña', 'Madre', 'Motocicleta personal', 'Hermanos', 'Buen comportamiento grupal.'),
(31, 31, 3, 3, '2026-02-15', 'Nohora Hermosa', 'Madre', 'Caminando', 'Otros no familiares', 'Requiere seguimiento por trabajo del acudiente.'),
(32, 32, 4, 1, '2026-02-16', 'Jaime Cabrera', 'Padre', 'Servicio Público', 'Normalmente solo', 'Compromiso de nivelación firmado por acudiente.'),
(33, 33, 4, 1, '2026-02-16', 'Gloria Bahamón', 'Madre', 'Bicicleta', 'Uno de mis padres', 'Integrada al grupo de danza escolar.'),
(34, 34, 4, 2, '2026-02-17', 'Mauricio Delgado', 'Padre', 'Motocicleta personal', 'Familiares', 'Interés por el área de sistemas.'),
(35, 35, 4, 2, '2026-02-17', 'Inés Losada', 'Abuela', 'Pago Ruta', 'Familiares', 'El hogar cuenta con apoyo de la abuela paterna.'),
(36, 36, 1, 3, '2026-02-18', 'Hernán Sánchez', 'Padre', 'Caminando', 'Hermanos', 'Destacado rendimiento en educación física.'),
(37, 37, 2, 3, '2026-02-18', 'Cielo Bonilla', 'Madre', 'Servicio Público', 'Normalmente solo', 'Población con atención prioritaria de orientación.'),
(38, 38, 3, 1, '2026-02-19', 'Yolanda Silva', 'Madre', 'Caminando', 'Normalmente solo', 'Beneficiario del programa PAE.'),
(39, 39, 4, 2, '2026-02-19', 'Óscar Polanía', 'Padre', 'Vehículo familiar', 'Uno de mis padres', 'Sin antecedentes disciplinares.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ficha_catalogos_detalle`
--

CREATE TABLE `ficha_catalogos_detalle` (
  `id_ficha` int(11) NOT NULL,
  `id_catalogo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ficha_catalogos_detalle`
--

INSERT INTO `ficha_catalogos_detalle` (`id_ficha`, `id_catalogo`) VALUES
(20, 17),
(21, 24),
(22, 24),
(23, 20),
(24, 25),
(25, 20),
(26, 24),
(27, 18),
(28, 26),
(28, 29),
(29, 17),
(30, 24),
(31, 32),
(32, 24),
(33, 21),
(34, 17),
(35, 24),
(36, 21),
(37, 30),
(38, 33),
(39, 18);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grados_cursos`
--

CREATE TABLE `grados_cursos` (
  `id_curso` int(11) NOT NULL,
  `id_sede` int(11) NOT NULL,
  `nombre_curso` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jornada` enum('Mañana','Tarde','Única') COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `grados_cursos`
--

INSERT INTO `grados_cursos` (`id_curso`, `id_sede`, `nombre_curso`, `jornada`) VALUES
(1, 1, '1106', 'Mañana'),
(2, 1, '1002', 'Tarde'),
(3, 2, '703', 'Mañana'),
(4, 3, '901', 'Tarde');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesor_cursos`
--

CREATE TABLE `profesor_cursos` (
  `id_usuario` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `profesor_cursos`
--

INSERT INTO `profesor_cursos` (`id_usuario`, `id_curso`) VALUES
(2, 1),
(3, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recursos_tecnologicos_hogar`
--

CREATE TABLE `recursos_tecnologicos_hogar` (
  `id_ficha` int(11) NOT NULL,
  `tiene_computador` tinyint(1) NOT NULL DEFAULT 0,
  `tiene_internet` tinyint(1) NOT NULL DEFAULT 0,
  `tiene_tv_suscripcion` tinyint(1) NOT NULL DEFAULT 0,
  `tiene_celular_personal` tinyint(1) NOT NULL DEFAULT 0,
  `cuenta_servicios_publicos` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `recursos_tecnologicos_hogar`
--

INSERT INTO `recursos_tecnologicos_hogar` (`id_ficha`, `tiene_computador`, `tiene_internet`, `tiene_tv_suscripcion`, `tiene_celular_personal`, `cuenta_servicios_publicos`) VALUES
(20, 1, 1, 1, 1, 1),
(21, 1, 1, 1, 1, 1),
(22, 1, 1, 0, 1, 1),
(23, 1, 1, 1, 0, 1),
(24, 0, 0, 0, 1, 1),
(25, 1, 1, 1, 1, 1),
(26, 1, 1, 1, 1, 1),
(27, 1, 1, 1, 1, 1),
(28, 0, 1, 0, 1, 1),
(29, 1, 1, 1, 1, 1),
(30, 1, 1, 1, 1, 1),
(31, 0, 0, 0, 1, 1),
(32, 0, 1, 1, 1, 1),
(33, 1, 1, 0, 1, 1),
(34, 1, 1, 1, 1, 1),
(35, 1, 1, 1, 1, 1),
(36, 1, 1, 0, 1, 1),
(37, 0, 0, 0, 0, 1),
(38, 0, 1, 0, 1, 1),
(39, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salud_estudiante`
--

CREATE TABLE `salud_estudiante` (
  `id_ficha` int(11) NOT NULL,
  `enfermedad_diagnosticada` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recibe_tratamiento_medico` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `consume_medicamento` text COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `salud_estudiante`
--

INSERT INTO `salud_estudiante` (`id_ficha`, `enfermedad_diagnosticada`, `recibe_tratamiento_medico`, `consume_medicamento`) VALUES
(20, NULL, NULL, NULL),
(21, 'Miopía', 'Control óptico semestral', 'No'),
(22, NULL, NULL, NULL),
(23, 'Asma bronquial', 'Tratamiento neumológico', 'Inhalador Salbutamol'),
(24, NULL, NULL, NULL),
(25, NULL, NULL, NULL),
(26, 'Gastritis crónica', 'Valoración pediátrica', 'Omeprazol'),
(27, NULL, NULL, NULL),
(28, NULL, NULL, NULL),
(29, 'Hipoglucemia', 'Dieta supervisada', 'No'),
(30, NULL, NULL, NULL),
(31, 'Dermatitis atópica', 'Tratamiento dermatológico', 'Cremas hidratantes formuladas'),
(32, NULL, NULL, NULL),
(33, NULL, NULL, NULL),
(34, 'Migraña episódica', 'Control por medicina general', 'Acetaminofén según dolor'),
(35, NULL, NULL, NULL),
(36, NULL, NULL, NULL),
(37, NULL, NULL, NULL),
(38, 'Anemia leve', 'Suplementación nutricional', 'Sulfato ferroso'),
(39, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sedes`
--

CREATE TABLE `sedes` (
  `id_sede` int(11) NOT NULL,
  `nombre_sede` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sedes`
--

INSERT INTO `sedes` (`id_sede`, `nombre_sede`) VALUES
(1, 'Central'),
(2, 'Elena Lara'),
(3, 'Floresmiro Azuero'),
(4, 'Los Mártires');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre_completo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correo` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` enum('Administrador','Profesor') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Profesor',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `creado_en` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_completo`, `correo`, `password`, `rol`, `activo`, `creado_en`) VALUES
(1, 'Carlos Eduardo Pérez', 'admin@tecnicosuperior.edu.co', '123', 'Administrador', 1, '2026-09-04 13:43:54'),
(2, 'Martha Lucía Gómez', 'mgomez@tecnicosuperior.edu.co', '123', 'Profesor', 1, '2026-09-04 13:43:54'),
(3, 'Jorge Iván Ramírez', 'jramirez@tecnicosuperior.edu.co', '123', 'Profesor', 0, '2026-09-04 13:43:54');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividades_extracurriculares`
--
ALTER TABLE `actividades_extracurriculares`
  ADD PRIMARY KEY (`id_ficha`);

--
-- Indices de la tabla `catalogos_caracterizacion`
--
ALTER TABLE `catalogos_caracterizacion`
  ADD PRIMARY KEY (`id_catalogo`);

--
-- Indices de la tabla `datos_familiares_hogar`
--
ALTER TABLE `datos_familiares_hogar`
  ADD PRIMARY KEY (`id_ficha`);

--
-- Indices de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD PRIMARY KEY (`id_estudiante`),
  ADD UNIQUE KEY `numero_documento` (`numero_documento`);

--
-- Indices de la tabla `fichas_caracterizacion`
--
ALTER TABLE `fichas_caracterizacion`
  ADD PRIMARY KEY (`id_ficha`),
  ADD KEY `fk_ficha_estudiante` (`id_estudiante`),
  ADD KEY `fk_ficha_curso` (`id_curso`),
  ADD KEY `fk_ficha_usuario` (`id_usuario_registro`);

--
-- Indices de la tabla `ficha_catalogos_detalle`
--
ALTER TABLE `ficha_catalogos_detalle`
  ADD PRIMARY KEY (`id_ficha`,`id_catalogo`),
  ADD KEY `fk_detalle_catalogo` (`id_catalogo`);

--
-- Indices de la tabla `grados_cursos`
--
ALTER TABLE `grados_cursos`
  ADD PRIMARY KEY (`id_curso`),
  ADD KEY `fk_cursos_sede` (`id_sede`);

--
-- Indices de la tabla `profesor_cursos`
--
ALTER TABLE `profesor_cursos`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_curso` (`id_curso`);

--
-- Indices de la tabla `recursos_tecnologicos_hogar`
--
ALTER TABLE `recursos_tecnologicos_hogar`
  ADD PRIMARY KEY (`id_ficha`);

--
-- Indices de la tabla `salud_estudiante`
--
ALTER TABLE `salud_estudiante`
  ADD PRIMARY KEY (`id_ficha`);

--
-- Indices de la tabla `sedes`
--
ALTER TABLE `sedes`
  ADD PRIMARY KEY (`id_sede`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `catalogos_caracterizacion`
--
ALTER TABLE `catalogos_caracterizacion`
  MODIFY `id_catalogo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  MODIFY `id_estudiante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de la tabla `fichas_caracterizacion`
--
ALTER TABLE `fichas_caracterizacion`
  MODIFY `id_ficha` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de la tabla `grados_cursos`
--
ALTER TABLE `grados_cursos`
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `sedes`
--
ALTER TABLE `sedes`
  MODIFY `id_sede` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividades_extracurriculares`
--
ALTER TABLE `actividades_extracurriculares`
  ADD CONSTRAINT `fk_actividades_ficha` FOREIGN KEY (`id_ficha`) REFERENCES `fichas_caracterizacion` (`id_ficha`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `datos_familiares_hogar`
--
ALTER TABLE `datos_familiares_hogar`
  ADD CONSTRAINT `fk_familiar_ficha` FOREIGN KEY (`id_ficha`) REFERENCES `fichas_caracterizacion` (`id_ficha`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `fichas_caracterizacion`
--
ALTER TABLE `fichas_caracterizacion`
  ADD CONSTRAINT `fk_ficha_curso` FOREIGN KEY (`id_curso`) REFERENCES `grados_cursos` (`id_curso`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ficha_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiantes` (`id_estudiante`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ficha_usuario` FOREIGN KEY (`id_usuario_registro`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `ficha_catalogos_detalle`
--
ALTER TABLE `ficha_catalogos_detalle`
  ADD CONSTRAINT `fk_detalle_catalogo` FOREIGN KEY (`id_catalogo`) REFERENCES `catalogos_caracterizacion` (`id_catalogo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detalle_ficha` FOREIGN KEY (`id_ficha`) REFERENCES `fichas_caracterizacion` (`id_ficha`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `grados_cursos`
--
ALTER TABLE `grados_cursos`
  ADD CONSTRAINT `fk_cursos_sede` FOREIGN KEY (`id_sede`) REFERENCES `sedes` (`id_sede`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
