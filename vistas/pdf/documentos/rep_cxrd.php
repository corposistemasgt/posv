<?php
session_start();
require_once "../../db.php";
require_once "../../php_conexion.php";
require_once "../../funciones.php";
$idcliente         = intval($_REQUEST['idcliente']);
$tables         = "SELECT id_credito,facturas_ventas.numero_factura,fecha_credito,
    factura_nombre_cliente,correos,nombre_users,apellido_users,estado_credito,
    clientes.limite_credito,monto_credito,saldo_credito,id_factura as idf,(select 
    sum(monto_factura-costo_producto * cantidad) from facturas_ventas,detalle_fact_ventas,
    productos  where facturas_ventas.id_factura=detalle_fact_ventas.id_factura and 
productos.id_producto =detalle_fact_ventas.id_producto and facturas_ventas.id_factura =idf) 
as ganancia  FROM creditos,facturas_ventas,users,clientes where 
creditos.numero_factura =facturas_ventas.numero_factura and 
facturas_ventas.id_vendedor =users.id_users and 
facturas_ventas.id_cliente=clientes.id_cliente and clientes.id_cliente=".$idcliente ." 
order by creditos.id_credito desc";

$query = mysqli_query($conexion, $tables);
ob_start();
include dirname(__FILE__) . '/res/rep_cxrd_html.php';
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
