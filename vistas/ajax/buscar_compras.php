<?php
include 'is_logged.php'; 
require_once "../db.php"; 
require_once "../php_conexion.php"; 
require_once "../funciones.php";   
$user_id = $_SESSION['id_users'];
$idsucursal=$_SESSION['idsucursal'];
$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != null) ? $_REQUEST['action'] : '';
if ($action == 'ajax') {
    $q      = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['q'], ENT_QUOTES)));
    $sTable = "facturas_compras, proveedores, users";
    $sWhere = "";
    $sWhere .= " WHERE facturas_compras.id_proveedor=proveedores.id_proveedor and facturas_compras.id_vendedor=users.id_users and facturas_compras.id_sucursal = '".$idsucursal ."'";
    if ($_GET['q'] != "") {
        $sWhere .= " and  (proveedores.nombre_proveedor like '%$q%' or facturas_compras.numero_factura like '%$q%')";

    }
    $sWhere .= " order by facturas_compras.id_factura desc";
    include 'pagination.php';
    $page      = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page  = 10;
    $adjacents = 4; 
    $offset    = ($page - 1) * $per_page;
    $count_query = mysqli_query($conexion, "SELECT count(*) AS numrows FROM $sTable  $sWhere");
    $row         = mysqli_fetch_array($count_query);
    $numrows     = $row['numrows'];
    $total_pages = ceil($numrows / $per_page);
    $reload      = '../html/bitacoras_compras.php';
    $sql   = "SELECT * FROM  $sTable $sWhere LIMIT $offset,$per_page";
    $query = mysqli_query($conexion, $sql);
    if ($numrows > 0) {
        echo mysqli_error($conexion);
        ?>
        <div class="table-responsive">
          <table class="table table-sm table-striped">
             <tr  class="info">
                <th># Factura</th>
                <th>Referencia</th>
                <th>Fecha</th>
                <th>Proveedor</th>
                <th>Usuario</th>
                <th>Estado</th>
                <th class='text-center'>Total</th>
                <th class='text-center'>Acciones</th>

            </tr>
            <?php
while ($row = mysqli_fetch_array($query)) {
            $id_factura         = $row['id_factura'];
            $numero_factura     = $row['numero_factura'];
            $referencia     = $row['ref_factura'];
            $fecha              = date("d/m/Y", strtotime($row['fecha_factura']));
            $nombre_proveedor   = $row['nombre_proveedor'];
            $telefono_proveedor = $row['telefono_proveedor'];
            $email_proveedor    = $row['email_proveedor'];
            $contacto_proveedor = $row['contacto_proveedor'];
            $nombre_vendedor    = $row['nombre_users'] . " " . $row['apellido_users'];
            $estado_factura     = $row['estado_factura'];
            if ($estado_factura == 1) {
                $text_estado = "Pagada";
                $label_class = 'badge-success';} else {
                $text_estado = "Pendiente";
                $label_class = 'badge-warning';}
            $total_venta    = $row['monto_factura'];
            $simbolo_moneda = "Q";
            ?>
                        <tr>
                         <td><label class='badge badge-purple'><?php echo $numero_factura; ?></label></td>
                         <td><?php echo $referencia; ?></td>
                         <td><?php echo $fecha; ?></td>
                         <td><?php echo $nombre_proveedor; ?></td>
                         <td><?php echo $nombre_vendedor; ?></td>
                         <td><span class="badge <?php echo $label_class; ?>"><?php echo $text_estado; ?></span></td>
                         <td class='text-left'><b><?php echo $simbolo_moneda . '' . number_format($total_venta, 2); ?></b></td>
                         <td class="text-center">
                          <div class="btn-group dropdown">
                            <button type="button" class="btn btn-warning btn-sm dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"> <i class='fa fa-cog'></i> <i class="caret"></i> </button>
                            <div class="dropdown-menu dropdown-menu-right">
                               <?php if (getpermiso(21)==1) {?>
                               <a class="dropdown-item" href="editar_compra.php?id_factura=<?php echo $id_factura; ?>"><i class='fa fa-edit'></i> Editar</a>
                               <?php }?>
                               <a class="dropdown-item" href="#" onclick="printOrder('<?php echo $row['id_factura']; ?>')"><i class='fa fa-print'></i> Imprimir</a>
                               <a class="dropdown-item" href="#" onclick="imprimir_factura('<?php echo $id_factura; ?>');"><i class='fa fa-download'></i> PDF</a>
                              
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
      <strong>Aviso!</strong> No hay Registro de Facturas
  </div>
  <?php
}
}
?>