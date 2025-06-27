<?php
include "../modal/cambiar_password.php";
include 'is_logged.php'; 
require_once "../db.php";
require_once "../php_conexion.php";
$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != null) ? $_REQUEST['action'] : '';
if ($action == 'ajax') {
    $q        = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['q'], ENT_QUOTES)));
    $aColumns = array('nombre_users', 'apellido_users'); 
    $sTable   = "users";
    $sWhere   = "";
    if ($_GET['q'] != "") {
        $sWhere = "WHERE (";
        for ($i = 0; $i < count($aColumns); $i++) {
            $sWhere .= $aColumns[$i] . " LIKE '%" . $q . "%' OR ";
        }
        $sWhere = substr_replace($sWhere, "", -3);
        $sWhere .= ')';
    }
    $sWhere .= " order by id_users desc";
    include 'pagination.php'; 
    $page      = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page  = 10;
    $adjacents = 4;
    $offset    = ($page - 1) * $per_page;
    $count_query = mysqli_query($conexion, "SELECT count(*) AS numrows FROM $sTable  $sWhere");
    $row         = mysqli_fetch_array($count_query);
    $numrows     = $row['numrows'];
    $total_pages = ceil($numrows / $per_page);
    $reload      = '../html/usuarios.php';
    $sql2   = "SELECT rutas FROM tbconfiguracion";
    $query2 = mysqli_query($conexion, $sql2);
    $rutas=0;
    while ($row = mysqli_fetch_array($query2)) {
        $rutas          = $row['rutas'];
    }
    $sql   = "SELECT * FROM  $sTable $sWhere LIMIT $offset,$per_page";
    $query = mysqli_query($conexion, $sql);
    if ($numrows > 0) {
        ?>
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <tr  class="info">
                    <th>ID</th>
                    <th>Nombres</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Agregado</th>
                    <th><span class="pull-right">Acciones</span></th>
                </tr>
                <?php
while ($row = mysqli_fetch_array($query)) {
            $user_id     = $row['id_users'];
            $fullname    = $row['nombre_users'] . " " . $row["apellido_users"];
            $user_name   = $row['usuario_users'];
            $user_email  = $row['email_users'];
            $id_cargo    = $row['cargo_users'];
            $id_sucursal = $row['sucursal_users'];
            $date_added  = date('d/m/Y', strtotime($row['date_added']));
            $tipo_precio = $row['tipo_precio'];
            ?>
                    <input type="hidden" value="<?php echo $row['nombre_users']; ?>" id="nombres<?php echo $user_id; ?>">
                    <input type="hidden" value="<?php echo $row['apellido_users']; ?>" id="apellidos<?php echo $user_id; ?>">
                    <input type="hidden" value="<?php echo $user_name ?>" id="usuario<?php echo $user_id; ?>">
                    <input type="hidden" value="<?php echo $user_email; ?>" id="email<?php echo $user_id; ?>">
                    <input type="hidden" value="<?php echo $id_cargo; ?>" id="cargo<?php echo $user_id; ?>">
                    <input type="hidden" value="<?php echo $id_cargo; ?>" id="cargo<?php echo $user_id; ?>">
                    <input type="hidden" value="<?php echo $id_sucursal; ?>" id="sucursal<?php echo $user_id; ?>">
                    <input type="hidden" value="<?php echo $tipo_precio; ?>" id="tipoprecio<?php echo $user_id; ?>">
                    <tr>
                        <td><span class="badge badge-pill badge-purple"><?php echo $user_id; ?></span></td>
                        <td><?php echo $fullname; ?></td>
                        <td ><?php echo $user_name; ?></td>
                        <td ><?php echo $user_email; ?></td>
                        <td><?php echo $date_added; ?></td>
                        <td >
                            <div class="btn-group dropdown">
                                                <button type="button" class="btn btn-warning btn-sm dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"> <i class='fa fa-cog'></i> <i class="caret"></i> </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                 <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editarUsers" onclick="obtener_datos('<?php echo $user_id; ?>');"><i class='fa fa-edit'></i> Editar</a>
                                                 <a class="dropdown-item" href="#" data-toggle="modal" data-target="#password_edit" onclick="editar_pw('<?php echo $user_id; ?>');"><i class='fa fa-unlock'></i> Cambiar Contraseña</a>
                                                 <a class="dropdown-item" href="#" data-toggle="modal" data-target="#dataDelete" data-id="<?php echo $user_id; ?>"><i class='fa fa-trash'></i> Borrar</a>
                                             </div>
                                         </div>
                        </td>
                    </tr>
                    <?php
}
        ?>
                <tr>
                    <td colspan=9><span class="pull-right">
                        <?php
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
              <strong>Aviso!</strong> No hay Registro de Usuarios
          </div>
          <?php
}
}
?>