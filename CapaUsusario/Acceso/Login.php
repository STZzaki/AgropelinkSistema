<?php
// ¡Necesario para guardar el estado del usuario!
session_start();

// RUTA CORREGIDA: Sube dos niveles y baja a CapaNegocio
require_once "../../CapaNegocio/Usuario/Usuario.php"; 

// Inicializamos la variable de error como cadena vacía
$error_message = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    $gestor = new GestorUsuarios();
    // La función verificarLogin usa password_verify() internamente
    $usuario = $gestor->verificarLogin($correo, $contrasena); 

    if ($usuario) {
        // ÉXITO: Guardar datos en la sesión
        $_SESSION['usuario_correo'] = $usuario->correo;
        $_SESSION['usuario_tipo'] = $usuario->tipo;
        
        if ($usuario->tipo == 'Agricultor') {
            // RUTA CORREGIDA: Sube uno a CapaUsuario y baja a Agricultor
            header('Location: ../Agricultor/SubirArticulo.html'); 
            exit;
        } else {
            // RUTA CORREGIDA: Sube uno a CapaUsuario y baja a Usuario (Cliente)
            header('Location: ../Usuario/Catalogo.php'); 
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- RUTA CORREGIDA al CSS: Sube dos niveles y baja a Lib/Estilos -->
    <link rel="stylesheet" href="../../Lib/Estilos/estilos.css">
    
    <style>
        /* Estilos base (Mantenidos de tu código original, movidos aquí para la unidad de archivo) */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: linear-gradient(135deg, #53ad57 0%, #29b69b 100%); display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        .container { display: flex; width: 1000px; max-width: 95%; height: 650px; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2); }
        .login-section { flex: 1; padding: 40px; display: flex; flex-direction: column; justify-content: center; overflow-y: auto; }
        .logo { display: flex; align-items: center; margin-bottom: 25px; flex-direction: column; }
        /* RUTA CORREGIDA a la imagen del logo */
        .logo img { width: 150px; height: auto; margin-bottom: 5px; } 
        .logo-text { font-size: 24px; font-weight: 700; color: #894514; letter-spacing: 1px; text-align: center; }
        .welcome-text { margin-bottom: 25px; text-align: center; }
        .welcome-text h1 { font-size: 28px; color: #333; margin-bottom: 8px; }
        .welcome-text p { color: #666; font-size: 15px; }
        .form-group { margin-bottom: 15px; position: relative; }
        .form-group label { display: block; margin-bottom: 6px; color: #555; font-weight: 500; font-size: 14px; }
        .input-with-icon { position: relative; }
        .input-with-icon i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #888; font-size: 14px; }
        .input-with-icon input, .input-with-icon select { width: 100%; padding: 12px 12px 12px 40px; border: 2px solid #e1e1e1; border-radius: 8px; font-size: 14px; transition: all 0.3s; }
        .input-with-icon input:focus, .input-with-icon select:focus { border-color: #53ad57; box-shadow: 0 0 0 3px rgba(83, 173, 87, 0.1); outline: none; }
        .options { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .remember { display: flex; align-items: center; }
        .remember input { margin-right: 8px; }
        .remember label { font-size: 14px; margin-bottom: 0; }
        .forgot-password { color: #29b69b; text-decoration: none; font-weight: 500; transition: color 0.3s; font-size: 14px; }
        .forgot-password:hover { color: #53ad57; text-decoration: underline; }
        .login-btn { background: #29b69b; color: white; border: none; padding: 14px; border-radius: 10px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; width: 100%; margin-bottom: 20px; box-shadow: 0 4px 10px rgba(41, 182, 155, 0.3); }
        .login-btn:hover { background: #53ad57; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(41, 182, 155, 0.4); }
        .register-link { text-align: center; color: #666; font-size: 14px; }
        .register-link a { color: #894514; text-decoration: none; font-weight: 600; transition: color 0.3s; }
        .register-link a:hover { color: #53ad57; text-decoration: underline; }
        .image-section { flex: 1; background: linear-gradient(rgba(137, 69, 20, 0.7), rgba(41, 182, 155, 0.7)), url('https://placehold.co/1000x650/53ad57/29b69b?text=Agro+Link'); background-size: cover; background-position: center; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 40px; color: white; text-align: center; }
        .error-message { background-color: #ffe0e0; color: #cc0000; border: 1px solid #ff9999; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 14px; text-align: center; }
        @media (max-width: 768px) { .container { flex-direction: column; height: auto; } .image-section { display: none; } .login-section { padding: 30px 20px; } }
    </style>
</head>
<body>
    <div class="container">
        
        <div class="login-section">
            <div class="logo">
                <!-- RUTA CORREGIDA a la imagen del logo -->
                <img src="../../Lib/img/logo_agropelink.png" alt="AgropeLink Logo" onerror="this.onerror=null;this.src='https://placehold.co/150x50/894514/ffffff?text=Logo'">
            </div>

            <div class="welcome-text">
                <h1>Acceso de Clientes</h1>
                <p>Ingresa tus datos para continuar con tu compra.</p>
            </div>
            
            <?php 
            // 🔑 CORRECCIÓN: Muestra el error solo si $error_message NO está vacío
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
                ¿No tienes cuenta? <a href="Registro.html">Regístrate aquí</a>
            </div>
        </div>
        
        <div class="image-section">
            <h2>¡Frescura Garantizada!</h2>
            <p>Descubre productos frescos, directos del campo a tu mesa, con la calidad que solo AgropeLink te puede ofrecer.</p>

            <!-- Se requiere agregar estilos para .features y .feature en el <style> -->
        </div>
    </div>
</body>
</html>
