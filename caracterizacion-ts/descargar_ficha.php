<?php
session_start();
require_once 'inc/conexion.php';

// Validar inicio de sesión
if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] !== 'Administrador' && $_SESSION['rol'] !== 'Profesor')) {
    header("Location: ../index.php");
    exit();
}

$conexion = conectarBD();
$id_ficha = isset($_GET['id_ficha']) ? intval($_GET['id_ficha']) : 0;

if ($id_ficha <= 0) {
    header("Location: ../administrador/grados.php");
    exit();
}

// Consultar todos los datos de la ficha y del estudiante
$sql_get_ficha = "SELECT f.*, e.*, fam.*, s.*, act.*, rec.*, c.nombre_curso, c.jornada, sd.nombre_sede 
                  FROM fichas_caracterizacion f 
                  INNER JOIN estudiantes e ON f.id_estudiante = e.id_estudiante 
                  LEFT JOIN datos_familiares_hogar fam ON f.id_ficha = fam.id_ficha 
                  LEFT JOIN salud_estudiante s ON f.id_ficha = s.id_ficha 
                  LEFT JOIN actividades_extracurriculares act ON f.id_ficha = act.id_ficha 
                  LEFT JOIN recursos_tecnologicos_hogar rec ON f.id_ficha = rec.id_ficha 
                  LEFT JOIN grados_cursos c ON f.id_curso = c.id_curso
                  LEFT JOIN sedes sd ON c.id_sede = sd.id_sede
                  WHERE f.id_ficha = ? LIMIT 1";

$stmt_get = mysqli_prepare($conexion, $sql_get_ficha);
mysqli_stmt_bind_param($stmt_get, "i", $id_ficha);
mysqli_stmt_execute($stmt_get);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_get));
mysqli_stmt_close($stmt_get);

if (!$data) {
    echo "Ficha no encontrada.";
    exit();
}

