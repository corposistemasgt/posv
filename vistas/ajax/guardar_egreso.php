<?php
include 'is_logged.php';
$session_id = session_id();
$idvendedor = $_GET['idusuario'];
$idusuario = $_SESSION['id_users'];
require_once "../db.php";
require_once "../php_conexion.php";
require_once "../funciones.php";
date_default_timezone_set('America/Guatemala');  
$fecha = date("Y-m-d");
$sq= "insert into tbegresos (idusuario,idvendedor,fecha,idsucursal) values('$idusuario',
'$idvendedor','$fecha','1')";
$query_update = mysqli_query($conexion, $sq);
$sql            = mysqli_query($conexion, "select max(idegreso) as id from tbegresos where 
idusuario='" . $idusuario . "' and idvendedor='$idvendedor'");
while ($row = mysqli_fetch_array($sql)) {
    $idegreso              = $row["id"];
}
$sql            = mysqli_query($conexion, "select * from productos, tbcarrito 
where productos.id_producto=tbcarrito.idproducto and tbcarrito.idusuario='".$idvendedor."'");
while ($row = mysqli_fetch_array($sql)) {
    $id              = $row["idcarrito"];
    $cantidad        = $row['cantidad'];
    if($cantidad==0)
    {
        $sq= "delete from tbcarrito where idcarrito=".$id;
        $query_update = mysqli_query($conexion, $sq);
    }
    else
    {
        $sq= "insert into tbdetalleegreso (idproducto, cantidad,idegreso) values('$id',
        '$cantidad','$idegreso')";
        $query_update = mysqli_query($conexion, $sq);
    }
}
