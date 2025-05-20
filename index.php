<?php 

    // Comando para levantar servidor en mac
    // php -S localhost:8000 
    header("Access-Control-Allow-Origin:*");
    header("Content-Type: application/json");

    require_once 'config/database.php';
    require_once 'app/controllers/CategoriaController.php';
    require_once 'app/controllers/FacturaCompraController.php';
    require_once 'app/controllers/ProductoController.php';

    $request = $_SERVER['REQUEST_URI'];
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($request) {
        case '/api/categorias':
            if ($method === 'GET') {
                $contoller = new CategoriaController();
                $contoller->obtenerCategorias();
            }else {
                http_response_code(405);
                echo json_encode(["message"=> "Metodo no permitido"]);
            }
            break;
        case '/api/facturas-compra':
            if ($method === 'POST') {
                $controller = new FacturaCompraController();
                $controller->registrarNuevaFactura();
            } else {
                http_response_code(405);
                echo json_encode(["message"=> "Metodo no permitido para esta ruta"]);
            }
            break;
        
        case '/api/productos':
            if ($method === 'POST') {
                $controller = new ProductoController();
                $controller->crearNuevoProducto(); // Método que crearemos
            } else {
                // Aquí podrías añadir manejo para GET /api/productos (listar), GET /api/productos/{id} (ver uno), etc.
                http_response_code(405);
                echo json_encode(["message"=> "Metodo no permitido para /api/productos en esta etapa. Solo POST para crear."]);
            }
            break;
        
        default:
            http_response_code(404);
            echo json_encode(["message" => "Ruta no encontrada"]);
            break;
    }


?>