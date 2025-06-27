<?php
include 'is_logged.php';
require_once "../db.php";
require_once "../php_conexion.php";
$idcoti=$_POST['idcoti'];
$sql            = mysqli_query($conexion, "select * from tbcotizacion where idcotizacion='" . $idcoti . "'");
while ($row = mysqli_fetch_array($sql)) {
    $cliente         = $row["cliente"];
    $nit     = $row['nit'];
    $codigo_producto = $row['numero_factura'];
   echo json_encode(array("cliente" => $cliente, "nit" => $nit, "factura" => $codigo_producto));  
}
?>