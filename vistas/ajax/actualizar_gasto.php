<?php
    include 'is_logged.php'; 
    require_once "../db.php";
    require_once "../php_conexion.php";
    try
    {
        $sql="update egresos set referencia_egreso='".$_POST['referencia']."',monto=".$_POST['monto'].",
        descripcion_egreso='".$_POST['descripcion']."' where id_egreso=".$_POST['id'];
        $query_update = mysqli_query($conexion, $sql);
        http_response_code(200);
    }
    catch(Exception $e)
    {
        http_response_code(400);
        echo "Error al actualzar gasto".$e;
    }
?>