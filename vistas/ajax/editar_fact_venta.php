<?php
include 'is_logged.php';
$id_factura = $_SESSION['id_factura'];
if (empty($_POST['id_cliente'])) {
    $errors[] = "ID vacío";
} else if (empty($_POST['id_vendedor'])) {
    $errors[] = "Selecciona el vendedor";
} else if (empty($_POST['condiciones'])) {
    $errors[] = "Selecciona forma de pago";
} else if ($_POST['estado_factura'] == "") {
    $errors[] = "Selecciona el estado de la factura";
} else if (
    !empty($_POST['id_cliente']) &&
    !empty($_POST['id_vendedor']) &&
    !empty($_POST['condiciones']) &&
    $_POST['estado_factura'] != ""
) {
    require_once "../db.php";
    require_once "../php_conexion.php";
    $id_cliente  = intval($_POST['id_cliente']);
    $id_vendedor = intval($_POST['id_vendedor']);
    $condiciones = intval($_POST['condiciones']);

    $estado_factura = intval($_POST['estado_factura']);

    $sql          = "UPDATE facturas_ventas SET id_cliente='" . $id_cliente . "', id_vendedor='" . $id_vendedor . "', condiciones='" . $condiciones . "', estado_factura='" . $estado_factura . "' WHERE id_factura='" . $id_factura . "'";
    $query_update = mysqli_query($conexion, $sql);
    if ($query_update) {
        $messages[] = "Datos del Cliente actualizado con exito!.";
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