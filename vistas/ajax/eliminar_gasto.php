<?php
include 'is_logged.php';
require_once "../db.php";
require_once "../php_conexion.php";
if ($delete1 = mysqli_query($conexion, "DELETE FROM egresos WHERE id_egreso='" . $_POST['id']. "'")) {
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
?>