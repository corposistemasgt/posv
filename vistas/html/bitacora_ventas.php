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
$permisos_ver =getpermiso(5);
$user_sucursal = $_SESSION['idsucursal'];
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
								Bítocora de Ventas
							</h3>
							
							<div class="clearfix"></div>
						</div>
						<div id="bg-primary" class="panel-collapse collapse show">
							<div class="portlet-body">
							<?php
include "../modal/eliminar_factura.php";
include "../modal/modal_consultar_nit.php";
    ?>

								<form class="form-horizontal" role="form" id="datos_cotizacion">
									<div class="form-group row">
										<div class="col-md-4">
											<div class="input-group">
												<input type="text" class="form-control" id="q" placeholder="Nombre del cliente o # factura" onkeyup='load(1);'>
												<span class="input-group-btn">
													<button type="button" class="btn btn-info waves-effect waves-light" onclick='load(1);'>
														<span class="fa fa-search" ></span> Buscar
													</button>
												</span>
											</div>
										</div>
										<div class="col-md-4">
										<div class="input-group">
											<div class="input-group-addon">
														<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control daterange pull-right" onchange='load(1);' value="<?php echo date('d/m/Y') . ' - ' . date('d/m/Y'); ?>" id="range" name="range" readonly>
											<span class="input-group-btn">
														<button class="btn btn-info waves-effect waves-light" type="button" onclick='load(1);'><i class='fa fa-search'></i></button>
											</span>
										</div>
										
									</div>
									<button type="button"  onclick="reporte(<?php echo $user_sucursal?>);" class="btn btn-default waves-effect waves-light"><i class='fa fa-print'></i> Imprimir</button>
													
													<div class="col-md-3">
															<span id="loader"></span>
													</div>
									</div>
									
								</form>
									<div class="datos_ajax_delete"></div>
									<div class='outer_div'></div>
									<div id="modal-container"></div>
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
	<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
	<script>
	function reporte(sucursal){
		console.log(sucursal);
		var fecha = $("#range").val();
		VentanaCentrada('../pdf/documentos/rep_documentos.php?range='+fecha+"&sucursal="+sucursal,'Reporte','','800','600','true');
	}
</script>
	<script>
	$(function() {
$('.daterange').daterangepicker({
	buttonClasses: ['btn', 'btn-sm'],
	applyClass: 'btn-success',
	cancelClass: 'btn-default',
	locale: {
		format: "DD/MM/YYYY",
		separator: " - ",
		applyLabel: "Aplicar",
		cancelLabel: "Cancelar",
		fromLabel: "Desde",
		toLabel: "Hasta",
		customRangeLabel: "Custom",
		daysOfWeek: [
		"Do",
		"Lu",
		"Ma",
		"Mi",
		"Ju",
		"Vi",
		"Sa"
		],
		monthNames: [
		"Enero",
		"Febrero",
		"Marzo",
		"Abril",
		"Mayo",
		"Junio",
		"Julio",
		"Agosto",
		"Septiembre",
		"Octubre",
		"Noviembre",
		"Diciembre"
		],
		firstDay: 1
	},
	opens: "right"

});
});
	</script>
	<script type="text/javascript" src="../../js/bitacora_ventas.js?ver=1.0"></script>

	<?php require 'includes/footer_end.php'
?>