<?php
// CapaUsuario/Usuario/Catalogo.php

// Ruta ajustada: asume que Articulos.php está en CapaNegocio/Usuario/Articulos.php
require_once "../../CapaNegocio/Usuario/Articulos.php"; 

$gestor = new GestorArticulos();
$articulos = $gestor->cargarArticulos(); // Carga los artículos de ejemplo

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

        .add-to-cart-btn {
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
        }

        .add-to-cart-btn:hover {
            background: #29b69b;
        }

        .add-to-cart-btn i {
            margin-right: 8px;
        }
        
        /* ------------------ RESPONSIVE ------------------ */
        @media (max-width: 768px) {
            .header {
                padding: 15px 20px;
            }
            
            .nav-links {
                display: none; 
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
            <a href="Catalogo.php">Catálogo</a>
            <a href="#">Pedidos</a>
            <a href="#">Mi Cuenta</a>
            <a href="../../Acceso/Login.php">Cerrar Sesión</a>
        </nav>
        
        <div class="cart-icon">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-count">3</span>
        </div>
    </header>

    <!-- CONTENIDO PRINCIPAL DEL CATÁLOGO -->
    <main class="catalog-container">
        
        <div class="catalog-header">
            <h1>Cosecha del Día</h1>
            <p>Descubre los productos más frescos y de temporada, directo de nuestros agricultores.</p>
        </div>

        <div class="product-grid">
            
            <?php foreach ($articulos as $articulo): ?>
            <div class="product-card">
                <div class="product-image-container">
                    <img src="<?php echo htmlspecialchars($articulo->imagen_url); ?>" alt="<?php echo htmlspecialchars($articulo->nombre); ?>" class="product-image">
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
                    <!-- El botón podría enviar un formulario o AJAX para añadir al carrito -->
                    <button class="add-to-cart-btn">
                        <i class="fas fa-cart-plus"></i> Añadir al Carrito
                    </button>
                </div>
            </div>
            <?php endforeach; ?>

        </div>
    </main>
    
</body>
</html>
