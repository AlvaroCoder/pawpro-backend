<?php

class FacturaCompra {
    private $conn;
    private $table_name = "FacturasCompra";
    private $table_name_details = "DetallesFacturaCompra"; // Nueva propiedad para la tabla de detalles

    public $factura_compra_id;
    public $numero_factura;
    public $proveedor_id;
    public $fecha_compra;
    public $usuario_id;
    public $monto_total; 
    public $estado;
    public $observaciones;

    // public $detalle_factura_id; // Es auto_increment
    // public $factura_compra_id; // Ya la tenemos como propiedad de la clase
    public $lote_id;
    public $producto_id_detalle; 
    public $cantidad_comprada;
    public $precio_compra_unitario_factura;
    public $subtotal_detalle;


    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    public function crearEncabezado() {

        $query = "INSERT INTO " . $this->table_name . " SET
                    numero_factura=:numero_factura,
                    proveedor_id=:proveedor_id,
                    fecha_compra=:fecha_compra,
                    usuario_id=:usuario_id,
                    observaciones=:observaciones,
                    estado=:estado"; 

        $stmt = $this->conn->prepare($query);

        $this->numero_factura = htmlspecialchars(strip_tags($this->numero_factura));
        $this->proveedor_id = htmlspecialchars(strip_tags($this->proveedor_id));
        $this->fecha_compra = htmlspecialchars(strip_tags($this->fecha_compra));
        $this->usuario_id = htmlspecialchars(strip_tags($this->usuario_id));
        $this->observaciones = htmlspecialchars(strip_tags($this->observaciones));
        $this->estado = htmlspecialchars(strip_tags($this->estado)); 

        $stmt->bindParam(":numero_factura", $this->numero_factura);
        $stmt->bindParam(":proveedor_id", $this->proveedor_id);
        $stmt->bindParam(":fecha_compra", $this->fecha_compra);
        $stmt->bindParam(":usuario_id", $this->usuario_id);
        $stmt->bindParam(":observaciones", $this->observaciones);
        $stmt->bindParam(":estado", $this->estado);

        if ($stmt->execute()) {
            $this->factura_compra_id = $this->conn->lastInsertId();
            return true;
        } else { 
            $errorInfo = $stmt->errorInfo();
            error_log("Error PDO (no excepción) al crear encabezado de factura: " . $errorInfo[2]);
            return false;
        }
    }


    // --- MÉTODO NUEVO PARA CREAR UN DETALLE DE FACTURA ---
    public function crearDetalle($factura_id, $lote_id_param, $producto_id_param, $cantidad_param, $precio_compra_param) {
        $query = "INSERT INTO " . $this->table_name_details . " SET
                    factura_compra_id=:factura_compra_id,
                    lote_id=:lote_id,
                    producto_id=:producto_id,
                    cantidad_comprada=:cantidad_comprada,
                    precio_compra_unitario_factura=:precio_compra_unitario_factura,
                    subtotal=:subtotal";

        $stmt = $this->conn->prepare($query);

        // Sanitizar datos
        $factura_id_clean = htmlspecialchars(strip_tags($factura_id));
        $lote_id_clean = htmlspecialchars(strip_tags($lote_id_param));
        $producto_id_clean = htmlspecialchars(strip_tags($producto_id_param));
        $cantidad_clean = htmlspecialchars(strip_tags($cantidad_param));
        $precio_compra_clean = htmlspecialchars(strip_tags($precio_compra_param));
        
        // Calcular subtotal
        $subtotal_calculado = $cantidad_clean * $precio_compra_clean;

        // Vincular parámetros
        $stmt->bindParam(":factura_compra_id", $factura_id_clean);
        $stmt->bindParam(":lote_id", $lote_id_clean);
        $stmt->bindParam(":producto_id", $producto_id_clean);
        $stmt->bindParam(":cantidad_comprada", $cantidad_clean);
        $stmt->bindParam(":precio_compra_unitario_factura", $precio_compra_clean);
        $stmt->bindParam(":subtotal", $subtotal_calculado);

        if ($stmt->execute()) {
            return true;
        }
        return false; // La excepción PDO será capturada por el controlador
    }
    // --- FIN MÉTODO NUEVO ---
}
?>