<?php
class KPI {
    public static function productoConMasStock($conexion) {
        $sql = "SELECT * FROM productos ORDER BY stock DESC LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function productoConMenosStock($conexion) {
        $sql = "SELECT * FROM productos WHERE stock > 0 ORDER BY stock ASC LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function totalProductos($conn) {
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM productos");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function totalMarcas($conn) {
        $stmt = $conn->prepare("SELECT COUNT(DISTINCT marca) as total FROM productos");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function tresConMenorStock($conn) {
        $stmt = $conn->prepare("SELECT nombre, stock FROM productos ORDER BY stock ASC LIMIT 3");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function productosPorVencer($conn) {
        $stmt = $conn->prepare("SELECT nombre, fecha_vencimiento FROM productos WHERE fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 MONTH)");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    

}
