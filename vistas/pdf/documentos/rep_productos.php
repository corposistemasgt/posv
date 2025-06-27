<?php
session_start();
require_once "../../db.php";
require_once "../../php_conexion.php";
require_once "../../funciones.php";
$id_categoria = intval($_REQUEST['categoria']);
$consulta2 = "SELECT * FROM productos left join lineas on productos.id_linea_producto = lineas.id_linea left join stock on productos.id_producto = stock.id_producto_stock left join perfil on id_perfil =  id_sucursal_stock"; 
if ($id_categoria > 0) {
    $consulta2 .= " Where productos.id_linea_producto = '" . $id_categoria . "'";
}
$consulta2 .= " order by productos.id_producto";
$query = mysqli_query($conexion, $consulta2);
ob_start();
include dirname(__FILE__) . '/res/rep_productos_html.php';
$content = ob_get_clean();
require_once dirname(__FILE__) . '/../html2pdf.class.php';
try
{
    $html2pdf = new HTML2PDF('L', 'A4', 'es', true, 'UTF-8', 3);
    $html2pdf->pdf->SetDisplayMode('fullpage');
    $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
    ob_end_clean();
    $html2pdf->Output('productos.pdf');
} catch (HTML2PDF_exception $e) {
    echo $e;
    exit;
}
