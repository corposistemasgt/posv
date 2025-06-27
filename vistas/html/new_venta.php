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
$permisos_ver =getpermiso(2);
$nombre_usuario = get_row('users', 'usuario_users', 'id_users', $user_id);
$query = $conexion->query("select * from comprobantes");
$tipo  = array();
while ($r = $query->fetch_object()) {$tipo[] = $r;}
$tipoDocto = $tipo[1];
$sqlUsuarioACT        = mysqli_query($conexion, "select * from users where id_users = '".$user_id."'");
    $rw         = mysqli_fetch_array($sqlUsuarioACT);
    $id_sucursal = $rw['sucursal_users'];
$query2 = $conexion->query("select * from perfil where id_perfil != '$id_sucursal'");
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
									Nueva Venta
								</h3>
								<div class="portlet-widgets">
									<div class="btn-group dropdown ">
										<button type="button" class="btn btn-primary btn-sm dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"> <i class='fa fa-dollar'></i> Caja<i class="caret"></i> </button>
										<div class="dropdown-menu dropdown-menu-right">
											<a class="dropdown-item text-muted" href="#" data-toggle="modal" data-target="#caja" ><i class='fa fa-search'></i>  Ver Caja</a>					
										</div>
									</div>
								</div>
								<div class="portlet-widgets">
										<div class="form-group">
											<select class="form-control input-sm traslados" id="traslado" name="traslado" onchange="trasladomercaderia(this);">
												<option value="1">Venta</option>
												<option value="2">Traslado Mercaderia</option>
											</select>
											</div>
								</div>
								<div class="clearfix"></div>
							</div>
							<div id="bg-primary" class="panel-collapse collapse show">
								<div class="portlet-body">
									<?php
	include "../modal/buscar_productos_ventas.php";
    include "../modal/registro_cliente.php";
    include "../modal/registro_producto.php";
    include "../modal/caja.php";
    include "../modal/anular_factura.php";
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
													
														<div id="divfiscal" name="divfiscal" class="row">
															<div  class="col-md-8" >
																<div class="form-group">
																	<label  for="fiscal"> Nit/Código</label>
																	<input type="text" class="form-control" autocomplete="off" id="rnc" name="rnc">
																	<input type="text" id="sesion" style="display: none" name="sesion" value='<?php echo $sesion;?>'>
																</div>
															</div>
															<div  class="col-md-2" >
																<div class="form-group" >
																<label  for="fiscal"> Buscar</label>
																<button id="btnnit" name="btnnit" onclick="buscar_nit('<?php echo $sesion;?>')" type="button" class="btn waves-effect waves-light btn-success" ><li id="btnnit" class="fa fa-search"></li></button>						
																</div>
															</div>
															<div  class="col-md-2">
																<div class="form-group">
																<label style="padding-left:10px" for="fiscal">  CF</label>
																<button  id="btncf" name="btncf" onclick="cf()" type="button" class="btn btn-primary waves-effect waves-light btn-success" ><strong>CF</strong></button>						
																</div>
															</div>
																	<select id = "id_comp" class = "form-control" name = "id_comp" required autocomplete="off" onchange="getval(this);">
																			<?php foreach ($tipo as $c): ?>
																				<option value="<?php echo $c->id_comp; ?>"><?php echo $c->nombre_comp; ?></option>
																			<?php endforeach;?>
															        </select>
														    </div>
														<div class="form-group row">
															<label class="col-2 col-form-label"></label>
															<div id="buscarcliente" class="col-12">
																<div class="input-group">
																	<input type="text" id="nombre_cliente" name="nombre_cliente" class="form-control" placeholder="Nombre  Cliente" required  tabindex="2">																	
																	<input id="id_cliente" name="id_cliente" type='hidden'>
																</div>
															</div>
														</div>
														<div class="form-group row">
															<label class="col-2 col-form-label"></label>
															<div  class="col-12">
																<div class="input-group">
																	<input type="text" id="direccion_cliente" name="direccion_cliente" class="form-control" placeholder="Direccion"  tabindex="2">						
																</div>
															</div>
														</div>
														
																	<input hidden id="correo_cliente" name="correo_cliente" class="form-control">						
											
																	<input hidden id="telefono_cliente" name="telefono_cliente" class="form-control">						
																	<input hidden type="text" value="" id="rutaz" name="rutaz" class="form-control">
														<div id="comprobante" class="row" hidden>
															<div class="col-md-0">
																<div id="numComprobando" class="form-group">
																	<label for="fiscal">No. Comprobante</label>
																	<div id="outer_comprobante"></div><!-- Carga los datos ajax -->
																</div>
															</div>
															<div class="col-md-12">
																<div class="form-group">
																	<div id="resultados4"></div><!-- Carga los datos ajax -->
																</div>
																<div id="resultados5"></div><!-- Carga los datos ajax -->
															</div>
														</div>


														<div class="row">
															<div class="col-md-12">
															<div id="resultados6"></div><!-- Carga los datos ajax -->
															</div>
														</div>
														<div id="idpagos" class="row">
															
															<div class="col-md-5">
																<div class="form-group">
																	<label for="condiciones">Pago</label>
																	<select class="form-control input-sm condiciones" id="condiciones" name="condiciones" onchange="showDiv(this);">
																		<option value="1">Efectivo</option>
																		<option value="2">Cheque</option>
																		<option value="3">Tarjeta</option>
																		<?php  if(getpermiso(4)==1) 
																		{  ?>
<option value="4">Crédito</option>

																		<?php }?>
																		
																		
																		<option value="6">Transeferencia</option>
																	</select>
																</div>
															</div>
															<div class="col-md1" style="padding-left:10px" >
																<div class="form-group">
																	<div  id="resultados3"></div><!-- Carga los datos ajax del incremento de la fatura -->
																</div>
															</div>
															
														</div>
														<div class="datos_ajax_delete"></div>				
														<div id="btnguardar" class="col-md-12" align="center">
															<button type="submit" id="guardar_factura"  name="guardar_factura" class="btn btn-danger btn-block btn-lg waves-effect waves-light" aria-haspopup="true" aria-expanded="false"><span class="fa fa-save"></span> Guardar</button>
														</div>
													</form>


													<form role="form" id="datos_traslados">
																			
														<div class="row">
															<div class="col-md-12">
																<div class="form-group">	
																	<select id = "selec_sucursal" class = "form-control" name = "selec_sucursal" required autocomplete="off">
																			<?php foreach ($sucursales as $c): ?>
								 												<option value="<?php echo $c->id_perfil; ?>"><?php echo $c->codigoEstablecimiento.'-'.$c->giro_empresa; ?></option>
																			<?php endforeach;?>
																	</select>
																</div>
															</div>
														</div>
														
														<div class="row">
															<div class="col-md-12">
																<div class="form-group">	
																	<button  type="submit" id="btnguardartraslado" class="btn btn-danger btn-block btn-lg waves-effect waves-light" aria-haspopup="true" aria-expanded="false"><span class="fa fa-save"></span> Trasladar</button>
																</div>
															</div>
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
<script type="text/javascript" src="../../js/VentanaCentrada.js"></script>
<script type="text/javascript" src="../../js/venta.js?ver=1.1"></script>
<!-- Codigos Para el Auto complete de Clientes -->
<script>

	$(document).ready(function(){
            $('#id_comp').hide();
			$.Notification.notify('success', 'bottom center', 'NOTIFICACIÓN', 'CAMBIO DE COMPROBANTE')
    		$("#outer_comprobante").load("../ajax/carga_correlativos.php?id_comp="+$('#id_comp').val());

        });

	$(function() {
		
			$("#nombre_cliente").autocomplete({
			source: "../ajax/autocomplete/clientes.php",
			minLength: 2,
			select: function(event, ui) {
				if($("#rnc").val() != "CF" || $("#rnc").val() != "cf")
				{
					event.preventDefault();
					$('#id_cliente').val(ui.item.id_cliente);
					$('#nombre_cliente').val(ui.item.nombre_cliente);
					$('#rnc').val(ui.item.fiscal_cliente);
					$.Notification.notify('custom','bottom right','EXITO!', 'CLIENTE AGREGADO CORRECTAMENTE');
				}
				
			}
		});
		
		
	});

	

	$("#nombre_cliente" ).on( "keydown", function( event ) {
		if (event.keyCode== $.ui.keyCode.LEFT || event.keyCode== $.ui.keyCode.RIGHT || event.keyCode== $.ui.keyCode.UP || event.keyCode== $.ui.keyCode.DOWN || event.keyCode== $.ui.keyCode.DELETE || event.keyCode== $.ui.keyCode.BACKSPACE )
		{
			$("#id_cliente" ).val("");
			$("#rnc" ).val("");
			$("#resultados4").load("../ajax/tipo_doc.php");
		}
		if (event.keyCode==$.ui.keyCode.DELETE){
			$("#nombre_cliente" ).val("");
			$("#id_cliente" ).val("");
			$("#rnc" ).val("");
		}
	});
