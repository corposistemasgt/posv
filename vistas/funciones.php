<?php
function get_cadena($user_id_iws)
{

    global $conexion, $cadena_permisos;

    $sql = "select * from  user_group,users where user_group.user_group_id=users.cargo_users and users.id_users='" . $user_id_iws . "' ";

    $query_user = mysqli_query($conexion, $sql);

    $row = mysqli_fetch_array($query_user);

    $cadena_permisos = $row['permission'];

    return $cadena_permisos;

}

function get_Sucursal($user_id_iws)
{

    global $conexion, $cadena_permisos;

    $sql = "select * from  users where id_users='" . $user_id_iws . "' ";

    $query_user = mysqli_query($conexion, $sql);

    $row = mysqli_fetch_array($query_user);

    $cadena_permisos = $row['sucursal_users'];

    return $cadena_permisos;
}





function get_row($table, $row, $id, $equal)
{
    global $conexion;
    $query = mysqli_query($conexion, "select $row from $table where $id='$equal'");
    $rw    = mysqli_fetch_array($query);
    $value = $rw[$row];
    return $value;
}

function condicion($tipo)
{
    if ($tipo == 1) {
        return 'Efectivo';
    } elseif ($tipo == 2) {
        return 'Cheque';
    } elseif ($tipo == 3) {
        return 'Tarjeta';
    } elseif ($tipo == 4) {
        return 'Crédito';
    }
}
/*--------------------------------------------------------------*/
/* MODIFICAR LOS DATOS DEL GRAFICO
/*--------------------------------------------------------------*/
function monto($table, $mes, $periodo)
{
    global $conexion;
  

    $query = mysqli_query($conexion, "select sum(monto_factura) as monto from $table where month(fecha_factura)='$mes' and year(fecha_factura)='$periodo'");
    $row   = mysqli_fetch_array($query);
    $monto = floatval($row['monto']);
    return $monto;
}
function stock($stock)
{
    if ($stock == 0) {
        return '<span class="badge badge-danger">' . $stock . '</span>';
    } else if ($stock <= 3) {
        return '<span class="badge badge-warning">' . $stock . '</span>';
    } else {
        return '<span class="badge badge-primary">' . $stock . '</span>';
    }
}
/*--------------------------------------------------------------*/
/* Funcion para obtener el total de Pacientes
/*--------------------------------------------------------------*/
function total_clientes()
{
    global $conexion;
    $orderSql       = "SELECT * FROM clientes";
    $orderQuery     = $conexion->query($orderSql);
    $countPacientes = $orderQuery->num_rows;

    echo '' . $countPacientes . '';
}
/*--------------------------------------------------------------*/
/* Funcion para obtener el total de Creditos
/*--------------------------------------------------------------*/
function total_creditos()
{
    $id_moneda    = "Q";
    $fecha_actual = date('Y-m-d');
    global $conexion;
    $orderSql     = "SELECT * FROM facturas_ventas where date(fecha_factura) = '$fecha_actual' and estado_factura=2";
    $orderQuery   = $conexion->query($orderSql);
    $totalRevenue = 0;
    while ($orderResult = $orderQuery->fetch_assoc()) {
        $totalRevenue += $orderResult['monto_factura'];
    }

    echo '' . $id_moneda . '' . number_format($totalRevenue, 2) . '';
}
/*--------------------------------------------------------------*/
/* Funcion para obtener el total de Abonos a proveedores
/*--------------------------------------------------------------*/
function total_cxp()
{
    $id_moneda    = "Q";
    $fecha_actual = date('Y-m-d');
    global $conexion;
    //---------------------------------------------------------------------------------------
    $abonoSql    = "SELECT * FROM creditos_abonos_prov where date(fecha_abono) = '$fecha_actual'";
    $abonoQuery  = $conexion->query($abonoSql);
    $total_abono = 0;
    while ($abonoResult = $abonoQuery->fetch_assoc()) {
        $total_abono += $abonoResult['abono'];
    }

    echo '' . $id_moneda . '' . number_format($total_abono, 2) . '';
}
/*--------------------------------------------------------------*/
/* Funcion para obtener el total de Abonos a proveedores
/*--------------------------------------------------------------*/
function total_cxc()
{
    $id_moneda    = "Q";
    $fecha_actual = date('Y-m-d');
    global $conexion;
    //---------------------------------------------------------------------------------------
    $abonoSql    = "SELECT * FROM creditos_abonos where date(fecha_abono) = '$fecha_actual'";
    $abonoQuery  = $conexion->query($abonoSql);
    $total_abono = 0;
    while ($abonoResult = $abonoQuery->fetch_assoc()) {
        $total_abono += $abonoResult['abono'];
    }

    echo '' . $id_moneda . '' . number_format($total_abono, 2) . '';
}
/*--------------------------------------------------------------*/
/* Funcion para obtener el total de Ingresos
/*--------------------------------------------------------------*/
function total_ingresos()
{
    $id_moneda    = "Q";
    $fecha_actual = date('Y-m-d');
    global $conexion;
    $orderSql     = "SELECT * FROM facturas_ventas where date(fecha_factura) = '$fecha_actual' and estado_factura<>3";
    $orderQuery   = $conexion->query($orderSql);
    $totalRevenue = 0;
    while ($orderResult = $orderQuery->fetch_assoc()) {
        $totalRevenue += $orderResult['monto_factura'];
    }

    echo '' . $id_moneda . '' . number_format($totalRevenue, 2) . '';
}
/*--------------------------------------------------------------*/
/* Funcion para obtener el total de Egresos
/*--------------------------------------------------------------*/
function total_egresos()
{
    $id_moneda    = "Q";
    $fecha_actual = date('Y-m-d');
    global $conexion;
    $orderSql    = "SELECT * FROM facturas_compras where date(fecha_factura) = '$fecha_actual'";
    $orderQuery  = $conexion->query($orderSql);
    $totalEgreso = 0;
    while ($orderResult = $orderQuery->fetch_assoc()) {
        $totalEgreso += $orderResult['monto_factura'];
    }

    echo '' . $id_moneda . '' . number_format($totalEgreso, 2) . '';
}
/*--------------------------------------------------------------*/
/* Funcion para obtener el total de Inventario Bajo
/*--------------------------------------------------------------*/
function poner_inventario()
{
    global $conexion;
    $lowStockSql   = "SELECT * FROM productos WHERE stock_producto <= 3 AND estado_producto = 1";
    $lowStockQuery = $conexion->query($lowStockSql);

 //   echo '' . $countLowStock . '';
}
/*--------------------------------------------------------------*/
/* Funcion para obtener las Ultimas Ventas
/*--------------------------------------------------------------*/
function latest_order()
{
    global $conexion;
    $id_moneda = "Q";

    $sql = mysqli_query($conexion, "select * from facturas_ventas where id_cliente >0 order by  id_factura desc limit 0,5");
    while ($rw = mysqli_fetch_array($sql)) {
        $id_factura     = $rw['id_factura'];
        $numero_factura = $rw['numero_factura'];

        $supplier_id       = $rw['id_cliente'];
        $sql_s             = mysqli_query($conexion, "select nombre_cliente from clientes where id_cliente='" . $supplier_id . "'");
        $rw_s              = mysqli_fetch_array($sql_s);
        $supplier_name     = $rw_s['nombre_cliente'];
        $date_added        = $rw['fecha_factura'];
        list($date, $hora) = explode(" ", $date_added);
        list($Y, $m, $d)   = explode("-", $date);
        $fecha             = $d . "-" . $m . "-" . $Y;
        $total             = number_format($rw['monto_factura'], 2);
        ?>
        <tr>
            <td><a data-toggle="tooltip" ><label class='badge badge-primary'><?php echo $numero_factura; ?></label></a></td>
            <td><?php echo $fecha; ?></td>
            <td class='text-left'><b><?php echo $id_moneda . '' . $total; ?></b></td>
        </tr>
        <?php

    }
}
/*--------------------------------------------------------------*/
/* Funcion para obtener el total de Ventas del Vendedor
/*--------------------------------------------------------------*/
function venta_users()
{
    $id_moneda    = "Q";
    $fecha_actual = date('Y-m-d');
    $users        = intval($_SESSION['id_users']);
    global $conexion;
    $orderSql   = "SELECT * FROM facturas_ventas where id_users_factura = '$users' and date(fecha_factura) = '$fecha_actual'";
    $orderQuery = $conexion->query($orderSql);
    $countOrder = $orderQuery->num_rows;

    $totalRevenue = 0;
    while ($orderResult = $orderQuery->fetch_assoc()) {
        $totalRevenue += $orderResult['monto_factura'];
    }

    echo '' . $id_moneda . '' . number_format($totalRevenue, 2) . '';
}
/*--------------------------------------------------------------*/
/* Calculo del Descuento
/*--------------------------------------------------------------*/
function rebajas($base, $dto = 0)
{
    $ahorro = ($base * $dto) / 100;
    $final  = $base - $ahorro;
    return $final;
}
/*--------------------------------------------------------------*/
/* Control de Stock
/*--------------------------------------------------------------*/
function guardar_historial($id_producto, $user_id, $fecha, $nota, $reference, $quantity, $tipo,$idsucursal)
{
    global $conexion;
    $sql = "INSERT INTO historial_productos (id_historial, id_producto, id_users, fecha_historial, nota_historial, referencia_historial, 
    cantidad_historial, tipo_historial,idsucursal)
  VALUES (NULL, '$id_producto', '$user_id', '$fecha', '$nota', '$reference', '$quantity','$tipo','$idsucursal');";
    $query = mysqli_query($conexion, $sql);
    echo $idsucursal."  ". $sql;
}



