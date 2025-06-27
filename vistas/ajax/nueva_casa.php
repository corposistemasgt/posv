<?php
include 'is_logged.php'; 
if (empty($_POST['nombre'])) {
    $errors[] = "referencia vacío";
} else if (!empty($_POST['nombre'])) {
    require_once "../db.php";
    require_once "../php_conexion.php";
    $nombre      = mysqli_real_escape_string($conexion, (strip_tags($_POST["nombre"], ENT_QUOTES)));
    $descripcion = mysqli_real_escape_string($conexion, (strip_tags($_POST["descripcion"], ENT_QUOTES)));
    $estado      = intval($_POST['estado']);
    $users       = intval($_SESSION['id_users']);
    $sql                   = "SELECT * FROM tbcasa WHERE casa ='" . $nombre . "';";
    $query_check_user_name = mysqli_query($conexion, $sql);
    $query_check_user      = mysqli_num_rows($query_check_user_name);
    if ($query_check_user == true) {
        $errors[] = "Nombre de Casa ya está en uso.";
    } else {
        $sql = "INSERT INTO tbcasa (casa, descripcion, estado)
    VALUES ('$nombre','$descripcion','$estado')";
        $query_new_insert = mysqli_query($conexion, $sql);
        if ($query_new_insert) {
            $messages[] = "Casa ingresada con Exito.";
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