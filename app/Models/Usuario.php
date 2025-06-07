<?php
require_once 'config/database.php';

class Usuario {
    private $conn;
    private $table_name = "usuarios";

    // Propiedades del usuario
    public $id;
    public $nombre_usuario;
    public $correo_electronico;
    public $contraseña_hash;
    public $estado;
    public $rol_id;
    public $creado_en;
    public $actualizado_en;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Registrar un nuevo usuario
     */
    public function registrar($nombre_usuario, $contrasena, $correo, $rol_id = 4) {
        // Validar datos básicos
        if (empty($nombre_usuario) || empty($contrasena) || empty($correo)) {
            throw new Exception("Todos los campos son requeridos");
        }

        // Verificar si el correo ya existe
        if ($this->correoExiste($correo)) {
            throw new Exception("El correo electrónico ya está registrado");
        }

        // Hash de la contraseña
        $contrasena_hash = password_hash($contrasena, PASSWORD_BCRYPT);

        $sql = "INSERT INTO " . $this->table_name . " 
                (nombre_usuario, contraseña_hash, correo_electronico, estado, rol_id, creado_en)
                VALUES (:nombre_usuario, :contrasena_hash, :correo, 'activo', :rol_id, NOW())";
        
        $stmt = $this->conn->prepare($sql);
        
        // Limpiar y bindear parámetros
        $nombre_usuario = htmlspecialchars(strip_tags($nombre_usuario));
        $correo = htmlspecialchars(strip_tags($correo));

        $stmt->bindParam(':nombre_usuario', $nombre_usuario);
        $stmt->bindParam(':contrasena_hash', $contrasena_hash);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':rol_id', $rol_id);

        if ($stmt->execute()) {
            // Retornar el ID del nuevo usuario
            $this->id = $this->conn->lastInsertId();
            return $this->obtenerPorId($this->id);
        }

        return false;
    }

    /**
     * Obtener usuario por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT id, nombre_usuario, correo_electronico, estado, rol_id, creado_en 
                FROM " . $this->table_name . " 
                WHERE id = :id LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->nombre_usuario = $row['nombre_usuario'];
            $this->correo_electronico = $row['correo_electronico'];
            $this->estado = $row['estado'];
            $this->rol_id = $row['rol_id'];
            $this->creado_en = $row['creado_en'];
            return $row;
        }

        return false;
    }

    /**
     * Verificar si un correo ya existe
     */
    public function correoExiste($correo) {
        $sql = "SELECT id FROM " . $this->table_name . " WHERE correo_electronico = :correo LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
    public function obtenerPorCoreo($cooreo){
        $sql = "SELECT * FROM Usuarios WHERE correo_electronico=".$correo;
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();

        if ($stmt->rowCount()>0 ) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->nombre_usuario = $row['nombre_usuario'];
            $this->correo_electronico = $row['correo_electronico'];
            $this->contraseña_hash = $row['contraseña_hash'];
            $this->estado = $row['estado'];
            $this->rol_id = $row['rol_id'];
            return $row;
        }
    }
    /**
     * Autenticar usuario (para uso en AuthController)
     */
    public function autenticar($correo, $contrasena) {
        $usuario = $this->obtenerPorCorreo($correo);

        if (!$usuario) {
            return false;
        }

        // Verificar estado
        if ($usuario['estado'] !== 'activo') {
            throw new Exception("Cuenta inactiva");
        }

        // Verificar contraseña
        if (password_verify($contrasena, $usuario['contraseña_hash'])) {
            // Eliminar contraseña del resultado
            unset($usuario['contraseña_hash']);
            return $usuario;
        }

        return false;
    }

    /**
     * Actualizar información del usuario
     */
    public function actualizar($id, $datos) {
        // Implementar según necesidades

    }

    /**
     * Cambiar contraseña
     */
    public function cambiarContrasena($id, $nueva_contrasena) {
        $contrasena_hash = password_hash($nueva_contrasena, PASSWORD_BCRYPT);
        
        $sql = "UPDATE " . $this->table_name . " 
                SET contraseña_hash = :contrasena_hash, actualizado_en = NOW() 
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':contrasena_hash', $contrasena_hash);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function listarUsuarios(){
        $sql = "SELECT * FROM Usuarios ";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>