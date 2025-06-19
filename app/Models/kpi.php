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
        $sql = "SELECT COUNT(*) AS total_marcas FROM marcas";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // ['total_marcas' => N]
    }


    public static function productosPorVencer($conn) {
        $sql = "
            SELECT 
                p.nombre_producto,
                l.codigo_lote,
                l.fecha_vencimiento,
                l.cantidad_actual
            FROM 
                productos p
            JOIN 
                lotesproducto l ON p.producto_id = l.producto_id
            WHERE 
                l.fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 700 DAY)
            ORDER BY 
                l.fecha_vencimiento ASC
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    

}
