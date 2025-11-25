<?php
// CapaUsusario/Usuario/Producto.php - Página de detalle de un solo producto

session_start();

// Rutas de las clases de Negocio
require_once "../../CapaNegocio/Usuario/Articulos.php"; 
// Aunque no lo usemos aquí, lo dejamos cargado para el futuro
require_once "../../CapaNegocio/Usuario/Usuario.php"; 

$articuloDetalle = null;
$error_message = "";

// 1. Obtener el ID del producto de la URL
$id_producto = $_GET['id'] ?? null;

if ($id_producto === null || !is_numeric($id_producto)) {
    $error_message = "ID de producto no válido.";
} else {
    $gestorArticulos = new GestorArticulos();
    // 2. Cargar el artículo con el nombre del agricultor
    $articuloDetalle = $gestorArticulos->obtenerArticuloPorIdConAgricultor($id_producto);
    
    if (!$articuloDetalle) {
        $error_message = "Producto no encontrado.";
    }
}

// 3. Procesar la acción de AÑADIR AL CARRITO
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar_carrito']) && $articuloDetalle) {
    
    $cantidad = (int)($_POST['cantidad'] ?? 1);
    
    if ($cantidad > 0 && $cantidad <= $articuloDetalle->stock) {
        
        // Inicializar carrito si no existe
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
        
        $item_key = $articuloDetalle->id;
        
        // Si el producto ya está en el carrito, aumentar la cantidad
        if (isset($_SESSION['carrito'][$item_key])) {
            $_SESSION['carrito'][$item_key]['cantidad'] += $cantidad;
        } else {
            // Si es la primera vez, añadir el producto
            $_SESSION['carrito'][$item_key] = [
                'id' => $articuloDetalle->id,
                'nombre' => $articuloDetalle->nombre,
                'precio' => $articuloDetalle->precio,
                'cantidad' => $cantidad,
                'imagen_url' => $articuloDetalle->imagen_url,
                'agricultor' => $articuloDetalle->nombre_agricultor,
            ];
        }
        
        $_SESSION['success_message'] = "¡{$cantidad} Kg de {$articuloDetalle->nombre} añadidos al carrito!";
        
        // Redirigir para evitar reenvío del formulario (POST Redirect GET pattern)
        header("Location: Producto.php?id={$id_producto}");
        exit;
        
    } else {
        $error_message = "Cantidad no válida o superior al stock disponible ({$articuloDetalle->stock} Kg).";
    }
}

// Limpiar mensajes de éxito
$success_message = $_SESSION['success_message'] ?? null;
unset($_SESSION['success_message']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $articuloDetalle ? htmlspecialchars($articuloDetalle->nombre) : 'Producto No Encontrado' ?> - AgropeLink</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Estilos generales */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f7f7;
            color: #333;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
        }

        /* Header (Barra de Navegación Simple) */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 28px;
            color: #53ad57;
        }
        .header a {
            text-decoration: none;
            color: #894514;
            font-weight: 600;
        }

        /* Layout del Producto (SIN PRODUCTOS AL LADO) */
        .product-layout {
            display: flex;
            gap: 40px;
            padding-bottom: 30px;
        }

        .product-image-area {
            flex: 0 0 400px; /* Ancho fijo para la imagen */
            max-width: 400px;
            height: 400px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-details {
            flex: 1;
        }

        .product-details h2 {
            font-size: 36px;
            color: #894514;
            margin-bottom: 10px;
        }

        .product-details .farmer-info {
            color: #53ad57;
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .product-details .price {
            font-size: 42px;
            font-weight: 700;
            color: #29b69b;
            margin-bottom: 20px;
        }
        
        .product-details .stock {
            font-size: 14px;
            color: #555;
            margin-bottom: 15px;
        }

        .product-details .description {
            line-height: 1.6;
            color: #555;
            margin-bottom: 30px;
        }

        /* Formulario y Carrito */
        .cart-form {
            display: flex;
            align-items: center;
            gap: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .cart-form label {
            font-weight: 600;
            color: #555;
        }

        .cart-form input[type="number"] {
            width: 80px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
            font-size: 16px;
        }

        .btn-add-to-cart {
            background: #53ad57;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
            box-shadow: 0 4px 10px rgba(83, 173, 87, 0.3);
        }
        
        .btn-add-to-cart:hover {
            background: #29b69b;
            transform: translateY(-1px);
        }
        
        /* Mensajes */
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .message.success {
            background-color: #e6f7e6;
            color: #0d8b5e;
            border: 1px solid #b7e6c3;
        }

        .message.error {
            background-color: #ffe0e0;
            color: #cc0000;
            border: 1px solid #ff9999;
        }

        /* Responsivo */
        @media (max-width: 768px) {
            .product-layout {
                flex-direction: column;
                gap: 20px;
            }
            .product-image-area {
                max-width: 100%;
                height: auto;
            }
            .cart-form {
                flex-direction: column;
                align-items: flex-start;
            }
            .btn-add-to-cart {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <a href="Catalogo.php"><i class="fas fa-arrow-left"></i> Volver al Catálogo</a>
            <a href="Carrito.php"><i class="fas fa-shopping-cart"></i> Ver Carrito</a>
        </header>

        <?php if ($error_message): ?>
            <div class="message error">
                <i class="fas fa-triangle-exclamation"></i> <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="message success">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>

        <?php if ($articuloDetalle): ?>
            
            <div class="product-layout">
                
                <div class="product-image-area">
                    <img src="<?= htmlspecialchars($articuloDetalle->imagen_url) ?>" 
                         onerror="this.onerror=null; this.src='../../Lib/Imagenes/productos/img/default.jpg';"
                         alt="<?= htmlspecialchars($articuloDetalle->nombre) ?>" 
                         class="product-image">
                </div>
                
                <div class="product-details">
                    <h2><?= htmlspecialchars($articuloDetalle->nombre) ?></h2>
                    <p class="farmer-info">
                        Vendido por: <i class="fas fa-tractor"></i> <?= htmlspecialchars($articuloDetalle->nombre_agricultor) ?>
                    </p>
                    
                    <p class="description">
                        **Descripción:** <?= nl2br(htmlspecialchars($articuloDetalle->descripcion)) ?>
                    </p>

                    <p class="stock">
                        <i class="fas fa-boxes-stacked"></i> Stock Disponible: <?= number_format($articuloDetalle->stock, 0) ?> Kg
                    </p>

                    <div class="price">
                        <?= number_format($articuloDetalle->precio, 2) ?>€ / Kg
                    </div>
                    
                    <!-- Formulario Añadir al Carrito -->
                    <form method="POST" class="cart-form">
                        <input type="hidden" name="id_producto" value="<?= $articuloDetalle->id ?>">
                        
                        <label for="cantidad">Cantidad (Kg):</label>
                        <input type="number" id="cantidad" name="cantidad" value="1" min="1" max="<?= $articuloDetalle->stock ?>">
                        
                        <button type="submit" name="agregar_carrito" class="btn-add-to-cart" <?= $articuloDetalle->stock <= 0 ? 'disabled' : '' ?>>
                            <i class="fas fa-cart-plus"></i> Añadir al Carrito
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Aquí irían las secciones extra (Reseñas, Datos de la Granja, etc.) si las necesitaras -->

        <?php endif; ?>
    </div>
</body>
</html>