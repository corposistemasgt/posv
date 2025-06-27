<?php
include 'is_logged.php'; 
require_once "../db.php";
require_once "../php_conexion.php";
$user_id  = $_SESSION['id_users'];
$sql   = "SELECT * FROM users WHERE id_users = '$user_id'";
$query1 = mysqli_query($conexion, $sql);
if (!$query1) {
    die('Error en la consulta: ' . mysqli_error($conexion));
}
$id_sucursalUsuario = ""; 
while ($row = mysqli_fetch_array($query1)) {
    $id_sucursalUsuario = $row['sucursal_users'];
}
$consulta = "SELECT RIGHT(numero_factura, 6) as factura FROM facturas_ventas WHERE id_sucursal = '$id_sucursalUsuario' ORDER BY factura DESC LIMIT 1";
$query_id = mysqli_query($conexion, $consulta);
if (!$query_id) {
    die('Error en la consulta: ' . mysqli_error($conexion));
}
$count = mysqli_num_rows($query_id);
if ($count != 0) {
    $data_id = mysqli_fetch_assoc($query_id);
    $factura = $data_id['factura'] + 1;
} else {
    $factura = 1;
}
$buat_id = str_pad($factura, 6, "0", STR_PAD_LEFT);
$factura = "CFF-$buat_id";
echo '<input type="text" class="form-control" autocomplete="off" id="factura" value="' . $factura . '" placeholder="Factura" readonly name="factura">';
?>
