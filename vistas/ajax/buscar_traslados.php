<?php
include 'is_logged.php'; 
require_once "../db.php"; 
require_once "../php_conexion.php"; 
require_once "../funciones.php";
$user_id = $_SESSION['id_users'];
$user_sucursal = $_SESSION['idsucursal'];
$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != null) ? $_REQUEST['action'] : '';
if ($action == 'ajax') {
    $daterange      = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['range'], ENT_QUOTES)));
    $q      = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['q'], ENT_QUOTES)));
    $sTable = "detalle_traslado left join tbl_traslado on detalle_traslado.id_traslado = tbl_traslado.id left join productos on detalle_traslado.id_producto = productos.id_producto
     left join users on tbl_traslado.id_usuario = users.id_users left join perfil on tbl_traslado.id_sucursal_destino = perfil.id_perfil ";
     if($user_sucursal != 0){
        $sWhere = " WHERE tbl_traslado.id_sucursal_origen = '".$user_sucursal."' ";
    }
    if (!empty($daterange)) {
        list($f_inicio, $f_final)                    = explode(" - ", $daterange); //Extrae la fecha inicial y la fecha final en formato espa?ol
        list($dia_inicio, $mes_inicio, $anio_inicio) = explode("/", $f_inicio); //Extrae fecha inicial
        $fecha_inicial                               = "$anio_inicio-$mes_inicio-$dia_inicio 00:00:00"; //Fecha inicial formato ingles
        list($dia_fin, $mes_fin, $anio_fin)          = explode("/", $f_final); //Extrae la fecha final
        $fecha_final                                 = "$anio_fin-$mes_fin-$dia_fin 23:59:59";
        $sWhere .= " and tbl_traslado.fecha between '$fecha_inicial' and '$fecha_final' ";
    }
    $sWhere .= " order by tbl_traslado.id desc";
    include 'pagination.php';
    $page      = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page  = 10;
    $adjacents = 4; 
    $offset    = ($page - 1) * $per_page;
    $sentencia = "SELECT count(*) AS numrows FROM $sTable  $sWhere";
    $count_query = mysqli_query($conexion, $sentencia);
    $row         = mysqli_fetch_array($count_query);
    $numrows     = $row['numrows'];
    $total_pages = ceil($numrows / $per_page);
    $reload      = '../reportes/facturas.php';
    $sql = "SELECT detalle_traslado.*, tbl_traslado.*, productos.*, users.*, perfil.giro_empresa, perfil.codigoEstablecimiento 
        FROM $sTable $sWhere 
        LIMIT $offset,$per_page";
    $query = mysqli_query($conexion, $sql);
    if ($numrows > 0) {
        echo mysqli_error($conexion);
        ?>
        <div class="table-responsive">
          <table class="table table-sm table-striped">
             <tr  class="info">
                <th># Traslado</th>
                <th>Fecha</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Destino</th>
                <th>Usuario</th>
            </tr>
            <?php
while ($row = mysqli_fetch_array($query)) {
            $numero_factura   = $row['id_traslado'];
            $fecha            = date("d/m/Y", strtotime($row['fecha']));
            $nit_cliente = "";
            $booleanClienteBD = true;
            $cliente = "CF";
            $nombre_cliente   = $row['nombre_producto'];
            $canitdad         = $row['cantidad'];
            $nombre_vendedor  = $row['nombre_users'] . " " . $row['apellido_users'];
            $giro_empresa     =$row['giro_empresa'];
            $codigo_establecimiento = $row['codigoEstablecimiento'];
            $simbolo_moneda = "Q";
            ?>
                        <tr>
                         <td><label class='badge badge-purple'><?php echo $numero_factura; ?></label></td>
                         <td><?php echo $fecha; ?></td>
                         <td><?php echo $nombre_cliente; ?></td>
                         <td><?php echo $canitdad; ?></td>
                         <td><?php echo $codigo_establecimiento . " - " . $giro_empresa; ?></td>
                         <td><?php echo $nombre_vendedor; ?></td>
                           </div>
                       </div>
                   </td>
               </tr>
               <?php
}
        ?>
           <tr>
              <td colspan=7><span class="pull-right"><?php
echo paginate($reload, $page, $total_pages, $adjacents);
        ?></span></td>
            </tr>
        </table>
    </div>
    <?php
}
    else {
        ?>
    <div class="alert alert-warning alert-dismissible" role="alert" align="center">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <strong>Aviso!</strong> No hay Registro de Traslados
  </div>
  <?php
}
}
?>
