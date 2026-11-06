<?php
session_start();
require_once "../inc/conexion.php";

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Administrador') {
    header("Location: ../index.php");
    exit();
}

$conexion = conectarBD();
$mensaje_exito = "";
$mensaje_error = "";

// Carpeta donde se guardarán las fotos de perfil
$directorio_fotos = "../uploads/profesores/";
if (!file_exists($directorio_fotos)) {
    mkdir($directorio_fotos, 0777, true);
}

// PROCESAR NUEVO PROFESOR (CON FOTO OPCIONAL)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar_profesor'])) {
    $nombre_completo = trim($_POST['nuevo_nombre']);
    $correo = trim($_POST['nuevo_correo']);
    $password = password_hash(trim($_POST['nuevo_password']), PASSWORD_DEFAULT);
    $rol = 'Profesor';
    $activo = 1;
    $nombre_foto = "default.png"; // Imagen por defecto si no sube ninguna

    // Procesar archivo de imagen si fue subido
    if (isset($_FILES['nueva_foto']) && $_FILES['nueva_foto']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['nueva_foto']['name'], PATHINFO_EXTENSION));
        $ext_permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $ext_permitidas)) {
            $nombre_foto = "profesor_" . time() . "_" . uniqid() . "." . $ext;
            move_uploaded_file($_FILES['nueva_foto']['tmp_name'], $directorio_fotos . $nombre_foto);
        } else {
            $mensaje_error = "Formato de imagen no permitido. Utilice JPG, PNG o WEBP.";
        }
    }

    if (empty($mensaje_error)) {
        try {
            $stmt_ins = mysqli_prepare($conexion, "INSERT INTO usuarios (nombre_completo, correo, password, rol, activo, foto) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt_ins, "ssssis", $nombre_completo, $correo, $password, $rol, $activo, $nombre_foto);
            mysqli_stmt_execute($stmt_ins);
            mysqli_stmt_close($stmt_ins);
            $mensaje_exito = "¡Profesor agregado exitosamente!";
        } catch (Exception $e) {
            $mensaje_error = "Error al agregar el profesor (el correo podría ya estar registrado): " . $e->getMessage();
        }
    }
}

// PROCESAR CAMBIO DE FOTO INDIVIDUAL
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cambiar_foto_profesor'])) {
    $id_profesor_foto = intval($_POST['id_profesor_foto']);
    
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
        $ext_permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $ext_permitidas)) {
            $nuevo_nombre_foto = "profesor_" . $id_profesor_foto . "_" . time() . "." . $ext;
            
            if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $directorio_fotos . $nuevo_nombre_foto)) {
                // Actualizar foto en la BD
                $stmt_foto = mysqli_prepare($conexion, "UPDATE usuarios SET foto = ? WHERE id_usuario = ? AND rol = 'Profesor'");
                mysqli_stmt_bind_param($stmt_foto, "si", $nuevo_nombre_foto, $id_profesor_foto);
                mysqli_stmt_execute($stmt_foto);
                mysqli_stmt_close($stmt_foto);
                
                $mensaje_exito = "¡Foto de perfil actualizada correctamente!";
            } else {
                $mensaje_error = "Error al guardar el archivo de la imagen.";
            }
        } else {
            $mensaje_error = "Formato de imagen inválido. Solo se admiten JPG, PNG y WEBP.";
        }
    } else {
        $mensaje_error = "Por favor seleccione una imagen válida.";
    }
}

// PROCESAR ELIMINAR PROFESOR
if (isset($_GET['eliminar'])) {
    $id_eliminar = intval($_GET['eliminar']);
    try {
        mysqli_begin_transaction($conexion);
        
        // Eliminar primero sus relaciones de cursos
        $stmt_del_c = mysqli_prepare($conexion, "DELETE FROM profesor_cursos WHERE id_usuario = ?");
        mysqli_stmt_bind_param($stmt_del_c, "i", $id_eliminar);
        mysqli_stmt_execute($stmt_del_c);
        mysqli_stmt_close($stmt_del_c);

        // Eliminar usuario
        $stmt_del_u = mysqli_prepare($conexion, "DELETE FROM usuarios WHERE id_usuario = ? AND rol = 'Profesor'");
        mysqli_stmt_bind_param($stmt_del_u, "i", $id_eliminar);
        mysqli_stmt_execute($stmt_del_u);
        mysqli_stmt_close($stmt_del_u);

        mysqli_commit($conexion);
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . "?exito=eliminado");
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conexion);
        $mensaje_error = "Error al eliminar el profesor: " . $e->getMessage();
    }
}

