<?php
include 'is_logged.php'; 
require_once "../db.php";
require_once "../php_conexion.php";
$user_id = $_SESSION['id_users'];
require_once "../funciones.php";
$id_moneda    = "Q";
$fecha_actual = date('Y-m-d');
$abonoSql    = "SELECT * FROM creditos_abonos where date(fecha_abono) = '$fecha_actual' and id_users_abono='$user_id' 
and idcierre=0";
$abonoQuery  = $conexion->query($abonoSql);
$total_abono = 0;
while ($abonoResult = $abonoQuery->fetch_assoc()) {
    $total_abono += $abonoResult['abono'];
}
$orderSql     = "SELECT * FROM facturas_ventas WHERE DATE(fecha_factura)='$fecha_actual' and 
id_users_factura='$user_id' and estado_factura<>3  and idcierre=0";
$orderQuery   = $conexion->query($orderSql);
$totalRevenue = 0;
while ($orderResult = $orderQuery->fetch_assoc()) {
    $totalRevenue += $orderResult['monto_factura'];
}
echo '' . $id_moneda . ' ' . number_format($totalRevenue + $total_abono, 2) . '';