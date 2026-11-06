<?php
// 1. Control de sesión y seguridad
session_start();

// Verificar si el usuario ha iniciado sesión y si su rol es Administrador o Profesor
if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] !== 'Administrador' && $_SESSION['rol'] !== 'Profesor')) {
    header("Location: index.php");
    exit();
}

// Obtener el nombre y rol del usuario
$nombre_usuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$rol_usuario = $_SESSION['rol'];

// Definir la ruta de retorno dinámica según el rol
$url_panel = ($rol_usuario === 'Administrador') ? 'administrador/panel_admin.php' : 'profesor/panel_profesor.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información de la Aplicación - IE Técnico Superior & SENA</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts (Inter) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bs-body-font-family: 'Inter', sans-serif;
            --bg-light: #f1f5f9;
            --primary-green: #14532d;
            --sena-green: #39a900;
        }

        body {
            background-color: var(--bg-light);
            color: #334155;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 24px 24px;
        }

        .navbar-custom {
            background-color: var(--primary-green) !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .hero-banner {
            background: linear-gradient(135deg, #052e16 0%, #14532d 50%, #166534 100%);
            color: white;
            border-radius: 1.5rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            position: relative;
            overflow: hidden;
        }

        .hero-banner::before {
            content: "";
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .logo-container {
            background: white;
            padding: 12px;
            border-radius: 1rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .logo-container:hover {
            transform: translateY(-5px);
        }

        .dev-card {
            border: none;
            border-radius: 1rem;
            background: #ffffff;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        .dev-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-green), var(--sena-green));
        }

        .dev-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .avatar-box {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid #e2e8f0;
            background-color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            flex-shrink: 0;
        }

        .avatar-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Estilo para los correos no seleccionables */
        .email-badge-static {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-size: 0.85rem;
            padding: 0.35rem 0.75rem;
            border-radius: 0.5rem;
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            display: inline-block;
            width: 100%;
        }

        .badge-ficha {
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            font-weight: 600;
        }

        footer {
            margin-top: auto;
            background-color: #ffffff;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>

    <!-- Header / Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fw-bold text-white" href="<?php echo $url_panel; ?>">
                <i class="bi bi-mortarboard-fill me-2 fs-4 text-white"></i>
                <span>IE Técnico Superior</span>
                <span class="fw-light fs-6 opacity-75 ms-2">| Neiva</span>
            </a>
            
            <div class="d-flex align-items-center gap-3">
                <a href="<?php echo $url_panel; ?>" class="btn btn-outline-light btn-sm d-flex align-items-center gap-2 rounded-pill px-3 shadow-sm">
                    <i class="bi bi-arrow-left"></i>
                    <span>Volver al Panel</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <main class="container py-5">
        
        <!-- Banner Principal Institucional y Alianza SENA -->
        <div class="hero-banner p-4 p-md-5 mb-5 text-center text-md-start">
            <div class="row align-items-center g-4">
                <div class="col-12 col-lg-8">
                    <div class="d-inline-flex align-items-center gap-2 badge-ficha px-3 py-1.5 rounded-pill mb-3 fs-6">
                        <i class="bi bi-patch-check-fill text-success"></i> Ficha SENA: <strong>3153943</strong>
                    </div>
                    <h1 class="display-6 fw-extrabold mb-2 text-white">Sistema de Caracterización Estudiantil</h1>
                    <p class="lead text-white-50 mb-4 fs-6">
                        Plataforma oficial desarrollada en alianza estratégica institucional entre la <strong>Institución Educativa Técnico Superior</strong> y el <strong>SENA</strong> para la gestión integral y socioeconómica de la comunidad estudiantil de Neiva.
                    </p>
                    <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3">
                        <span class="badge bg-success bg-opacity-25 border border-success border-opacity-50 text-light px-3 py-2 rounded-3"><i class="bi bi-code-slash me-1"></i> Versión 1.0.0 Stable</span>
                        <span class="badge bg-primary bg-opacity-25 border border-primary border-opacity-50 text-light px-3 py-2 rounded-3"><i class="bi bi-shield-lock-fill me-1"></i> Entorno Seguro PHP</span>
                    </div>
                </div>

                <!-- Logos Institucionales -->
                <div class="col-12 col-lg-4 d-flex justify-content-center align-items-center gap-4">
                    <div class="logo-container text-center" style="width: 110px; height: 110px; display: flex; align-items: center; justify-content: center;">
                        <a href="https://www.sena.edu.co" target="_blank">
                            <img src="images/sena.png" alt="Logo SENA" class="img-fluid" style="max-height: 85px; object-fit: contain;" title="Servicio Nacional de Aprendizaje - SENA">
                        </a>
                    </div>
                    <div class="logo-container text-center" style="width: 110px; height: 110px; display: flex; align-items: center; justify-content: center;">
                        <a href="https://tecnicosuperiorneiva.com.co/" target="_blank">
                            <img src="images/tecnico.png" alt="Logo Colegio" class="img-fluid" style="max-height: 85px; object-fit: contain;" title="Institución Educativa Técnico Superior Neiva">
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Desarrolladores -->
        <div class="text-center mb-4">
            <h2 class="h3 fw-bold text-dark"><i class="bi bi-people-fill text-success me-2"></i> Equipo de Desarrollo</h2>
            <p class="text-secondary small">Talento humano técnico encargado del diseño, arquitectura y despliegue del sistema.</p>
        </div>

        <div class="row g-4 justify-content-center mb-5">
            
            <!-- Desarrollador 1: Jhoserp Beicker -->
            <div class="col-12 col-md-4">
                <div class="dev-card h-100 p-4 d-flex flex-column">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-box">
                            <img src="images/jhoserp.jpeg" alt="Jhoserp Beicker" onerror="this.src='https://cdn-icons-png.flaticon.com/512/149/149071.png'">
                        </div>
                        <div>
                            <span class="badge bg-success bg-opacity-10 text-success fw-semibold mb-1 small">Desarrollador FullStack</span>
                            <h5 class="fw-bold text-dark mb-0 fs-6">Jhoserp Beicker Torres Hernandez</h5>
                        </div>
                    </div>
                    <div class="mt-auto pt-3 border-top border-light">
                        <label class="form-label small fw-semibold text-muted mb-1"><i class="bi bi-envelope-at me-1"></i> Correo Electrónico:</label>
                        <div class="email-badge-static">JBTH36137657@soy.sena.edu.co</div>
                    </div>
                </div>
            </div>

            <!-- Desarrollador 2: Eddie Santiago -->
            <div class="col-12 col-md-4">
                <div class="dev-card h-100 p-4 d-flex flex-column">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-box">
                            <img src="images/marin.jpeg" alt="Eddie Santiago" onerror="this.src='https://cdn-icons-png.flaticon.com/512/149/149071.png'">
                        </div>
                        <div>
                            <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold mb-1 small">Desarrollador Backend</span>
                            <h5 class="fw-bold text-dark mb-0 fs-6">Eddie Santiago Marin Ayuren</h5>
                        </div>
                    </div>
                    <div class="mt-auto pt-3 border-top border-light">
                        <label class="form-label small fw-semibold text-muted mb-1"><i class="bi bi-envelope-at me-1"></i> Correo Electrónico:</label>
                        <div class="email-badge-static">ESMA36137208@soy.sena.edu.co</div>
                    </div>
                </div>
            </div>

            <!-- Desarrollador 3: Jose Jair -->
            <div class="col-12 col-md-4">
                <div class="dev-card h-100 p-4 d-flex flex-column">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-box">
                            <img src="images/devia.jpeg" alt="Jose Jair" onerror="this.src='https://cdn-icons-png.flaticon.com/512/149/149071.png'">
                        </div>
                        <div>
                            <span class="badge bg-warning bg-opacity-10 text-warning text-dark fw-semibold mb-1 small">Desarrollador Frontend</span>
                            <h5 class="fw-bold text-dark mb-0 fs-6">Jose Jair Devia Cruz</h5>
                        </div>
                    </div>
                    <div class="mt-auto pt-3 border-top border-light">
                        <label class="form-label small fw-semibold text-muted mb-1"><i class="bi bi-envelope-at me-1"></i> Correo Electrónico:</label>
                        <div class="email-badge-static">JJDC31374913@soy.sena.edu.co</div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Botón de Retorno Inferior -->
        <div class="text-center">
            <a href="<?php echo $url_panel; ?>" class="btn btn-success btn-lg px-5 py-2.5 rounded-pill shadow fw-bold" style="background-color: var(--primary-green); border: none;">
                <i class="bi bi-house-door-fill me-2"></i> Regresar al Panel
            </a>
        </div>

    </main>

    <!-- Pie de Página -->
    <footer class="py-4 text-center text-secondary small">
        <div class="container">
            <p class="mb-1">&copy; <?php echo date('Y'); ?> <strong>IE Técnico Superior - Neiva & SENA</strong>. Todos los derechos reservados.</p>
            <p class="mb-0 text-muted small">Programa de Formación Titulada - Ficha <strong>3153943</strong></p>
        </div>
    </footer>

    <!-- Bootstrap 5.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>