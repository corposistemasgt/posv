<?php
include 'is_logged.php';
require_once "../db.php";
require_once "../php_conexion.php";
$id            = $_REQUEST["id_permiso"];
$user_group_id = intval($id);
if ($user_group_id != 1) {
    if (empty($_POST['id_permiso'])) {
        $errors[] = "ID vacío";
    } else if (
        !empty($_POST['id_permiso'])
    ) {
        $id_permiso = intval($_POST['id_permiso']);
        $query      = mysqli_query($conexion, "select * from users where cargo_users='" . $id_permiso . "'");
        $count      = mysqli_num_rows($query);
        if ($count == 0) {
          $delete1 = mysqli_query($conexion, "DELETE FROM tbasignacionpermiso WHERE idgrupo='" . $id_permiso . "'");
            if ($delete1 = mysqli_query($conexion, "DELETE FROM tbgrupo WHERE idgrupo='" . $id_permiso . "'")) {
                ?>
      <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Aviso!</strong> Datos eliminados exitosamente.
      </div>
      <?php
} else {
                ?>
      <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Error!</strong> Lo siento algo ha salido mal intenta nuevamente.
      </div>
      <?php
            }
        } else {
            ?>
    <div class="alert alert-danger alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <strong>Error!</strong> No se pudo eliminar éste Cargo. Existe Usuario vinculado a éste Cargo.
    </div>
    <?php
}
    } else {
        $errors[] = "Error desconocido.";
    }
    if (isset($errors)) {
        ?>
  <div class="alert alert-danger" role="alert">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>Error!</strong>
    <?php
foreach ($errors as $error) {
            echo $error;
        }
        ?>
  </div>
  <?php
}} else {
    ?>
<div class="alert alert-danger alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <strong>Error!</strong> No se puede eliminar el Cargo de usuario super administrador..
    </div>
  <?php
}
?>