<?php
session_start();
if (!isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] != 1) {
    header("location: ../../../login.php");
    exit;
}
include "../../db.php";
include "../../php_conexion.php";
include "../../funciones.php";
$session_id = session_id();
$sql_count = mysqli_query($conexion, "select * from tmp_compra where session_id='" . $session_id . "'");
$count     = mysqli_num_rows($sql_count);
if ($count == 0) {
    echo "<script>alert('No hay productos agregados a la factura')</script>";
    echo "<script>window.close();</script>";
    exit;
}
require_once dirname(__FILE__) . '/../html2pdf.class.php';
$id_proveedor = intval($_GET['id_proveedor']);
$fecha        = $_GET['fecha'];
$id_vendedor  = intval($_GET['id_vendedor']);
$condiciones  = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['condiciones'], ENT_QUOTES)));
$factura      = mysqli_real_escape_string($conexion, (strip_tags($_GET["factura"], ENT_QUOTES)));
$sql            = mysqli_query($conexion, "select LAST_INSERT_ID(id_factura) as last from facturas_compras order by id_factura desc limit 0,1 ");
$rw             = mysqli_fetch_array($sql);
$numero_factura = $rw['last'] + 1;
$simbolo_moneda = "Q";
ob_start();
include dirname('__FILE__') . '/res/compra_html.php';
$content = ob_get_clean();
try
{
    $html2pdf = new HTML2PDF('P', 'LETTER', 'es', true, 'UTF-8', array(0, 0, 0, 0));
    $html2pdf->pdf->SetDisplayMode('fullpage');
    $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
    $html2pdf->Output('Factura.pdf');
} catch (HTML2PDF_exception $e) {
    echo $e;
    exit;
}
