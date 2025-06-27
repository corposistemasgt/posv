<?php
include '../../ajax/is_logged.php'; 
require_once "../../db.php";
require_once "../../php_conexion.php";
require_once "../../funciones.php";
$id_producto    = $_SESSION['idpk'];
$idsucursal   = $_SESSION['idsk'];
$nombre_producto = get_row('productos', 'nombre_producto', 'id_producto', $id_producto);
$daterange = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['daterange'], ENT_QUOTES)));
$tables    = "kardex, productos";
$campos    = "*";
$sWhere    = "kardex.producto_kardex=productos.id_producto";
if (!empty($daterange)) {
    list($f_inicio, $f_final)                    = explode(" - ", $daterange);
    list($dia_inicio, $mes_inicio, $anio_inicio) = explode("/", $f_inicio); 
    $fecha_inicial                               = "$anio_inicio-$mes_inicio-$dia_inicio 00:00:00"; 
    list($dia_fin, $mes_fin, $anio_fin)          = explode("/", $f_final); 
    $fecha_final                                 = "$anio_fin-$mes_fin-$dia_fin 23:59:59";
    $sWhere .= " and kardex.fecha_kardex between '$fecha_inicial' and '$fecha_final' ";
}
$sWhere .= " and kardex.producto_kardex='" . $id_producto . "' and idsucursal=" . $idsucursal;
$query = mysqli_query($conexion, "SELECT $campos FROM  $tables where $sWhere ");
ob_start();
include dirname(__FILE__) . '/res/rep_kardex_html.php';
$content = ob_get_clean();
require_once dirname(__FILE__) . '/../html2pdf.class.php';
try
{
    $html2pdf = new HTML2PDF('L', 'A4', 'es', true, 'UTF-8', 3);
    $html2pdf->pdf->SetDisplayMode('fullpage');
    $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
    ob_end_clean();
    $html2pdf->Output('kardex.pdf');
} catch (HTML2PDF_exception $e) {
    echo $e;
    exit;
}