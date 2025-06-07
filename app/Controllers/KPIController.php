<?php
require_once __DIR__ . '/../Models/kpi.php';
require_once __DIR__ . '/../../config/database.php'; 

class KPIController {
    public static function resumenStock() {
        // ✅ Crear instancia de Database (porque no usas métodos estáticos)
        $database = new Database();
        $conexion = $database->getConnection();

        // ✅ Usar la conexión en los métodos del modelo KPI
        $mayor = KPI::productoConMasStock($conexion);
        $menor = KPI::productoConMenosStock($conexion);

        // ✅ Devolver JSON limpio
        echo json_encode([
            'producto_con_mas_stock' => $mayor,
            'producto_con_menos_stock' => $menor
        ]);
    }
}
