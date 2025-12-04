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
    <link rel="stylesheet" href="..\..\Lib\Estilos\estilos.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
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