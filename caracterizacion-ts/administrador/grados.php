<?php
session_start();
require_once "../inc/conexion.php";

// Validar que el usuario haya iniciado sesión y sea Administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Administrador') {
    header("Location: ../index.php");
    exit();
}

$conexion = conectarBD();$mensaje_exito = "";
$mensaje_error = "";

// PROCESAR COPIA DE SEGURIDAD DE LA BASE DE DATOS
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['hacer_backup'])) {$tables = [];
    $result = mysqli_query($conexion, "SHOW TABLES");
    while ($row = mysqli_fetch_row($result)) {$tables[] = $row[0]; }$sql_contenido = "-- Copia de seguridad de la base de datos - IE Técnico Superior\n";
    $sql_contenido .= "-- Fecha y Hora: " . date('Y-m-d H:i:s') . "\n\n";
    $sql_contenido .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
    
    foreach ($tables as$table) {
        $res_create = mysqli_query($conexion, "SHOW CREATE TABLE `$table`");
        if ($res_create) {$row_create = mysqli_fetch_row($res_create);$sql_contenido .= "DROP TABLE IF EXISTS `$table`;\n";
            $sql_contenido .=$row_create[1] . ";\n\n";
        }
        
        $res_data = mysqli_query($conexion, "SELECT * FROM `$table`");
        if ($res_data && mysqli_num_rows($res_data) > 0) {
            while ($row_data = mysqli_fetch_assoc($res_data)) {$fields = array_keys($row_data);$values = array_values($row_data);$escaped_values = array_map(function($val) use ($conexion) {
                    if ($val === null) return "NULL";
                    return "'" . mysqli_real_escape_string($conexion,$val) . "'";
                }, $values);$sql_contenido .= "INSERT INTO `$table` (`" . implode("`, `", $fields) . "`) VALUES (" . implode(", ", $escaped_values) . ");\n";
            }
            $sql_contenido .= "\n";
        }
    }
    
    $sql_contenido .= "SET FOREIGN_KEY_CHECKS = 1;\n";
    
    $nombre_archivo = "backup_ie_tecnico_superior_" . date('Y-m-d_H-i-s') . ".sql";
    
    header('Content-Type: application/sql; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $nombre_archivo);
    echo $sql_contenido;
    exit();
}

// PROCESAR CREACIÓN DE UNA NUEVA SEDE
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['crear_sede'])) {
    $nombre_sede = trim($_POST['nombre_sede']);

    if (!empty($nombre_sede)) {
        $sql_add_s = "INSERT INTO sedes (nombre_sede) VALUES (?)";
        $stmt = mysqli_prepare($conexion, $sql_add_s);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $nombre_sede);
            if (mysqli_stmt_execute($stmt)) {
                $mensaje_exito = "¡Sede $nombre_sede creada exitosamente!";
            } else {
                $mensaje_error = "Error al crear la sede.";
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $mensaje_error = "Por favor completa el nombre de la sede.";
    }
}

