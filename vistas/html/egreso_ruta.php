<?php
require_once "../security.php"; 
session_start();
if ( strpos(get_url(), $_SESSION['ruta']) === false||isset($_SESSION['ruta']) == false) {
    header("location: ../../login.php?logout");
    exit;
}
$sesion= session_id();
if (!isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] != 1) {
    header("location: ../../login.php");
    exit;
}
require_once "../db.php"; 
require_once "../php_conexion.php";
require_once "../funciones.php";
$user_id = $_SESSION['id_users'];
$permisos_ver =getpermiso(48);
$nombre_usuario = get_row('users', 'usuario_users', 'id_users', $user_id);
$query = $conexion->query("select * from comprobantes");
$tipo  = array();
while ($r = $query->fetch_object()) {$tipo[] = $r;}
$tipoDocto = $tipo[1];
$sqlUsuarioACT        = mysqli_query($conexion, "select * from users where id_users = '".$user_id."'"); //obtener el usuario activo 1aqui1
    $rw         = mysqli_fetch_array($sqlUsuarioACT);
    $id_sucursal = $rw['sucursal_users'];
$query2 = $conexion->query("select * from users");// id_perfil != '$id_sucursal'");
while($res = $query2->fetch_object()){$sucursales[] = $res;}
require 'includes/header_start.php';
require 'includes/header_end.php';?>
<div id="wrapper" class="forced enlarged">
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
									Nuevo Egreso
								</h3>
								<div class="clearfix"></div>
							</div>
							<div id="bg-primary" class="panel-collapse collapse show">
								<div class="portlet-body">
									<?php
	include "../modal/buscar_productos_ventas.php";
    ?>
									<div class="row">
										<div class="col-lg-8">
											<div class="card-box">
												<div class="widget-chart">
													<div id="resultados_ajaxf" class='col-md-12' style="margin-top:10px"></div><!-- Carga los datos ajax -->
													<form class="form-horizontal" role="form" id="barcode_form">
														<div class="form-group row">
															<label for="barcode_qty" class="col-md-1 control-label">Cant:</label>
															<div class="col-md-2">
																<input type="text" class="form-control" id="barcode_qty" value="1" autocomplete="off">
															</div>
															<label for="condiciones" class="control-label">Codigo:</label>
															<div class="col-md-5" align="left">
																<div class="input-group">
																	<input type="text" class="form-control" id="barcode" autocomplete="off"  tabindex="1" autofocus="true" >
																	<span class="input-group-btn">
																		<button type="submit" class="btn btn-default"><span class="fa fa-barcode"></span></button>
																	</span>
																</div>
															</div>
															<div class="col-md-2">
																<button type="button" accesskey="a" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target="#buscar">
																	<span class="fa fa-search"></span> Buscar
																</button>
															</div>
														</div>
													</form>
													<div id="resultados" class='col-md-12' style="margin-top:10px"></div>
												</div>
											</div>
										</div>
										<div class="col-lg-4">
											<div class="card-box">
												<div class="widget-chart">
													<form role="form" id="datos_factura">
														<div >
															<select id = "selec_usuario" class = "form-control" name = "selec_usuario" required autocomplete="off" onchange="cambio()">
																<?php foreach ($sucursales as $c): ?>
								 									<option value="<?php echo $c->id_users; ?>"><?php echo $c->nombre_users.' '.$c->apellido_users; ?></option>
																<?php endforeach;?>
															</select>
														</div>				
														<div id="btnguardar" style="margin-top:20px;" class="col-md-12" align="center">
															<button onclick="guardar()" id="guardar_factura"  name="guardar_factura" class="btn btn-danger btn-block btn-lg waves-effect waves-light" aria-haspopup="true" aria-expanded="false"><span class="fa fa-save"></span> Guardar</button>
														</div>
													</form>
												</div>
											</div>
										</div>
									</div>
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
<script type="text/javascript" src="../../js/egresos.js?ver=1.2"></script>
<?php require 'includes/footer_end.php'
?>