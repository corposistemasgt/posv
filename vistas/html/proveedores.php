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
$permisos_ver =getpermiso(40);
$user_id = $_SESSION['id_users'];
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
								proveedores
							</h3>
							<div class="clearfix"></div>
						</div>
						<div id="bg-primary" class="panel-collapse collapse show">
							<div class="portlet-body">
<?php
        include '../modal/registro_proveedor.php';
        include "../modal/editar_proveedor.php";
        include "../modal/eliminar_proveedor.php";
    
    ?>
								<form class="form-horizontal" role="form" id="datos_cotizacion">
									<div class="form-group row">
										<div class="col-md-6">
											<div class="input-group">
												<input type="text" class="form-control" id="q" placeholder="Buscar por Nombre o  Nit/Código" onkeyup='load(1);' autocomplete="off">
												<span class="input-group-btn">
													<button type="button" class="btn btn-outline-info btn-rounded waves-effect waves-light" onclick='load(1);'>
														<span class="fa fa-search" ></span></button>
													</span>
												</div>
											</div>
											<div class="col-md-2">
												<span id="loader"></span>
											</div>
											<div class="col-md-2">
												<div class="btn-group pull-right">
												<button type="button" class="btn btn-success btn-rounded waves-effect waves-light" data-toggle="modal" data-target="#nuevoProveedor"><i class="fa fa-user-plus"></i> Agregar</button>
												</div>
											</div>
											<div class="col-md-2">
													<div class="btn-group pull-right">
														<div class="btn-group dropup">
															<button aria-expanded="false" class="btn btn-outline-default btn-rounded waves-effect waves-light" data-toggle="dropdown" type="button">
																<i class='fa fa-file-text'></i> Reporte
																<span class="caret">
																</span>
															</button>
															<div class="dropdown-menu">
																<a class="dropdown-item" href="#" onclick="reporte();">
																	<i class='fa fa-file-pdf-o'></i> PDF
																</a>
																<a class="dropdown-item" href="#" onclick="reporte_excel();">
																	<i class='fa fa-file-excel-o'></i> Excel
																</a>
															</div>
														</div>
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
	<script type="text/javascript" src="../../js/VentanaCentrada.js"></script>
	<script type="text/javascript" src="../../js/proveedores.js"></script>
	<script>
	function obtener_historial(id_paciente) {
		    $(".outer_div4").load("../modal/historial.php?id_paciente=" + id_paciente);
		}
		function obtener_pagos(id_paciente) {
		    $(".outer_div5").load("../modal/pagos.php?id_paciente=" + id_paciente);
		}
</script>
<script>
       $(document).ready( function () {
        $(".UpperCase").on("keypress", function () {
         $input=$(this);
         setTimeout(function () {
          $input.val($input.val().toUpperCase());
         },50);
        })
       })
       function reporte_excel(){
			var q=$("#q").val();
			window.location.replace("../excel/rep_proveedores.php?q="+q);
}
      </script>
       <script type="text/javascript">
      	function reporte(){
		var q=$("#q").val();
		VentanaCentrada('../pdf/documentos/rep_proveedores.php?q='+q,'Reporte','','800','600','true');
	}
      </script>
	<?php require 'includes/footer_end.php'
?>