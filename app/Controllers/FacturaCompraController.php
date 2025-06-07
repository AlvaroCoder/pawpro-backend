<?php
// Incluimos los archivos necesarios
require_once __DIR__ . '/../../config/database.php'; // Para la conexión a la BD
require_once __DIR__ . '/../models/FacturaCompra.php'; // Modelo FacturaCompra
require_once __DIR__ . '/../models/Producto.php'; 
require_once __DIR__ . '/../models/LoteProducto.php'; 

class FacturaCompraController {
    public function registrarNuevaFactura() {
        // 1. Obtener los datos del cuerpo de la solicitud (payload JSON)
        $inputData = json_decode(file_get_contents("php://input"));

        // 2. Validación básica de datos
        if (is_null($inputData)) {
            http_response_code(400);
            echo json_encode(['message' => 'Error: El JSON enviado es inválido.']);
            return;
        }

        if (!isset($inputData->proveedor_id) || !isset($inputData->numero_factura) || !isset($inputData->fecha_compra) || !isset($inputData->usuario_id) || !isset($inputData->items) || !is_array($inputData->items) || empty($inputData->items)) {
            http_response_code(400);
            echo json_encode([
                'message' => 'Datos incompletos o mal formados. Se requiere proveedor_id, numero_factura, fecha_compra, usuario_id e items (array).',
                'received_data_keys' => array_keys(get_object_vars($inputData))
            ]);
            return;
        }

        // --- Inicio de la Lógica de Base de Datos ---
        $database = new Database();
        $conn = $database->getConnection();

        if (!$conn) {
            http_response_code(500);
            echo json_encode(['message' => 'Error interno del servidor: No se pudo conectar a la base de datos.']);
            return;
        }

        $facturaCompraModel = new FacturaCompra($conn);
        $loteProductoModel = new LoteProducto($conn);
        // Asignar datos del input al modelo para el encabezado
        $facturaCompraModel->numero_factura = $inputData->numero_factura;
        $facturaCompraModel->proveedor_id = $inputData->proveedor_id;
        $facturaCompraModel->fecha_compra = $inputData->fecha_compra;
        $facturaCompraModel->usuario_id = $inputData->usuario_id;
        $facturaCompraModel->observaciones = isset($inputData->observaciones) ? $inputData->observaciones : null;
        $facturaCompraModel->estado = 'registrada'; // Estado por defecto al crear

        try {
            $conn->beginTransaction();

            if ($facturaCompraModel->crearEncabezado()) {
                $factura_compra_id_generado = $facturaCompraModel->factura_compra_id;

                // --- INICIO: PROCESAR ITEMS DE LA FACTURA ---
                foreach ($inputData->items as $item) {
                    // Validación básica del item
                    if (!isset($item->producto_id) || !isset($item->codigo_lote) || !isset($item->cantidad_comprada) || !isset($item->precio_compra_unitario_factura) || !isset($item->fecha_vencimiento)) {
                        throw new Exception("Datos incompletos para uno de los items de la factura.");
                    }

                    // 1. Crear el Lote
                    $loteProductoModel->producto_id = $item->producto_id;
                    $loteProductoModel->codigo_lote = $item->codigo_lote;
                    $loteProductoModel->cantidad_actual = $item->cantidad_comprada; // La cantidad comprada es la cantidad actual del nuevo lote
                    $loteProductoModel->fecha_vencimiento = $item->fecha_vencimiento;
                    $loteProductoModel->precio_compra_unitario = $item->precio_compra_unitario_factura;
                    $loteProductoModel->fecha_ingreso = $inputData->fecha_compra; // Usamos la fecha de compra como fecha de ingreso del lote

                    if (!$loteProductoModel->crear()) {
                        throw new Exception("No se pudo crear el lote para el producto ID: " . $item->producto_id);
                    }
                    $lote_id_generado = $loteProductoModel->lote_id;

                    // 2. Crear el Detalle de la Factura de Compra
                    if (!$facturaCompraModel->crearDetalle(
                        $factura_compra_id_generado,
                        $lote_id_generado,
                        $item->producto_id,
                        $item->cantidad_comprada,
                        $item->precio_compra_unitario_factura
                    )) {
                        throw new Exception("No se pudo crear el detalle de factura para el producto ID: " . $item->producto_id);
                    }
                }
                // --- FIN: PROCESAR ITEMS DE LA FACTURA ---

                $conn->commit();
                http_response_code(201); // Created
                echo json_encode([
                    'message' => 'Factura de compra y sus detalles registrados exitosamente.',
                    'factura_compra_id' => $factura_compra_id_generado
                ]);

            } else {
                $conn->rollBack(); // Aunque crearEncabezado podría no necesitarlo si lanza excepción
                http_response_code(500);
                echo json_encode(['message' => 'No se pudo crear el encabezado de la factura.']);
            }

        } catch (PDOException $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            http_response_code(500);
            echo json_encode(['message' => 'Error en la base de datos al registrar la factura: ' . $e->getMessage()]);
        } catch (Exception $e) { // Captura las excepciones personalizadas de validación de items
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            http_response_code(400); // Bad request si los datos del item son malos
            echo json_encode(['message' => 'Error al procesar los items de la factura: ' . $e->getMessage()]);
        }
    }
/**
     * Lista todos los encabezados de las facturas de compra.
     * Corresponde a GET /api/facturas-compra
     */
    public function listarFacturasCompra() {
        $database = new Database();
        $conn = $database->getConnection();

        if (!$conn) {
            http_response_code(503);
            echo json_encode(['message' => 'No se pudo conectar a la base de datos.']);
            return;
        }

        $facturaCompraModel = new FacturaCompra($conn);
        $stmt = $facturaCompraModel->getAllHeaders();
        $num_rows = $stmt->rowCount();

        if ($num_rows > 0) {
            $facturas_arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
            http_response_code(200); // OK
            echo json_encode($facturas_arr);
        } else {
            http_response_code(404); // Not Found
            echo json_encode(["message" => "No se encontraron facturas de compra."]);
        }
    }

