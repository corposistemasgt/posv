<?php
include 'is_logged.php'; 
require_once "../db.php";
require_once "../php_conexion.php";
if (empty($_POST['id_factura'])) {
    $errors[] = "ID vacío";
} else if (
    !empty($_POST['id_factura'])
) {
    $id_factura = intval($_POST['id_factura']);
    if ($anular = mysqli_query($conexion, "UPDATE facturas_ventas SET estado_factura=0, monto_factura=0 WHERE id_factura='$id_factura'")) {
        $anul = mysqli_query($conexion, "UPDATE detalle_fact_ventas SET importe_venta=0 WHERE id_factura='$id_factura'")
        ?>
       <div class="alert alert-success alert-dismissible" role="alert">
          <strong>Aviso!</strong> Factura Anulada exitosamente.
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