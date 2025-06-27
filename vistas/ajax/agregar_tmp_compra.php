<?php
include 'is_logged.php';
$session_id = session_id();
if (isset($_POST['id'])) {$id = $_POST['id'];}
if (isset($_POST['cantidad'])) {$cantidad = $_POST['cantidad'];}
require_once "../db.php";
require_once "../php_conexion.php";
require_once "../funciones.php";
if (!empty($id) and !empty($cantidad)) {
    $id_producto    = get_row('productos', 'id_producto', 'codigo_producto', $id);
    $costo_producto = get_row('productos', 'costo_producto', 'id_producto', $id_producto);
    $query = mysqli_query($conexion, "select codigo_producto from productos where codigo_producto = '$id'");
    $rw    = mysqli_fetch_array($query);
    $comprobar = mysqli_query($conexion, "select * from tmp_compra where id_producto='" . $id_producto . "' and session_id='" . $session_id . "'");
    if ($row = mysqli_fetch_array($comprobar)) {
        $cant = $row['cantidad_tmp'] + $cantidad;
        $sql          = "UPDATE tmp_compra SET cantidad_tmp='" . $cant . "' WHERE id_producto='" . $id_producto . "' and session_id='" . $session_id . "'";
        $query_update = mysqli_query($conexion, $sql);

    } else {
        $insert_tmp = mysqli_query($conexion, "INSERT INTO tmp_compra (id_producto,cantidad_tmp,costo_tmp,session_id) VALUES ('$id_producto','$cantidad','$costo_producto','$session_id')");
    }
}
if (isset($_GET['id']))
{
    $id_tmp = intval($_GET['id']);
    $delete = mysqli_query($conexion, "DELETE FROM tmp_compra WHERE id_tmp='" . $id_tmp . "'");
}
$simbolo_moneda = "Q";
?>
<div class="table-responsive">
    <table class="table table-sm">
        <thead class="thead-default">
            <tr>
                <th class='text-center'>COD.</th>
                <th class='text-center'>CANT.</th>
                <th>DESCRIP.</th>
                <th class='text-center'>COSTO</th>
                <th class='text-right'>TOTAL</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
$sumador_total = 0;
$sql           = mysqli_query($conexion, "select * from productos, tmp_compra where productos.id_producto=tmp_compra.id_producto and tmp_compra.session_id='" . $session_id . "'");
while ($row = mysqli_fetch_array($sql)) {
    $id_tmp          = $row["id_tmp"];
    $codigo_producto = $row['codigo_producto'];
    $cantidad        = $row['cantidad_tmp'];
    $nombre_producto = $row['nombre_producto'];
    $precio_costo   = $row['costo_tmp'];
    $precio_costo_f = number_format($precio_costo, 2); 
    $precio_costo_r = str_replace(",", "", $precio_costo_f); 
    $precio_total   = $precio_costo_r * $cantidad;
    $precio_total_f = number_format($precio_total, 2); 
    $precio_total_r = str_replace(",", "", $precio_total_f); 
    $sumador_total += $precio_total_r; 
    ?>
    <tr>
        <td class='text-center'><?php echo $codigo_producto; ?></td>
        <td class='text-center'><?php echo $cantidad; ?></td>
        <td><?php echo $nombre_producto; ?></td>
        <td align="right" width="15%">
            <input type="text" class="form-control txt_costo" value="<?php echo $precio_costo; ?>" id="<?php echo $id_tmp; ?>">
        </td>
        <td class='text-right'><?php echo $simbolo_moneda . ' ' . $precio_total_f; ?></td>
        <td class='text-center'>
            <a href="#" class='btn btn-danger btn-sm waves-effect waves-light' onclick="eliminar('<?php echo $id_tmp ?>')"><i class="fa fa-remove"></i>
            </a>
        </td>
    </tr>
    <?php
}

$subtotal      = number_format($sumador_total, 2, '.', '');
$total_factura = $subtotal;

?>
<tr>
    <td class='text-right' colspan=4>SUBTOTAL <?php echo $simbolo_moneda; ?></td>
    <td class='text-right'><b><?php echo number_format($subtotal, 2); ?></b></td>
    <td></td>
</tr>
<tr>
    <td style="font-size: 14pt;" class='text-right' colspan=4><b>TOTAL <?php echo $simbolo_moneda; ?> </b></td>
    <td style="font-size: 14pt;" class='text-right'><b><?php echo number_format($total_factura, 2); ?></b></td>
    <td></td>
</tr>
</tbody>
</table>
</div>
<script>
    $(document).ready(function () {
        $('.txt_costo').off('blur');
        $('.txt_costo').on('blur',function(event){
            var keycode = (event.keyCode ? event.keyCode : event.which);
            id_tmp = $(this).attr("id");
            costo = $(this).val();
             if (isNaN(costo)) {
                $.Notification.notify('error','bottom center','ERROR!', 'EL COSTO DIGITADO NO ES UN FORMATO VALIDO')
                $(this).focus();
                return false;
            }
    $.ajax({
        type: "POST",
        url: "../ajax/editar_costo_compra.php",
        data: "id_tmp=" + id_tmp + "&costo=" + costo,
        success: function(datos) {
         $("#resultados").load("../ajax/agregar_tmp_compra.php");
         $.Notification.notify('success','bottom center','EXITO!', 'COSTO ACTUALIZADO CORRECTAMENTE')
     }
 });
    });
    });
</script>