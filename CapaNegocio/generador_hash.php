<?php
// Script temporal para generar el hash de la contraseña del administrador.
// Ejecuta este archivo en tu navegador una vez.

$contrasena_plana = 'admin12345'; // <-- CAMBIA ESTO por la contraseña real que quieres usar
$hash = password_hash($contrasena_plana, PASSWORD_DEFAULT);

echo "Contraseña plana: " . htmlspecialchars($contrasena_plana) . "<br>";
echo "<strong>HASH GENERADO:</strong> " . htmlspecialchars($hash);

// Descomenta la siguiente línea para insertar el hash en tu BD automáticamente:
// require_once 'ruta/a/Conexion.php';
// global $pdo;
// $pdo->exec("INSERT INTO usuarios (nombre, apellidos, provincia, localidad, direccion, tipo, correo, contrasena_hash) 
//            VALUES ('Admin', 'Jefe', 'Madrid', 'Madrid', 'Calle Central 1', 'Admin', 'admin@agropelink.com', '{$hash}')");

?>