<?php
include 'is_logged.php'; 
$session_id = session_id();
require_once "../db.php";
require_once "../php_conexion.php";
require_once "../funciones.php";
if (isset($_POST['id'])) {$id_producto = $_POST['id'];}
if (isset($_POST['cantidad'])) {$cantidad = $_POST['cantidad'];}
if (isset($_POST['precio_venta'])) {$precio_venta = $_POST['precio_venta'];}
if (!empty($id_producto) and !empty($cantidad) and !empty($precio_venta)) {
    $id_producto_vendedor    = intval($_SESSION['id_users']);
    $id_producto_sucursal = $_SESSION['idsucursal'];
    $consultaStock = "select * from productos left join stock on productos.id_producto = stock.id_producto_stock WHERE id_sucursal_stock = '".$id_producto_sucursal."' and id_producto_stock = '".$id_producto."'";
    $query = mysqli_query($conexion, $consultaStock);
    $count = mysqli_num_rows($query);
    if($count!=0){
        $rw    = mysqli_fetch_array($query);
        $stock = $rw['cantidad_stock'];
        $inv   = $rw['inv_producto'];
    }else{
        $stock = 0;
        $consultaManejaInventario = "select * from productos where id_producto = '$id_producto'";
        $query2 = mysqli_query($conexion, $consultaManejaInventario);
        $count2 = mysqli_num_rows($query2);
        
        if($count2!=0){
            $rw    = mysqli_fetch_array($query2);
            $inv   = $rw['inv_producto'];
        }else{
            $inv   = 0;
        }
    }
    $comprobar = mysqli_query($conexion, "select * from tmp_ventas, productos where productos.id_producto = tmp_ventas.id_producto and tmp_ventas.id_producto='" . $id_producto . "' and tmp_ventas.session_id='" . $session_id . "'");
    if ($row = mysqli_fetch_array($comprobar)) {
        $cant = $row['cantidad_tmp'] + $cantidad;
        if ($cant > $stock and $inv == 0) {
            echo "<script>swal('LA CATIDAD SUPERA AL STOCK', 'INTENTAR NUEVAMENTE', 'error')
            $('#resultados').load('../ajax/agregar_tmp.php');
            </script>";
            exit;
        } else {

            $sql          = "UPDATE tmp_ventas SET cantidad_tmp='" . $cant . "', precio_tmp='" . $precio_venta . "' WHERE id_producto='" . $id_producto . "' and session_id='" . $session_id . "'";
            $query_update = mysqli_query($conexion, $sql);
            echo "<script> $.Notification.notify('success','bottom center','NOTIFICACIÓN', 'PRODUCTO AGREGADO A LA FACTURA CORRECTAMENTE') 
          
            </script>";
        }
    } else {
        if ($cantidad > $stock and $inv == 0) {
            echo "<script>swal('LA CATIDAD SUPERA AL STOCK', 'INTENTAR NUEVAMENTE', 'error')
             $('#resultados').load('../ajax/agregar_tmp.php');
            </script>";
            exit;
        } else {
            $insert_tmp = mysqli_query($conexion, "INSERT INTO tmp_ventas (id_producto,cantidad_tmp,precio_tmp,desc_tmp,session_id) VALUES ('$id_producto','$cantidad','$precio_venta','0','$session_id')");
            echo "<script> $.Notification.notify('success','bottom center','NOTIFICACIÓN', 'PRODUCTO AGREGADO A LA FACTURA CORRECTAMENTE')
            
            </script>";
        }
    }
}
if (isset($_GET['id']))
{
    //detalle(1,". $id_producto."," . $precio_venta . "," . $cant . ");
    $id_producto_tmp = intval($_GET['id']);
    $delete = mysqli_query($conexion, "DELETE FROM tmp_ventas WHERE id_tmp='" . $id_producto_tmp . "'");
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
$subtotal       = 0;
$sql            = mysqli_query($conexion, "select * from productos, tmp_ventas where productos.id_producto=tmp_ventas.id_producto and tmp_ventas.session_id='" . $session_id . "'");
while ($row = mysqli_fetch_array($sql)) {
    $id_producto_tmp          = $row["id_tmp"];
    $codigo_producto = $row['codigo_producto'];
    $id_producto     = $row['id_producto'];
    $cantidad        = $row['cantidad_tmp'];
    $desc_tmp        = $row['desc_tmp'];
    $nombre_producto = $row['nombre_producto'];
    $precio_venta   = $row['precio_tmp'];
    $precio_venta_f = number_format($precio_venta, 2);
    $precio_venta_r = str_replace(",", "", $precio_venta_f); 
    $precio_total   = $precio_venta_r * $cantidad;
    $final_items    = rebajas($precio_total, $desc_tmp); 
    $precio_total_f = number_format($final_items, 2); 
    $precio_total_r = str_replace(",", "", $precio_total_f); 
    $sumador_total += $precio_total_r; 
    $final_items = rebajas($precio_total, $desc_tmp); 
    $subtotal    = number_format($sumador_total, 2, '.', '');
    ?>
    <tr>
        <td class='text-center'><?php echo $codigo_producto; ?></td>
        <td class='text-center'><?php echo $cantidad; ?></td>
        <td><?php echo $nombre_producto; ?></td>
        <td class='text-center'>
            <div class="input-group">
                <select id="<?php echo $id_producto_tmp; ?>" class="form-control employee_id">
                    <?php
$sql1 = mysqli_query($conexion, "select * from productos where id_producto='" . $id_producto . "'");
    while ($rw1 = mysqli_fetch_array($sql1)) {
        ?>
                         <option selected disabled value="<?php echo $precio_venta ?>"><?php echo number_format($precio_venta, 2); ?></option>
                        <option value="<?php echo $rw1['valor1_producto'] ?>">PUB <?php echo number_format($rw1['valor1_producto'], 2); ?></option>
                        <option value="<?php echo $rw1['valor2_producto'] ?>">PROM <?php echo number_format($rw1['valor2_producto'], 2); ?></option>
                        <option value="<?php echo $rw1['valor3_producto'] ?>">MAY <?php echo number_format($rw1['valor3_producto'], 2); ?></option>
                        <option value="<?php echo $rw1['valor4_producto'] ?>">ESP <?php echo number_format($rw1['valor4_producto'], 2); ?></option>
                        <?php
}
    ?>
                </select>
            </div>
        </td>
        <td align="right" width="15%">
            <?php 
            $es="";
            if(getpermiso(3)==0)
            {
                $es="disabled";
            }?>
                <input type="text" <?php echo $es;?> class="form-control txt_desc" style="text-align:center" value="<?php echo $desc_tmp; ?>" id="<?php echo $id_producto_tmp; ?>">
        </td>
        <td class='text-right'><?php echo $simbolo_moneda . ' ' . number_format($final_items, 2); ?></td>
        <td class='text-center'>
            <a href="#" class='btn btn-danger btn-sm waves-effect waves-light' onclick="eliminar('<?php echo $id_producto_tmp ?>')"><i class="fa fa-remove"></i>
            </a>
        </td>
    </tr>
    <?php
}
$total_factura = $subtotal;

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
        // if(keycode == '13'){
            id_tmp = $(this).attr("id");
            desc = $(this).val();
             //Inicia validacion
             if (isNaN(desc)) {
                $.Notification.notify('error','bottom center','ERROR', 'DIGITAR UN DESCUENTO VALIDO')
                $(this).focus();
                return false;
            }
    //Fin validacion
    $.ajax({
        type: "POST",
        url: "../ajax/editar_desc_venta.php",
        data: "id_tmp=" + id_tmp + "&desc=" + desc,
        success: function(datos) {
           $("#resultados").load("../ajax/agregar_tmp.php");
           detalle(5,id_tmp,0,0);
           $.Notification.notify('success','bottom center','EXITO!', 'DESCUENTO ACTUALIZADO CORRECTAMENTE')
       }
   });
        // }
    });
     $(".employee_id").on("change", function(event) {
         id_tmp = $(this).attr("id");
        precio = $(this).val();
        $.ajax({
            type: "POST",
            url: "../ajax/editar_precio_venta.php",
            data: "id_tmp=" + id_tmp + "&precio=" + precio,
            success: function(datos) {
               $("#resultados").load("../ajax/agregar_tmp.php");
               detalle(4,id_tmp,0,0);
               $.Notification.notify('success','bottom center','EXITO!', 'PRECIO ACTUALIZADO CORRECTAMENTE')
           }
       });
    });

    });
    function isMobile(){
    return (
        (navigator.userAgent.match(/Android/i)) ||
        (navigator.userAgent.match(/webOS/i)) ||
        (navigator.userAgent.match(/iPhone/i)) ||
        (navigator.userAgent.match(/iPod/i)) ||
        (navigator.userAgent.match(/iPad/i)) ||
        (navigator.userAgent.match(/BlackBerry/i))
    );
}
</script>