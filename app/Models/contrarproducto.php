<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Parámetros de conexión
$host = "localhost";
$usuario = "root"; // Ajusta si usas otro usuario
$contrasena = "123456";  // Ajusta si tu MySQL tiene contraseña
$base_datos = "pawpro_database";

// Crear conexión
$conn = new mysqli($host, $usuario, $contrasena, $base_datos);

// Verificar conexión
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión: " . $conn->connect_error]);
    exit();
}

// Consulta: contar productos
$sql = "SELECT COUNT(*) AS total FROM productos";
$resultado = $conn->query($sql);

// Resultado
if ($resultado && $fila = $resultado->fetch_assoc()) {
    echo json_encode(["total_productos" => $fila["total"]]);
} else {
    echo json_encode(["error" => "Error en la consulta"]);
}

$conn->close();
?>
