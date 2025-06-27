<?php
include "is_logged.php"; 
require_once "../db.php";
require_once "../php_conexion.php";
require_once "../funciones.php";
$user_id = $_SESSION['id_users'];
$action  = (isset($_REQUEST['action']) && $_REQUEST['action'] != null) ? $_REQUEST['action'] : '';
if ($action == 'ajax') {
    $daterange      = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['range'], ENT_QUOTES)));
    $employee_id    = intval($_REQUEST['employee_id']);
    $tables         = "tbegresos";
    $campos         = "*";
    $sWhere         = "";
    if ($employee_id > 0) {
        if(strcmp($sWhere,'')==0){}else{
            $sWhere .= " and ";
        }
        $sWhere .= "idvendedor = '" . $employee_id . "' ";
    }
    if (!empty($daterange)) {
        list($f_inicio, $f_final)                    = explode(" - ", $daterange); //Extrae la fecha inicial y la fecha final en formato espa?ol
        list($dia_inicio, $mes_inicio, $anio_inicio) = explode("/", $f_inicio); //Extrae fecha inicial
        $fecha_inicial                               = "$anio_inicio-$mes_inicio-$dia_inicio 00:00:00"; //Fecha inicial formato ingles
        list($dia_fin, $mes_fin, $anio_fin)          = explode("/", $f_final); //Extrae la fecha final
        $fecha_final                                 = "$anio_fin-$mes_fin-$dia_fin 23:59:59";
        if(strcmp($sWhere,'')==0){}else{
            $sWhere .= " and ";
        }
        $sWhere .= "fecha between '$fecha_inicial' and '$fecha_final' ";
    }
    $sWhere .= " order by idegreso";
    include 'pagination.php';
    $page      = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page  = 100;
    $adjacents = 4; 
    $offset    = ($page - 1) * $per_page;
    $count_query = mysqli_query($conexion, "SELECT count(*) AS numrows FROM $tables where $sWhere ");
    if ($row = mysqli_fetch_array($count_query)) {$numrows = $row['numrows'];} else {echo mysqli_error($conexion);}
    $total_pages = ceil($numrows / $per_page);
    $reload      = '../rep_egresos.php';
    $query = mysqli_query($conexion, "SELECT $campos FROM  $tables where $sWhere LIMIT $offset,$per_page");
    if ($numrows > 0) {
        ?>
        <div class="table-responsive">
            <table class="table table-condensed table-hover table-striped table-sm">
                <tr>
                    <th class='text-center'>ID</th>
                    <th>Vendedor</th>
                    <th class='text-center'>Despachador </th>
                    <th class='text-center'>Fecha Egreso </th>
                    <th>Sucursal </th>
                    <th>Acciones </th>
                </tr>
                <?php
$finales = 0;
        while ($row = mysqli_fetch_array($query)) {
            $id           = $row['idegreso'];
            $ven      = $row['idvendedor'];
            $usu          = $row['idusuario'];
            $fecha             = $row['fecha'];
            $suc        = $row['idsucursal'];
            $query2= mysqli_query($conexion, "select concat(nombre_users,' ',apellido_users) as nn from users where id_users =".$ven);
            while ($row1 = mysqli_fetch_array($query2)) 
            {
                $vendedor     = $row1['nn'];
            }
            $query2= mysqli_query($conexion, "select concat(nombre_users,' ',apellido_users) as nn from users where id_users =".$usu);
            while ($row1 = mysqli_fetch_array($query2)) 
            {
                $usuarioss    = $row1['nn'];
            }
            $query2= mysqli_query($conexion, "select giro_empresa from perfil where id_perfil =".$suc);
            while ($row1 = mysqli_fetch_array($query2)) 
            {
                $sucursal     = $row1['giro_empresa'];
            }
            list($date, $hora) = explode(" ", $fecha);
            list($Y, $m, $d)   = explode("-", $date);
            $fecha             = $d . "-" . $m . "-" . $Y;
            $finales++;
            $simbolo_moneda = "Q";
            ?>
                    <tr>
                        <td class='text-center'><label class='badge badge-purple'><?php echo $id; ?></label></td>
                        <td><?php echo $vendedor; ?></td>
                        <td class='text-center'><?php echo $usuarioss; ?></td>
                        <td><?php echo $fecha; ?></td>
                        <td><?php echo $sucursal; ?></td>
                        <td >
                            <div class="btn-group dropdown pull-right">
                                <button type="button" class="btn btn-warning btn-rounded waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"> <i class='fa fa-cog'></i> <i class="caret"></i> </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="rep_detallerutas.php?id=<?php echo $id;?>" ><i class='fa fa-list'></i> Ver detalles</a>
                                </div>
                            </div>
                        </td>
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