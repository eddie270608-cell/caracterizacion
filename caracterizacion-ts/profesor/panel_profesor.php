<?php
session_start();
require_once "../inc/conexion.php";

// Validar inicio de sesión y rol de Profesor
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Profesor') {
    header("Location: ../index.php");
    exit();
}

$conexion = conectarBD();
$id_profesor = $_SESSION['id_usuario'];
$nombre_profesor = $_SESSION['nombre'];

$mensaje_exito = "";
$mensaje_error = "";

// 1. CONSULTAR DATOS DEL PROFESOR (Incluyendo la foto)
$sql_profesor = "SELECT nombre_completo, correo, foto FROM usuarios WHERE id_usuario = ? AND rol = 'Profesor' LIMIT 1";
$stmt_prof = mysqli_prepare($conexion, $sql_profesor);
mysqli_stmt_bind_param($stmt_prof, "i", $id_profesor);
mysqli_stmt_execute($stmt_prof);
$res_prof = mysqli_stmt_get_result($stmt_prof);
$datos_profesor = mysqli_fetch_assoc($res_prof);
mysqli_stmt_close($stmt_prof);

// Definir la ruta de la foto de perfil del profesor
$foto_perfil_ruta = "";
if (!empty($datos_profesor['foto']) && file_exists("../uploads/profesores/" . $datos_profesor['foto'])) {
    $foto_perfil_ruta = "../uploads/profesores/" . $datos_profesor['foto'];
}

// 2. PROCESAR GUARDADO DE ESTADOS (Activo / Inactivo) DE ESTUDIANTES
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar_estados_estudiantes'])) {
    if (isset($_POST['estados']) && is_array($_POST['estados'])) {
        mysqli_begin_transaction($conexion);
        try {
            $stmt_e = mysqli_prepare($conexion, "UPDATE estudiantes SET activo = ? WHERE id_estudiante = ?");
            foreach ($_POST['estados'] as $id_est => $st) {
                $id_e  = intval($id_est);
                $state = intval($st);
                mysqli_stmt_bind_param($stmt_e, "ii", $state, $id_e);
                mysqli_stmt_execute($stmt_e);
            }
            mysqli_stmt_close($stmt_e);
            mysqli_commit($conexion);
            $mensaje_exito = "¡Estados de los estudiantes actualizados correctamente!";
        } catch (Exception $e) {
            mysqli_rollback($conexion);
            $mensaje_error = "Error al actualizar estados: " . $e->getMessage();
        }
    }
}

// 3. CONSULTAR CURSO ASIGNADO AL PROFESOR
$sql_curso = "SELECT gc.id_curso, gc.nombre_curso, gc.jornada, s.nombre_sede 
              FROM profesor_cursos pc 
              INNER JOIN grados_cursos gc ON pc.id_curso = gc.id_curso 
              INNER JOIN sedes s ON gc.id_sede = s.id_sede 
              WHERE pc.id_usuario = ? LIMIT 1";
$stmt_c = mysqli_prepare($conexion, $sql_curso);
mysqli_stmt_bind_param($stmt_c, "i", $id_profesor);
mysqli_stmt_execute($stmt_c);
$curso_asignado = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_c));
mysqli_stmt_close($stmt_c);

