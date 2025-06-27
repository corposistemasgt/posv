<?php
include 'is_logged.php';
require_once "../db.php";
require_once "../php_conexion.php";
if (empty($_POST['id_linea'])) {
    $errors[] = "ID vacío";
} else if (
    !empty($_POST['id_linea'])
) {
    $id_linea = intval($_POST['id_linea']);
    $query    = mysqli_query($conexion, "select * from productos where id_linea_producto='" . $id_linea . "'");
    $count    = mysqli_num_rows($query);
    if ($count == 0) {
        if ($delete1 = mysqli_query($conexion, "DELETE FROM lineas WHERE id_linea='" . $id_linea . "'")) {
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
      <strong>Error!</strong> No se pudo eliminar éste Medicamento. Existe Información vinculadas.
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