<?php
session_start();
require_once '../inc/conexion.php';

// Validar inicio de sesión para Administrador o Profesor
if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] !== 'Administrador' && $_SESSION['rol'] !== 'Profesor')) {
    header("Location: ../index.php");
    exit();
}

$conexion = conectarBD();
$mensaje_exito = "";
$mensaje_error = "";

$id_ficha = isset($_GET['id_ficha']) ? intval($_GET['id_ficha']) : 0;

if ($id_ficha <= 0) {
    header("Location: ../administrador/grados.php");
    exit();
}

// ==============================================================================
// 1. PROCESAR ACTUALIZACIÓN DE LA FICHA (POST)
// ==============================================================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['actualizar_ficha'])) {
    mysqli_begin_transaction($conexion);

    try {
        $id_estudiante = intval($_POST['id_estudiante']);

        // A. Actualizar Datos del Estudiante
        $tipo_documento       = $_POST['tipo_documento'] ?? 'TI';
        $numero_documento     = trim($_POST['numero_documento']);
        $nombres_apellidos    = trim($_POST['nombres_apellidos']);
        $fecha_nacimiento     = $_POST['fecha_nacimiento'];
        $edad                 = intval($_POST['edad']);
        $grupo_sanguineo      = trim($_POST['grupo_sanguineo']);
        $tipo_estudiante      = $_POST['tipo_estudiante'];
        $colegio_procedencia  = isset($_POST['colegio_procedencia']) && !empty(trim($_POST['colegio_procedencia'])) ? trim($_POST['colegio_procedencia']) : NULL;
        $direccion_residencia = trim($_POST['direccion_residencia']);
        $barrio               = trim($_POST['barrio']);
        $comuna               = trim($_POST['comuna']);
        $estrato              = intval($_POST['estrato']);
        $servicio_salud       = trim($_POST['servicio_salud']);
        $telefono_fijo        = trim($_POST['telefono_fijo']) ?: NULL;
        $telefono_movil       = trim($_POST['telefono_movil']) ?: NULL;
        $email_padres         = trim($_POST['email_padres']) ?: NULL;
        $tel_movil_padre      = trim($_POST['tel_movil_padre']) ?: NULL;
        $tel_movil_madre      = trim($_POST['tel_movil_madre']) ?: NULL;

        $stmt_est = mysqli_prepare($conexion, "UPDATE estudiantes SET tipo_documento=?, numero_documento=?, nombres_apellidos=?, fecha_nacimiento=?, edad=?, grupo_sanguineo=?, tipo_estudiante=?, colegio_procedencia=?, direccion_residencia=?, barrio=?, comuna=?, estrato=?, servicio_salud=?, telefono_fijo=?, telefono_movil=?, email_padres=?, tel_movil_padre=?, tel_movil_madre=? WHERE id_estudiante=?");
        mysqli_stmt_bind_param($stmt_est, "ssssissssssissssssi", $tipo_documento, $numero_documento, $nombres_apellidos, $fecha_nacimiento, $edad, $grupo_sanguineo, $tipo_estudiante, $colegio_procedencia, $direccion_residencia, $barrio, $comuna, $estrato, $servicio_salud, $telefono_fijo, $telefono_movil, $email_padres, $tel_movil_padre, $tel_movil_madre, $id_estudiante);
        mysqli_stmt_execute($stmt_est);
        mysqli_stmt_close($stmt_est);

        // B. Actualizar Ficha
        $id_curso                         = intval($_POST['id_curso']);
        $fecha_diligenciamiento           = $_POST['fecha_diligenciamiento'];
        $acudiente_nombre                 = trim($_POST['acudiente_nombre']);
        $acudiente_parentesco             = trim($_POST['acudiente_parentesco']);
        $medio_transporte                 = $_POST['medio_transporte'];
        $acompanamiento_jornada_contraria = $_POST['acompanamiento_jornada_contraria'];
        $observaciones_orientacion        = trim($_POST['observaciones_orientacion']) ?: NULL;

        $stmt_ficha = mysqli_prepare($conexion, "UPDATE fichas_caracterizacion SET id_curso=?, fecha_diligenciamiento=?, acudiente_nombre=?, acudiente_parentesco=?, medio_transporte=?, acompanamiento_jornada_contraria=?, observaciones_orientacion=? WHERE id_ficha=?");
        mysqli_stmt_bind_param($stmt_ficha, "issssssi", $id_curso, $fecha_diligenciamiento, $acudiente_nombre, $acudiente_parentesco, $medio_transporte, $acompanamiento_jornada_contraria, $observaciones_orientacion, $id_ficha);
        mysqli_stmt_execute($stmt_ficha);
        mysqli_stmt_close($stmt_ficha);

        // C. Actualizar Datos Familiares
        $padre_nombres        = trim($_POST['padre_nombres']) ?: NULL;
        $padre_escolaridad    = trim($_POST['padre_escolaridad']) ?: NULL;
        $padre_edad           = !empty($_POST['padre_edad']) ? intval($_POST['padre_edad']) : NULL;
        $padre_ocupacion      = trim($_POST['padre_ocupacion']) ?: NULL;
        $padre_tipo_ocupacion = $_POST['padre_tipo_ocupacion'] ?? NULL;

        $madre_nombres        = trim($_POST['madre_nombres']) ?: NULL;
        $madre_escolaridad    = trim($_POST['madre_escolaridad']) ?: NULL;
        $madre_edad           = !empty($_POST['madre_edad']) ? intval($_POST['madre_edad']) : NULL;
        $madre_ocupacion      = trim($_POST['madre_ocupacion']) ?: NULL;
        $madre_tipo_ocupacion = $_POST['madre_tipo_ocupacion'] ?? NULL;

        $ingresos_hogar       = $_POST['ingresos_hogar'];
        $tenencia_vivienda    = $_POST['tenencia_vivienda'];
        $convive_con          = $_POST['convive_con'];
        $es_hijo_unico        = isset($_POST['es_hijo_unico']) ? 1 : 0;
        $hermanos_en_colegio  = isset($_POST['hermanos_en_colegio']) ? 1 : 0;
        $total_personas_hogar = intval($_POST['total_personas_hogar'] ?? 1);

        $stmt_fam = mysqli_prepare($conexion, "UPDATE datos_familiares_hogar SET padre_nombres=?, padre_escolaridad=?, padre_edad=?, padre_ocupacion=?, padre_tipo_ocupacion=?, madre_nombres=?, madre_escolaridad=?, madre_edad=?, madre_ocupacion=?, madre_tipo_ocupacion=?, ingresos_hogar=?, tenencia_vivienda=?, convive_con=?, es_hijo_unico=?, hermanos_en_colegio=?, total_personas_hogar=? WHERE id_ficha=?");
        mysqli_stmt_bind_param($stmt_fam, "ssissssisssssiiii", $padre_nombres, $padre_escolaridad, $padre_edad, $padre_ocupacion, $padre_tipo_ocupacion, $madre_nombres, $madre_escolaridad, $madre_edad, $madre_ocupacion, $madre_tipo_ocupacion, $ingresos_hogar, $tenencia_vivienda, $convive_con, $es_hijo_unico, $hermanos_en_colegio, $total_personas_hogar, $id_ficha);
        mysqli_stmt_execute($stmt_fam);
        mysqli_stmt_close($stmt_fam);

        // D. Actualizar Salud
        $enfermedad_diagnosticada  = trim($_POST['enfermedad_diagnosticada']) ?: NULL;
        $recibe_tratamiento_medico = trim($_POST['recibe_tratamiento_medico']) ?: NULL;
        $consume_medicamento       = trim($_POST['consume_medicamento']) ?: NULL;

        $stmt_salud = mysqli_prepare($conexion, "UPDATE salud_estudiante SET enfermedad_diagnosticada=?, recibe_tratamiento_medico=?, consume_medicamento=? WHERE id_ficha=?");
        mysqli_stmt_bind_param($stmt_salud, "sssi", $enfermedad_diagnosticada, $recibe_tratamiento_medico, $consume_medicamento, $id_ficha);
        mysqli_stmt_execute($stmt_salud);
        mysqli_stmt_close($stmt_salud);

        // E. Actualizar Actividades Extracurriculares
        $educacion_comp_desc = isset($_POST['educacion_comp_desc']) && !empty(trim($_POST['educacion_comp_desc'])) ? trim($_POST['educacion_comp_desc']) : NULL;
        $educacion_comp_dias = isset($_POST['educacion_comp_dias']) && !empty(trim($_POST['educacion_comp_dias'])) ? trim($_POST['educacion_comp_dias']) : NULL;
        $educacion_comp_hora = isset($_POST['educacion_comp_hora']) && !empty(trim($_POST['educacion_comp_hora'])) ? trim($_POST['educacion_comp_hora']) : NULL;

        $deporte_entrena     = isset($_POST['deporte_entrena']) && !empty(trim($_POST['deporte_entrena'])) ? trim($_POST['deporte_entrena']) : NULL;
        $deporte_dias        = isset($_POST['deporte_dias']) && !empty(trim($_POST['deporte_dias'])) ? trim($_POST['deporte_dias']) : NULL;
        $deporte_hora        = isset($_POST['deporte_hora']) && !empty(trim($_POST['deporte_hora'])) ? trim($_POST['deporte_hora']) : NULL;

        $trabajo_ocupacion   = isset($_POST['trabajo_ocupacion']) && !empty(trim($_POST['trabajo_ocupacion'])) ? trim($_POST['trabajo_ocupacion']) : NULL;
        $trabajo_dias        = isset($_POST['trabajo_dias']) && !empty(trim($_POST['trabajo_dias'])) ? trim($_POST['trabajo_dias']) : NULL;
        $trabajo_hora        = isset($_POST['trabajo_hora']) && !empty(trim($_POST['trabajo_hora'])) ? trim($_POST['trabajo_hora']) : NULL;

        $stmt_act = mysqli_prepare($conexion, "UPDATE actividades_extracurriculares SET educacion_complementaria_desc=?, educacion_comp_dias=?, educacion_comp_horario=?, deporte_entrenamiento=?, deporte_dias=?, deporte_horario=?, trabajo_ocupacion=?, trabajo_dias=?, trabajo_horario=? WHERE id_ficha=?");
        mysqli_stmt_bind_param($stmt_act, "sssssssssi", $educacion_comp_desc, $educacion_comp_dias, $educacion_comp_hora, $deporte_entrena, $deporte_dias, $deporte_hora, $trabajo_ocupacion, $trabajo_dias, $trabajo_hora, $id_ficha);
        mysqli_stmt_execute($stmt_act);
        mysqli_stmt_close($stmt_act);

        // F. Actualizar Recursos Tecnológicos
        $tiene_computador          = isset($_POST['tiene_computador']) ? 1 : 0;
        $tiene_internet            = isset($_POST['tiene_internet']) ? 1 : 0;
        $tiene_tv_suscripcion      = isset($_POST['tiene_tv_suscripcion']) ? 1 : 0;
        $tiene_celular_personal    = isset($_POST['tiene_celular_personal']) ? 1 : 0;
        $cuenta_servicios_publicos = isset($_POST['cuenta_servicios_publicos']) ? 1 : 0;

        $stmt_rec = mysqli_prepare($conexion, "UPDATE recursos_tecnologicos_hogar SET tiene_computador=?, tiene_internet=?, tiene_tv_suscripcion=?, tiene_celular_personal=?, cuenta_servicios_publicos=? WHERE id_ficha=?");
        mysqli_stmt_bind_param($stmt_rec, "iiiiii", $tiene_computador, $tiene_internet, $tiene_tv_suscripcion, $tiene_celular_personal, $cuenta_servicios_publicos, $id_ficha);
        mysqli_stmt_execute($stmt_rec);
        mysqli_stmt_close($stmt_rec);

        // G. Actualizar Catálogos
        mysqli_query($conexion, "DELETE FROM ficha_catalogos_detalle WHERE id_ficha = $id_ficha");
        if (!empty($_POST['catalogos']) && is_array($_POST['catalogos'])) {
            $stmt_cat_det = mysqli_prepare($conexion, "INSERT INTO ficha_catalogos_detalle (id_ficha, id_catalogo) VALUES (?, ?)");
            foreach ($_POST['catalogos'] as $id_cat) {
                $id_c = intval($id_cat);
                mysqli_stmt_bind_param($stmt_cat_det, "ii", $id_ficha, $id_c);
                mysqli_stmt_execute($stmt_cat_det);
            }
            mysqli_stmt_close($stmt_cat_det);
        }

        mysqli_commit($conexion);
        $mensaje_exito = "¡La ficha de caracterización ha sido actualizada correctamente!";
    } catch (Exception $e) {
        mysqli_rollback($conexion);
        $mensaje_error = "Error al actualizar la ficha: " . $e->getMessage();
    }
}

