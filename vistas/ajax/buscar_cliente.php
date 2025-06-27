<?php
include 'is_logged.php';
require_once "../db.php";
require_once "../php_conexion.php";
include "../funciones.php";
$permisos_editar =getpermiso(38);
$permisos_eliminar =getpermiso(39);
$user_id = $_SESSION['id_users'];
$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != null) ? $_REQUEST['action'] : '';
if ($action == 'ajax') {
    $q        = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['q'], ENT_QUOTES)));
    $r        = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['r'], ENT_QUOTES)));
    $aColumns = array('nombre_cliente', 'fiscal_cliente');
    $sTable   = " clientes ";
    $sWhere   = "";
    if ($_GET['q'] != "") {
        $sWhere = "Where  (";
        for ($i = 0; $i < count($aColumns); $i++) {
            $sWhere .= $aColumns[$i] . " LIKE '%" . $q . "%' OR  ";
        }
        $sWhere = substr_replace($sWhere, "", -4);
        $sWhere .= ')';
    } 
   

    if ($_GET['r'] != "") 
    {
        if ($sWhere == "") {
            $sWhere .= " where ";
        }
        else
        {
            $sWhere.=" and ";
        }
        $sWhere .= " idruta=".$r;
    }
    if ($_GET['rutero'] != "") 
    {
        if ($sWhere == "") {
            $sWhere .= " where ";
        }
        else
        {
            $sWhere.=" and ";
        }
        $sWhere.="(";

        $sql2   = "SELECT idruta FROM tbasignacionrutas where idusuario =  ".$_GET['rutero'];
        $query2 = mysqli_query($conexion, $sql2);
        $rutas=0;
        while ($row = mysqli_fetch_array($query2)) {
            $sWhere.= " idruta=".$row['idruta']." or";
        }
        $sWhere=trim($sWhere,"or");
        $sWhere.=")";

    }
    $sWhere .= " order by nombre_cliente";
    include 'pagination.php'; 

    $sql2   = "SELECT rutas FROM tbconfiguracion";
    $query2 = mysqli_query($conexion, $sql2);
    $rutas=0;
    while ($row = mysqli_fetch_array($query2)) {
        $rutas          = $row['rutas'];
    }
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
                    <th>Nombre</th>
                    <th>NIT</th>
                    <?php if($rutas==1){?>
                        <th>Ruta</th>
                    <?php }?>
                    <th>Estado</th>
                    <th>Agregado</th>
                    <th class='text-right'>Acciones</th>
                </tr>
                <?php
while ($row = mysqli_fetch_array($query)) {
            $id_cliente        = $row['id_cliente'];
            $nombre_cliente    = $row['nombre_cliente'];
            $fiscal_cliente    = $row['fiscal_cliente'];
            $telefono_cliente  = $row['telefono_cliente'];
            $email_cliente     = $row['email_cliente'];
            $direccion_cliente = $row['direccion_cliente'];
            $status_cliente    = $row['status_cliente'];
            $idruta            = $row['idruta'];
            $credito           = $row['credito'];
            $limite_credito    = $row['limite_credito'];
            $date_added        = date('d/m/Y', strtotime($row['date_added']));
            if ($status_cliente == 1) {
                $estado = "<span class='badge badge-success'>Activo</span>";
            } else {
                $estado = "<span class='badge badge-danger'>Inactivo</span>";
            }
            ?>
                    <input type="hidden" value="<?php echo $nombre_cliente; ?>" id="nombre_cliente<?php echo $id_cliente; ?>">
                    <input type="hidden" value="<?php echo $fiscal_cliente; ?>" id="fiscal_cliente<?php echo $id_cliente; ?>">
                    <input type="hidden" value="<?php echo $telefono_cliente; ?>" id="telefono_cliente<?php echo $id_cliente; ?>">
                    <input type="hidden" value="<?php echo $email_cliente; ?>" id="email_cliente<?php echo $id_cliente; ?>">
                    <input type="hidden" value="<?php echo $direccion_cliente; ?>" id="direccion_cliente<?php echo $id_cliente; ?>">
                    <input type="hidden" value="<?php echo $status_cliente; ?>" id="status_cliente<?php echo $id_cliente; ?>">
                    <input type="hidden" value="<?php echo $idruta; ?>" id="idruta<?php echo $id_cliente; ?>">
                    <input type="hidden" value="<?php echo $credito; ?>" id="credito<?php echo $id_cliente; ?>">
                    <input type="hidden" value="<?php echo $limite_credito; ?>" id="limite_credito<?php echo $id_cliente; ?>">
                    <tr>
                        <td><span class="badge badge-purple"><?php echo $id_cliente; ?></span></td>
                        <td><?php echo $nombre_cliente; ?></td>
                        <td ><?php echo $fiscal_cliente; ?></td>
                        <?php 
                        if($rutas==1){
                            $sql2   = "SELECT ruta FROM tbruta where idruta =  ".$idruta;
                            $query2 = mysqli_query($conexion, $sql2);
                            $rut="";
                            while ($row = mysqli_fetch_array($query2)) {
                                $rut          = $row['ruta'];
                            }
                        ?>
                        <th><?php echo $rut;?></th>
                        <?php }?>
                        <td><?php echo $estado; ?></td>
                        <td><?php echo $date_added; ?></td>
                        <td>
                            <div class="btn-group dropdown pull-right">
                                <button type="button" class="btn btn-warning btn-rounded btn-sm waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"> <i class='fa fa-cog'></i> <i class="caret"></i> </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="detalle_cliente.php?idcliente=<?php echo $id_cliente; ?>"><i class='fa fa-th-list'></i> Detalles</a>  
                                   <?php if ($permisos_editar == 1) 
                                    {
                                    ?>
                                        
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editarCliente" onclick="obtener_datos('<?php echo $id_cliente; ?>');"><i class='fa fa-edit'></i> Editar</a>
                                       
                                    <?php 
                                    }
                                    if ($permisos_eliminar == 1) 
                                    {
                                    ?>
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#dataDelete" data-id="<?php echo $id_cliente; ?>"><i class='fa fa-trash'></i> Borrar</a>
                                    <?php 
                                    }
                                    ?>
                             </div>
                         </div>
                        </td>
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
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <strong>Aviso!</strong> No hay Registro de Clientes
      </div>
      <?php
}
}
?>