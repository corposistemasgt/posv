<?php
include 'is_logged.php';
require_once "../db.php";
require_once "../php_conexion.php";
include "../funciones.php";
$permiso=getpermiso(39);
if($permiso==1)
{
if (empty($_POST['id_cliente'])) {
    $errors[] = "ID vacío";
} else if (
    !empty($_POST['id_cliente'])
) {
    $id_cliente = intval($_POST['id_cliente']);
    $query      = mysqli_query($conexion, "select * from facturas_ventas where id_cliente='" . $id_cliente . "'");
    $count      = mysqli_num_rows($query);
    if ($count == 0) {
        if ($delete1 = mysqli_query($conexion, "DELETE FROM clientes WHERE id_cliente='" . $id_cliente . "'")) {
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
      <strong>Error!</strong> No se pudo eliminar éste Cliente. Existe Información vinculadas.
  </div>
  <?php
}

} else {
    $errors[] = "Error desconocido.";
}
}
else
{
    ?>
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <strong>Error!</strong> No tiene permisos para realizar esta accion.
                </div>
                <?php
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