// 4. CONSULTAR ESTUDIANTES SI TIENE CURSO ASIGNADO
$estudiantes = [];
if ($curso_asignado) {
    $id_curso = $curso_asignado['id_curso'];
    $sql_est = "SELECT e.id_estudiante, e.tipo_documento, e.numero_documento, e.nombres_apellidos, e.edad, e.telefono_movil, e.activo, f.id_ficha 
                FROM fichas_caracterizacion f 
                INNER JOIN estudiantes e ON f.id_estudiante = e.id_estudiante 
                WHERE f.id_curso = ? 
                ORDER BY e.nombres_apellidos ASC";
    $stmt_est = mysqli_prepare($conexion, $sql_est);
    mysqli_stmt_bind_param($stmt_est, "i", $id_curso);
    mysqli_stmt_execute($stmt_est);
    $res_est = mysqli_stmt_get_result($stmt_est);
    while ($row = mysqli_fetch_assoc($res_est)) {
        $estudiantes[] = $row;
    }
    mysqli_stmt_close($stmt_est);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Docente - IE Técnico Superior</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style> 
        :root {
            --primary-color: #14532d;      /* Verde institucional principal */
            --primary-hover: #166534;    /* Tono hover para botones principales */
            --accent-color: #15803d;     /* Acento verde vivo */
            --bg-color: #f8fafc;         /* Fondo claro limpio */
        }
        body { 
            background-color: var(--bg-color); 
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; 
            color: #334155;
        }
        .card { 
            border: none; 
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1); 
        }
        .table-hover tbody tr:hover { 
            background-color: #f1f5f9; 
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
        .welcome-card {
            background: linear-gradient(135deg, #14532d 0%, #052e16 100%);
            color: white;
        }
        .avatar-docente {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid rgba(255, 255, 255, 0.8);
        }
    </style>
</head>
<body>

<!-- Navbar Institucional -->
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm py-3" style="background-color: var(--primary-color) !important;">
    <div class="container">
        <a class="navbar-brand fw-bold fs-5 text-white" href="../profesor/panel_profesor.php">
            <i class="bi bi-mortarboard-fill text-white me-2"></i> IE Técnico Superior <span class="fw-light fs-6 opacity-75">| Panel Docente</span>
        </a>
        <div class="d-flex align-items-center gap-2">
            <!-- Botón de Información y Desarrolladores con el icono de grupo de personas -->
            <a href="../informacion.php" class="btn btn-outline-light btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;" title="Información y Desarrolladores">
                <i class="bi bi-people-fill"></i>
            </a>
            
            <a href="../logout.php" class="btn btn-outline-light btn-sm rounded-pill px-3">
                <i class="bi bi-box-arrow-right me-1"></i> Cerrar Sesión
            </a>
        </div>
    </div>
</nav>

<div class="container py-5">

    <!-- Mensajes -->
    <?php if (!empty($mensaje_exito)): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i><strong>¡Éxito!</strong> <?php echo $mensaje_exito; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($mensaje_error)): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i><strong>¡Atención!</strong> <?php echo $mensaje_error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Bienvenida con Foto de Perfil Visualizable -->
    <div class="card welcome-card rounded-4 shadow-sm mb-4 p-2">
        <div class="card-body p-4 d-flex align-items-center gap-3 flex-wrap">
            <div>
                <?php if (!empty($foto_perfil_ruta)): ?>
                    <img src="<?php echo $foto_perfil_ruta; ?>" alt="Foto de perfil" class="avatar-docente shadow-sm">
                <?php else: ?>
                    <div class="bg-white text-success rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 70px; height: 70px;">
                        <i class="bi bi-person-fill fs-1"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div>
                <h3 class="fw-bold mb-1">¡Bienvenido(a), Prof. <?php echo htmlspecialchars($nombre_profesor); ?>!</h3>
                <p class="mb-0 opacity-75">Gestiona la caracterización, estado académico y consulta de fichas de los estudiantes asignados a tu grado.</p>
            </div>
        </div>
    </div>

    <!-- Información del Curso Asignado -->
    <?php if ($curso_asignado): ?>
        <form method="POST" action="">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white p-4 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">
                            <i class="bi bi-diagram-3-fill me-2 text-success"></i>Curso Asignado: <span class="text-success"><?php echo htmlspecialchars($curso_asignado['nombre_curso']); ?></span>
                        </h4>
                        <small class="text-muted"><i class="bi bi-geo-alt-fill me-1"></i>Sede: <?php echo htmlspecialchars($curso_asignado['nombre_sede']); ?> | <i class="bi bi-clock-fill ms-2 me-1"></i>Jornada: <?php echo htmlspecialchars($curso_asignado['jornada']); ?></small>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3">Estudiante</th>
                                    <th class="py-3">Documento</th>
                                    <th class="py-3">Edad / Contacto</th>
                                    <th class="py-3 text-center">Estado del Estudiante</th>
                                    <th class="py-3 text-center" style="width: 250px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($estudiantes)): ?>
                                    <?php foreach ($estudiantes as $est): ?>
                                        <tr>
                                            <td class="fw-semibold text-dark py-3">
                                                <i class="bi bi-person-circle text-secondary me-2 fs-5 align-middle"></i><?php echo htmlspecialchars($est['nombres_apellidos']); ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-secondary border px-2 py-1"><?php echo htmlspecialchars($est['tipo_documento']); ?>: <?php echo htmlspecialchars($est['numero_documento']); ?></span>
                                            </td>
                                            <td>
                                                <small class="d-block text-dark fw-semibold"><?php echo htmlspecialchars($est['edad']); ?> años</small>
                                                <small class="text-muted"><i class="bi bi-telephone-fill me-1"></i><?php echo !empty($est['telefono_movil']) ? htmlspecialchars($est['telefono_movil']) : 'Sin teléfono'; ?></small>
                                            </td>
                                            <td class="text-center">
                                                <!-- Selector de Estado editable para el Profesor -->
                                                <select name="estados[<?php echo $est['id_estudiante']; ?>]" class="form-select form-select-sm mx-auto shadow-sm rounded-3" style="max-width: 130px;">
                                                    <option value="1" <?php echo ($est['activo'] == 1) ? 'selected' : ''; ?>>Activo</option>
                                                    <option value="0" <?php echo ($est['activo'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <!-- Botón Descargar Ficha -->
                                                    <a href="../descargar_ficha.php?id_ficha=<?php echo $est['id_ficha']; ?>" class="btn btn-outline-success btn-sm rounded-3 fw-semibold px-2" title="Ver y Descargar Ficha">
                                                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Ficha
                                                    </a>
                                                    <!-- Editar Información -->
                                                    <a href="../administrador/editar_caracterizacion.php?id_ficha=<?php echo $est['id_ficha']; ?>" class="btn btn-outline-primary btn-sm rounded-3 fw-semibold px-2" title="Editar Información">
                                                        <i class="bi bi-pencil-square me-1"></i> Editar
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-person-x fs-1 d-block mb-2 text-secondary"></i>
                                            No hay estudiantes caracterizados en este curso actualmente.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if (!empty($estudiantes)): ?>
                    <div class="card-footer bg-light p-3 text-end border-top">
                        <button type="submit" name="guardar_estados_estudiantes" class="btn btn-institutional fw-bold px-4 rounded-3 shadow-sm">
                            <i class="bi bi-check-circle-fill me-2"></i>Guardar Estados
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-warning shadow-sm rounded-4 p-5 text-center bg-white border-0">
            <i class="bi bi-exclamation-triangle-fill fs-1 text-warning d-block mb-3"></i>
            <h4 class="fw-bold text-dark">Aún no tienes un curso asignado</h4>
            <p class="mb-0 text-secondary">Comunícate con la administración para que te asignen un grado y puedas gestionar a tus estudiantes.</p>
        </div>
    <?php endif; ?>

</div>

<footer class="text-center py-4 text-muted small">
    &copy; <?php echo date('Y'); ?> IE Técnico Superior - Neiva. Todos los derechos reservados.
</footer>

<!-- Bootstrap 5.3 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>