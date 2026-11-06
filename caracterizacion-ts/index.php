<?php
session_start();
require_once "inc/conexion.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST['correo']);
    $clave  = trim($_POST['clave']);

    if (!empty($correo) && !empty($clave)) {
        $conexion = conectarBD();

        // 1. Buscamos el usuario únicamente por correo para verificar su existencia y estado
        $sql = "SELECT id_usuario, nombre_completo, correo, password, rol, activo FROM usuarios WHERE correo = ? LIMIT 1";
        $sentencia = mysqli_prepare($conexion, $sql);

        if ($sentencia) {
            mysqli_stmt_bind_param($sentencia, "s", $correo);
            mysqli_stmt_execute($sentencia);
            $resultado = mysqli_stmt_get_result($sentencia);

            if ($fila = mysqli_fetch_assoc($resultado)) {
                
                // 2. Verificamos primero si el usuario está inactivo
                if ((int)$fila['activo'] === 0) {
                    $mensaje = "Tu usuario se encuentra INACTIVO. Contacta al administrador para habilitar tu acceso.";
                } 
                // 3. Verificamos la contraseña (soporta texto plano o hash si luego migras a password_verify)
                else if ($clave === $fila['password'] || password_verify($clave, $fila['password'])) {
                    
                    $_SESSION['id_usuario'] = $fila['id_usuario'];
                    $_SESSION['nombre']     = $fila['nombre_completo'];
                    $_SESSION['rol']        = $fila['rol'];

                    mysqli_stmt_close($sentencia);
                    mysqli_close($conexion);

                    // Redirección según rol
                    if ($fila['rol'] === 'Administrador') {
                        header("Location: administrador/panel_admin.php");
                    } else {
                        header("Location: profesor/panel_profesor.php");
                    }
                    exit();
                } else {
                    $mensaje = "Contraseña incorrecta.";
                }
            } else {
                $mensaje = "El correo electrónico no se encuentra registrado en el sistema.";
            }
            mysqli_stmt_close($sentencia);
        } else {
            $mensaje = "Error al conectar con la consulta.";
        }
        mysqli_close($conexion);
    } else {
        $mensaje = "Por favor diligencia todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - IE Técnico Superior</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --primary-green: #14532d;         /* Color verde institucional oscuro unificado */
            --primary-green-hover: #166534;   /* Color hover unificado */
            --light-green-bg: #f0fdf4;        /* Fondo claro acorde */
        }

        body {
            background-color: #f8f9fa;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Contenedor del video de fondo */
        .video-container {
            position: relative;
            width: 100%;
            height: 220px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(20, 83, 45, 0.15);
            background-color: var(--primary-green);
            border: 3px solid #ffffff;
        }

        .video-container video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Tarjeta de login estilizada */
        .card-login {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(20, 83, 45, 0.1);
            padding: 10px;
        }

        .btn-green-style {
            background-color: var(--primary-green);
            border: none;
            font-size: 1.1rem;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
            color: #ffffff;
            transition: background-color 0.2s ease, transform 0.1s ease;
        }

        .btn-green-style:hover {
            background-color: var(--primary-green-hover);
            color: #ffffff;
        }

        .form-control-lg {
            font-size: 1rem;
            padding: 14px 16px;
            border-radius: 8px;
            border: 1px solid #ced4da;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-control-lg:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.25rem rgba(20, 83, 45, 0.15);
        }

        .text-institucional {
            color: var(--primary-green) !important;
        }
    </style>
</head>
<body>

<div class="container my-auto py-5">
    <div class="row align-items-center justify-content-center g-5">
        
        <!-- Columna Izquierda: Branding y Espacio para Video -->
        <div class="col-12 col-lg-6 text-center text-lg-start">
            <h1 class="display-5 fw-bold text-institucional mb-2">IE Técnico Superior</h1>
            <p class="fs-4 text-secondary mb-4">Sistema de Caracterización Estudiantil e Información Académica.</p>

            <!-- CONTENEDOR PARA TU VIDEO PERSONALIZADO EN BUCLE -->
            <div class="video-container mb-3">
                <video autoplay loop muted playsinline>
                    <source src="images/video-index.mp4" type="video/mp4">
                    Tu navegador no soporta la reproducción de video.
                </video>
            </div>
            <small class="text-muted d-block text-center text-lg-start">
                <i class="bi bi-info-circle me-1 text-institucional"></i> Plataforma oficial para docentes y administrativos de Neiva.
            </small>
        </div>

        <!-- Columna Derecha: Tarjeta de Inicio de Sesión -->
        <div class="col-12 col-sm-10 col-md-8 col-lg-5">
            
            <div class="card card-login">
                <div class="card-body p-4">
                    <h4 class="fw-bold text-center mb-4 text-dark">Iniciar Sesión</h4>

                    <!-- Alerta de Error / Inactividad -->
                    <?php if (!empty($mensaje)): ?>
                        <div class="alert alert-danger alert-dismissible fade show small d-flex align-items-center" role="alert">
                            <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2 fs-5"></i>
                            <div><?php echo htmlspecialchars($mensaje); ?></div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <input type="email" class="form-control form-control-lg" id="correo" name="correo" placeholder="Correo electrónico institucional" required autofocus>
                        </div>

                        <div class="mb-3">
                            <input type="password" class="form-control form-control-lg" id="clave" name="clave" placeholder="Contraseña" required>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-green-style">
                                Entrar
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <small class="text-muted">¿Necesitas ayuda con tu acceso?</small><br>
                        <a href="https://t.me/+w-c6hOBiGQRmZDU5" class="text-decoration-none small fw-semibold text-institucional">Contacta al administrador del sistema</a>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- Pie de página -->
<footer class="text-center py-3 bg-white border-top text-muted small">
    &copy; <?php echo date('Y'); ?> IE Técnico Superior - Neiva. Todos los derechos reservados.
</footer>

<!-- Bootstrap 5.3 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>