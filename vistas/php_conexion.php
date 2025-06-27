<?php
$user=base64_decode($_SESSION['us']);
$pass=base64_decode($_SESSION['pa']);
$bd=base64_decode($_SESSION['bd']);
$conexion = @mysqli_connect(DB_HOST, $user, $pass, "corpo_".$bd);
if (!$conexion) {
    die("imposible conectarse: ". mysqli_error($conexion));
}
if (@mysqli_connect_errno()) {
    die("Conexión falló: " . mysqli_connect_errno() . " : " . mysqli_connect_error());
}
date_default_timezone_set("America/Guatemala");
mysqli_query($conexion, "SET NAMES utf8");
function limpiar($tags)
{
    $tags = strip_tags($tags);
    return $tags;
}
