<?php
// Incluimos los archivos necesarios
require_once __DIR__ . '/../../config/database.php'; // Para la conexión a la BD
require_once __DIR__ . '/../models/FacturaCompra.php'; // Modelo FacturaCompra

// --- INICIO DEPURACIÓN PARA EL MODELO ---
$rutaModeloFacturaCompra = __DIR__ . '/../models/FacturaCompra.php';
echo "Intentando incluir: " . $rutaModeloFacturaCompra . "<br>"; // Imprime la ruta

if (file_exists($rutaModeloFacturaCompra)) {
    echo "El archivo FacturaCompra.php SÍ existe en la ruta.<br>";
    require_once $rutaModeloFacturaCompra; // Modelo FacturaCompra
    echo "FacturaCompra.php debería haber sido incluido.<br>";
} else {
    echo "¡ERROR CRÍTICO! El archivo FacturaCompra.php NO existe en la ruta: " . $rutaModeloFacturaCompra . "<br>";
    die("Deteniendo ejecución porque el archivo del modelo no se encuentra.");
}

// Verificar si la clase existe DESPUÉS de incluir el archivo
if (class_exists('FacturaCompra')) {
    echo "La clase FacturaCompra SÍ existe después del include.<br>";
} else {
    echo "¡ERROR! La clase FacturaCompra NO existe después del include.<br>";
    // die("Deteniendo ejecución porque la clase FacturaCompra no está definida."); // Puedes descomentar esto para forzar parada si no existe
}
// --- FIN DEPURACIÓN ---

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

        // Asignar datos del input al modelo para el encabezado
        $facturaCompraModel->numero_factura = $inputData->numero_factura;
        $facturaCompraModel->proveedor_id = $inputData->proveedor_id;
        $facturaCompraModel->fecha_compra = $inputData->fecha_compra;
        $facturaCompraModel->usuario_id = $inputData->usuario_id;
        $facturaCompraModel->observaciones = isset($inputData->observaciones) ? $inputData->observaciones : null;
        $facturaCompraModel->estado = 'registrada'; // Estado por defecto al crear

        try {
            // Iniciar transacción
            $conn->beginTransaction();

            // Crear el encabezado de la factura
            if ($facturaCompraModel->crearEncabezado()) {
                $factura_compra_id_generado = $facturaCompraModel->factura_compra_id;

                // AQUÍ IRÁ LA LÓGICA PARA PROCESAR LOS ITEMS (PRODUCTOS Y LOTES)
                // Por ahora, si el encabezado se crea, hacemos commit y damos éxito.

                $conn->commit();
                http_response_code(201); // 201 Created
                echo json_encode([
                    'message' => 'Encabezado de factura de compra registrado exitosamente (items pendientes).',
                    'factura_compra_id' => $factura_compra_id_generado
                ]);

            } else {
                // Si crearEncabezado devuelve false (sin lanzar excepción)
                $conn->rollBack();
                http_response_code(500);
                echo json_encode(['message' => 'No se pudo crear el encabezado de la factura.']);
            }

        } catch (PDOException $e) {
            // Si ocurre una excepción PDO, hacemos rollback
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            http_response_code(500);
            // En desarrollo, $e->getMessage() es útil. En producción, un mensaje genérico.
            echo json_encode(['message' => 'Error en la base de datos al registrar la factura: ' . $e->getMessage()]);
        } catch (Exception $e) {
            // Captura otras excepciones generales
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            http_response_code(500);
            echo json_encode(['message' => 'Error general al registrar la factura: ' . $e->getMessage()]);
        }
    }
}
?>>