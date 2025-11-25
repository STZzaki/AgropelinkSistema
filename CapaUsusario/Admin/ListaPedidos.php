<?php
// CapaUsusario/Admin/ListaPedidos.php - Gestión de pedidos por el Administrador

// 🔑 LÍNEAS DE DEBUG: ESTO MOSTRARÁ EL ERROR FATAL
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Redirigir si no es Admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'Admin') {
    header('Location: ../Acceso/Login.php');
    exit;
}

// Incluir la capa de negocio
require_once "../../CapaNegocio/Usuario/Usuario.php";
require_once "../../CapaNegocio/Usuario/Pedidos.php"; 
$gestorPedidos = new GestorPedidos();
$gestorUsuarios = new GestorUsuarios();

$message = null;

// LÓGICA DE PROCESAMIENTO (Eliminar)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id_to_delete = (int)$_POST['id'];
    
    if ($gestorPedidos->eliminarPedido($id_to_delete)) {
        $message = ["type" => "success", "text" => "Pedido ID {$id_to_delete} y sus detalles eliminados con éxito."];
    } else {
        $message = ["type" => "error", "text" => "Error al eliminar el pedido ID {$id_to_delete}. Asegúrese de que no hay restricciones."];
    }
}

// Lógica para cargar todos los pedidos: 
// Extendemos GestorPedidos para añadir la función de listado general con JOIN al cliente
class GestorPedidosAdmin extends GestorPedidos {
    public function cargarTodosLosPedidos() {
        // La variable $pdo fue declarada globalmente en Conexion.php
        global $pdo; 
        
        // 🔑 VERIFICACIÓN DE CONEXIÓN
        if (!$pdo) {
             error_log("PDO connection not available in GestorPedidosAdmin.");
             return [];
        }

        $sql = "SELECT p.*, u.nombre AS nombre_cliente 
                FROM pedidos p 
                JOIN usuarios u ON p.id_cliente = u.id 
                ORDER BY p.fecha_pedido DESC";
        
        try {
            // Usamos $pdo para acceder a la conexión global
            $stmt = $pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Registrar el error de la consulta para ver qué está fallando
            error_log("Error al cargar pedidos: " . $e->getMessage());
            return [];
        }
    }
}

$gestorPedidosAdmin = new GestorPedidosAdmin();
$pedidos = $gestorPedidosAdmin->cargarTodosLosPedidos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pedidos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f9; color: #333; padding: 20px; }
        .container { max-width: 1400px; margin: 0 auto; background: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); padding: 30px; }
        h1 { color: #007bff; margin-bottom: 25px; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #007bff; text-decoration: none; font-weight: 600; }
        
        .management-table { width: 100%; border-collapse: collapse; font-size: 14px; }
        .management-table th { background-color: #007bff; color: white; padding: 12px 15px; text-align: left; }
        .management-table td { padding: 10px 15px; border-bottom: 1px solid #eee; vertical-align: top; }
        .management-table tr:nth-child(even) { background-color: #f9f9f9; }
        
        .status-pill { padding: 5px 10px; border-radius: 15px; font-weight: 600; font-size: 12px; }
        .status-Pendiente { background-color: #ffc107; color: #333; }
        .status-Procesando { background-color: #007bff; color: white; }
        .status-Completado { background-color: #28a745; color: white; }
        .status-Cancelado { background-color: #dc3545; color: white; }

        .btn { padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; transition: opacity 0.3s; }
        .btn-delete { background-color: #dc3545; color: white; }
        .btn-delete:hover { opacity: 0.8; }
        
        .message { padding: 15px; border-radius: 5px; margin-bottom: 20px; font-weight: 600; }
        .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        /* Estilo para el mensaje de No hay pedidos */
        .empty-message { text-align: center; padding: 50px; color: #888; border: 1px solid #ddd; border-radius: 8px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="Panel.php" class="back-link"><i class="fas fa-arrow-left"></i> Volver al Panel</a>
        <h1>Gestión de Pedidos del Sistema</h1>

        <?php if ($message): ?>
            <div class="message <?= htmlspecialchars($message['type']) ?>">
                <?= htmlspecialchars($message['text']) ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($pedidos)): ?>
            <div class="empty-message">
                <p><i class="fas fa-info-circle"></i> No se encontraron pedidos registrados.</p>
                <p>La consulta se ejecutó, pero no devolvió resultados. Si esta página aparece completamente en blanco, **debes revisar el mensaje de error de PHP (Paso 1)**.</p>
            </div>
        <?php else: ?>

        <table class="management-table">
            <thead>
                <tr>
                    <th>ID Pedido</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Dirección de Envío</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><?= htmlspecialchars($p['nombre_cliente']) ?> (ID: <?= $p['id_cliente'] ?>)</td>
                    <td><?= htmlspecialchars($p['fecha_pedido']) ?></td>
                    <td><?= number_format($p['total'], 2) ?>€</td>
                    <td><?= htmlspecialchars(substr($p['direccion_envio'], 0, 40)) ?>...</td>
                    <td>
                        <span class="status-pill status-<?= htmlspecialchars($p['estado']) ?>">
                            <?= htmlspecialchars($p['estado']) ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <button type="submit" name="action" value="delete" class="btn btn-delete"
                                    onclick="return confirm('¿Estás seguro de eliminar el Pedido #<?= $p['id'] ?> y todos sus detalles?')">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php endif; ?>
    </div>
</body>
</html>