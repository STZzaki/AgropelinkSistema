<html>
<body>

<?php
// CapaNegocio/Usuario/Articulos.php

/**
 * Clase Articulo
 * Representa la estructura de un artículo de venta subido por un agricultor.
 */
class Articulo {
    public $nombre;
    public $categoria;
    public $precio;
    public $stock;
    public $descripcion;
    public $imagen_url;

    public function __construct($nombre, $categoria, $precio, $stock, $descripcion, $imagen_url) {
        $this->nombre = $nombre;
        $this->categoria = $categoria;
        $this->precio = $precio;
        $this->stock = $stock;
        $this->descripcion = $descripcion;
        $this->imagen_url = $imagen_url;
    }
}

/**
 * Clase GestorArticulos
 * Simula la gestión (carga y listado) de los artículos.
 * En una aplicación real, esto interactuaría con una base de datos.
 */
class GestorArticulos {
    
    /**
     * Simula la carga de artículos disponibles.
     * En una versión final, aquí se leería un archivo o base de datos.
     * @return Articulo[]
     */
    public function cargarArticulos() {
        // Datos de ejemplo para la demostración en el Catálogo
        $articulos = [];
        
        $articulos[] = new Articulo(
            "Manzana Fuji Ecológica", 
            "Frutas de Hueso", 
            1.85, 
            120, 
            "Dulces, crujientes y cultivadas bajo métodos ecológicos y sostenibles en Sevilla.",
            
			"../../Lib/Imagenes/productos/manzana.jpg"
        );

        $articulos[] = new Articulo(
            "Naranjas Navel de Zumo", 
            "Cítricos", 
            0.99, 
            500, 
            "Elaboradas para un zumo dulce y refrescante, recolectadas en Valencia.",
            "../../Lib/Imagenes/productos/naranja.jpg"
        );

        $articulos[] = new Articulo(
            "Pimientos Rojos de Asar", 
            "Hortalizas", 
            2.50, 
            80, 
            "Gran tamaño y sabor intenso, ideales para asados y guisos tradicionales.",
            "../../Lib/Imagenes/productos/pimientRojo.jpg"
        );

        $articulos[] = new Articulo(
            "Aguacates Hass", 
            "Frutas", 
            4.20, 
            30, 
            "Cremosos y listos para consumir, perfectos para ensaladas y tostadas.",
            "../../Lib/Imagenes/productos/aguacate.jpg"
        );
        
        return $articulos;
    }
}
?></body></html>
