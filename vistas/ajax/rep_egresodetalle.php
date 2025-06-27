<?php
include "is_logged.php"; 
require_once "../db.php";
require_once "../php_conexion.php";
require_once "../funciones.php";
$user_id = $_SESSION['id_users'];
$action  = (isset($_REQUEST['action']) && $_REQUEST['action'] != null) ? $_REQUEST['action'] : '';
if ($action == 'ajax') {
    $id    = intval($_REQUEST['ide']);
    $tables         = "tbdetalleegreso,productos";
    $campos         = "*";
    $sWhere         = " idegreso=".$id." and idproducto=codigo_producto";
    $sWhere .= " order by iddetalle";
    include 'pagination.php';
    $page      = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page  = 100;
    $adjacents = 4; 
    $offset    = ($page - 1) * $per_page;
    $count_query = mysqli_query($conexion, "SELECT count(*) AS numrows FROM $tables where $sWhere ");
    if ($row = mysqli_fetch_array($count_query)) {$numrows = $row['numrows'];} else {echo mysqli_error($conexion);}
    $total_pages = ceil($numrows / $per_page);
    $reload      = '../rep_detallerutas.php';
    $query = mysqli_query($conexion, "SELECT $campos FROM  $tables where $sWhere LIMIT $offset,$per_page");
    if ($numrows > 0) {
        ?>
        <div class="table-responsive">
            <table class="table table-condensed table-hover table-striped table-sm">
                <tr>
                    <th class='text-center'>ID</th>
                    <th>Producto</th>
                    <th class='text-center'>Cantidad</th>
                </tr>
                <?php
            $finales = 0;
        while ($row = mysqli_fetch_array($query)) {
            $id           = $row['iddetalle'];
            $producto      = $row['nombre_producto'];
            $cantidad          = $row['cantidad'];
            ?>
                    <tr>
                        <td class='text-center'><label class='badge badge-purple'><?php echo $id; ?></label></td>
                        <td><?php echo $producto; ?></td>
                        <td><?php echo $cantidad; ?></td>
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