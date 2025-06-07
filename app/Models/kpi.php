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
}
