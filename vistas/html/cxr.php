<?php
require_once "../security.php"; 
session_start();
if ( strpos(get_url(), $_SESSION['ruta']) === false ||isset($_SESSION['ruta']) == false) {
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
$permisos_ver =getpermiso(51);
require 'includes/header_start.php';
require 'includes/header_end.php';?>
<div id="wrapper">
	<?php require 'includes/menu.php';?>
	<div class="content-page">
		<div class="content">
			<div class="container">
<?php if ($permisos_ver == 1) {
    include "../modal/ver_datos.php";
    ?>
				<div class="col-lg-12">
					<div class="portlet">
						<div class="portlet-heading bg-secondtabla">
							<h3 class="portlet-title">
								Cuentas por Cobrar
							</h3>
							<div class="clearfix"></div>
						</div>
						<div id="bg-primary" class="panel-collapse collapse show">
							<div class="portlet-body">
								<form class="form-horizontal" role="form" id="datos_cotizacion">
									<div class="form-group row">
										<div class="col-md-3">
											<div class="input-group">
												<input type="text" class="form-control" id="q" placeholder="Nombre del cliente o # factura" onkeyup='load(1);'>
												<span class="input-group-btn">
													<button type="button" class="btn btn-info waves-effect waves-light" onclick='load(1);'>
														<span class="fa fa-search" ></span> Buscar</button>
													</span>
												</div>
											</div>
											<div class="col-md-3">
											<div class="input-group">
												<select name='ruta' id='ruta' class="form-control" onchange="load(1);">
														<option value="">-- Selecciona Ruta --</option>
														<option value="">Todas</option>
														<?php
																$query_categoria = mysqli_query($conexion, "select * from tbruta order by ruta");
																while ($rw = mysqli_fetch_array($query_categoria)) {
														?>
																		<option value="<?php echo $rw['idruta']; ?>"><?php echo $rw['ruta']; ?></option>
																		<?php
																}
  																		?>
													</select>
												<span class="input-group-btn">
														<button class="btn btn-outline-info btn-rounded waves-effect waves-light" type="button" onclick='load(1);'><i class='fa fa-search'></i></button>
													</span>
												</div>
											</div>
											<div class="col-md-3">
												<span id="loader"></span>
											</div>
											<div class="col-md-3 ">
												<div class="btn-group pull-right">
													<?php if ($permisos_editar == 1) {?>
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
													<?php }?>
												</div>
											</div>
										</div>
									</form>
									<div id="resultados_ajax"></div>
									<div class="datos_ajax_delete"></div><!-- Datos ajax Final -->
									<div class='outer_div'></div><!-- Carga los datos ajax -->
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
	<script type="text/javascript" src="../../js/cxr.js"></script>
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
<script>
  function reporte(){
    var idruta=$("#ruta").val();
    VentanaCentrada('../pdf/documentos/rep_cxr.php?idruta='+idruta,'Reporte','','800','600','true');
  }
  function reporte_excel(){
	var idruta=$("#ruta").val();
	window.location.replace("../excel/rep_cxr.php?idruta="+idruta);
}
</script>
	<script>
		function obtener_datos(numero_factura) {
			$(".outer_datos").load("../modal/datos.php?numero_factura=" + numero_factura);
		}
	</script>
	<?php require 'includes/footer_end.php'
?>