<?php

class FacturaCompra {
    private $conn;
    private $table_name = "FacturasCompra"; // Nombre de la tabla de encabezados de factura

    // Propiedades del objeto FacturaCompra (corresponden a las columnas de la tabla)
    public $factura_compra_id;
    public $numero_factura;
    public $proveedor_id;
    public $fecha_compra;
    public $usuario_id;
    public $monto_total; 
    public $estado;
    public $observaciones;

    // Constructor que recibe la conexión a la base de datos
    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    // Método para crear un nuevo encabezado de factura de compra
    public function crearEncabezado() {
        $query = "INSERT INTO " . $this->table_name . " SET
                    numero_factura=:numero_factura,
                    proveedor_id=:proveedor_id,
                    fecha_compra=:fecha_compra,
                    usuario_id=:usuario_id,
                    observaciones=:observaciones,
                    estado=:estado"; // Asumimos un estado por defecto, ej: 'registrada'

        $stmt = $this->conn->prepare($query);

        // Limpiar datos (sanitizar)
        $this->numero_factura = htmlspecialchars(strip_tags($this->numero_factura));
        $this->proveedor_id = htmlspecialchars(strip_tags($this->proveedor_id));
        $this->fecha_compra = htmlspecialchars(strip_tags($this->fecha_compra));
        $this->usuario_id = htmlspecialchars(strip_tags($this->usuario_id));
        $this->observaciones = htmlspecialchars(strip_tags($this->observaciones));
        $this->estado = htmlspecialchars(strip_tags($this->estado)); 

        // Vincular parámetros
        $stmt->bindParam(":numero_factura", $this->numero_factura);
        $stmt->bindParam(":proveedor_id", $this->proveedor_id);
        $stmt->bindParam(":fecha_compra", $this->fecha_compra);
        $stmt->bindParam(":usuario_id", $this->usuario_id);
        $stmt->bindParam(":observaciones", $this->observaciones);
        $stmt->bindParam(":estado", $this->estado);

        if ($stmt->execute()) {
            // Obtener el ID del último registro insertado
            $this->factura_compra_id = $this->conn->lastInsertId();
            return true;
        }

        // Imprimir error si algo sale mal (útil para depuración)
        // printf("Error: %s.\n", $stmt->error); 
        return false;
    }


}
?>