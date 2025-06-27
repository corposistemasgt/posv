<?php
include 'is_logged.php';
if (empty($_POST['id_tmp'])) {
    $errors[] = "ID vacío";
} else if (
    !empty($_POST['id_tmp'])
) {
    require_once "../db.php";
    require_once "../php_conexion.php";
    $id_tmp = intval($_POST['id_tmp']);
    $desc   = floatval($_POST['desc']);
    $sql          = "UPDATE tmp_cotizacion SET  desc_tmp='" . $desc . "' WHERE id_tmp='" . $id_tmp . "'";
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