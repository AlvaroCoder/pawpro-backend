<?php
require_once __DIR__ . '/../Models/kpi.php';
require_once __DIR__ . '/../../config/database.php'; 

class KPIController {
    public static function resumenStock() {
        $conexion = Database::connect();
        $mayor = KPI::productoConMasStock($conexion);
        $menor = KPI::productoConMenosStock($conexion);

        echo json_encode([
            'producto_con_mas_stock' => $mayor,
            'producto_con_menos_stock' => $menor
        ]);
    }
}
