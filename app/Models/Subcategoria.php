<?php
    // Utilizamos el modelo de tipo PDO para nuestra tabla de Subcategorias
    require_once 'config/database.php';
    // El modelo ingresa la query a la base de datos
    class Subcategoria{
        // Propiedades
        public $id;
        public $nombreCategoria;
        public $descripcion;
        public $conn;

        public function __construct() {
            $database = new Database();
            $this->conn = $database->getConnection();
        }

        public function obtenerSubcategorias() {
            $stmt = $this->conn->query("SELECT * FROM Subcategoria");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        /**
         * Obtener categoría por ID (seguro contra SQL injection)
         */
        public function obtenerPorId($idSubcategoria) {
            $sql = "SELECT * FROM Subcategoria WHERE id_subcategoria = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $idSubcategoria, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        /**
         * Crear nueva categoría
         */
        public function crearSubcategoria(
            $idCategoria,
            $nombre, 
            $descripcion) 
        {
            $sql = "INSERT INTO Subcategoria 
            (id_categoria, nombre_subcategoria, descripcion) VALUES (:id_categoria, :nombre, :descripcion)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_categoria',$idCategoria);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':descripcion', $descripcion);

            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return $this->obtenerPorId($this->id);
            }
            return false;
        }

        public function actualizarSubcategoria($idCategoria, $nombreCategoria, $descripcion){
            if (empty($idCategoria) || !is_numeric($idCategoria)) {
                throw new InvalidArgumentException("ID de categoría inválido");
            }
        
            if (empty($nombreCategoria)) {
                throw new InvalidArgumentException("El nombre de categoría no puede estar vacío");
            }

            $sql = "UPDATE Categoria SET nombre_categoria=:nombre_categoria, descripcion=:descripcion, fecha_modificacion=CURRENT_TIMESTAMP WHERE id_categoria=:id_categoria";
            $stmt = $this->conn->prepare($sql);

            $nombreCategoria = htmlspecialchars(strip_tags($nombreCategoria));
            $descripcion = htmlspecialchars(strip_tags($descripcion));
            $idCategoria = (int)$idCategoria;


            $stmt->bindParam(':nombre_categoria',$nombreCategoria);
            $stmt->bindParam(':descripcion',$descripcion);
            $stmt->bindParam(':id_categoria', $idCategoria);

            if ($stmt->execute()) {
                return $this->obtenerPorId($idCategoria);
            }
            return false;
        }

        public function deleteCategoriaPorId($idCategoria){
            if (!is_numeric($idCategoria)) {
                throw new InvalidArgumentException("ID de categoría inválido");
            }
            $sql = "DELETE FROM Categoria WHERE id_categoria = :id_categoria";
            $stmt = $this->conn->prepare($sql);

            $stmt->bindParam(':id_categoria', $idCategoria);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        }
    }
?>