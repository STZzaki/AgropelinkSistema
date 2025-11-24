<?php
// Asegúrate de iniciar la sesión si necesitas mensajes flash o redirección post-registro
session_start();

// RUTA CORREGIDA: Sube dos niveles (Acceso -> CapaUsuario -> ejercicio1) y baja a CapaNegocio
require_once "../../CapaNegocio/Usuario/Usuario.php";

$message = "";
$is_success = null; // Usamos null para no mostrar la caja de mensaje al inicio
$icon = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recoger y validar datos
    $nombre = $_POST['nombre'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $provincia = $_POST['provincia'] ?? '';
    $localidad = $_POST['localidad'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $tipo = $_POST['tipo'] ?? 'Cliente'; 
    $correo = $_POST['correo'] ?? '';
    $contrasena_plana = $_POST['contrasena'] ?? '';

    $gestor = new GestorUsuarios();
    
    // 2. Verificar si el correo ya existe
    if ($gestor->existeUsuario($correo)) {
        $message = "Error: El correo electrónico ya está registrado. Por favor, inicia sesión o usa otro correo.";
        $is_success = false;
        $icon = "fas fa-triangle-exclamation";
    } else {
        
        // 🔑 CORRECCIÓN CLAVE: Hashear la contraseña antes de guardarla
        $contrasena_hasheada = password_hash($contrasena_plana, PASSWORD_DEFAULT);
        
        // 3. Guardar el nuevo usuario usando el HASH
        $usuario = new Usuario(
            $nombre, $apellidos, $provincia, $localidad, $direccion, $tipo, 
            $correo, $contrasena_hasheada // Guarda el HASH aquí
        );

        $gestor->guardar($usuario);
        $message = "¡Registro completado correctamente! Ya puedes iniciar sesión.";
        $is_success = true;
        $icon = "fas fa-circle-check";
        
        // Redirigir inmediatamente al login tras el éxito para evitar reenvíos
        header('Location: Login.php');
        exit;
    }
} else {
    // Si se accede directamente, redirigir al formulario
    header('Location: Registro.html');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ($is_success === false) ? "Error de Registro" : "Procesando Registro" ?> - AgropeLink</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- RUTA CORREGIDA al CSS: Sube dos niveles (Acceso -> CapaUsuario -> ejercicio1) y baja a Lib/Estilos -->
    <link rel="stylesheet" href="../../Lib/Estilos/estilos.css">
    
    <style>
        /* Estilos específicos para la caja de mensaje */
        body {
            background: linear-gradient(135deg, #53ad57 0%, #29b69b 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .message-box {
            width: 450px;
            max-width: 90%;
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .icon-container {
            width: 80px; height: 80px; border-radius: 50%; display: flex;
            justify-content: center; align-items: center; margin: 0 auto 20px;
            font-size: 36px; border: 2px solid;
        }
        .error {
            background-color: #ffe6e6; color: #cc0000; border-color: #cc0000;
        }
        .logo-text { font-size: 28px; font-weight: 700; color: #894514; margin-bottom: 20px; }
        h1 { font-size: 24px; color: #333; margin-bottom: 15px; }
        p { color: #666; margin-bottom: 30px; line-height: 1.6; }
        .links a {
            display: block; background: #29b69b; color: white; padding: 12px;
            border-radius: 8px; text-decoration: none; font-weight: 600;
            margin-bottom: 10px; transition: background 0.3s;
        }
        .links a:hover { background: #53ad57; }
    </style>
</head>
<body>
    <?php if ($is_success === false): // Solo mostramos si hay un error ?>
        <div class="message-box">
            <div class="logo-text">AgropeLink</div>
            
            <div class="icon-container error">
                <i class="<?= $icon ?>"></i>
            </div>
            
            <h1>¡Ha Ocurrido un Error!</h1>
            
            <p><?= htmlspecialchars($message) ?></p>

            <div class="links">
                <a href="Registro.html">Volver al Registro</a>
                <a href="Login.php">Ir al Inicio de Sesión</a>
            </div>
        </div>
    <?php endif; ?>
</body>
</html>
