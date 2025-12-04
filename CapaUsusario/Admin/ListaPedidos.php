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
    <link rel="stylesheet" href="..\..\Lib\Estilos\estilos.css">
    
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
                    <td><?= number_format($p['total'], 2) ?>�?/td>
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