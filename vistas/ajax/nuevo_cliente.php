<?php
include 'is_logged.php';
if (empty($_POST['nombre'])) {
    $errors[] = "Nombre vacío";
} else if (!empty($_POST['nombre'])) {
    require_once "../db.php";
    require_once "../php_conexion.php";
    $session_id     = session_id();
    $idUser          = intval($_SESSION['id_users']);
    $sqlUsuarioACT        = mysqli_query($conexion, "select * from users inner join perfil on id_perfil = sucursal_users where id_users = '".$idUser."'"); //obtener el usuario activo 1aqui1
    $rw         = mysqli_fetch_array($sqlUsuarioACT);
    $id_sucursal = $rw['sucursal_users'];
    $nombre     = mysqli_real_escape_string($conexion, (strip_tags($_POST["nombre"], ENT_QUOTES)));
    $fiscal     = mysqli_real_escape_string($conexion, (strip_tags($_POST["fiscal"], ENT_QUOTES)));
    $telefono   = mysqli_real_escape_string($conexion, (strip_tags($_POST["telefono"], ENT_QUOTES)));
    $email      = mysqli_real_escape_string($conexion, (strip_tags($_POST["email"], ENT_QUOTES)));
    $direccion  = mysqli_real_escape_string($conexion, (strip_tags($_POST["direccion"], ENT_QUOTES)));
    $estado     = intval($_POST['estado']);
    $idruta     = intval($_POST['ruta']);
    $credito     = intval($_POST['credito']);
    $limite_credito     = doubleval($_POST['limite_credito']);
    $date_added = date("Y-m-d H:i:s");
    $sql                   = "SELECT * FROM clientes WHERE fiscal_cliente ='" . $fiscal . "';";
    $query_check_user_name = mysqli_query($conexion, $sql);
    $query_check_user      = mysqli_num_rows($query_check_user_name);
    if ($query_check_user == true) {
        $errors[] = "Lo sentimos , el documento ó la dirección de correo electrónico ya está en uso.";
    } else {
        $sql              = "INSERT INTO clientes (nombre_cliente, 
        fiscal_cliente, telefono_cliente, email_cliente, direccion_cliente, 
        status_cliente, date_added, id_perfil,idruta, credito,limite_credito) 
        VALUES ('$nombre','$fiscal','$telefono','$email','$direccion',
        '$estado','$date_added', '$id_sucursal','$idruta','$credito',
        '$limite_credito')";
        $query_new_insert = mysqli_query($conexion, $sql);
        if ($query_new_insert) {
            $messages[] = "Cliente ha sido ingresado con Exito.";
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