</script>
<!-- FIN -->
<script>
// print order function
function printOrder(id_factura) {
	$('#modal_vuelto').modal('hide');//CIERRA LA MODAL
	if (id_factura) {
		$.ajax({
			url: '../pdf/documentos/imprimir_venta.php',
			type: 'post',
			data: {
				id_factura: id_factura
			},
			dataType: 'text',
			success: function(response) {
				var mywindow = window.open('', 'Stock Management System', 'height=400,width=600');
				mywindow.document.write('<html><head><title>Facturación</title>');
				mywindow.document.write('</head><body>');
				mywindow.document.write(response);
				mywindow.document.write('</body></html>');
                mywindow.document.close(); // necessary for IE >= 10
                mywindow.focus(); // necessary for IE >= 10
                mywindow.print();
                mywindow.close();
            } // /success function

        }); // /ajax function to fetch the printable order
    } // /if orderId
} // /print order function
</script>
<script>
// print order function

function printFactura(id_factura) {
	$('#modal_vuelto').modal('hide');
	if (id_factura) {
		$.ajax({
			url: '../pdf/documentos/imprimir_factura_venta.php',
			type: 'post',
			data: {
				id_factura: id_factura
			},
			dataType: 'text',
			success: function(response) {
				var mywindow = window.open('', 'Stock Management System', 'height=400,width=600');
				mywindow.document.write('<html><head><title>Facturación</title>');
				mywindow.document.write('</head><body>');
				mywindow.document.write(response);
				mywindow.document.write('</body></html>');
                mywindow.document.close(); // necessary for IE >= 10
                mywindow.focus(); // necessary for IE >= 10
                mywindow.print();
                mywindow.close();
            } // /success function

        }); // /ajax function to fetch the printable order
    } // /if orderId
} // /print order function
</script>
<script>
	function obtener_caja(user_id) {
		console.log("carga");
		//$(".outer_div3").load("../modal/carga_caja.php?user_id=" + user_id);//carga desde el ajax
	}
