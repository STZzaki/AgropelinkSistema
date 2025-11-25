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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f7f7;
            color: #333;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
        }
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
            color: #894514;
        }
        .header a {
            text-decoration: none;
            color: #53ad57;
            font-weight: 600;
        }
        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px dashed #eee;
            gap: 20px;
        }
        .item-info {
            flex-grow: 1;
        }
        .item-info h3 {
            font-size: 18px;
            color: #29b69b;
            margin-bottom: 5px;
        }
        .item-info p {
            font-size: 14px;
            color: #666;
        }
        .item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        .item-actions {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 5px;
        }
        .item-price {
            font-weight: 700;
            font-size: 1.2em;
            color: #894514;
            margin-bottom: 5px;
        }
        .btn-remove {
            background: none;
            border: none;
            color: #f44336;
            cursor: pointer;
            font-size: 14px;
            padding: 0;
        }
        .cart-total {
            text-align: right;
            margin-top: 30px;
            font-size: 1.5em;
            font-weight: 700;
            color: #53ad57;
        }
        .btn-checkout {
            background: #53ad57;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 1.2em;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
            /* Estilos para el enlace <a> */
            text-decoration: none;
            display: block; 
            text-align: center;
        }
        .btn-checkout:hover {
             background: #29b69b;
        }
        .cart-empty {
            text-align: center;
            padding: 50px;
            color: #999;
        }
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
        
    </style>
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