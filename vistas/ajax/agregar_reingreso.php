<?php
include 'is_logged.php';
$session_id = session_id();
$idusuario = $_GET['idusuario'];
require_once "../db.php";
require_once "../php_conexion.php";
require_once "../funciones.php";
?>
<div class="table-responsive">
    <table class="table table-sm">
        <thead class="thead-default">
            <tr>
                <th class='text-center'>COD</th>
                <th class='text-center'>CANT.</th>
                <th class='text-center'>DESCRIP.</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            
            $items       = 0;
$sql            = mysqli_query($conexion, "select * from productos, tbcarrito 
where productos.id_producto=tbcarrito.idproducto and tbcarrito.idusuario='" . $idusuario . "'");
while ($row = mysqli_fetch_array($sql)) {
    $id_tmp          = $row["idcarrito"];
    $id_producto     = $row['id_producto'];
    $codigo_producto = $row['codigo_producto'];
    $cantidad        = $row['cantidad'];
    if($cantidad>0){
        $items+=$cantidad;}
    $nombre_producto = $row['nombre_producto'];
    ?>
    <tr>
        <td class='text-center'><?php echo $codigo_producto; ?></td>
        <td class='text-center'><?php echo $cantidad; ?></td>
        <td><?php echo $nombre_producto; ?></td>
    </tr>
    <?php
}
?>
<tr>
    <td style="font-size: 14pt;" class='text-right' colspan=5><b>TOTAL ITEMS </b></td>
    <td style="font-size: 16pt;" class='text-right'><b><?php echo $items; ?></b></td>
    <td></td>
</tr>
</tbody>
</table>
</div>