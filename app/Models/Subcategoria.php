<?php
    // Utilizamos el modelo de tipo PDO para nuestra tabla de Subcategorias
    require_once 'config/database.php';
    // El modelo ingresa la query a la base de datos
    
class Subcategoria {
    private $conn;
    private $table_name = "Subcategoria";

    // Propiedades del objeto Subcategoria
    public $id_subcategoria;
    public $id_categoria;
    public $nombre_subcategoria;
    public $descripcion;
    public $fecha_creacion;
    public $fecha_modificacion;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Obtiene todas las subcategorías con el nombre de su categoría asociada.
     * @return array Un array asociativo de subcategorías.
     */
    public function obtenerSubcategorias() {
        $query = "SELECT sc.id_subcategoria, sc.nombre_subcategoria, sc.descripcion,
                         c.id_categoria, c.nombre_categoria
                  FROM " . $this->table_name . " sc
                  JOIN Categoria c ON sc.id_categoria = c.id_categoria
                  ORDER BY sc.nombre_subcategoria ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crea una nueva subcategoría.
     * @param int $idCategoria El ID de la categoría a la que pertenece esta subcategoría.
     * @param string $nombreSubcategoria El nombre de la subcategoría.
     * @param string $descripcion La descripción de la subcategoría.
     * @return bool True si la subcategoría se creó exitosamente, false en caso contrario.
     * @throws Exception Si el nombre o la descripción de la subcategoría están vacíos.
     */
    public function crearSubcategoria($idCategoria, $nombreSubcategoria, $descripcion) {
        // Validación básica y sanitización
        if (empty(trim($nombreSubcategoria)) || empty(trim($descripcion))) {
            throw new Exception("Nombre y descripción de la subcategoría no pueden estar vacíos.");
        }

        $query = "INSERT INTO " . $this->table_name . " SET id_categoria=:id_categoria, nombre_subcategoria=:nombre_subcategoria, descripcion=:descripcion";
        $stmt = $this->conn->prepare($query);

        $idCategoria_clean = htmlspecialchars(strip_tags($idCategoria));
        $nombreSubcategoria_clean = htmlspecialchars(strip_tags(trim($nombreSubcategoria)));
        $descripcion_clean = htmlspecialchars(strip_tags(trim($descripcion)));

        $stmt->bindParam(":id_categoria", $idCategoria_clean, PDO::PARAM_INT);
        $stmt->bindParam(":nombre_subcategoria", $nombreSubcategoria_clean);
        $stmt->bindParam(":descripcion", $descripcion_clean);

        if ($stmt->execute()) {
            $this->id_subcategoria = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Actualiza una subcategoría existente.
     * @param int $idSubcategoria El ID de la subcategoría a actualizar.
     * @param string $nombreSubcategoria El nuevo nombre de la subcategoría.
     * @param string $descripcion La nueva descripción de la subcategoría.
     * @return bool True si la subcategoría se actualizó exitosamente, false en caso contrario.
     * @throws Exception Si el nombre o la descripción de la subcategoría están vacíos.
     */
    public function actualizarSubcategoria($idSubcategoria, $nombreSubcategoria, $descripcion) {
        // Validación básica y sanitización
        if (empty(trim($nombreSubcategoria)) || empty(trim($descripcion))) {
            throw new Exception("Nombre y descripción de la subcategoría no pueden estar vacíos.");
        }

        $query = "UPDATE " . $this->table_name . " SET nombre_subcategoria=:nombre_subcategoria, descripcion=:descripcion, fecha_modificacion=CURRENT_TIMESTAMP WHERE id_subcategoria=:id_subcategoria";
        $stmt = $this->conn->prepare($query);

        $nombreSubcategoria_clean = htmlspecialchars(strip_tags(trim($nombreSubcategoria)));
        $descripcion_clean = htmlspecialchars(strip_tags(trim($descripcion)));
        $idSubcategoria_clean = htmlspecialchars(strip_tags($idSubcategoria));

        $stmt->bindParam(":nombre_subcategoria", $nombreSubcategoria_clean);
        $stmt->bindParam(":descripcion", $descripcion_clean);
        $stmt->bindParam(":id_subcategoria", $idSubcategoria_clean, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }
        return false;
    }

    /**
     * Elimina una subcategoría por su ID.
     * @param int $idSubcategoria El ID de la subcategoría a eliminar.
     * @return bool True si la subcategoría se eliminó exitosamente, false en caso contrario.
     */
    public function eliminarSubcategoria($idSubcategoria) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_subcategoria = :id_subcategoria";
        $stmt = $this->conn->prepare($query);

        $idSubcategoria_clean = htmlspecialchars(strip_tags($idSubcategoria));
        $stmt->bindParam(":id_subcategoria", $idSubcategoria_clean, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }
        return false;
    }

    /**
     * Busca una subcategoría por su ID.
     * @param int $idSubcategoria El ID de la subcategoría a buscar.
     * @return array|false Un array asociativo con los datos de la subcategoría o false si no se encuentra.
     */
    public function buscarPorId($idSubcategoria) {
        $query = "SELECT id_subcategoria, nombre_subcategoria, descripcion, id_categoria
                  FROM " . $this->table_name . "
                  WHERE id_subcategoria = :id_subcategoria LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_subcategoria", $idSubcategoria, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