// Obtener catálogos marcados
$catalogos_marcados_nombres = [];
$res_mar = mysqli_query($conexion, "SELECT c.categoria, c.descripcion FROM ficha_catalogos_detalle det INNER JOIN catalogos_caracterizacion c ON det.id_catalogo = c.id_catalogo WHERE det.id_ficha = $id_ficha");
if ($res_mar) {
    while ($m = mysqli_fetch_assoc($res_mar)) {
        $catalogos_marcados_nombres[$m['categoria']][] = $m['descripcion'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha de Caracterización - <?php echo htmlspecialchars($data['nombres_apellidos']); ?></title>
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
            font-size: 13px; 
            color: #334155; 
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        .sheet-container {
            max-width: 900px;
            background: #fff;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 8px;
        }
        .section-title {
            background-color: #e2e8f0;
            color: var(--primary-color);
            font-size: 14px;
            letter-spacing: 0.5px;
            border-left: 4px solid var(--primary-color);
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
        @media print {
            body { background: #fff; padding: 0 !important; }
            .no-print { display: none !important; }
            .sheet-container { box-shadow: none; border: none; padding: 0; width: 100%; max-width: 100%; }
        }
    </style>
</head>
<body class="py-5">

    <!-- Barra de Acciones Flotante (Se oculta al imprimir) -->
    <div class="container mb-3 no-print">
        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded-4 shadow-sm border">
            <div>
                <span class="text-muted small"><i class="bi bi-info-circle me-1"></i> Visualizando ficha de:</span>
                <h6 class="mb-0 fw-bold text-success"><?php echo htmlspecialchars($data['nombres_apellidos']); ?></h6>
            </div>
            <div>
                <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm rounded-3 me-2">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
                <button onclick="window.print()" class="btn btn-institutional btn-sm px-4 rounded-3 shadow-sm">
                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Descargar / Imprimir Ficha
                </button>
            </div>
        </div>
    </div>

    <!-- Contenedor Principal de la Ficha -->
    <div class="container sheet-container p-5 border">
        <!-- Encabezado Institucional -->
        <div class="text-center border-bottom pb-4 mb-4">
            <h4 class="fw-bold mb-1 text-uppercase text-success">INSTITUCIÓN EDUCATIVA TÉCNICO SUPERIOR</h4>
            <p class="mb-1 text-muted small">Sede: <strong><?php echo htmlspecialchars($data['nombre_sede'] ?? 'N/A'); ?></strong> | Jornada: <strong><?php echo htmlspecialchars($data['jornada'] ?? 'N/A'); ?></strong></p>
            <h5 class="fw-bold text-dark mt-2">FICHA DE CARACTERIZACIÓN ESTUDIANTIL</h5>
        </div>

        <!-- 1. Datos del Estudiante -->
        <div class="section-title p-2 fw-bold mb-3 rounded-end">1. DATOS PERSONALES DEL ESTUDIANTE</div>
        <div class="row g-3 mb-4">
            <div class="col-md-6"><strong>Nombres y Apellidos:</strong> <span class="text-muted"><?php echo htmlspecialchars($data['nombres_apellidos']); ?></span></div>
            <div class="col-md-3"><strong>Documento:</strong> <span class="text-muted"><?php echo htmlspecialchars($data['tipo_documento'] . ' ' . $data['numero_documento']); ?></span></div>
            <div class="col-md-3"><strong>Fecha Nacimiento:</strong> <span class="text-muted"><?php echo htmlspecialchars($data['fecha_nacimiento']); ?></span></div>
            <div class="col-md-3"><strong>Edad:</strong> <span class="text-muted"><?php echo htmlspecialchars($data['edad']); ?></span></div>
            <div class="col-md-3"><strong>Grupo Sanguíneo:</strong> <span class="text-muted"><?php echo htmlspecialchars($data['grupo_sanguineo']); ?></span></div>
            <div class="col-md-3"><strong>Curso:</strong> <span class="text-muted"><?php echo htmlspecialchars($data['nombre_curso']); ?></span></div>
            <div class="col-md-3"><strong>Tipo Estudiante:</strong> <span class="text-muted"><?php echo htmlspecialchars($data['tipo_estudiante']); ?></span></div>
            <div class="col-md-6"><strong>Dirección:</strong> <span class="text-muted"><?php echo htmlspecialchars($data['direccion_residencia'] . ' (Barrio: ' . $data['barrio'] . ' - Comuna ' . $data['comuna'] . ')'); ?></span></div>
            <div class="col-md-3"><strong>Estrato:</strong> <span class="text-muted"><?php echo htmlspecialchars($data['estrato']); ?></span></div>
            <div class="col-md-3"><strong>EPS:</strong> <span class="text-muted"><?php echo htmlspecialchars($data['servicio_salud']); ?></span></div>
            <div class="col-md-4"><strong>Teléfono Fijo:</strong> <span class="text-muted"><?php echo htmlspecialchars($data['telefono_fijo'] ?? 'N/A'); ?></span></div>
            <div class="col-md-4"><strong>Móvil Estudiante:</strong> <span class="text-muted"><?php echo htmlspecialchars($data['telefono_movil'] ?? 'N/A'); ?></span></div>
            <div class="col-md-4"><strong>Correo Padres:</strong> <span class="text-muted"><?php echo htmlspecialchars($data['email_padres'] ?? 'N/A'); ?></span></div>
        </div>

        <!-- 2. Familia e Ingresos -->
        <div class="section-title p-2 fw-bold mb-3 rounded-end">2. INFORMACIÓN FAMILIAR Y SOCIOECONÓMICA</div>
        <div class="row g-3 mb-4">
            <div class="col-md-8"><strong>Acudiente:</strong> <span class="text-muted"><?php echo htmlspecialchars($data['acudiente_nombre']); ?> (<?php echo htmlspecialchars($data['acudiente_parentesco']); ?>)</span></div>
            <div class="col-md-4"><strong>Ingresos Hogar:</strong> <span class="text-muted"><?php echo htmlspecialchars($data['ingresos_hogar']); ?></span></div>
            <div class="col-md-4"><strong>Tenencia Vivienda:</strong> <span class="text-muted"><?php echo htmlspecialchars($data['tenencia_vivienda']); ?></span></div>
            <div class="col-md-4"><strong>Convive con:</strong> <span class="text-muted"><?php echo htmlspecialchars($data['convive_con']); ?></span></div>
            <div class="col-md-4"><strong>Personas en Hogar:</strong> <span class="text-muted"><?php echo htmlspecialchars($data['total_personas_hogar']); ?></span></div>
            <div class="col-md-6"><strong>Padre:</strong> <span class="text-muted"><?php echo htmlspecialchars($data['padre_nombres'] ?? 'No registra'); ?> - Ocupación: <?php echo htmlspecialchars($data['padre_ocupacion'] ?? 'N/A'); ?></span></div>
            <div class="col-md-6"><strong>Madre:</strong> <span class="text-muted"><?php echo htmlspecialchars($data['madre_nombres'] ?? 'No registra'); ?> - Ocupación: <?php echo htmlspecialchars($data['madre_ocupacion'] ?? 'N/A'); ?></span></div>
        </div>

        <!-- 3. Salud y Necesidades -->
        <div class="section-title p-2 fw-bold mb-3 rounded-end">3. SALUD Y CONDICIONES ESPECÍFICAS</div>
        <div class="row g-3 mb-4">
            <div class="col-md-4"><strong>Enfermedad:</strong> <span class="text-muted"><?php echo htmlspecialchars($data['enfermedad_diagnosticada'] ?? 'Ninguna'); ?></span></div>
            <div class="col-md-4"><strong>Tratamiento:</strong> <span class="text-muted"><?php echo htmlspecialchars($data['recibe_tratamiento_medico'] ?? 'Ninguno'); ?></span></div>
            <div class="col-md-4"><strong>Medicamentos:</strong> <span class="text-muted"><?php echo htmlspecialchars($data['consume_medicamento'] ?? 'Ninguno'); ?></span></div>
            
            <div class="col-12 mt-2">
                <strong>Condiciones Especiales Registradas:</strong>
                <ul class="mt-1 text-muted">
                    <?php 
                    if (!empty($catalogos_marcados_nombres)) {
                        foreach ($catalogos_marcados_nombres as $cat => $items) {
                            echo "<li><strong>$cat:</strong> " . implode(', ', $items) . "</li>";
                        }
                    } else {
                        echo "<li>Ninguna registrada.</li>";
                    }
                    ?>
                </ul>
            </div>
        </div>

        <!-- 4. Recursos y Observaciones -->
        <div class="section-title p-2 fw-bold mb-3 rounded-end">4. RECURSOS Y OBSERVACIONES</div>
        <div class="row g-3 mb-4">
            <div class="col-md-6"><strong>Medio de Transporte:</strong> <span class="text-muted"><?php echo htmlspecialchars($data['medio_transporte']); ?></span></div>
            <div class="col-md-6"><strong>Acompañamiento Jornada Contraria:</strong> <span class="text-muted"><?php echo htmlspecialchars($data['acompanamiento_jornada_contraria']); ?></span></div>
            <div class="col-12 mt-2">
                <strong>Observaciones de Orientación Escolar:</strong>
                <div class="border p-3 bg-light rounded mt-1 text-muted"><?php echo nl2br(htmlspecialchars($data['observaciones_orientacion'] ?? 'Sin observaciones.')); ?></div>
            </div>
        </div>

        <!-- Firmas -->
        <div class="row mt-5 pt-5 text-center">
            <div class="col-6">
                <p class="border-top pt-2 mx-4 text-muted fw-bold">Firma del Acudiente</p>
            </div>
            <div class="col-6">
                <p class="border-top pt-2 mx-4 text-muted fw-bold">Firma Orientación Escolar / Docente</p>
            </div>
        </div>
    </div>

</body>
</html>