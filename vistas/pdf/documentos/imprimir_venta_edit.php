<?php
include '../../ajax/is_logged.php';
?>
<style type="text/css" media="print">
    #Imprime {
        height: auto;
        width: 377px;
        margin: 0px;
        padding: 0px;
        float: left;
        font-family: "Comic Sans MS", cursive;
        font-size: 7px;
        font-style: normal;
        line-height: normal;
        font-weight: normal;
        font-variant: normal;
        text-transform: none;
        color: #000;
    }
    @page{
        margin: 0;
    }
</style>
<?php
require_once "../../db.php";
require_once "../../php_conexion.php"; 
include "../../funciones.php";
$id_factura = $_POST['id_factura'];
$sql           = mysqli_query($conexion, "SELECT * FROM perfil, facturas_ventas,users where 
facturas_ventas.id_vendedor =users.id_users and users.sucursal_users =perfil.id_perfil and 
facturas_ventas.id_factura =".$id_factura);
$rw            = mysqli_fetch_array($sql);
$moneda        = $rw["moneda"];
$bussines_name = $rw["nombre_empresa"];
$giro          = $rw["giro_empresa"];
$fiscal        = $_SESSION['nit'];
$address       = $rw["direccion"];
$city          = $rw["ciudad"];
$state         = $rw["estado"];
$postal_code   = $rw["codigo_postal"];
$phone         = $rw["telefono"];
$email         = $rw["email"];
$logo_url      = $rw["logo_url"];
/*Fin datos empresa*/
$simbolo_moneda = "Q";
$sql_factura    = mysqli_query($conexion, "select * from facturas_ventas, users where facturas_ventas.id_vendedor=users.id_users and facturas_ventas.id_factura='" . $id_factura . "'");
$count          = mysqli_num_rows($sql_factura);
$rw_factura     = mysqli_fetch_array($sql_factura);
$nombre_users   = $rw_factura['nombre_users'];
$fecha_factura  = date("d/m/Y", strtotime($rw_factura['fecha_factura']));
$hora_factura   = date('H:i:s', strtotime($rw_factura['fecha_factura']));
$condiciones    = $rw_factura['condiciones'];
$numero_factura = $rw_factura['numero_factura'];
$resibido       = $rw_factura['dinero_resibido_fac'];
$factura_cliente= $rw_factura['factura_nombre_cliente'];
$nit_cliente    = $rw_factura['factura_nit_cliente'];
?>

<page pageset='new' backtop='10mm' backbottom='10mm' backleft='20mm' backright='20mm' footer='page'>
    <table width="175px" style="font-size:12px; font-family:'Lucida Sans Unicode', 'Lucida Grande', sans-serif" border="0" >
        <tr>
            <td colspan="3">
                <div align="center" style="font-size:22px"><strong><?php echo $giro; ?></strong><br></div>
                <div align="center" style="font-size:16px"><strong><?php echo  $bussines_name; ?></strong><br></div>
                <div align="center" style="font-size:16px"><strong>NIT: <?php echo $fiscal; ?></strong><br></div>
                <div align="center" style="font-size:14px"><strong><?php echo $address; ?></strong><br></div>
                <div align="center" style="font-size:14px"><strong><?php echo $city . ',' . $state; ?></strong><br></div>
                <div align="center" style="font-size:14px"><strong>Tel: </strong> <?php echo $phone; ?><br></div>
            </td>
        </tr>
        <tr>
            <td colspan="3"><center>-----------------------------------------</center></td>
        </tr>
        <tr>
            <td colspan="4">
                <div align="left">Ticket: <?php echo $numero_factura; ?></div>
                <div align="left">Cliente: <?php echo $factura_cliente; ?></div>
                <div align="left">Vendedor: <?php echo $nombre_users; ?><br></div>
                <div align="left">Fecha: <?php echo $fecha_factura . ' ' . $hora_factura; ?><br></div>
            </td>
        </tr>
        <tr>
        </tr>
        <tr>
            <td colspan="3"><center>==============================</center></td>
        </tr>
        <tr>
            <td>Cant.</td>
            <td>Descrip.</td>
            <td>Precio Total</td>
        </tr>
        <tr>
            <td colspan="3"><center>==============================</center></td>
        </tr>
        <?php
$nums          = 1;
$sumador_total = 0;
$sum_total     = 0;
$sql           = mysqli_query($conexion, "select * from productos, detalle_fact_ventas,
 facturas_ventas where productos.id_producto=detalle_fact_ventas.id_producto and 
 detalle_fact_ventas.id_factura=facturas_ventas.id_factura and 
 facturas_ventas.id_factura='" . $id_factura . "'");

while ($row = mysqli_fetch_array($sql)) {
    $id_producto     = $row["id_producto"];
    $codigo_producto = $row['codigo_producto'];
    $cantidad        = $row['cantidad'];
    $desc_tmp        = $row['desc_venta'];
    $nombre_producto = $row['nombre_producto'];

    $precio_venta   = $row['precio_venta'];
    $precio_venta_f = number_format($precio_venta, 2); //Formateo variables
    $precio_venta_r = str_replace(",", "", $precio_venta_f); //Reemplazo las comas
    $precio_total   = $precio_venta_r * $cantidad;
    $final_items    = rebajas($precio_total, $desc_tmp); //Aplicando el descuento
    /*--------------------------------------------------------------------------------*/
    $precio_total_f = number_format($final_items, 2); //Precio total formateado
    $precio_total_r = str_replace(",", "", $precio_total_f); //Reemplazo las comas
    $sumador_total += $precio_total_r; //Sumador
    if ($nums % 2 == 0) {
        $clase = "clouds";
    } else {
        $clase = "silver";
    }
    ?>

    <tr>
        <td><?php echo $cantidad; ?></td>
        <td><?php echo $nombre_producto; ?></td>
        <td><?php echo $simbolo_moneda . ' ' . number_format($precio_total, 2); ?></td>
    </tr>
    <?php

    $nums++;
}
$subtotal      = number_format($sumador_total, 2, '.', '');
$total_factura = $subtotal ;
$cambio        = $resibido - $total_factura;
?>
<tr>
<td colspan="3"><center>----------------------------------------</center></td>
</tr>
<tr>
<td colspan="2">TOTAL:</td>
<td><?php echo $simbolo_moneda . ' ' . number_format($total_factura, 2); ?></td>
</tr>
<tr>
<td colspan="2"> PAGO:</td>
<td><?php echo $simbolo_moneda . ' ' . number_format($resibido, 2); ?></td>
</tr>
<tr>
<td colspan="2"> VUELTO:</td>
<td><?php echo $simbolo_moneda . ' ' . number_format($cambio, 2); ?></td>
</tr>
<tr>
<td colspan="3"><center>----------------------------------------</center></td>
</tr>
<tr>
<td colspan="2">NO. DE ARTICULOS:</td>
<td align="center"> 1 </td>
</tr>
<tr>
<td colspan="3"><center>----------------------------------------</center></td>
</tr>
<tr>
<td colspan="3"><center>*GRACIAS POR SU COMPRA*</center></td>
</tr>
<tr>
<td colspan="3"><center></center></td>
</tr><br>
</table>
</page>