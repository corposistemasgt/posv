<?php
include 'is_logged.php'; 
if (empty($_POST['id_cliente']) && 1 != 1) {
    $errors[] = "ID VACIO";
} 
else if (!empty($_POST['id_cliente']) ||  1 == 1) 
{ 
    require_once "../db.php";
    require_once "../php_conexion.php";
    require_once "../funciones.php";
    $sql = "SELECT * FROM clientes WHERE fiscal_cliente ='" . $nit_cliente . "';";
    $query_check_user_name = mysqli_query($conexion, $sql);
    $id_cliente = "0";
    while ($row = mysqli_fetch_array($query_check_user_name)) 
    {
         $id_cliente = $row['id_cliente'];
    }
    $session_id     = session_id();
    $simbolo_moneda = "Q";
    $sql_count = mysqli_query($conexion, "select * from tmp_ventas where session_id='" . $session_id . "'");
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
    $id_comp        = intval($_POST['id_comp']);
    $tipo_doc       = intval($_POST['tip_doc']);
    $users          = intval($_SESSION['id_users']);
    $condiciones    = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['condiciones'], ENT_QUOTES)));
    $numero_factura = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST["factura"], ENT_QUOTES)));
    $trans          = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST["trans"], ENT_QUOTES)));
    $date_added     = date("Y-m-d H:i:s");
    $nombre_cliente = $_POST['nombre_cliente'];
    $direccion_cliente = $_POST['direccion_cliente'];
    $correo_cliente = $_POST['correo_cliente'];
    $telefono_cliente = $_POST['telefono_cliente'];
    $nit_cliente    = strtoupper(($_POST['rnc']));
    $numero_transferencia = "01";
    if ($condiciones == 4 || $condiciones == 5 || $tipo_doc == 3) {
        $estado = 2;
    }
    else {
        $estado = 1;
    }
    if($condiciones == 2){
    }
    $sql        = mysqli_query($conexion, "select LAST_INSERT_ID(id_factura) as last from facturas_ventas order by id_factura desc limit 0,1 ");
    $rw         = mysqli_fetch_array($sql);
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
    $sql           = mysqli_query($conexion, "select * from productos, tmp_ventas where productos.id_producto=tmp_ventas.id_producto and tmp_ventas.session_id='" . $session_id . "'");
    $id_vendedor    = intval($_SESSION['id_users']);
    $id_sucursal = intval($_SESSION['idsucursal']);
    $rutero = $rw['rutero'];
    if(intval($rutero)==0)
    {
        $id_vendedor    = intval($_SESSION['id_users']);
    }
    else
    {
        $sqlUsuarioACT        = mysqli_query($conexion, "select id_cliente from users,
        clientes where users.idruta =clientes.idruta and clientes.id_cliente ='".$id_cliente."'");
        $rw         = mysqli_fetch_array($sqlUsuarioACT);
        $id_vendedor = $rw['id_cliente'];
    }
    $fecha_factura = date('Y-m-d H:i:s');
    $insert = mysqli_query($conexion, "INSERT INTO facturas_ventas(id_factura,numero_factura,
    fecha_factura, estado_factura) VALUES (NULL, '1234', '$fecha_factura', '1')");
    $id_factura = mysqli_insert_id($conexion);
    while ($row = mysqli_fetch_array($sql)) {
        $id_tmp          = $row["id_tmp"];
        $id_producto     = $row['id_producto'];
        $codigo_producto = $row['codigo_producto'];
        $cantidad        = $row['cantidad_tmp'];
        $desc_tmp        = $row['desc_tmp'];
        $nombre_producto = $row['nombre_producto'];
        $precio_venta   = $row['precio_tmp'];
        $costo_producto = $row['costo_producto'];
        $precio_venta_f = number_format($precio_venta, 2); 
        $precio_venta_r = str_replace(",", "", $precio_venta_f); 
        $precio_total   = $precio_venta_r * $cantidad;
        $final_items    = rebajas($precio_total, $desc_tmp);
        $precio_total_f = number_format($final_items, 2); 
        $precio_total_r = str_replace(",", "", $precio_total_f);
        $sumador_total += $precio_total_r;
        if($condiciones == 2 || $condiciones == 6 || $condiciones == 3){
            $numero_transferencia       = $_POST['resibido'];
            $resibido         = $sumador_total;
        }else{
            $resibido       = floatval($_POST['resibido']);
        }
        if ($resibido < $sumador_total and $condiciones != 4 and $condiciones != 5 and $condiciones != 2 and $condiciones !=6 and $tipo_doc != 3) {
            echo "<script>
            swal({
              title: 'PAGO RECIBIDO ES MENOR AL MONTO TOTAL',
              text: 'Intentar Nuevamente',
              type: 'error',
              confirmButtonText: 'ok'
          })</script>";
            exit;
        }
        $insert_detail = mysqli_query($conexion, "INSERT INTO detalle_fact_ventas VALUES 
        (NULL,'$id_factura','$numero_factura','$id_producto','$cantidad','$desc_tmp','$precio_venta_r','$final_items')");  
        $saldo_total = $cantidad * $costo_producto;
        $sql_kardex  = mysqli_query($conexion, "select * from kardex where producto_kardex='" . $id_producto . "' order by id_kardex DESC LIMIT 1");
        $rww         = mysqli_fetch_array($sql_kardex);
        $costo_saldo = $rww['costo_saldo'];
        $cant_saldo  = $rww['cant_saldo'] - $cantidad;
        $nuevo_saldo = $cant_saldo * $costo_producto;
        $tipo        = 2;
        if(strcmp($costo_saldo,'')==0){$costo_saldo=0;}
        guardar_salidas($date_added, $id_producto, $cantidad, $costo_producto, $saldo_total, $cant_saldo, $costo_saldo, $nuevo_saldo, $date_added, $users, $tipo,$id_sucursal);
        $sql2    = mysqli_query($conexion, "select * from productos where id_producto='" .$id_producto ."'");
        $rw      = mysqli_fetch_array($sql2);
        $maneja_inventario = $rw['inv_producto'];
        if($maneja_inventario == 0)
        {
            $sqlCantAnterior = "select * from stock where id_sucursal_stock = '$id_sucursal' AND id_producto_stock = '$id_producto'";
            $sql3    = mysqli_query($conexion, $sqlCantAnterior);
            $rw3      = mysqli_fetch_array($sql3);
            $ban = 0;
            $seguro = 0;
            $mensaje="";
            while($ban<=0) 
            {
                $old_qty = $rw3['cantidad_stock'];
                $new_qty = $old_qty - $cantidad;
                $update = "update stock set cantidad_stock = '$new_qty' where id_sucursal_stock = '$id_sucursal' 
                AND id_producto_stock = '$id_producto' ";
                $sql3    = mysqli_query($conexion, $update);
                $sqlCantAnterior = "select * from stock where id_sucursal_stock = '$id_sucursal' AND id_producto_stock = '$id_producto'";
                $sql3    = mysqli_query($conexion, $sqlCantAnterior);
                $rw3      = mysqli_fetch_array($sql3);
                $old = $rw3['cantidad_stock']; 
                if($old_qty>$old)
                {
                    $ban=10;
                }
                $seguro++;
                if($seguro==10)
                {
                    $ban=10;
                    $mensaje="Hubo un error al intentar hace el descuento";
                }
            }
        }
        $nums++;
    }
    echo $mensaje;
    $subtotal         = number_format($sumador_total, 2, '.', '');
    $total_factura    = $subtotal;
    $cambio           = $resibido - $total_factura;
    $saldo_credito    = $total_factura - $resibido;
    $camb             = number_format($cambio, 2);
    $resibido_formato = number_format($resibido, 2);
    $sql = "SELECT * FROM clientes WHERE fiscal_cliente ='" . $nit_cliente . "';";
    $query_check_user_name = mysqli_query($conexion, $sql);
    $id_cliente = "0";
    while ($row = mysqli_fetch_array($query_check_user_name)) {
         $id_cliente = $row['id_cliente'];
    }
    echo $condiciones;
    if ($condiciones == 4 or $condiciones == 5 || $tipo_doc == 3) {
        $sq="INSERT INTO creditos_abonos VALUES (NULL,'$numero_factura','$date_added','$id_cliente','$total_factura','$resibido','$saldo_credito','$users','1','CREDITO INICAL','0','0')";
        $insert_prima = mysqli_query($conexion, "INSERT INTO creditos VALUES 
        (NULL,'$numero_factura','$date_added','$id_cliente','$id_vendedor','$total_factura',
        '$saldo_credito','1','$users','1','0')");
        $insert_abono = mysqli_query($conexion, $sq);  
    }

    $nit_cliente = trim($nit_cliente);
    $nombre_cliente = trim($nombre_cliente);
    if($numero_transferencia != null and $numero_transferencia != ""){
        $agregarCheque = ", num_cheque = '$numero_transferencia' ";
    }else{
        $agregarCheque = "";
    }
    $consultaUpdate = "UPDATE facturas_ventas SET numero_factura = '$numero_factura', fecha_factura = '$date_added', 
    id_cliente = '$id_cliente', id_vendedor = '$id_vendedor',  
    condiciones = '$condiciones', monto_factura ='$total_factura',  
    estado_factura = '$estado', id_users_factura = '$users', 
    dinero_resibido_fac = '$resibido', id_sucursal = '$id_sucursal', 
    id_comp_factura = '$id_comp', num_trans_factura = '$trans', 
    factura_nombre_cliente = '$nombre_cliente', 
    factura_nit_cliente = '$nit_cliente', factura_direccion_cliente = '$direccion_cliente', 
    factura_numero_cliente = '$telefono_cliente',
    tipoDocumento = '$tipo_doc' where id_factura = '$id_factura'";
    $insert = mysqli_query($conexion, $consultaUpdate);    
    $idVenta = $id_factura;
    $delete = mysqli_query($conexion, "DELETE FROM tmp_ventas WHERE session_id='" .$session_id. "'");
    if ($insert_detail) {
        echo "<script>
        $('#outer_comprobante').load('../ajax/carga_correlativos.php');
        $('#resultados5').load('../ajax/carga_num_trans.php')
    $('#modal_vuelto').modal('show');
    console.log('corpoprint-cajaAbriendo');
</script>";
    } else {
        $errors[] = "Lo siento algo ha salido mal intenta nuevamente." . mysqli_error($conexion);
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
<script type="text/javascript" src="../../js/venta.js?ver=1.4"></script>
<div class="modal fade" id="modal_vuelto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class='fa fa-edit'></i> FACTURA: <?php echo $numero_factura; ?></h4>
            </div>
            <div class="modal-body" align="center"> 
                <strong><h3>CAMBIO:</h3></strong> 
                <div class="alert alert-info" align="center"> 
                    <strong><h1> 
                        <?php echo $simbolo_moneda . ' ' . $camb; ?>
                    </h1></strong>
                </div>
            </div>
            <div class="col-md-8" style="text-align: center; margin: 0 auto;">
                <div  >
				    <div class="form-group">
                        
					    <label  for="fiscal">Telefono para Enviar Whatsapp</label>
					    <input type="text" class="form-control" autocomplete="off" id="tel" name="tel" value="<?php echo $telefono_cliente;?>">
				    </div>
			    </div>
                <div   >
				    <div class="form-group">
					    <label  for="fiscal">Correo Electronico</label>
					    <input type="text" class="form-control" autocomplete="off" id="corr" name="corr" value="<?php echo $correo_cliente;?>">
				    </div>
			    </div>
            </div>
            <div class="modal-footer"> 
                <div class="col-md-12">

                <?php if($_SESSION['ticket']==0)
                {
                    ?>
                    <div class="form-group row" style="margin: auto;">
                       
                       <div class="col-md-12">
                           <button type="button" id="imprimir2" class="btn btn-success btn-block btn-lg waves-effect waves-light" onclick="imprimir_factura(<?php echo $idVenta; ?>,1,<?php echo $id_sucursal; ?>);" accesskey="p"><span class="fa fa-print"></span> Factura</button>            
                       </div>
                   </div>
                       <?php
                }
                else
                {
                    ?>
                 <div class="form-group row" style="margin: auto;">
                    <div class="col-md-6">
                        <button type="button" id="imprimir" class="btn btn-primary btn-block btn-lg waves-effect waves-light" onclick="print_ticket(<?php echo $idVenta; ?>,1,<?php echo $_SESSION['comprobante_carta']?>);" accesskey="t" ><span class="fa fa-print"></span> Ticket</button><br>             
                    </div>
                    <div class="col-md-6">
                        <button type="button" id="imprimir2" class="btn btn-success btn-block btn-lg waves-effect waves-light" onclick="imprimir_factura(<?php echo $idVenta; ?>,1,<?php echo $id_sucursal; ?>);" accesskey="p"><span class="fa fa-print"></span> Factura</button>            
                    </div>
                </div>
                    <?php
                }
                ?>
               
            </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function(){
			$('#modal_vuelto').on('hidden.bs.modal', function () {
                if ('Android'==isMobile())
				{
                    console.log("corpoprint-recargar");
                }
                else
                {
                    location.reload();
                }
            	});
        });
</script>
<script>
    function isMobile(){
    return (
        (navigator.userAgent.match(/Android/i)) ||
        (navigator.userAgent.match(/webOS/i)) ||
        (navigator.userAgent.match(/iPhone/i)) ||
        (navigator.userAgent.match(/iPod/i)) ||
        (navigator.userAgent.match(/iPad/i)) ||
        (navigator.userAgent.match(/BlackBerry/i))
    );
}
    </script>