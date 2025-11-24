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
    <!-- Enlace a Font Awesome para los íconos --><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" xintegrity="sha512-SnH5WK+bZ6fW+Nn4P2Jz7j4+Wz/z+r1/2e45N3J+J+A3Uo6lA5u0xY/1x5u6u6/9t8t5g/A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <style>
        /* Estilos base consistentes con AgropeLink */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
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
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 20px;
            font-size: 36px;
        }

        .success {
            background-color: #e6f7e6; /* Verde muy claro */
            color: #53ad57; /* Verde oliva */
            border: 2px solid #53ad57;
        }
        
        .error {
            background-color: #ffe6e6; /* Rojo muy claro */
            color: #cc0000;
            border: 2px solid #cc0000;
        }

        .logo-text {
            font-size: 28px;
            font-weight: 700;
            color: #894514; /* Marrón */
            margin-bottom: 20px;
        }

        h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 15px;
        }

        p {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .links a {
            display: block;
            background: #29b69b; /* Verde Esmeralda */
            color: white;
            padding: 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 10px;
            transition: background 0.3s;
        }

        .links a:hover {
            background: #53ad57;
        }
    </style>
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
                <a href="../../CapaUsuario/Acceso/Registro.html">Volver al Registro</a>
            <?php endif; ?>
            <a href="../../CapaUsuario/Acceso/Login.php">Ir al Inicio de Sesión</a>
        </div>
    </div>
</body>
</html>
