<?php
require_once 'app/models/Subcategoria.php';

class SubcategoriaController{
    public function listarSubcategorias(){
        $subcategoriaModelo = new Subcategoria();
        $response = $subcategoriaModelo->obtenerSubcategorias();
        echo json_encode($response);
    }

    public function crearSubcategoria(){
        $subcategoriaModelo = new Subcategoria();
        $data = json_decode(file_get_contents('php://input'), true);
        $idCategoria = $data['idCategoria'];
        $nombreSubcategoria = trim($data['nombreSubcategoria']);
        $descripcion = trim($data['descripcion']);

        if (empty($nombreSubcategoria) || empty($descripcion)) {
            http_response_code(401);
            echo json_encode([
                'success'=>false,
                'message'=>'Completa la información'
            ]);
            return;
        }

        try {
            $response = $subcategoriaModelo->crearSubcategoria($idCategoria, $nombreSubcategoria, $descripcion);
            if (!$response) {
                http_response_code(401);
                echo json_encode([
                    'success'=>false,
                    'message'=>'Error al crear la subcategoria'
                ]);
                return;
            }

            echo json_encode([
                'success'=>true,
                'message'=>'Subcategoria guardada correctamente',
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

    public function actualizarSubcategoria(){
        $subcategoriaModelo = new Subcategoria();
        $data = json_decode(file_get_contents('php://input'), true);
        $idSubcategoria = $data['idSubcategoria'];
        $nombreSubcategoria = trim($data['nombreSubcategoria']);
        $descripcion = trim($data['descripcion']);
        
        if (empty($idSubcategoria) || !is_numeric($idSubcategoria)) {
            http_response_code(401);
            echo json_encode([
                'success'=>false,
                'message'=>'ID de subcategoría inválido'
            ]);
            return;
        }
        if (empty($nombreSubcategoria) || empty($descripcion)) {
            http_response_code(401);
            echo json_encode([
                'success'=>false,
                'message'=>'Completa la información'
            ]);
            return;
        }
        try {
            $response = $subcategoriaModelo->actualizarSubcategoria($idSubcategoria, $nombreSubcategoria, $descripcion);
            if (!$response) {
                http_response_code(401);
                echo json_encode([
                    'success'=>false,
                    'message'=>'Error al actualizar la subcategoría'
                ]);
                return;
            }

            echo json_encode([
                'success'=>true,
                'message'=>'Subcategoría actualizada correctamente',
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
}

?>