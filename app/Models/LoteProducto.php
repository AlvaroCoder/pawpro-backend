<?php

class LoteProducto {
    private $conn;
    private $table_name = "LotesProducto";

    // Propiedades del objeto LoteProducto
    public $lote_id;
    public $producto_id; // Clave foránea a Productos
    public $codigo_lote;
    public $cantidad_actual; // En una compra, esta será la cantidad_comprada
    public $fecha_vencimiento;
    public $precio_compra_unitario; // Precio al que se compró este lote
    public $fecha_ingreso;
    // fecha_creacion y fecha_modificacion son automáticas por la DB

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    // Método para crear un nuevo lote
    public function crear() {
        $query = "INSERT INTO " . $this->table_name . " SET
                    producto_id=:producto_id,
                    codigo_lote=:codigo_lote,
                    cantidad_actual=:cantidad_actual,
                    fecha_vencimiento=:fecha_vencimiento,
                    precio_compra_unitario=:precio_compra_unitario,
                    fecha_ingreso=:fecha_ingreso";

        $stmt = $this->conn->prepare($query);

        // Sanitizar datos
        $this->producto_id = htmlspecialchars(strip_tags($this->producto_id));
        $this->codigo_lote = htmlspecialchars(strip_tags($this->codigo_lote));
        $this->cantidad_actual = htmlspecialchars(strip_tags($this->cantidad_actual));
        $this->fecha_vencimiento = htmlspecialchars(strip_tags($this->fecha_vencimiento));
        $this->precio_compra_unitario = htmlspecialchars(strip_tags($this->precio_compra_unitario));
        $this->fecha_ingreso = htmlspecialchars(strip_tags($this->fecha_ingreso));

        // Vincular parámetros
        $stmt->bindParam(":producto_id", $this->producto_id);
        $stmt->bindParam(":codigo_lote", $this->codigo_lote);
        $stmt->bindParam(":cantidad_actual", $this->cantidad_actual);
        $stmt->bindParam(":fecha_vencimiento", $this->fecha_vencimiento);
        $stmt->bindParam(":precio_compra_unitario", $this->precio_compra_unitario);
        $stmt->bindParam(":fecha_ingreso", $this->fecha_ingreso);

        if ($stmt->execute()) {
            $this->lote_id = $this->conn->lastInsertId();
            return true;
        }
        return false; // La excepción PDO será capturada por el controlador
    }
}
?>