<?php
require_once 'app/models/Categoria.php';

class CategoriaController{
    // CRUD
    public function listarCategorias(){
        $categoriaModelo = new Categoria();
        $response = $categoriaModelo->obtenerCategorias();
        echo json_encode($response);
    }
    
    public function crearCategoria(){
        $categoriaModelo = new Categoria();
        
        $data = json_decode(file_get_contents('php://input'), true);

        $nombreCategoria = trim($data['nombreCategoria']);
        $descripcion = trim($data['descripcion']);

        try {
            $response = $categoriaModelo->crearCategoria($nombreCategoria, $descripcion);
            if (!$response) {
               http_response_code(401);
               echo json_encode([
                'success'=>false,
                'message'=>'Error al crear la categoria'
                ]);
                return;
            }

            echo json_encode([
                'success'=>true,
                'message'=>'Categoria guardada correctamente',
                'data'=>$response
            ]);
        } catch (Exception $error) {
            http_response_code(401);
            echo json_encode([
                'success'=>false,
                'message'=>$error->getMessage()
            ]);
        }
    }
    
    public function actualizarCategoria(){
        $categoriaModelo = new Categoria();
        
        $data = json_decode(file_get_contents('php://input'), true);

        $idCategoria = $data['idCategoria'];
        try {
            $response = $categoriaModelo->actualizarCategoria();
            if (!$response) {
                http_response_code(401);
                echo json_encode([
                 'success'=>false,
                 'message'=>'Error al crear la categoria'
                 ]);
                 return;
            }
            echo json_encode([
                'success'=>true,
                'message'=>'Categoria actualizada',
                'data'=>$response
            ]);
        } catch (Exception $error) {
            http_response_code(401);
            echo json_encode([
                'success'=>false,
                'message'=>$error->getMessage()
            ]);
        }

    }
    public function eliminarCategoria(){

    }
}


?>