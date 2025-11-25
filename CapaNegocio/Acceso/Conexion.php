<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost'); // Cambia si tu servidor de DB no está en localhost
define('DB_NAME', 'agropelink_db'); // Cambia al nombre real de tu base de datos
define('DB_USER', 'root'); // **IMPORTANTE: Cambia por tu usuario**
define('DB_PASS', ''); // **IMPORTANTE: Cambia por tu contraseña**

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
    // En caso de error de conexión, terminamos el script y mostramos el error (solo en desarrollo)
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Puedes comentar o eliminar esta línea si no quieres que se imprima nada
// echo "Conexión a la base de datos exitosa.";

// La variable $pdo contiene el objeto de conexión que usaremos en GestorUsuarios.
?>