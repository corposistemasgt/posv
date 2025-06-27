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
require_once "../funciones.php"; 
$user_id = $_SESSION['id_users'];
$sucursal=$_SESSION['idsucursal'];
$permisos_ver =getpermiso(31);
require 'includes/header_start.php';
require 'includes/header_end.php';?>
<div id="wrapper">
	<?php require 'includes/menu.php';?>
	<div class="content-page">
		<div class="content">
			<div class="container">

				<div class="col-lg-12">
					<div class="portlet">
						<div class="portlet-heading bg-secondtabla">
							<h3 class="portlet-title">
								Control de Gastos
							</h3>
							<div class="portlet-widgets">
								<a href="javascript:;" data-toggle="reload"><i class="ion-refresh"></i></a>
								<span class="divider"></span>
								
							</div>
							<div class="clearfix"></div>
						</div>
						<div id="bg-primary" class="panel-collapse collapse show">
							<div class="portlet-body">

								<form class="form-horizontal" role="form" id="datos_cotizacion">
									<div class="form-group row">
										<div class="col-md-6">
											<div class="input-group">
												<input type="text" class="form-control" id="q" placeholder="Buscar por Referencia" onkeyup='load(1);'>
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
													<button type="button" class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#nuevoGastoo"><i class="fa fa-plus"></i> Nuevo</button>
												</div>

											</div>

										</div>
									</form>
									<div class="datos_ajax_delete"></div><!-- Datos ajax Final -->
									<div class='outer_div'></div><!-- Carga los datos ajax -->

								</div>
							</div>
						</div>
					</div>


				</div>	
			</div>
			<?php require 'includes/pie.php';?>

		</div>
	</div>
	<div id="nuevoGastoo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title"><i class='fa fa-edit'></i> Nuevo Gastos</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" method="post" id="guardar_gasto" name="guardar_gasto">
						<div id="resultados_ajax"></div>

						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label for="referencia" class="control-label">Referencia:</label>
									<input type="text" class="form-control" id="referencia" name="referencia"  autocomplete="off">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="monto" class="control-label">Monto Factura</label>	
									<input type="hidden" class="form-control" id="sucursal" name="sucursal" value='<?php echo $sucursal;?>'>
								
									<input type="text" class="form-control" id="monto" name="monto" autocomplete="off" pattern="^[0-9]{1,5}(\.[0-9]{0,2})?$">
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="descripcion" class="control-label">Descripción</label>
									<textarea class="form-control"  id="descripcion" name="descripcion" maxlength="255"  autocomplete="off" required></textarea>
								</div>
							</div>
						</div>

					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Cerrar</button>
						<button type="submit" class="btn btn-primary waves-effect waves-light"  id="guardar_datos">Guardar</button>
					</div>
				</form>
			</div>
		</div>
	</div>



	<?php require 'includes/footer_start.php'
?>
	<script type="text/javascript" src="../../js/gastos.js"></script>
	<?php require 'includes/footer_end.php'
?>

