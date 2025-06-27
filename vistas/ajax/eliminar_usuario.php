<?php
include 'is_logged.php';
require_once "../db.php";
require_once "../php_conexion.php";
$id      = $_REQUEST["id_usuario"];
$user_id = intval($id);
if ($user_id != 1) {
    if (empty($_POST['id_usuario'])) {
        $errors[] = "ID vacío";
    } else if (
        !empty($_POST['id_usuario'])
    ) {
        $id_usuario = intval($_POST['id_usuario']);
        $query      = mysqli_query($conexion, "select * from facturas_ventas where id_users_factura='" . $id_usuario . "'");
        $count      = mysqli_num_rows($query);
        if ($count == 0) {
            if ($delete1 = mysqli_query($conexion, "DELETE FROM users WHERE id_users='" . $id_usuario . "'")) {
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
      <strong>Error!</strong> No se pudo eliminar éste Usuario. Existe Información vinculadas a éste Usuario.
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
}} else {
    ?>
<div class="alert alert-danger alert-dismissible" role="alert">
      <strong>Error!</strong> No se puede eliminar el Usuario por default..
    </div>
  <?php
}
?>