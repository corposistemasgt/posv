<?php
session_start();
require_once "../../db.php";
require_once "../../php_conexion.php";
require_once "../../funciones.php";
$id_categoria = intval($_REQUEST['categoria']);
$agrupar      = $_REQUEST['agrupar'];
$tables       = "detalle_fact_ventas AS detf inner join facturas_ventas 
                     on detf.id_factura = facturas_ventas.id_factura inner join productos 
                     on detf.id_producto = productos.id_producto inner join lineas 
                     on productos.id_linea_producto = lineas.id_linea inner join users on facturas_ventas.id_vendedor = users.id_users ";
if($agrupar == "si"){
$campos       = "productos.*, lineas.id_linea, lineas.nombre_linea, 
                productos.estado_producto, facturas_ventas.fecha_factura, 
                facturas_ventas.id_factura, facturas_ventas.condiciones, detf.*, sum(detf.cantidad) as cantidad_vendida, 
                sum(cantidad*precio_venta*desc_venta/100) as descuento, users.usuario_users, sum(detf.precio_venta*cantidad) as precio_venta,
                sum(importe_venta) as importe_venta ";
}else{
$campos       = "productos.*, lineas.id_linea, lineas.nombre_linea, 
productos.estado_producto, facturas_ventas.fecha_factura, 
facturas_ventas.id_factura, facturas_ventas.condiciones, detf.*, detf.cantidad as cantidad_vendida, users.usuario_users ";
}
$sWhere      = "lineas.id_linea=productos.id_linea_producto and facturas_ventas.estado_factura <> 3 ";
$daterange      = mysqli_real_escape_string($conexion, (strip_tags($_GET['daterange'], ENT_QUOTES)));
if (!empty($daterange)) {
    list($f_inicio, $f_final)                    = explode(" - ", $daterange); //Extrae la fecha inicial y la fecha final en formato espa?ol
    list($dia_inicio, $mes_inicio, $anio_inicio) = explode("/", $f_inicio); //Extrae fecha inicial
    $fecha_inicial                               = "$anio_inicio-$mes_inicio-$dia_inicio 00:00:00"; //Fecha inicial formato ingles
    list($dia_fin, $mes_fin, $anio_fin)          = explode("/", $f_final); //Extrae la fecha final
    $fecha_final                                 = "$anio_fin-$mes_fin-$dia_fin 23:59:59";

    $sWhere .= "and (facturas_ventas.fecha_factura between '$fecha_inicial' and '$fecha_final' )";
}
if ($id_categoria > 0) {
    $sWhere .= " and productos.id_linea_producto = '" . $id_categoria . "' ";
}
if($agrupar == "si"){
    $sWhere .= " group by productos.id_producto ";
}
$sWhere .= " order by (productos.id_producto) ";
$sql = "SELECT $campos FROM  $tables where $sWhere ";
$query = mysqli_query($conexion,$sql );
ob_start();
include dirname(__FILE__) . '/res/rep_productos_ventas_html.php';
$content = ob_get_clean();
require_once dirname(__FILE__) . '/../html2pdf.class.php';
try
{
    $html2pdf = new HTML2PDF('P', 'A4', 'es', true, 'UTF-8', 3);
    $html2pdf->pdf->SetDisplayMode('fullpage');
    $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
    ob_end_clean();
    $html2pdf->Output('productos.pdf');
} catch (HTML2PDF_exception $e) {
    echo $e;
    exit;
}
