<?php
include 'is_logged.php';
$session_id = session_id();
if (isset($_POST['id'])) {$id = $_POST['id'];}
if (isset($_POST['idusuario'])) {$idusuario = $_POST['idusuario'];}else{$idusuario=1;}
if (isset($_POST['cantidad'])) {$cantidad = $_POST['cantidad'];}
require_once "../db.php";
require_once "../php_conexion.php";
require_once "../funciones.php";
if (!empty($id) and !empty($cantidad)) {
    $id_vendedor    = intval($_SESSION['id_users']);
    $id_producto  = get_row('productos', 'id_producto', 'codigo_producto', $id);
    $precio_venta = get_row('productos', 'valor1_producto', 'id_producto', $id_producto);
    $sqlUsuarioACT        = mysqli_query($conexion, "select * from users where id_users = '".$id_vendedor."'");
    $rw         = mysqli_fetch_array($sqlUsuarioACT);
    $id_sucursal = $rw['sucursal_users'];
    $consultaStock = "select * from productos left join stock on productos.id_producto = stock.id_producto_stock WHERE id_sucursal_stock = '".$id_sucursal."' and id_producto_stock = '".$id_producto."'";
    $query = mysqli_query($conexion, $consultaStock);
    $count = mysqli_num_rows($query);
    if($count!=0){
        $rw    = mysqli_fetch_array($query);
        $stock = $rw['cantidad_stock'];
        $inv   = $rw['inv_producto'];
    }else{
        $consultaStock = "select * from productos WHERE id_producto = '".$id_producto."'";
        $query = mysqli_query($conexion, $consultaStock);
        $count = mysqli_num_rows($query);
        $stock = 0;
        $inv   = $rw['inv_producto'];
    }
    $comprobar = mysqli_query($conexion, "select * from tbcarrito, productos where 
    productos.id_producto = tbcarrito.idproducto and  tbcarrito.idproducto='" . $id_producto .
     "' and idusuario='" . $idusuario. "'");

    if ($row = mysqli_fetch_array($comprobar)) {
        $cant = $row['cantidad'] + $cantidad;
        if ($cant > $stock and $inv == 0) { 
            echo "<script>swal('LA CANTIDAD SUPERA AL STOCK!', 'INTENTAR NUEVAMENTE ', 'error')
            $('#resultados').load('../ajax/agregar_tmpegreso.php');
        </script>";
            exit;
        } else {

            $sql          = "UPDATE tbcarrito SET cantidad='" . $cant . "' WHERE 
            idproducto='" . $id_producto . "' and idusuario='" . $idusuario . "'";
            $query_update = mysqli_query($conexion, $sql);
        }
    } else {
        if ($cantidad > $stock and $inv == 0) {
            echo( $cantidad."-cantidad       stock". $stock);
            echo "<script>swal('LA CANTIDAD SUPERA AL STOCK!', 'INTENTAR NUEVAMENTE', 'error')
        $('#resultados').load('../ajax/agregar_tmpegreso.php');
    </script>";
            exit;
        } else {
            $insert_tmp = mysqli_query($conexion, "INSERT INTO tbcarrito(idproducto,cantidad,
            idsucursal,idsuario) VALUES ('$id_producto','$cantidad','1','$idusuario')");
        }
    }
}
if (isset($_GET['id']))
{
    $id_tmp = intval($_GET['id']);
    $delete = mysqli_query($conexion, "DELETE FROM tbcarrito WHERE idcarrito='" . $id_tmp . "'");
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
    if($cantidad>0){$items+=$cantidad;}
    $nombre_producto = $row['nombre_producto'];
    ;
    ?>
    <tr>
        <td class='text-center'><?php echo $codigo_producto; ?></td>
        <td class='text-center'><?php echo $cantidad; ?></td>
        <td><?php echo $nombre_producto; ?></td>
         <td class='text-center'>
            <a href="#" class='btn btn-danger btn-sm waves-effect waves-light' onclick="eliminar('<?php echo $id_tmp ?>')"><i class="fa fa-remove"></i>
            </a>
        </td>
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