<?php
// Verificar si la clase ya está definida para evitar redeclaración
require_once 'config/database.php';

    class Categoria {
        // Propiedades
        public $id;
        public $nombreCategoria;
        public $descripcion;
        public $conn;

        public function __construct() {
            $database = new Database();
            $this->conn = $database->getConnection();
        }
        
        /**
         * Obtener todas las categorías (versión estática)
         */
        public static function getAll() {
            $database = new Database();
            $db = $database->getConnection();
            $stmt = $db->query("SELECT * FROM Categoria");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }


        public function obtenerCategorias() {
            $stmt = $this->conn->query("SELECT * FROM Categoria");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        /**
         * Obtener categoría por ID (seguro contra SQL injection)
         */
        public function obtenerPorId($idCategoria) {
            $sql = "SELECT * FROM Categoria WHERE id_categoria = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $idCategoria, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        /**
         * Crear nueva categoría
         */
        public function crearCategoria($nombre, $descripcion) {
            $sql = "INSERT INTO Categoria (nombre_categoria, descripcion) VALUES (:nombre, :descripcion)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':descripcion', $descripcion);

            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return $this->obtenerPorId($this->id);
            }
            return false;
        }

        public function actualizarCategoria($idCategoria, $nombreCategoria, $descripcion){
            if (empty($idCategoria) || !is_numeric($idCategoria)) {
                throw new InvalidArgumentException("ID de categoría inválido");
            }
        
            if (empty($nombreCategoria)) {
                throw new InvalidArgumentException("El nombre de categoría no puede estar vacío");
            }

            $sql = "UPDATE Categoria SET nombre_categoria=:nombre_categoria, descripcion=:descripcion, fecha_modificacion=CURRENT_TIMESTAMP WHERE id_categoria=:id_categoria";
            $stmt = $this->conn->prepare($sq);

            $nombreCategoria = htmlspecialchars(strip_tags($nombreCategoria));
            $descripcion = htmlspecialchars(strip_tags($descripcion));
            $idCategoria = (int)$idCategoria;

            $stmt->bindParam(':nombre_categoria',$nombreCategoria);
            $stmt->bindParam(':descripcion',$descripcion);
            $stmt->bindParam(':id_categoria', $nombre);

            if ($stmt->execute()) {
                return $this->obtenerPorId($idCategoria);
            }
            return false;
        }
    }
?>