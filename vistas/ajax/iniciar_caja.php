<?php
    include 'is_logged.php'; 
    require_once "../db.php";
    require_once "../php_conexion.php";
    $fecha=date('Y-m-d H:i:s');
    try
    {   
        $sql = "select * from apertura_caja  where idcierre=0 and idsucursal=".$_POST['sucursal'];;
        $query_check_user_name = mysqli_query($conexion, $sql);
        $id= "0";
        while ($row = mysqli_fetch_array($query_check_user_name)) 
        {
            $id = $row['id_apertura'];
        }
        if(intval($id)==0)
        { 
            $sss ="insert into apertura_caja(monto,fecha,idcierre,idusuario,idsucursal) 
            values(".$_POST['efectivo'].",'".$fecha."',0,".$_POST['idusuario'].",".$_POST['sucursal'].")";  
            $insert = mysqli_query($conexion, $sss);
            http_response_code(200);
        }
        else
        {
            http_response_code(400);
            echo "La Caja  ya ha sido inicializada anteriormente";
        }
    }
    catch(Exception $e)
    {
        http_response_code(400);
        echo "Error al actualzar gasto".$e;
    }
