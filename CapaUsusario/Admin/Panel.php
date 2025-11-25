<?php
// CapaUsusario/Admin/Panel.php - Panel de control y acceso a las vistas de gestión

session_start();

// 1. Verificar si el usuario está logueado y es ADMIN
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'Admin') {
    header('Location: ../Acceso/Login.php');
    exit;
}

require_once "../../CapaNegocio/Usuario/Usuario.php";
$gestor = new GestorUsuarios();
$userId = $_SESSION['usuario_id'];
$usuario = $gestor->obtenerUsuarioPorId($userId);

// Si el admin no existe (error en BD)
if (!$usuario) {
    session_destroy();
    header('Location: ../Acceso/Login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Agropelink</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Estilos base */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2b6cb0 0%, #1a4d8c 100%); /* Tema Azul Oscuro para Admin */
            min-height: 100vh;
            padding: 20px;
            color: #fff;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        header {
            background: #1a4d8c;
            border-radius: 10px;
            padding: 15px 30px;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo-text {
            font-size: 24px;
            font-weight: 700;
            color: #ffc107; /* Amarillo para contraste */
            letter-spacing: 1px;
        }
        
        .admin-content {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            color: #333;
        }

        .welcome-message h2 {
            font-size: 32px;
            color: #1a4d8c;
            margin-bottom: 10px;
        }
        
        .welcome-message p {
            color: #555;
            margin-bottom: 30px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }

        .management-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .management-buttons a {
            text-align: center;
            text-decoration: none;
            padding: 25px 15px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .management-buttons a i {
            font-size: 36px;
            margin-bottom: 10px;
        }

        /* Colores Específicos */
        .btn-users { background: #ffc107; color: #333; }
        .btn-users:hover { background: #ffae00; transform: translateY(-3px); }
        
        .btn-products { background: #53ad57; color: white; }
        .btn-products:hover { background: #29b69b; transform: translateY(-3px); }

        .btn-orders { background: #007bff; color: white; }
        .btn-orders:hover { background: #0056b3; transform: translateY(-3px); }

        .btn-details { background: #6c757d; color: white; }
        .btn-details:hover { background: #5a6268; transform: translateY(-3px); }

        .logout-link a {
            color: #ffc107;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo-text">
                <i class="fas fa-user-shield"></i> Panel de Administración
            </div>
            <div class="logout-link">
                <a href="../Inicial/Logout.php">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </div>
        </header>

        <div class="admin-content">
            <div class="welcome-message">
                <h2>Bienvenido, Administrador <?= htmlspecialchars($usuario->nombre) ?></h2>
                <p>Utiliza los botones a continuación para gestionar los usuarios, artículos y pedidos de Agropelink. Tu rol es ADMINISTRADOR, con acceso total a los datos del sistema.</p>
            </div>

            <div class="management-buttons">
                
                <!-- 1. Lista de Usuarios -->
                <a href="ListaUsuarios.php" class="btn-users">
                    <i class="fas fa-users-cog"></i>
                    Lista de Usuarios
                </a>
                
                <!-- 2. Lista de Artículos -->
                <a href="ListaArticulos.php" class="btn-products">
                    <i class="fas fa-boxes"></i>
                    Lista de Artículos
                </a>

                <!-- 3. Lista de Pedidos -->
                <a href="ListaPedidos.php" class="btn-orders">
                    <i class="fas fa-receipt"></i>
                    Lista de Pedidos
                </a>

                <!-- 4. Detalles de la Cuenta -->
                <a href="../Inicial/MiCuenta.php?section=details" class="btn-details">
                    <i class="fas fa-user"></i>
                    Detalles de la Cuenta
                </a>
            </div>
        </div>
    </div>
</body>
</html>