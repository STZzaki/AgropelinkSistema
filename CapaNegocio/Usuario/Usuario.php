<?php
// CapaNegocio/Usuario/Usuario.php

// Incluir el archivo de conexión. 
// RUTA CORREGIDA: Subir un nivel (Usuario/) y luego ir a la carpeta Acceso/
require_once __DIR__ . "/../Acceso/Conexion.php"; 

/**
 * Clase Usuario
 * Representa a un usuario en el sistema AgropeLink.
 */
class Usuario {
    public $id; 
    public $nombre;
    public $apellidos;
    public $provincia;
    public $localidad;
    public $direccion;
    public $tipo; // 'Cliente', 'Agricultor', 'Admin'
    public $correo;
    public $contrasena; // Contiene el HASH de la contraseña

    // CONSTRUCTOR CON 9 ARGUMENTOS (ID incluido). 
    public function __construct($id, $nombre, $apellidos, $provincia, $localidad, $direccion, $tipo, $correo, $contrasena) {
        $this->id = $id; 
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->provincia = $provincia;
        $this->localidad = $localidad;
        $this->direccion = $direccion;
        $this->tipo = $tipo;
        $this->correo = $correo;
        $this->contrasena = $contrasena;
    }
}

/**
 * Clase GestorUsuarios
 * Maneja las operaciones CRUD en la tabla 'usuarios' usando la variable global $pdo.
 */
class GestorUsuarios {
    
    private $pdo;

    public function __construct() {
        // 🔑 CORRECCIÓN CLAVE: Usamos la variable global $pdo
        global $pdo; 
        $this->pdo = $pdo;
    }
    
    // Crear un nuevo usuario (Usado por el Registro)
    public function guardar(Usuario $usuario) {
        $sql = "INSERT INTO usuarios (nombre, apellidos, provincia, localidad, direccion, tipo, correo, contrasena_hash) 
                VALUES (:nombre, :apellidos, :provincia, :localidad, :direccion, :tipo, :correo, :contrasena_hash)";
        
        $stmt = $this->pdo->prepare($sql);
        
        $stmt->execute([
            ':nombre' => $usuario->nombre,
            ':apellidos' => $usuario->apellidos,
            ':provincia' => $usuario->provincia,
            ':localidad' => $usuario->localidad,
            ':direccion' => $usuario->direccion,
            ':tipo' => $usuario->tipo,
            ':correo' => $usuario->correo,
            ':contrasena_hash' => $usuario->contrasena
        ]);
    }

    /**
     * Obtiene un usuario por su ID. NECESARIO para MiCuenta.php
     */
    public function obtenerUsuarioPorId($id) {
        $sql = "SELECT id, nombre, apellidos, provincia, localidad, direccion, tipo, correo, contrasena_hash FROM usuarios WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            // Pasamos los 9 argumentos al constructor
            return new Usuario(
                $data['id'], 
                $data['nombre'], 
                $data['apellidos'], 
                $data['provincia'], 
                $data['localidad'], 
                $data['direccion'], 
                $data['tipo'], 
                $data['correo'], 
                $data['contrasena_hash']
            );
        }
        return null;
    }
    
    /**
     * Obtiene un usuario por su correo electrónico para verificar el login.
     */
    public function verificarLogin($correo, $contrasena_ingresada) {
        $sql = "SELECT id, nombre, apellidos, provincia, localidad, direccion, tipo, correo, contrasena_hash FROM usuarios WHERE correo = :correo";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':correo' => $correo]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            if (password_verify($contrasena_ingresada, $data['contrasena_hash'])) {
                return new Usuario(
                    $data['id'], 
                    $data['nombre'], $data['apellidos'], $data['provincia'], $data['localidad'], 
                    $data['direccion'], $data['tipo'], $data['correo'], $data['contrasena_hash']
                ); 
            }
        }
        return null;
    }

    /**
     * Verifica si un correo ya existe.
     */
    public function existeUsuario($correo) {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE correo = :correo";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':correo' => $correo]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Actualiza los detalles de la cuenta de un usuario (MiCuenta.php).
     */
    public function actualizarDetalles($id, $nombre, $apellidos, $correo, $password_hash = null) {
        $sql = "UPDATE usuarios SET nombre = :nombre, apellidos = :apellidos, correo = :correo";
        
        if ($password_hash !== null) {
            $sql .= ", contrasena_hash = :contrasena_hash";
        }
        
        $sql .= " WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        
        $params = [
            ':id' => $id,
            ':nombre' => $nombre,
            ':apellidos' => $apellidos,
            ':correo' => $correo
        ];
        
        if ($password_hash !== null) {
            $params[':contrasena_hash'] = $password_hash;
        }

        return $stmt->execute($params);
    }

    // --- MÉTODOS AÑADIDOS PARA EL ADMINISTRADOR ---

    /**
     * Carga todos los usuarios del sistema. Usado principalmente por el Administrador.
     */
    public function cargarUsuarios(): array {
        try {
            $sql = "SELECT id, nombre, apellidos, provincia, localidad, direccion, tipo, correo, contrasena_hash FROM usuarios ORDER BY id ASC";
            $stmt = $this->pdo->query($sql);
            $usuarios = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                 $usuarios[] = new Usuario(
                    $row['id'], 
                    $row['nombre'], 
                    $row['apellidos'], 
                    $row['provincia'], 
                    $row['localidad'], 
                    $row['direccion'], 
                    $row['tipo'], 
                    $row['correo'], 
                    $row['contrasena_hash']
                );
            }

            return $usuarios;
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Actualiza los detalles de un usuario desde el panel de administración.
     */
    public function actualizarUsuarioAdmin(int $id, string $nombre, string $correo, string $tipo): bool {
        try {
            // Nota: En la vista de administración, los apellidos no se editan.
            $sql = "UPDATE usuarios SET nombre = :nombre, correo = :correo, tipo = :tipo WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            
            return $stmt->execute([
                ':nombre' => $nombre,
                ':correo' => $correo,
                ':tipo' => $tipo,
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Elimina un usuario del sistema por su ID.
     */
    public function eliminarUsuario(int $id): bool {
        try {
            $sql = "DELETE FROM usuarios WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>