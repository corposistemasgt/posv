<?php
include 'is_logged.php'; 
if (empty($_POST['id_proveedor'])) {
    $errors[] = "ID VACIO";
} else if (!empty($_POST['id_proveedor'])) {
    require_once "../db.php";
    require_once "../php_conexion.php";
    require_once "../funciones.php";
    $session_id = session_id();
    $sql_count = mysqli_query($conexion, "select * from tmp_compra where session_id='" . $session_id . "'");
    $count     = mysqli_num_rows($sql_count);
    if ($count == 0) {
        echo "<script>
  swal('NO HAY PRODUCTOS AGREGADOS EN LA FACTURA', 'INTENTAR DE NUEVO', 'error')
  </script>";
        exit;
    }
    $id_proveedor = intval($_POST['id_proveedor']);
    $id_vendedor  = intval($_SESSION['id_users']);
    $users        = intval($_SESSION['id_users']);
    $condiciones  = intval($_POST['condiciones']);
    $sucursal  = intval($_POST['sucursals']);
    $factura      = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST["factura"], ENT_QUOTES)));
    $referencia   = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST["ref"], ENT_QUOTES)));
    $resibido     = floatval($_POST['resibido']);
    $fecha        = $_POST["fecha"];
    $sql        = mysqli_query($conexion, "select * from users where id_users = '".$id_vendedor."'"); //obtener el usuario activo 1aqui1
    $rw         = mysqli_fetch_array($sql);
    $id_sucursal = $rw['sucursal_users'];
    if ($condiciones == 4) {
        $estado = 2;
    } else {
        $estado = 1;
    }
    $sql                   = "SELECT * FROM facturas_compras WHERE numero_factura ='" . $factura . "';";
    $query_check_user_name = mysqli_query($conexion, $sql);
    $query_check_factura   = mysqli_num_rows($query_check_user_name);
    if ($query_check_factura == true) {
        echo "<script>
      swal('NUMERO DE FACTURA YA ESTA REGISTRADO', 'Inténtalo de nuevo!', 'error')
  </script>";
        exit;
    }
    $sql            = mysqli_query($conexion, "select LAST_INSERT_ID(id_factura) as last from facturas_compras order by id_factura desc limit 0,1 ");
    $rw             = mysqli_fetch_array($sql);
    $numero_factura = $rw['last'] + 1;
    $sqlIdUser      = mysqli_query($conexion, "select sucursal_users from users where id_users = '".$users."'");
    $rwUser         = mysqli_fetch_array($sqlIdUser);
    $simbolo_moneda = "Q";
    $nums          = 1;
    $sumador_total = 0;
    $resultado           = mysqli_query($conexion, "SELECT * from productos inner join tmp_compra on productos.id_producto = tmp_compra.id_producto where tmp_compra.session_id = '" . $session_id . "'");
    while ($row = mysqli_fetch_array($resultado)){
        $id_tmp          = $row["id_tmp"];
        $id_producto     = $row["id_producto"];
        $codigo_producto = $row['codigo_producto'];
        $cantidad        = $row['cantidad_tmp'];
        $nombre_producto = $row['nombre_producto'];
        $precio_venta   = $row['costo_tmp'];
        $precio_venta_f = number_format($precio_venta, 2); 
        $precio_venta_r = str_replace(",", "", $precio_venta_f); 
        $precio_total   = $precio_venta_r * $cantidad;
        $precio_total_f = number_format($precio_total, 2); 
        $precio_total_r = str_replace(",", "", $precio_total_f); 
        $sumador_total += $precio_total_r; 
        $insert_detail = mysqli_query($conexion, "INSERT INTO detalle_fact_compra VALUES (NULL,'$numero_factura','$factura','$id_producto','$cantidad','$precio_venta_r')");
        $saldo_total = $cantidad * $precio_venta;
        $cant_saldo = $cantidad;
        $tipo  = 1;
        $costo_promedio = $saldo_total / $cant_saldo;
        $saldo_full = $saldo_total;
        $sql_kardex  = mysqli_query($conexion, "select * from kardex where producto_kardex='" . $id_producto . "' order by id_kardex DESC LIMIT 1");
        while ($row = mysqli_fetch_array($sql_kardex)) {
             $cant_saldo = $row['cant_saldo'] + $cantidad;
             $saldo_full     = ($row['total_saldo'] + $saldo_total);
             $costo_promedio = ($row['total_saldo'] + $saldo_total) / $cant_saldo;
             $tipo           = 1;
        }
        guardar_entradas($fecha, $id_producto, $cantidad, $precio_venta, $saldo_total, $cant_saldo, $costo_promedio, $saldo_full, $fecha, $users, $tipo,$sucursal);
        $sql2    = mysqli_query($conexion, "select * from productos where id_producto='" . $id_producto . "'");
        $sql                   = "SELECT * FROM stock WHERE id_producto_stock ='" . $id_producto . "' and id_sucursal_stock = '".$sucursal."';";
        $query_check_user_name = mysqli_query($conexion, $sql);
        $query_check_stock   = mysqli_num_rows($query_check_user_name);
        if ($query_check_stock == true) {
           $rw      = mysqli_fetch_array($query_check_user_name);
           $old_qty = $rw['cantidad_stock'];
           $new_qty = $old_qty + $cantidad; 
           $update  = mysqli_query($conexion, "UPDATE stock SET cantidad_stock='" . $new_qty . "' WHERE id_producto_stock = '" . $id_producto . "' and id_sucursal_stock = '".$sucursal ."'");
        }
        else{
            $insert_stockNuevo = mysqli_query($conexion, "INSERT INTO stock VALUES (NULL,'$id_producto','$sucursal','$cantidad')");        
        }
        $costo = mysqli_query($conexion, "UPDATE productos SET costo_producto='" . $precio_venta . "' WHERE id_producto = '" . $id_producto . "'");
        $nums++;
    }
    $subtotal         = number_format($sumador_total, 2, '.', '');
    $total_factura    = $subtotal;
    $saldo_credito    = $total_factura - $resibido;
    $resibido_formato = number_format($resibido, 2);
    $date             = date("Y-m-d H:i:s");
    if ($condiciones == 4) {
        $insert_prima = mysqli_query($conexion, "INSERT INTO credito_proveedor VALUES (NULL,'$factura','$date','$id_proveedor','$total_factura','$saldo_credito','1','$users','$sucursal')");
        $insert_abono = mysqli_query($conexion, "INSERT INTO creditos_abonos_prov VALUES (NULL,'$factura','$date','$id_proveedor','$total_factura','$resibido','$saldo_credito','$users','$sucursal','CREDITO INICAL')");
    }
    $insert = mysqli_query($conexion, "INSERT INTO facturas_compras VALUES (NULL,'$factura','$fecha','$id_proveedor','$id_vendedor','$condiciones','$total_factura','$estado','$users','$sucursal','$referencia')");
    $delete = mysqli_query($conexion, "DELETE FROM tmp_compra WHERE session_id='" . $session_id . "'");
    if ($condiciones == 4) {
        echo "<script>
       swal('COMPRA GUARDADA AL CREDITO CON ANTICIPO DE: $simbolo_moneda $resibido_formato', 'Factura: $factura', 'success')
  </script>";
        exit;
    }
    if ($insert_detail) {
        $messages[] = "La Compra ha sido Guardada satisfactoriamente.";
    } else {
        $errors[] = "Lo siento algo ha salido mal intenta nuevamente." . mysqli_error($conexion);
    }
} else {
    $errors[] = "Error desconocido.";
}
if (isset($errors)) {
    ?>
    <div class="alert alert-danger" role="alert">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
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
        <button type="button" class="close" data-dismiss="alert">&times;</button>
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