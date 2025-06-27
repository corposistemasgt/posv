<?php
include 'is_logged.php'; 
$numero_factura = $_SESSION['numero_factura'];
if (empty($_POST['abono'])) {
    $errors[] = "Cantidad vacía";
} else if (!empty($_POST['abono'])) {
    require_once "../db.php";
    require_once "../php_conexion.php";
    require_once "../funciones.php";
    $abono    = floatval($_POST['abono']);
    $corre    = floatval($_POST['corre']);
    $concepto = mysqli_real_escape_string($conexion, (strip_tags($_POST["concepto"], ENT_QUOTES)));
    $user_id  = $_SESSION['id_users'];
    $fecha    = date("Y-m-d H:i:s");
    $consultar     = mysqli_query($conexion, "select * from creditos where numero_factura='" . $numero_factura . "'");
    $rw            = mysqli_fetch_array($consultar);
    $id_cliente    = $rw['id_cliente'];
    $monto_credito = $rw['monto_credito'];
    $saldo         = $rw['saldo_credito'] - $abono;
    if ($rw['saldo_credito'] == 0) {
        echo "<script>
        $.Notification.notify('info','bottom center','NOTIFICACIÓN', 'EL CREDITO YA FUE CANCELADO EN SU TOTALIDAD')
        </script>";
        exit;
    }
    if ($abono > $rw['saldo_credito']) {
        echo "<script>
        $.Notification.notify('error','bottom center','NOTIFICACIÓN', 'EL ABONO ES MAYOR A LA DEUDA, INTENTAR NUEVAMENTE')
        </script>";
        exit;
    }
    $sql = "INSERT INTO creditos_abonos (numero_factura, fecha_abono, id_cliente, 
    monto_abono, abono, saldo_abono, id_users_abono, id_sucursal, concepto_abono,correlativo)
  VALUES ('$numero_factura', '$fecha', '$id_cliente', '$monto_credito', '$abono', '$saldo',
   '$user_id','1','$concepto','$corre');";
    $query = mysqli_query($conexion, $sql);
    $update_saldo = mysqli_query($conexion, "update creditos set saldo_credito=saldo_credito-'$abono' where numero_factura='$numero_factura'");
     $comprobar = mysqli_query($conexion, "select * from creditos where numero_factura='" . $numero_factura . "'");
    $rww       = mysqli_fetch_array($comprobar);
    if ($rww['saldo_credito'] == 0) {
        $up_credito = mysqli_query($conexion, "update creditos set estado_credito=2 where numero_factura='$numero_factura'");
        $up_factura = mysqli_query($conexion, "update facturas_ventas set estado_factura=1 where numero_factura='$numero_factura'");
        echo "<script>
        $.Notification.notify('info','bottom center','NOTIFICACIÓN', 'EL CREDITO SE HA CANCELADO EN SU TOTALIDAD')
        </script>";
    }
    if ($sql) {
        $messages[] = "El Abono  ha sido ingresado satisfactoriamente." . '' . $numero_factura;
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