function eliminar_stock($id_producto, $quantity, $id_sucursal)
{
    global $conexion;
    $update = mysqli_query($conexion, "update stock set cantidad_stock=cantidad_stock-'$quantity' where id_producto_stock='$id_producto' and id_sucursal_stock='$id_sucursal'");
    if ($update) {
        return 1;
    } else {
        return 0;
    }

}
/*--------------------------------------------------------------*/
/* Control de KARDEX
/*--------------------------------------------------------------*/
function guardar_salidas($fecha, $id_producto, $cant_salida, $costo_salida, $total_salida, $cant_saldo, $costo_saldo, $total_saldo, $fecha_added, $users, $tipo,$idsucursal)
{
    global $conexion;

    // Depuración de los valores recibidos
    error_log("Valores recibidos: fecha=$fecha, id_producto=$id_producto, cant_salida=$cant_salida, costo_salida=$costo_salida, total_salida=$total_salida, cant_saldo=$cant_saldo, costo_saldo=$costo_saldo, total_saldo=$total_saldo, fecha_added=$fecha_added, users=$users, tipo=$tipo");

    $sql = "INSERT INTO kardex (fecha_kardex, producto_kardex, cant_salida, costo_salida, total_salida, cant_saldo, costo_saldo, total_saldo, added_kardex, users_kardex, tipo_movimiento,idsucursal)
            VALUES ('$fecha', '$id_producto', '$cant_salida', '$costo_salida', '$total_salida', '$cant_saldo', '$costo_saldo', '$total_saldo', '$fecha_added', '$users', '$tipo','$idsucursal');";

    $query = mysqli_query($conexion, $sql);

    // Verificar si la consulta tuvo éxito
    if (!$query) {
        // Registrar el error de MySQL
        error_log("Error en la consulta SQL: " . mysqli_error($conexion));
    }

    return $sql;
}
function getpermiso($permiso)
{
    $grupo=$_SESSION['grupo'];
    $per=0;
    global $conexion;
    $sql = "select valor from tbasignacionpermiso,tbpermiso where tbasignacionpermiso.idpermiso=tbpermiso.idpermiso and 
    idgrupo ='$grupo' and tbpermiso.idpermiso ='$permiso'";
    $query = mysqli_query($conexion, $sql);
    while ($row = mysqli_fetch_array($query)) 
    {
        $per=intval($row['valor']);
    }
    return $per;
}
function guardar_entradas($fecha, $id_producto, $cant_entrada, $costo_entrada, $total_entrada, $cant_saldo, $costo_promedio, $total_saldo, $fecha_added, $users, $tipo,$idsucursal)
{
    global $conexion;
    $sql = "INSERT INTO kardex (fecha_kardex, producto_kardex, cant_entrada, costo_entrada, total_entrada, cant_saldo, costo_saldo, total_saldo, added_kardex, users_kardex, tipo_movimiento,idsucursal)
  VALUES ('$fecha','$id_producto','$cant_entrada','$costo_entrada','$total_entrada', '$cant_saldo','$costo_promedio','$total_saldo','$fecha_added','$users','$tipo','$idsucursal');";
    $query = mysqli_query($conexion, $sql);

}
function formato($valor)
{
    return number_format($valor, 2);
}

