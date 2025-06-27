<?php
include 'is_logged.php';
if (empty($_POST['id_detalle'])) {
    $errors[] = "ID vacío";
} else if (
    !empty($_POST['id_detalle'])
) {
    require_once "../db.php";
    require_once "../php_conexion.php";
    $id_detalle = intval($_POST['id_detalle']);
    $desc       = floatval($_POST['desc']);
    $sql          = "UPDATE detalle_fact_ventas SET  desc_venta='" . $desc . "' WHERE id_detalle='" . $id_detalle . "'";
    $query_update = mysqli_query($conexion, $sql);
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