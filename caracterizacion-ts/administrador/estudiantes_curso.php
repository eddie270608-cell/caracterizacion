<?php
session_start();
require_once "../inc/conexion.php";

// Validar inicio de sesión y rol de Administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Administrador') {
    header("Location: ../index.php");
    exit();
}

$conexion = conectarBD();
$mensaje_exito = "";
$mensaje_error = "";

// Capturar el ID del curso
$id_curso = isset($_GET['id_curso']) ? intval($_GET['id_curso']) : 0;

if ($id_curso <= 0) {
    header("Location: grados.php");
    exit();
}

// 1. PROCESAR ELIMINACIÓN DE UN ESTUDIANTE
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_estudiante'])) {
    $id_estudiante = intval($_POST['id_estudiante']);

    if ($id_estudiante > 0) {
        $sql_del = "DELETE FROM estudiantes WHERE id_estudiante = ?";
        $stmt_del = mysqli_prepare($conexion, $sql_del);
        if ($stmt_del) {
            mysqli_stmt_bind_param($stmt_del, "i", $id_estudiante);
            if (mysqli_stmt_execute($stmt_del)) {
                $mensaje_exito = "¡Estudiante eliminado del sistema correctamente!";
            } else {
                $mensaje_error = "Error al intentar eliminar el estudiante: " . mysqli_error($conexion);
            }
            mysqli_stmt_close($stmt_del);
        }
    }
}

