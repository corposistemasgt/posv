<?php
include 'is_logged.php'; 
if (empty($_POST['mod_id'])) {
    $errors[] = "ID vacío";
} else if (
    !empty($_POST['mod_id'])
) {
    require_once "../db.php";
    require_once "../php_conexion.php";
    $codigo      = mysqli_real_escape_string($conexion, (strip_tags($_POST["mod_codigo"], ENT_QUOTES)));
    $nombre      = mysqli_real_escape_string($conexion, (strip_tags($_POST["mod_nombre"], ENT_QUOTES)));
    $direccion   = mysqli_real_escape_string($conexion, (strip_tags($_POST["mod_direccion"], ENT_QUOTES)));
    $limite      = floatval($_POST['mod_limite']);
    $estado      = intval($_POST['mod_estado']);
    $id_sucursal = intval($_POST['mod_id']);
    $sql = "UPDATE sucursales SET  nombre_sucursal='" . $nombre . "',
                                limite_sucursal='" . $limite . "',
                                estado_sucursal='" . $estado . "',
                                direccion_sucursal='" . $direccion . "'
                                WHERE id_sucursal='" . $id_sucursal . "'";
    $query_update = mysqli_query($conexion, $sql);
    if ($query_update) {
        $messages[] = "Sucursal ha sido actualizada con Exito.";
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