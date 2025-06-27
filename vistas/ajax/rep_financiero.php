<?php
include "is_logged.php"; 
require_once "../db.php";
require_once "../php_conexion.php";
require_once "../funciones.php";
$user_id = $_SESSION['id_users'];
$action  = (isset($_REQUEST['action']) && $_REQUEST['action'] != null) ? $_REQUEST['action'] : '';
if ($action == 'ajax') {
    $daterange = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['range'], ENT_QUOTES)));
    $categoria = intval($_REQUEST['categoria']);
    $tables    = "detalle_fact_ventas,  productos, facturas_ventas";
    $campos    = " productos.id_producto,codigo_producto,nombre_producto,
detalle_fact_ventas.cantidad ,costo_producto,desc_venta,importe_venta,precio_venta ";
    $sWhere    = "productos.id_producto=detalle_fact_ventas.id_producto and facturas_ventas.id_factura=detalle_fact_ventas.id_factura";
    if ($categoria > 0) {
        $sWhere .= " and productos.id_linea_producto = '" . $categoria . "'";
    }
    if (!empty($daterange)) {
        list($f_inicio, $f_final)                    = explode(" - ", $daterange); //Extrae la fecha inicial y la fecha final en formato espa?ol
        list($dia_inicio, $mes_inicio, $anio_inicio) = explode("/", $f_inicio); //Extrae fecha inicial
        $fecha_inicial                               = "$anio_inicio-$mes_inicio-$dia_inicio 00:00:00"; //Fecha inicial formato ingles
        list($dia_fin, $mes_fin, $anio_fin)          = explode("/", $f_final); //Extrae la fecha final
        $fecha_final                                 = "$anio_fin-$mes_fin-$dia_fin 23:59:59";

        $sWhere .= " and facturas_ventas.fecha_factura between '$fecha_inicial' and '$fecha_final' ";
    }
    $sWhere .= " group by detalle_fact_ventas.id_producto";
    include 'pagination.php'; 
    $page      = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page  = 100; 
    $adjacents = 4; 
    $offset    = ($page - 1) * $per_page;
    $numrows     = 0;
    $count_query = mysqli_query($conexion, "SELECT count(*) AS numrows FROM $tables where $sWhere ");
    if ($row = mysqli_fetch_array($count_query)) {$numrows = $row['numrows'];} else {echo mysqli_error($conexion);}
    $total_pages = ceil($numrows / $per_page);
    $reload      = '../rep_ventas.php';
     $query = mysqli_query($conexion, "SELECT $campos FROM  $tables where $sWhere LIMIT $offset,$per_page");
    if ($numrows > 0) {
        ?>
        <div class="table-responsive">
            <table class="table table-condensed table-hover table-striped table-sm">
                <tr>
                    <th class='text-center'>Codigo</th>
                    <th>Producto</th>
                    <th>Cant.</th>
                    <th>Costo</th>
                    <th>Total Costo</th>
                    <th>Desc.</th>
                    <th>Total Vendido</th>
                    <th>Utilidad</th>
                </tr>
                <?php
$finales = 0;
        while ($row = mysqli_fetch_array($query)) {
            $id_producto     = $row['id_producto'];
            $codigo_producto = $row['codigo_producto'];
            $nombre_producto = $row['nombre_producto'];
            $costo_producto  = $row['costo_producto'];
            $precio_vendido  = $row['precio_venta'];
            $costo_saldo     = $row['costo_producto'];
            $cantidad    = $row['cantidad'];
            $desc_venta  = $row['desc_venta'];
            $total_costo = $cantidad * $costo_saldo;
            $total_pv    = $row['importe_venta'];
            $final_items = rebajas($total_pv, $desc_venta); 
            $descuento   = $total_pv - $final_items;
            $utilidad    = $final_items - $total_costo;
            $finales++;
            $simbolo_moneda ="Q";
            ?>
                    <tr>
                        <td class='text-center'><label class='badge badge-purple'><?php echo $codigo_producto; ?></label></td>
                        <td><?php echo $nombre_producto; ?></td>
                        <td><span class="badge badge-success"><?php echo $cantidad; ?></span></td>
                        <td class='text-left'><?php echo $simbolo_moneda . '' . number_format($costo_saldo, 2); ?></td>
                        <td><b><?php echo $simbolo_moneda . '' . number_format($total_costo, 2); ?></b></td>
                        <td><?php echo $simbolo_moneda . '' . number_format($descuento, 2); ?></td>
                        <td><b><?php echo $simbolo_moneda . '' . number_format($final_items, 2); ?></b></td>
                        <td><b><?php echo $simbolo_moneda . '' . number_format($utilidad, 2); ?></b></td>
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