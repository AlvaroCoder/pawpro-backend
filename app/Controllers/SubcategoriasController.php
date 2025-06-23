<?php
require_once 'app/models/Subcategoria.php'; 
require_once 'config/database.php'; // Para la conexión a la BD

class SubcategoriaController{

    /**
     * Lista todas las subcategorías.
     * Corresponde a GET /subcategorias
     */
    public function listarSubcategorias(){
        $database = new Database();
        $conn = $database->getConnection();
        if (!$conn) {
            http_response_code(503); // Service Unavailable
            echo json_encode(['message' => 'No se pudo conectar a la base de datos.']);
            return;
        }

        $subcategoriaModelo = new Subcategoria($conn);
        $response = $subcategoriaModelo->obtenerSubcategorias();
        http_response_code(200); // OK
        echo json_encode($response);
    }

    /**
     * Crea una nueva subcategoría.
     * Corresponde a POST /subcategorias
     */
    public function crearSubcategoria(){
        $database = new Database();
        $conn = $database->getConnection();
        if (!$conn) {
            http_response_code(503); // Service Unavailable
            echo json_encode(['message' => 'No se pudo conectar a la base de datos.']);
            return;
        }

        $subcategoriaModelo = new Subcategoria($conn);
        $data = json_decode(file_get_contents('php://input'), true);

        $idCategoria = filter_var($data['idCategoria'] ?? null, FILTER_VALIDATE_INT);
        $nombreSubcategoria = trim($data['nombreSubcategoria'] ?? '');
        $descripcion = trim($data['descripcion'] ?? '');

        // Validaciones
        if ($idCategoria === false || $idCategoria <= 0) {
            http_response_code(400); // Bad Request
            echo json_encode([
                'success' => false,
                'message' => 'ID de categoría inválido para la subcategoría.'
            ]);
            return;
        }
        if (empty($nombreSubcategoria) || empty($descripcion)) {
            http_response_code(400); // Bad Request
            echo json_encode([
                'success' => false,
                'message' => 'Completa la información (nombre y descripción de la subcategoría).'
            ]);
            return;
        }

        try {
            $response = $subcategoriaModelo->crearSubcategoria($idCategoria, $nombreSubcategoria, $descripcion);
            if (!$response) {
                http_response_code(500); // Internal Server Error
                echo json_encode([
                    'success'=>false,
                    'message'=>'Error al crear la subcategoría en la base de datos.'
                ]);
                return;
            }

            http_response_code(201); // Created
            echo json_encode([
                'success'=>true,
                'message'=>'Subcategoría guardada correctamente',
                'data'=>['id_subcategoria' => $subcategoriaModelo->id_subcategoria, 'nombre_subcategoria' => $nombreSubcategoria, 'descripcion' => $descripcion, 'id_categoria' => $idCategoria]
            ]);

        } catch (Exception $error) {
            // Manejo de errores específicos (ej. duplicidad o clave foránea inexistente)
            if ($error->getCode() == '23000') { // SQLSTATE para violación de integridad
                if (strpos($error->getMessage(), 'uq_subcategoria_categoria_nombre') !== false) {
                    http_response_code(409); // Conflict
                    echo json_encode([
                        'success'=>false,
                        'message'=>'Ya existe una subcategoría con ese nombre para la categoría seleccionada.'
                    ]);
                } else if (strpos($error->getMessage(), 'fk_subcategoria_categoria') !== false) {
                    http_response_code(400); // Bad Request
                    echo json_encode([
                        'success'=>false,
                        'message'=>'El ID de categoría proporcionado no existe.'
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode([
                        'success'=>false,
                        'message'=>'Error de base de datos al crear la subcategoría: ' . $error->getMessage()
                    ]);
                }
            } else {
                http_response_code(500);
                echo json_encode([
                    'success'=>false,
                    'message'=>'Error en el servidor al crear la subcategoría: ' . $error->getMessage()
                ]);
            }
        }
    }

    /**
     * Actualiza una subcategoría existente.
     * Corresponde a PUT /subcategorias
     */
    public function actualizarSubcategoria(){
        $database = new Database();
        $conn = $database->getConnection();
        if (!$conn) {
            http_response_code(503); // Service Unavailable
            echo json_encode(['message' => 'No se pudo conectar a la base de datos.']);
            return;
        }

        $subcategoriaModelo = new Subcategoria($conn);
        $data = json_decode(file_get_contents('php://input'), true);

        $idSubcategoria = filter_var($data['idSubcategoria'] ?? null, FILTER_VALIDATE_INT); // Sanitización robusta
        $nombreSubcategoria = trim($data['nombreSubcategoria'] ?? '');
        $descripcion = trim($data['descripcion'] ?? '');
        // idCategoria no se actualiza en este método, pero se podría añadir si fuera necesario

        if ($idSubcategoria === false || $idSubcategoria <= 0) {
            http_response_code(400); // Bad Request
            echo json_encode([
                'success'=>false,
                'message'=>'ID de subcategoría inválido para actualizar.'
            ]);
            return;
        }
        if (empty($nombreSubcategoria) || empty($descripcion)) {
            http_response_code(400); // Bad Request
            echo json_encode([
                'success'=>false,
                'message'=>'Completa la información (nombre y descripción de la subcategoría).'
            ]);
            return;
        }
        try {
            // Opcional: Verificar si la subcategoría existe antes de intentar actualizar
            $existingSubcategory = $subcategoriaModelo->buscarPorId($idSubcategoria);
            if (!$existingSubcategory) {
                http_response_code(404); // Not Found
                echo json_encode([
                    'success'=>false,
                    'message'=>'Subcategoría con ID ' . $idSubcategoria . ' no encontrada para actualizar.'
                ]);
                return;
            }

            $response = $subcategoriaModelo->actualizarSubcategoria($idSubcategoria, $nombreSubcategoria, $descripcion);
            if (!$response) {
                http_response_code(304); // Not Modified
                echo json_encode([
                    'success'=>false,
                    'message'=>'No se realizó ninguna actualización en la subcategoría o los datos son idénticos.'
                ]);
                return;
            }

            http_response_code(200); // OK
            echo json_encode([
                'success'=>true,
                'message'=>'Subcategoría actualizada correctamente',
                'data'=>['id_subcategoria' => $idSubcategoria, 'nombre_subcategoria' => $nombreSubcategoria, 'descripcion' => $descripcion]
            ]);

        } catch (Exception $error) {
            // Manejo de errores específicos (ej. duplicidad)
            if ($error->getCode() == '23000') { // SQLSTATE para violación de unicidad
                http_response_code(409); // Conflict
                echo json_encode([
                    'success'=>false,
                    'message'=>'Ya existe una subcategoría con ese nombre para la categoría actual.'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success'=>false,
                    'message'=>$error->getMessage()
                ]);
            }
        }
    }

    /**
     * Elimina una subcategoría.
     * Corresponde a DELETE /subcategorias
     */
    public function eliminarSubcategoria(){
        $database = new Database();
        $conn = $database->getConnection();
        if (!$conn) {
            http_response_code(503); // Service Unavailable
            echo json_encode(['message' => 'No se pudo conectar a la base de datos.']);
            return;
        }

        $subcategoriaModelo = new Subcategoria($conn);
        $data = json_decode(file_get_contents('php://input'), true);

        $idSubcategoria = filter_var($data['idSubcategoria'] ?? null, FILTER_VALIDATE_INT);

        if ($idSubcategoria === false || $idSubcategoria <= 0) {
            http_response_code(400); // Bad Request
            echo json_encode([
                'success' => false,
                'message' => 'ID de subcategoría inválido para eliminar.'
            ]);
            return;
        }

        try {
            // Verificar si la subcategoría existe antes de intentar eliminarla
            $existingSubcategory = $subcategoriaModelo->buscarPorId($idSubcategoria);
            if (!$existingSubcategory) {
                http_response_code(404); // Not Found
                echo json_encode([
                    'success'=>false,
                    'message'=>'Subcategoría con ID ' . $idSubcategoria . ' no encontrada para eliminar.'
                ]);
                return;
            }

            $response = $subcategoriaModelo->eliminarSubcategoria($idSubcategoria);
            if (!$response) {
                http_response_code(500); // Internal Server Error
                echo json_encode([
                 'success'=>false,
                 'message'=>'Error al eliminar la subcategoría de la base de datos.'
                 ]);
                 return;
            }
            http_response_code(200); // OK
            echo json_encode([
                'success'=>true,
                'message'=>'Subcategoría eliminada correctamente'
            ]);
        } catch (Exception $error) {
            // Manejar posibles errores de clave foránea si la BD está configurada con RESTRICT
            if ($error->getCode() == '23000') { // SQLSTATE para violación de integridad
                 http_response_code(409); // Conflict
                 echo json_encode([
                    'success'=>false,
                    'message'=>'No se puede eliminar la subcategoría porque tiene productos asociados.'
                 ]);
            } else {
                http_response_code(500); // Internal Server Error
                echo json_encode([
                    'success'=>false,
                    'message'=>'Error en el servidor al eliminar la subcategoría: ' . $error->getMessage()
                ]);
            }
        }
    }
}
?>
