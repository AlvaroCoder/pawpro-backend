<?php
class KPI {

// funcion para extraer los 3 productos con mayor stock, esta haciendo join de 2 tablas

    public static function tresConMayorStock($conn) {
        $sql = "
            SELECT 
                p.nombre_producto, 
                SUM(l.cantidad_actual) AS stock_total
            FROM 
                productos p
            JOIN 
                lotesproducto l ON p.producto_id = l.producto_id
            GROUP BY 
                p.producto_id
            ORDER BY 
                stock_total DESC
            LIMIT 3
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// funcion para extraer los 3 productos con menor stock, esta haciendo join de 2 tablas
    public static function tresConMenorStock($conn) {
        $sql = "
            SELECT 
                p.nombre_producto, 
                SUM(l.cantidad_actual) AS stock_total
            FROM 
                productos p
            JOIN 
                lotesproducto l ON p.producto_id = l.producto_id
            GROUP BY 
                p.producto_id
            ORDER BY 
                stock_total ASC
            LIMIT 3
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    

    public static function productosPorVencer($conn) {
        $stmt = $conn->prepare("SELECT nombre, fecha_vencimiento
        FROM productos WHERE fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 MONTH)");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    

}
