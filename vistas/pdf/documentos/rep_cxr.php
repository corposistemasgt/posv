<?php
session_start();
require_once "../../db.php";
require_once "../../php_conexion.php";
require_once "../../funciones.php";
$idruta         = intval($_REQUEST['idruta']);
$tables         = "creditos,facturas_ventas,users,clientes ";
$campos         = "*";
$sWhere         = " creditos.numero_factura =facturas_ventas.numero_factura and 
    facturas_ventas.id_vendedor =users.id_users  
    and facturas_ventas.id_cliente=clientes.id_cliente ";
if ($idruta != "") 
{
    $sWhere .= " and idruta=".$idruta;
}
$sWhere .= " order by creditos.id_credito desc";

$query = mysqli_query($conexion, "SELECT $campos FROM  $tables where $sWhere ");
ob_start();
include dirname(__FILE__) . '/res/rep_cxr_html.php';
$content = ob_get_clean();
require_once dirname(__FILE__) . '/../html2pdf.class.php';
try
{
    $html2pdf = new HTML2PDF('L', 'A4', 'es', true, 'UTF-8', 3);
    $html2pdf->pdf->SetDisplayMode('fullpage');
    $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
    ob_end_clean();
    $html2pdf->Output('usuarios.pdf');
} catch (HTML2PDF_exception $e) {
    echo $e;
    exit;
}
