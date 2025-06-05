<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Producto.php';

class KPIController {

    // Obtener productos con más stock
    public function productosConMasStock($limite = 5) {
        $conn = (new Database())->getConnection();
        $productoModel = new Producto($conn);

        $productos = $productoModel->obtenerTopStock($limite);

        http_response_code(200);
        echo json_encode($productos);
    }

    // Obtener productos con menor stock (alerta)
    public function productosConBajoStock($limite = 3) {
        $conn = (new Database())->getConnection();
        $productoModel = new Producto($conn);

        $productos = $productoModel->obtenerProductosBajoStock($limite);

        http_response_code(200);
        echo json_encode($productos);
    }

    // Obtener productos recientemente añadidos
    public function productosRecientes($limite = 4) {
        $conn = (new Database())->getConnection();
        $productoModel = new Producto($conn);

        $productos = $productoModel->obtenerProductosRecientes($limite);

        http_response_code(200);
        echo json_encode($productos);
    }

    // Endpoint resumen general
    public function resumenKPI() {
        $conn = (new Database())->getConnection();
        $productoModel = new Producto($conn);

        $resumen = [
            "top_stock" => $productoModel->obtenerTopStock(5),
            "bajo_stock" => $productoModel->obtenerProductosBajoStock(3),
            "recientes" => $productoModel->obtenerProductosRecientes(4)
        ];

        http_response_code(200);
        echo json_encode($resumen);
    }
}
