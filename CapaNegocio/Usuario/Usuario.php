<?php
// Clase principal que representa a un usuario
class Usuario {
    public $nombre;
    public $apellidos;
    public $provincia;
    public $localidad;
    public $direccion;
    public $tipo;
    public $correo;
    public $contrasena; // Contiene el HASH de la contraseña

    public function __construct($nombre, $apellidos, $provincia, $localidad, $direccion, $tipo, $correo, $contrasena) {
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->provincia = $provincia;
        $this->localidad = $localidad;
        $this->direccion = $direccion;
        $this->tipo = $tipo;
        $this->correo = $correo;
        $this->contrasena = $contrasena;
    }

    // Método para generar la fila HTML (para guardar en usuarios_data.html)
    public function toRow() {
        // Aseguramos que la fila se guarde de forma compacta para evitar problemas de lectura con RegEx
        return "<tr>" .
            "<td>{$this->nombre}</td>" .
            "<td>{$this->apellidos}</td>" .
            "<td>{$this->provincia}</td>" .
            "<td>{$this->localidad}</td>" .
            "<td>{$this->direccion}</td>" .
            "<td>{$this->tipo}</td>" .
            "<td>{$this->correo}</td>" .
            "<td>{$this->contrasena}</td>" . // Guardamos el HASH
            "</tr>\n";
    }
}

// Clase para manejar las operaciones de guardar y cargar usuarios
class GestorUsuarios {
    // RUTA CORREGIDA: Usamos __DIR__ para garantizar que el archivo se encuentra 
    // en la misma carpeta que Usuario.php (CapaNegocio/Usuario/)
    private $archivo = __DIR__ . "/usuarios_data.html"; 

    public function guardar(Usuario $usuario) {
        if (!file_exists($this->archivo)) {
            // Contenido inicial de la tabla con encabezados
            $contenido = "<table><tr><th>Nombre</th><th>Apellidos</th><th>Provincia</th><th>Localidad</th><th>Dirección</th><th>Tipo</th><th>Correo</th><th>Contraseña</th></tr>\n";
        } else {
            $contenido = file_get_contents($this->archivo);
            // Quitamos la etiqueta de cierre </table> para añadir la nueva fila
            $contenido = preg_replace('/<\/table>/', '', $contenido); 
        }

        $contenido .= $usuario->toRow();
        $contenido .= "</table>";

        file_put_contents($this->archivo, $contenido);
    }

    public function cargarUsuarios() {
        if (!file_exists($this->archivo)) {
            return [];
        }

        $contenido = file_get_contents($this->archivo);
        // RegEx mejorada sin el modificador 's' para prevenir la captura de saltos de línea
        $regex = '/<tr><td>(.*?)<\/td><td>(.*?)<\/td><td>(.*?)<\/td><td>(.*?)<\/td><td>(.*?)<\/td><td>(.*?)<\/td><td>(.*?)<\/td><td>(.*?)<\/td><\/tr>/';
        
        preg_match_all($regex, $contenido, $filas, PREG_SET_ORDER);

        $usuarios = [];
        foreach ($filas as $f) {
            // 🔑 CORRECCIÓN: Aplicar trim() a la contraseña (índice 8) para asegurar la limpieza del hash
            $contrasena_limpia = trim($f[8]); 

            $usuarios[] = new Usuario(
                trim($f[1]), trim($f[2]), trim($f[3]), trim($f[4]), 
                trim($f[5]), trim($f[6]), trim($f[7]), $contrasena_limpia
            );
        }
        return $usuarios;
    }

    // 🔑 CORRECCIÓN CLAVE: Verifica el login usando password_verify()
    public function verificarLogin($correo, $contrasena_ingresada) {
        $usuarios = $this->cargarUsuarios();
        foreach ($usuarios as $u) {
            if ($u->correo == $correo) {
                // Compara la contraseña plana ingresada contra el hash guardado
                if (password_verify($contrasena_ingresada, $u->contrasena)) {
                    return $u; // Login exitoso
                }
                // Correo encontrado, pero contraseña incorrecta
                return null; 
            }
        }
        return null; // Usuario no encontrado
    }

    public function existeUsuario($correo) {
        $usuarios = $this->cargarUsuarios();
        foreach ($usuarios as $u) {
            if ($u->correo == $correo) {
                return true;
            }
        }
        return false;
    }
}
// El cierre ?>