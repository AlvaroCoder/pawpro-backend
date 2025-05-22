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
// -- Método para obtener las facturas
public function getAllHeaders() {
    $query = "SELECT
                fc.factura_compra_id,
                fc.numero_factura,
                fc.proveedor_id,
                prov.razon_social AS proveedor_razon_social,
                fc.fecha_compra,
                fc.fecha_registro,
                fc.usuario_id,
                u.nombre_usuario AS usuario_nombre_registro,
                fc.monto_total,
                fc.estado,
                fc.observaciones,
                fc.fecha_creacion AS factura_fecha_creacion,
                fc.fecha_modificacion AS factura_fecha_modificacion
            FROM
                " . $this->table_name . " fc
                LEFT JOIN Proveedores prov ON fc.proveedor_id = prov.proveedor_id
                LEFT JOIN Usuarios u ON fc.usuario_id = u.usuario_id
            ORDER BY
                fc.fecha_compra DESC, fc.factura_compra_id DESC";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();

    return $stmt; // Devolvemos el statement para que el controlador haga el fetch
}
// --- MÉTODO PARA OBTENER EL ENCABEZADO DE UNA FACTURA POR SU ID ---
    public function getHeaderById($id) {
        $query = "SELECT
                    fc.factura_compra_id,
                    fc.numero_factura,
                    fc.proveedor_id,
                    prov.razon_social AS proveedor_razon_social,
                    fc.fecha_compra,
                    fc.fecha_registro,
                    fc.usuario_id,
                    u.nombre_usuario AS usuario_nombre_registro,
                    fc.monto_total,
                    fc.estado,
                    fc.observaciones,
                    fc.fecha_creacion AS factura_fecha_creacion,
                    fc.fecha_modificacion AS factura_fecha_modificacion
                FROM
                    " . $this->table_name . " fc
                    LEFT JOIN Proveedores prov ON fc.proveedor_id = prov.proveedor_id
                    LEFT JOIN Usuarios u ON fc.usuario_id = u.usuario_id
                WHERE
                    fc.factura_compra_id = :id
                LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC); // Devuelve una fila o false si no se encuentra
    }

    // --- MÉTODO PARA OBTENER LOS DETALLES (ITEMS) DE UNA FACTURA POR SU ID ---
    public function getDetailsByInvoiceId($factura_compra_id) {
        $query = "SELECT
                    dfc.detalle_factura_id,
                    dfc.producto_id,
                    p.codigo_producto,
                    p.nombre_producto,
                    p.unidad_medida AS producto_unidad_medida,
                    m.nombre_marca AS producto_nombre_marca,
                    dfc.lote_id,
                    lp.codigo_lote,
                    lp.fecha_vencimiento AS lote_fecha_vencimiento,
                    lp.precio_compra_unitario AS lote_precio_compra_original, 
                    dfc.cantidad_comprada,
                    dfc.precio_compra_unitario_factura,
                    dfc.subtotal
                FROM
                    " . $this->table_name_details . " dfc
                    JOIN LotesProducto lp ON dfc.lote_id = lp.lote_id
                    JOIN Productos p ON dfc.producto_id = p.producto_id 
                    LEFT JOIN Marcas m ON p.marca_id = m.marca_id
                    -- Podrías añadir más JOINs si necesitas nombre de subcategoría o presentación del producto aquí
                WHERE
                    dfc.factura_compra_id = :factura_compra_id
                ORDER BY
                    p.nombre_producto ASC"; // O por dfc.detalle_factura_id

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":factura_compra_id", $factura_compra_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todas las filas de detalles
    }

    /**
     * Actualiza el estado y/u observaciones de una factura de compra.
     */
    public function actualizarEstado($id, $nuevoEstado, $nuevasObservaciones = null) {
        // Se construye la parte SET de la consulta dinámicamente.
        $setParts = [];
        $paramsToBind = [];

        if ($nuevoEstado !== null) {
            $setParts[] = "estado = :estado";
            $paramsToBind[':estado'] = htmlspecialchars(strip_tags($nuevoEstado));
        }

        // Solo se actualizan observaciones si se proporciona un valor (incluso una cadena vacía).
        // Si se quiere borrar observaciones, se debería pasar una cadena vacía, no null.
        // O ajustar la lógica para que null signifique SET observaciones = NULL.
        // Por simplicidad, si es null, no se incluye en el UPDATE de observaciones.
        if ($nuevasObservaciones !== null) {
            $setParts[] = "observaciones = :observaciones";
            $paramsToBind[':observaciones'] = htmlspecialchars(strip_tags(trim($nuevasObservaciones)));
        }
        
        // Se añade fecha_modificacion para que siempre se actualice.
        $setParts[] = "fecha_modificacion = CURRENT_TIMESTAMP";


        if (empty($setParts)) {
            // No hay nada que actualizar.
            return false;
        }

        $query = "UPDATE " . $this->table_name . " SET " . implode(', ', $setParts) . " WHERE factura_compra_id = :factura_compra_id";
        
        $stmt = $this->conn->prepare($query);
        
        // Se vinculan los parámetros dinámicamente.
        foreach ($paramsToBind as $param => $value) {
            $stmt->bindValue($param, $value);
        }
        $stmt->bindParam(":factura_compra_id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $stmt->rowCount() > 0; // Verdadero si se actualizó alguna fila.
        }
        return false; // La excepción PDO será capturada por el controlador.
    }


}
?>