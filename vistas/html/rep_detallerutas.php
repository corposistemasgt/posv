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
$iddetalle=$_GET["id"];
$permisos_ver =getpermiso(43);
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
									Detalle del Egreso
								</h3>
								<input type="text" value="<?php echo $iddetalle;?> " id="ide" name="ide" style="visibility:hidden"></div>
								<div class="clearfix"></div>
							</div>
							<div id="bg-primary" class="panel-collapse collapse show">
								<div class="portlet-body">
									<form class="form-horizontal" role="form" id="datos_cotizacion">
										<div class="form-group row">
											<div class="col-12" >
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
<script>
	$(function() {
		load(1);
});
	function load(page){
		var idfactura=$("#ide").val();
		console.log(idfactura);
		var parametros = {"action":"ajax",'ide':idfactura};
		$("#loader").fadeIn('slow');
		$.ajax({
			url:'../ajax/rep_egresodetalle.php',
			data: parametros,
			beforeSend: function(objeto){
				$("#loader").html("<img src='../../img/ajax-loader.gif'>");
			},
			success:function(data){
				console.log(data)
				$(".outer_div").html(data).fadeIn('slow');
				$("#loader").html("");
			}
		})
	}
</script>
<script>
	function reporte(){
		var id=$("#ide").val();
		VentanaCentrada('../pdf/documentos/rep_egresodetalle.php?id='+id,'Reporte','','800','600','true');
	}
	function reporte_excel(){
		var id=$("#ide").val();
		window.location.replace("../excel/rep_egresodetalle.php?id="+id);
}
</script>
<?php require 'includes/footer_end.php'
?>