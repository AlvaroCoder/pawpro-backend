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
require_once 'app/controllers/ProductoController.php';

// --- RUTA Y MÉTODO ---
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// 1. Limpiar parámetros de consulta para obtener la ruta pura
$pathWithoutQuery = strtok($requestUri, '?'); // Ej: /api/productos/4 o /api/productos

// 2. Extraer ID y normalizar la ruta para el switch
$resourceId = null;
$baseRouteForSwitch = $pathWithoutQuery; // Por defecto, usamos la ruta completa (sin query params)

// Patrón para /api/productos/{id}
if (preg_match('/^\/api\/productos\/(\d+)$/', $pathWithoutQuery, $matchesProductos)) {
    $resourceId = $matchesProductos[1]; // ID capturado, ej: "4"
    $baseRouteForSwitch = '/api/productos'; // Normalizamos la ruta para el switch
}
// Patrón para /api/facturas-compra/{id}
elseif (preg_match('/^\/api\/facturas-compra\/(\d+)$/', $pathWithoutQuery, $matchesFacturas)) {
    $resourceId = $matchesFacturas[1];// ID capturado
    $baseRouteForSwitch = '/api/facturas-compra'; // Normalizamos la ruta para el switch
}

// Ahora $baseRouteForSwitch será '/api/productos', '/api/facturas-compra', etc.
// y $resourceId contendrá el ID numérico si la URL coincidió con un patrón de ID.


// --- ENRUTADOR ---
switch ($baseRouteForSwitch) {
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
    case '/api/productos':
        $controller = new ProductoController();
        if ($method === 'POST' && $resourceId === null) {
        $controller->crearProducto(); // Crear nuevo producto
            } elseif ($method === 'GET' && $resourceId === null) {
        $controller->listarProductos(); // Listar todos o buscar por ?codigo_producto=
            } elseif ($method === 'GET' && $resourceId !== null) {
        $controller->obtenerProductoPorId($resourceId); // Obtener producto por ID numérico
            } elseif ($method === 'PUT' && $resourceId !== null) {
        $controller->actualizarProducto($resourceId); // Actualizar producto (completo)
            } elseif ($method === 'DELETE' && $resourceId !== null) {
        $controller->eliminarProducto($resourceId); // Eliminar producto
            } else {
        http_response_code(405); // Method Not Allowed
        echo json_encode(["message" => "Método no permitido o combinación de ruta/ID inválida para /api/productos."]);
            }
        break;
    case '/api/facturas-compra':
        $controller = new FacturaCompraController();
            if ($method === 'POST' && $resourceId === null) {
                $controller->registrarNuevaFactura(); // Registrar nueva factura
            } elseif ($method === 'GET' && $resourceId === null) {
                // gestionarGetFacturasCompra puede listar todo o buscar por ?id=
                // Si queremos que sea solo para listar todo aquí:
                $controller->listarFacturasCompra(); // Asumiendo que este método solo lista todos.
            } elseif ($method === 'GET' && $resourceId !== null) {
                $controller->obtenerFacturaPorId($resourceId); // Obtener factura por ID de ruta
            } elseif (($method === 'PUT' || $method === 'PATCH') && $resourceId !== null) {
                // Usaremos PATCH para actualizar estado, PUT podría ser para más campos.
                $controller->actualizarEstadoFactura($resourceId); // Actualizar estado de factura
            } elseif ($method === 'DELETE' && $resourceId !== null) {
                $controller->anularFacturaCompra($resourceId); // Anular (soft delete) factura
            } else {
                http_response_code(405); // Method Not Allowed
                echo json_encode(["message" => "Método no permitido o combinación de ruta/ID inválida para /api/facturas-compra."]);
            }
            break;
    default:
        http_response_code(404);
        echo json_encode([
            "message" => "Ruta no encontrada.",
            "ruta_solicitada_debug" => htmlspecialchars($pathWithoutQuery),
            "server_request_uri_debug" => htmlspecialchars($_SERVER['REQUEST_URI'])
        ]);
        break;
}
?>