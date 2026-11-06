<?php
// 1. Control de sesión y seguridad
session_start();

// Verificar si el usuario ha iniciado sesión y si su rol es 'Administrador'
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Administrador') {
    header("Location: index.php");
    exit();
}

// Obtener el nombre del usuario
$nombre_usuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Administrador';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - IE Técnico Superior</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts (Inter) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bs-body-font-family: 'Inter', sans-serif;
            --bg-light: #f8fafc;
            --primary-green: #14532d; /* Verde institucional para la barra superior */

            /* Tarjeta 1: Caracterización (Verde esmeralda institucional) */
            --card1-main: #198754;
            --card1-bg: #e8f5e9;
            --card1-hover: #146c43;

            /* Tarjeta 2: Profesores (Azul corporativo profesional) */
            --card2-main: #2563eb;
            --card2-bg: #eff6ff;
            --card2-hover: #1d4ed8;

            /* Tarjeta 3: Grados (Ámbar / Naranja cálido distintivo) */
            --card3-main: #d97706;
            --card3-bg: #fef3c7;
            --card3-hover: #b45309;
        }

        body {
            background-color: var(--bg-light);
            color: #334155;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Navbar estilo con barra superior verde oscuro institucional */
        .navbar-custom {
            background-color: var(--primary-green) !important;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        /* Tarjetas con elevación en hover */
        .card-dashboard {
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            transition: all 0.25s ease-in-out;
            background: #ffffff;
        }
        
        .card-dashboard:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            border-color: #cbd5e1;
        }

        .icon-box {
            width: 60px;
            height: 60px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 1.25rem;
        }

        /* Estilos específicos y colores independientes para cada tarjeta */
        .icon-caracterizacion {
            background-color: var(--card1-bg);
            color: var(--card1-main);
        }
        .btn-caracterizacion {
            background-color: var(--card1-main);
            color: #ffffff;
            border: none;
        }
        .btn-caracterizacion:hover {
            background-color: var(--card1-hover);
            color: #ffffff;
        }

        .icon-profesores {
            background-color: var(--card2-bg);
            color: var(--card2-main);
        }
        .btn-profesores {
            background-color: var(--card2-main);
            color: #ffffff;
            border: none;
        }
        .btn-profesores:hover {
            background-color: var(--card2-hover);
            color: #ffffff;
        }

        .icon-grados {
            background-color: var(--card3-bg);
            color: var(--card3-main);
        }
        .btn-grados {
            background-color: var(--card3-main);
            color: #ffffff;
            border: none;
        }
        .btn-grados:hover {
            background-color: var(--card3-hover);
            color: #ffffff;
        }

        /* Pie de página */
        footer {
            margin-top: auto;
            background-color: #ffffff;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>

    <!-- Header / Navbar (Verde Oscuro Institucional) -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fw-bold text-white" href="panel_admin.php">
                <i class="bi bi-mortarboard-fill me-2 fs-4"></i>
                <span>IE Técnico Superior</span>
                <span class="fw-light fs-6 opacity-75 ms-2">| Neiva</span>
            </a>
            
            <div class="d-flex align-items-center gap-2">
                <div class="d-none d-sm-block text-end me-2">
                    <span class="d-block lh-1 fw-semibold text-white"><?php echo htmlspecialchars($nombre_usuario); ?></span>
                    <small class="text-white-50 fs-7">Administrador</small>
                </div>
                <div class="vr d-none d-sm-block my-1 bg-white opacity-25"></div>
                
                <!-- Botón de Información con el logo de grupo de personas (Desarrolladores) -->
                <a href="../informacion.php" class="btn btn-outline-light btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;" title="Información y Desarrolladores">
                    <i class="bi bi-people-fill"></i>
                </a>

                <!-- Botón Cerrar Sesión -->
                <a href="../logout.php" class="btn btn-outline-light btn-sm d-flex align-items-center gap-2 rounded-pill px-3">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Cerrar sesión</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <main class="container py-5">
        
        <!-- Saludo e Introducción -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3 fw-bold text-dark mb-1">Panel de Control</h1>
                <p class="text-secondary mb-0">Bienvenido(a), <strong><?php echo htmlspecialchars($nombre_usuario); ?></strong>. Seleccione un módulo para gestionar la información.</p>
            </div>
        </div>

        <!-- Tarjetas de Módulos -->
        <div class="row g-4">
            
            <!-- Tarjeta 1: Caracterización -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card card-dashboard h-100 p-4">
                    <div class="card-body d-flex flex-column p-0">
                        <div class="icon-box icon-caracterizacion">
                            <i class="bi bi-journal-text"></i>
                        </div>
                        <h2 class="h5 card-title fw-bold text-dark mb-2">Caracterización</h2>
                        <p class="card-text text-secondary small mb-4 flex-grow-1">
                            Gestione las fichas socioeconómicas, datos institucionales y registros detallados de los estudiantes.
                        </p>
                        <a href="caracterizacion.php" class="btn btn-caracterizacion w-100 d-flex align-items-center justify-content-center gap-2 rounded-3 py-2">
                            <span>Acceder al módulo</span>
                            <i class="bi bi-arrow-right fs-6"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tarjeta 2: Profesores -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card card-dashboard h-100 p-4">
                    <div class="card-body d-flex flex-column p-0">
                        <div class="icon-box icon-profesores">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <h2 class="h5 card-title fw-bold text-dark mb-2">Profesores</h2>
                        <p class="card-text text-secondary small mb-4 flex-grow-1">
                            Administre la planta docente, asignación de asignaturas, datos de contacto y credenciales de acceso.
                        </p>
                        <a href="profesores.php" class="btn btn-profesores w-100 d-flex align-items-center justify-content-center gap-2 rounded-3 py-2">
                            <span>Gestionar Profesores</span>
                            <i class="bi bi-arrow-right fs-6"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tarjeta 3: Grados -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card card-dashboard h-100 p-4">
                    <div class="card-body d-flex flex-column p-0">
                        <div class="icon-box icon-grados">
                            <i class="bi bi-diagram-3-fill"></i>
                        </div>
                        <h2 class="h5 card-title fw-bold text-dark mb-2">Grados</h2>
                        <p class="card-text text-secondary small mb-4 flex-grow-1">
                            Configure los niveles académicos, grupos, cursos y la distribución de aulas para el año lectivo.
                        </p>
                        <a href="grados.php" class="btn btn-grados w-100 d-flex align-items-center justify-content-center gap-2 rounded-3 py-2">
                            <span>Configurar Grados</span>
                            <i class="bi bi-arrow-right fs-6"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Pie de Página -->
    <footer class="py-3 text-center text-secondary small">
        <div class="container">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> <strong>IE Técnico Superior - Neiva</strong>. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Bootstrap 5.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>