</script>
<script>
	function showDiv(select){
		const btncompra = document.getElementById('guardar_factura');
		if(select.value == 2 || select.value == 6)
		{
			$("#resultados3").load("../ajax/carga_numcheque.php");
			btncompra.disabled = false;
		}
		else if(select.value==4 || select.value==5 )
		{
			$("#resultados3").load("../ajax/carga_prima.php");
			btncompra.disabled = false;
		} 
		else if(select.value==7 )	
		{
			$("#resultados3").load("../ajax/carga_prima.php");
   			btncompra.disabled = true;
		}
		else if(select.value==3 )	
		{
			$("#resultados3").load("../ajax/carga_numvoucher.php");
			
   			btncompra.disabled = false;
		}
		else{
			$("#resultados3").load("../ajax/carga_resibido.php");
			btncompra.disabled = false;
		}
	
	}
	function comprobar(select){
		var rnc = $("#rnc").val();
		if(select.value==1 && rnc==''){
			$.Notification.notify('warning','bottom center','NOTIFICACIÓN', 'AL CLIENTE SELECCIONADO NO SE LE PUEDE IMPRIR LA FACTURA, NO TIENE RNC/DEDULA REGISTRADO')
			$("#resultados4").load("../ajax/tipo_doc.php");
		} 
	}
</script>
<script>
	function trasladomercaderia(select)
	{
		if(select.value == 1)
		{
			location.reload();	
		}else{
			document.getElementById('buscarcliente').style.display = 'none'; 
			document.getElementById('divfiscal').style.display = 'none'; 
			document.getElementById('idpagos').style.display = 'none'; 
			document.getElementById('comprobante').style.display = 'none'; 
			document.getElementById('btnguardar').style.display = 'none';
			$("#datos_traslados").show(); 
		}
	}
</script>
<script>
  function getval(sel)
  {
    $.Notification.notify('success', 'bottom center', 'NOTIFICACIÓN', 'CAMBIO DE COMPROBANTE')
    $("#outer_comprobante").load("../ajax/carga_correlativos.php?id_comp="+sel.value);

  }
</script>
<script>
  function cf()
  {
	$('#nombre_cliente').val("Consumidor Final");
	$('#direccion_cliente').val("Ciudad");
	$('#rnc').val("CF");
  }
</script>
<?php require 'includes/footer_end.php'
?>