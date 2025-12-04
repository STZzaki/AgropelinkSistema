<?php
// CapaUsusario/Acceso/Login.php

// ¡Necesario para guardar el estado del usuario!
session_start(); //

// RUTA CORREGIDA: Sube dos niveles y baja a CapaNegocio
require_once "../../CapaNegocio/Usuario/Usuario.php"; //

// Inicializamos la variable de error como cadena vacía
$error_message = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    $gestor = new GestorUsuarios();
    // La función verificarLogin usa password_verify() internamente
    $usuario = $gestor->verificarLogin($correo, $contrasena); //

    if ($usuario) {
        // ÉXITO: Guardar datos en la sesión
        $_SESSION['usuario_correo'] = $usuario->correo; //
        $_SESSION['usuario_tipo'] = $usuario->tipo; //
        $_SESSION['usuario_id'] = $usuario->id; // <-- ¡NUEVA LÍNEA CLAVE PARA SQL!
        
        if ($usuario->tipo == 'Agricultor') {
            // RUTA CORREGIDA: Sube uno a CapaUsuario y baja a Agricultor
            header('Location: ../Inicial/MiCuenta.php'); //
            exit;
        } else {
            // RUTA CORREGIDA: Sube uno a CapaUsuario y baja a Usuario (Cliente)
            header('Location: ../Usuario/Catalogo.php'); //
            exit;
        }
    } else {
        // ERROR: El login falló
        $error_message = "Correo o contraseña incorrectos. Por favor, inténtalo de nuevo.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - AgropeLink</title>
    <link rel="stylesheet" href="..\..\Lib\Estilos\estilos.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../../Lib/Estilos/estilos.css">
    
    
</head>
<body>
    <div class="container">
        
        <div class="login-section">
            <div class="logo">
                <img src="../../Lib/img/logo_agropelink.png" alt="AgropeLink Logo" onerror="this.onerror=null;this.src='https://placehold.co/150x50/894514/ffffff?text=Logo'">
            </div>

            <div class="welcome-text">
                <h1>Acceso de Clientes</h1>
                <p>Ingresa tus datos para continuar con tu compra.</p>
            </div>
            
            <?php 
            // Muestra el error solo si $error_message NO está vacío
            if (!empty($error_message)): 
            ?>
                <div class="error-message">
                    <i class="fas fa-triangle-exclamation"></i> <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="Login.php">
                
                <div class="form-group">
                    <label for="correo">Correo Electrónico</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="correo" id="correo" placeholder="tu.correo@agropelink.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="contrasena">Contraseña</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="contrasena" id="contrasena" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="options">
                    <div class="remember">
                        <input type="checkbox" id="remember">
                        <label for="remember">Recordarme</label>
                    </div>
                    <a href="#" class="forgot-password">¿Olvidaste la contraseña?</a>
                </div>

                <button type="submit" class="login-btn">
                    Iniciar Sesión
                </button>
            </form>

            <div class="register-link">
                ¿No tienes cuenta? <a href="Registro.php">Regístrate aquí</a>
            </div>
        </div>
        
        <div class="image-section">
            <h2>¡Frescura Garantizada!</h2>
            <p>Descubre productos frescos, directos del campo a tu mesa, con la calidad que solo AgropeLink te puede ofrecer.</p>
        </div>
    </div>
</body>
</html>