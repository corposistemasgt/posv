<?php
include 'is_logged.php'; 
require_once "../db.php";
require_once "../php_conexion.php"; 
require_once "../funciones.php";
$permisos_editar =getpermiso(30);
$user_id = $_SESSION['id_users'];
$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != null) ? $_REQUEST['action'] : '';
if ($action == 'ajax') {
    $q      = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['q'], ENT_QUOTES)));
    $sTable = "credito_proveedor, proveedores, users";
    $sWhere = "";
    $sWhere .= " WHERE credito_proveedor.id_proveedor=proveedores.id_proveedor and credito_proveedor.id_users_credito=users.id_users";
    if ($_GET['q'] != "") {
        $sWhere .= " and  (proveedores.nombre_proveedor like '%$q%' or credito_proveedor.numero_factura like '%$q%')";

    }
    $sWhere .= " order by credito_proveedor.id_credito desc";
    include 'pagination.php'; 
    $page      = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page  = 10; 
    $adjacents = 4;
    $offset    = ($page - 1) * $per_page;
    $count_query = mysqli_query($conexion, "SELECT count(*) AS numrows FROM $sTable  $sWhere");
    $row         = mysqli_fetch_array($count_query);
    $numrows     = $row['numrows'];
    $total_pages = ceil($numrows / $per_page);
    $reload      = '../html/cxp.php';
    $sql   = "SELECT * FROM  $sTable $sWhere LIMIT $offset,$per_page";
    $query = mysqli_query($conexion, $sql);
    if ($numrows > 0) {
        echo mysqli_error($conexion);
        ?>
        <div class="table-responsive">
          <table class="table table-sm table-striped">
             <tr  class="info">
                <th># Factura</th>
                <th>Fecha</th>
                <th>Proveedor</th>
                <th>Estado</th>
                <th class='text-center'>Crédito</th>
                <th class='text-center'>Saldo</th>
                <th class='text-center'>Acciones</th>
            </tr>
            <?php
while ($row = mysqli_fetch_array($query)) {
            $id_credito       = $row['id_credito'];
            $numero_factura   = $row['numero_factura'];
            $fecha            = date("d/m/Y", strtotime($row['fecha_credito']));
            $nombre_proveedor = $row['nombre_proveedor'];
            $nombre_vendedor  = $row['nombre_users'] . " " . $row['apellido_users'];
            $estado_factura   = $row['estado_credito'];
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
                         <td><?php echo $nombre_proveedor; ?></td>
                         <td><span class="badge <?php echo $label_class; ?>"><?php echo $text_estado; ?></span></td>
                         <td class='text-left'><b><?php echo $simbolo_moneda . '' . number_format($total_venta, 2); ?></b></td>
                         <td class='text-left'><b><?php echo $simbolo_moneda . '' . number_format($saldo, 2); ?></b></td>
                         <td class="text-center">
                          <div class="btn-group dropdown">
                            <button type="button" class="btn btn-warning btn-sm dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"> <i class='fa fa-cog'></i> <i class="caret"></i> </button>
                            <div class="dropdown-menu dropdown-menu-right">
                               <?php if ($permisos_editar == 1) {?>
                               <a class="dropdown-item" href="abonos_cxp.php?numero_factura=<?php echo $numero_factura; ?>"><i class='fa fa-search'></i> Ver Abonos</a>
                               <?php }?>
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
      <strong>Aviso!</strong> No hay Registro de Cuantas por Pagar
  </div>
  <?php
}
}
?>