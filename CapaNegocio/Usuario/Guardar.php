<?php
require_once "Usuario.php";

$message = "";
$is_success = true;
$icon = "fas fa-circle-check"; // Icono de éxito

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ... (recuperación de variables)
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $provincia = $_POST['provincia'];
    $localidad = $_POST['localidad'];
    $direccion = $_POST['direccion'];
    $tipo = $_POST['tipo'];
    $correo = $_POST['correo'];
    $contrasena_plana = $_POST['contrasena']; // Cambiamos el nombre para claridad

    $gestor = new GestorUsuarios();
    
    // 1. Verificar si el correo ya existe
    if ($gestor->existeUsuario($correo)) {
        $message = "Error: El correo electrónico ya está registrado. Por favor, inicia sesión o usa otro correo.";
        $is_success = false;
        $icon = "fas fa-triangle-exclamation"; // Icono de error
    } else {
        
        // 🚨 CORRECCIÓN CLAVE: Hashear la contraseña antes de guardarla 🚨
        $contrasena_hasheada = password_hash($contrasena_plana, PASSWORD_DEFAULT);
        
        // 2. Guardar el nuevo usuario usando el HASH
        $usuario = new Usuario(
            $nombre,
            $apellidos,
            $provincia,
            $localidad,
            $direccion,
            $tipo,
            $correo,
            $contrasena_hasheada // <-- USAR EL HASH AQUÍ
        );

        $gestor->guardar($usuario);
        $message = "¡Registro completado correctamente! Ya puedes iniciar sesión.";
        $is_success = true;
    }
} else {
    $message = "Acceso no permitido.";
    $is_success = false;
    $icon = "fas fa-ban";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_success ? "Éxito" : "Error" ?> - AgropeLink</title>
    <!-- Enlace a Font Awesome para los íconos --><link rel="stylesheet" href="..\..\Lib\Estilos\estilos.css" xintegrity="sha512-SnH5WK+bZ6fW+Nn4P2Jz7j4+Wz/z+r1/2e45N3J+J+A3Uo6lA5u0xY/1x5u6u6/9t8t5g/A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
   
</head>
<body>
    <div class="message-box">
        <div class="logo-text">AgropeLink</div>
        
        <div class="icon-container <?= $is_success ? 'success' : 'error' ?>">
            <i class="<?= $icon ?>"></i>
        </div>
        
        <h1><?= $is_success ? "¡Operación Exitosa!" : "¡Ha Ocurrido un Error!" ?></h1>
        
        <p><?= $message ?></p>

        <div class="links">
            <?php if (!$is_success): ?>
                <a href="../../CapaUsuario/Acceso/Registro.php">Volver al Registro</a>
            <?php endif; ?>
            <a href="../../CapaUsuario/Acceso/Login.php">Ir al Inicio de Sesión</a>
        </div>
    </div>
</body>
</html>
