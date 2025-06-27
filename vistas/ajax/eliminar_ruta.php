<?php
include 'is_logged.php'; 
require_once "../db.php";
require_once "../php_conexion.php";
if (empty($_POST['id_cliente'])) {
    $errors[] = "ID vacío";
} else if (
    !empty($_POST['id_cliente'])
) {
    $id_cliente = intval($_POST['id_cliente']);
        if ($delete1 = mysqli_query($conexion, "DELETE FROM tbruta WHERE idruta='" . $id_cliente . "'")) {
            ?>
            <div class="alert alert-success alert-dismissible" role="alert">
              <strong>Aviso!</strong> Datos eliminados exitosamente.
          </div>
          <?php
} else {
            ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
          <strong>Error!</strong> Lo siento algo ha salido mal intenta nuevamente.
      </div>
      <?php
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
?>