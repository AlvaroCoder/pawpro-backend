<?php

class FacturaCompra {
    private $conn;
    private $table_name = "FacturasCompra"; // Nombre de la tabla de encabezados de factura
    private $table_name_details = "DetallesFacturaCompra";


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
    // --- MÉTODO PARA CREAR UN DETALLE DE FACTURA ---
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
    $subtotal_calculado = floatval($cantidad_clean) * floatval($precio_compra_clean); // Asegurar que sean números

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
    // Si falla, la excepción PDO será capturada por el controlador si está configurado así.
    // Opcionalmente, loguear error aquí:
    // $errorInfo = $stmt->errorInfo();
    // error_log("Error al crear detalle de factura: " . $errorInfo[2]);
    return false;
}


}
?>