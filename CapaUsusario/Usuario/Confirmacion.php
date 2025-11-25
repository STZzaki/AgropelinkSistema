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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* Degradado de fondo con colores de AgropeLink */
            background: linear-gradient(135deg, #53ad57 0%, #29b69b 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .confirmation-box {
            width: 500px;
            max-width: 90%;
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            text-align: center;
        }
        .icon-success {
            font-size: 80px;
            color: #53ad57; /* Verde Oliva */
            margin-bottom: 20px;
        }
        h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }
        p {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .btn-link {
            display: inline-block;
            background: #29b69b; /* Verde Esmeralda */
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s;
        }
        .btn-link:hover {
            background: #53ad57; /* Verde Oliva */
        }
    </style>
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