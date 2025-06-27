<?php
session_start();
if (!isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] != 1) {
    header("location: ../../../login.php");
    exit;
}
include "../../db.php";
include "../../php_conexion.php";
include "../../funciones.php";
$id_factura = $_GET['id_factura'];
$sql_count  = mysqli_query($conexion, "select * from facturas_ventas where id_factura='" . $id_factura . "'");
$count      = mysqli_num_rows($sql_count);
if ($count == 0) {
    echo "<script>alert('Factura no encontrada')</script>";
    echo "<script>window.close();</script>";
    exit;
}
while ($row = mysqli_fetch_array($sql_count)) {
    $estado     = $row["estado_factura"];
    $guidExistente = $row['guid_factura'];
    $nitExistente  = $row['factura_nit_cliente'];
    $nombreExistente = $row['factura_nombre_cliente'];    
}
if(trim($guidExistente) === ""){
    $arreglo = array(
        "nitExistente" => $nitExistente,
        "nombreExistente" => $nombreExistente,
        "certificado"     => "0",
    );
}else{
    $arreglo = array(
        "nitExistente" => $nitExistente,
        "nombreExistente" => $nombreExistente,
        "certificado"     => "1",
    );
}
echo json_encode($arreglo);
exit;