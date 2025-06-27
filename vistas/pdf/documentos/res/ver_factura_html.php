<?php  
    $id_vendedor    = intval($_SESSION['id_users']);
    $sql    = mysqli_query($conexion, "select * from facturas_ventas left join detalle_fact_ventas on facturas_ventas.id_factura = detalle_fact_ventas.id_factura left join productos on productos.id_producto = detalle_fact_ventas.id_producto left join users on users.id_users = facturas_ventas.id_vendedor where facturas_ventas.id_factura = '" . $id_factura . "'");
    $listaProductos = array();
    $boolTieneAfectos = false;
    $boolTieneExentos = false;
    while ($row = mysqli_fetch_array($sql)) 
    {
        $id_producto     = $row["id_producto"];
        $codigo_producto = $row['codigo_producto'];
        $cantidad        = $row['cantidad'];
        $nombre_producto = $row['nombre_producto'];
        $medida=$row['medida'];
        $precio_venta_sub   = $row['importe_venta'] ;
        $maneja_inventario = $row['inv_producto'];
        $monto_sobre_cual_calcular_impuestos   = $row['importe_venta'];
        $descuento_enporcentaje = $row['desc_venta'];
        $descuento_endecimal = $descuento_enporcentaje/100;
        $precio_venta_uni = $row['precio_venta'];
        $monto_descuento = "0";
        if($descuento_endecimal > 0)
        {
            $monto_descuento = $precio_venta_uni*$descuento_endecimal*$cantidad;
        }
        $precio_venta_sub = $precio_venta_uni * $cantidad;
        $id_vendedor_db = $row['id_users'];
        $nombreVendedor =  $row['nombre_users']." ".$row['apellido_users'];
        $nombreCliente = $row['factura_nombre_cliente'];
        $nitCliente = $row['factura_nit_cliente'];
        $esGenerico = $row['esGenerico'];
        $tipoFact =  $row['tipoDocumento'];
        $estadoFactura = $row['estado_factura'];
        $bien_servicio = "B";
        if( $maneja_inventario == 1)
        {
            $bien_servicio = "S";
        }
        if($esGenerico === '0')
        {
            $boolTieneAfectos = true;
        }
        $batchExistente = $row['serie_factura'];
        $certificacionExistente = $row['numero_certificacion'];
        $guidExistente = $row['guid_factura'];
        $montoGravable = $monto_sobre_cual_calcular_impuestos/1.12;
        $montoGravable = round($montoGravable, 6, PHP_ROUND_HALF_EVEN); 
        $montoImpuesto = $monto_sobre_cual_calcular_impuestos-$montoGravable;
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
        array_push($itemsVenta, $cantidad,$nombre_producto,$bien_servicio,$precio_venta_uni,$precio_venta_sub,$impuestosDelProducto,$monto_descuento,$medida);
        array_push($listaProductos,$itemsVenta);
    }
    $sqlUsuarioACT        = mysqli_query($conexion, "select * from users where id_users = '".$id_vendedor."'"); //obtener el usuario activo 1aqui1
    $rw         = mysqli_fetch_array($sqlUsuarioACT);
    $id_sucursal = $rw['sucursal_users'];
    $sql    = mysqli_query($conexion, "select * from perfil where id_perfil = '".$id_sucursal."'");
    while ($row = mysqli_fetch_array($sql)) 
    {
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
    if(trim($frase) == "3" and trim($escenario) == "1")
    {
        $tipoFact = "FPEQ";
    }
    if(trim($nombreCliente) == "" || trim($nitCliente) == "" || trim($nitCliente) == "0")
    {
        $esCF = true;
        $nitCliente = "CF";
        if(trim($nombreCliente) == "")
        {
            $nombreCliente = "CF";
        }
    }
    else
    {
        $esCF = false;
    }
    $frasesyEscenarios =  array();
    if($boolTieneExentos  === true)
    {
        array_push($frasesyEscenarios,"4","9");
    }
    array_push($frasesyEscenarios,$frase,$escenario);
    $usuario = "ADMINISTRADOR";
    $caja = 1;
    if($estadoFactura == 3 && trim($guidExistente) === "")
    {
            return;
    }
    else if(trim($batchExistente) === "" && trim($certificacionExistente) === "")
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
    }
    else
    {
        $batch =$batchExistente;
        $numero_factura = $certificacionExistente;
        $guid = $guidExistente;
    }
    $nums          = 1;
    $impuesto      = get_row('perfil', 'impuesto', 'id_perfil', 1);
    $sumador_total = 0;
    $sum_total     = 0;
    $t_iva         = 0;
    $sql    = mysqli_query($conexion, "select * from facturas_ventas left join detalle_fact_ventas on facturas_ventas.id_factura = detalle_fact_ventas.id_factura left join productos on productos.id_producto = detalle_fact_ventas.id_producto left join users on users.id_users = facturas_ventas.id_vendedor where facturas_ventas.id_factura = '" . $id_factura . "'");
    while ($row = mysqli_fetch_array($sql)) 
    {
        $id_producto     = $row["id_producto"];
        $codigo_producto = $row['codigo_producto']; 
        $cantidad        = $row['cantidad'];
        $desc_tmp        = $row['desc_venta'];
        $nombre_producto = $row['nombre_producto'];
        $esGenerico = $row['esGenerico'];
        $FechaCertificacion = $row['fechaCertificacion'];
        if($esGenerico === '1')
        {
            $nombre_producto = "*".$nombre_producto;
        }
    if ($row['iva_producto'] == 0) 
    {
        $p_venta   = $row['precio_venta'];
        $p_venta_f = number_format($p_venta, 2); 
        $p_venta_r = str_replace(",", "", $p_venta_f); 
        $p_total   = $p_venta_r * $cantidad;
        $f_items   = rebajas($p_total, $desc_tmp); 
        $p_total_f = number_format($f_items, 2); 
        $p_total_r = str_replace(",", "", $p_total_f);
        $sum_total += $p_total_r;
    }
    $precio_venta   = $row['precio_venta'];
    $precio_venta_f = number_format($precio_venta, 2);
    $precio_venta_r = str_replace(",", "", $precio_venta_f);
    $precio_total   = $precio_venta_r * $cantidad;
    $final_items    = rebajas($precio_total, $desc_tmp); 
    $precio_total_f = number_format($final_items, 2); 
    $precio_total_r = str_replace(",", "", $precio_total_f); 
    $sumador_total += $precio_total_r; 
    if ($nums % 2 == 0) {
        $clase = "clouds";
    } else {
        $clase = "silver";
    }
    $nums++;
}
$subtotal      = number_format($sumador_total, 2, '.', '');
$total_factura = $subtotal + $total_iva;
?>