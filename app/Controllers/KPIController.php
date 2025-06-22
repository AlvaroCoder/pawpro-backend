<?php
require_once __DIR__ . '/../Models/kpi.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../vendor/autoload.php';
use Dompdf\Dompdf;
class KPIController
{
    private static function getConexion()
    {
        return (new Database())->getConnection();
    }
// productos con mas stocks
    public static function tresConMayorStock()
    {
        $conexion = self::getConexion();
        echo json_encode([
            'producto_con_mas_stock' => KPI::tresConMayorStock($conexion)
        ]);
    }
//productos con menos stock
    public static function tresConMenorStock()
    {
        $conexion = self::getConexion();
        echo json_encode([
            'productos_con_menos_stock' => KPI::tresConMenorStock($conexion)
        ]);
    }

    public static function totalProductos()
    {
        $conexion = self::getConexion();
        echo json_encode([
            'total_productos' => KPI::totalProductos($conexion)
        ]);
    }

    public static function totalMarcas()
    {
        $conexion = self::getConexion();
        echo json_encode([
            'total_marcas' => KPI::totalMarcas($conexion)
        ]);
    }

    public static function productosPorVencer()
    {
        $conexion = self::getConexion();
        echo json_encode([
            'productos_por_vencer' => KPI::productosPorVencer($conexion)
        ]);
    }
    public static function generarReportePDF()
    {
        $conexion = self::getConexion();

        // Recolectar los datos del modelo
        $top3MayorStock = KPI::tresConMayorStock($conexion);
        $top3MenorStock = KPI::tresConMenorStock($conexion);
        $productosPorVencer = KPI::productosPorVencer($conexion);
        $totalProductos = KPI::totalProductos($conexion);
        $totalMarcas = KPI::totalMarcas($conexion);

        // Preparar HTML para el PDF (usa vista PHP externa)
        ob_start();
        include __DIR__ . '/../View/resumen_inventario.php';
        $html = ob_get_clean();

        // Crear PDF con DomPDF
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="reporte_inventario.pdf"');
        echo $dompdf->output();
    }



    
}
