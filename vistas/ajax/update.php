<?php
include 'is_logged.php';
require_once "../db.php";
require_once "../php_conexion.php";
$sql  = "select id_producto,date_vence,stock_min_producto from productos";
$query = mysqli_query($conexion, $sql);
while ($rww = mysqli_fetch_array($query)) {
    $idproducto = $rww['id_producto'];
    $fecha = $rww['date_vence'];
    $stock = $rww['stock_min_producto'];
    if(strcmp($fecha,'')==0)
    {
        $fecha="2000-01-01";
    }
    $sql = "UPDATE stock SET fecha_vencimiento='" . $fecha . "',
    stock_minimo = ".$stock." WHERE id_producto_stock='" . $idproducto . "'";
    echo $sql.'\n';
    $query_update = mysqli_query($conexion, $sql);
}