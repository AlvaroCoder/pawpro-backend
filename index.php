<?php 

// Comando para levantar servidor en mac:
// php -S localhost:8000

header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, PATCH, DELETE");
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
require_once 'app/controllers/CategoriasController.php';
require_once 'app/controllers/UsuarioController.php';
require_once 'app/controllers/ProductoController.php';
require_once 'app/controllers/FacturaCompraController.php';
require_once 'app/controllers/SubcategoriasController.php';
require_once 'app/controllers/KPIController.php';


// --- RUTA Y MÉTODO ---
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// 1. Limpiar parámetros de consulta para obtener la ruta pura
$pathWithoutQuery = strtok($request, '?'); // Ej: /api/productos/4 o /api/productos

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
    $resourceId = $matchesFacturas[1]; // ID capturado
    $baseRouteForSwitch = '/api/facturas-compra'; // Normalizamos la ruta para el switch
}

// --- ENRUTADOR ---
switch ($baseRouteForSwitch) {
    case '/users':
        $controller = new UsuarioController();
        $controller->listarUsuarios();
        break;
    case '/users/signup':
        $controller = new UsuarioController();
        $controller->registrar();
        break;

    case '/users/login':
        $controller = new UsuarioController();
        $controller->iniciarSesion();
        break;

    case '/categorias':
        $controller = new CategoriaController();
        if ($method === 'GET') { 
            $controller->listarCategorias();
        }
        else if ($method === 'POST') {
            $controller->crearCategoria();
        }
        else if ($method === 'PUT') {
            $controller->actualizarCategoria();
        }
        else if ($method === 'DELETE') {
            $controller->eliminarCategoria();
        }
        else {
            http_response_code(405);
            echo json_encode(["message" => "Método no permitido"]);
        }
        break;
    case '/subcategorias':
        $controller = new SubcategoriaController();
        if ($method === 'GET') {
            $controller->listarSubcategorias();
        } 
        else if ($method === 'POST') {
            $controller->crearSubcategoria();
        }
        else if ($method === 'PUT') {
            $controller->actualizarSubcategoria();
        }
        else{
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
            $controller->crearProducto();
        } elseif ($method === 'GET' && $resourceId === null) {
            $controller->listarProductos();
        } elseif ($method === 'GET' && $resourceId !== null) {
            $controller->obtenerProductoPorId($resourceId);
        } elseif ($method === 'PUT' && $resourceId !== null) {
            $controller->actualizarProducto($resourceId);
        } elseif ($method === 'DELETE' && $resourceId !== null) {
            $controller->eliminarProducto($resourceId);
        } else {
            http_response_code(405);
            echo json_encode(["message" => "Método no permitido o combinación de ruta/ID inválida para /api/productos."]);
        }
        break;

    case '/api/facturas-compra':
        $controller = new FacturaCompraController();
        if ($method === 'POST' && $resourceId === null) {
            $controller->registrarNuevaFactura();
        } elseif ($method === 'GET' && $resourceId === null) {
            $controller->listarFacturasCompra();
        } elseif ($method === 'GET' && $resourceId !== null) {
            $controller->obtenerFacturaPorId($resourceId);
        } elseif (($method === 'PUT' || $method === 'PATCH') && $resourceId !== null) {
            $controller->actualizarEstadoFactura($resourceId);
        } elseif ($method === 'DELETE' && $resourceId !== null) {
            $controller->anularFacturaCompra($resourceId);
        } else {
            http_response_code(405);
            echo json_encode(["message" => "Método no permitido o combinación de ruta/ID inválida para /api/facturas-compra."]);
        }
        break;
    //AGREGADO POR BIANQUISS
    case '/api/kpi/producto-mas-stock':
        if ($method === 'GET') {
            KPIController::productoConMasStock();
        } else {
            http_response_code(405);
            echo json_encode(["message" => "Método no permitido para /api/kpi/resumen-stock"]);
        }
        break;

    case '/api/kpi/productos-menos-stock':
        if ($method === 'GET') {
            KPIController::productosMenorStock();
        } else {
            http_response_code(405);
            echo json_encode(["message" => "Método no permitido para /api/kpi/resumen-stock"]);
        }
        break;

    case '/api/kpi/total-productos':
        if ($method === 'GET') {
            KPIController::totalProductos();
        } else {
            http_response_code(405);
            echo json_encode(["message" => "Método no permitido para /api/kpi/resumen-stock"]);
        }
        break;

    case '/api/kpi/total-marcas':
        if ($method === 'GET') {
            KPIController::totalMarcas();
        } else {
            http_response_code(405);
            echo json_encode(["message" => "Método no permitido para /api/kpi/resumen-stock"]);
        }
        break;

    case '/api/kpi/productos-por-vencer':
        if ($method === 'GET') {
            KPIController::productosPorVencer();
        } else {
            http_response_code(405);
            echo json_encode(["message" => "Método no permitido para /api/kpi/resumen-stock"]);
        }
        break;
    
    case '/api/kpi/reporte-pdf':
        if ($method === 'GET') {
            KPIController::generarReportePDF();
        } else {
            http_response_code(405);
            echo json_encode(["message" => "Método no permitido para /api/kpi/reporte-pdf"]);
        }
        break;



//FIN DE AGREGADO POR BIANQUIS

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