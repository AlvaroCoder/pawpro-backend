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
}
?>