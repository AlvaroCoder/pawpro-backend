<?php
require_once 'app/models/Categoria.php'; 
require_once 'config/database.php'; // Para la conexión a la BD

class CategoriaController{
    // CRUD

    /**
     * Lista todas las categorías.
     * Corresponde a GET /categorias
     */
    public function listarCategorias(){
        $database = new Database();
        $conn = $database->getConnection();
        if (!$conn) {
            http_response_code(503); // Service Unavailable
            echo json_encode(['message' => 'No se pudo conectar a la base de datos.']);
            return;
        }

        $categoriaModelo = new Categoria($conn);
        $response = $categoriaModelo->obtenerCategorias();
        http_response_code(200); // OK
        echo json_encode($response);
    }

    /**
     * Crea una nueva categoría.
     * Corresponde a POST /categorias
     */
    public function crearCategoria(){
        $database = new Database();
        $conn = $database->getConnection();
        if (!$conn) {
            http_response_code(503); // Service Unavailable
            echo json_encode(['message' => 'No se pudo conectar a la base de datos.']);
            return;
        }

        $categoriaModelo = new Categoria($conn);
        $data = json_decode(file_get_contents('php://input'), true);

        $nombreCategoria = trim($data['nombreCategoria'] ?? '');
        $descripcion = trim($data['descripcion'] ?? '');

        // Validación de campos obligatorios
        if (empty($nombreCategoria) || empty($descripcion)) {
            http_response_code(400); // Bad Request
            echo json_encode([
                'success' => false,
                'message' => 'Completa todos los campos obligatorios (nombre y descripción).'
            ]);
            return;
        }

        try {
            $response = $categoriaModelo->crearCategoria($nombreCategoria, $descripcion);
            if (!$response) {
               http_response_code(500); // Internal Server Error
               echo json_encode([
                'success'=>false,
                'message'=>'Error al crear la categoría en la base de datos.'
                ]);
                return;
            }

            http_response_code(201); // Created
            echo json_encode([
                'success'=>true,
                'message'=>'Categoría guardada correctamente',
                'data'=>['id_categoria' => $categoriaModelo->id_categoria, 'nombre_categoria' => $nombreCategoria, 'descripcion' => $descripcion] // Devolver datos creados
            ]);
        } catch (Exception $error) {
            // Manejo de errores específicos (ej. duplicidad)
            if ($error->getCode() == '23000') { // SQLSTATE para violación de unicidad
                http_response_code(409); // Conflict
                echo json_encode([
                    'success'=>false,
                    'message'=>'La categoría ya existe.'
                ]);
            } else {
                http_response_code(500); // Internal Server Error
                echo json_encode([
                    'success'=>false,
                    'message'=>'Error en el servidor al crear la categoría: ' . $error->getMessage()
                ]);
            }
        }
    }

