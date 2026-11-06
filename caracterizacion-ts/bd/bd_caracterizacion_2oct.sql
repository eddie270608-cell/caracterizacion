-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 02-10-2026 a las 14:48:24
-- Versión del servidor: 8.4.3
-- Versión de PHP: 8.3.30

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
CREATE DATABASE IF NOT EXISTS `bd_caracterizacion_tecnico` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `bd_caracterizacion_tecnico`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividades_extracurriculares`
--

CREATE TABLE `actividades_extracurriculares` (
  `id_ficha` int NOT NULL,
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `catalogos_caracterizacion`
--

CREATE TABLE `catalogos_caracterizacion` (
  `id_catalogo` int NOT NULL,
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
  `id_ficha` int NOT NULL,
  `padre_nombres` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `padre_escolaridad` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `padre_edad` tinyint UNSIGNED DEFAULT NULL,
  `padre_ocupacion` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `padre_tipo_ocupacion` enum('Independiente','Empleado') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `madre_nombres` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `madre_escolaridad` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `madre_edad` tinyint UNSIGNED DEFAULT NULL,
  `madre_ocupacion` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `madre_tipo_ocupacion` enum('Independiente','Empleado') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ingresos_hogar` enum('Menos de 1 SMLV','Entre 1 y 2 SMLV','Más de 2 SMLV') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenencia_vivienda` enum('Propia','Arrendada','De familiares','Otro') COLLATE utf8mb4_unicode_ci NOT NULL,
  `convive_con` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `es_hijo_unico` tinyint(1) NOT NULL DEFAULT '0',
  `hermanos_en_colegio` tinyint(1) NOT NULL DEFAULT '0',
  `total_personas_hogar` tinyint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiantes`
--

