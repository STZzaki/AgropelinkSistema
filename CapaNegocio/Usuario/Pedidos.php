<?php
// CapaNegocio/Usuario/Pedidos.php

// Incluir el archivo de conexi車n. 
// ?? RUTA CORREGIDA: Subir un nivel (Usuario/) y luego ir a la nueva carpeta Acceso/
require_once __DIR__ . "/../Acceso/Conexion.php"; 
// Necesitamos el GestorArticulos para obtener el ID del agricultor
require_once __DIR__ . "/Articulos.php"; 

/**
 * Clase para manejar la logica de insercion y gestiin de pedidos y detalles en la BD.
 */
class GestorPedidos {
    
    // ?? CAMBIO CLAVE: Cambiamos a protected para que las clases hijas puedan acceder a la conexi車n
    protected $pdo;

    public function __construct() {
        global $pdo; 
        $this->pdo = $pdo;
    }
    
    /**
     * Guarda el pedido completo (cabecera y detalles) en la BD usando una transacci車n.
     * @param int $id_cliente ID del usuario que compra.
     * @param array $carrito Contenido del carrito de $_SESSION.
     * @param string $direccion_envio Direcci車n proporcionada en el checkout.
     * @param float $total El importe total del pedido.
     * @return bool True si la compra fue exitosa, false en caso contrario.
     */
    public function guardarPedido($id_cliente, array $carrito, $direccion_envio, $total): bool {
        
        try {
            $this->pdo->beginTransaction();

            // 1. INSERTAR LA CABECERA DEL PEDIDO
            $sql_pedido = "INSERT INTO pedidos (id_cliente, total, direccion_envio, estado) 
                           VALUES (:id_cliente, :total, :direccion_envio, 'Procesando')";
            
            $stmt_pedido = $this->pdo->prepare($sql_pedido);
            $stmt_pedido->execute([
                ':id_cliente' => $id_cliente,
                ':total' => $total,
                ':direccion_envio' => $direccion_envio
            ]);
            
            // Obtener el ID del pedido reci谷n insertado
            $id_pedido = $this->pdo->lastInsertId();

            // 2. INSERTAR LOS DETALLES DEL PEDIDO (los productos)
            $sql_detalle = "INSERT INTO detalles_pedido (id_pedido, id_articulo, id_agricultor, nombre_articulo, precio_unitario, cantidad) 
                            VALUES (:id_pedido, :id_articulo, :id_agricultor, :nombre_articulo, :precio_unitario, :cantidad)";
            
            $stmt_detalle = $this->pdo->prepare($sql_detalle);
            $gestorArticulos = new GestorArticulos();

            foreach ($carrito as $item) {
                
                // Obtenemos el art赤culo original para el id_agricultor
                $articulo = $gestorArticulos->obtenerArticuloPorId($item['id']);
                
                if (!$articulo) {
                    throw new Exception("Error: Art赤culo con ID {$item['id']} no encontrado durante la compra.");
                }

                $stmt_detalle->execute([
                    ':id_pedido' => $id_pedido,
                    ':id_articulo' => $item['id'],
                    ':id_agricultor' => $articulo->id_agricultor,
                    ':nombre_articulo' => $item['nombre'],
                    ':precio_unitario' => $item['precio'],
                    ':cantidad' => $item['cantidad']
                ]);
            }
            
            // 3. Confirmar la transacci車n
            $this->pdo->commit();
            return true;
            
        } catch (Exception $e) {
            // Si algo falla, deshacemos todos los cambios
            $this->pdo->rollBack();
            error_log("Error al procesar el pedido: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un pedido y todos sus detalles asociados. NECESARIO para ListaPedidos.php
     * Utiliza transacciones para garantizar que ambos se eliminen o ninguno.
     * @param int $id_pedido ID del pedido a eliminar.
     * @return bool True si la eliminaci車n fue exitosa.
     */
    public function eliminarPedido(int $id_pedido): bool {
        try {
            $this->pdo->beginTransaction();

            // 1. Eliminar los detalles asociados al pedido
            $sql_detalle = "DELETE FROM detalles_pedido WHERE id_pedido = :id_pedido";
            $stmt_detalle = $this->pdo->prepare($sql_detalle);
            $stmt_detalle->execute([':id_pedido' => $id_pedido]);

            // 2. Eliminar la cabecera del pedido
            $sql_pedido = "DELETE FROM pedidos WHERE id = :id_pedido";
            $stmt_pedido = $this->pdo->prepare($sql_pedido);
            $stmt_pedido->execute([':id_pedido' => $id_pedido]);
            
            $this->pdo->commit();
            return true;
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error al eliminar el pedido: " . $e->getMessage());
            return false;
        }
    }
}
?>