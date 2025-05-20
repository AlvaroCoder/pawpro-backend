<?php

class Producto {
    private $conn;
    private $table_name = "Productos";

    // Propiedades del objeto Producto
    public $producto_id;
    public $codigo_producto;
    public $nombre_producto;
    public $descripcion;
    public $marca_id;
    public $precio_venta_unitario;
    public $stock_minimo;
    public $stock_maximo;       // Puede ser NULL
    public $unidad_medida;
    public $estado;
    public $subcategoria_id;    // Puede ser NULL
    public $presentacion_id;    // Puede ser NULL
    // fecha_creacion y fecha_modificacion son automáticas por la DB

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    // Método para crear un nuevo producto POST
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

        $stmt = $this->conn->prepare($query);

        // Sanitizar datos (básico)
        $this->codigo_producto = htmlspecialchars(strip_tags($this->codigo_producto));
        $this->nombre_producto = htmlspecialchars(strip_tags($this->nombre_producto));
        $this->descripcion = !empty($this->descripcion) ? htmlspecialchars(strip_tags($this->descripcion)) : null;
        $this->marca_id = htmlspecialchars(strip_tags($this->marca_id));
        $this->precio_venta_unitario = htmlspecialchars(strip_tags($this->precio_venta_unitario));
        $this->stock_minimo = htmlspecialchars(strip_tags($this->stock_minimo));
        $this->stock_maximo = !empty($this->stock_maximo) ? htmlspecialchars(strip_tags($this->stock_maximo)) : null;
        $this->unidad_medida = !empty($this->unidad_medida) ? htmlspecialchars(strip_tags($this->unidad_medida)) : 'unidad';
        $this->estado = htmlspecialchars(strip_tags($this->estado));
        $this->subcategoria_id = !empty($this->subcategoria_id) ? htmlspecialchars(strip_tags($this->subcategoria_id)) : null;
        $this->presentacion_id = !empty($this->presentacion_id) ? htmlspecialchars(strip_tags($this->presentacion_id)) : null;


        // Vincular parámetros
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
        // Si falla, la excepción PDO será capturada por el controlador
        return false;
    }

    // Método para listar productos GET
    public function getAll() {
        $query = "SELECT
                    p.producto_id,
                    p.codigo_producto,
                    p.nombre_producto,
                    p.descripcion,
                    p.marca_id,
                    m.nombre_marca,
                    p.precio_venta_unitario,
                    p.stock_minimo,
                    p.stock_maximo,
                    p.unidad_medida,
                    p.estado,
                    p.subcategoria_id,
                    s.nombre_subcategoria,
                    p.presentacion_id,
                    pr.nombre_presentacion,
                    p.fecha_creacion,
                    p.fecha_modificacion
                FROM
                    " . $this->table_name . " p
                    LEFT JOIN Marcas m ON p.marca_id = m.marca_id
                    LEFT JOIN Subcategoria s ON p.subcategoria_id = s.id_subcategoria
                    LEFT JOIN Presentacion pr ON p.presentacion_id = pr.id_presentacion
                ORDER BY
                    p.nombre_producto ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt; // Devolvemos el statement para que el controlador haga el fetch (Obtener, recuperar datos del servidor)
    }
}
?>