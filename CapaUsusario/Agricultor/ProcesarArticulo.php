<?php
// CapaUsusario/Agricultor/ProcesarArticulo.php

session_start();

// 1. Verificar sesión y tipo de usuario (Seguridad)
// REQUIERE que el login haya guardado $_SESSION['usuario_id'] y $_SESSION['usuario_tipo']
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'Agricultor') {
    header('Location: ../Acceso/Login.php');
    exit;
}

// Rutas de las clases de Negocio (Asegúrate que la ruta a Articulos.php es correcta)
require_once "../../CapaNegocio/Usuario/Articulos.php"; 

$message = "";
$is_success = false;
$icon = "fas fa-triangle-exclamation"; // Icono de error por defecto

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. Recolección de datos del formulario
    $id_agricultor = $_SESSION['usuario_id']; // ID del agricultor logueado
    $nombre = $_POST['nombre'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $precio = (float)($_POST['precio'] ?? 0.0);
    $stock = (int)($_POST['stock'] ?? 0);
    $descripcion = $_POST['descripcion'] ?? '';
    
    // 3. Manejo de Subida de Archivos
    // A. Definir rutas
    $target_dir = "../../Lib/Imagenes/productos/"; // Carpeta de destino relativa al PROCESADOR
    $base_filename = basename($_FILES["imagen"]["name"]);
    
    // Generamos un nombre de archivo único (timestamp + nombre original)
    $unique_filename = time() . "_" . $base_filename;
    
    // Ruta ABSOLUTA para guardar el archivo físicamente
    $target_file_path = $target_dir . $unique_filename; 
    
    // Ruta RELATIVA para GUARDAR EN LA BASE DE DATOS (lo que el navegador verá)
    // Es relativa a la raíz del sitio, o al menos a la ubicación del Catalogo.php
    $db_imagen_path = "../../Lib/Imagenes/productos/" . $unique_filename; 

    // B. Mover el archivo subido
    if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file_path)) {
        
        // La imagen se subió físicamente con éxito
        
        // 4. Guardar datos en la Base de Datos
        $articulo = new Articulo(
            null, // ID (auto-incrementado)
            $id_agricultor, 
            $nombre, 
            $categoria, 
            $precio, 
            $stock, 
            $descripcion, 
            $db_imagen_path // <-- GUARDAMOS LA RUTA WEB EN LA BD
        );

        $gestor = new GestorArticulos();
        
        try {
            $gestor->guardarArticulo($articulo);
            $message = "¡Artículo subido correctamente! Aparecerá en el catálogo.";
            $is_success = true;
            $icon = "fas fa-circle-check";
        } catch (Exception $e) {
            $message = "Error al guardar los datos del artículo en la base de datos. Asegúrate de que tu ID de agricultor es válido. Detalles: " . $e->getMessage();
            $is_success = false;
            $icon = "fas fa-database";
            // Opcional: Si falla la BD, podrías eliminar el archivo subido físicamente:
            // unlink($target_file_path); 
        }
        
    } else {
        // Error de subida de archivo (permisos, tamaño, etc.)
        $message = "Error: No se pudo subir el archivo de imagen. Asegúrate de que la carpeta 'Lib/Imagenes/productos/' tiene permisos de escritura.";
        $is_success = false;
        $icon = "fas fa-image";
    }
    
} else {
    // Si se accede directamente, redirigir al formulario
    // header('Location: SubirArticulo.html');
    // exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_success ? "Éxito" : "Error" ?> - AgropeLink</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Estilos básicos para mostrar el resultado */
        body {
            background: linear-gradient(135deg, #53ad57 0%, #29b69b 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .message-box {
            width: 450px;
            max-width: 90%;
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .icon-container {
            width: 80px; height: 80px; border-radius: 50%; display: flex;
            justify-content: center; align-items: center; margin: 0 auto 20px;
            font-size: 36px; border: 2px solid;
        }
        .success { background-color: #e6f7e6; color: #53ad57; border-color: #53ad57; }
        .error { background-color: #ffe6e6; color: #cc0000; border-color: #cc0000; }
        h1 { font-size: 24px; color: #333; margin-bottom: 15px; }
        p { color: #666; margin-bottom: 30px; line-height: 1.6; }
        .links a {
            display: block; background: #29b69b; color: white; padding: 12px;
            border-radius: 8px; text-decoration: none; font-weight: 600;
            margin-bottom: 10px; transition: background 0.3s;
        }
        .links a:hover { background: #53ad57; }
    </style>
</head>
<body>
    <div class="message-box">
        <div class="icon-container <?= $is_success ? 'success' : 'error' ?>">
            <i class="<?= $icon ?>"></i>
        </div>
        
        <h1><?= $is_success ? "¡Subida Exitosa!" : "¡Ha Ocurrido un Error!" ?></h1>
        
        <p><?= htmlspecialchars($message) ?></p>

        <div class="links">
            <a href="SubirArticulo.html">Subir otro Artículo</a>
            <a href="../../Usuario/Catalogo.php">Ver Catálogo</a>
        </div>
    </div>
</body>
</html>