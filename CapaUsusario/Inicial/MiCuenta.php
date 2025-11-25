<?php
// CapaUsusario/Inicial/MiCuenta.php - Gestión de cuenta de Cliente, Agricultor y Admin

session_start();

// Redirigir si no hay sesión
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../Acceso/Login.php');
    exit;
}

require_once "../../CapaNegocio/Usuario/Usuario.php";
// Asegúrate de requerir otros gestores si usas las secciones 'orders' o 'addresses'
// require_once "../../CapaNegocio/Usuario/Pedidos.php"; 

$gestor = new GestorUsuarios();
$userId = $_SESSION['usuario_id'];
$usuario = $gestor->obtenerUsuarioPorId($userId);

// Si el usuario no existe o hay un error, cerrar sesión
if (!$usuario) {
    session_destroy();
    header('Location: ../Acceso/Login.php');
    exit;
}

// Determinar la sección actual
$current_section = $_GET['section'] ?? 'dashboard';

// Lógica de acciones
$message = null;

if ($current_section === 'logout') {
    header('Location: Logout.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $current_section === 'details' && isset($_POST['update_details'])) {
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $correo = trim($_POST['correo']);
    $password_new = trim($_POST['password_new']);
    
    // Validación simple
    if (empty($nombre) || empty($correo)) {
        $message = ["type" => "error", "text" => "Nombre y correo no pueden estar vacíos."];
    } elseif ($gestor->existeUsuario($correo) && $correo !== $usuario->correo) {
        $message = ["type" => "error", "text" => "El correo electrónico ya está registrado."];
    } else {
        $password_hash = null;
        if (!empty($password_new)) {
            // Hashing de la nueva contraseña
            $password_hash = password_hash($password_new, PASSWORD_DEFAULT);
        }

        if ($gestor->actualizarDetalles($userId, $nombre, $apellidos, $correo, $password_hash)) {
            $message = ["type" => "success", "text" => "Detalles actualizados con éxito."];
            // Refrescar el objeto usuario para mostrar los nuevos datos
            $usuario = $gestor->obtenerUsuarioPorId($userId);
            $_SESSION['usuario_nombre'] = $usuario->nombre;

        } else {
            $message = ["type" => "error", "text" => "Error al actualizar los detalles. Inténtelo de nuevo."];
        }
    }
}

// Lógica de contenido de secciones (solo la estructura)
$content = "";
switch ($current_section) {
    case 'dashboard':
        if ($usuario->tipo === 'Admin') {
            $content = "
                <p><strong>Bienvenido al Panel de Administración.</strong> Utiliza el menú lateral (o el botón de abajo) para acceder a las herramientas de gestión.</p>
                <div style='margin-top: 20px;'>
                    <a href='../Admin/Panel.php' style='display: inline-block; padding: 10px 20px; background: #1a4d8c; color: white; text-decoration: none; border-radius: 5px;'>Ir al Panel de Administración</a>
                </div>
                <div style='margin-top: 30px; padding: 15px; border: 1px dashed #ccc; background: #f9f9f9;'>
                    <h3>Información Rápida</h3>
                    <p><strong>Rol:</strong> Administrador del Sistema</p>
                    <p><strong>Correo:</strong> {$usuario->correo}</p>
                </div>
            ";
        } elseif ($usuario->tipo === 'Agricultor') {
            $content = "
                <p><strong>Bienvenido, Agricultor.</strong> Desde aquí puedes gestionar tus productos y ver el estado de tus pedidos y ganancias.</p>
                <div style='margin-top: 20px;'>
                    <a href='../Agricultor/MisProductos.php' style='display: inline-block; padding: 10px 20px; background: #894514; color: white; text-decoration: none; border-radius: 5px; margin-right: 15px;'>
                        <i class='fas fa-boxes'></i> Gestionar Productos
                    </a>
                    <a href='../Agricultor/SubirArticulo.html' style='display: inline-block; padding: 10px 20px; background: #53ad57; color: white; text-decoration: none; border-radius: 5px;'>
                        <i class='fas fa-upload'></i> Subir Artículo
                    </a>
                </div>
            ";
        } else { // Cliente
            $content = "
                <p><strong>Bienvenido, Cliente.</strong> Desde tu escritorio puedes ver un resumen de tus pedidos recientes y gestionar tu información personal.</p>
                <div style='margin-top: 20px;'>
                    <a href='../Usuario/Catalogo.php' style='display: inline-block; padding: 10px 20px; background: #29b69b; color: white; text-decoration: none; border-radius: 5px;'>Ir al Catálogo</a>
                </div>
            ";
        }
        break;
    
    case 'orders':
        // Simulación de Pedidos
        $content = "<h2>Tus Pedidos</h2><p>Aquí se mostrará la lista de tus pedidos con su estado y detalles.</p>";
        break;
    
    case 'addresses':
        // Simulación de Direcciones
        $content = "
            <h2>Direcciones de Envío</h2>
            <p>Esta es tu dirección actual registrada:</p>
            <div class='address-box'>
                <p><strong>{$usuario->nombre} {$usuario->apellidos}</strong></p>
                <p>{$usuario->direccion}</p>
                <p>{$usuario->localidad}, {$usuario->provincia}</p>
            </div>
            <p style='margin-top: 15px;'>Puedes actualizar tu dirección en la sección 'Detalles de la cuenta'.</p>
        ";
        break;

    case 'details':
        // Formulario de detalles
        $content = "
            <h2>Detalles de la Cuenta</h2>
            <p>Actualiza tu información personal y contraseña.</p>
            <form method='POST' action='MiCuenta.php?section=details'>
                <input type='hidden' name='update_details' value='1'>
                <div class='form-group'>
                    <label for='nombre'>Nombre</label>
                    <input type='text' id='nombre' name='nombre' value='" . htmlspecialchars($usuario->nombre) . "' required>
                </div>
                <div class='form-group'>
                    <label for='apellidos'>Apellidos</label>
                    <input type='text' id='apellidos' name='apellidos' value='" . htmlspecialchars($usuario->apellidos) . "'>
                </div>
                <div class='form-group'>
                    <label for='correo'>Correo Electrónico</label>
                    <input type='email' id='correo' name='correo' value='" . htmlspecialchars($usuario->correo) . "' required>
                </div>
                <div class='form-group'>
                    <label for='password_new'>Nueva Contraseña</label>
                    <input type='password' id='password_new' name='password_new' placeholder='Dejar vacío para no cambiar'>
                </div>
                
                <h3>Dirección de Registro</h3>
                <div class='form-group'>
                    <label>Provincia</label>
                    <input type='text' value='" . htmlspecialchars($usuario->provincia) . "' disabled>
                </div>
                <div class='form-group'>
                    <label>Dirección</label>
                    <input type='text' value='" . htmlspecialchars($usuario->direccion) . "' disabled>
                </div>
                <p style='font-size: 0.9em; color: #777;'>Para cambiar la dirección de registro, contacta a soporte.</p>

                <button type='submit' class='btn-update'>Guardar Cambios</button>
            </form>
        ";
        break;
    
    default:
        $content = "<h2>Error</h2><p>Sección no encontrada.</p>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Cuenta - AgropeLink</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            min-height: 70vh;
        }
        h1 {
            color: #894514;
            margin-bottom: 25px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        h2 {
            color: #29b69b;
            margin-bottom: 15px;
        }
        
        /* Sidebar (Menú) */
        .account-sidebar {
            width: 280px;
            background-color: #f9f9f9;
            border-right: 1px solid #eee;
            padding: 30px 0;
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }
        .account-sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .account-sidebar ul li a {
            display: block;
            padding: 15px 30px;
            color: #555;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s, color 0.3s;
            border-left: 4px solid transparent;
        }
        .account-sidebar ul li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        .account-sidebar ul li a:hover, .account-sidebar ul li.active a {
            background-color: #e0f2f1;
            color: #29b69b;
            border-left: 4px solid #29b69b;
        }
        
        /* Contenido Principal */
        .account-content {
            flex-grow: 1;
            padding: 40px;
        }
        
        /* Formularios */
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn-update {
            background: #894514;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 10px;
        }
        .btn-update:hover {
            background: #6a3510;
        }

        /* Mensajes de feedback */
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Estilos de caja de dirección */
        .address-box {
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            .account-sidebar {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid #eee;
                border-top-right-radius: 10px;
                border-bottom-left-radius: 0;
            }
            .account-sidebar ul {
                display: flex;
                overflow-x: auto;
                white-space: nowrap;
            }
            .account-sidebar ul li a {
                border-left: none;
                border-bottom: 4px solid transparent;
            }
            .account-sidebar ul li.active a {
                border-left: none;
                border-bottom: 4px solid #29b69b;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        
        <div class="account-sidebar">
            <h3 style="padding: 0 30px 10px; color: #894514; border-bottom: 1px solid #eee;">
                Hola, <?= htmlspecialchars($usuario->nombre) ?>
            </h3>
            <ul>
                <?php if ($usuario->tipo === 'Admin'): ?>
                    <!-- Menú Específico para Administrador -->
                    <li class="<?= $current_section === 'dashboard' ? 'active' : '' ?>">
                        <a href="../Admin/Panel.php">
                            <i class="fas fa-home"></i> Panel de Admin
                        </a>
                    </li>
                    <li class="<?= $current_section === 'details' ? 'active' : '' ?>">
                        <a href="MiCuenta.php?section=details">
                            <i class="fas fa-user"></i> Detalles de la cuenta
                        </a>
                    </li>
                    <li class="<?= $current_section === 'logout' ? 'active' : '' ?>">
                        <a href="Logout.php">
                            <i class="fas fa-sign-out-alt"></i> Salir
                        </a>
                    </li>

                <?php elseif ($usuario->tipo === 'Agricultor'): ?>
                    <!-- Menú para Agricultor -->
                    <li class="<?= $current_section === 'dashboard' ? 'active' : '' ?>" >
                        <a href="MiCuenta.php?section=dashboard">
                            <i class="fas fa-home"></i> Escritorio
                        </a>
                    </li>
                    <li class="<?= $current_section === 'my_products' ? 'active' : '' ?>">
                        <!-- 🔑 NUEVA SECCIÓN: MIS PRODUCTOS -->
                        <a href="../Agricultor/MisProductos.php">
                            <i class="fas fa-boxes"></i> Mis Productos
                        </a>
                    </li>
                    <li class="<?= $current_section === 'orders' ? 'active' : '' ?>">
                        <a href="MiCuenta.php?section=orders">
                            <i class="fas fa-shopping-bag"></i> Pedidos (Simulado)
                        </a>
                    </li>
                    <li class="<?= $current_section === 'details' ? 'active' : '' ?>">
                        <a href="MiCuenta.php?section=details">
                            <i class="fas fa-user"></i> Detalles de la cuenta
                        </a>
                    </li>
                    <li class="<?= $current_section === 'logout' ? 'active' : '' ?>">
                        <a href="Logout.php">
                            <i class="fas fa-sign-out-alt"></i> Salir
                        </a>
                    </li>
                <?php else: ?>
                    <!-- Menú Normal (Cliente) -->
                    <li class="<?= $current_section === 'dashboard' ? 'active' : '' ?>" >
                        <a href="MiCuenta.php?section=dashboard">
                            <i class="fas fa-home"></i> Escritorio
                        </a>
                    </li>
                    <li class="<?= $current_section === 'orders' ? 'active' : '' ?>">
                        <a href="MiCuenta.php?section=orders">
                            <i class="fas fa-shopping-bag"></i> Pedidos
                        </a>
                    </li>
                    <li class="<?= $current_section === 'addresses' ? 'active' : '' ?>">
                        <a href="MiCuenta.php?section=addresses">
                            <i class="fas fa-map-marker-alt"></i> Direcciones
                        </a>
                    </li>
                    <li class="<?= $current_section === 'details' ? 'active' : '' ?>">
                        <a href="MiCuenta.php?section=details">
                            <i class="fas fa-user"></i> Detalles de la cuenta
                        </a>
                    </li>
                    <li class="<?= $current_section === 'logout' ? 'active' : '' ?>">
                        <a href="Logout.php">
                            <i class="fas fa-sign-out-alt"></i> Salir
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        
        <div class="account-content">
            <h1>Mi Cuenta</h1>
            
            <?php if (!empty($message)): ?>
                <div class="message <?= htmlspecialchars($message['type']) ?>">
                    <i class="fas fa-info-circle"></i> <?= htmlspecialchars($message['text']) ?>
                </div>
            <?php endif; ?>

            <div class="section-content">
                <?= $content ?>
            </div>
        </div>
        
    </div>
</body>
</html>