    /**
     * Obtiene una factura de compra específica con todos sus detalles por ID de ruta.
     * Corresponde a GET /api/facturas-compra/{id}
     * @param int $id El ID de la factura a obtener.
     */
    public function obtenerFacturaPorId($id) {
        $factura_id_sanitizada = filter_var($id, FILTER_VALIDATE_INT);
        if ($factura_id_sanitizada === false || $factura_id_sanitizada <= 0) {
            http_response_code(400);
            echo json_encode(['message' => 'ID de factura inválido.']);
            return;
        }

        $database = new Database();
        $conn = $database->getConnection();

        if (!$conn) {
            http_response_code(503);
            echo json_encode(['message' => 'No se pudo conectar a la base de datos.']);
            return;
        }

        $facturaCompraModel = new FacturaCompra($conn);
        $encabezadoFactura = $facturaCompraModel->getHeaderById($factura_id_sanitizada);

        if (!$encabezadoFactura) {
            http_response_code(404);
            echo json_encode(['message' => 'Factura de compra no encontrada con el ID: ' . $factura_id_sanitizada]);
            return;
        }

        $detallesFactura = $facturaCompraModel->getDetailsByInvoiceId($factura_id_sanitizada);
        $respuestaCompleta = $encabezadoFactura;
        $respuestaCompleta['items'] = $detallesFactura ? $detallesFactura : [];

        http_response_code(200); // OK
        echo json_encode($respuestaCompleta);
    }


