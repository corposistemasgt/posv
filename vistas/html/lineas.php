<?php
require_once "../security.php"; 
session_start();
if ( strpos(get_url(), $_SESSION['ruta']) === false||isset($_SESSION['ruta']) == false) {
    header("location: ../../login.php?logout");
    exit;
}
if (!isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] != 1) {
    header("location: ../../login.php");
    exit;
}
require_once "../db.php"; 
require_once "../php_conexion.php"; 
include "../funciones.php";
$user_id = $_SESSION['id_users'];
$permisos_ver =getpermiso(8);
$permisos_editar = getpermiso(9);
$permisos_eliminar = getpermiso(10);
require 'includes/header_start.php';
require 'includes/header_end.php';?>
<div id="wrapper">
	<?php require 'includes/menu.php';?>
	<div class="content-page">
		<div class="content">
			<div class="container">
<?php if ($permisos_ver == 1) {
    ?>
				<div class="col-lg-12">
					<div class="portlet">
						<div class="portlet-heading bg-secondtabla">
							<h3 class="portlet-title">
								Lineas
							</h3>
							<div class="clearfix"></div>
						</div>
						<div id="bg-primary" class="panel-collapse collapse show">
							<div class="portlet-body">
<?php
if ($permisos_editar == 1) {
        include '../modal/registro_linea.php';
        include "../modal/editar_linea.php";
    }
    if ($permisos_eliminar == 1) {
        include "../modal/eliminar_linea.php";
    }
    ?>
								<form class="form-horizontal" role="form" id="datos_cotizacion">
									<div class="form-group row">
										<div class="col-md-6">
											<div class="input-group">
												<input type="text" class="form-control" id="q" placeholder="Buscar por Nombre" onkeyup='load(1);'>
												<span class="input-group-btn">
													<button type="button" class="btn btn-info waves-effect waves-light" onclick='load(1);'>
														<span class="fa fa-search" ></span> Buscar</button>
													</span>
												</div>
											</div>
											<div class="col-md-3">
												<span id="loader"></span>
											</div>
											<div class="col-md-3">
												<div class="btn-group pull-right">
												<button type="button" class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#nuevaLinea"><i class="fa fa-plus"></i> Nueva</button>
												</div>
											</div>
										</div>
									</form>
									<div class="datos_ajax_delete"></div>
									<div class='outer_div'></div>
								</div>
							</div>
						</div>
					</div>
					<?php
} else {
    ?>
		<section class="content">
			<div class="alert alert-danger" align="center">
				<h3>Acceso denegado! </h3>
				<p>No cuentas con los permisos necesario para acceder a este módulo.</p>
			</div>
		</section>
		<?php
}
?>
				</div>
			</div>
			<?php require 'includes/pie.php';?>
		</div>
	</div>
	<?php require 'includes/footer_start.php'
?>
	<script type="text/javascript" src="../../js/lineas.js"></script>
	<script>
       $(document).ready( function () {
        $(".UpperCase").on("keypress", function () {
         $input=$(this);
         setTimeout(function () {
          $input.val($input.val().toUpperCase());
         },50);
        })
       })
      </script>
	<?php require 'includes/footer_end.php'
?>