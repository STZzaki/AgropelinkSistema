<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'agropelink_db'); 
define('DB_USER', 'root'); 
define('DB_PASS', ''); 

// Variable global para la conexión PDO
$pdo = null;

try {
    // Establecer la conexión usando PDO
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", 
        DB_USER, 
        DB_PASS
    );
    
    // Configurar atributos de PDO para manejar errores (excepciones)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // 🔑 Muestra el error de conexión en la página
    die("ERROR DE CONEXIÓN A LA BASE DE DATOS: Revise DB_USER/DB_PASS/DB_NAME. Mensaje: " . $e->getMessage());
}
?>