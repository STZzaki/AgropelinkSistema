<?php
// CapaUsuario/Usuario/Catalogo.php

// 1. INICIAR SESIÓN
session_start();

// Ruta ajustada: asume que Articulos.php está en CapaNegocio/Usuario/Articulos.php
require_once "../../CapaNegocio/Usuario/Articulos.php"; 

$gestor = new GestorArticulos();
$articulos = $gestor->cargarArticulos(); // Carga los artículos de la BD

// 2. VERIFICAR SI EL USUARIO ESTÁ LOGUEADO
$is_logged_in = isset($_SESSION['usuario_id']);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo - AgropeLink</title>
    <!-- Enlace a Font Awesome para los íconos -->
    <link rel="stylesheet" href="..\..\Lib\Estilos\estilos.css" xintegrity="sha512-SnH5WK+bZ6fW+Nn4P2Jz7j4+Wz/z+r1/2e45N3J+J+A3Uo6lA5u0xY/1x5u6u6/9t8t5g/A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    
</head>
<body>
    
    <!-- BARRA DE NAVEGACIÓN -->
    <header class="header">
        <div class="logo-nav">
            <!-- RUTA DEL LOGO: Corregida a dos niveles: ../../Lib/img/... -->
            <img src="../../Lib/img/logo_agropelink.png" onerror="this.onerror=null; this.src='https://placehold.co/40x40/894514/FFFFFF?text=AG'" alt="Logo AgropeLink">
            <span class="logo-text">AgropeLink</span>
        </div>
        
        <nav class="nav-links">
            <a href="Catalogo.php"><i class="fas fa-store"></i> Catálogo</a>
            <a href="Carrito.php"><i class="fas fa-receipt"></i> Pedidos</a>
            
            <?php if ($is_logged_in): ?>
                <!-- BOTÓN "MI CUENTA" y "CERRAR SESIÓN" -->
                <a href="../Inicial/MiCuenta.php" style="font-weight: 600; color: #894514;">
                    <i class="fas fa-user-circle"></i> Mi Cuenta
                </a>
                <a href="../Inicial/Logout.php">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            <?php else: ?>
                <!-- BOTÓN "INICIAR SESIÓN" -->
                <a href="../Acceso/Login.php" style="font-weight: 600; color: #29b69b;">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </a>
            <?php endif; ?>

            <div class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <!-- Simulamos la cuenta del carrito por ahora -->
                <span class="cart-count">0</span>
            </div>
        </nav>
        
    </header>

    <!-- CONTENIDO PRINCIPAL DEL CATÁLOGO -->
    <main class="catalog-container">
        
        <div class="catalog-header">
            <h1>Cosecha del Día</h1>
            <p>Descubre los productos más frescos y de temporada, directo de nuestros agricultores.</p>
        </div>

        <div class="product-grid">
            
            <?php if (empty($articulos)): ?>
                <p style="text-align: center; grid-column: 1 / -1; color: #666;">No hay artículos disponibles en el catálogo por el momento.</p>
            <?php else: ?>
                <?php foreach ($articulos as $articulo): ?>
                <div class="product-card">
                    
                    <!-- ENLACE CLICKABLE HACIA LA PÁGINA DE DETALLE -->
                    <a href="Producto.php?id=<?= $articulo->id ?>" class="product-link">
                        <div class="product-image-container">
                            <img src="<?php echo htmlspecialchars($articulo->imagen_url); ?>" 
                                onerror="this.onerror=null; this.src='https://placehold.co/280x200/cccccc/333333?text=Sin+Imagen'" 
                                alt="<?php echo htmlspecialchars($articulo->nombre); ?>" 
                                class="product-image">
                        </div>
                        <div class="product-info">
                            <div>
                                <h3><?php echo htmlspecialchars($articulo->nombre); ?></h3>
                                <p><?php echo htmlspecialchars($articulo->descripcion); ?></p>
                            </div>
                            <div class="price-stock">
                                <span class="price"><?php echo number_format($articulo->precio, 2) . '€ / Kg'; ?></span>
                                <span class="stock">Quedan <?php echo htmlspecialchars($articulo->stock); ?> Kg</span>
                            </div>
                        </div>
                    </a>
                    
                    <!-- Botón para ir al detalle (que ahora es el enlace principal) -->
                    <a href="Producto.php?id=<?= $articulo->id ?>" class="view-detail-btn">
                        <i class="fas fa-search-plus"></i> Ver Detalle
                    </a>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </main>
    
</body>
</html>