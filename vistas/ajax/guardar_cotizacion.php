<?php
include 'is_logged.php';
if (empty($_POST['id_cliente']) && 1 !=1 ) {
    $errors[] = "ID VACIO";
} else if (!empty($_POST['id_cliente'])||  1 == 1) {
    require_once "../db.php";
    require_once "../php_conexion.php";
    require_once "../funciones.php";
    $session_id     = session_id();
    $tipo=$_SESSION["cotizacion_carta"];
    $simbolo_moneda = "Q";
    $sql_count = mysqli_query($conexion, "select * from tmp_cotizacion where session_id='" . $session_id . "'");
    $count     = mysqli_num_rows($sql_count);
    if ($count == 0) {
        echo "<script>
        swal({
          title: 'No hay Productos agregados en la factura',
          text: 'Intentar nuevamente',
          type: 'error',
          confirmButtonText: 'ok'
      })</script>";
        exit;
    }
    $id_cliente     = intval($_POST['id_cliente']);
    $id_vendedor    = intval($_SESSION['id_users']);
    $users          = intval($_SESSION['id_users']);
    $condiciones    = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['condiciones'], ENT_QUOTES)));
    $numero_factura = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST["factura"], ENT_QUOTES)));
    $validez        = floatval($_POST['validez']);
    $date_added     = date("Y-m-d H:i:s");
    $nombre_cliente = $_POST['nombre_cliente']; 
    $nit_cliente    = strtoupper(($_POST['tel1']));
    if ($condiciones == 4) {
        $estado = 2;
    } else {
        $estado = 1;
    } 
    $sql        = mysqli_query($conexion, "select LAST_INSERT_ID(id_factura) as last from facturas_cot order by id_factura desc limit 0,1 ");
    $rw         = mysqli_fetch_array($sql);
    $id_factura = $rw['last'] + 1;
    $query_id = mysqli_query($conexion, "SELECT RIGHT(numero_factura,6) as factura FROM facturas_cot ORDER BY factura DESC LIMIT 1")
    or die('error ' . mysqli_error($conexion));
    $count = mysqli_num_rows($query_id);
    if ($count != 0) {

        $data_id = mysqli_fetch_assoc($query_id);
        $factura = $data_id['factura'] + 1;
    } else {
        $factura = 1;
    }
    $buat_id = str_pad($factura, 6, "0", STR_PAD_LEFT);
    $factura = "COT-$buat_id";
    $nums          = 1;
    $sumador_total = 0;
    $sum_total     = 0;
    $sql           = mysqli_query($conexion, "select * from productos, tmp_cotizacion where productos.id_producto=tmp_cotizacion.id_producto and tmp_cotizacion.session_id='" . $session_id . "'");
    while ($row = mysqli_fetch_array($sql)) {
        $id_tmp          = $row["id_tmp"];
        $id_producto     = $row['id_producto'];
        $codigo_producto = $row['codigo_producto'];
        $cantidad        = $row['cantidad_tmp'];
        $desc_tmp        = $row['desc_tmp'];
        $nombre_producto = $row['nombre_producto'];
        $precio_venta   = $row['precio_tmp'];
        $precio_venta_f = number_format($precio_venta, 2); 
        $precio_venta_r = str_replace(",", "", $precio_venta_f); 
        $precio_total   = $precio_venta_r * $cantidad;
        $final_items    = rebajas($precio_total, $desc_tmp); 
        $precio_total_f = number_format($final_items, 2); 
        $precio_total_r = str_replace(",", "", $precio_total_f); 
        $sumador_total += $precio_total_r;
        $insert_detail = mysqli_query($conexion, "INSERT INTO detalle_fact_cot VALUES (NULL,'$id_factura','$factura','$id_producto','$cantidad','$desc_tmp','$precio_venta_r')");
    }
    $subtotal      = number_format($sumador_total, 2, '.', '');
    $total_factura = $subtotal;
    $sql = "SELECT * FROM clientes WHERE fiscal_cliente ='" . $nit_cliente . "';";
    $query_check_user_name = mysqli_query($conexion, $sql);
    while ($row = mysqli_fetch_array($query_check_user_name)) {
       $id_cliente = $row['id_cliente'];
    }
    $insert        = mysqli_query($conexion, "INSERT INTO facturas_cot VALUES 
    ('$id_factura','$factura','$date_added','$id_cliente','$id_vendedor','$condiciones','$total_factura','$estado','$users','$validez','1','$nombre_cliente','$nit_cliente','')");
    $delete        = mysqli_query($conexion, "DELETE FROM tmp_cotizacion WHERE session_id='" . $session_id . "'");
    if ($insert_detail) {
        echo "<script>
    $('#modal_cot').modal('show');
</script>";
    } else {
        $errors[] = "Lo siento algo ha salido mal intenta nuevamente." . mysqli_error($conexion);
    }
} else {
    $errors[] = "Error desconocido.".$_POST['nombre_cliente'].$_POST['tel1'];
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
<div class="modal fade" id="modal_cot" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class='fa fa-edit'></i> INFORMACIÓN</h4>
            </div>
            <div class="modal-body" align="center">
                <strong><h3>NO. COTIZACION</h3></strong>
                <div class="alert alert-info" align="center">
                    <strong><h1>
                        <?php echo $factura; ?>
                    </h1></strong> 
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="imprimir2" class="btn btn-success btn-block btn-lg waves-effect waves-light" onclick="printFactura('<?php echo $factura; ?>','<?php echo $tipo; ?>');"  accesskey="p"><span class="fa fa-print"></span> IMPRIMIR</button>
            </div>
        </div>
    </div>
</div>