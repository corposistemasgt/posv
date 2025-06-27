<?php
include 'is_logged.php'; 
require_once "../db.php";
require_once "../php_conexion.php"; 
require_once "../funciones.php";
$user_id = $_SESSION['id_users'];
get_cadena($user_id);
$modulo = "Ventas";
permisos($modulo, $cadena_permisos);
$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != null) ? $_REQUEST['action'] : '';
if ($action == 'ajax') {
    $q      = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['q'], ENT_QUOTES)));
    $sTable = "creditos,facturas_ventas,users,clientes  ";
    $sWhere = " where creditos.numero_factura =facturas_ventas.numero_factura and 
    facturas_ventas.id_vendedor =users.id_users  
    and facturas_ventas.id_cliente=clientes.id_cliente ";
    if ($_GET['q'] != "") {
        $sWhere .= " and (facturas_ventas.factura_nombre_cliente like '%$q%' or creditos.numero_factura like '%$q%')";
    }
    if ($_GET['idruta'] != "") {
        $sWhere .= " and idruta=".$_GET['idruta'];
    }
    $sWhere .= " order by creditos.id_credito desc";
    include 'pagination.php';
    $page      = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page']:1;
    $per_page  = 10; 
    $adjacents = 4; 
    $offset    = ($page - 1) * $per_page;
    $count_query = mysqli_query($conexion, "SELECT count(*) AS numrows FROM $sTable  $sWhere");
    $row         = mysqli_fetch_array($count_query);
    $numrows     = $row['numrows'];
    $total_pages = ceil($numrows / $per_page);
    $reload      = '../reportes/facturas.php';
    $sql   = "SELECT * FROM  $sTable $sWhere LIMIT $offset,$per_page";
    $query = mysqli_query($conexion, $sql);
    if ($numrows > 0) {
        ?>
        <div class="table-responsive">
          <table class="table table-sm table-striped">
             <tr  class="info">
                <th># Factura</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Estado</th>
                <th>Dias de Cr.</th>
                <th>Dias Restantes.</th>
                <th class='text-center'>Crédito</th>
                <th class='text-center'>Saldo</th>
                <th class='text-center'>Acciones</th>
            </tr>
            <?php
while ($row = mysqli_fetch_array($query)) {
            $id_credito       = $row['id_credito'];
            $numero_factura   = $row['numero_factura'];
            $fecha            = date("d/m/Y", strtotime($row['fecha_credito']));
            $nombre_cliente   = $row['factura_nombre_cliente'];
            $telefono_cliente = $row['factura_nombre_cliente']; 
            $email_cliente    = $row['correos'];
            $nombre_vendedor  = $row['nombre_users'] . " " . $row['apellido_users'];
            $estado_factura   = $row['estado_credito'];
            $dias   = $row['limite_credito'];
            $fecha1 = new DateTime($row['fecha_credito']);
            $fecha1->modify("+$dias days");
            $fechaActual = new DateTime();
            $diferencia = $fechaActual->diff($fecha1);
            $diasr=$diferencia->days;
            if ($estado_factura == 2) {
                $text_estado = "Pagada";
                $label_class = 'badge-success';} else {
                $text_estado = "Pendiente";
                $label_class = 'badge-danger';}
            $total_venta    = $row['monto_credito'];
            $saldo          = $row['saldo_credito'];
            $simbolo_moneda = "Q";
            ?>
                        <tr>
                         <td><label class='badge badge-purple'><?php echo $numero_factura; ?></label></td>
                         <td><?php echo $fecha; ?></td>
                         <td><?php echo $nombre_cliente; ?></td>
                         <td><span class="badge <?php echo $label_class; ?>"><?php echo $text_estado; ?></span></td>
                         <td class='text-left'><b><?php echo $dias;?></b></td>
                         <td class='text-left'><b><?php echo $diasr;?></b></td>
                         <td class='text-left'><b><?php echo $simbolo_moneda . '' . number_format($total_venta, 2); ?></b></td>
                         <td class='text-left'><b><?php echo $simbolo_moneda . '' . number_format($saldo, 2); ?></b></td>                      
                         <td class="text-center">
                          <div class="btn-group dropdown">
                            <button type="button" class="btn btn-warning btn-sm dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"> <i class='fa fa-cog'></i> <i class="caret"></i> </button>
                            <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="abonos_cxc.php?numero_factura=<?php echo $numero_factura; ?>"><i class='fa fa-search'></i> Ver Abonos</a>
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
      <strong>Aviso!</strong> No hay Registro de Créditos
  </div>
  <?php
}
}
?>