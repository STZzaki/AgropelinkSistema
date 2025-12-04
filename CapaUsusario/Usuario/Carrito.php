<?php
// CapaUsusario/Usuario/Carrito.php - Simulación de la página del carrito

session_start();

// Redirigir si no hay usuario logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../Acceso/Login.php');
    exit;
}

$carrito_items = $_SESSION['carrito'] ?? [];
$total = 0;

foreach ($carrito_items as $item) {
    // Aseguramos que los valores sean numéricos antes de operar
    $precio = is_numeric($item['precio']) ? (float)$item['precio'] : 0;
    $cantidad = is_numeric($item['cantidad']) ? (int)$item['cantidad'] : 0;
    $total += $precio * $cantidad;
}

// Lógica de eliminación de un artículo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_item'])) {
    $item_id_to_remove = $_POST['item_id'];
    
    // El carrito usa el ID de artículo como clave, por lo que la eliminación es directa
    if (isset($_SESSION['carrito'][$item_id_to_remove])) {
        unset($_SESSION['carrito'][$item_id_to_remove]);
        $_SESSION['carrito_message'] = "Artículo eliminado del carrito.";
    }
    header('Location: Carrito.php');
    exit;
}

// Mostrar mensaje de éxito o error
$carrito_message = $_SESSION['carrito_message'] ?? null;
unset($_SESSION['carrito_message']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - AgropeLink</title>
    <link rel="stylesheet" href="..\..\Lib\Estilos\estilos.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
</head>
<body>
    <div class="container">
        <header class="header">
            <h1><i class="fas fa-shopping-cart"></i> Tu Carrito</h1>
            <a href="Catalogo.php"><i class="fas fa-arrow-left"></i> Seguir Comprando</a>
        </header>
        
        <?php if ($carrito_message): ?>
            <div class="message success">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($carrito_message) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($carrito_items)): ?>
            
            <?php foreach ($carrito_items as $item): ?>
            <div class="cart-item">
                <img src="<?= htmlspecialchars($item['imagen_url']) ?>" 
                     onerror="this.onerror=null; this.src='../../Lib/Imagenes/productos/img/default.jpg';"
                     alt="<?= htmlspecialchars($item['nombre']) ?>" 
                     class="item-image">
                
                <div class="item-info">
                    <h3><?= htmlspecialchars($item['nombre']) ?></h3>
                    <p>Agricultor: <?= htmlspecialchars($item['agricultor']) ?></p>
                    <p>Precio unitario: <?= number_format($item['precio'], 2) ?>€ / Kg</p>
                    <p>Cantidad: <?= number_format($item['cantidad'], 0) ?> Kg</p>
                </div>
                
                <div class="item-actions">
                    <div class="item-price">
                        <?= number_format($item['precio'] * $item['cantidad'], 2) ?>€
                    </div>
                    <!-- Formulario de eliminación -->
                    <form method="POST">
                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                        <button type="submit" name="eliminar_item" class="btn-remove">
                            <i class="fas fa-times-circle"></i> Eliminar
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="cart-total">
                Total del Carrito: <?= number_format($total, 2) ?>€
            </div>
            
            <!-- ENLACE A LA PASARELA DE PAGO -->
            <a href="Pagar.php" class="btn-checkout">
                <i class="fas fa-credit-card"></i> Finalizar Compra
            </a>

        <?php else: ?>
            <div class="cart-empty">
                <h2>Tu carrito está vacío.</h2>
                <p>¡Añade productos frescos de nuestro catálogo!</p>
                <a href="Catalogo.php" class="header-link">Ir al Catálogo</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>