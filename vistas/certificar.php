
<?php 
    ini_set('display_errors', '1');
    session_start();
    include "db.php";
    include "php_conexion.php";
    include "funciones.php";
    $id_factura = intval($_GET['id_factura']);
    $id_sucursal= intval($_GET['idsucursal']);
    $sql    = mysqli_query($conexion, "select * from facturas_ventas left join detalle_fact_ventas on 
    facturas_ventas.id_factura = detalle_fact_ventas.id_factura left join productos on productos.id_producto = 
    detalle_fact_ventas.id_producto left join users on users.id_users = facturas_ventas.id_vendedor where 
    facturas_ventas.id_factura = '" . $id_factura . "'");
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
        $direccion = $row['factura_direccion_cliente'];
        $esGenerico = $row['esGenerico'];
        $tipoFact =  "FACT";
        $estadoFactura = $row['estado_factura'];
        $batchExistente = $row['serie_factura'];
        $certificacionExistente = $row['numero_certificacion'];
        $guidExistente = $row['guid_factura'];
        $gtotal = $row['monto_factura'];
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
        }
        $itemsVenta = array();
        array_push($itemsVenta, $cantidad,$nombre_producto,"B", $precio_venta_uni,
            $precio_venta_sub,$impuestosDelProducto,$monto_descuento,$medida,$codigo_producto);
        array_push($listaProductos,$itemsVenta);
    }
    $frasesyEscenarios =  array();
    $resoluciones =  array();
    $sql    = mysqli_query($conexion, "select * from perfil where id_perfil = '".$id_sucursal."'");
    while ($row = mysqli_fetch_array($sql)) 
    {
        $id     = $row["id_perfil"];    
        $nombreEmisor          = $row["nombre_empresa"];
        $nombreComercial       = $row["giro_empresa"];
        $direccionEmisor       = $row["direccion"];
        $departamento          = $row["estado"];
        $municipio             = $row["ciudad"];
        $codigoEstablecimiento = $row["codigoEstablecimiento"];
    }
    $nitEmisor             = $_SESSION['nit'];
    $requestor             = $_SESSION['requestor'];
    $regimen               = $_SESSION['regimen'];
    $codigos               = $_SESSION['imprimir_codigo'] ;
    $sql    = mysqli_query($conexion, "select * from tbfrase");
    while ($row = mysqli_fetch_array($sql)) 
    {
        array_push($frasesyEscenarios,$row["frase"],$row["escenario"]);
    }
    $sql1    = mysqli_query($conexion, "select * from tbresolucion");
    while ($row = mysqli_fetch_array($sql1)) 
    {
        array_push($resoluciones,$row["fecha"],$row["relacion"]);
    }
    $codigoPostal = "16001";
    $esCF = false;
    if(strcmp($regimen,'PEQ')===0)
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
    if($boolTieneExentos  === true)
    {
        array_push($frasesyEscenarios,"4","9");
    }                          
    $usuario = "ADMINISTRADOR";
    if($estadoFactura == 3 && trim($guidExistente) === "")
    {
        return;
    }
    else if(trim($batchExistente) === "" && trim($certificacionExistente) === "")
    {     
        $var = certificar($tipoFact,$nitEmisor,$nombreEmisor,$codigoEstablecimiento,$direccionEmisor,
        $codigoPostal, $departamento, $municipio,$nitCliente, $nombreCliente,$frasesyEscenarios,$usuario,
        $requestor,$listaProductos,$nombreComercial,$direccion,$codigos,$resoluciones);   
        if(strcmp($var['Result'],'true')==0)
        {
            $serie = $var['serie'];
            $numero_factura = $var['numero'];
            $guid = $var['guid'];
            $FechaCertificacion = $var['TimeStamp'];
            $horaEmision = $var['HoraEmision']; 
            $link = $var['Link'];                                                                                                                 
            $sql   = mysqli_query($conexion, "UPDATE facturas_ventas set serie_factura = '".$serie."', 
            guid_factura = '".$guid."', factura_nombre_cliente = '".$nombreCliente."', 
            factura_nit_cliente = '".$nitCliente."', numero_certificacion = '".$numero_factura."', 
            fechaCertificacion = '".$FechaCertificacion."', fecha_emision = '".$horaEmision."' where id_factura = '" . $id_factura . "'");
            echo json_encode(array("resultado"=>"true","link"=>$link,"receptor"=>$nombreCliente,
            "emisor"=>$nombreEmisor,"nitemisor"=>$nitEmisor,"requestor"=>$requestor,"guid"=>$guid,
            "fecha"=>$FechaCertificacion,"tipo"=>$tipoFact,"total"=>$gtotal));
        }
        else
        {
            $descripcion = $var['Description'];
            $descripcion=base64_encode($descripcion);
            echo json_encode(array("resultado"=>"false","descripcion"=>$descripcion));
        }
     }
     else
     {
        $batch =$batchExistente;
        $numero_factura = $certificacionExistente;
        $guid = $guidExistente;
     }
?>
