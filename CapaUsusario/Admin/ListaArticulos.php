<?php
// CapaUsusario/Admin/ListaArticulos.php - Gestión de artículos por el Administrador

session_start();

// Redirigir si no es Admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'Admin') {
    header('Location: ../Acceso/Login.php');
    exit;
}

require_once "../../CapaNegocio/Usuario/Articulos.php";
require_once "../../CapaNegocio/Usuario/Usuario.php";
$gestorArticulos = new GestorArticulos();
$gestorUsuarios = new GestorUsuarios();

$message = null;

// LÓGICA DE PROCESAMIENTO (Eliminar)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id_to_delete = (int)$_POST['id'];
    $image_path = $_POST['image_path']; // Ruta relativa de la imagen guardada en BD

    // 1. Eliminar el registro de la base de datos
    if ($gestorArticulos->eliminarArticulo($id_to_delete)) {
        
        // 2. Intentar eliminar la imagen física (OPCIONAL: Limpiar el servidor)
        // Construimos la ruta absoluta: subimos desde Admin/, subimos desde CapaUsusario/ a la raíz del proyecto
        // y luego bajamos a Lib/Imagenes/productos/.
        $absolute_path = realpath(__DIR__ . "/../../" . str_replace("../", "", $image_path));
        
        // La comprobación de ruta puede ser compleja; si falla, se registra y se ignora.
        if (file_exists($absolute_path) && !is_dir($absolute_path)) {
            // Eliminar la imagen
            unlink($absolute_path);
        }
        
        $message = ["type" => "success", "text" => "Artículo ID {$id_to_delete} eliminado con éxito."];
    } else {
        $message = ["type" => "error", "text" => "Error al eliminar el artículo ID {$id_to_delete}. Podría estar asociado a un pedido."];
    }
}

// Cargar todos los artículos para la tabla
$articulos = $gestorArticulos->cargarArticulos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Artículos</title>
    <link rel="stylesheet" href="..\..\Lib\Estilos\estilos.css">
    
</head>
<body>
    <div class="container">
        <a href="Panel.php" class="back-link"><i class="fas fa-arrow-left"></i> Volver al Panel</a>
        <h1>Gestión de Artículos Publicados</h1>

        <?php if ($message): ?>
            <div class="message <?= htmlspecialchars($message['type']) ?>">
                <?= htmlspecialchars($message['text']) ?>
            </div>
        <?php endif; ?>

        <table class="management-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Agricultor ID</th>
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
                    <td><?= $a->id_agricultor ?></td>
                    <td><?= htmlspecialchars($a->categoria) ?></td>
                    <td><?= number_format($a->precio, 2) ?></td>
                    <td><?= $a->stock ?></td>
                    <td><?= htmlspecialchars(substr($a->descripcion, 0, 50)) ?>...</td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?= $a->id ?>">
                            <input type="hidden" name="image_path" value="<?= htmlspecialchars($a->imagen_url) ?>">
                            <button type="submit" name="action" value="delete" class="btn btn-delete"
                                    onclick="return confirm('¿Estás seguro de eliminar el artículo <?= htmlspecialchars($a->nombre) ?>? Esto eliminará el producto y la imagen del servidor.')">
                                <i class="fas fa-trash-alt"></i> Borrar
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>