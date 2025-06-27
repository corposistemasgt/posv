<?php
include 'is_logged.php';
$session_id = session_id();
if (isset($_POST['idcoti'])) {$idcoti = $_POST['idcoti'];}
require_once "../db.php";
require_once "../php_conexion.php";
require_once "../funciones.php";
if (empty($idcoti) ) {
    $idcoti=0;
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
                <th class='text-right'>TOTAL</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
$sumador_total  = 0;
$subtotal       = 0;
$sql            = mysqli_query($conexion, "select * from productos, tbdetallecot where id_producto=idproducto and idcotizacion='" . $idcoti . "'");
while ($row = mysqli_fetch_array($sql)) {
    $id_tmp          = $row["iddetalle"];
    $id_producto     = $row['id_producto'];
    $codigo_producto = $row['codigo_producto'];
    $cantidad        = $row['cantidad'];
    $nombre_producto = $row['nombre_producto'];
    $precio_venta   = $row['precio'];
    $precio_venta_f = number_format($precio_venta, 2);
    $precio_venta_r = str_replace(",", "", $precio_venta_f);
    $precio_total   = $precio_venta_r * $cantidad;
    $final_items    = rebajas($precio_total, 0);
    $precio_total_f = number_format($final_items, 2);
    $precio_total_r = str_replace(",", "", $precio_total_f);
    $sumador_total += $precio_total_r; //Sumador
    $subtotal = number_format($sumador_total, 2, '.', '');
    ?>
    <tr>
        <td class='text-center'><?php echo $codigo_producto; ?></td>
        <td class='text-center'><?php echo $cantidad; ?></td>
        <td><?php echo $nombre_producto; ?></td>
        <td class='text-center'>
            <div class="input-group">
                <select id="<?php echo $id_tmp; ?>" class="form-control employee_id" >
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
        <td class='text-right'><?php echo $simbolo_moneda . ' ' . number_format($final_items, 2); ?></td>
        <td class='text-center'>
            <a href="#" class='btn btn-danger btn-sm waves-effect waves-light' onclick="eliminar('<?php echo $id_tmp ?>')"><i class="fa fa-remove"></i>
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
    <td style="font-size: 16pt;" class='text-right'><b><?php echo number_format($total_factura, 2); ?></b></td>
    <td></td>
</tr>
</tbody>
</table>
</div>
<script>
    $(document).ready(function () {
               $(".employee_id").on("change", function(event) {
         id_tmp = $(this).attr("id");
        precio = $(this).val();
        console.log("--"+precio+"//");   
        $.ajax({
            type: "POST",
            url: "../ajax/editar_precio_cot_escuela.php",
            data: "id_tmp=" + id_tmp + "&precio=" + precio,
            success: function(datos) {
              load();
              console.log(datos);
               $.Notification.notify('success','bottom center','EXITO!', 'PRECIO ACTUALIZADO CORRECTAMENTE')
           }
       });
    });
    });
</script>