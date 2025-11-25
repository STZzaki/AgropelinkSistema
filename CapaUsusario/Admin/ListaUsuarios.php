<?php
// CapaUsusario/Admin/ListaUsuarios.php - Gestión de usuarios por el Administrador

session_start();

// Redirigir si no es Admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'Admin') {
    header('Location: ../Acceso/Login.php');
    exit;
}

// Incluir la capa de negocio
require_once "../../CapaNegocio/Usuario/Usuario.php";
$gestorUsuarios = new GestorUsuarios();

$message = null;

// LÓGICA DE PROCESAMIENTO (Editar / Eliminar)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['action']) && $_POST['action'] === 'edit') {
        $id_to_edit = (int)$_POST['id'];
        $nombre = trim($_POST['nombre']);
        $correo = trim($_POST['correo']);
        $tipo = $_POST['tipo'];

        // Solo se permiten los roles definidos
        if (!in_array($tipo, ['Cliente', 'Agricultor', 'Admin'])) {
            $message = ["type" => "error", "text" => "Rol no válido."];
        } else {
            if ($gestorUsuarios->actualizarUsuarioAdmin($id_to_edit, $nombre, $correo, $tipo)) {
                $message = ["type" => "success", "text" => "Usuario ID {$id_to_edit} actualizado con éxito."];
            } else {
                $message = ["type" => "error", "text" => "Error al actualizar el usuario ID {$id_to_edit}. El correo podría ya estar en uso."];
            }
        }
    }
    
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id_to_delete = (int)$_POST['id'];
        // Evitar que el admin se elimine a sí mismo
        if ($id_to_delete === (int)$_SESSION['usuario_id']) {
            $message = ["type" => "error", "text" => "No puedes eliminar tu propia cuenta de Administrador desde aquí."];
        } elseif ($gestorUsuarios->eliminarUsuario($id_to_delete)) {
            $message = ["type" => "success", "text" => "Usuario ID {$id_to_delete} eliminado con éxito."];
        } else {
            $message = ["type" => "error", "text" => "Error al eliminar el usuario ID {$id_to_delete}. Podría tener pedidos o artículos asociados."];
        }
    }
}

$usuarios = $gestorUsuarios->cargarUsuarios();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f9; color: #333; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); padding: 30px; }
        h1 { color: #1a4d8c; margin-bottom: 25px; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #007bff; text-decoration: none; font-weight: 600; }
        
        .management-table { width: 100%; border-collapse: collapse; font-size: 14px; }
        .management-table th { background-color: #1a4d8c; color: white; padding: 12px 15px; text-align: left; }
        .management-table td { padding: 10px 15px; border-bottom: 1px solid #eee; vertical-align: middle; }
        .management-table tr:nth-child(even) { background-color: #f9f9f9; }
        
        /* Estilos de inputs para edición */
        .management-table input[type="text"], .management-table input[type="email"], .management-table select {
            width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;
        }
        
        /* Botones de acción */
        .btn { padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; transition: opacity 0.3s; }
        .btn-edit { background-color: #ffc107; color: #333; }
        .btn-delete { background-color: #dc3545; color: white; margin-left: 5px; }
        .btn-save { background-color: #28a745; color: white; }
        .btn-cancel { background-color: #6c757d; color: white; }
        
        .message { padding: 15px; border-radius: 5px; margin-bottom: 20px; font-weight: 600; }
        .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <a href="Panel.php" class="back-link"><i class="fas fa-arrow-left"></i> Volver al Panel</a>
        <h1>Gestión de Usuarios del Sistema</h1>

        <?php if ($message): ?>
            <div class="message <?= htmlspecialchars($message['type']) ?>">
                <?= htmlspecialchars($message['text']) ?>
            </div>
        <?php endif; ?>

        <table class="management-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Correo</th>
                    <th>Tipo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= $u->id ?>">
                        <td><?= $u->id ?></td>
                        <td><input type="text" name="nombre" value="<?= htmlspecialchars($u->nombre) ?>" required></td>
                        <td><?= htmlspecialchars($u->apellidos) ?></td>
                        <td><input type="email" name="correo" value="<?= htmlspecialchars($u->correo) ?>" required></td>
                        <td>
                            <select name="tipo">
                                <?php foreach (['Cliente', 'Agricultor', 'Admin'] as $tipo): ?>
                                    <option value="<?= $tipo ?>" <?= $u->tipo === $tipo ? 'selected' : '' ?>>
                                        <?= $tipo ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <button type="submit" name="action" value="edit" class="btn btn-save">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                            <button type="submit" name="action" value="delete" class="btn btn-delete" 
                                    onclick="return confirm('¿Estás seguro de que quieres eliminar al usuario <?= htmlspecialchars($u->nombre) ?>?')">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </button>
                        </td>
                    </form>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>