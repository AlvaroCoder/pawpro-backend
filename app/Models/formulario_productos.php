<?php
$host = "localhost";
$usuario = "root";
$contrasena = "";
$bd = "registro_productos"; // Asegúrate de que existe

$conexion = new mysqli($host, $usuario, $contrasena, $bd);
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

function generarIdProducto($conexion) {
    do {
        $letras = strtoupper(substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 3));
        $numeros = rand(100, 999);
        $id_producto = $letras . $numeros;

        // Verificar si el ID ya existe en la base de datos
        $query = $conexion->prepare("SELECT COUNT(*) FROM productos WHERE id_producto = ?");
        $query->bind_param("s", $id_producto);
        $query->execute();
        $query->bind_result($count);
        $query->fetch();
        $query->close();
    } while ($count > 0); 

    return $id_producto;
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $nombre = isset($_GET['nombre']) ? trim($_GET['nombre']) : '';
    $descripcion = isset($_GET['descripcion']) ? trim($_GET['descripcion']) : '';
    $precio_compra = isset($_GET['precio_compra']) ? floatval($_GET['precio_compra']) : 0;
    $precio_venta = isset($_GET['precio_venta']) ? floatval($_GET['precio_venta']) : 0;
    $stock = isset($_GET['stock']) ? intval($_GET['stock']) : 0;
    $categoria = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';
    $subcategoria = isset($_GET['subcategoria']) ? trim($_GET['subcategoria']) : '';
    $marca = isset($_GET['marca']) ? trim($_GET['marca']) : '';
    $presentacion = isset($_GET['presentacion']) ? trim($_GET['presentacion']) : '';
    $fecha_vencimiento = isset($_GET['fecha_vencimiento']) ? trim($_GET['fecha_vencimiento']) : '';
    $fabricante = isset($_GET['fabricante']) ? trim($_GET['fabricante']) : '';
    $peso_volumen = isset($_GET['peso_volumen']) ? floatval($_GET['peso_volumen']) : 0;
    $unidad_medida = isset($_GET['unidad_medida']) ? trim($_GET['unidad_medida']) : '';
    $ubicacion_almacen = isset($_GET['ubicacion_almacen']) ? trim($_GET['ubicacion_almacen']) : '';
    $notas = isset($_GET['notas']) ? trim($_GET['notas']) : '';

    // Generar un nuevo ID único
    $id_producto = generarIdProducto($conexion);

    // Preparar la consulta
    $stmt = $conexion->prepare("INSERT INTO productos (id_producto, nombre, descripcion, precio_compra, precio_venta, stock, categoria, subcategoria, marca, presentacion, fecha_vencimiento, fabricante, peso_volumen, unidad_medida, ubicacion_almacen, notas)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("sssddissssssssss",
        $id_producto,
        $nombre,
        $descripcion,
        $precio_compra,
        $precio_venta,
        $stock,
        $categoria,
        $subcategoria,
        $marca,
        $presentacion,
        $fecha_vencimiento,
        $fabricante,
        $peso_volumen,
        $unidad_medida,
        $ubicacion_almacen,
        $notas
    );

    if ($stmt->execute()) {
        echo "Producto registrado exitosamente. ID: " . $id_producto;
    } else {
        echo "Error al registrar: " . $stmt->error;
    }

    $stmt->close();
}

$conexion->close();
?>
