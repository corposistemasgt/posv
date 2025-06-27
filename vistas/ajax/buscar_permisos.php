<?php
include 'is_logged.php';
require_once "../db.php";
require_once "../php_conexion.php";

$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != null) ? $_REQUEST['action'] : '';
if ($action == 'ajax') {
    $query  = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['query'], ENT_QUOTES)));
    $tables = "tbgrupo";
    $campos = "idgrupo, grupo";
    $sWhere = " grupo LIKE '%" . $query . "%'";
    include 'pagination.php'; 
    $page      = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page  = 10;
    $adjacents = 4;
    $offset    = ($page - 1) * $per_page;
    $count_query = mysqli_query($conexion, "SELECT count(*) AS numrows FROM $tables where $sWhere ");
    if ($row = mysqli_fetch_array($count_query)) {$numrows = $row['numrows'];} else {echo mysqli_error($conexion);}
    $total_pages = ceil($numrows / $per_page);
    $reload      = './permisos.php';
    $query = mysqli_query($conexion, "SELECT $campos FROM  $tables where $sWhere LIMIT $offset,$per_page");
    if (isset($_REQUEST["id"])) {
        ?>
        <div class="<?php echo $classM; ?>">
            <button type="button" class="close" data-dismiss="alert"><?php echo $times; ?></button>
            <strong><?php echo $aviso ?> </strong>
            <?php echo $msj; ?>
        </div>
        <?php
}
    if ($numrows > 0) {
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <tr class="success">
                                    <th>ID</th>
                                    <th>Nivel Administrador </th>
                                    <th>Usuarios</th>
                                    <th>Acciones</th>
                                </tr>
                                <?php
$finales = 0;
        while ($row = mysqli_fetch_array($query)) {
            $user_group_id     = $row['idgrupo'];
            $name              = $row['grupo'];
            $user              = mysqli_query($conexion, "select * from users where cargo_users='$user_group_id'");
            $num               = mysqli_num_rows($user);
            $finales++;
            ?>
                                    <tr>
                                        <td><span class="badge badge-pill badge-purple"><?php echo $user_group_id; ?></span></td>
                                        <td><?php echo $name; ?></td>
                                        <td><span class="badge badge-pill badge-info"><?php echo $num; ?></span></td>
                                        <td>

                                            <div class="btn-group dropdown">
                                                <button type="button" class="btn btn-warning btn-sm dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"> <i class='fa fa-cog'></i> <i class="caret"></i> </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                 <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editarGrupo" onclick="editar('<?php echo $user_group_id; ?>');"><i class='fa fa-edit'></i> Editar</a>
                                                 <a class="dropdown-item" href="#" data-toggle="modal" data-target="#dataDelete" data-id="<?php echo $user_group_id; ?>"><i class='fa fa-trash'></i> Borrar</a>
                                             </div>
                                         </div>
                                     </td>
                                 </tr>
                                 <?php }?>
                                 <tr>
                                    <td colspan='5'><span class="pull-right">
                                        <?php
$inicios = $offset + 1;
        $finales += $inicios - 1;
        echo "Mostrando $inicios al $finales de $numrows registros";
        echo paginate($reload, $page, $total_pages, $adjacents);
        ?></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
}
}
?>