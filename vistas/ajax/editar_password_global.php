<?php
include 'is_logged.php';
if (empty($_POST['user_password_new3']) || empty($_POST['user_password_repeat3'])) {
    $errors[] = "Contraseña vacía";
} elseif ($_POST['user_password_new3'] !== $_POST['user_password_repeat3']) {
    $errors[] = "la contraseña y la repetición de la contraseña no son lo mismo";
} elseif ( !empty($_POST['user_password_new3'])
    && !empty($_POST['user_password_repeat3'])
    && ($_POST['user_password_new3'] === $_POST['user_password_repeat3'])
) {
    require_once "../db.php";
    require_once "../php_conexion.php";
    $user_password = $_POST['user_password_new3'];
    $user_password_hash = base64_encode(base64_encode(base64_encode($user_password)));
    $sql   = "UPDATE users SET con_users='" . $user_password_hash . "' WHERE id_users='" . $_SESSION['id_users']. "'";
    $query = mysqli_query($conexion, $sql);
    if ($query) {
        $messages[] = "contraseña ha sido modificada con éxito.";
    } else {
        $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
    }
} else { 
    $errors[] = "Un error desconocido ocurrió.";
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