<?php
include 'is_logged.php';
if (empty($_POST['mod_id'])) {
    $errors[] = "ID vacío";
} elseif (empty($_POST['mod_codigo'])) {
    $errors[] = "Codigo vacío";
} else if (empty($_POST['mod_nombre'])) {
    $errors[] = "Nombre del producto vacío";
} else if ($_POST['mod_linea'] == "") {
    $errors[] = "Selecciona una categoria del producto";
} else if ($_POST['mod_proveedor'] == "") {
    $errors[] = "Selecciona un Proveedor";
} else if (empty($_POST['mod_costo'])) {
    $errors[] = "Costo de Producto vacío";
} else if (empty($_POST['mod_precio'])) {
    $errors[] = "Precio de venta vacío";
} 
else if ($_POST['mod_estado'] == "") {
    $errors[] = "Selecciona el estado del producto";
} else if (
    !empty($_POST['mod_codigo']) &&
    !empty($_POST['mod_nombre']) &&
    $_POST['mod_linea'] != "" &&
    $_POST['mod_proveedor'] != "" &&
    $_POST['mod_inv'] != "" &&
    $_POST['mod_estado'] != "" &&
    !empty($_POST['mod_costo']) &&
    !empty($_POST['mod_precio']) 
) {
    require_once "../db.php";
    require_once "../php_conexion.php";
    $codigo      = mysqli_real_escape_string($conexion, (strip_tags($_POST["mod_codigo"], ENT_QUOTES)));
    $nombre      = mysqli_real_escape_string($conexion, (strip_tags($_POST["mod_nombre"], ENT_QUOTES)));
    $descripcion = mysqli_real_escape_string($conexion, (strip_tags($_POST["mod_descripcion"], ENT_QUOTES)));
    $linea       = intval($_POST['mod_linea']);
    $proveedor   = intval($_POST['mod_proveedor']);
    $inv      = intval($_POST['mod_inv']);
    $estado   = intval($_POST['mod_estado']);
    $costo           = floatval($_POST['mod_costo']);
    $utilidad        = floatval($_POST['mod_utilidad']);
    $precio_venta    = floatval($_POST['mod_precio']);
    $precio_mayoreo  = floatval($_POST['mod_preciom']);
    $precio_especial = floatval($_POST['mod_precioe']);
    $precio_cuatro   = floatval($_POST['mod_precioc']);
    $stock           = floatval($_POST['mod_stock']);
    $esGenerico='0';
    $medida='';
    $fechaVence='';
    $casa='0';
    if(isset($_POST['mod_bien']))
    {
        $bien  = $_POST['mod_bien'];
    }
    if(isset($_POST['mod_casa']))
    {
        $casa  = $_POST['mod_casa'];
    }
    if(isset($_POST['mod_fecha']))
    {
        $fechaVence       = $_POST['mod_fecha'];
    }
    if(isset($_POST['mod_fecha']))
    {
        $fechaVence       = $_POST['mod_fecha'];
    }
    $medida       =''; 
    try
    {
        if(isset($_POST['mod_medida']))
    {
        $medida       = $_POST['mod_medida'];
    }
    }
    catch(Exception $e){}
    if(strcmp($medida,'')==0)
    {
        $medida="UNI";
    }
    if (empty($_POST['mod_minimo'])) {
        $stock_minimo = 0;
    }else{
        $stock_minimo     = floatval($_POST['mod_minimo']);
    }
    if(isset($_POST['mod_generico']))
    {
        $esGenerico     =  $_POST['mod_generico'];
    }
    $idsucursal    =  $_POST['mod_sucursal'];
    $bien="B";
    if(strcmp($bien,'B')==0)
    {
        $bien=1;
    }
    else
    {
        $bien=0;
    }
    if($fechaVence == ''){
        $fechaVence = "NULL";
       
    }else{
        $fechaVence = "'".$fechaVence."'";
    }
  
    $id_producto     = $_POST['mod_id'];
    $sql             = "UPDATE productos SET codigo_producto='" . $codigo . "',
                                        nombre_producto='" . $nombre . "',
                                        descripcion_producto='" . $descripcion . "',
                                        id_linea_producto='" . $linea . "',
                                        id_proveedor='" . $proveedor . "',
                                        inv_producto='" . $inv . "',
                                        estado_producto='" . $estado . "',
                                        costo_producto='" . $costo . "',
                                        utilidad_producto='" . $utilidad . "',
                                        valor1_producto='" . $precio_venta . "',
                                        valor2_producto='" . $precio_mayoreo . "',
                                        valor3_producto='" . $precio_especial . "',
                                        valor4_producto='".$precio_cuatro."',
                                        stock_producto='" . $stock . "',
                                        medida='" . $medida . "',
                                        bien='" . $bien . "',
                                        idcasa='" . $casa . "',
                                        esGenerico = '".$esGenerico."'
                                        WHERE id_producto='" . $id_producto . "'";
 $sql2 = "UPDATE stock SET fecha_vencimiento=" . $fechaVence . ",
 stock_minimo = ".$stock_minimo." WHERE id_producto_stock='" . $id_producto . "' 
 and id_sucursal_stock=".$idsucursal;
    $query_update = mysqli_query($conexion, $sql);
    $query_update2 = mysqli_query($conexion, $sql2);
    if ($query_update) {
        $messages[] = "Producto ha sido actualizado satisfactoriamente.";
    } else {
        $errors[] = "Lo siento algo ha salido mal intenta nuevamente." . mysqli_error($conexion);
    }
} else {
    $errors[] = "Error desconocido.";
}
if (isset($errors)) {
    ?>
    <div class="alert alert-danger" role="alert">
        <strong>Error!</strong>
        <?php
foreach ($errors as $error) {
        echo $error;
    }
    ?>
    </div>
    <?php
}
if (isset($messages)) {
    ?>
    <div class="alert alert-success" role="alert">
        <strong>¡Bien hecho!</strong>
        <?php
foreach ($messages as $message) {
        echo $message;
    }
    ?>
    </div>
    <?php
}

?>