// ==============================================================================
// 2. CARGAR DATOS EXISTENTES DE LA FICHA
// ==============================================================================
$sql_get_ficha = "SELECT f.*, e.*, fam.*, s.*, act.*, rec.* 
                  FROM fichas_caracterizacion f 
                  INNER JOIN estudiantes e ON f.id_estudiante = e.id_estudiante 
                  LEFT JOIN datos_familiares_hogar fam ON f.id_ficha = fam.id_ficha 
                  LEFT JOIN salud_estudiante s ON f.id_ficha = s.id_ficha 
                  LEFT JOIN actividades_extracurriculares act ON f.id_ficha = act.id_ficha 
                  LEFT JOIN recursos_tecnologicos_hogar rec ON f.id_ficha = rec.id_ficha 
                  WHERE f.id_ficha = ? LIMIT 1";
$stmt_get = mysqli_prepare($conexion, $sql_get_ficha);
mysqli_stmt_bind_param($stmt_get, "i", $id_ficha);
mysqli_stmt_execute($stmt_get);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_get));
mysqli_stmt_close($stmt_get);

if (!$data) {
    header("Location: grados.php");
    exit();
}

// Cargar Cursos
$cursos = [];
$res_cursos = mysqli_query($conexion, "SELECT c.id_curso, c.nombre_curso, c.jornada, s.nombre_sede FROM grados_cursos c INNER JOIN sedes s ON c.id_sede = s.id_sede ORDER BY c.nombre_curso ASC");
if ($res_cursos) {
    while ($row = mysqli_fetch_assoc($res_cursos)) {
        $cursos[] = $row;
    }
}

