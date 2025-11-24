<?php
require_once "Usuario.php";
// La clase GestorUsuarios ahora lee de usuarios_data.html
$gestor = new GestorUsuarios();
$usuarios = $gestor->cargarUsuarios();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios Registrados - AgropeLink</title>
    <!-- Enlace a Font Awesome para los íconos --><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" xintegrity="sha512-SnH5WK+bZ6fW+Nn4P2Jz7j4+Wz/z+r1/2e45N3J+J+A3Uo6lA5u0xY/1x5u6u6/9t8t5g/A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* COLORES AGROPELINK: #894514 (Marrón), #53ad57 (Verde Oliva), #29b69b (Verde Esmeralda) */
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f0f4f8 0%, #e0e6eb 100%);
            min-height: 100vh;
            padding: 30px 20px;
            color: #333;
        }

        .header {
            max-width: 1100px;
            margin: 0 auto 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            color: #894514;
            font-size: 28px;
        }

        .back-link a {
            color: #29b69b;
            text-decoration: none;
            font-weight: 600;
            padding: 8px 15px;
            border: 2px solid #29b69b;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .back-link a:hover {
            background-color: #29b69b;
            color: white;
        }

        .table-container {
            max-width: 1100px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th {
            background-color: #53ad57; /* Verde Oliva */
            color: white;
            padding: 15px;
            text-align: left;
            position: sticky;
            top: 0;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            white-space: nowrap; /* Evita que el texto se rompa */
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #e6f7e6; /* Verde muy claro al pasar el ratón */
        }

        /* Estilos de roles para las filas */
        .agricultor-row {
            border-left: 5px solid #894514; /* Marrón */
        }

        .cliente-row {
            border-left: 5px solid #29b69b; /* Verde Esmeralda */
        }

        /* Estilo específico para la columna Tipo */
        .tipo-col {
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <?php
    // Si la lista de usuarios está vacía, mostramos un mensaje amigable
    if (empty($usuarios)) {
    ?>
        <div class="header" style="justify-content: center; text-align: center;">
            <h1 style="color: #29b69b;"><i class="fas fa-info-circle"></i> No hay usuarios registrados.</h1>
        </div>
    <?php
    } else {
    // Si hay usuarios, mostramos la tabla:
    ?>
    
    <div class="header">
        <h1><i class="fas fa-users"></i> Listado de Usuarios AgropeLink</h1>
        <div class="back-link">
            <a href="../../CapaUsuario/Acceso/Login.php">Volver al Login</a>
        </div>
    </div>

    <div class="table-container">
        <table>
            <tr>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Provincia</th>
                <th>Localidad</th>
                <th>Dirección</th>
                <th>Tipo</th>
                <th>Correo</th>
                <th>Contraseña</th>
            </tr>
            <?php
            // Se imprimen las filas generadas por la función toHtmlTableRows()
            echo $gestor->toHtmlTableRows(); 
            ?>
        </table>
    </div>

    <?php
    } // Cierre del bloque else
    ?>
    
</body>
</html>
