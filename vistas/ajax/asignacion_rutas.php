<?php
include 'is_logged.php'; 
require_once "../db.php";
require_once "../php_conexion.php";
$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != null) ? $_REQUEST['action'] : '';
if ($action == 'ajax') {
    $id        = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['id'], ENT_QUOTES)));
    $sTable   = " tbasignacionrutas,tbruta ";
    $sWhere   = " where idusuario=".$id." and tbasignacionrutas.idruta=tbruta.idruta";
    $sWhere .= " order by tbruta.idruta";
    include 'pagination.php'; 
    $page      = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page  = 10; 
    $adjacents = 4;
    $offset    = ($page - 1) * $per_page;
    $count_query = mysqli_query($conexion, "SELECT count(*) AS numrows FROM $sTable  $sWhere");
    $row         = mysqli_fetch_array($count_query);
    $numrows     = $row['numrows'];
    $total_pages = ceil($numrows / $per_page);
    $reload      = '../html/clientes.php';
    $sql   = "SELECT * FROM  $sTable $sWhere LIMIT $offset,$per_page";
    $query = mysqli_query($conexion, $sql);
    if ($numrows > 0) {
        ?>
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <tr  class="info">
                    <th>ID</th>
                    <th>Ruta</th>
                </tr>
                <?php
while ($row = mysqli_fetch_array($query)) {
            $idruta        = $row['idruta'];
            $ruta          = $row['ruta'];
            ?>
                    <input type="hidden" value="<?php echo $ruta; ?>" id="ruta<?php echo $idruta; ?>">
                    <tr>
                        <td><span class="badge badge-purple"><?php echo $idruta; ?></span></td>
                        <td><?php echo $ruta; ?></td>
                     </tr>
                 <?php
}
        ?>
             <tr>
                <td colspan="7">
                    <span class="pull-right">
                        <?php
echo paginate($reload, $page, $total_pages, $adjacents);
        ?></span>
                    </td>
                </tr>
            </table>
        </div>
        <?php
}
    else {
        ?>
        <div class="alert alert-warning alert-dismissible" role="alert" align="center">
          <strong>Aviso!</strong> No hay Registro de Rutas
      </div>
      <?php
}
}
?>