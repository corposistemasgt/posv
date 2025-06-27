<?php
session_start();
include "../../db.php";
include "../../php_conexion.php";
include('../documentos/descargar_factura.php');
include "../../funciones.php";
$id_factura = intval($_GET['id_factura']);
$sql_count  = mysqli_query($conexion, "select * from facturas_ventas where id_factura='" . $id_factura . "'");
$count      = mysqli_num_rows($sql_count);
if ($count == 0) {
    echo "<script>alert('Factura no encontrada')</script>";
    echo "<script>window.close();</script>";
    exit;
}
$sql_factura    = mysqli_query($conexion, "select * from facturas_ventas where id_factura='" . $id_factura . "'");
$rw_factura     = mysqli_fetch_array($sql_factura);
$numero_factura = $rw_factura['numero_factura'];
$id_cliente     = $rw_factura['id_cliente'];
$id_vendedor    = $rw_factura['id_vendedor'];
$fecha_factura  = $rw_factura['fecha_factura'];
$condiciones    = $rw_factura['condiciones'];
$simbolo_moneda = "Q";
    $sql    = mysqli_query($conexion, "select * from facturas_ventas left join detalle_fact_ventas on facturas_ventas.id_factura = detalle_fact_ventas.id_factura left join productos on productos.id_producto = detalle_fact_ventas.id_producto left join users on users.id_users = facturas_ventas.id_vendedor where facturas_ventas.id_factura = '" . $id_factura . "'");
     $listaProductos = array();
     $boolTieneAfectos = false;
     $boolTieneExentos = false;
   while ($row = mysqli_fetch_array($sql)) {
    $id_producto     = $row["id_producto"];
    $codigo_producto = $row['codigo_producto'];
    $cantidad        = $row['cantidad'];
    $nombre_producto = $row['nombre_producto'];
    $precio_venta_sub   = $row['importe_venta'];
    $precio_venta_uni = $row['precio_venta'];
    $id_vendedor_db = $row['id_users'];
    $nombreVendedor =  $row['nombre_users']." ".$row['apellido_users'];
    $nombreCliente = $row['factura_nombre_cliente'];
    $nitCliente = $row['factura_nit_cliente'];
    $esGenerico = $row['esGenerico'];
    $tipoFact =  $row['tipoDocumento'];
    $estadoFactura = $row['estado_factura'];
    if($esGenerico === '0')
    {
        $boolTieneAfectos = true;
    }
    $batchExistente = $row['serie_factura'];
    $certificacionExistente = $row['numero_certificacion'];
    $guidExistente = $row['guid_factura'];
    $montoGravable = $precio_venta_sub/1.12;
    $montoGravable = round($montoGravable, 6, PHP_ROUND_HALF_EVEN); 
    $montoImpuesto = $precio_venta_sub-$montoGravable;
    $montoImpuesto = round($montoImpuesto, 6, PHP_ROUND_HALF_EVEN); 
    $impuestosDelProducto = array("IVA","1",$montoGravable,$montoImpuesto);
    if($esGenerico === '1')
    {
        $boolTieneExentos = true;
        $montoGravable = round($precio_venta_sub, 6, PHP_ROUND_HALF_EVEN); 
        $montoImpuesto = 0;
        $impuestosDelProducto = array("IVA","2",$montoGravable,$montoImpuesto);
        $nombre_producto = $nombre_producto;
    }
    $itemsVenta = array();
    array_push($itemsVenta, $cantidad,$nombre_producto,"B",$precio_venta_uni,$precio_venta_sub,$impuestosDelProducto);
    array_push($listaProductos,$itemsVenta);
   }
    $sqlUsuarioACT        = mysqli_query($conexion, "select * from users where id_users = '".$id_vendedor."'"); //obtener el usuario activo 1aqui1
    $rw         = mysqli_fetch_array($sqlUsuarioACT);
    $id_sucursal = $rw['sucursal_users'];
    $sql    = mysqli_query($conexion, "select * from perfil where id_perfil = '".$id_sucursal."'");
        while ($row = mysqli_fetch_array($sql)) {
            $id     = $row["id_perfil"];
            $nitEmisor     = $_SESSION['nit'];
            $nombreEmisor     = $row["nombre_empresa"];
            $nombreComercial     = $row["giro_empresa"];
            $direccionEmisor     = $row["direccion"];
            $departamento     = $row["estado"];
            $municipio     = $row["ciudad"];
            $codigoEstablecimiento     = $row["codigoEstablecimiento"];
            $requestor = $row["requestor"];
            $frase     = $row["frase"];
            $escenario     = $row["escenario"];
            $regimen       = $row["regimen"];

         }
    $codigoPostal = "16001";
    $esCF = false;
    if(trim($nombreCliente) == "" || trim($nitCliente) == "" || trim($nitCliente) == "0")
    {
        $esCF = true;
        $nitCliente = "CF";
        if(trim($nombreCliente) == "")
        {
            echo("nombre CFF");
            $nombreCliente = "CF";
        }
    }
    else{$esCF = false;}
    $frasesyEscenarios =  array();
    if($boolTieneExentos  === true)
    {
        array_push($frasesyEscenarios,"4","9");
    }
        array_push($frasesyEscenarios,$frase,$escenario);
    $usuario = "ADMINISTRADOR";
    $caja = 1;
    if($estadoFactura == 1 || $tipoFact == "FCAM")
    {
        if(trim($batchExistente) === "" && trim($certificacionExistente) === "")
        {
            $var = certificar($tipoFact,$nitEmisor,$nombreEmisor,$codigoEstablecimiento,$direccionEmisor,$codigoPostal,$departamento, $municipio,$nitCliente, $nombreCliente,$frasesyEscenarios,$usuario,$requestor,
            $caja,$listaProductos,$nombreComercial);
            $batch = $var['Batch'];
            $numero_factura = $var['Serial'];
            $guid = $var['Guid'];
            $FechaCertificacion = $var['TimeStamp'];
            $totalIva = $var['TotalIva'];
            $horaEmision = $var['HoraEmision'];                                                                                                                    
            date("d/m/Y", strtotime($fecha_factura));
            $sql   = mysqli_query($conexion, "UPDATE facturas_ventas set serie_factura = '".$batch."', guid_factura = '".$guid."', factura_nombre_cliente = '".$nombreCliente."', factura_nit_cliente = '".$nitCliente."', numero_certificacion = '".$numero_factura."', fechaCertificacion = '".$FechaCertificacion."', totalIva = '".$totalIva."', fecha_emision = '".$horaEmision."' where id_factura = '" . $id_factura . "'");
            descargar_factura($id_factura, $conexion);
        }else{
            $batch =$batchExistente;
            $numero_factura = $certificacionExistente;
            $guid = $guidExistente;
            descargar_factura($id_factura, $conexion);
        }
    }