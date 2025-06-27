<?php
include 'is_logged.php';
$idproducto    = $_SESSION['idpa'];
$idsucursal   = $_SESSION['idsa'];
if (empty($_POST['quantity'])) {
    $errors[] = "Cantidad vacía";
} else if (!empty($_POST['quantity'])) {
    require_once "../db.php";
    require_once "../php_conexion.php";
    require_once "../funciones.php";
    $quantity  = intval($_POST['quantity']);
    $reference = mysqli_real_escape_string($conexion, (strip_tags($_POST["reference"], ENT_QUOTES)));
    $nota      = "agregó $quantity producto(s) al inventario";
    $fecha     = date("Y-m-d H:i:s");
    $tipo      = 1;
    $user_id   = $_SESSION['id_users'];
   guardar_historial($idproducto, $user_id, $fecha, $nota, $reference, $quantity, $tipo, $idsucursal);
    $update = agregar_stock($idproducto, $quantity,$idsucursal);
    $sql_kardex  = mysqli_query($conexion, "select * from kardex where producto_kardex='" . $idproducto . "'  and idsucursal='$idsucursal' order by id_kardex DESC LIMIT 1");
    $rww         = mysqli_fetch_array($sql_kardex);
    $costo       = $rww['costo_saldo'];
    $saldo_total = $quantity * $costo;
    $cant_saldo  = $rww['cant_saldo'] + $quantity;
    $saldo_full     = ($rww['total_saldo'] + $saldo_total);
    $costo_promedio = ($rww['total_saldo'] + $saldo_total) / $cant_saldo;
    $tip            = 3;
    guardar_entradas($fecha, $idproducto, $quantity, $costo, $saldo_total, $cant_saldo, $costo_promedio, $saldo_full, $fecha, $user_id, $tip,$idsucursal);
    if ($update) {
        $messages[] = "El Stock  ha sido ingresado satisfactoriamente.";
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