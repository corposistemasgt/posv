<?php
include 'is_logged.php'; 
require_once "../db.php";
require_once "../php_conexion.php";
$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != null) ? $_REQUEST['action'] : '';
if ($action == 'ajax') 
{
    $iduser       = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['iduser'], ENT_QUOTES)));
    $idruta       = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['idruta'], ENT_QUOTES)));
    $sql   = "SELECT * FROM  tbasignacionrutas where idruta=".$idruta.
    " and idusuario=".$iduser;
    $query_check_user_name = mysqli_query($conexion, $sql);
    $query_check_user      = mysqli_num_rows($query_check_user_name);
    if ($query_check_user == true) 
    {
       
    } else 
    {
        $sql          = "INSERT INTO tbasignacionrutas(idusuario,idruta) 
        VALUES ('$iduser','$idruta')";
        $query_update = mysqli_query($conexion, $sql);
    }
}