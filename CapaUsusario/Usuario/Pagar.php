<?php
// CapaUsusario/Usuario/Pagar.php - Pasarela de Pago y Finalización de Compra

session_start();

// Redirigir si no hay usuario logueado o el carrito está vacío
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header('Location: Catalogo.php');
    exit;
}

require_once "../../CapaNegocio/Usuario/Usuario.php";
// RUTA CORREGIDA: Apunta a la ubicación especificada por el usuario
require_once "../../CapaNegocio/Usuario/Pedidos.php"; 

$gestorUsuarios = new GestorUsuarios();
$gestorPedidos = new GestorPedidos(); // Asumimos que esta clase está en Pedidos.php
$userId = $_SESSION['usuario_id'];

$usuario = $gestorUsuarios->obtenerUsuarioPorId($userId);
$carrito_items = $_SESSION['carrito'];
$total = 0;

foreach ($carrito_items as $item) {
    // Aseguramos que los valores sean numéricos antes de operar
    $precio = is_numeric($item['precio']) ? (float)$item['precio'] : 0;
    $cantidad = is_numeric($item['cantidad']) ? (int)$item['cantidad'] : 0;
    $total += $precio * $cantidad;
}

$error_message = "";
$success_message = "";

// 1. PROCESAR EL PAGO Y LA COMPRA
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['finalizar_compra'])) {
    
    // Recoger datos de envío (se usa la dirección del usuario por defecto)
    $direccion = $_POST['direccion'] ?? $usuario->direccion;
    $tarjeta = $_POST['numero_tarjeta'] ?? '';
    $cvc = $_POST['cvc'] ?? '';

    // Validación básica de datos (Simulada)
    if (empty($direccion) || empty($tarjeta) || empty($cvc)) {
        $error_message = "Por favor, completa todos los campos de envío y pago.";
    } elseif (strlen($tarjeta) < 16) {
        $error_message = "El número de tarjeta es inválido.";
    } else {
        
        // ** 2. REGISTRAR EL PEDIDO EN LA BASE DE DATOS **
        $compra_exitosa = $gestorPedidos->guardarPedido($userId, $carrito_items, $direccion, $total);

        if ($compra_exitosa) {
            // 3. ÉXITO: Vaciar el carrito y redirigir
            unset($_SESSION['carrito']);
            $_SESSION['compra_success'] = "¡Tu pedido ha sido registrado con éxito! Total: " . number_format($total, 2) . "€";
            header('Location: Confirmacion.php');
            exit;
        } else {
            $error_message = "Error al procesar la compra. Por favor, inténtalo de nuevo.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pasarela de Pago - AgropeLink</title>
    <link rel="stylesheet" href="..\..\Lib\Estilos\estilos.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
</head>
<body>
    <div class="container">
        
        <div class="order-summary">
            <h1><i class="fas fa-file-invoice"></i> Resumen del Pedido</h1>

            <?php if ($error_message): ?>
                <div class="message error">
                    <i class="fas fa-triangle-exclamation"></i> <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <ul class="item-list">
                <?php foreach ($carrito_items as $item): ?>
                    <li>
                        <span><?= htmlspecialchars($item['nombre']) ?> (<?= number_format($item['cantidad'], 0) ?> Kg)</span>
                        <strong><?= number_format($item['precio'] * $item['cantidad'], 2) ?>€</strong>
                    </li>
                <?php endforeach; ?>
            </ul>
            
            <div class="total-box">
                <span>TOTAL A PAGAR:</span>
                <span><?= number_format($total, 2) ?>€</span>
            </div>
            
            <p style="margin-top: 20px; font-size: 14px; color: #666;">
                <i class="fas fa-lock"></i> Transacción segura.
            </p>
        </div>

        <div class="payment-form">
            <h1><i class="fas fa-credit-card"></i> Método de Pago</h1>

            <form method="POST" action="Pagar.php">
                
                <!-- Dirección de Envío -->
                <h2><i class="fas fa-map-marker-alt"></i> Dirección de Envío</h2>
                <div class="form-group">
                    <label for="direccion">Dirección Completa</label>
                    <textarea name="direccion" id="direccion" required><?= htmlspecialchars("{$usuario->direccion}, {$usuario->localidad}, {$usuario->provincia}") ?></textarea>
                    <small style="color: #555;">Usamos tu dirección de registro por defecto.</small>
                </div>

                <!-- Detalles de Pago (Simulado) -->
                <h2><i class="fas fa-money-check"></i> Datos Bancarios (Simulado)</h2>
                <div class="form-group">
                    <label for="numero_tarjeta">Número de Tarjeta</label>
                    <input type="text" name="numero_tarjeta" id="numero_tarjeta" placeholder="XXXX XXXX XXXX XXXX" required maxlength="16">
                </div>
                
                <div style="display: flex; gap: 20px;">
                    <div class="form-group" style="flex: 1;">
                        <label for="fecha_exp">Fecha Exp.</label>
                        <input type="text" name="fecha_exp" id="fecha_exp" placeholder="MM/AA" required maxlength="5">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="cvc">CVC</label>
                        <input type="text" name="cvc" id="cvc" placeholder="XXX" required maxlength="3">
                    </div>
                </div>

                <button type="submit" name="finalizar_compra" class="btn-pay">
                    Pagar <?= number_format($total, 2) ?>€
                </button>
                
                <a href="Carrito.php" style="display: block; text-align: center; margin-top: 15px; color: #894514; text-decoration: none;">
                    Volver al Carrito
                </a>
            </form>
        </div>
    </div>
</body>
</html>