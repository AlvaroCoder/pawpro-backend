<?php

/**
 * Clase Producto.
 * Maneja las operaciones de base de datos para la entidad Productos.
 */
class Producto {
    private $conn;
    private $table_name = "Productos";

    // Propiedades del objeto Producto (reflejan columnas y datos de JOINs)
    public $producto_id;
    public $codigo_producto;
    public $nombre_producto;
    public $descripcion;
    public $marca_id;
    public $nombre_marca; // Proveniente de JOIN con Marcas
    public $precio_venta_unitario;
    public $stock_minimo;
    public $stock_maximo;
    public $unidad_medida;
    public $estado;
    public $subcategoria_id;
    public $nombre_subcategoria; // Proveniente de JOIN con Subcategoria
    public $presentacion_id;
    public $nombre_presentacion; // Proveniente de JOIN con Presentacion
    public $fecha_creacion;
    public $fecha_modificacion;

    /**
     * Constructor con la conexión a la base de datos.
     * @param $db_connection Objeto de conexión PDO.
     */
    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    /**
     * Crea un nuevo producto en la base de datos.
     * Los datos se toman de las propiedades públicas del objeto.
     * @return bool Verdadero si la creación fue exitosa, falso en caso contrario.
     */
    public function crear() {
        $query = "INSERT INTO " . $this->table_name . " SET
                    codigo_producto=:codigo_producto,
                    nombre_producto=:nombre_producto,
                    descripcion=:descripcion,
                    marca_id=:marca_id,
                    precio_venta_unitario=:precio_venta_unitario,
                    stock_minimo=:stock_minimo,
                    stock_maximo=:stock_maximo,
                    unidad_medida=:unidad_medida,
                    estado=:estado,
                    subcategoria_id=:subcategoria_id,
                    presentacion_id=:presentacion_id";
        // fecha_creacion y fecha_modificacion se manejan por DEFAULT o TRIGGERS en la BD.

        $stmt = $this->conn->prepare($query);

        // Sanitización básica de datos
        $this->codigo_producto = htmlspecialchars(strip_tags(trim($this->codigo_producto)));
        $this->nombre_producto = htmlspecialchars(strip_tags(trim($this->nombre_producto)));
        $this->descripcion = !empty($this->descripcion) ? htmlspecialchars(strip_tags(trim($this->descripcion))) : null;
        $this->marca_id = htmlspecialchars(strip_tags($this->marca_id));
        $this->precio_venta_unitario = htmlspecialchars(strip_tags($this->precio_venta_unitario));
        $this->stock_minimo = htmlspecialchars(strip_tags($this->stock_minimo));
        $this->stock_maximo = !empty($this->stock_maximo) ? htmlspecialchars(strip_tags($this->stock_maximo)) : null;
        $this->unidad_medida = !empty($this->unidad_medida) ? htmlspecialchars(strip_tags(trim($this->unidad_medida))) : 'unidad';
        $this->estado = htmlspecialchars(strip_tags($this->estado));
        $this->subcategoria_id = !empty($this->subcategoria_id) ? htmlspecialchars(strip_tags($this->subcategoria_id)) : null;
        $this->presentacion_id = !empty($this->presentacion_id) ? htmlspecialchars(strip_tags($this->presentacion_id)) : null;

        // Vinculación de parámetros
        $stmt->bindParam(":codigo_producto", $this->codigo_producto);
        $stmt->bindParam(":nombre_producto", $this->nombre_producto);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":marca_id", $this->marca_id);
        $stmt->bindParam(":precio_venta_unitario", $this->precio_venta_unitario);
        $stmt->bindParam(":stock_minimo", $this->stock_minimo);
        $stmt->bindParam(":stock_maximo", $this->stock_maximo);
        $stmt->bindParam(":unidad_medida", $this->unidad_medida);
        $stmt->bindParam(":estado", $this->estado);
        $stmt->bindParam(":subcategoria_id", $this->subcategoria_id);
        $stmt->bindParam(":presentacion_id", $this->presentacion_id);

