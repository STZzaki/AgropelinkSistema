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
    <link rel="stylesheet" href="..\..\Lib\Estilos\estilos.css">
    
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