CREATE TABLE `estudiantes` (
  `id_estudiante` int NOT NULL,
  `tipo_documento` enum('RC','TI','CC','Otro') COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_documento` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombres_apellidos` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `edad` tinyint UNSIGNED NOT NULL,
  `grupo_sanguineo` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_estudiante` enum('Antiguo','Antiguo Repitente','Nuevo','Nuevo Repitente') COLLATE utf8mb4_unicode_ci NOT NULL,
  `colegio_procedencia` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion_residencia` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barrio` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comuna` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estrato` tinyint UNSIGNED NOT NULL,
  `servicio_salud` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono_fijo` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono_movil` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_padres` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tel_movil_padre` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tel_movil_madre` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `estudiantes`
--

INSERT INTO `estudiantes` (`id_estudiante`, `tipo_documento`, `numero_documento`, `nombres_apellidos`, `fecha_nacimiento`, `edad`, `grupo_sanguineo`, `tipo_estudiante`, `colegio_procedencia`, `direccion_residencia`, `barrio`, `comuna`, `estrato`, `servicio_salud`, `telefono_fijo`, `telefono_movil`, `email_padres`, `tel_movil_padre`, `tel_movil_madre`, `activo`, `creado_en`) VALUES
(3, 'RC', '1117854210', 'Santiago Andrés Medina Charry', '2013-02-08', 13, 'B+', 'Antiguo Repitente', NULL, 'Manzana D Casa 12', 'Los Mártires', 'Comuna 8', 1, 'Asmet Salud', '6088765432', '3201478523', NULL, NULL, '3219632587', 0, '2026-09-04 13:46:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fichas_caracterizacion`
--

CREATE TABLE `fichas_caracterizacion` (
  `id_ficha` int NOT NULL,
  `id_estudiante` int NOT NULL,
  `id_curso` int NOT NULL,
  `id_usuario_registro` int NOT NULL,
  `fecha_diligenciamiento` date NOT NULL,
  `acudiente_nombre` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `acudiente_parentesco` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `medio_transporte` enum('Servicio Público','Vehículo familiar','Bicicleta','Motocicleta personal','Caminando','Pago Ruta') COLLATE utf8mb4_unicode_ci NOT NULL,
  `acompanamiento_jornada_contraria` enum('Hermanos','Uno de mis padres','Familiares','Otros no familiares','Normalmente solo') COLLATE utf8mb4_unicode_ci NOT NULL,
  `observaciones_orientacion` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `fichas_caracterizacion`
--

INSERT INTO `fichas_caracterizacion` (`id_ficha`, `id_estudiante`, `id_curso`, `id_usuario_registro`, `fecha_diligenciamiento`, `acudiente_nombre`, `acudiente_parentesco`, `medio_transporte`, `acompanamiento_jornada_contraria`, `observaciones_orientacion`) VALUES
(3, 3, 3, 3, '2026-02-15', 'Rosa Elena Charry', 'Abuela', 'Caminando', 'Normalmente solo', 'Vive en zona de asentamiento, se prioriza remisión a programas de bienestar y refrigerio.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ficha_catalogos_detalle`
--

CREATE TABLE `ficha_catalogos_detalle` (
  `id_ficha` int NOT NULL,
  `id_catalogo` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grados_cursos`
--

CREATE TABLE `grados_cursos` (
  `id_curso` int NOT NULL,
  `id_sede` int NOT NULL,
  `nombre_curso` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jornada` enum('Mañana','Tarde','Única') COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `grados_cursos`
--

INSERT INTO `grados_cursos` (`id_curso`, `id_sede`, `nombre_curso`, `jornada`) VALUES
(1, 1, '1106', 'Mañana'),
(2, 1, '1002', 'Tarde'),
(3, 2, '703', 'Mañana');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesor_cursos`
--

CREATE TABLE `profesor_cursos` (
  `id_usuario` int NOT NULL,
  `id_curso` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `profesor_cursos`
--

INSERT INTO `profesor_cursos` (`id_usuario`, `id_curso`) VALUES
(2, 3),
(3, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recursos_tecnologicos_hogar`
--

CREATE TABLE `recursos_tecnologicos_hogar` (
  `id_ficha` int NOT NULL,
  `tiene_computador` tinyint(1) NOT NULL DEFAULT '0',
  `tiene_internet` tinyint(1) NOT NULL DEFAULT '0',
  `tiene_tv_suscripcion` tinyint(1) NOT NULL DEFAULT '0',
  `tiene_celular_personal` tinyint(1) NOT NULL DEFAULT '0',
  `cuenta_servicios_publicos` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salud_estudiante`
--

CREATE TABLE `salud_estudiante` (
  `id_ficha` int NOT NULL,
  `enfermedad_diagnosticada` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recibe_tratamiento_medico` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `consume_medicamento` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sedes`
--

CREATE TABLE `sedes` (
  `id_sede` int NOT NULL,
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
  `id_usuario` int NOT NULL,
  `nombre_completo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correo` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` enum('Administrador','Profesor') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Profesor',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP
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
  MODIFY `id_catalogo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  MODIFY `id_estudiante` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `fichas_caracterizacion`
--
ALTER TABLE `fichas_caracterizacion`
  MODIFY `id_ficha` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `grados_cursos`
--
ALTER TABLE `grados_cursos`
  MODIFY `id_curso` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `sedes`
--
ALTER TABLE `sedes`
  MODIFY `id_sede` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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

--
-- Filtros para la tabla `profesor_cursos`
--
ALTER TABLE `profesor_cursos`
  ADD CONSTRAINT `profesor_cursos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `profesor_cursos_ibfk_2` FOREIGN KEY (`id_curso`) REFERENCES `grados_cursos` (`id_curso`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `recursos_tecnologicos_hogar`
--
ALTER TABLE `recursos_tecnologicos_hogar`
  ADD CONSTRAINT `fk_recursos_ficha` FOREIGN KEY (`id_ficha`) REFERENCES `fichas_caracterizacion` (`id_ficha`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `salud_estudiante`
--
ALTER TABLE `salud_estudiante`
  ADD CONSTRAINT `fk_salud_ficha` FOREIGN KEY (`id_ficha`) REFERENCES `fichas_caracterizacion` (`id_ficha`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
