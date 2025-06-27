<?php
include 'is_logged.php';
require_once "../db.php";
require_once "../php_conexion.php";
$idcoti=$_GET['idcoti'];
$cantidad=$_GET['cantidad'];
if(intval($idcoti)<1){
    $idcoti=0;
}
$sql="UPDATE tbdetallecot set cantidad=".$cantidad." WHERE idcotizacion=".$idcoti;
$delete = mysqli_query($conexion, $sql);
echo "exito";
?>