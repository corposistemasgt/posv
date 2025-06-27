<?php
session_start();
if (!isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] != 1) {
    header("location: ../../../login.php");
    exit;
}
include "../../db.php";
include "../../php_conexion.php";
include "../../funciones.php";
$id_sucursal=$_SESSION['idsucursal'];
$id_factura = intval($_POST['id_factura']);
$sql_count  = mysqli_query($conexion, "select * from facturas_ventas where id_factura='" . $id_factura . "'");
$count      = mysqli_num_rows($sql_count);
if ($count == 0) {
    echo "<script>alert('Factura no encontrada')</script>";
    echo "<script>window.close();</script>";
    exit;
}
$sql_factura    = mysqli_query($conexion, "select * from facturas_ventas where id_factura='" . $id_factura . "'");
$rw_factura     = mysqli_fetch_array($sql_factura);
$numero = $rw_factura['numero_certificacion'];
$id_cliente     = $rw_factura['id_cliente'];
$guid    = $rw_factura['guid_factura'];
$serie   = $rw_factura['serie_factura'];
$fecha_certificacion  = $rw_factura['fechaCertificacion'];
$fecha_emision        = $rw_factura['fecha_factura'];
$nit_cliente          = $rw_factura['factura_nit_cliente'];
$FechaEmision         = $rw_factura['fecha_emision'];    
$estado               = $rw_factura['estado_factura'];
$numero_factura= $rw_factura['numero_factura']; 
$motivo_anulacion = "correcion datos";
if(trim($guid) === ""){
    $id_vendedor    = intval($_SESSION['id_users']);
    if($estado == 3){
        echo("El documento ya ha sido anulado anteriormente");
    }else{
        actualizar_estado_documento($id_factura,$id_sucursal );
    }  
}
else{
    $id_vendedor    = intval($_SESSION['id_users']);
    $requestor   = $_SESSION['requestor'];
    $nit_emisor  = $_SESSION['nit'];

    $motivo = "Error en datos";
    $date_added     = date("Y-m-dTH:i:s");  
    if($estado == 3)
    {
        echo("<script>alert('El documento ya ha sido anulado')</script>");
    }
    else
    {
      echo anularFactura($guid,$nit_emisor, $motivo,$nit_cliente,$date_added,$FechaEmision,$requestor, $id_factura, $id_sucursal);
    } 
}
if($estado==2)
{
     $update = mysqli_query($conexion, "delete from creditos where numero_factura='$numero_factura'");

}