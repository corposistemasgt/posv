<?php
include 'is_logged.php';
if (empty($_POST['mod_id'])) {
    $errors[] = "ID vacío";
} else if (empty($_POST['mod_nombre'])) {
    $errors[] = "Nombre vacío";
} else if (
    !empty($_POST['mod_id']) &&
    !empty($_POST['mod_nombre']) 
) {
    require_once "../db.php";
    require_once "../php_conexion.php";
    $ruta   = mysqli_real_escape_string($conexion, (strip_tags($_POST["mod_nombre"], ENT_QUOTES)));
    $idruta = intval($_POST['mod_id']);
    $sql        = "UPDATE tbruta SET ruta='" . $ruta . "' WHERE idruta='" . $idruta . "'";
    $query_update = mysqli_query($conexion, $sql);
    if ($query_update) {
        $messages[] = "La Ruta ha sido actualizada con Exito.";
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