if (isset($_GET['exito']) && $_GET['exito'] == 'eliminado') {
    $mensaje_exito = "¡Profesor eliminado correctamente!";
}

// PROCESAR ASIGNACIÓN DE CURSOS Y CAMBIO DE ESTADO
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar_todas_asignaciones'])) {
    mysqli_begin_transaction($conexion);
    try {
        // 1. Actualizar Estados
        if (isset($_POST['estados']) && is_array($_POST['estados'])) {
            $stmt_est = mysqli_prepare($conexion, "UPDATE usuarios SET activo = ? WHERE id_usuario = ?");
            foreach ($_POST['estados'] as $id_p => $st) {
                $id_prof = intval($id_p);
                $activo  = intval($st);
                mysqli_stmt_bind_param($stmt_est, "ii", $activo, $id_prof);
                mysqli_stmt_execute($stmt_est);
            }
            mysqli_stmt_close($stmt_est);
        }

        // 2. Actualizar Asignaciones de Cursos
        if (isset($_POST['asignaciones']) && is_array($_POST['asignaciones'])) {
            $stmt_asig = mysqli_prepare($conexion, "INSERT INTO profesor_cursos (id_usuario, id_curso) VALUES (?, ?) ON DUPLICATE KEY UPDATE id_curso = VALUES(id_curso)");
            $stmt_del  = mysqli_prepare($conexion, "DELETE FROM profesor_cursos WHERE id_usuario = ?");

            foreach ($_POST['asignaciones'] as $id_p => $id_c) {
                $id_prof = intval($id_p);
                $id_cur  = !empty($id_c) ? intval($id_c) : NULL;

                if ($id_cur !== NULL) {
                    mysqli_stmt_bind_param($stmt_asig, "ii", $id_prof, $id_cur);
                    mysqli_stmt_execute($stmt_asig);
                } else {
                    mysqli_stmt_bind_param($stmt_del, "i", $id_prof);
                    mysqli_stmt_execute($stmt_del);
                }
            }
            if ($stmt_asig) mysqli_stmt_close($stmt_asig);
            if ($stmt_del) mysqli_stmt_close($stmt_del);
        }

        mysqli_commit($conexion);
        $mensaje_exito = "¡Cambios guardados correctamente!";
    } catch (Exception $e) {
        mysqli_rollback($conexion);
        $mensaje_error = "Error al actualizar: " . $e->getMessage();
    }
}

// OBTENER CURSOS Y PROFESORES
$cursos_disponibles = [];
$res_cursos = mysqli_query($conexion, "SELECT c.id_curso, c.nombre_curso, c.jornada, s.nombre_sede FROM grados_cursos c INNER JOIN sedes s ON c.id_sede = s.id_sede ORDER BY c.nombre_curso ASC");
if ($res_cursos) {
    while ($row = mysqli_fetch_assoc($res_cursos)) {
        $cursos_disponibles[] = $row;
    }
}

$sql_profesores = "SELECT u.id_usuario, u.nombre_completo, u.correo, u.activo, u.foto, pc.id_curso 
                   FROM usuarios u 
                   LEFT JOIN profesor_cursos pc ON u.id_usuario = pc.id_usuario 
                   WHERE u.rol = 'Profesor' 
                   ORDER BY u.nombre_completo ASC";
