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
									Reporte de Vencimientos
								</h3>
								
								<div class="clearfix"></div>
							</div>
							<div id="bg-primary" class="panel-collapse collapse show">
								<div class="portlet-body">
									<form class="form-horizontal" role="form" id="datos_cotizacion">
										<div class="form-group row">  
										<div class="col-xs-3">
												<div class="input-group">
													<select name='sucursal1' id='sucursal1' class="form-control" onchange="load(1);">
														<option value="-1">-- Selecciona Sucursal --</option>
														<option value="-1">Todos</option>
														<?php
														$query_categoria = mysqli_query($conexion, "select * from perfil order by giro_empresa ");
														while ($rw = mysqli_fetch_array($query_categoria)) {
															?>
															<option value="<?php echo $rw['id_perfil']; ?>"><?php echo $rw['codigoEstablecimiento'].'-'. $rw['giro_empresa']; ?></option>
															<?php
															}
																?>
													</select>
													<span class="input-group-btn">
														<button class="btn btn-info waves-effect waves-light" type="button" onclick='load(1);'><i class='fa fa-search'></i></button>
													</span>
												</div>
											</div>
											<div class="col-xs-3">
												<div class="input-group">
													<select name='categoria' id='categoria' class="form-control" onchange="load(1);">
														<option value="">-- Selecciona Linea --</option>
														<option value="">Todos</option>
														<?php 
														 $query_categoria = mysqli_query($conexion, "select * from lineas order by nombre_linea");
    														while ($rw = mysqli_fetch_array($query_categoria)) {  ?>
															<option value="<?php echo $rw['id_linea']; ?>"><?php echo $rw['nombre_linea']; ?></option>
															<?php
}
    ?>
													</select>
													<span class="input-group-btn">
														<button class="btn btn-info waves-effect waves-light" type="button" onclick='load(1);'><i class='fa fa-search'></i></button>
													</span>
												</div>
											</div>
											<div class="col-xs-3">
												<div class="input-group">
													<select name='casa' id='casa' class="form-control" onchange="load(1);">
														<option value="">-- Selecciona Casa --</option>
														<option value="">Todos</option>
														<?php 
														 $query_categoria = mysqli_query($conexion, "select idcasa,casa from tbcasa order by casa");
    														while ($rw = mysqli_fetch_array($query_categoria)) {  ?>
															<option value="<?php echo $rw['idcasa']; ?>"><?php echo $rw['casa']; ?></option>
															<?php
}
    ?>
													</select>
													<span class="input-group-btn">
														<button class="btn btn-info waves-effect waves-light" type="button" onclick='load(1);'><i class='fa fa-search'></i></button>
													</span>
												</div>
											</div>
											<div class="col-xs-3">
												<div class="input-group">
													<input type="text" id="meses" name="meses" class="form-control ui-autocomplete-input" keydown="actu();"; placeholder="Meses de Anticipacion" required="" tabindex="2" autocomplete="off">
												</div>
											</div>										
											<div class="col-xs-3">
												<div id="loader" class="text-center"></div>
											</div>
											<div class="col-xs-3">
												<div id="loader" class="text-center"></div>
											</div>
											<div class="btn-group dropup">
														<button aria-expanded="false" class="btn btn-outline-default btn-rounded waves-effect waves-light" data-toggle="dropdown" type="button">
															<i class='fa fa-file-text'></i> Reporte
															<span class="caret">
															</span>
														</button>
														<div class="dropdown-menu">
															
															<a class="dropdown-item" href="#" onclick="reporte_excel();">
																<i class='fa fa-file-excel-o'></i> Excel
															</a>
															<a class="dropdown-item" href="#" onclick="reporte();">
																<i class='fa fa-file-excel-o'></i> PDF
															</a>
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
		var categoria=$("#categoria").val();
		var sucursal = $("#sucursal1").val();
		var meses = $("#meses").val();
		var casa = $("#casa").val();
		if(meses=='')
		{
			meses=0;
		}
		var parametros = {"action":"ajax","page":page,'categoria':categoria,'sucursal':sucursal,'meses':meses,'casa':casa};
		
		$("#loader").fadeIn('slow');
		$.ajax({ 
			url:'../ajax/rep_vencimientos.php',
			data: parametros,
			beforeSend: function(objeto){
				$("#loader").html("<img src='../../img/ajax-loader.gif'>Cargando...");
			},
			success:function(data){
				$(".outer_div").html(data).fadeIn('slow');
				$("#loader").html("");
			}
		})	
	}
</script>
<script>
	function reporte(){
		var categoria=$("#categoria").val();
		var sucursal = $("#sucursal1").val();
		var meses = $("#meses").val();
		VentanaCentrada('../pdf/documentos/rep_vencimientos.php?sucursal='+sucursal+"&categoria="+categoria+"&meses="+meses,'Reporte','','800','600','true');
	}
</script>
<script>
	$("#meses").keydown( function(e) {
    if(e.which == 13) {
       
        e.preventDefault();
        load(1);
    }
    });
</script>
<script>
function reporte_excel(){//NO ENCUENTRA ESTO
	var categoria=$("#categoria").val();
	var sucursal = $("#sucursal1").val();
	var meses = $("#meses").val();
	window.location.replace("../excel/rep_vencimientos.php?meses="+meses+"&sucursalid="+sucursal+"&id_categoria="+categoria);
	}
</script>
<?php require 'includes/footer_end.php'
?>