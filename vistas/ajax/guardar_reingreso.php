<?php
include 'is_logged.php';
$session_id = session_id();
$idusuario = $_GET['idusuario'];
require_once "../db.php";
require_once "../php_conexion.php";
require_once "../funciones.php";
date_default_timezone_set('America/Guatemala');  
$fecha = date("Y-m-d");
$sql            = mysqli_query($conexion, "select * from productos, tbcarrito
where productos.id_producto=tbcarrito.idproducto and tbcarrito.idusuario='" . $idusuario . "'");
echo "todo bien";
while ($row = mysqli_fetch_array($sql)) {
    $id              = $row["idcarrito"];
    $idproducto      = $row["idproducto"];
    $idsucursal      = $row["idsucursal"];
    $cantidad        = $row['cantidad'];
    $costo        = $row['costo_producto'];
    $stock        = 1;
    $saldo=$stock+$cantidad;
    $saldosub=$saldo*$costo;
    if($cantidad>0)
    {
        $sq= "update stock set stock=".$saldo." where id_producto_stock=".$idproducto.
        " and id_sucursal_stock=".$idsucursal;
        $query_update = mysqli_query($conexion, $sq);
        $sub=$costo*$cantidad;
        $sq1 = "INSERT INTO kardex (fecha_kardex, producto_kardex,cant_entrada, costo_entrada,
         total_entrada, cant_salida, costo_salida,total_salida, cant_saldo, costo_saldo, total_saldo, added_kardex, users_kardex, tipo_movimiento)
            VALUES ('$fecha', '$idproducto', '$cantidad', '$costo', '$sub','0','0','0',
            '$saldo', '$costo', '$saldosub', '$fecha', '1', '3');";
        $query_update = mysqli_query($conexion, $sq1);
        $sq2= "update tbcarrito set cantidad=0 where idsucursal=".$idsucursal.
        " and idusuario=".$idusuario;
        $query_update = mysqli_query($conexion, $sq2);

    }
}