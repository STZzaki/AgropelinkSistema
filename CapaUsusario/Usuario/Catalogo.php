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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" xintegrity="sha512-SnH5WK+bZ6fW+Nn4P2Jz7j4+Wz/z+r1/2e45N3J+J+A3Uo6lA5u0xY/1x5u6u6/9t8t5g/A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <style>
        /* COLORES AGROPELINK: #894514 (Marrón), #53ad57 (Verde Oliva), #29b69b (Verde Esmeralda) */
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f7f7f7; /* Fondo más claro para el catálogo */
            min-height: 100vh;
            color: #333;
        }
        
        /* ------------------ NAVEGACIÓN (HEADER) ------------------ */
        .header {
            background-color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-nav {
            display: flex;
            align-items: center;
        }

        .logo-nav img {
            width: 40px;
            height: auto;
            margin-right: 10px;
        }

        .logo-nav .logo-text {
            font-size: 20px;
            font-weight: 700;
            color: #894514; 
        }

        .nav-links {
            display: flex;
            align-items: center;
        }

        .nav-links a {
            color: #53ad57;
            text-decoration: none;
            margin-left: 25px;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #29b69b;
        }

        .cart-icon {
            font-size: 20px;
            color: #29b69b;
            cursor: pointer;
            position: relative;
            margin-left: 25px;
        }

        .cart-count {
            position: absolute;
            top: -10px;
            right: -10px;
            background-color: #894514;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
        }

        /* ------------------ CUERPO DEL CATÁLOGO ------------------ */

        .catalog-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .catalog-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .catalog-header h1 {
            font-size: 36px;
            color: #53ad57;
            margin-bottom: 10px;
        }

        .catalog-header p {
            color: #666;
            font-size: 16px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        /* ESTILO DE LA TARJETA DE PRODUCTO */
        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
        }

        /* Contenedor del enlace para que la tarjeta sea clicable */
        .product-link {
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
        }

        .product-image-container {
            height: 200px;
            overflow: hidden;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .product-info {
            padding: 20px;
            flex-grow: 1; 
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .product-info h3 {
            font-size: 20px;
            color: #894514;
            margin-bottom: 8px;
        }

        .product-info p {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
            line-height: 1.4;
        }

        .price-stock {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .price {
            font-size: 24px;
            font-weight: 700;
            color: #29b69b;
        }

        .stock {
            font-size: 14px;
            color: #53ad57;
            font-weight: 600;
        }

        .view-detail-btn {
            background: #53ad57;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            width: 100%;
            text-align: center;
            text-decoration: none; /* Asegura que el botón se vea bien */
            display: block;
        }

        .view-detail-btn:hover {
            background: #29b69b;
        }

        .view-detail-btn i {
            margin-right: 8px;
        }
        
        /* ------------------ RESPONSIVE ------------------ */
        @media (max-width: 768px) {
            .header {
                padding: 15px 20px;
            }
            
            .nav-links {
                display: flex; 
                flex-wrap: wrap;
                justify-content: flex-end;
            }
            
            .nav-links a {
                margin-left: 10px;
                margin-bottom: 5px;
            }

            .catalog-container {
                margin: 20px 0;
                padding: 0 10px;
            }
            
            .product-grid {
                grid-template-columns: 1fr; 
            }
        }

    </style>
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