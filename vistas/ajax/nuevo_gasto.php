<?php
include 'is_logged.php'; 
if (empty($_POST['referencia'])) {
    $errors[] = "referencia vacío";
} else if (!empty($_POST['referencia'])) {
    require_once "../db.php";
    require_once "../php_conexion.php";
    $referencia  = mysqli_real_escape_string($conexion, (strip_tags($_POST["referencia"], ENT_QUOTES)));
    $descripcion = mysqli_real_escape_string($conexion, (strip_tags($_POST["descripcion"], ENT_QUOTES)));
    $monto       = floatval($_POST['monto']);
    $date_added  = date("Y-m-d H:i:s");
    $users       = intval($_SESSION['id_users']);
    $sucu      = $_POST['sucursal'];
    $sql                   = "SELECT * FROM egresos WHERE referencia_egreso ='" . $referencia . "';";
    $query_check_user_name = mysqli_query($conexion, $sql);
    $query_check_user      = mysqli_num_rows($query_check_user_name);
    if ($query_check_user == true) {
        $errors[] = "Referencia del Gasto ya está en uso.";
    } else {
        $sql = "INSERT INTO egresos (referencia_egreso, monto, descripcion_egreso, fecha_added, users,idsucursal)
    VALUES ('$referencia','$monto','$descripcion','$date_added','$users','$sucu')";
        $query_new_insert = mysqli_query($conexion, $sql);

        if ($query_new_insert) {
            $messages[] = "Gasto ha sido ingresado con Exito.";
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