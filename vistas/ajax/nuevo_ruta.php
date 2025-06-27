<?php
include 'is_logged.php';
if (empty($_POST['nombre'])) {
    $errors[] = "Nombre vacío";
} else if (!empty($_POST['nombre'])) {
    require_once "../db.php";
    require_once "../php_conexion.php";
    $session_id     = session_id();
    $ruta    = mysqli_real_escape_string($conexion, (strip_tags($_POST["nombre"], ENT_QUOTES)));
    $sql                   = "SELECT * FROM tbruta WHERE ruta ='" . $ruta . "';";
    $query_check_user_name = mysqli_query($conexion, $sql);
    $query_check_user      = mysqli_num_rows($query_check_user_name);
    if ($query_check_user == true) {
        $errors[] = "Lo sentimos , la ruta ya está en uso.";
    } else {
        $sql              = "INSERT INTO tbruta (ruta) VALUES ('$ruta')";
        $query_new_insert = mysqli_query($conexion, $sql);
        if ($query_new_insert) {
            $messages[] = "Ruta ha sido ingresado con Exito.";
        } else {
            $errors[] = "Lo siento algo ha salido mal intenta nuevamente." . mysqli_error($conexion);
        }
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