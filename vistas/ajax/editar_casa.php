<?php
include 'is_logged.php'; 
if (empty($_POST['mod_id'])) {
    $errors[] = "ID vacío";
} else if (
    !empty($_POST['mod_id'])
) {
    require_once "../db.php";
    require_once "../php_conexion.php";
    $nombre      = mysqli_real_escape_string($conexion, (strip_tags($_POST["mod_nombre"], ENT_QUOTES)));
    $descripcion = mysqli_real_escape_string($conexion, (strip_tags($_POST["mod_descripcion"], ENT_QUOTES)));
    $estado      = intval($_POST['mod_estado']);
    $id_linea    = intval($_POST['mod_id']);
    $sql = "UPDATE tbcasa SET  casa='" . $nombre . "', estado='" . $estado . "',
                                descripcion='" . $descripcion . "'
                                WHERE idcasa='" . $id_linea . "'";
    $query_update = mysqli_query($conexion, $sql);
    if ($query_update) {
        $messages[] = "Casa actualizada con Exito.";
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