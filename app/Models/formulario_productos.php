<?php
$host = "localhost";
$usuario = "root";
$contrasena = "";
$bd = "registro_productos"; // Asegúrate de que existe

$conexion = new mysqli($host, $usuario, $contrasena, $bd);
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id_producto'])) {
    $stmt = $conexion->prepare("INSERT INTO productos (id_producto, nombre, descripcion, precio_compra, precio_venta, stock, categoria, subcategoria,marca, presentacion, fecha_vencimiento, fabricante, peso_volumen, unidad_medida, ubicacion_almacen, notas)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("sssddisssssssss",
        $_GET['id_producto'],
        $_GET['nombre'],
        $_GET['descripcion'],
        $_GET['precio_compra'],
        $_GET['precio_venta'],
        $_GET['stock'],
        $_GET['categoria'],
        $_GET['subcategoria'],
        $_GET['marca'],
        $_GET['presentacion'],
        $_GET['fecha_vencimiento'],
        $_GET['fabricante'],
        $_GET['unidad_medida'],
        $_GET['notas']
    );

    if ($stmt->execute()) {
        echo "Producto registrado exitosamente.";
    } else {
        echo "Error al registrar: " . $stmt->error;
    }

    $stmt->close();
}

$conexion->close();
?>
