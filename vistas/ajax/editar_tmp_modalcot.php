<?php
include 'is_logged.php';
$id_factura     = $_SESSION['id_factura'];
$numero_factura = $_SESSION['numero_factura'];
if (isset($_POST['id'])) {$id = intval($_POST['id']);}
if (isset($_POST['cantidad'])) {$cantidad = intval($_POST['cantidad']);}
if (isset($_POST['precio_venta'])) {$precio_venta = floatval($_POST['precio_venta']);}
require_once "../db.php";
require_once "../php_conexion.php"; 
include "../funciones.php";
if (!empty($id) and !empty($cantidad) and !empty($precio_venta)) {
    $query = mysqli_query($conexion, "select stock_producto, inv_producto from productos where id_producto = '$id'");
    $rw    = mysqli_fetch_array($query);
    $stock = $rw['stock_producto'];
    $inv   = $rw['inv_producto'];
    $comprobar = mysqli_query($conexion, "select * from detalle_fact_cot where id_producto='" . $id . "' and id_factura='" . $id_factura . "'");
    if ($row = mysqli_fetch_array($comprobar)) {
        $cant = $row['cantidad'] + $cantidad;
        if ($cant > $stock and $inv == 0) {
            echo "<script>swal('LA CANTIDAD SUPERA AL STOCK!', 'INTENTAR NUEVAMENTE', 'error')</script>";
        } else {
            $sql          = "UPDATE detalle_fact_cot SET cantidad='" . $cant . "' WHERE id_producto='" . $id . "' and id_factura='" . $id_factura . "'";
            $query_update = mysqli_query($conexion, $sql);
        }
    } else {
        if ($cantidad > $stock and $inv == 0) {
            echo "<script>swal('LA CANTIDAD SUPERA AL STOCK!', 'INTENTAR NUEVAMENTE', 'error')</script>";
        } else {
            $insert_tmp = mysqli_query($conexion, "INSERT INTO detalle_fact_cot (id_factura,numero_factura, id_producto,cantidad,precio_venta) VALUES ('$id_factura','$numero_factura','$id','$cantidad','$precio_venta')");
        }
    }
}
if (isset($_GET['id'])) 
{
    $user_id   = $_SESSION['id_users'];
    $sqlUsuarioACT        = mysqli_query($conexion, "select * from users where id_users = '".$user_id."'");
    $row             = mysqli_fetch_array($sqlUsuarioACT);
    $id_sucursal         = $row['sucursal_users'];
    $id_detalle = intval($_GET['id']);
    $id_prod    = get_row('detalle_fact_cot', 'id_producto', 'id_detalle', $id_detalle);
    $quantity   = get_row('detalle_fact_cot', 'cantidad', 'id_detalle', $id_detalle);
    $update     = agregar_stock($id_prod, $quantity,$id_sucursal); //Vuelve agregar al inventario
    $delete     = mysqli_query($conexion, "DELETE FROM detalle_fact_cot WHERE id_detalle='" . $id_detalle . "'");
}
$simbolo_moneda = "Q";
?>
<div class="table-responsive">
    <table class="table table-sm">
        <thead class="thead-default">
            <tr>
                <th class='text-center'>COD</th>
                <th class='text-center'>CANT.</th>
                <th class='text-center'>DESCRIP.</th>
                <th class='text-center'>PRECIO <?php echo $simbolo_moneda; ?></th>
                <th class='text-center'>DESC %</th>
                <th class='text-right'>TOTAL</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
