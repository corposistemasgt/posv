<?php
include 'is_logged.php'; 
require_once "../db.php";
require_once "../php_conexion.php";
if (empty($_POST['id_sucursal'])) {
    $errors[] = "ID vacío";
} else if (
    !empty($_POST['id_sucursal'])
) {
    $id_sucursal = intval($_POST['id_sucursal']);
    $query       = mysqli_query($conexion, "select * from users where sucursal_users='" . $id_sucursal . "'");
    $count       = mysqli_num_rows($query);
    if ($count == 0) {
        if ($delete1 = mysqli_query($conexion, "DELETE FROM sucursales WHERE id_sucursal='" . $id_sucursal . "'")) {
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
        ?>
    <div class="alert alert-danger alert-dismissible" role="alert">
      <strong>Error!</strong> No se pudo eliminar esta Sucursal. Existe Información vinculadas.
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