// PROCESAR ELIMINACIÓN DE UNA SEDE
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_sede'])) {
    $id_sede_del = intval($_POST['id_sede_eliminar']);

    if ($id_sede_del > 0) {
        $sql_del_s = "DELETE FROM sedes WHERE id_sede = ?";
        $stmt = mysqli_prepare($conexion, $sql_del_s);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id_sede_del);
            if (mysqli_stmt_execute($stmt)) {
                $mensaje_exito = "¡Sede eliminada correctamente!";
                if (isset($_GET['sede']) && intval($_GET['sede']) === $id_sede_del) {
                    header("Location: grados.php");
                    exit();
                }
            } else {
                if (mysqli_errno($conexion) == 1451) {
                    $mensaje_error = "No se puede eliminar esta sede porque tiene cursos asociados.";
                } else {
                    $mensaje_error = "Error al intentar eliminar la sede: " . mysqli_error($conexion);
                }
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// 1. PROCESAR CREACIÓN DE UN NUEVO CURSO
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['crear_curso'])) {
    $id_sede      = intval($_POST['id_sede']);
    $nombre_curso = trim($_POST['nombre_curso']);
    $jornada      = trim($_POST['jornada']);

    if ($id_sede > 0 && !empty($nombre_curso) && !empty($jornada)) {
        $sql_add = "INSERT INTO grados_cursos (id_sede, nombre_curso, jornada) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conexion, $sql_add);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "iss", $id_sede, $nombre_curso, $jornada);
            if (mysqli_stmt_execute($stmt)) {
                $mensaje_exito = "¡Curso $nombre_curso creado exitosamente!";
            } else {
                $mensaje_error = "Error al crear el curso.";
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $mensaje_error = "Por favor completa todos los campos del curso.";
    }
}

// 2. PROCESAR EDICIÓN DE UN CURSO
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editar_curso'])) {
    $id_curso     = intval($_POST['id_curso']);
    $nombre_curso = trim($_POST['nombre_curso']);
    $jornada      = trim($_POST['jornada']);

    if ($id_curso > 0 && !empty($nombre_curso) && !empty($jornada)) {
        $sql_edit = "UPDATE grados_cursos SET nombre_curso = ?, jornada = ? WHERE id_curso = ?";
        $stmt = mysqli_prepare($conexion, $sql_edit);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssi", $nombre_curso, $jornada, $id_curso);
            if (mysqli_stmt_execute($stmt)) {
                $mensaje_exito = "¡Curso actualizado correctamente!";
            } else {
                $mensaje_error = "Error al actualizar el curso.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// 3. PROCESAR ELIMINACIÓN DE UN CURSO
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_curso'])) {
    $id_curso = intval($_POST['id_curso']);

    if ($id_curso > 0) {
        $sql_del = "DELETE FROM grados_cursos WHERE id_curso = ?";
        $stmt = mysqli_prepare($conexion, $sql_del);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id_curso);
            if (mysqli_stmt_execute($stmt)) {
                $mensaje_exito = "¡Curso eliminado correctamente!";
            } else {
                if (mysqli_errno($conexion) == 1451) {
                    $mensaje_error = "No se puede eliminar este curso porque ya tiene estudiantes o docentes asociados a él.";
                } else {
                    $mensaje_error = "Error al intentar eliminar el curso: " . mysqli_error($conexion);
                }
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// 4. OBTENER TODAS LAS SEDES
$sedes = [];
$res_sedes = mysqli_query($conexion, "SELECT id_sede, nombre_sede FROM sedes ORDER BY id_sede ASC");
if ($res_sedes) {
    while ($row = mysqli_fetch_assoc($res_sedes)) {
        $sedes[] = $row;
    }
}

// 5. CAPTURAR SEDE SELECCIONADA Y FILTROS
$sede_seleccionada = isset($_GET['sede']) ? intval($_GET['sede']) : NULL;
$filtro_jornada    = isset($_GET['jornada']) ? trim($_GET['jornada']) : '';
$filtro_grado      = isset($_GET['grado']) ? trim($_GET['grado']) : '';

$info_sede_actual  = NULL;
$cursos_sede       = [];

if ($sede_seleccionada !== NULL) {
    $stmt_s = mysqli_prepare($conexion, "SELECT id_sede, nombre_sede FROM sedes WHERE id_sede = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt_s, "i", $sede_seleccionada);
    mysqli_stmt_execute($stmt_s);
    $res_s = mysqli_stmt_get_result($stmt_s);
    $info_sede_actual = mysqli_fetch_assoc($res_s);
    mysqli_stmt_close($stmt_s);

    if ($info_sede_actual) {
        $query_c = "SELECT id_curso, nombre_curso, jornada FROM grados_cursos WHERE id_sede = ?";
        $types = "i";
        $params = [$sede_seleccionada];

        if (!empty($filtro_jornada)) {
            $query_c .= " AND jornada = ?";
            $types .= "s";
            $params[] = $filtro_jornada;
        }

        if (!empty($filtro_grado)) {
            $query_c .= " AND nombre_curso LIKE ?";
            $types .= "s";
            $params[] = "%" . $filtro_grado . "%";
        }

        $query_c .= " ORDER BY CAST(nombre_curso AS UNSIGNED) ASC, jornada ASC";

        $stmt_c = mysqli_prepare($conexion, $query_c);
        if ($stmt_c) {
            mysqli_stmt_bind_param($stmt_c, $types, ...$params);
            mysqli_stmt_execute($stmt_c);
            $res_c = mysqli_stmt_get_result($stmt_c);
            while ($c = mysqli_fetch_assoc($res_c)) {
                $cursos_sede[] = $c;
            }
            mysqli_stmt_close($stmt_c);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Grados y Cursos - IE Técnico Superior</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary-color: #14532d;      
            --primary-hover: #166534;    
            --accent-color: #15803d;     
            --bg-color: #f8fafc;         
        }
        body { 
            background-color: var(--bg-color); 
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; 
            color: #334155;
        }
        .navbar-brand {
            letter-spacing: 0.5px;
        }
        .card {
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        }
        .card-header-title { 
            background: linear-gradient(135deg, #14532d 0%, #052e16 100%); 
            color: white; 
        }
        .card-sede { 
            transition: all 0.25s ease-in-out; 
            border: 2px solid #e2e8f0; 
            background: #ffffff;
        }
        .card-sede:hover { 
            transform: translateY(-4px); 
            border-color: var(--accent-color); 
            box-shadow: 0 10px 20px rgba(21, 128, 61, 0.08); 
        }
        .card-sede.active { 
            border-color: var(--accent-color); 
            background-color: #f0fdf4; 
            box-shadow: 0 4px 12px rgba(21, 128, 61, 0.12);
        }
        .curso-card {
            transition: all 0.2s ease-in-out;
            border: 1px solid #e2e8f0;
            background: #ffffff;
        }
        .curso-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.06);
            border-color: #cbd5e1;
        }
        .btn-institutional {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #ffffff;
        }
        .btn-institutional:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            color: #ffffff;
        }
        .btn-light-institutional {
            background-color: #ffffff;
            color: var(--primary-color);
            border: none;
            transition: all 0.2s ease-in-out;
        }
        .btn-light-institutional:hover {
            background-color: #f0fdf4;
            color: var(--primary-hover);
        }
        .form-control:focus, .form-select:focus { 
            border-color: var(--accent-color); 
            box-shadow: 0 0 0 0.25rem rgba(21, 128, 61, 0.15); 
        }
        .jornada-section-header {
            background: #ffffff;
            border-left: 4px solid var(--accent-color);
            border-radius: 0.5rem;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm py-3" style="background-color: var(--primary-color) !important;">
    <div class="container">
        <a class="navbar-brand fw-bold text-white" href="panel_admin.php">
            <i class="bi bi-mortarboard-fill me-2"></i> IE Técnico Superior <span class="fw-light fs-6 opacity-75">| Neiva</span>
        </a>
        <a href="panel_admin.php" class="btn btn-outline-light btn-sm fw-semibold rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Volver al Panel
        </a>
    </div>
</nav>

<div class="container py-5 flex-grow-1">

    <?php if (!empty($mensaje_exito)): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i><?php echo $mensaje_exito; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($mensaje_error)): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-4 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i><?php echo $mensaje_error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- TARJETA CABECERA CON COPIA DE SEGURIDAD -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-header card-header-title p-4 px-md-5 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h3 class="fw-bold mb-1"><i class="bi bi-diagram-3-fill me-2"></i>Gestión de Grados y Cursos</h3>
                <p class="mb-0 small opacity-75">Administra las sedes educativas, organiza los cursos por jornada y filtra por grados académicos.</p>
            </div>
            <div>
                <button type="button" class="btn btn-light-institutional fw-semibold rounded-pill px-4 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalBackup">
                    <i class="bi bi-cloud-arrow-down-fill me-1"></i> Copia de Seguridad
                </button>
            </div>
        </div>
    </div>

    <!-- SECCIÓN DE SEDES -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-building me-2 text-success"></i>Seleccione la Sede Educativa</h5>
        <button type="button" class="btn btn-institutional fw-semibold rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevaSede">
            <i class="bi bi-plus-lg me-1"></i> Agregar Sede
        </button>
    </div>

    <div class="row g-3 mb-5">
        <?php foreach ($sedes as $s): ?>
            <?php $isActive = ($sede_seleccionada === intval($s['id_sede'])); ?>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="card card-sede h-100 p-3 shadow-sm rounded-4 position-relative <?php echo $isActive ? 'active' : ''; ?>">
                    
                    <!-- Botón para eliminar sede -->
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-circle position-absolute top-0 end-0 m-2 z-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEliminarSede<?php echo $s['id_sede']; ?>" title="Eliminar Sede" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-trash-fill fs-6"></i>
                    </button>

                    <a href="grados.php?sede=<?php echo $s['id_sede']; ?>" class="text-decoration-none stretched-link">
                        <div class="card-body text-center py-2">
                            <div class="rounded-circle d-inline-flex justify-content-center align-items-center mb-3 shadow-sm" style="width: 55px; height: 55px; background-color: #dcfce7; color: var(--accent-color);">
                                <i class="bi bi-geo-alt-fill fs-3"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Sede <?php echo htmlspecialchars($s['nombre_sede']); ?></h6>
                            <span class="badge rounded-pill mt-2 px-3 py-1 text-white shadow-sm" style="background-color: var(--accent-color);">Gestionar</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- MODAL ELIMINAR SEDE -->
            <div class="modal fade" id="modalEliminarSede<?php echo $s['id_sede']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4 border-0 shadow">
                        <div class="modal-header bg-danger text-white rounded-top-4">
                            <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmar Eliminación</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST" action="">
                            <div class="modal-body p-4 text-center">
                                <input type="hidden" name="id_sede_eliminar" value="<?php echo $s['id_sede']; ?>">
                                <i class="bi bi-building-x text-danger display-4 mb-3"></i>
                                <p class="mb-1 fs-5 text-dark">¿Estás seguro de eliminar la sede <strong><?php echo htmlspecialchars($s['nombre_sede']); ?></strong>?</p>
                                <small class="text-muted">No se podrá eliminar si cuenta con cursos asociados.</small>
                            </div>
                            <div class="modal-footer bg-light justify-content-center rounded-bottom-4">
                                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" name="eliminar_sede" class="btn btn-danger fw-semibold rounded-pill px-4">Sí, Eliminar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- CURSOS DE LA SEDE SELECCIONADA -->
    <?php if ($info_sede_actual): ?>
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white p-4 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h4 class="fw-bold text-dark mb-1">
                        Sede: <span style="color: var(--accent-color);"><?php echo htmlspecialchars($info_sede_actual['nombre_sede']); ?></span>
                    </h4>
                    <p class="text-muted small mb-0">Listado de grados organizados por jornadas y filtros activos.</p>
                </div>
                <button type="button" class="btn btn-institutional fw-semibold rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoCurso">
                    <i class="bi bi-plus-lg me-1"></i> Agregar Nuevo Curso
                </button>
            </div>
            
            <!-- BARRA DE FILTROS -->
            <div class="card-body bg-light border-bottom p-3 px-4">
                <form method="GET" action="grados.php" class="row g-3 align-items-center">
                    <input type="hidden" name="sede" value="<?php echo $sede_seleccionada; ?>">
                    
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-semibold text-muted mb-1">Filtrar por Grado o Número</label>
                        <div class="input-group input-group-sm shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-success"><i class="bi bi-search"></i></span>
                            <input type="text" name="grado" class="form-control border-start-0 ps-0" placeholder="Ej: 9, 1001, 11..." value="<?php echo htmlspecialchars($filtro_grado); ?>" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-semibold text-muted mb-1">Filtrar por Jornada</label>
                        <select name="jornada" class="form-select form-select-sm shadow-sm">
                            <option value="">-- Todas las Jornadas --</option>
                            <option value="Mañana" <?php echo ($filtro_jornada === 'Mañana') ? 'selected' : ''; ?>>Mañana</option>
                            <option value="Tarde" <?php echo ($filtro_jornada === 'Tarde') ? 'selected' : ''; ?>>Tarde</option>
                            <option value="Única" <?php echo ($filtro_jornada === 'Única') ? 'selected' : ''; ?>>Única</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-4 d-flex align-items-end gap-2 pt-1 pt-md-4">
                        <button type="submit" class="btn btn-institutional btn-sm fw-semibold px-4 flex-grow-1 shadow-sm">
                            <i class="bi bi-filter me-1"></i> Filtrar
                        </button>
                        <?php if (!empty($filtro_jornada) || !empty($filtro_grado)): ?>
                            <a href="grados.php?sede=<?php echo $sede_seleccionada; ?>" class="btn btn-outline-secondary btn-sm px-3 shadow-sm" title="Limpiar Filtros">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="card-body p-4">
                <?php if (!empty($cursos_sede)): ?>
                    <?php 
                        $cursos_por_jornada = [];
                        foreach ($cursos_sede as $curso) {$cursos_por_jornada[$curso['jornada']][] =$curso;
                        }
                        $orden_jornadas = ['Mañana', 'Tarde', 'Única'];
                    ?>

                    <?php foreach ($orden_jornadas as$jornada_nombre): ?>
                        <?php if (isset($cursos_por_jornada[$jornada_nombre])): ?>
                            <div class="mb-5">
                                <div class="jornada-section-header p-3 mb-3 shadow-sm d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;">
                                            <i class="bi bi-clock-fill small"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-0">Jornada <?php echo $jornada_nombre; ?></h5>
                                    </div>
                                    <span class="badge bg-success-subtle text-success border px-3 py-1 rounded-pill fw-semibold">
                                        <?php echo count($cursos_por_jornada[$jornada_nombre]); ?> cursos
                                    </span>
                                </div>

                                <div class="row g-3">
                                    <?php foreach ($cursos_por_jornada[$jornada_nombre] as$curso): ?>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                            <div class="curso-card rounded-4 p-3 d-flex flex-column justify-content-between h-100 shadow-sm">
                                                <div>
                                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                                        <div>
                                                            <span class="text-uppercase text-muted small fw-bold tracking-wider" style="font-size: 0.7rem;">Curso / Grado</span>
                                                            <h4 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($curso['nombre_curso']); ?></h4>
                                                        </div>
                                                        <div class="bg-light rounded-circle p-2 shadow-sm text-success">
                                                            <i class="bi bi-people-fill fs-5"></i>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill small">
                                                            <i class="bi bi-sun-fill me-1 text-warning"></i><?php echo htmlspecialchars($curso['jornada']); ?>
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="border-top pt-3 d-flex align-items-center justify-content-between gap-2">
                                                    <a href="estudiantes_curso.php?id_curso=<?php echo $curso['id_curso']; ?>" class="btn btn-institutional btn-sm rounded-pill px-3 fw-semibold flex-grow-1 shadow-sm">
                                                        <i class="bi bi-eye-fill me-1"></i> Estudiantes
                                                    </a>

                                                    <button type="button" class="btn btn-outline-success btn-sm rounded-circle shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEditarCurso<?php echo $curso['id_curso']; ?>" title="Editar" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-circle shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEliminarCurso<?php echo $curso['id_curso']; ?>" title="Eliminar" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- MODAL EDITAR CURSO -->
                                        <div class="modal fade" id="modalEditarCurso<?php echo $curso['id_curso']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content rounded-4 border-0 shadow">
                                                    <div class="modal-header bg-light rounded-top-4">
                                                        <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-success me-2"></i>Editar Curso <?php echo htmlspecialchars($curso['nombre_curso']); ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST" action="?sede=<?php echo $info_sede_actual['id_sede']; ?>">
                                                        <div class="modal-body p-4">
                                                            <input type="hidden" name="id_curso" value="<?php echo $curso['id_curso']; ?>">

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Nombre del Curso / Grado (Solo números)</label>
                                                                <input type="text" name="nombre_curso" class="form-control shadow-sm" value="<?php echo htmlspecialchars($curso['nombre_curso']); ?>" required inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Jornada</label>
                                                                <select name="jornada" class="form-select shadow-sm" required>
                                                                    <option value="Mañana" <?php echo ($curso['jornada'] == 'Mañana') ? 'selected' : ''; ?>>Mañana</option>
                                                                    <option value="Tarde" <?php echo ($curso['jornada'] == 'Tarde') ? 'selected' : ''; ?>>Tarde</option>
                                                                    <option value="Única" <?php echo ($curso['jornada'] == 'Única') ? 'selected' : ''; ?>>Única</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light rounded-bottom-4">
                                                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" name="editar_curso" class="btn btn-institutional fw-semibold rounded-pill px-4">Guardar Cambios</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- MODAL ELIMINAR CURSO -->
                                        <div class="modal fade" id="modalEliminarCurso<?php echo $curso['id_curso']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content rounded-4 border-0 shadow">
                                                    <div class="modal-header bg-danger text-white rounded-top-4">
                                                        <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmar Eliminación</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST" action="?sede=<?php echo $info_sede_actual['id_sede']; ?>">
                                                        <div class="modal-body p-4 text-center">
                                                            <input type="hidden" name="id_curso" value="<?php echo $curso['id_curso']; ?>">
                                                            <i class="bi bi-trash3 text-danger display-4 mb-3"></i>
                                                            <p class="mb-1 fs-5 text-dark">¿Estás seguro de eliminar el curso <strong><?php echo htmlspecialchars($curso['nombre_curso']); ?></strong>?</p>
                                                            <small class="text-muted">Esta acción no se puede deshacer.</small>
                                                        </div>
                                                        <div class="modal-footer bg-light justify-content-center rounded-bottom-4">
                                                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" name="eliminar_curso" class="btn btn-danger fw-semibold rounded-pill px-4">Sí, Eliminar</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>

                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-folder-x display-4 d-block mb-3 text-secondary opacity-50"></i>
                        <h5 class="fw-bold">No se encontraron cursos</h5>
                        <p class="mb-0 small">Prueba cambiando los filtros o agregando un nuevo curso a esta sede.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- MODAL AGREGAR NUEVO CURSO -->
        <div class="modal fade" id="modalNuevoCurso" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header bg-light rounded-top-4">
                        <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle-fill text-success me-2"></i>Nuevo Curso - Sede <?php echo htmlspecialchars($info_sede_actual['nombre_sede']); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="?sede=<?php echo $info_sede_actual['id_sede']; ?>">
                        <div class="modal-body p-4">
                            <input type="hidden" name="id_sede" value="<?php echo $info_sede_actual['id_sede']; ?>">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nombre del Curso / Grado (Solo números)</label>
                                <input type="text" name="nombre_curso" class="form-control shadow-sm" required placeholder="Ej: 901, 1002, 1101" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Jornada</label>
                                <select name="jornada" class="form-select shadow-sm" required>
                                    <option value="Mañana">Mañana</option>
                                    <option value="Tarde">Tarde</option>
                                    <option value="Única">Única</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer bg-light rounded-bottom-4">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" name="crear_curso" class="btn btn-institutional fw-semibold rounded-pill px-4">Guardar Curso</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

<!-- MODAL NUEVA SEDE -->
<div class="modal fade" id="modalNuevaSede" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-light rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-building-add text-success me-2"></i>Registrar Nueva Sede</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre de la Sede</label>
                        <input type="text" name="nombre_sede" class="form-control shadow-sm" required placeholder="Ej: Central, Norte, Sur">
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="crear_sede" class="btn btn-institutional fw-semibold rounded-pill px-4">Guardar Sede</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL COPIA DE SEGURIDAD -->
<div class="modal fade" id="modalBackup" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header text-white rounded-top-4" style="background-color: var(--primary-color);">
                <h5 class="modal-title fw-bold"><i class="bi bi-shield-lock-fill me-2"></i>Copia de Seguridad</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body p-4 text-center">
                    <div class="mb-3 text-success">
                        <i class="bi bi-database-fill-down display-4"></i>
                    </div>
                    <p class="mb-1 fs-5 text-dark">¿Deseas generar un backup completo de la base de datos?</p>
                    <small class="text-muted">Se descargará un archivo SQL con toda la información actual del sistema institucional.</small>
                </div>
                <div class="modal-footer bg-light justify-content-center rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="hacer_backup" class="btn btn-institutional fw-semibold rounded-pill px-4">Sí, Descargar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<footer class="text-center py-3 bg-white border-top text-muted small mt-auto">
    &copy; <?php echo date('Y'); ?> IE Técnico Superior - Neiva
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>