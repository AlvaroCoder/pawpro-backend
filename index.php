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
// require_once 'app/controllers/ControladorGet.php'; // Removido si ya no se usa, ya que Categoria y Subcategoria tendrán sus propios controladores completos.
require_once 'app/controllers/CategoriasController.php';
require_once 'app/controllers/UsuarioController.php';
require_once 'app/controllers/ProductoController.php';
require_once 'app/controllers/FacturaCompraController.php';
require_once 'app/controllers/SubcategoriasController.php';
require_once 'app/controllers/KPIController.php'; // Asegúrate de que este controlador exista.


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
// Patrón para /subcategorias/{id} para DELETE y PUT si aplica (SubcategoriasController espera ID en el body)
// Esta lógica con $resourceId para subcategorias y categorias es más común si el ID va en la URL.
// Dado que los controladores CategoriasController y SubcategoriasController esperan el ID en el body
// para PUT y DELETE, esta parte de extracción de ID para ellos en el enrutador podría no ser estrictamente
// necesaria si se sigue esa convención para esas rutas.
// Sin embargo, si se decide cambiar a /categorias/{id} y /subcategorias/{id} para PUT/DELETE,
// entonces esta lógica es útil.
elseif (preg_match('/^\/subcategorias\/(\d+)$/', $pathWithoutQuery, $matchesSubcategorias)) {
    $resourceId = $matchesSubcategorias[1];
    $baseRouteForSwitch = '/subcategorias';
}
elseif (preg_match('/^\/categorias\/(\d+)$/', $pathWithoutQuery, $matchesCategorias)) {
    $resourceId = $matchesCategorias[1];
    $baseRouteForSwitch = '/categorias';
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
            // Asume que el ID de la categoría está en el cuerpo de la solicitud JSON
            $controller->actualizarCategoria();
        }
        else if ($method === 'DELETE') {
            // Asume que el ID de la categoría está en el cuerpo de la solicitud JSON
            $controller->eliminarCategoria();
        }
        else {
            http_response_code(405);
            echo json_encode(["message" => "Método no permitido para /categorias."]);
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
            // Asume que el ID de la subcategoría está en el cuerpo de la solicitud JSON
            $controller->actualizarSubcategoria();
        }
        else if ($method === 'DELETE') {
            // Asume que el ID de la subcategoría está en el cuerpo de la solicitud JSON
            $controller->eliminarSubcategoria();
        }
        else{
            http_response_code(405);
            echo json_encode(["message" => "Método no permitido para /subcategorias."]);
        }
        break;

    // Los casos para '/api/subcategorias', '/api/presentaciones', '/api/marcas'
    // se han eliminado ya que se asume que cada entidad tendrá su propio controlador
    // CRUD completo (ej. SubcategoriasController) que manejará el listado (GET) también.
    // Si necesitas un ControladorGet para listados muy simples sin lógica CRUD completa,
    // puedes mantenerlo y ajustar las rutas aquí.

    case '/api/productos':
        $controller = new ProductoController();
        if ($method === 'POST' &&  is_null($resourceId)) {
            $controller->crearProducto();
        } elseif ($method === 'GET' && is_null($resourceId)) {
            // Esto manejará GET /api/productos y GET /api/productos?codigo_producto=XYZ
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
        } elseif ($method === 'GET' && is_null($resourceId)) {
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

    // AGREGADO POR BIANQUISS (mensajes de error corregidos)
    case '/api/kpi/producto-mas-stock':
        if ($method === 'GET') {
            KPIController::tresConMayorStock();
        } else {
            http_response_code(405);
            echo json_encode(["message" => "Método no permitido para /api/kpi/producto-mas-stock."]); // Mensaje corregido
        }
        break;
    case '/api/kpi/productos-menos-stock':
        if ($method === 'GET') {
            KPIController::tresConMenorStock();
        } else {
            http_response_code(405);
            echo json_encode(["message" => "Método no permitido para /api/kpi/productos-menos-stock."]); // Mensaje corregido
        }
        break;

    case '/api/kpi/total-productos':
        if ($method === 'GET') {
            KPIController::totalProductos();
        } else {
            http_response_code(405);
            echo json_encode(["message" => "Método no permitido para /api/kpi/total-productos."]); // Mensaje corregido
        }
        break;

    case '/api/kpi/total-marcas':
        if ($method === 'GET') {
            KPIController::totalMarcas();
        } else {
            http_response_code(405);
            echo json_encode(["message" => "Método no permitido para /api/kpi/total-marcas."]); // Mensaje corregido
        }
        break;

    case '/api/kpi/productos-por-vencer':
        if ($method === 'GET') {
            KPIController::productosPorVencer();
        } else {
            http_response_code(405);
            echo json_encode(["message" => "Método no permitido para /api/kpi/productos-por-vencer."]); // Mensaje corregido
        }
        break;

    case '/api/kpi/reporte-pdf':
        if ($method === 'GET') {
            KPIController::generarReportePDF();
        } else {
            http_response_code(405);
            echo json_encode(["message" => "Método no permitido para /api/kpi/reporte-pdf."]); // Mensaje corregido
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