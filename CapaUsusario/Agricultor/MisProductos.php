<?php
// CapaUsusario/Agricultor/MisProductos.php - Gestión de artículos del agricultor

session_start();

// Redirigir si no está logueado o no es Agricultor
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'Agricultor') {
    header('Location: ../Acceso/Login.php');
    exit;
}

require_once "../../CapaNegocio/Usuario/Articulos.php";
$gestorArticulos = new GestorArticulos();
$id_agricultor = (int)$_SESSION['usuario_id'];

$message = null;

// LÓGICA DE PROCESAMIENTO (Eliminar Artículo)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id_to_delete = (int)$_POST['id'];
    $image_path = $_POST['image_path']; 

    // 1. Eliminar el registro de la base de datos
    if ($gestorArticulos->eliminarArticulo($id_to_delete)) {
        
        // 2. Intentar eliminar la imagen física para liberar espacio
        // Ruta para ir de CapaUsusario/Agricultor/ a la raíz del proyecto
        $absolute_path = realpath(__DIR__ . "/../../" . str_replace("../", "", $image_path));
        
        if (file_exists($absolute_path) && !is_dir($absolute_path)) {
            // Eliminar la imagen
            unlink($absolute_path);
        }
        
        $message = ["type" => "success", "text" => "Artículo ID {$id_to_delete} eliminado con éxito."];
    } else {
        $message = ["type" => "error", "text" => "Error al eliminar el artículo ID {$id_to_delete}. Podría estar asociado a un pedido."];
    }
}

// Cargar SOLAMENTE los artículos de este agricultor
$articulos = $gestorArticulos->cargarArticulosPorAgricultor($id_agricultor);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Productos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f9; color: #333; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); padding: 30px; }
        h1 { color: #53ad57; margin-bottom: 25px; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #894514; text-decoration: none; font-weight: 600; }
        
        .management-table { width: 100%; border-collapse: collapse; font-size: 14px; }
        .management-table th { background-color: #53ad57; color: white; padding: 12px 15px; text-align: left; }
        .management-table td { padding: 10px 15px; border-bottom: 1px solid #eee; vertical-align: middle; }
        .management-table tr:nth-child(even) { background-color: #f9f9f9; }
        
        .item-image { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; }

        .btn { padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; transition: opacity 0.3s; }
        .btn-delete { background-color: #dc3545; color: white; }
        .btn-delete:hover { opacity: 0.8; }
        
        .btn-upload { background: #894514; color: white; text-decoration: none; padding: 10px 15px; border-radius: 5px; margin-bottom: 20px; display: inline-block; }
        .btn-upload:hover { background: #6b360f; }

        .message { padding: 15px; border-radius: 5px; margin-bottom: 20px; font-weight: 600; }
        .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        .empty-message { text-align: center; padding: 50px; color: #888; border: 1px dashed #ddd; border-radius: 8px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="../Inicial/MiCuenta.php" class="back-link"><i class="fas fa-arrow-left"></i> Volver a Mi Cuenta</a>
        <h1><i class="fas fa-seedling"></i> Mi Inventario de Productos</h1>

        <?php if ($message): ?>
            <div class="message <?= htmlspecialchars($message['type']) ?>">
                <?= htmlspecialchars($message['text']) ?>
            </div>
        <?php endif; ?>

        <a href="SubirArticulo.html" class="btn-upload"><i class="fas fa-plus"></i> Añadir Nuevo Artículo</a>
        
        <?php if (empty($articulos)): ?>
            <div class="empty-message">
                <p>Aún no has subido ningún artículo a AgropeLink.</p>
                <p>¡Empieza a vender tu cosecha!</p>
            </div>
        <?php else: ?>

        <table class="management-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Precio (€/Kg)</th>
                    <th>Stock (Kg)</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articulos as $a): ?>
                <tr>
                    <td><?= $a->id ?></td>
                    <td>
                        <img src="<?= htmlspecialchars($a->imagen_url) ?>" 
                             onerror="this.onerror=null; this.src='../../Lib/Imagenes/productos/img/default.jpg';"
                             alt="Imagen" class="item-image">
                    </td>
                    <td><?= htmlspecialchars($a->nombre) ?></td>
                    <td><?= htmlspecialchars($a->categoria) ?></td>
                    <td><?= number_format($a->precio, 2) ?></td>
                    <td><?= $a->stock ?></td>
                    <td><?= htmlspecialchars(substr($a->descripcion, 0, 40)) ?>...</td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?= $a->id ?>">
                            <input type="hidden" name="image_path" value="<?= htmlspecialchars($a->imagen_url) ?>">
                            <button type="submit" name="action" value="delete" class="btn btn-delete"
                                    onclick="return confirm('¿Estás seguro de eliminar el artículo <?= htmlspecialchars($a->nombre) ?>? Esto eliminará el producto y la imagen del servidor.')">
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