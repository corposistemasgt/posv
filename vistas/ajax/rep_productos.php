<?php
include "is_logged.php"; 
require_once "../db.php";
require_once "../php_conexion.php";
require_once "../funciones.php";
$user_id = $_SESSION['id_users'];
$sqlUsuarioACT        = mysqli_query($conexion, "select * from users inner join perfil on sucursal_users = perfil.id_perfil 
inner join user_group on users.cargo_users = user_group.user_group_id 
where id_users = '".$user_id."'"); 
    $rw         = mysqli_fetch_array($sqlUsuarioACT);
    $id_sucursal = $rw['sucursal_users'];
    $nombreSucursal = $rw['giro_empresa'];
    $nombreCargo      = $rw['name'];
$action  = (isset($_REQUEST['action']) && $_REQUEST['action'] != null) ? $_REQUEST['action'] : '';
if ($action == 'ajax') {
    $id_categoria = intval($_REQUEST['categoria']);
    $id_casa = intval($_REQUEST['casa']);
    $tables       = " productos,stock,lineas,perfil  ";
    $campos       = " id_producto,codigo_producto,nombre_producto,nombre_linea,giro_empresa,cantidad_stock,
valor1_producto,valor2_producto,valor3_producto,estado_producto,productos.date_added,costo_producto ";
    $sWhere       = " where id_producto =id_producto_stock and 
id_linea_producto =id_linea and id_sucursal_stock =id_perfil ";
    if ($id_categoria > 0) {
        $sWhere .= " and productos.id_linea_producto = '" . $id_categoria . "' ";
    }
    if ($id_casa > 0) {
        $sWhere .= " and idcasa= '" . $id_casa. "' ";
    }
    $sWhere .= " order by productos.id_producto ";
    include 'pagination.php'; 
    $page      = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page  = 100; 
    $adjacents = 4; 
    $offset    = ($page - 1) * $per_page;
    $count_query = mysqli_query($conexion, "SELECT count(*) AS numrows FROM $tables $sWhere ");
    if ($row = mysqli_fetch_array($count_query)) {$numrows = $row['numrows'];} else {echo mysqli_error($conexion);}
    $total_pages = ceil($numrows / $per_page);
    $reload      = '../rep_ventas.php';
    $query = mysqli_query($conexion, "SELECT $campos FROM  $tables $sWhere LIMIT $offset,$per_page");
    if ($numrows > 0) {
        ?>
        <div class="table-responsive">
            <table class="table table-condensed table-hover table-striped table-sm ">
                <tr>
                    <th class='text-center'>Codigo</th>
                    <th>Nombre</th>
                    <th>Categoria</th>
                    <th>Sucursal</th>
                    <th>Stock</th>
                    <th class='text-left'>Costo </th>
                    <th class='text-left'>Precio V. </th>
                    <th class='text-left'>Precio M. </th>
                    <th class='text-left'>Precio E. </th>
                    <th class='text-center'>Estado </th>
                    <th>Agregado </th>
                </tr>
                <?php
$finales = 0;
        while ($row = mysqli_fetch_array($query)) {
            $id_producto = $row['id_producto'];
            $codigo           = $row['codigo_producto'];
            $nombre_producto  = $row['nombre_producto'];
            $nombre_linea     = $row['nombre_linea'];
            $costo_producto   = $row['costo_producto'];
            $precio_venta     = $row['valor1_producto'];
            $precio_mayorista = $row['valor2_producto'];
            $precio_especial  = $row['valor3_producto'];
            $estado_producto  = $row['estado_producto'];
            $stock_producto   = $row['cantidad_stock'];
            $sucursal_producto= $row['giro_empresa'];
            $date_added       = date('d/m/Y', strtotime($row['date_added']));
            if ($estado_producto == 1) {
                $estado = "<label class='badge badge-success'>Activo</label>";
            } else {
                $estado = "<label class='badge badge-danger'>Inactivo</label>";
            }
            $simbolo_moneda = "Q";
            ?>
                    <tr>
                        <td class='text-center'><label class='badge badge-purple'><?php echo $codigo; ?></label></td>
                        <td class='text-left'><?php echo $nombre_producto; ?></td>
                        <td class='text-left'><?php echo $nombre_linea; ?></td>
                        <td class='text-center'><?php echo $nombreSucursal ?></td>
                        <td class='text-center'><?php echo $stock_producto ?></td>
                        <td class='text-left'><?php echo $simbolo_moneda . '' . number_format($costo_producto, 2); ?></td>
                        <td class='text-left'><?php echo $simbolo_moneda . '' . number_format($precio_venta, 2); ?></td>
                        <td class='text-left'><?php echo $simbolo_moneda . '' . number_format($precio_mayorista, 2); ?></td>
                        <td class='text-left'><?php echo $simbolo_moneda . '' . number_format($precio_especial, 2); ?></td>
                        <td class='text-center'><?php echo $estado; ?></td>
                        <td class='text-center'><?php echo $date_added; ?></td>
                    </tr>
                    <?php }?>
                </table>
            </div>
            <div class="box-footer clearfix" align="right">
                <?php
$inicios = $offset + 1;
        $finales += $inicios - 1;
        echo "Mostrando $inicios al $finales de $numrows registros";
        echo paginate($reload, $page, $total_pages, $adjacents);?>
            </div>
            <?php
}
}
?>