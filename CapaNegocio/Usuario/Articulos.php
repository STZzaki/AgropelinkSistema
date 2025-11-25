<?php
// CapaNegocio/Usuario/Articulos.php

// Incluir el archivo de conexión. 
// 🔑 RUTA CORREGIDA: Subir un nivel (Usuario/) y luego ir a la nueva carpeta Acceso/
require_once __DIR__ . "/../Acceso/Conexion.php"; 

/**
 * Clase Articulo (Versión simplificada para listado)
 * Representa la estructura básica de un artículo de venta.
 */
class Articulo {
    public $id;
    public $id_agricultor;
    public $nombre;
    public $categoria;
    public $precio;
    public $stock;
    public $descripcion;
    public $imagen_url;

    public function __construct($id, $id_agricultor, $nombre, $categoria, $precio, $stock, $descripcion, $imagen_url) {
        $this->id = $id;
        $this->id_agricultor = $id_agricultor;
        $this->nombre = $nombre;
        $this->categoria = $categoria;
        $this->precio = $precio;
        $this->stock = $stock;
        $this->descripcion = $descripcion;
        $this->imagen_url = $imagen_url;
    }
}

/**
 * Clase ArticuloDetalle (Versión extendida para la página de producto)
 * Añade el nombre del agricultor al artículo.
 */
class ArticuloDetalle extends Articulo {
    public $nombre_agricultor;

    public function __construct($id, $id_agricultor, $nombre, $categoria, $precio, $stock, $descripcion, $imagen_url, $nombre_agricultor) {
        parent::__construct($id, $id_agricultor, $nombre, $categoria, $precio, $stock, $descripcion, $imagen_url);
        $this->nombre_agricultor = $nombre_agricultor;
    }
}


/**
 * Clase GestorArticulos - Interactúa con la BD.
 */
class GestorArticulos {
    
    private $pdo;

    public function __construct() {
        global $pdo; 
        $this->pdo = $pdo;
    }
    
    // GUARDAR NUEVO ARTÍCULO (Usado por ProcesarArticulo.php)
    public function guardarArticulo(Articulo $articulo) {
        $sql = "INSERT INTO articulos (id_agricultor, nombre, categoria, precio, stock, descripcion, imagen_url) 
                VALUES (:id_agricultor, :nombre, :categoria, :precio, :stock, :descripcion, :imagen_url)";
        
        $stmt = $this->pdo->prepare($sql);
        
        $stmt->execute([
            ':id_agricultor' => $articulo->id_agricultor,
            ':nombre' => $articulo->nombre,
            ':categoria' => $articulo->categoria,
            ':precio' => $articulo->precio,
            ':stock' => $articulo->stock,
            ':descripcion' => $articulo->descripcion,
            ':imagen_url' => $articulo->imagen_url
        ]);
        return true;
    }
    
    // CARGAR ARTÍCULOS (Usado por Catalogo.php y Listado Admin)
    public function cargarArticulos() {
        $sql = "SELECT id, id_agricultor, nombre, categoria, precio, stock, descripcion, imagen_url FROM articulos ORDER BY fecha_subida DESC";
        $stmt = $this->pdo->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $articulos = [];
        foreach ($data as $row) {
             $articulos[] = new Articulo(
                $row['id'], 
                $row['id_agricultor'], 
                $row['nombre'], 
                $row['categoria'], 
                $row['precio'], 
                $row['stock'], 
                $row['descripcion'], 
                $row['imagen_url'] 
            );
        }
        return $articulos;
    }
    
    /**
     * Carga todos los artículos subidos por un agricultor específico.
     * NECESARIO para MisProductos.php
     * @param int $id_agricultor ID del usuario agricultor.
     * @return Articulo[] Array de objetos Articulo.
     */
    public function cargarArticulosPorAgricultor(int $id_agricultor): array {
        $sql = "SELECT id, id_agricultor, nombre, categoria, precio, stock, descripcion, imagen_url 
                FROM articulos 
                WHERE id_agricultor = :id_agricultor 
                ORDER BY fecha_subida DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_agricultor' => $id_agricultor]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $articulos = [];
        foreach ($data as $row) {
             $articulos[] = new Articulo(
                $row['id'], 
                $row['id_agricultor'], 
                $row['nombre'], 
                $row['categoria'], 
                $row['precio'], 
                $row['stock'], 
                $row['descripcion'], 
                $row['imagen_url'] 
            );
        }
        return $articulos;
    }

    /**
     * Obtiene un artículo con el nombre del agricultor. NECESARIO para Producto.php.
     */
    public function obtenerArticuloPorIdConAgricultor($id) {
        $sql = "SELECT a.*, u.nombre AS nombre_agricultor 
                FROM articulos a
                JOIN usuarios u ON a.id_agricultor = u.id
                WHERE a.id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new ArticuloDetalle(
                $row['id'], 
                $row['id_agricultor'], 
                $row['nombre'], 
                $row['categoria'], 
                $row['precio'], 
                $row['stock'], 
                $row['descripcion'], 
                $row['imagen_url'], 
                $row['nombre_agricultor'] // Campo extraído del JOIN
            );
        }
        return null;
    }
    
    // NECESARIO para GestorPedidos::guardarPedido y otros usos internos
    public function obtenerArticuloPorId($id) {
        $sql = "SELECT id, id_agricultor, nombre, categoria, precio, stock, descripcion, imagen_url FROM articulos WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
             return new Articulo(
                $row['id'], 
                $row['id_agricultor'], 
                $row['nombre'], 
                $row['categoria'], 
                $row['precio'], 
                $row['stock'], 
                $row['descripcion'], 
                $row['imagen_url'] 
            );
        }
        return null;
    }
    
    /**
     * Elimina un artículo de la base de datos. Usado por Listado Admin y MisProductos.php
     * @param int $id ID del artículo a eliminar.
     * @return bool True si la eliminación fue exitosa, false en caso contrario.
     */
    public function eliminarArticulo(int $id): bool {
        try {
            $sql = "DELETE FROM articulos WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            // En un entorno real, registra $e->getMessage(). 
            error_log("Error al eliminar artículo: " . $e->getMessage());
            return false;
        }
    }
}
?>