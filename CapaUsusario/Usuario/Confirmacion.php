<?php
// CapaUsusario/Usuario/Confirmacion.php - Muestra el mensaje final tras el pago

session_start();

$compra_success = $_SESSION['compra_success'] ?? null;
unset($_SESSION['compra_success']); // Limpiar el mensaje de la sesión para que no se muestre al recargar

// Si no hay mensaje de éxito (acceso directo o sesión expirada), redirigir al catálogo
if (!$compra_success) {
    header('Location: Catalogo.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación - AgropeLink</title>
    <!-- Incluimos Font Awesome para los íconos -->
    <link rel="stylesheet" href="..\..\Lib\Estilos\estilos.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
</head>
<body>
    <div class="confirmation-box">
        <div class="icon-success">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1>¡Pedido Registrado!</h1>
        <p><?= htmlspecialchars($compra_success) ?></p>
        <p>Tu pedido será procesado inmediatamente. Recibirás una confirmación por correo electrónico (simulado).</p>
        <a href="Catalogo.php" class="btn-link">Volver al Catálogo</a>
    </div>
</body>
</html>