// Cargar Catálogos Generales
$catalogos = [];
$res_cat = mysqli_query($conexion, "SELECT id_catalogo, categoria, descripcion FROM catalogos_caracterizacion ORDER BY categoria, id_catalogo");
if ($res_cat) {
    while ($row = mysqli_fetch_assoc($res_cat)) {
        $catalogos[$row['categoria']][] = $row;
    }
}

// Cargar Catálogos Seleccionados por este Estudiante
$catalogos_marcados = [];
$res_mar = mysqli_query($conexion, "SELECT id_catalogo FROM ficha_catalogos_detalle WHERE id_ficha = $id_ficha");
if ($res_mar) {
    while ($m = mysqli_fetch_assoc($res_mar)) {
        $catalogos_marcados[] = $m['id_catalogo'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Caracterización - <?php echo htmlspecialchars($data['nombres_apellidos']); ?></title>
    <!-- Bootstrap 5.3 CSS -->
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
        .navbar-brand { letter-spacing: -0.5px; }
        .card { border: none; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1); }
        .card-header-title { 
            background: linear-gradient(135deg, #14532d 0%, #052e16 100%); 
            color: white; 
        }
        .nav-tabs { border-bottom: 2px solid #e2e8f0; }
        .nav-tabs .nav-link { 
            color: #64748b; 
            font-weight: 500; 
            border: none; 
            padding: 0.75rem 1.25rem;
            transition: all 0.2s ease-in-out;
        }
        .nav-tabs .nav-link:hover { color: var(--accent-color); isolation: isolate; }
        .nav-tabs .nav-link.active { 
            color: var(--accent-color); 
            background: transparent; 
            font-weight: 600; 
            border-bottom: 3px solid var(--accent-color); 
        }
        .form-label { font-size: 0.875rem; color: #475569; margin-bottom: 0.35rem; }
        .form-control, .form-select { 
            padding: 0.6rem 0.85rem; 
            font-size: 0.925rem; 
            border-color: #cbd5e1; 
            border-radius: 0.5rem; 
        }
        .form-control:focus, .form-select:focus { 
            border-color: var(--accent-color); 
            box-shadow: 0 0 0 0.25rem rgba(21, 128, 61, 0.15); 
        }
        .section-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1e293b;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 0.5rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
        }
        .section-title i { margin-right: 0.5rem; color: var(--accent-color); }
        .custom-card-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1.25rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.05);
            margin-bottom: 1rem;
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
        /* Estilo indicador campos obligatorios */
        .form-label.required::after {
            content: " *";
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>

<!-- Navbar Institucional -->
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm py-3" style="background-color: var(--primary-color) !important;">
    <div class="container">
        <a class="navbar-brand fw-bold fs-5 text-white" href="#">
            <i class="bi bi-mortarboard-fill text-light me-2"></i> IE Técnico Superior <span class="fw-light fs-6 opacity-75">| Neiva</span>
        </a>
        <?php 
            if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Profesor') {
                $url_volver = '../profesor/panel_profesor.php';
            } else {
                $url_volver = (isset($data['id_curso']) && $data['id_curso'] > 0) 
                              ? "estudiantes_curso.php?id_curso=" . $data['id_curso'] 
                              : "grados.php";
            }
        ?>
        <a href="<?php echo $url_volver; ?>" class="btn btn-outline-light btn-sm px-3 rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>
</nav>

<div class="container py-5">

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

    <!-- Alerta emergente de advertencia (naranja) para campos obligatorios vacíos -->
    <div id="alerta-validacion" class="alert alert-warning alert-dismissible fade d-none shadow-sm rounded-3 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i><strong>¡Campos obligatorios incompletos!</strong> Por favor, rellena todas las casillas obligatorias marcadas con asterisco (*) antes de guardar. Revisa las pestañas del formulario.
        <button type="button" class="btn-close" onclick="ocultarAlertaValidacion()"></button>
    </div>

    <div class="card rounded-4 overflow-hidden">
        <div class="card-header card-header-title p-4 px-md-5">
            <h3 class="fw-bold mb-1"><i class="bi bi-pencil-square me-2 text-light"></i>Editar Ficha de Caracterización</h3>
            <p class="mb-0 small opacity-75">Estudiante: <strong><?php echo htmlspecialchars($data['nombres_apellidos']); ?></strong></p>
        </div>
        <div class="card-body p-4 p-md-5">

            <form method="POST" action="" class="needs-validation" id="formCaracterizacion" novalidate>
                <input type="hidden" name="id_estudiante" value="<?php echo $data['id_estudiante']; ?>">

                <!-- Pestañas de Navegación -->
                <ul class="nav nav-tabs mb-4 flex-column flex-sm-row" id="fichaTab" role="tablist">
                    <li class="nav-item" role="presentation"><button class="nav-link active" id="estudiante-tab" data-bs-toggle="tab" data-bs-target="#estudiante" type="button"><i class="bi bi-person-fill me-1"></i> 1. Estudiante</button></li>
                    <li class="nav-item" role="presentation"><button class="nav-link" id="familia-tab" data-bs-toggle="tab" data-bs-target="#familia" type="button"><i class="bi bi-people-fill me-1"></i> 2. Familia e Ingresos</button></li>
                    <li class="nav-item" role="presentation"><button class="nav-link" id="salud-tab" data-bs-toggle="tab" data-bs-target="#salud" type="button"><i class="bi bi-heart-pulse-fill me-1"></i> 3. Salud y Necesidades</button></li>
                    <li class="nav-item" role="presentation"><button class="nav-link" id="actividades-tab" data-bs-toggle="tab" data-bs-target="#actividades" type="button"><i class="bi bi-controller me-1"></i> 4. Actividades y Entorno</button></li>
                </ul>

                <div class="tab-content" id="fichaTabContent">

                    <!-- SECCIÓN 1: DATOS DEL ESTUDIANTE -->
                    <div class="tab-pane fade show active" id="estudiante">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold required">Fecha Diligenciamiento</label>
                                <input type="date" name="fecha_diligenciamiento" class="form-control" value="<?php echo htmlspecialchars($data['fecha_diligenciamiento']); ?>" required>
                                <div class="invalid-feedback">Este campo es obligatorio.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold required">Curso / Grado</label>
                                <select name="id_curso" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($cursos as $c): ?>
                                        <option value="<?php echo $c['id_curso']; ?>" <?php echo ($c['id_curso'] == $data['id_curso']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($c['nombre_curso'] . ' (' . $c['jornada'] . ' - ' . $c['nombre_sede'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Seleccione un curso.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold required">Nombres y Apellidos del Estudiante</label>
                                <input type="text" name="nombres_apellidos" class="form-control" value="<?php echo htmlspecialchars($data['nombres_apellidos']); ?>" required>
                                <div class="invalid-feedback">Ingrese los nombres y apellidos.</div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold required">Tipo Documento</label>
                                <select name="tipo_documento" class="form-select" required>
                                    <option value="TI" <?php echo ($data['tipo_documento'] == 'TI') ? 'selected' : ''; ?>>T.I.</option>
                                    <option value="RC" <?php echo ($data['tipo_documento'] == 'RC') ? 'selected' : ''; ?>>R.C.</option>
                                    <option value="CC" <?php echo ($data['tipo_documento'] == 'CC') ? 'selected' : ''; ?>>C.C.</option>
                                    <option value="Otro" <?php echo ($data['tipo_documento'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
                                </select>
                                <div class="invalid-feedback">Seleccione el tipo de documento.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold required">Nro. Documento</label>
                                <input type="text" name="numero_documento" class="form-control" value="<?php echo htmlspecialchars($data['numero_documento']); ?>" required inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                <div class="invalid-feedback">Ingrese el número de documento.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold required">Fecha Nacimiento</label>
                                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control" value="<?php echo htmlspecialchars($data['fecha_nacimiento']); ?>" required onchange="calcularEdad()">
                                <div class="invalid-feedback">Seleccione la fecha de nacimiento.</div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-semibold required">Edad</label>
                                <input type="number" name="edad" id="edad" class="form-control bg-light" value="<?php echo htmlspecialchars($data['edad']); ?>" required readonly tabindex="-1" style="pointer-events: none;">
                                <div class="invalid-feedback">La edad es requerida.</div>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label fw-semibold required">G. Sanguíneo</label>
                                <input type="text" name="grupo_sanguineo" class="form-control" value="<?php echo htmlspecialchars($data['grupo_sanguineo']); ?>" required>
                                <div class="invalid-feedback">Requerido.</div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold required">Tipo Estudiante</label>
                                <select name="tipo_estudiante" id="tipo_estudiante" class="form-select" required onchange="validarColegioProcedencia()">
                                    <option value="Antiguo" <?php echo ($data['tipo_estudiante'] == 'Antiguo') ? 'selected' : ''; ?>>Antiguo</option>
                                    <option value="Antiguo Repitente" <?php echo ($data['tipo_estudiante'] == 'Antiguo Repitente') ? 'selected' : ''; ?>>Antiguo Repitente</option>
                                    <option value="Nuevo" <?php echo ($data['tipo_estudiante'] == 'Nuevo') ? 'selected' : ''; ?>>Nuevo</option>
                                    <option value="Nuevo Repitente" <?php echo ($data['tipo_estudiante'] == 'Nuevo Repitente') ? 'selected' : ''; ?>>Nuevo Repitente</option>
                                </select>
                                <div class="invalid-feedback">Seleccione el tipo de estudiante.</div>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Colegio de Procedencia</label>
                                <input type="text" name="colegio_procedencia" id="colegio_procedencia" class="form-control" value="<?php echo htmlspecialchars($data['colegio_procedencia'] ?? ''); ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold required">Dirección Residencia</label>
                                <input type="text" name="direccion_residencia" class="form-control" value="<?php echo htmlspecialchars($data['direccion_residencia']); ?>" required>
                                <div class="invalid-feedback">Ingrese la dirección.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold required">Barrio</label>
                                <input type="text" name="barrio" class="form-control" value="<?php echo htmlspecialchars($data['barrio']); ?>" required>
                                <div class="invalid-feedback">Ingrese el barrio.</div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-semibold required">Comuna</label>
                                <input type="text" name="comuna" id="comuna" class="form-control" value="<?php echo htmlspecialchars($data['comuna']); ?>" required inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                <div class="invalid-feedback">Ingrese comuna.</div>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label fw-semibold required">Estrato</label>
                                <input type="number" name="estrato" id="estrato" class="form-control" value="<?php echo htmlspecialchars($data['estrato']); ?>" required min="1" max="6" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                <div class="invalid-feedback">Requerido.</div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold required">Servicio Salud (EPS)</label>
                                <input type="text" name="servicio_salud" class="form-control" value="<?php echo htmlspecialchars($data['servicio_salud']); ?>" required oninput="this.value = this.value.replace(/[0-9]/g, '')">
                                <div class="invalid-feedback">Ingrese la EPS.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Teléfono Fijo</label>
                                <input type="text" name="telefono_fijo" class="form-control" value="<?php echo htmlspecialchars($data['telefono_fijo'] ?? ''); ?>" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Teléfono Móvil Estudiante</label>
                                <input type="text" name="telefono_movil" class="form-control" value="<?php echo htmlspecialchars($data['telefono_movil'] ?? ''); ?>" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Móvil Padre</label>
                                <input type="text" name="tel_movil_padre" class="form-control" value="<?php echo htmlspecialchars($data['tel_movil_padre'] ?? ''); ?>" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Móvil Madre</label>
                                <input type="text" name="tel_movil_madre" class="form-control" value="<?php echo htmlspecialchars($data['tel_movil_madre'] ?? ''); ?>" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Correo Padres</label>
                                <input type="email" name="email_padres" class="form-control" value="<?php echo htmlspecialchars($data['email_padres'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: FAMILIA E INGRESOS -->
                    <div class="tab-pane fade" id="familia">
                        <div class="section-title"><i class="bi bi-person-badge"></i> Información del Acudiente y Padres</div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold required">Nombre del Acudiente</label>
                                <input type="text" name="acudiente_nombre" class="form-control" value="<?php echo htmlspecialchars($data['acudiente_nombre']); ?>" required>
                                <div class="invalid-feedback">Ingrese el nombre del acudiente.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold required">Parentesco</label>
                                <input type="text" name="acudiente_parentesco" class="form-control" value="<?php echo htmlspecialchars($data['acudiente_parentesco']); ?>" required>
                                <div class="invalid-feedback">Ingrese el parentesco.</div>
                            </div>

                            <!-- Padre -->
                            <div class="col-md-4"><label class="form-label">Nombre del Padre</label><input type="text" name="padre_nombres" class="form-control" value="<?php echo htmlspecialchars($data['padre_nombres'] ?? ''); ?>"></div>
                            <div class="col-md-2"><label class="form-label">Escolaridad</label><input type="text" name="padre_escolaridad" class="form-control" value="<?php echo htmlspecialchars($data['padre_escolaridad'] ?? ''); ?>"></div>
                            <div class="col-md-2"><label class="form-label">Edad Padre</label><input type="text" name="padre_edad" class="form-control" value="<?php echo htmlspecialchars($data['padre_edad'] ?? ''); ?>" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></div>
                            <div class="col-md-2"><label class="form-label">Ocupación</label><input type="text" name="padre_ocupacion" class="form-control" value="<?php echo htmlspecialchars($data['padre_ocupacion'] ?? ''); ?>"></div>
                            <div class="col-md-2">
                                <label class="form-label">Tipo Ocupación</label>
                                <select name="padre_tipo_ocupacion" class="form-select">
                                    <option value="">Seleccione...</option>
                                    <option value="Empleado" <?php echo ($data['padre_tipo_ocupacion'] == 'Empleado') ? 'selected' : ''; ?>>Empleado</option>
                                    <option value="Independiente" <?php echo ($data['padre_tipo_ocupacion'] == 'Independiente') ? 'selected' : ''; ?>>Independiente</option>
                                </select>
                            </div>

                            <!-- Madre -->
                            <div class="col-md-4"><label class="form-label">Nombre de la Madre</label><input type="text" name="madre_nombres" class="form-control" value="<?php echo htmlspecialchars($data['madre_nombres'] ?? ''); ?>"></div>
                            <div class="col-md-2"><label class="form-label">Escolaridad</label><input type="text" name="madre_escolaridad" class="form-control" value="<?php echo htmlspecialchars($data['madre_escolaridad'] ?? ''); ?>"></div>
                            <div class="col-md-2"><label class="form-label">Edad Madre</label><input type="text" name="madre_edad" class="form-control" value="<?php echo htmlspecialchars($data['madre_edad'] ?? ''); ?>" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></div>
                            <div class="col-md-2"><label class="form-label">Ocupación</label><input type="text" name="madre_ocupacion" class="form-control" value="<?php echo htmlspecialchars($data['madre_ocupacion'] ?? ''); ?>"></div>
                            <div class="col-md-2">
                                <label class="form-label">Tipo Ocupación</label>
                                <select name="madre_tipo_ocupacion" class="form-select">
                                    <option value="">Seleccione...</option>
                                    <option value="Empleado" <?php echo ($data['madre_tipo_ocupacion'] == 'Empleado') ? 'selected' : ''; ?>>Empleado</option>
                                    <option value="Independiente" <?php echo ($data['madre_tipo_ocupacion'] == 'Independiente') ? 'selected' : ''; ?>>Independiente</option>
                                </select>
                            </div>
                        </div>

                        <div class="section-title"><i class="bi bi-house-door"></i> Vivienda y Composición Familiar</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold required">Ingresos Económicos del Hogar</label>
                                <select name="ingresos_hogar" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Menos de 1 SMLV" <?php echo ($data['ingresos_hogar'] == 'Menos de 1 SMLV') ? 'selected' : ''; ?>>Menos de 1 SMLV</option>
                                    <option value="Entre 1 y 2 SMLV" <?php echo ($data['ingresos_hogar'] == 'Entre 1 y 2 SMLV') ? 'selected' : ''; ?>>Entre 1 y 2 SMLV</option>
                                    <option value="Más de 2 SMLV" <?php echo ($data['ingresos_hogar'] == 'Más de 2 SMLV') ? 'selected' : ''; ?>>Más de 2 SMLV</option>
                                </select>
                                <div class="invalid-feedback">Seleccione los ingresos.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold required">Tenencia de Vivienda</label>
                                <select name="tenencia_vivienda" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Propia" <?php echo ($data['tenencia_vivienda'] == 'Propia') ? 'selected' : ''; ?>>Propia</option>
                                    <option value="Arrendada" <?php echo ($data['tenencia_vivienda'] == 'Arrendada') ? 'selected' : ''; ?>>Arrendada</option>
                                    <option value="De familiares" <?php echo ($data['tenencia_vivienda'] == 'De familiares') ? 'selected' : ''; ?>>De familiares</option>
                                    <option value="Otro" <?php echo ($data['tenencia_vivienda'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
                                </select>
                                <div class="invalid-feedback">Seleccione la tenencia.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold required">Convive en casa con</label>
                                <select name="convive_con" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Mis padres y hermanos" <?php echo ($data['convive_con'] == 'Mis padres y hermanos') ? 'selected' : ''; ?>>Mis padres y hermanos</option>
                                    <option value="Padres, hermanos y otros familiares" <?php echo ($data['convive_con'] == 'Padres, hermanos y otros familiares') ? 'selected' : ''; ?>>Padres, hermanos y otros familiares</option>
                                    <option value="Hermanos y uno de mis padres" <?php echo ($data['convive_con'] == 'Hermanos y uno de mis padres') ? 'selected' : ''; ?>>Hermanos y uno de mis padres</option>
                                    <option value="Hermanos, otros familiares y uno de mis padres" <?php echo ($data['convive_con'] == 'Hermanos, otros familiares y uno de mis padres') ? 'selected' : ''; ?>>Hermanos, otros familiares y uno de mis padres</option>
                                    <option value="Únicamente otros familiares" <?php echo ($data['convive_con'] == 'Únicamente otros familiares') ? 'selected' : ''; ?>>Únicamente otros familiares</option>
                                </select>
                                <div class="invalid-feedback">Seleccione con quién convive.</div>
                            </div>

                            <div class="col-md-6 pt-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="es_hijo_unico" id="es_hijo_unico" <?php echo ($data['es_hijo_unico'] == 1) ? 'checked' : ''; ?> onchange="validarHijoUnico()">
                                    <label class="form-check-label fw-semibold" for="es_hijo_unico">Es Hijo Único</label>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="hermanos_en_colegio" id="hermanos_en_colegio" <?php echo ($data['hermanos_en_colegio'] == 1) ? 'checked' : ''; ?> onchange="validarHijoUnico()">
                                    <label class="form-check-label fw-semibold" for="hermanos_en_colegio">Tiene hermanos en el colegio</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold required">Total personas que viven en su hogar</label>
                                <input type="number" name="total_personas_hogar" id="total_personas_hogar" class="form-control" value="<?php echo htmlspecialchars($data['total_personas_hogar']); ?>" required min="1" max="30" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                <div class="invalid-feedback">Indique el total de personas.</div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 3: SALUD Y NECESIDADES ESPECIALES -->
                    <div class="tab-pane fade" id="salud">
                        <div class="section-title"><i class="bi bi-file-medical"></i> Diagnóstico Médico</div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Enfermedad Diagnosticada</label>
                                <input type="text" name="enfermedad_diagnosticada" class="form-control" value="<?php echo htmlspecialchars($data['enfermedad_diagnosticada'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tratamiento Médico Recibido</label>
                                <input type="text" name="recibe_tratamiento_medico" class="form-control" value="<?php echo htmlspecialchars($data['recibe_tratamiento_medico'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Medicamento que consume</label>
                                <input type="text" name="consume_medicamento" class="form-control" value="<?php echo htmlspecialchars($data['consume_medicamento'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="custom-card-box">
                                    <h6 class="fw-bold border-bottom pb-2 text-success"><i class="bi bi-shield-exclamation me-1"></i> Discapacidad</h6>
                                    <?php if (!empty($catalogos['Discapacidad'])): ?>
                                        <?php foreach ($catalogos['Discapacidad'] as $cat): ?>
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="catalogos[]" value="<?php echo $cat['id_catalogo']; ?>" id="cat_<?php echo $cat['id_catalogo']; ?>" <?php echo in_array($cat['id_catalogo'], $catalogos_marcados) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="cat_<?php echo $cat['id_catalogo']; ?>"><?php echo htmlspecialchars($cat['descripcion']); ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-muted small mb-0">No hay registros disponibles.</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="custom-card-box">
                                    <h6 class="fw-bold border-bottom pb-2 text-success"><i class="bi bi-book me-1"></i> Trastorno del Aprendizaje</h6>
                                    <?php if (!empty($catalogos['Trastorno Aprendizaje'])): ?>
                                        <?php foreach ($catalogos['Trastorno Aprendizaje'] as $cat): ?>
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="catalogos[]" value="<?php echo $cat['id_catalogo']; ?>" id="cat_<?php echo $cat['id_catalogo']; ?>" <?php echo in_array($cat['id_catalogo'], $catalogos_marcados) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="cat_<?php echo $cat['id_catalogo']; ?>"><?php echo htmlspecialchars($cat['descripcion']); ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-muted small mb-0">No hay registros disponibles.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 4: ACTIVIDADES, RECURSOS Y EXCEPCIONALIDAD -->
                    <div class="tab-pane fade" id="actividades">
                        
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="custom-card-box h-100">
                                    <h6 class="fw-bold border-bottom pb-2 text-success"><i class="bi bi-star-fill me-1"></i> Excepcionalidad Demostrable</h6>
                                    <?php if (!empty($catalogos['Excepcionalidad'])): ?>
                                        <?php foreach ($catalogos['Excepcionalidad'] as $cat): ?>
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="catalogos[]" value="<?php echo $cat['id_catalogo']; ?>" id="cat_<?php echo $cat['id_catalogo']; ?>" <?php echo in_array($cat['id_catalogo'], $catalogos_marcados) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="cat_<?php echo $cat['id_catalogo']; ?>"><?php echo htmlspecialchars($cat['descripcion']); ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-muted small">No hay ítems registrados.</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="custom-card-box h-100">
                                    <h6 class="fw-bold border-bottom pb-2 text-success"><i class="bi bi-people-fill me-1"></i> Grupo Poblacional</h6>
                                    <?php if (!empty($catalogos['Grupo Poblacional'])): ?>
                                        <?php foreach ($catalogos['Grupo Poblacional'] as $cat): ?>
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="catalogos[]" value="<?php echo $cat['id_catalogo']; ?>" id="cat_<?php echo $cat['id_catalogo']; ?>" <?php echo in_array($cat['id_catalogo'], $catalogos_marcados) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="cat_<?php echo $cat['id_catalogo']; ?>"><?php echo htmlspecialchars($cat['descripcion']); ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-muted small">No hay ítems registrados.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="section-title"><i class="bi bi-journal-check"></i> Actividades Extracurriculares</div>

                        <div class="row g-3 mb-3 custom-card-box">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Educación Complementaria (Cursos, Inglés, Música, etc.)</label>
                                <input type="text" name="educacion_comp_desc" class="form-control" value="<?php echo htmlspecialchars($data['educacion_complementaria_desc'] ?? ''); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Días</label>
                                <input type="text" name="educacion_comp_dias" class="form-control" value="<?php echo htmlspecialchars($data['educacion_comp_dias'] ?? ''); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Horario</label>
                                <input type="text" name="educacion_comp_hora" class="form-control" value="<?php echo htmlspecialchars($data['educacion_comp_horario'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-3 custom-card-box">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Entrenamiento Deportivo</label>
                                <input type="text" name="deporte_entrena" class="form-control" value="<?php echo htmlspecialchars($data['deporte_entrenamiento'] ?? ''); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Días</label>
                                <input type="text" name="deporte_dias" class="form-control" value="<?php echo htmlspecialchars($data['deporte_dias'] ?? ''); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Horario</label>
                                <input type="text" name="deporte_hora" class="form-control" value="<?php echo htmlspecialchars($data['deporte_horario'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-4 custom-card-box">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Joven Trabajador (Ocupación)</label>
                                <input type="text" name="trabajo_ocupacion" class="form-control" value="<?php echo htmlspecialchars($data['trabajo_ocupacion'] ?? ''); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Días</label>
                                <input type="text" name="trabajo_dias" class="form-control" value="<?php echo htmlspecialchars($data['trabajo_dias'] ?? ''); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Horario</label>
                                <input type="text" name="trabajo_hora" class="form-control" value="<?php echo htmlspecialchars($data['trabajo_horario'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="section-title"><i class="bi bi-laptop"></i> Recursos Tecnológicos y Transporte</div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="fw-semibold d-block mb-2">Recursos en Casa:</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="tiene_computador" id="comp" <?php echo ($data['tiene_computador'] == 1) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="comp">Computador</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="tiene_internet" id="net" <?php echo ($data['tiene_internet'] == 1) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="net">Internet</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="tiene_tv_suscripcion" id="tv" <?php echo ($data['tiene_tv_suscripcion'] == 1) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="tv">TV Suscripción</label>
                                </div>
                                <div class="form-check form-check-inline mt-2">
                                    <input class="form-check-input" type="checkbox" name="tiene_celular_personal" id="cel" <?php echo ($data['tiene_celular_personal'] == 1) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="cel">Celular Personal</label>
                                </div>
                                <div class="form-check form-check-inline mt-2">
                                    <input class="form-check-input" type="checkbox" name="cuenta_servicios_publicos" id="serv" <?php echo ($data['cuenta_servicios_publicos'] == 1) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="serv">Servicios Públicos</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold required">Medio de Transporte</label>
                                <select name="medio_transporte" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Caminando" <?php echo ($data['medio_transporte'] == 'Caminando') ? 'selected' : ''; ?>>Caminando</option>
                                    <option value="Servicio Público" <?php echo ($data['medio_transporte'] == 'Servicio Público') ? 'selected' : ''; ?>>Servicio Público</option>
                                    <option value="Vehículo familiar" <?php echo ($data['medio_transporte'] == 'Vehículo familiar') ? 'selected' : ''; ?>>Vehículo familiar</option>
                                    <option value="Bicicleta" <?php echo ($data['medio_transporte'] == 'Bicicleta') ? 'selected' : ''; ?>>Bicicleta</option>
                                    <option value="Motocicleta personal" <?php echo ($data['medio_transporte'] == 'Motocicleta personal') ? 'selected' : ''; ?>>Motocicleta personal</option>
                                    <option value="Pago Ruta" <?php echo ($data['medio_transporte'] == 'Pago Ruta') ? 'selected' : ''; ?>>Pago Ruta</option>
                                </select>
                                <div class="invalid-feedback">Seleccione un medio.</div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold required">Acompañamiento en Casa</label>
                                <select name="acompanamiento_jornada_contraria" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Hermanos" <?php echo ($data['acompanamiento_jornada_contraria'] == 'Hermanos') ? 'selected' : ''; ?>>Hermanos</option>
                                    <option value="Uno de mis padres" <?php echo ($data['acompanamiento_jornada_contraria'] == 'Uno de mis padres') ? 'selected' : ''; ?>>Uno de mis padres</option>
                                    <option value="Familiares" <?php echo ($data['acompanamiento_jornada_contraria'] == 'Familiares') ? 'selected' : ''; ?>>Familiares</option>
                                    <option value="Otros no familiares" <?php echo ($data['acompanamiento_jornada_contraria'] == 'Otros no familiares') ? 'selected' : ''; ?>>Otros no familiares</option>
                                    <option value="Normalmente solo" <?php echo ($data['acompanamiento_jornada_contraria'] == 'Normalmente solo') ? 'selected' : ''; ?>>Normalmente solo</option>
                                </select>
                                <div class="invalid-feedback">Seleccione un acompañamiento.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Observaciones de Orientación Escolar</label>
                            <textarea name="observaciones_orientacion" class="form-control" rows="3"><?php echo htmlspecialchars($data['observaciones_orientacion'] ?? ''); ?></textarea>
                        </div>

                    </div>

                </div>

                <!-- Botón de Envío General -->
                <div class="border-top pt-4 mt-4 d-flex justify-content-end">
                    <button type="submit" name="actualizar_ficha" class="btn btn-institutional btn-lg fw-bold rounded-pill px-5 shadow">
                        <i class="bi bi-save2-fill me-2"></i>Guardar Cambios de Caracterización
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<footer class="text-center py-4 text-muted small">
    &copy; <?php echo date('Y'); ?> IE Técnico Superior - Neiva. Todos los derechos reservados.
</footer>

<!-- Bootstrap 5.3 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Scripts de lógica y validación -->
<script>
function calcularEdad() {
    const inputFecha = document.getElementById('fecha_nacimiento').value;
    const inputEdad = document.getElementById('edad');

    if (!inputFecha) {
        inputEdad.value = '';
        return;
    }

    const fechaNacimiento = new Date(inputFecha);
    const hoy = new Date();

    let edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
    const diferenciaMeses = hoy.getMonth() - fechaNacimiento.getMonth();

    if (diferenciaMeses < 0 || (diferenciaMeses === 0 && hoy.getDate() < fechaNacimiento.getDate())) {
        edad--;
    }

    if (edad >= 0) {
        inputEdad.value = edad;
    } else {
        inputEdad.value = '';
    }
}

function validarColegioProcedencia() {
    const tipoEstudiante = document.getElementById('tipo_estudiante').value;
    const inputColegio = document.getElementById('colegio_procedencia');

    if (tipoEstudiante === 'Antiguo' || tipoEstudiante === 'Antiguo Repitente') {
        inputColegio.value = '';
        inputColegio.disabled = true;
        inputColegio.placeholder = 'No aplica para estudiantes antiguos';
        inputColegio.classList.add('bg-light');
    } else {
        inputColegio.disabled = false;
        inputColegio.placeholder = 'Nombre del colegio anterior';
        inputColegio.classList.remove('bg-light');
    }
}

function validarHijoUnico() {
    const chkHijoUnico = document.getElementById('es_hijo_unico');
    const chkHermanos = document.getElementById('hermanos_en_colegio');

    if (chkHijoUnico.checked) {
        chkHermanos.checked = false;
        chkHermanos.disabled = true;
    } else {
        chkHermanos.disabled = false;
    }

    if (chkHermanos.checked) {
        chkHijoUnico.checked = false;
        chkHijoUnico.disabled = true;
    } else if (!chkHijoUnico.checked) {
        chkHijoUnico.disabled = false;
    }
}

function ocultarAlertaValidacion() {
    const alerta = document.getElementById('alerta-validacion');
    alerta.classList.remove('show');
    alerta.classList.add('d-none');
}

document.addEventListener('DOMContentLoaded', function() {
    validarColegioProcedencia();
    validarHijoUnico();
});

// Script de validación nativa Bootstrap y alerta de advertencia naranja superior
(function () {
    'use strict'
    const form = document.getElementById('formCaracterizacion');
    const alertaValidacion = document.getElementById('alerta-validacion');

    form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();

            // Mostrar banner de advertencia naranja
            alertaValidacion.classList.remove('d-none');
            alertaValidacion.classList.add('show');

            // Desplazar la ventana hacia arriba para ver el aviso
            window.scrollTo({ top: 150, behavior: 'smooth' });
        } else {
            alertaValidacion.classList.remove('show');
            alertaValidacion.classList.add('d-none');
        }

        form.classList.add('was-validated');
    }, false);
})();
</script>

</body>
</html>