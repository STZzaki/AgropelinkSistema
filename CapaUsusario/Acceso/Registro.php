<?php
// CapaUsusario/Acceso/Registro.php
// Este archivo maneja tanto la visualización del formulario como el procesamiento de datos.

session_start();

// RUTA CRÍTICA: Incluye el gestor de usuarios que a su vez llama a Conexion.php
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
        
        // 🔑 Hashear la contraseña antes de guardarla para seguridad
        $contrasena_hasheada = password_hash($contrasena_plana, PASSWORD_DEFAULT);
        
        // 3. Guardar el nuevo usuario usando el HASH
        $usuario = new Usuario(
            null, // ID: null para el nuevo registro
            $nombre, 
            $apellidos, 
            $provincia, 
            $localidad, 
            $direccion, 
            $tipo, 
            $correo, 
            $contrasena_hasheada
        );

        // Si el registro es exitoso, redirigimos al login inmediatamente (evitando la caja de error/éxito)
        if ($gestor->guardar($usuario)) {
            header('Location: Login.php?registered=true'); // Redirigir al login
            exit;
        } else {
            // Error de base de datos
             $message = "Error: No se pudo completar el registro en la base de datos.";
             $is_success = false;
             $icon = "fas fa-database";
        }
    }
}
// Si es GET (acceso directo) o si el registro POST falló, mostramos el formulario o el error.
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - AgropeLink</title>
    <link rel="stylesheet" href="..\..\Lib\Estilos\estilos.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- RUTA al CSS -->
    <link rel="stylesheet" href="../../Lib/Estilos/estilos.css">
    
    
</head>
<body>
    <div class="container">
        <div class="logo">
            <!-- RUTA a la imagen del logo -->
            <img src="../../Lib/img/logo_agropelink.png" alt="AgropeLink Logo" onerror="this.onerror=null;this.src='https://placehold.co/120x50/894514/ffffff?text=Logo'">
            <div class="logo-text">Registro</div>
        </div>

        <div class="welcome-text">
            <h1>Crea tu Cuenta</h1>
            <p>Únete a AgropeLink para conectar directamente con el campo.</p>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="error-message">
                <i class="<?= $icon ?>"></i> <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- La acción apunta a sí mismo para el procesamiento POST -->
        <form method="post" action="Registro.php">
            
            <div class="grid-2">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" name="nombre" id="nombre" required value="<?= htmlspecialchars($nombre ?? '') ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="apellidos">Apellidos</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user-tag"></i>
                        <input type="text" name="apellidos" id="apellidos" required value="<?= htmlspecialchars($apellidos ?? '') ?>">
                    </div>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="provincia">Provincia</label>
                    <div class="input-with-icon">
                        <i class="fas fa-map-marker-alt"></i>
                        <input type="text" name="provincia" id="provincia" required value="<?= htmlspecialchars($provincia ?? '') ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="localidad">Localidad</label>
                    <div class="input-with-icon">
                        <i class="fas fa-city"></i>
                        <input type="text" name="localidad" id="localidad" required value="<?= htmlspecialchars($localidad ?? '') ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="direccion">Dirección Completa</label>
                <div class="input-with-icon">
                    <i class="fas fa-road"></i>
                    <input type="text" name="direccion" id="direccion" required value="<?= htmlspecialchars($direccion ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="tipo">Tipo de Usuario</label>
                <div class="input-with-icon">
                    <i class="fas fa-users"></i>
                    <select name="tipo" id="tipo" required>
                        <option value="Cliente" <?= ($tipo ?? '') == 'Cliente' ? 'selected' : '' ?>>Cliente</option>
                        <option value="Agricultor" <?= ($tipo ?? '') == 'Agricultor' ? 'selected' : '' ?>>Agricultor</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="correo">Correo Electrónico</label>
                <div class="input-with-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="correo" id="correo" placeholder="ejemplo@correo.com" required value="<?= htmlspecialchars($correo ?? '') ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="contrasena">Contraseña</label>
                <div class="input-with-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="contrasena" id="contrasena" placeholder="Mínimo 8 caracteres" required minlength="8">
                </div>
            </div>

            <button type="submit" class="register-btn">
                Registrarse
            </button>
        </form>

        <div class="login-link">
            ¿Ya tienes cuenta? <a href="Login.php">Inicia Sesión</a>
        </div>
    </div>
</body>
</html>