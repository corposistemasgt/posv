<?php
include 'is_logged.php'; 
require_once "../db.php";
require_once "../php_conexion.php";
if (empty($_POST['id_producto'])) 
{
    $errors[] = "ID vacío";
} 
else if(!empty($_POST['id_producto'])) 
{
    $id_producto = intval($_POST['id_producto']);
    $query       = mysqli_query($conexion, "select * from facturas where id_producto='" . $id_producto . "'");
    if($query || !$query)
    {
        $count = 0;
    }
    else
    {
        $count       = mysqli_num_rows($query);
    } 
    if ($count == 0) 
    {
        if ($delete1 = mysqli_query($conexion, "DELETE FROM productos WHERE id_producto='" . $id_producto . "'")) 
        {
            mysqli_query($conexion, "DELETE FROM stock WHERE id_producto_stock='" . $id_producto . "'")
            ?>
                <div class="alert alert-success alert-dismissible" role="alert">
                    <strong>Aviso!</strong> Datos eliminados exitosamente.
                </div>
            <?php
        } 
        else
        {
            ?>
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <strong>Error!</strong> Lo siento algo ha salido mal intenta nuevamente.
                </div>
            <?php
        }

    } 
    else 
    {
        ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
            <strong>Error!</strong> No se pudo eliminar éste Producto. Existe Información vinculadas.
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