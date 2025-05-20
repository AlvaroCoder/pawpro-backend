<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Producto.php';

class ProductoController {

    public function crearNuevoProducto() {
        $inputData = json_decode(file_get_contents("php://input"));

        // Validación básica de datos (campos obligatorios)
        if (is_null($inputData) ||
            !isset($inputData->codigo_producto) || empty(trim($inputData->codigo_producto)) ||
            !isset($inputData->nombre_producto) || empty(trim($inputData->nombre_producto)) ||
            !isset($inputData->marca_id) || !is_numeric($inputData->marca_id) ||
            !isset($inputData->precio_venta_unitario) || !is_numeric($inputData->precio_venta_unitario) ||
            !isset($inputData->stock_minimo) || !is_numeric($inputData->stock_minimo)
            // subcategoria_id y presentacion_id son opcionales en la BD
        ) {
            http_response_code(400); // Bad Request
            echo json_encode(['message' => 'Datos incompletos o mal formados para crear el producto. Campos requeridos: codigo_producto, nombre_producto, marca_id, precio_venta_unitario, stock_minimo.']);
            return;
        }

        $database = new Database();
        $conn = $database->getConnection();

        if (!$conn) {
            http_response_code(503); // Service Unavailable
            echo json_encode(['message' => 'No se pudo conectar a la base de datos.']);
            return;
        }

        $productoModel = new Producto($conn);

        // Asignar datos del input al modelo
        $productoModel->codigo_producto = trim($inputData->codigo_producto);
        $productoModel->nombre_producto = trim($inputData->nombre_producto);
        $productoModel->descripcion = isset($inputData->descripcion) ? trim($inputData->descripcion) : null;
        $productoModel->marca_id = $inputData->marca_id;
        $productoModel->precio_venta_unitario = $inputData->precio_venta_unitario;
        $productoModel->stock_minimo = $inputData->stock_minimo;
        $productoModel->stock_maximo = isset($inputData->stock_maximo) && is_numeric($inputData->stock_maximo) ? $inputData->stock_maximo : null;
        $productoModel->unidad_medida = isset($inputData->unidad_medida) ? trim($inputData->unidad_medida) : 'unidad';
        $productoModel->estado = isset($inputData->estado) ? trim($inputData->estado) : 'activo';
        $productoModel->subcategoria_id = isset($inputData->subcategoria_id) && is_numeric($inputData->subcategoria_id) ? $inputData->subcategoria_id : null;
        $productoModel->presentacion_id = isset($inputData->presentacion_id) && is_numeric($inputData->presentacion_id) ? $inputData->presentacion_id : null;

        try {
            if ($productoModel->crear()) {
                http_response_code(201); // Created
                echo json_encode([
                    'message' => 'Producto creado exitosamente.',
                    'producto_id' => $productoModel->producto_id,
                    'codigo_producto' => $productoModel->codigo_producto,
                    'nombre_producto' => $productoModel->nombre_producto
                ]);
            } else {
                // Este 'else' podría no alcanzarse si las excepciones PDO están activas
                // y el error es a nivel de base de datos.
                http_response_code(500);
                echo json_encode(['message' => 'No se pudo crear el producto (error genérico del modelo).']);
            }
        } catch (PDOException $e) {
            // Manejar errores específicos de PDO (ej. violación de UNIQUE constraint)
            if ($e->getCode() == '23000') { // Código SQLSTATE para violación de integridad (incluye UNIQUE)
                http_response_code(409); // Conflict
                echo json_encode(['message' => 'Error al crear el producto: El código de producto ya existe o hay una violación de clave foránea. Detalles: ' . $e->getMessage()]);
            } else {
                http_response_code(500); // Internal Server Error
                echo json_encode(['message' => 'Error en la base de datos al crear el producto: ' . $e->getMessage()]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Error general al crear el producto: ' . $e->getMessage()]);
        }
    }
     // --- MÉTODO PARA LISTAR PRODUCTOS ---
    public function listarProductos() {
        $database = new Database();
        $conn = $database->getConnection();

        if (!$conn) {
            http_response_code(503); // Service Unavailable
            echo json_encode(['message' => 'No se pudo conectar a la base de datos.']);
            return;
        }

        $productoModel = new Producto($conn);
        $stmt = $productoModel->getAll();
        $num_rows = $stmt->rowCount();

        if ($num_rows > 0) {
            $productos_arr = array();
            // $productos_arr["records"] = array(); // Opcional, para anidar bajo una clave "records"

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row); // Extrae las columnas a variables ($producto_id, $nombre_producto, etc.)
                $producto_item = array(
                    "producto_id" => $producto_id,
                    "codigo_producto" => $codigo_producto,
                    "nombre_producto" => $nombre_producto,
                    "descripcion" => $descripcion,
                    "marca_id" => $marca_id,
                    "nombre_marca" => $nombre_marca,
                    "precio_venta_unitario" => $precio_venta_unitario,
                    "stock_minimo" => $stock_minimo,
                    "stock_maximo" => $stock_maximo,
                    "unidad_medida" => $unidad_medida,
                    "estado" => $estado,
                    "subcategoria_id" => $subcategoria_id,
                    "nombre_subcategoria" => $nombre_subcategoria,
                    "presentacion_id" => $presentacion_id,
                    "nombre_presentacion" => $nombre_presentacion,
                    "fecha_creacion" => $fecha_creacion,
                    "fecha_modificacion" => $fecha_modificacion
                );
                // array_push($productos_arr["records"], $producto_item); // Si usas la clave "records"
                array_push($productos_arr, $producto_item); // Directamente al array principal
            }
            http_response_code(200); // OK
            echo json_encode($productos_arr);
        } else {
            http_response_code(404); // Not Found
            echo json_encode(["message" => "No se encontraron productos."]);
        }
    }
    // --- FIN MÉTODO NUEVO ---
}
?>