function certificar($tipoFact,$nitEmisor,$nombreEmisor,$codigoEstablecimiento,$direccionEmisor,$codigoPostal,$departamento, $municipio,$nitCliente, 
$nombreCliente,$frasesyEscenarios,$usuario,$requestor,$listaArreglo,$nombreComercial,$direccion,$codigo,$resoluciones)
{
    date_default_timezone_set('America/Guatemala'); 
    $invoice_date = date('Y-m-d\TH:i:s');   
    $fechaActual = date("Y-m-d");
    $fechaVencimiento = date("Y-m-d",strtotime($fechaActual."+ 30 days"));
    $horaEmision = $invoice_date;
    $nitEmisorEntity = trim($nitEmisor);
    $totalIVA = 0;
    $totalOtro1 = 0;
    $totalOtro2 = 0;
    $ttalOtro3 = 0;
    $granTotal = 0;
$w=new XMLWriter();
$w->openMemory();
$w->startDocument('1.0','UTF-8');
    $w->startElement("dte:GTDocumento");
        $w->writeAttribute("xmlns:ds","http://www.w3.org/2000/09/xmldsig#");
        $w->writeAttribute("xmlns:dte","http://www.sat.gob.gt/dte/fel/0.2.0");
        $w->writeAttribute("xmlns:cfc","http://www.sat.gob.gt/dte/fel/CompCambiaria/0.1.0");
        $w->writeAttribute("xmlns:cex","http://www.sat.gob.gt/face2/ComplementoExportaciones/0.1.0");
        $w->writeAttribute("xmlns:cno","http://www.sat.gob.gt/face2/ComplementoReferenciaNota/0.1.0");
        $w->writeAttribute("xmlns:cfe","http://www.sat.gob.gt/face2/ComplementoFacturaEspecial/0.1.0");
        $w->writeAttribute("Version","0.1");
        $w->startElement("dte:SAT");
            $w->writeAttribute("ClaseDocumento", "dte");
            $w->startElement("dte:DTE");
                $w->writeAttribute("ID","DatosCertificados");
                    $w->startElement("dte:DatosEmision");
                        $w->writeAttribute("ID","DatosEmision");
                            $w->startElement("dte:DatosGenerales");
                                $w->writeAttribute("Tipo",$tipoFact);
                                $w->writeAttribute("FechaHoraEmision", $horaEmision);
                                $w->writeAttribute("CodigoMoneda","GTQ");
                            $w->endElement(); 
                            $w->startElement("dte:Emisor");
                                $w->writeAttribute("NITEmisor",$nitEmisorEntity);
                                $w->writeAttribute("NombreEmisor",$nombreEmisor);
                                $w->writeAttribute("CodigoEstablecimiento",$codigoEstablecimiento);
                                $w->writeAttribute("NombreComercial",$nombreComercial);
                                $afiliacion_iva = "GEN";
                                if(trim($tipoFact) == "FPEQ"){
                                    $afiliacion_iva = "PEQ";
                                }
                                $w->writeAttribute("AfiliacionIVA",$afiliacion_iva);
                                    $w->startElement("dte:DireccionEmisor");
                                        $w->startElement("dte:Direccion");
                                            $w->text($direccionEmisor);
                                        $w->endElement();
                                        $w->startElement("dte:CodigoPostal");
                                            $w->text($codigoPostal);
                                        $w->endElement();
                                        $w->startElement("dte:Municipio");
                                            $w->text($municipio);
                                        $w->endElement();
                                        $w->startElement("dte:Departamento");
                                        $w->text($departamento);
                                        $w->endElement();
                                        $w->startElement("dte:Pais");
                                        $w->text("GT");
                                        $w->endElement();
                                    $w->endElement();
                            $w->endElement();
                            $w->startElement("dte:Receptor");
                                $w->writeAttribute("IDReceptor",$nitCliente);
                                if(strlen($nitCliente) >= 12){
                                    $w->writeAttribute("TipoEspecial","CUI");
                                }
                                $w->writeAttribute("NombreReceptor",$nombreCliente);
                                $w->startElement("dte:DireccionReceptor");
                                        $w->startElement("dte:Direccion");
                                        if(strcmp($direccion,"")==0)
                                        {
                                            $w->text("ciudad");
                                        }
                                        else
                                        {
                                            $w->text($direccion);
                                        }
                                            
                                        $w->endElement();
                                        $w->startElement("dte:CodigoPostal");
                                            $w->text($codigoPostal);
                                        $w->endElement();
                                        $w->startElement("dte:Municipio");
                                            $w->text(".");
                                        $w->endElement();
                                        $w->startElement("dte:Departamento");
                                        $w->text(".");
                                        $w->endElement();
                                        $w->startElement("dte:Pais");
                                        $w->text("GT");
                                        $w->endElement();
                                    $w->endElement();

                            $w->endElement();
                            $w->startElement("dte:Frases");
                            for ($x=0;$x<count($frasesyEscenarios); $x+=2) { 
                                $w->startElement("dte:Frase");
                                    $w->writeAttribute("TipoFrase",$frasesyEscenarios[$x]);
                                    $w->writeAttribute("CodigoEscenario",$frasesyEscenarios[$x+1]);
                                $w->endElement();
                            }
                            $w->endElement();
                            $w->startElement("dte:Items");
                                for($i=0; $i<count($listaArreglo); $i++)
                                {
                                    $w->startElement("dte:Item");
                                    $w->writeAttribute("NumeroLinea",$i+1);
                                    $listaItems = $listaArreglo[$i];
                                    $w->writeAttribute("BienOServicio",$listaItems[2]);
                                    $w->startElement("dte:Cantidad");
                                    $uni=$listaItems[7];
                                    if(strcmp(trim($uni),'')==0)
                                    {
                                        $uni="UNI";
                                    }
                                    $w->text($listaItems[0]);
                                    $w->endElement();
                                    $w->startElement("dte:UnidadMedida");
                                    $w->text($uni);
                                    $w->endElement();
                                    $w->startElement("dte:Descripcion");
                                    $w->text($listaItems[1]);
                                    $w->endElement();
                                    $w->startElement("dte:PrecioUnitario");
                                    $w->text($listaItems[3]);
                                    $w->endElement();
                                    $w->startElement("dte:Precio");
                                    $w->text($listaItems[4]);
                                    $w->endElement();
                                    $w->startElement("dte:Descuento");
                                    $w->text($listaItems[6]);
                                    $w->endElement();
                                    if(trim($tipoFact) == "FACT"){
                                        $w->startElement("dte:Impuestos");
                                        $w->startElement("dte:Impuesto");
                                                $w->startElement("dte:NombreCorto");
                                                $w->text($listaItems[5][0]);
                                                $w->endElement();
                                                $w->startElement("dte:CodigoUnidadGravable");
                                                $w->text($listaItems[5][1]);
                                                $w->endElement();
                                                $w->startElement("dte:MontoGravable");
                                                $w->text($listaItems[5][2]);
                                                $w->endElement();
                                                $w->startElement("dte:MontoImpuesto");
                                                $w->text($listaItems[5][3]);
                                                $w->endElement();
                                                if (strcmp("IVA", $listaItems[5][0]) === 0){
                                                    $totalIVA = $totalIVA+$listaItems[5][3];
                                                }
                                        $w->endElement();    
                                    $w->endElement();
                                    }
                                    $w->startElement("dte:Total");
                                    $total_de_item = $listaItems[4]-$listaItems[6];
                                    $granTotal = $granTotal + $total_de_item;
                                    $w->text($total_de_item);
                                    $w->endElement();
                                    $w->endElement();
                                }
                            $w->endElement();
                            $w->startElement("dte:Totales");
                                    if($listaItems[5] === NULL)
                                    {                                
                                    }else if(trim($tipoFact) == "FACT"){
                                        $w->startElement("dte:TotalImpuestos");
                                            $w->startElement("dte:TotalImpuesto");
                                                $w->writeAttribute("NombreCorto","IVA");
                                                $w->writeAttribute("TotalMontoImpuesto",$totalIVA);
                                            $w->endElement();
                                        $w->endElement();
                                    }
                                $w->startElement("dte:GranTotal");
                                $w->text($granTotal);
                                $w->endElement();
                            $w->endElement();
                    $w->endElement();
            $w->endElement();
            $w->startElement("dte:Adenda");
            $w->startElement("Adicionales");
                $w->writeAttribute("xmlns:xsi","http://www.w3.org/2001/XMLSchema-instance");
                $w->writeAttribute("xmlns:xsd","http://www.w3.org/2001/XMLSchema");
                $w->writeAttribute("xmlns","Schema-totalletras");
            $w->startElement("TotalEnLetras");                
                $arr = explode(".", $granTotal);
                $entero = $arr[0];
                if (isset($arr[1])) 
                {
                    $decimos = strlen($arr[1]) == 1 ? $arr[1] . '0' : $arr[1];
                }
                $fmt = new NumberFormatter('es', NumberFormatter::SPELLOUT);
                if (is_array($arr)) 
                {
                    $num_word = ($arr[0]>=1000000) ? "{$fmt->format($entero)} de Quetzales" : "{$fmt->format($entero)} Quetzales";
                    if (isset($decimos) && $decimos > 0) 
                    {
                        $num_word .= " con  {$fmt->format($decimos)} Centavos";
                    }
                    else
                    {
                        $num_word .= " exactos";
                    }
                }
                $num_word =strtoupper($num_word );
                $w->text(trim($num_word));
            $w->endElement();
            $w->startElement("textoadicional");
                $w->text(trim("FACTURA EMITIDAD DESDE CORPOVENTAS"));
            $w->endElement();
            if(intval($codigo)==1)
            {
                for($i=0; $i<count($listaArreglo); $i++)
                {
                    $listaItems = $listaArreglo[$i];
                    $o=$i+1;
                    $w->startElement("valor".$o);
                    $w->text(trim($listaItems[8]));
                    $w->endElement();
                }
            }
        $w->endElement();
        $w->endElement();
        $w->endElement();
    $w->endElement();
$w->endElement();
    $var = $w->outputMemory(true);
   // echo $var;
    $xml = base64_encode($var);
    try {
        $Requestor= $requestor; 
        $Entity= $nitEmisorEntity; 
        $fecha=date("Y").'-'.date("n").'-'.date("j")."T".date("H").':'.date("i").':'.date("s");                                
        $Data3 = $fecha."-".$codigoEstablecimiento;
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://app.corposistemasgt.com/webservicefront/factwsfront.asmx?WSDL=null',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'<?xml version="1.0" encoding="UTF-8"?>
        <SOAP-ENV:Envelope xmlns:ws="http://www.fact.com.mx/schema/ws" xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
            <SOAP-ENV:Header/>
            <SOAP-ENV:Body>
                <ws:RequestTransaction>
                    <ws:Requestor>'.$Requestor.'</ws:Requestor>
                    <ws:Transaction>SYSTEM_REQUEST</ws:Transaction>
                    <ws:Country>GT</ws:Country>
                    <ws:Entity>'.$Entity.'</ws:Entity>
                    <ws:User>'.$Requestor.'</ws:User>
                    <ws:UserName>ADMINISTRADOR</ws:UserName>
                    <ws:Data1>POST_DOCUMENT_SAT</ws:Data1>
                    <ws:Data2>'.$xml.'</ws:Data2>
                    <ws:Data3>'.$Data3.'</ws:Data3>
                </ws:RequestTransaction>
            </SOAP-ENV:Body>
        </SOAP-ENV:Envelope>
        ',CURLOPT_HTTPHEADER => array('Content-Type: text/xml'),));
       
        $response = curl_exec($curl);
        curl_close($curl);
        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
        $xml = new SimpleXMLElement($response);
        $bodys = $xml->xpath('//soapBody')[0];
        $array = json_decode(json_encode((array)$bodys));             
        $n=$array->{'RequestTransactionResponse'}; 
        $n=json_encode($n);
        $r = json_decode($n);
        $r=$r->{'RequestTransactionResult'}; 
        $s=json_encode($r);
        $s = json_decode($s);
        $p=$s;
        $s=$s->{'Response'}; 
        $s=json_encode($s);
        $s = json_decode($s);
        $resultado=$s->{'Result'};
        $detalle=$s->{'Description'};
        $tiempo=$s->{'TimeStamp'};
        if(strcmp($resultado,"true")==0)
        {
            $d=$s->{'Identifier'}; 
            $d=json_encode($d);
            $d = json_decode($d);
             $retornar = array(
                'Result' => 'true',
                'serie' => $d->{'Batch'},
                'numero' => $d->{'Serial'},
                'guid' => $d->{'DocumentGUID'},
                'TimeStamp' =>$tiempo,
                'TotalIva' => $totalIVA,
                'Link' => $d->{'InternalID'},
                'HoraEmision' => $horaEmision
            ); 
        }
        else
        {
            $retornar = array(
                'Result' => 'false',
                'Description' => $detalle
            );  
        }
    }
    catch(Exception $e)
    {
        echo 'Error: ' . $e->getMessage();
    }
    return $retornar;
}
function anularFactura($guid, $nitEmisor, $motivoAnulacion, $nitCliente, $fechaAnulacion, $fechaEmision,$requestor, $idFactura, $idSucursal){
    date_default_timezone_set('America/Guatemala'); 
    $invoice_date = date('Y-m-d\TH:i:s'); 
    $fechaActual = date("Y-m-d");
    $w=new XMLWriter();
    $w->openMemory();
    $w->startDocument('1.0','UTF-8');
        $w->startElement("dte:GTAnulacionDocumento");
            $w->writeAttribute("xmlns:dte","http://www.sat.gob.gt/dte/fel/0.1.0");
            $w->writeAttribute("xmlns:ds","http://www.w3.org/2000/09/xmldsig#");
            $w->writeAttribute("xmlns:xsi","http://www.w3.org/2001/XMLSchema-instance");
            $w->writeAttribute("Version","0.1");
            $w->writeAttribute("xsi:schemaLocation","http://www.sat.gob.gt/dte/fel/0.1.0 GT_AnulacionDocumento-0.1.0.xsd");
            $w->startElement("dte:SAT");
                $w->startElement("dte:AnulacionDTE");
                    $w->writeAttribute("ID","DatosCertificados");
                    $w->startElement("dte:DatosGenerales");
                        $w->writeAttribute("FechaEmisionDocumentoAnular",$fechaEmision);
                        $w->writeAttribute("FechaHoraAnulacion",$invoice_date);
                        $w->writeAttribute("ID","DatosAnulacion");
                        $w->writeAttribute("IDReceptor",$nitCliente);
                        $w->writeAttribute("MotivoAnulacion",$motivoAnulacion);
                        $w->writeAttribute("NITEmisor",$nitEmisor);
                        $w->writeAttribute("NumeroDocumentoAAnular",$guid);
                    $w->endElement();
                $w->endElement();
            $w->endElement();
        $w->endElement();
    $w->endElement();
    $var = $w->outputMemory(true);
    $codificado64 = base64_encode($var);
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://app.corposistemasgt.com/webservicefront/factwsfront.asmx?WSDL=null',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'<?xml version="1.0" encoding="utf-8"?>
        <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
    xmlns:ws="http://www.fact.com.mx/schema/ws">
        <SOAP-ENV:Header/>
        <SOAP-ENV:Body>
            <ws:RequestTransaction>
                <ws:Requestor>'.$requestor.'</ws:Requestor>
                <ws:Transaction>SYSTEM_REQUEST</ws:Transaction>
                <ws:Country>GT</ws:Country>
                <ws:Entity>'.$nitEmisor.'</ws:Entity>
                <ws:User>'.$requestor.'</ws:User>
                <ws:UserName>ADMINISTRADOR</ws:UserName>
                <ws:Data1>VOID_DOCUMENT</ws:Data1>
                <ws:Data2>'.$codificado64.'</ws:Data2>
                <ws:Data3>XML</ws:Data3>
            </ws:RequestTransaction>
        </SOAP-ENV:Body>
        </SOAP-ENV:Envelope>',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: text/xml'
      ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
    $xml = new SimpleXMLElement($response);
    $bodys = $xml->xpath('//soapBody')[0];
    $array = json_decode(json_encode((array)$bodys));             
    $n=$array->{'RequestTransactionResponse'}; 
    $n=json_encode($n);
    $r = json_decode($n);
    $r=$r->{'RequestTransactionResult'}; 
    $s=json_encode($r);
    $s = json_decode($s);
    $p=$s;
    $s=$s->{'Response'}; 
    $s=json_encode($s);
    $s = json_decode($s);
    $resultado=$s->{'Result'};
    $detalle=$s->{'Description'};
    if(strcmp($resultado,"true")==0)
    {
        actualizar_estado_documento($idFactura, $idSucursal);
    }
    else
    {
        $detalle=base64_encode($detalle);
        echo json_encode(array("resultado"=>"false","detalle"=>$detalle));
    }
}
function actualizar_estado_documento($idFactura, $idSucursal){
    global $conexion;
    $query = mysqli_query($conexion, "select * from detalle_fact_ventas where id_factura = '$idFactura'");
    while ($row = mysqli_fetch_array($query)) {
        $id_producto = $row['id_producto'];
        $cantidad = $row['cantidad'];
        $row['importe_venta'];
        agregar_stock($id_producto, $cantidad, $idSucursal);
    }
    $update = mysqli_query($conexion, "update facturas_ventas set estado_documento= 'anulado', estado_factura = '3' where id_factura = '$idFactura'");
    echo json_encode(array("resultado"=>"true","detalle"=>"Exito"));
}
function agregar_stock($id_producto, $quantity, $id_sucursal)
{
    global $conexion;
    $update = mysqli_query($conexion, "update stock set cantidad_stock=cantidad_stock+'$quantity' where id_producto_stock='$id_producto' and id_sucursal_stock = '$id_sucursal'");
    if ($update) {
        return 1;
    } else {
        return 0;
    }
}