$res_profesores = mysqli_query($conexion, $sql_profesores);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Profesores - IE Técnico Superior</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary-color: #14532d;
            --primary-hover: #166534;
            --accent-color: #15803d;
            --bg-color: #f8fafc;
        }
        body { background-color: var(--bg-color); font-family: 'Segoe UI', system-ui, sans-serif; color: #334155; }
        .card { border: none; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); border-radius: 1rem; }
        .card-header-title { background: linear-gradient(135deg, #14532d 0%, #052e16 100%); color: white; }
        .btn-institutional { background-color: var(--primary-color); border-color: var(--primary-color); color: #fff; }
        .btn-institutional:hover { background-color: var(--primary-hover); border-color: var(--primary-hover); color: #fff; }
        
        .img-avatar {
            width: 42px;
            height: 42px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #14532d;
        }
        .btn-change-photo {
            font-size: 0.75rem;
            padding: 2px 8px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm py-3" style="background-color: var(--primary-color) !important;">
    <div class="container">
        <a class="navbar-brand fw-bold text-white" href="panel_admin.php"><i class="bi bi-mortarboard-fill me-2"></i> IE Técnico Superior <span class="fw-light fs-6 opacity-75">| Admin</span></a>
        <a href="panel_admin.php" class="btn btn-outline-light btn-sm fw-semibold rounded-pill px-3"><i class="bi bi-arrow-left me-1"></i> Volver al Panel</a>
    </div>
</nav>

<div class="container py-5">

    <?php if (!empty($mensaje_exito)): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i><?php echo $mensaje_exito; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($mensaje_error)): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i><?php echo $mensaje_error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Encabezado y Botón Agregar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Gestión de Docentes</h2>
            <p class="text-muted small mb-0">Administre el acceso, fotos de perfil, cursos asignados y registros de profesores.</p>
        </div>
        <button type="button" class="btn btn-institutional fw-bold px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAgregarProfesor">
            <i class="bi bi-person-plus-fill me-2"></i>Agregar Profesor
        </button>
    </div>

    <form method="POST" action="">
        <div class="card shadow-sm mb-4">
            <div class="card-header card-header-title p-4 rounded-top-4">
                <h4 class="fw-bold mb-1"><i class="bi bi-person-badge-fill me-2"></i>Lista de Profesores y Cursos</h4>
                <p class="mb-0 small opacity-75">Un mismo curso no puede ser asignado a dos profesores simultáneamente.</p>
            </div>
            
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Docente</th>
                                <th>Correo Institucional</th>
                                <th class="text-center">Estado de Acceso</th>
                                <th class="text-center">Curso Asignado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($res_profesores && mysqli_num_rows($res_profesores) > 0): ?>
                                <?php while ($profesor = mysqli_fetch_assoc($res_profesores)): ?>
                                    <?php 
                                        $foto_ruta = !empty($profesor['foto']) && file_exists("../uploads/profesores/" . $profesor['foto']) 
                                                     ? "../uploads/profesores/" . $profesor['foto'] 
                                                     : null;
                                    ?>
                                    <tr>
                                        <td class="fw-semibold">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="position-relative">
                                                    <?php if ($foto_ruta): ?>
                                                        <img src="<?php echo $foto_ruta; ?>" alt="Foto" class="img-avatar">
                                                    <?php else: ?>
                                                        <i class="bi bi-person-circle fs-2 text-secondary"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <div><?php echo htmlspecialchars($profesor['nombre_completo']); ?></div>
                                                    <button type="button" class="btn btn-link text-decoration-none p-0 border-0 btn-change-photo" onclick="abrirModalCambiarFoto(<?php echo $profesor['id_usuario']; ?>, '<?php echo addslashes($profesor['nombre_completo']); ?>')">
                                                        <i class="bi bi-camera-fill me-1"></i>Cambiar foto
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-muted"><?php echo htmlspecialchars($profesor['correo']); ?></td>
                                        <td class="text-center">
                                            <select name="estados[<?php echo $profesor['id_usuario']; ?>]" class="form-select form-select-sm mx-auto shadow-sm" style="max-width: 130px;">
                                                <option value="1" <?php echo ($profesor['activo'] == 1) ? 'selected' : ''; ?>>Activo</option>
                                                <option value="0" <?php echo ($profesor['activo'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            <select name="asignaciones[<?php echo $profesor['id_usuario']; ?>]" class="form-select form-select-sm mx-auto curso-select shadow-sm" style="max-width: 260px;" onchange="actualizarCursosDisponibles()">
                                                <option value="">-- Sin Asignar --</option>
                                                <?php foreach ($cursos_disponibles as $curso): ?>
                                                    <option value="<?php echo $curso['id_curso']; ?>" <?php echo ($profesor['id_curso'] == $curso['id_curso']) ? 'selected' : ''; ?>>
                                                        <?php echo $curso['nombre_curso'] . ' - ' . $curso['jornada'] . ' (' . $curso['nombre_sede'] . ')'; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="abrirModalEliminar(<?php echo $profesor['id_usuario']; ?>, '<?php echo addslashes($profesor['nombre_completo']); ?>')">
                                                <i class="bi bi-trash-fill me-1"></i> Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No hay profesores registrados en el sistema.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-light p-3 text-end border-top rounded-bottom-4">
                <button type="submit" name="guardar_todas_asignaciones" class="btn btn-success fw-bold px-4 py-2 rounded-pill shadow-sm">
                    <i class="bi bi-check-circle-fill me-2"></i>Guardar Todos los Cambios
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Modal para Agregar Profesor -->
<div class="modal fade" id="modalAgregarProfesor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-header bg-success text-white rounded-top-4">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2"></i>Registrar Nuevo Profesor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre Completo</label>
                        <input type="text" name="nuevo_nombre" class="form-control" required placeholder="Ej: Carlos Pérez">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Correo Institucional</label>
                        <input type="email" name="nuevo_correo" class="form-control" required placeholder="correo@iestecnico.edu.co">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Contraseña Temporal</label>
                        <input type="password" name="nuevo_password" class="form-control" required placeholder="••••••••">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Foto de Perfil (Opcional)</label>
                        <input type="file" name="nueva_foto" class="form-control" accept="image/png, image/jpeg, image/webp">
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="agregar_profesor" class="btn btn-institutional rounded-pill px-4 fw-bold">Guardar Profesor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Cambiar Foto de Perfil -->
<div class="modal fade" id="modalCambiarFoto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-header bg-success text-white rounded-top-4">
                    <h5 class="modal-title fw-bold"><i class="bi bi-camera-fill me-2"></i>Cambiar Foto de Perfil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="id_profesor_foto" id="idProfesorFoto">
                    <p class="text-muted small mb-3">Profesor: <strong id="nombreProfesorFoto" class="text-dark"></strong></p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Seleccionar Nueva Imagen</label>
                        <input type="file" name="foto_perfil" class="form-control" accept="image/png, image/jpeg, image/webp" required>
                        <div class="form-text">Formatos permitidos: JPG, PNG, WEBP.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="cambiar_foto_profesor" class="btn btn-institutional rounded-pill px-4 fw-bold">Actualizar Foto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmación para Eliminar -->
<div class="modal fade" id="modalConfirmarEliminar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-danger text-white rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmar Eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="bi bi-trash3 text-danger display-4 mb-3"></i>
                <p class="mb-1 text-muted">¿Está seguro que desea eliminar al profesor?</p>
                <h5 id="nombreProfesorEliminar" class="fw-bold text-dark mb-3"></h5>
                <p class="small text-danger mb-0">Esta acción borrará sus asignaciones de cursos de forma permanente.</p>
            </div>
            <div class="modal-footer bg-light justify-content-center rounded-bottom-4">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                <a id="btnConfirmarEliminacion" href="#" class="btn btn-danger rounded-pill px-4 fw-bold">Sí, Eliminar</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function actualizarCursosDisponibles() {
    const selects = document.querySelectorAll('.curso-select');
    let seleccionados = [];

    selects.forEach(select => {
        if (select.value !== "") {
            seleccionados.push(select.value);
        }
    });

    selects.forEach(select => {
        const valorActual = select.value;
        Array.from(select.options).forEach(option => {
            if (option.value !== "") {
                if (seleccionados.includes(option.value) && option.value !== valorActual) {
                    option.disabled = true;
                    option.style.color = "#cbd5e1";
                } else {
                    option.disabled = false;
                    option.style.color = "#334155";
                }
            }
        });
    });
}

function abrirModalCambiarFoto(idUsuario, nombreCompleto) {
    document.getElementById('idProfesorFoto').value = idUsuario;
    document.getElementById('nombreProfesorFoto').textContent = nombreCompleto;
    
    var modalFoto = new bootstrap.Modal(document.getElementById('modalCambiarFoto'));
    modalFoto.show();
}

function abrirModalEliminar(idUsuario, nombreCompleto) {
    document.getElementById('nombreProfesorEliminar').textContent = nombreCompleto;
    document.getElementById('btnConfirmarEliminacion').href = "?eliminar=" + idUsuario;
    
    var modalEliminar = new bootstrap.Modal(document.getElementById('modalConfirmarEliminar'));
    modalEliminar.show();
}

document.addEventListener('DOMContentLoaded', function() {
    actualizarCursosDisponibles();
});
</script>
</body>
</html>