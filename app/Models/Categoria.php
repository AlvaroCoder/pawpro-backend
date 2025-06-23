<?php
require_once __DIR__ . '/../../config/database.php'; 
class Categoria {
    private $conn;
    private $table_name = "Categoria";

    // Propiedades del objeto Categoria
    public $id_categoria;
    public $nombre_categoria;
    public $descripcion;
    public $fecha_creacion;
    public $fecha_modificacion;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Obtiene todas las categorías de la base de datos.
     * @return array Un array asociativo de categorías.
     */
    public function obtenerCategorias() {
        $query = "SELECT id_categoria, nombre_categoria, descripcion, fecha_creacion, fecha_modificacion
                  FROM " . $this->table_name . "
                  ORDER BY nombre_categoria ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crea una nueva categoría en la base de datos.
     * @param string $nombreCategoria El nombre de la categoría.
     * @param string $descripcion La descripción de la categoría.
     * @return bool True si la categoría se creó exitosamente, false en caso contrario.
     * @throws Exception Si el nombre de la categoría está vacío.
     */
    public function crearCategoria($nombreCategoria, $descripcion) {
        // Validación básica y sanitización
        if (empty(trim($nombreCategoria))) {
            throw new Exception("El nombre de la categoría no puede estar vacío.");
        }

        $query = "INSERT INTO " . $this->table_name . " SET nombre_categoria=:nombre_categoria, descripcion=:descripcion";
        $stmt = $this->conn->prepare($query);

        $nombreCategoria_clean = htmlspecialchars(strip_tags(trim($nombreCategoria)));
        $descripcion_clean = htmlspecialchars(strip_tags(trim($descripcion)));

        $stmt->bindParam(":nombre_categoria", $nombreCategoria_clean);
        $stmt->bindParam(":descripcion", $descripcion_clean);

        if ($stmt->execute()) {
            $this->id_categoria = $this->conn->lastInsertId(); // Establece el ID de la categoría recién creada
            return true;
        }

        return false;
    }

    /**
     * Actualiza una categoría existente en la base de datos.
     * @param int $idCategoria El ID de la categoría a actualizar.
     * @param string $nombreCategoria El nuevo nombre de la categoría.
     * @param string $descripcion La nueva descripción de la categoría.
     * @return bool True si la categoría se actualizó exitosamente, false en caso contrario.
     * @throws Exception Si el nombre o la descripción de la categoría están vacíos.
     */
    public function actualizarCategoria($idCategoria, $nombreCategoria, $descripcion) {
        // Validación básica y sanitización
        if (empty(trim($nombreCategoria)) || empty(trim($descripcion))) {
            throw new Exception("Nombre y descripción de la categoría no pueden estar vacíos.");
        }

        $query = "UPDATE " . $this->table_name . " SET nombre_categoria=:nombre_categoria, descripcion=:descripcion, fecha_modificacion=CURRENT_TIMESTAMP WHERE id_categoria=:id_categoria";
        $stmt = $this->conn->prepare($query);

        $nombreCategoria_clean = htmlspecialchars(strip_tags(trim($nombreCategoria)));
        $descripcion_clean = htmlspecialchars(strip_tags(trim($descripcion)));
        $idCategoria_clean = htmlspecialchars(strip_tags($idCategoria));

        $stmt->bindParam(":nombre_categoria", $nombreCategoria_clean);
        $stmt->bindParam(":descripcion", $descripcion_clean);
        $stmt->bindParam(":id_categoria", $idCategoria_clean);

        if ($stmt->execute()) {
            return $stmt->rowCount() > 0; // Retorna true si se afectó al menos una fila
        }
        return false;
    }

    /**
     * Elimina una categoría de la base de datos por su ID.
     * @param int $idCategoria El ID de la categoría a eliminar.
     * @return bool True si la categoría se eliminó exitosamente, false en caso contrario.
     */
    public function deleteCategoriaPorId($idCategoria) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_categoria = :id_categoria";
        $stmt = $this->conn->prepare($query);

        $idCategoria_clean = htmlspecialchars(strip_tags($idCategoria));
        $stmt->bindParam(":id_categoria", $idCategoria_clean, PDO::PARAM_INT); // Usar PDO::PARAM_INT para IDs

        if ($stmt->execute()) {
            return $stmt->rowCount() > 0; // Retorna true si se eliminó alguna fila
        }
        return false;
    }

    /**
     * Obtiene una categoría específica por su ID.
     * @param int $idCategoria El ID de la categoría a buscar.
     * @return array|false Un array asociativo con los datos de la categoría o false si no se encuentra.
     */
    public function obtenerCategoriaPorId($idCategoria) {
        $query = "SELECT id_categoria, nombre_categoria, descripcion
                  FROM " . $this->table_name . "
                  WHERE id_categoria = :id_categoria LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_categoria", $idCategoria, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
