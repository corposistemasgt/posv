<?php
include 'is_logged.php';
require_once "../db.php";
require_once "../php_conexion.php";
$sql  = "select id_producto from productos";
$query = mysqli_query($conexion, $sql);
while ($rww = mysqli_fetch_array($query)) {
    $idproducto = $rww['id_producto'];
    $sql = "INSERT INTO stock (id_producto_stock,id_sucursal_stock,
    cantidad_stock,stock_minimo,fecha_vencimiento) values
    ('$idproducto','2','0','1','1969-12-31 06:00:00')";
    $query_update = mysqli_query($conexion, $sql);
}