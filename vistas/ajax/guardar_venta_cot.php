<?php
include 'is_logged.php'; 
if (empty($_POST['id_cliente'])&& 1!=1  ) {
    $errors[] = "ID VACIO";
} else if (!empty($_POST['id_cliente'])|| 1==1)  {
    require_once "../db.php";
    require_once "../php_conexion.php";
    require_once "../funciones.php";
    $session_id     = session_id();
    $simbolo_moneda = "Q";
    $id_cliente        = 0  ;
    $id_vendedor       = intval($_SESSION['id_users']);
    $users             = intval($_SESSION['id_users']);
    $condiciones       = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['condiciones'], ENT_QUOTES)));
    $numero_cotizacion = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['cotizacion'], ENT_QUOTES)));
    $tip_doc           = intval($_POST['tip_doc']);
    $trans             = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['trans'], ENT_QUOTES)));
    $resibido          = floatval($_POST['resibido']);
    $date_added        = date("Y-m-d H:i:s");
    $cliente           =$_POST['cliente'];
    $nit               =$_POST['nit'];
    $sucursal = $_SESSION['idsucursal'];
    if ($condiciones == 4) {
        $estado = 2;
    } else {
        $estado = 1;
    }
    $sql        = mysqli_query($conexion, "select LAST_INSERT_ID(id_factura) as last from facturas_ventas order by id_factura desc limit 0,1 ");
    $rw         = mysqli_fetch_array($sql);
    $id_factura = $rw['last'] + 1;
    $query_id = mysqli_query($conexion, "SELECT RIGHT(numero_factura,6) as factura FROM facturas_ventas ORDER BY factura DESC LIMIT 1")
    or die('error ' . mysqli_error($conexion));
    $count = mysqli_num_rows($query_id);

    if ($count != 0) {

        $data_id = mysqli_fetch_assoc($query_id);
        $factura = $data_id['factura'] + 1;
    } else {
        $factura = 1;
    }
    $buat_id = str_pad($factura, 6, "0", STR_PAD_LEFT);
    $factura = "CFF-$buat_id";
    $nums          = 1;
    $sumador_total = 0;
    $sum_total     = 0;
    $sql           = mysqli_query($conexion, "select * from productos, detalle_fact_cot where productos.id_producto=detalle_fact_cot.id_producto and detalle_fact_cot.numero_factura='" . $numero_cotizacion . "'");
    while ($row = mysqli_fetch_array($sql)) {
        $id_tmp          = $row["id_detalle"];
        $id_producto     = $row['id_producto'];
        $codigo_producto = $row['codigo_producto'];
        $cantidad        = $row['cantidad'];
        $desc_tmp        = $row['desc_venta'];
        $nombre_producto = $row['nombre_producto'];
        $precio_venta   = $row['precio_venta'];
        $costo_producto = $row['costo_producto'];
        $precio_venta_f = number_format($precio_venta, 2);
        $precio_venta_r = str_replace(",", "", $precio_venta_f); 
        $precio_total   = $precio_venta_r * $cantidad;
        $final_items    = rebajas($precio_total, $desc_tmp); 
        $precio_total_f = number_format($final_items, 2);
        $precio_total_r = str_replace(",", "", $precio_total_f); 
        $sumador_total += $precio_total_r; 
        if ($resibido < $sumador_total and $condiciones != 4 and $condiciones != 5) {
            echo "<script>
            swal({
              title: 'PAGO RECIBIDO ES MENOR AL MONTO TOTAL',
              text: 'Intentar Nuevamente',
              type: 'error',
              confirmButtonText: 'ok'
          })</script>";
            exit;
        }
$insert_detail = mysqli_query($conexion, "INSERT INTO detalle_fact_ventas VALUES (NULL, '$id_factura', '$factura', '$id_producto', '$cantidad', '$desc_tmp', '$precio_venta_r', '$precio_total')");
$saldo_total = $cantidad * $costo_producto;
$sql_kardex  = mysqli_query($conexion, "SELECT * FROM kardex WHERE producto_kardex='" . $id_producto . "' ORDER BY id_kardex DESC LIMIT 1");
if ($sql_kardex) {
    $rww = mysqli_fetch_array($sql_kardex);
    if ($rww) {
        $id_producto = $rww['producto_kardex'];
        $costo_saldo = $rww['costo_saldo'];
        $cant_saldo  = $rww['cant_saldo'] - $cantidad;
        $nuevo_saldo = $cant_saldo * $costo_producto;
        $tipo        = 2;
        if (!empty($id_producto) && !empty($cantidad) && !empty($costo_producto) && !empty($saldo_total) && !empty($cant_saldo) && !empty($costo_saldo) && !empty($nuevo_saldo) && !empty($date_added) && !empty($users) && !empty($tipo)) {
            guardar_salidas($date_added, $id_producto, $cantidad, $costo_producto, $saldo_total, $cant_saldo, $costo_saldo, $nuevo_saldo, $date_added, $users, $tipo,$sucursal);
        } else {
           error_log("Uno o más valores son null o están vacíos. Por favor verifica los datos: id_producto=$id_producto, cantidad=$cantidad, costo_producto=$costo_producto, saldo_total=$saldo_total, cant_saldo=$cant_saldo, costo_saldo=$costo_saldo, nuevo_saldo=$nuevo_saldo, date_added=$date_added, users=$users, tipo=$tipo");
        }
    } else {
        error_log("No se encontró ningún registro en la tabla kardex para el producto con id_producto=$id_producto");
    }
} else {
    error_log("Error en la consulta SQL: " . mysqli_error($conexion));
}
        $sqlCantAnterior = "select * from stock where id_sucursal_stock = '$sucursal' AND id_producto_stock = '$id_producto'";
        $sql3    = mysqli_query($conexion, $sqlCantAnterior);
        $rw3      = mysqli_fetch_array($sql3);
        $old_qty = $rw3['cantidad_stock'];
        $new_qty = $old_qty - $cantidad;

$update = "update stock set cantidad_stock = '$new_qty' where id_sucursal_stock = '$sucursal' AND id_producto_stock = '$id_producto' ";
$sql3    = mysqli_query($conexion, $update);
        $nums++;
    }
    $subtotal         = number_format($sumador_total, 2, '.', '');
    $total_factura    = $subtotal ;
    $cambio           = $resibido - $total_factura;
    $saldo_credito    = $total_factura - $resibido;
    $camb             = number_format($cambio, 2);
    $resibido_formato = number_format($resibido, 2);
    if ($condiciones == 4 || $condiciones == 5) {
        $insert_prima = mysqli_query($conexion, "INSERT INTO creditos VALUES (NULL,'$factura','$date_added','$id_cliente','$id_vendedor','$total_factura','$saldo_credito','1','$users','1')");
        $insert_abono = mysqli_query($conexion, "INSERT INTO creditos_abonos VALUES (NULL,'$factura','$date_added','$id_cliente','$total_factura','$resibido','$saldo_credito','$users','1','CREDITO INICAL')");
    }
    $insert = mysqli_query($conexion, "INSERT INTO facturas_ventas (numero_factura,fecha_factura,id_cliente,id_vendedor,condiciones,
    monto_factura,estado_factura,id_users_factura,dinero_resibido_fac,id_sucursal,id_comp_factura,num_trans_factura,tipoDocumento,
    factura_nombre_cliente,factura_nit_cliente) VALUES ('$factura','$date_added','$id_cliente','$id_vendedor','$condiciones',
    '$total_factura','$estado','$users','$resibido','1','1','$trans','FACT','$cliente','$nit')");
     $idf=$_SESSION['id_factura'];
     $update = mysqli_query($conexion, "UPDATE facturas_cot set estado_factura=2 where id_factura='$idf'");
    $idfactura=0;
    $sql            = mysqli_query($conexion, "select id_factura from facturas_ventas where numero_factura='$factura'");
    while ($row = mysqli_fetch_array($sql)) {
        $idfactura=$row['id_factura']; 
    }
    if ($condiciones == 4 || $condiciones == 5) {
        echo "<script>
        swal({
          title: 'VENTA AL CREDITO GUARDADA CON EXITO CON ANTICIPO DE: $simbolo_moneda $resibido_formato',
          text: 'Factura: $factura',
          type: 'success',
          confirmButtonText: 'ok'
      })
  </script>";
        exit;
    }
    if ($insert_detail) {
        echo "<script> $('#modal_vuelto').modal('show');</script>";
    } else {
        $errors[] = "Lo siento algo ha salido mal intenta nuevamente." . mysqli_error($conexion);
        echo "<script>
        swal({
          title: 'Error: $simbolo_moneda $resibido_formato',
          text: 'Factura: $factura',
          type: 'success',
          confirmButtonText: 'ok'
      }) </script>";
    }
} else {
    $errors[] = "Error desconocido.";
}
if (isset($errors)) {
    ?>
    <div class="alert alert-danger" role="alert">
        <strong>Error!</strong>
        <?php
foreach ($errors as $error) {
        echo $error;
    }
    ?>
    </div>
    <?php
}
if (isset($messages)) {
    ?>
    <div class="alert alert-success" role="alert">
        <strong>¡Bien hecho!</strong>
        <?php
foreach ($messages as $message) {
        echo $message;
    }
    ?>
    </div>
    <?php
}
?> 
<script type="text/javascript" src="../../js/bitacora_ventas.js?ver=1.0"></script>
<div class="modal fade" id="modal_vuelto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class='fa fa-edit'></i> FACTURA: <?php echo $factura; ?></h4>
            </div>
            <div class="modal-body" align="center">
                <strong><h3><?php echo "Cambio ". $cambio ?> </h3></strong>
                <div class="alert alert-info" align="center">
                    <strong><h1>
                        <?php echo $simbolo_moneda . ' ' . $camb; ?>
                    </h1></strong>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="imprimir" class="btn btn-primary btn-block btn-lg waves-effect waves-light" onclick="printOrder('1');" accesskey="t" ><span class="fa fa-print"></span> Ticket</button><br>
                <button type="button" id="imprimir2" class="btn btn-success btn-block btn-lg waves-effect waves-light" onclick="imprimir_factura(<?php echo $idfactura;?>,1,<?php echo $sucursal;?>);" accesskey="p"><span class="fa fa-print"></span> Factura</button>
            </div>
        </div>
    </div>
</div>