    /**
     * Actualiza el estado y/u observaciones de una factura de compra.
     * Corresponde a PUT /api/facturas-compra/{id} o PATCH /api/facturas-compra/{id}
     * @param int $id El ID de la factura a actualizar.
     */
    public function actualizarEstadoFactura($id) {
        $factura_id_sanitizada = filter_var($id, FILTER_VALIDATE_INT);
        if ($factura_id_sanitizada === false || $factura_id_sanitizada <= 0) {
            http_response_code(400);
            echo json_encode(['message' => 'ID de factura inválido para actualizar.']);
            return;
        }

        $inputData = json_decode(file_get_contents("php://input"));

        // Se espera 'estado' y opcionalmente 'observaciones' en el cuerpo JSON.
        if (is_null($inputData) || !isset($inputData->estado) || empty(trim($inputData->estado))) {
            http_response_code(400);
            echo json_encode(['message' => 'Datos incompletos. Se requiere al menos el campo "estado".']);
            return;
        }

        // Validación del valor de estado (ejemplo básico)
        $estadosPermitidos = ['registrada', 'pagada', 'anulada'];
        if (!in_array($inputData->estado, $estadosPermitidos)) {
            http_response_code(400);
            echo json_encode(['message' => 'Valor de "estado" inválido. Permitidos: ' . implode(', ', $estadosPermitidos)]);
            return;
        }

        $database = new Database();
        $conn = $database->getConnection();
        if (!$conn) { http_response_code(503); echo json_encode(['message' => 'No se pudo conectar a la base de datos.']); return; }

        $facturaCompraModel = new FacturaCompra($conn);

        // Se verifica si la factura existe antes de intentar actualizar.
        if (!$facturaCompraModel->getHeaderById($factura_id_sanitizada)) {
             http_response_code(404);
             echo json_encode(['message' => 'Factura de compra con ID ' . $factura_id_sanitizada . ' no encontrada.']);
             return;
        }
        
        $nuevasObservaciones = isset($inputData->observaciones) ? trim($inputData->observaciones) : null;

        try {
            if ($facturaCompraModel->actualizarEstado($factura_id_sanitizada, trim($inputData->estado), $nuevasObservaciones)) {
                http_response_code(200); // OK
                echo json_encode(['message' => 'Estado de la factura de compra actualizado exitosamente.']);
            } else {
                // Esto podría ser si el estado ya era el mismo y no se afectaron filas.
                http_response_code(304); // Not Modified
                echo json_encode(['message' => 'No se actualizó el estado de la factura (posiblemente ya tenía el estado enviado o el ID no existe).']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en la base de datos al actualizar estado de la factura.', 'error_detail' => $e->getMessage()]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Error general al actualizar estado de la factura.', 'error_detail' => $e->getMessage()]);
        }
    }


    /**
     * Anula una factura de compra (soft delete).
     * Corresponde a DELETE /api/facturas-compra/{id}
     * @param int $id El ID de la factura a anular.
     */
    public function anularFacturaCompra($id) {
        $factura_id_sanitizada = filter_var($id, FILTER_VALIDATE_INT);
        if ($factura_id_sanitizada === false || $factura_id_sanitizada <= 0) {
            http_response_code(400);
            echo json_encode(['message' => 'ID de factura inválido para anular.']);
            return;
        }

        // Opcional: Permitir enviar observaciones para la anulación en el cuerpo JSON.
        $inputData = json_decode(file_get_contents("php://input"));
        $observacionesAnulacion = "Factura anulada por el usuario."; // Mensaje por defecto.
        if (!is_null($inputData) && isset($inputData->observaciones_anulacion) && !empty(trim($inputData->observaciones_anulacion))) {
            $observacionesAnulacion = trim($inputData->observaciones_anulacion);
        }


        $database = new Database();
        $conn = $database->getConnection();
        if (!$conn) { http_response_code(503); echo json_encode(['message' => 'No se pudo conectar a la base de datos.']); return; }

        $facturaCompraModel = new FacturaCompra($conn);

        // Se verifica si la factura existe y no está ya anulada.
        $facturaExistente = $facturaCompraModel->getHeaderById($factura_id_sanitizada);
        if (!$facturaExistente) {
             http_response_code(404);
             echo json_encode(['message' => 'Factura de compra con ID ' . $factura_id_sanitizada . ' no encontrada.']);
             return;
        }
        if ($facturaExistente['estado'] === 'anulada') {
            http_response_code(409); // Conflict
            echo json_encode(['message' => 'La factura de compra con ID ' . $factura_id_sanitizada . ' ya se encuentra anulada.']);
            return;
        }

        // NOTA IMPORTANTE: Anular una factura de compra podría implicar la reversión de movimientos de stock
        // de los lotes asociados. Esa lógica es compleja y no se incluye aquí, solo se cambia el estado.

        try {
            if ($facturaCompraModel->actualizarEstado($factura_id_sanitizada, 'anulada', $observacionesAnulacion)) {
                http_response_code(200); // OK
                echo json_encode(['message' => 'Factura de compra anulada exitosamente.']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'No se pudo anular la factura de compra.']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en la base de datos al anular la factura.', 'error_detail' => $e->getMessage()]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Error general al anular la factura.', 'error_detail' => $e->getMessage()]);
        }
    }
}

?>