<?php
require_once "../../db.php";
require_once "../../php_conexion.php";
require_once "../../funciones.php";
$id  = intval($_REQUEST['id']);
$tables         = "tbdetalleegreso,productos";
$campos         = " * ";
$sWhere         = " idproducto=codigo_producto and idegreso=".$id;
$sWhere .= " order by iddetalle";
$query = mysqli_query($conexion, "SELECT $campos FROM  $tables where $sWhere ");
ob_start();
include dirname(__FILE__) . '/res/rep_egresosdetalle_html.php';
$content = ob_get_clean();
require_once dirname(__FILE__) . '/../html2pdf.class.php';
try
{
    $html2pdf = new HTML2PDF('P', 'A4', 'es', true, 'UTF-8', 3);
    $html2pdf->pdf->SetDisplayMode('fullpage');
    $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
    ob_end_clean();
    $html2pdf->Output('usuarios.pdf');
} catch (HTML2PDF_exception $e) {
    echo $e;
    exit;
}