    /**
     * Actualiza una categoría existente.
     * Corresponde a PUT /categorias
     */
    public function actualizarCategoria(){
        $database = new Database();
        $conn = $database->getConnection();
        if (!$conn) {
            http_response_code(503); // Service Unavailable
            echo json_encode(['message' => 'No se pudo conectar a la base de datos.']);
            return;
        }

        $categoriaModelo = new Categoria($conn);
        $data = json_decode(file_get_contents('php://input'), true);

        $idCategoria = filter_var($data['idCategoria'] ?? null, FILTER_VALIDATE_INT);
        $nombreCategoria = trim($data['nombreCategoria'] ?? '');
        $descripcion = trim($data['descripcion'] ?? '');

        // Validaciones
        if ($idCategoria === false || $idCategoria <= 0) {
            http_response_code(400); // Bad Request
            echo json_encode([
                'success' => false,
                'message' => 'ID de categoría inválido.'
            ]);
            return;
        }
        if (empty($nombreCategoria) || empty($descripcion)) {
            http_response_code(400); // Bad Request
            echo json_encode([
                'success' => false,
                'message' => 'Completa la información (nombre y descripción).'
            ]);
            return;
        }

        try {
            // Opcional: Verificar si la categoría existe antes de intentar actualizar
            $existingCategory = $categoriaModelo->obtenerCategoriaPorId($idCategoria);
            if (!$existingCategory) {
                http_response_code(404); // Not Found
                echo json_encode([
                    'success'=>false,
                    'message'=>'Categoría con ID ' . $idCategoria . ' no encontrada para actualizar.'
                ]);
                return;
            }

            $response = $categoriaModelo->actualizarCategoria(
                $idCategoria,
                $nombreCategoria,
                $descripcion
            );
            if (!$response) {
                http_response_code(304); // Not Modified
                echo json_encode([
                    'success'=>false,
                    'message'=>'No se realizó ninguna actualización o los datos son idénticos.'
                ]);
                return;
            }
            http_response_code(200); // OK
            echo json_encode([
                'success'=>true,
                'message'=>'Categoría actualizada correctamente', // Mensaje corregido
                'data'=>['id_categoria' => $idCategoria, 'nombre_categoria' => $nombreCategoria, 'descripcion' => $descripcion] // Devolver datos actualizados
            ]);
        } catch (Exception $error) { // Corrección: Eception a Exception
            // Manejo de errores específicos (ej. duplicidad)
            if ($error->getCode() == '23000') { // SQLSTATE para violación de unicidad
                http_response_code(409); // Conflict
                echo json_encode([
                    'success'=>false,
                    'message'=>'Ya existe una categoría con ese nombre.'
                ]);
            } else {
                http_response_code(500); // Internal Server Error
                echo json_encode([
                    'success'=>false,
                    'message'=>'Error en el servidor al actualizar la categoría: ' . $error->getMessage()
                ]);
            }
        }
    }

    /**
     * Elimina una categoría.
     * Corresponde a DELETE /categorias
     */
    public function eliminarCategoria(){
        $database = new Database();
        $conn = $database->getConnection();
        if (!$conn) {
            http_response_code(503); // Service Unavailable
            echo json_encode(['message' => 'No se pudo conectar a la base de datos.']);
            return;
        }

        $categoriaModelo = new Categoria($conn);
        $data = json_decode(file_get_contents('php://input'), true);

        $idCategoria = filter_var($data['idCategoria'] ?? null, FILTER_VALIDATE_INT);

        if ($idCategoria === false || $idCategoria <= 0) {
            http_response_code(400); // Bad Request
            echo json_encode([
                'success' => false,
                'message' => 'ID de categoría inválido para eliminar.'
            ]);
            return;
        }

        try {
            // Opcional: Verificar si la categoría existe antes de intentar eliminarla
            $existingCategory = $categoriaModelo->obtenerCategoriaPorId($idCategoria);
            if (!$existingCategory) {
                http_response_code(404); // Not Found
                echo json_encode([
                    'success'=>false,
                    'message'=>'Categoría con ID ' . $idCategoria . ' no encontrada para eliminar.'
                ]);
                return;
            }

            $response = $categoriaModelo->deleteCategoriaPorId($idCategoria);
            if (!$response) {
                http_response_code(500); // Internal Server Error
                echo json_encode([
                 'success'=>false,
                 'message'=>'Error al eliminar la categoría de la base de datos.'
                 ]);
                 return;
            }
            http_response_code(200); // OK
            echo json_encode([
                'success'=>true,
                'message'=>'Categoría eliminada correctamente'
            ]);
        } catch (Exception $error) { // Corrección: Eception a Exception
            // Manejar posibles errores de clave foránea si la BD está configurada con RESTRICT
            if ($error->getCode() == '23000') { // SQLSTATE para violación de integridad
                 http_response_code(409); // Conflict
                 echo json_encode([
                    'success'=>false,
                    'message'=>'No se puede eliminar la categoría porque tiene subcategorías asociadas.'
                 ]);
            } else {
                http_response_code(500); // Internal Server Error
                echo json_encode([
                    'success'=>false,
                    'message'=>'Error en el servidor al eliminar la categoría: ' . $error->getMessage()
                ]);
            }
        }
    }
}
?>
