<?php
session_start();
require_once "../../db.php";
require_once "../../php_conexion.php";
require_once "../../funciones.php";
$meses =intval($_REQUEST['meses']);
$id_categoria = intval($_REQUEST['categoria']);
$id_sucursal =  intval($_REQUEST['sucursal']);
$mes=date("m");
    $year=date("Y");
    $t=$mes+$meses;
    if($t>12)
    {
        $mes=$t-12;
        $year++;
    }
    $fecha_final=$year."-".$t."-31 00:00:00";
    $fecha_inicial=date("Y-m-d")." 00:00:00";
    $cadena = " from productos,lineas,stock where stock.id_producto_stock=productos.id_producto and fecha_vencimiento  between '$fecha_inicial' and '$fecha_final' and lineas.id_linea=productos.id_linea_producto";
if($id_sucursal>0)
{
    $cadena.=" and stock.id_sucursal_stock=".$id_sucursal;
}
if($id_categoria>0)
{
    $cadena.=" and lineas.id_linea=".$id_categoria;
}
if($id_proveedor>0)
{
    $cadena.=" and id_proveedor=".$id_proveedor;
}
$sql = "SELECT * ".$cadena;
$query = mysqli_query($conexion,$sql );
ob_start();
include dirname(__FILE__) . '/res/rep_vencimientos_html.php';
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
