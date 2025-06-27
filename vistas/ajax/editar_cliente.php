<?php
include 'is_logged.php';
if (empty($_POST['mod_id'])) {
    $errors[] = "ID vacío";
} else if (empty($_POST['mod_nombre'])) {
    $errors[] = "Nombre vacío";
} else if ($_POST['mod_estado'] == "") {
    $errors[] = "Selecciona el estado del cliente";
} else if (
    !empty($_POST['mod_id']) &&
    !empty($_POST['mod_nombre']) &&
    $_POST['mod_estado'] != ""
) {
    require_once "../db.php";
    require_once "../php_conexion.php";
    $nombre    = mysqli_real_escape_string($conexion, (strip_tags($_POST["mod_nombre"], ENT_QUOTES)));
    $fiscal    = mysqli_real_escape_string($conexion, (strip_tags($_POST["mod_fiscal"], ENT_QUOTES)));
    $telefono  = mysqli_real_escape_string($conexion, (strip_tags($_POST["mod_telefono"], ENT_QUOTES)));
    $email     = mysqli_real_escape_string($conexion, (strip_tags($_POST["mod_email"], ENT_QUOTES)));
    $direccion = mysqli_real_escape_string($conexion, (strip_tags($_POST["mod_direccion"], ENT_QUOTES)));
    $estado    = intval($_POST['mod_estado']);
    $id_cliente = intval($_POST['mod_id']);
    $credito= intval($_POST['mod_credito']);
    $limite_credito= doubleval($_POST['mod_limite_credito']);
    $ruta = intval($_POST['mod_ruta']);
    $sql        = "UPDATE clientes SET nombre_cliente='" . $nombre . "',
                                        fiscal_cliente='" . $fiscal . "',
                                        telefono_cliente='" . $telefono . "',
                                        email_cliente='" . $email . "',
                                        direccion_cliente='" . $direccion . "',
                                        idruta='" . $ruta . "',
                                        credito='" . $credito . "',
                                        limite_credito='" . $limite_credito . "',
                                        status_cliente='" . $estado . "'
                                        WHERE id_cliente='" . $id_cliente . "'";
    $query_update = mysqli_query($conexion, $sql);
    if ($query_update) {
        $messages[] = "Cliente ha sido actualizado con Exito.";
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