$sumador_total  = 0;
$sql            = mysqli_query($conexion, "select * from productos, facturas_cot, detalle_fact_cot where facturas_cot.id_factura=detalle_fact_cot.id_factura and  facturas_cot.id_factura='$id_factura' and productos.id_producto=detalle_fact_cot.id_producto");
while ($row = mysqli_fetch_array($sql)) {
    $id_detalle      = $row["id_detalle"];
    $id_producto     = $row["id_producto"];
    $codigo_producto = $row['codigo_producto'];
    $cantidad        = $row['cantidad'];
    $desc_tmp        = $row['desc_venta'];
    $nombre_producto = $row['nombre_producto'];
    $precio_venta   = $row['precio_venta'];
    $precio_venta_f = number_format($precio_venta, 2); 
    $precio_venta_r = str_replace(",", "", $precio_venta_f); 
    $precio_total   = $precio_venta_r * $cantidad;
    $final_items    = rebajas($precio_total, $desc_tmp); 
    $precio_total_f = number_format($final_items, 2); 
    $precio_total_r = str_replace(",", "", $precio_total_f); 
    $sumador_total += $precio_total_r; 
    $subtotal = number_format($sumador_total, 2, '.', '');
    ?>
    <tr>
        <td class='text-center'><?php echo $codigo_producto; ?></td>
        <td class='text-center'><?php echo $cantidad; ?></td>
        <td><?php echo $nombre_producto; ?></td>
        <td class='text-center'>
            <div class="input-group">
                <select id="<?php echo $id_detalle; ?>" class="form-control employee_id">
                    <?php
$sql1 = mysqli_query($conexion, "select * from productos where id_producto='" . $id_producto . "'");
    while ($rw1 = mysqli_fetch_array($sql1)) {
        ?>
                        <option selected disabled value="<?php echo $precio_venta ?>"><?php echo number_format($precio_venta, 2); ?></option>
                        <option value="<?php echo $rw1['valor1_producto'] ?>">PV <?php echo number_format($rw1['valor1_producto'], 2); ?></option>
                        <option value="<?php echo $rw1['valor2_producto'] ?>">PM <?php echo number_format($rw1['valor2_producto'], 2); ?></option>
                        <option value="<?php echo $rw1['valor3_producto'] ?>">PE <?php echo number_format($rw1['valor3_producto'], 2); ?></option>
                        <?php
}
    ?>
                </select>
            </div>
        </td>
        <td align="right" width="15%">
                <input type="text" class="form-control txt_desc" style="text-align:center" value="<?php echo $desc_tmp; ?>" id="<?php echo $id_detalle; ?>">
        </td>
        <td class='text-right'><?php echo $simbolo_moneda . ' ' . number_format($final_items, 2); ?></td>
        <td class='text-center'>
            <a href="#" class='btn btn-danger btn-sm waves-effect waves-light' onclick="eliminar('<?php echo $id_detalle ?>')"><i class="fa fa-remove"></i>
            </a>
        </td>
    </tr>
    <?php
}
$total_factura = $subtotal;
$update        = mysqli_query($conexion, "update facturas_cot set monto_factura='$total_factura' where id_factura='$id_factura'");

?>
<tr>
    <td class='text-right' colspan=5>SUBTOTAL</td>
    <td class='text-right'><b><?php echo $simbolo_moneda . ' ' . number_format($subtotal, 2); ?></b></td>
    <td></td>
</tr>
<tr>
    <td style="font-size: 14pt;" class='text-right' colspan=5><b>TOTAL <?php echo $simbolo_moneda; ?></b></td>
    <td style="font-size: 16pt;" class='text-right'><span class="label label-danger"><b><?php echo number_format($total_factura, 2); ?></b></span></td>
    <td></td>
</tr>
</tbody>
</table>
</div>
<script>
    $(document).ready(function () {
        $('.txt_desc').off('blur');
        $('.txt_desc').on('blur',function(event){
            var keycode = (event.keyCode ? event.keyCode : event.which);
            id_detalle = $(this).attr("id");
            desc = $(this).val();
             if (isNaN(desc)) {
                $.Notification.notify('error','bottom center','ERROR', 'DIGITAR UN DESCUENTO VALIDO')
                $(this).focus();
                return false;
            }
    $.ajax({
        type: "POST",
        url: "../ajax/editar_desc_cot.php",
        data: "id_detalle=" + id_detalle + "&desc=" + desc,
        success: function(datos) {
           $("#resultados").load("../ajax/editar_tmp_cot.php");
           $.Notification.notify('success','bottom center','EXITO!', 'DESCUENTO ACTUALIZADO CORRECTAMENTE')
       }
   });
    });
     $(".employee_id").on("change", function(event) {
         id_detalle = $(this).attr("id");
        precio = $(this).val();
        $.ajax({
            type: "POST",
            url: "../ajax/editar_precio_cot2.php",
            data: "id_tmp=" + id_tmp + "&precio=" + precio,
            success: function(datos) {
               $("#resultados").load("../ajax/editar_tmp_cot.php");
               $.Notification.notify('success','bottom center','EXITO!', 'PRECIO ACTUALIZADO CORRECTAMENTE')
           }
       });
    });
    });
</script>