// 2. OBTENER INFORMACIÓN DEL CURSO Y LA SEDE
$stmt_info = mysqli_prepare($conexion, "SELECT gc.nombre_curso, gc.jornada, gc.id_sede, s.nombre_sede 
                                        FROM grados_cursos gc 
                                        INNER JOIN sedes s ON gc.id_sede = s.id_sede 
                                        WHERE gc.id_curso = ? LIMIT 1");
mysqli_stmt_bind_param($stmt_info, "i", $id_curso);
mysqli_stmt_execute($stmt_info);
$res_info = mysqli_stmt_get_result($stmt_info);
$curso_info = mysqli_fetch_assoc($res_info);
mysqli_stmt_close($stmt_info);

if (!$curso_info) {
    header("Location: grados.php");
    exit();
}

// 3. OBTENER LOS ESTUDIANTES DEL CURSO
$sql_est = "SELECT e.id_estudiante, e.tipo_documento, e.numero_documento, e.nombres_apellidos, e.edad, e.telefono_movil, e.activo, f.id_ficha, f.fecha_diligenciamiento 
            FROM fichas_caracterizacion f 
            INNER JOIN estudiantes e ON f.id_estudiante = e.id_estudiante 
            WHERE f.id_curso = ? 
            ORDER BY e.nombres_apellidos ASC";
$stmt_est = mysqli_prepare($conexion, $sql_est);
mysqli_stmt_bind_param($stmt_est, "i", $id_curso);
mysqli_stmt_execute($stmt_est);
$res_estudiantes = mysqli_stmt_get_result($stmt_est);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estudiantes - Curso <?php echo htmlspecialchars($curso_info['nombre_curso']); ?></title>
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
        .card-header-title { 
            background: linear-gradient(135deg, #14532d 0%, #052e16 100%); 
            color: white; 
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
        .form-control:focus, .form-select:focus { 
            border-color: var(--accent-color); 
            box-shadow: 0 0 0 0.25rem rgba(21, 128, 61, 0.15); 
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm py-3" style="background-color: var(--primary-color) !important;">
    <div class="container">
        <a class="navbar-brand fw-bold text-white" href="panel_admin.php">
            <i class="bi bi-mortarboard-fill me-2"></i> IE Técnico Superior <span class="fw-light fs-6 opacity-75">| Neiva</span>
        </a>
        <a href="grados.php?sede=<?php echo $curso_info['id_sede']; ?>" class="btn btn-outline-light btn-sm fw-semibold rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Volver a Grados
        </a>
    </div>
</nav>

<div class="container py-5 flex-grow-1">

    <!-- Mensajes de Alerta -->
    <?php if (!empty($mensaje_exito)): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?php echo $mensaje_exito; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($mensaje_error)): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $mensaje_error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Encabezado -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-header card-header-title p-4 px-md-5">
            <h4 class="fw-bold mb-1">
                <i class="bi bi-people-fill me-2"></i>Estudiantes del Curso <?php echo htmlspecialchars($curso_info['nombre_curso']); ?>
            </h4>
            <p class="mb-0 small opacity-75">
                Sede: <strong><?php echo htmlspecialchars($curso_info['nombre_sede']); ?></strong> | Jornada: <strong><?php echo htmlspecialchars($curso_info['jornada']); ?></strong>
            </p>
        </div>
    </div>

    <!-- Tabla de Estudiantes -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Estudiante</th>
                            <th>Documento</th>
                            <th>Edad / Teléfono</th>
                            <th>Estado (Docente)</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($res_estudiantes && mysqli_num_rows($res_estudiantes) > 0): ?>
                            <?php while ($est = mysqli_fetch_assoc($res_estudiantes)): ?>
                                <tr>
                                    <td class="fw-semibold text-dark">
                                        <i class="bi bi-person-fill text-secondary me-2"></i>
                                        <?php echo htmlspecialchars($est['nombres_apellidos']); ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary border">
                                            <?php echo htmlspecialchars($est['tipo_documento']); ?>: <?php echo htmlspecialchars($est['numero_documento']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="d-block text-dark"><?php echo htmlspecialchars($est['edad']); ?> años</small>
                                        <small class="text-muted"><?php echo !empty($est['telefono_movil']) ? htmlspecialchars($est['telefono_movil']) : 'Sin teléfono'; ?></small>
                                    </td>
                                    <td>
                                        <?php if ($est['activo'] == 1): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2">
                                                <i class="bi bi-check-circle-fill me-1"></i>Activo
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2">
                                                <i class="bi bi-x-circle-fill me-1"></i>Inactivo
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                                            <!-- Ver / Editar Caracterización -->
                                            <a href="editar_caracterizacion.php?id_ficha=<?php echo $est['id_ficha']; ?>" class="btn btn-outline-primary btn-sm rounded-3 fw-semibold" title="Ver o Editar Ficha">
                                                <i class="bi bi-file-earmark-text-fill me-1"></i> Ver / Editar
                                            </a>

                                            <!-- Botón Descargar Ficha -->
                                            <a href="../descargar_ficha.php?id_ficha=<?php echo $est['id_ficha']; ?>" class="btn btn-outline-success btn-sm rounded-3 fw-semibold" title="Descargar Ficha en PDF">
                                                <i class="bi bi-cloud-arrow-down-fill me-1"></i> Descargar
                                            </a>

                                            <!-- Botón Eliminar Estudiante -->
                                            <button type="button" class="btn btn-outline-danger btn-sm rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalEliminarEstudiante<?php echo $est['id_estudiante']; ?>" title="Eliminar Estudiante">
                                                <i class="bi bi-trash-fill me-1"></i> Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- MODAL ELIMINAR ESTUDIANTE -->
                                <div class="modal fade" id="modalEliminarEstudiante<?php echo $est['id_estudiante']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmar Eliminación</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="?id_curso=<?php echo $id_curso; ?>">
                                                <div class="modal-body p-4 text-center">
                                                    <input type="hidden" name="id_estudiante" value="<?php echo $est['id_estudiante']; ?>">
                                                    <p class="mb-1 fs-5 text-dark">¿Estás seguro de que deseas eliminar al estudiante <strong><?php echo htmlspecialchars($est['nombres_apellidos']); ?></strong>?</p>
                                                    <small class="text-muted">Esta acción borrará de forma permanente su ficha de caracterización y todos sus datos relacionados.</small>
                                                </div>
                                                <div class="modal-footer bg-light justify-content-center">
                                                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" name="eliminar_estudiante" class="btn btn-danger fw-semibold rounded-3">
                                                        <i class="bi bi-trash-fill me-1"></i> Sí, Eliminar
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                                    No hay estudiantes caracterizados en este curso todavía.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<footer class="text-center py-3 bg-white border-top text-muted small mt-auto">
    &copy; <?php echo date('Y'); ?> IE Técnico Superior - Neiva
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>