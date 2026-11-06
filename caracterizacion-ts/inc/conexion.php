<?php
function conectarBD() {
    $servidor   = "localhost";
    $usuario    = "root";
    $clave      = "";
    $base_datos = "bd_caracterizacion_tecnico";

    // Conexión procedural básica
    $conexion = mysqli_connect($servidor, $usuario, $clave, $base_datos);

    // Verificamos si hubo error al conectar
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    // Aseguramos codificación UTF-8 para tildes y caracteres especiales
    mysqli_set_charset($conexion, "utf8mb4");

    return $conexion;
}
?>