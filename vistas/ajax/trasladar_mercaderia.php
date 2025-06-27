<?php
include 'is_logged.php'; 
if (1 === 1 ){
    require_once "../db.php";
    require_once "../php_conexion.php";
    require_once "../funciones.php";
    $session_id     = session_id();
    $id_sucursalTraslado    = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['selec_sucursal'], ENT_QUOTES)));
    $simbolo_moneda = "Q";
    $sql_count = mysqli_query($conexion, "select * from tmp_ventas where session_id='" . $session_id . "'");
    $count     = mysqli_num_rows($sql_count);
    if ($count == 0) {
        echo "<script>
        swal({
          title: 'No hay Productos agregados en la factura',
          text: 'Intentar nuevamente',
          type: 'error',
          confirmButtonText: 'ok'
      })</script>";
        exit;
    }
    $id_vendedor    = intval($_SESSION['id_users']);
    date_default_timezone_set('America/Guatemala'); 
    $date_added     = date("Y-m-d H:i:s");
    $sqlUsuarioACT        = mysqli_query($conexion, "select * from users where id_users = '".$id_vendedor."'"); 
    $rw         = mysqli_fetch_array($sqlUsuarioACT);
    $id_sucursal = $rw['sucursal_users'];
    $sql           = mysqli_query($conexion, "select * from productos, tmp_ventas where productos.id_producto=tmp_ventas.id_producto and tmp_ventas.session_id='" . $session_id . "'");
    $registrar_traslado     = mysqli_query($conexion, "insert into tbl_traslado values(NULL,'$id_vendedor','$id_sucursal', '$id_sucursalTraslado','$date_added')");
    $id_tbl_traslado = $conexion->insert_id;
    while ($row = mysqli_fetch_array($sql)) {
        $id_tmp          = $row["id_tmp"];
        $id_producto     = $row['id_producto'];
        $codigo_producto = $row['codigo_producto'];
        $cantidad        = $row['cantidad_tmp'];
        $desc_tmp        = $row['desc_tmp'];
        $nombre_producto = $row['nombre_producto']; 
        $sqlStock           = mysqli_query($conexion, "select * from stock where id_producto_stock = '".$id_producto."' and id_sucursal_stock = '".$id_sucursal."'" ); //and tmp_ventas.session_id='" . $session_id . "'");
        if( !$sqlStock  )
        {
            $cantidadStock = 0;
        }else if(mysqli_num_rows($sqlStock)==0){
            $cantidadStock = 0;
        }else{
            while($row2 = mysqli_fetch_array($sqlStock)){
                $cantidadStock = $row2['cantidad_stock'];
                $nuevaCantidad = $cantidadStock - $cantidad;
                $insert_detail           = mysqli_query($conexion, "update stock set cantidad_stock = '".$nuevaCantidad."' where id_producto_stock = '".$id_producto."' and id_sucursal_stock = '".$id_sucursal."'");
                $insertarTraslado = "insert into detalle_traslado values(NULL, '$id_tbl_traslado', '$id_producto', '$cantidad', '0','')";
                $insert_detail           = mysqli_query($conexion, $insertarTraslado);
            }
        }
        $sqlTrasladoAumentar =  mysqli_query($conexion, "select * from stock where id_producto_stock = '".$id_producto."' and id_sucursal_stock = '".$id_sucursalTraslado."'" );
        $row3 = mysqli_fetch_array($sqlTrasladoAumentar);
        if(!$row3){
            $insertarInventario = "INSERT INTO stock (id_stock, id_producto_stock, id_sucursal_stock, cantidad_stock) 
            VALUES (NULL, '$id_producto', '$id_sucursalTraslado' ,'$cantidad');";
              $insert_detail = mysqli_query($conexion, $insertarInventario);
        }
        else if(empty($row3) || is_null($row3)){
            echo("no esta llena la variable <br>");
        }
        else{
            $cantidadStock = $row3['cantidad_stock'];
            $nuevaCantidad = $cantidadStock + $cantidad;
            $insert_detail           = mysqli_query($conexion, "update stock set cantidad_stock = '".$nuevaCantidad."' where id_producto_stock = '".$id_producto."' and id_sucursal_stock = '".$id_sucursalTraslado."'");
            while($row3 = mysqli_fetch_array($sqlTrasladoAumentar)){
                $cantidadStock = $row3['cantidad_stock'];
                $nuevaCantidad = $cantidadStock + $cantidad;
                $insert_detail           = mysqli_query($conexion, "update stock set cantidad_stock = '".$nuevaCantidad."' where id_producto_stock = '".$id_producto."' and id_sucursal_stock = '".$id_sucursalTraslado."'");
            }
        }
    }
    if ($insert_detail) {
        $delete = mysqli_query($conexion, "DELETE FROM tmp_ventas WHERE session_id='" .$session_id. "'");
        echo "<script>
        $('#outer_comprobante').load('../ajax/carga_correlativos.php');
        $('#resultados5').load('../ajax/carga_num_trans.php')
        $('#modal_vuelto').modal('show');
        </script>";
    } else {
        $errors[] = "Lo siento algo ha salido mal intenta nuevamente." . mysqli_error($conexion);
    }
}
?>
