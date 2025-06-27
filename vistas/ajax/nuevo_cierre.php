<?php
    include 'is_logged.php'; 
    require_once "../db.php";
    require_once "../php_conexion.php";
    try
    {
        date_default_timezone_set('America/Guatemala'); 
        $fecha=date("Y-m-d H:i:s"); 
        $diferencia=$_POST['monto']-$_POST['efectivo'];
        $insert = mysqli_query($conexion, "INSERT into cierre(fecha,monto,efectivo,diferencia,estado,idusuario) 
            values('$fecha',".$_POST['monto'].",".$_POST['efectivo'].",'$diferencia',1,".$_POST['idusuario'].")");
        $idcierre = mysqli_insert_id($conexion);
        $sucu=$_POST['sucursal'];
        $iuser= intval($_POST['idusuario']);
        if($iuser>0)
        {
            $insert = mysqli_query($conexion,"UPDATE facturas_ventas set idcierre='$idcierre' where idcierre=0 and id_vendedor='$iuser'");
            $insert = mysqli_query($conexion,"UPDATE apertura_caja set idcierre='$idcierre' where idcierre=0 and idusuario='$iuser'");
            $insert = mysqli_query($conexion,"UPDATE egresos set idcierre='$idcierre' where idcierre=0 and users='$iuser'");
            $insert = mysqli_query($conexion,"UPDATE creditos_abonos set idcierre='$idcierre' where idcierre=0 and id_users_abono='$iuser'");
        }
        else
        {
            $insert = mysqli_query($conexion,"UPDATE facturas_ventas set idcierre='$idcierre' where idcierre=0 and id_sucursal='$sucu'");
            $insert = mysqli_query($conexion,"UPDATE apertura_caja set idcierre='$idcierre' where idcierre=0 and idsucursal='$sucu'");
            $insert = mysqli_query($conexion,"UPDATE egresos set idcierre='$idcierre' where idcierre=0 and idsucursal='$sucu'");
            $insert = mysqli_query($conexion,"UPDATE creditos_abonos set idcierre='$idcierre' where idcierre=0 and id_sucursal='$sucu'");
        }
        
        http_response_code(200);
    }
    catch(Exception $e)
    {
        http_response_code(400);
        echo "Error al realizar el cierre de caja";
    }
?>