<?php 

// Comando para levantar servidor en mac:
// php -S localhost:8000


header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// --- RESPONDER a las solicitudes OPTIONS (preflight) ---
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// --- TIPO DE RESPUESTA ---
header("Content-Type: application/json");

// --- DEPENDENCIAS ---
require_once 'config/database.php';
require_once 'app/controllers/ControladorGet.php';
require_once 'app/controllers/ControladorLogin.php';
require_once 'app/controllers/UsuarioController.php';

// --- RUTA Y MÉTODO ---
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];


// --- ENRUTADOR ---
switch ($request) {
    case '/api/register':
        $controller = new UsuarioController();
        $controller->registrar();
        break;

    case '/api/login':
        if ($method === 'POST') {
            $controller = new AuthController();
            $controller->login();
        } else {
            http_response_code(405);
            echo json_encode(["message" => "Método no permitido"]);
        }
        break;

    case '/api/categorias':
        if ($method === 'GET') {
            $controller = new Controlador();
            $controller->obtenerCategorias();
        } else {
            http_response_code(405);
            echo json_encode(["message" => "Método no permitido"]);
        }
        break;

    case '/api/subcategorias':
        if ($method === 'GET') {
            $controller = new Controlador();
            $controller->obtenerSubcategorias(); 
        } else {
            http_response_code(405);
            echo json_encode(["message" => "Método no permitido"]);
        }
        break;

    case '/api/presentaciones':
        if ($method === 'GET') {
            $controller = new Controlador();
            $controller->obtenerPresentaciones();
        } else {
            http_response_code(405);
            echo json_encode(["message" => "Método no permitido"]);
        }
        break;
    
    case '/api/marcas':
        if ($method === 'GET') {
            $controller = new Controlador();
            $controller->obtenerMarcas();
        } else {
            http_response_code(405);
            echo json_encode(["message" => "Método no permitido"]);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(["message" => "Ruta no encontrada"]);
        break;
}

?>