        if ($stmt->execute()) {
            $this->producto_id = $this->conn->lastInsertId();
            return true;
        }
        // En caso de error, se espera que el controlador capture la PDOException.
        return false;
    }

    /**
     * Obtiene todos los productos con información relacionada.
     * @return PDOStatement Objeto PDOStatement para iterar sobre los resultados.
     */
    public function getAll() {
        $query = "SELECT
                    p.producto_id, p.codigo_producto, p.nombre_producto, p.descripcion,
                    p.marca_id, m.nombre_marca,
                    p.precio_venta_unitario, p.stock_minimo, p.stock_maximo,
                    p.unidad_medida, p.estado,
                    p.subcategoria_id, s.nombre_subcategoria,
                    p.presentacion_id, pr.nombre_presentacion,
                    p.fecha_creacion, p.fecha_modificacion
                FROM
                    " . $this->table_name . " p
                    LEFT JOIN Marcas m ON p.marca_id = m.marca_id
                    LEFT JOIN Subcategoria s ON p.subcategoria_id = s.id_subcategoria
                    LEFT JOIN Presentacion pr ON p.presentacion_id = pr.id_presentacion
                ORDER BY
                    p.nombre_producto ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Busca un producto por su código único.
     * @param string $codigo_producto_param El código del producto a buscar.
     * @return array|false Array asociativo con los datos del producto si se encuentra, o false si no.
     */
    public function buscarPorCodigo($codigo_producto_param) {
        $query = "SELECT
                    p.producto_id, p.codigo_producto, p.nombre_producto, p.descripcion,
                    p.marca_id, m.nombre_marca,
                    p.precio_venta_unitario, p.stock_minimo, p.stock_maximo,
                    p.unidad_medida, p.estado,
                    p.subcategoria_id, s.nombre_subcategoria,
                    p.presentacion_id, pr.nombre_presentacion,
                    p.fecha_creacion, p.fecha_modificacion
                FROM
                    " . $this->table_name . " p
                    LEFT JOIN Marcas m ON p.marca_id = m.marca_id
                    LEFT JOIN Subcategoria s ON p.subcategoria_id = s.id_subcategoria
                    LEFT JOIN Presentacion pr ON p.presentacion_id = pr.id_presentacion
                WHERE
                    p.codigo_producto = :codigo_producto
                LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $codigo_limpio = trim($codigo_producto_param);
        $stmt->bindParam(":codigo_producto", $codigo_limpio);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row; // $row será false si no se encuentra
    }

    /**
     * Busca un producto por su ID numérico (clave primaria).
     * @param int $id El ID del producto a buscar.
     * @return array|false Array asociativo con los datos del producto si se encuentra, o false si no.
     */
    public function buscarPorId($id) {
        $query = "SELECT
                    p.producto_id, p.codigo_producto, p.nombre_producto, p.descripcion,
                    p.marca_id, m.nombre_marca,
                    p.precio_venta_unitario, p.stock_minimo, p.stock_maximo,
                    p.unidad_medida, p.estado,
                    p.subcategoria_id, s.nombre_subcategoria,
                    p.presentacion_id, pr.nombre_presentacion,
                    p.fecha_creacion, p.fecha_modificacion
                FROM
                    " . $this->table_name . " p
                    LEFT JOIN Marcas m ON p.marca_id = m.marca_id
                    LEFT JOIN Subcategoria s ON p.subcategoria_id = s.id_subcategoria
                    LEFT JOIN Presentacion pr ON p.presentacion_id = pr.id_presentacion
                WHERE
                    p.producto_id = :producto_id
                LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $id_limpio = htmlspecialchars(strip_tags($id)); // ID numérico
        $stmt->bindParam(":producto_id", $id_limpio, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row; // $row será false si no se encuentra
    }

    /**
     * Actualiza un producto existente en la base de datos.
     * Los datos se toman de las propiedades públicas del objeto. $this->producto_id debe estar seteado.
     * No actualiza codigo_producto para mantener la simplicidad.
     * @return bool Verdadero si la actualización fue exitosa (al menos una fila afectada), falso en caso contrario.
     */
    public function actualizar() {
        $query = "UPDATE " . $this->table_name . " SET
                    nombre_producto = :nombre_producto,
                    descripcion = :descripcion,
                    marca_id = :marca_id,
                    precio_venta_unitario = :precio_venta_unitario,
                    stock_minimo = :stock_minimo,
                    stock_maximo = :stock_maximo,
                    unidad_medida = :unidad_medida,
                    estado = :estado,
                    subcategoria_id = :subcategoria_id,
                    presentacion_id = :presentacion_id
                    -- fecha_modificacion se actualiza automáticamente por la BD
                WHERE
                    producto_id = :producto_id";

        $stmt = $this->conn->prepare($query);

        // Sanitización
        $this->nombre_producto = htmlspecialchars(strip_tags(trim($this->nombre_producto)));
        $this->descripcion = !empty($this->descripcion) ? htmlspecialchars(strip_tags(trim($this->descripcion))) : null;
        $this->marca_id = htmlspecialchars(strip_tags($this->marca_id));
        $this->precio_venta_unitario = htmlspecialchars(strip_tags($this->precio_venta_unitario));
        $this->stock_minimo = htmlspecialchars(strip_tags($this->stock_minimo));
        $this->stock_maximo = !empty($this->stock_maximo) ? htmlspecialchars(strip_tags($this->stock_maximo)) : null;
        $this->unidad_medida = !empty($this->unidad_medida) ? htmlspecialchars(strip_tags(trim($this->unidad_medida))) : 'unidad';
        $this->estado = htmlspecialchars(strip_tags($this->estado));
        $this->subcategoria_id = !empty($this->subcategoria_id) ? htmlspecialchars(strip_tags($this->subcategoria_id)) : null;
        $this->presentacion_id = !empty($this->presentacion_id) ? htmlspecialchars(strip_tags($this->presentacion_id)) : null;
        $this->producto_id = htmlspecialchars(strip_tags($this->producto_id)); // ID para el WHERE

        // Vinculación de parámetros
        $stmt->bindParam(":nombre_producto", $this->nombre_producto);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":marca_id", $this->marca_id);
        $stmt->bindParam(":precio_venta_unitario", $this->precio_venta_unitario);
        $stmt->bindParam(":stock_minimo", $this->stock_minimo);
        $stmt->bindParam(":stock_maximo", $this->stock_maximo);
        $stmt->bindParam(":unidad_medida", $this->unidad_medida);
        $stmt->bindParam(":estado", $this->estado);
        $stmt->bindParam(":subcategoria_id", $this->subcategoria_id);
        $stmt->bindParam(":presentacion_id", $this->presentacion_id);
        $stmt->bindParam(":producto_id", $this->producto_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Se puede verificar $stmt->rowCount() para ver si realmente hubo cambios.
            // Para un PUT, si la ejecución es exitosa, se considera logrado.
            return true;
        }
        return false;
    }

    /**
     * Elimina un producto (soft delete cambiando el estado a 'descontinuado').
     * @param int $id El ID del producto a eliminar.
     * @return bool Verdadero si la actualización del estado fue exitosa.
     */
    public function eliminar($id) {
        // Se opta por un soft delete actualizando el estado.
        // La tabla Productos tiene ENUM('activo', 'descontinuado', 'suspendido') para 'estado'.
        $nuevo_estado = 'descontinuado';

        $query = "UPDATE " . $this->table_name . "
                  SET estado = :estado
                  WHERE producto_id = :producto_id";

        $stmt = $this->conn->prepare($query);

        $id_limpio = htmlspecialchars(strip_tags($id));
        $stmt->bindParam(":estado", $nuevo_estado);
        $stmt->bindParam(":producto_id", $id_limpio, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Devuelve true si se afectó alguna fila.
            return $stmt->rowCount() > 0;
        }
        return false;
    }
}
?>