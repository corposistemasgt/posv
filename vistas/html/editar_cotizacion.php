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
require_once "../funciones.php";
$user_id = $_SESSION['id_users'];
$permisos_ver =getpermiso(26);
$nombre_usuario = get_row('users', 'usuario_users', 'id_users', $user_id);
if (isset($_GET['id_factura'])) {
    $id_factura  = intval($_GET['id_factura']);
    $campos      = "facturas_cot.id_cliente, facturas_cot.id_vendedor, facturas_cot.fecha_factura, facturas_cot.condiciones, facturas_cot.validez, facturas_cot.numero_factura, facturas_cot.estado_factura, clientes.nombre_cliente, clientes.fiscal_cliente, clientes.direccion_cliente";
    
   
    $sql_factura = mysqli_query($conexion, "SELECT $campos FROM facturas_cot LEFT JOIN clientes ON facturas_cot.id_cliente = clientes.id_cliente WHERE facturas_cot.id_factura = '$id_factura'");
    
    $count       = mysqli_num_rows($sql_factura);
    if ($count == 1) {
        $rw_factura                 = mysqli_fetch_array($sql_factura);
        $id_cliente                 = $rw_factura['id_cliente'];
        $nombre_cliente             = $rw_factura['nombre_cliente'];  
        $fiscal_cliente             = $rw_factura['fiscal_cliente'];  
        $direccion_cliente          = $rw_factura['direccion_cliente'];  
        $id_vendedor_db             = $rw_factura['id_vendedor'];
        $fecha_factura              = date("d/m/Y", strtotime($rw_factura['fecha_factura']));
        $condiciones                = $rw_factura['condiciones'];
        $validez                    = $rw_factura['validez'];
        $numero_factura             = $rw_factura['numero_factura'];
        $estado             		= $rw_factura['estado_factura'];
        $_SESSION['id_factura']     = $id_factura;
        $_SESSION['numero_factura'] = $numero_factura;
    } else {
        header("location: facturas.php");
        exit;
    }
} else {
    header("location: facturas.php");
    exit;
}
$query = $conexion->query("select * from comprobantes");
$tipo  = array();
while ($r = $query->fetch_object()) {$tipo[] = $r;}
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
							<div class="portlet-heading bg-primary">
								<h3 class="portlet-title">
									Editar Cotización
								</h3>
								<div class="portlet-widgets">
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
																	<input type="text" class="form-control" id="barcode" <?php if($estado==2){echo 'disabled="true"';}?>  autocomplete="off"  tabindex="1" autofocus="true" >
																	<span class="input-group-btn">
																		<button type="submit" id="txtbusqueda" class="btn btn-default"><span  class="fa fa-barcode"></span></button>
																	</span>
																</div>
															</div>
															<div class="col-md-2">
																<button type="button" accesskey="a" <?php if($estado==2){echo 'disabled="true"';}?>  	 class="btn btn-primary waves-effect waves-light" id="btbusqueda" data-toggle="modal" data-target="#buscar">
																	<span class="fa fa-search"></span> Buscar
																</button>
															</div>
														</div>
													</form>
													<div id="resultados" class='col-md-12' style="margin-top:10px"  ></div><!-- Carga los datos ajax -->
												</div>
											</div>
										</div>
										<div class="col-lg-4">
											<div class="card-box">
												<div class="widget-chart">
												<div class="editar_factura" class='col-md-12' style="margin-top:10px"></div><!-- Carga los datos ajax -->
													<form role="form" id="datos_factura">
														<input id="id_vendedor" name="id_vendedor" type='hidden' value="<?php echo $id_vendedor_db; ?>">
														<div class="form-group row">
															<label class="col-2 col-form-label"></label>
															<div class="col-12">
																<div class="input-group">
																	<input type="text" id="nombre_cliente" name="nombre_cliente"  class="form-control" required value="<?php echo $nombre_cliente; ?>" tabindex="2">
																	<input type="text" id="nit_cliente" name="nit_cliente" style="display:none"  value="<?php echo $fiscal_cliente; ?>" >
																	<span class="input-group-btn">
																		<button type="button" <?php if($estado==2){echo 'disabled="true"';}?>  class="btn waves-effect waves-light btn-success" data-toggle="modal" data-target="#nuevoCliente"><li class="fa fa-plus"></li></button>
																	</span>
																	<input id="id_cliente" name="id_cliente" type='hidden' value="<?php echo $id_cliente; ?>">
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-6">
																<div class="form-group">
																	<label for="cotizacion">No. Cotización</label>
																	<input type="text" class="form-control" autocomplete="off" id="cotizacion"  name="cotizacion" value="<?php echo $numero_factura; ?>" readonly>
																</div>
															</div>
																<div class="col-md-6">
																<div class="form-group">
																	<label for="validez">Periodo de Validez</label>
																	<select class='form-control' id="validez" name="validez">
																		<option value="1" <?php if ($validez == 1) {echo "selected";}?>>5 días</option>
																		<option value="2" <?php if ($validez == 2) {echo "selected";}?>>10 días</option>
																		<option value="3" <?php if ($validez == 3) {echo "selected";}?>>15 días</option>
																		<option value="4" <?php if ($validez == 4) {echo "selected";}?>>30 días</option>
																		<option value="5" <?php if ($validez == 5) {echo "selected";}?>>60 días</option>
																	</select>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-6">
																<div class="form-group">
																	<label for="fiscal"> Nit</label>
																	<input type="text" class="form-control" autocomplete="off" id="rnc" name="rnc" disabled="true" value="<?php echo $fiscal_cliente; ?>">
																</div>
															</div>
															<div class="col-md-6">
																<div class="form-group">
															<div class="col-md-12">
																<div class="form-group">
																	<div id="resultados4"></div>
																</div>
																<div id="resultados5"></div>
															</div>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-6">
																<div class="form-group">
																	<label for="fiscal">Pago</label>
																	<select class='form-control input-sm' id="condiciones" name="condiciones" onchange="showDiv(this)">
																		<option value="1" <?php if ($condiciones == 1) {echo "selected";}?>>Contado</option>
																	</select>
																</div>
															</div>
															<div class="col-md-6">
																<div class="form-group">
																	<div id="resultados3"></div><!-- Carga los datos ajax del incremento de la fatura  <?php if($estado==2){echo 'disabled="true"';}?>-->
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-6">
																<button type="button" class="btn btn-danger waves-effect waves-light" aria-haspopup="true" <?php if($estado==2){echo 'disabled="true"';}?>   aria-expanded="false" id="btn_actualizar"><span class="fa fa-refresh"></span> Actualizar</button>
															</div>
															<div class="col-md-6">
																<button type="button" class="btn btn-default waves-effect waves-light"   <?php if($estado==2){echo 'disabled="true"';}?>  id="btn_guardar"><span class="ti-shopping-cart-full"></span> FACTURAR</button>
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
	<script type="text/javascript" src="../../js/editar_cotizacion.js"></script>
	<script>
  		function ocultar(){
		   $('#btn_guardar').disabled(true);
		   $('#btn_actualizar').disabled(true);
		   $('#txtbusqueda').disabled(true);
		   $('#btbusqueda').disabled(true);
		   $('#barcode').disabled(true);
  		}		
</script>
	<script>
		$(function() {
			$("#nombre_cliente").autocomplete({
				source: "../ajax/autocomplete/clientes.php",
				minLength: 2,
				select: function(event, ui) {
					event.preventDefault();
					$('#id_cliente').val(ui.item.id_cliente);
					$('#nombre_cliente').val(ui.item.nombre_cliente);
					$('#rnc').val(ui.item.fiscal_cliente);
					$('#direccion_cliente').val(ui.item.direccion_cliente);
					$.Notification.notify('custom','bottom right','EXITO!', 'CLIENTE AGREGADO CORRECTAMENTE')
				}
			});
		});
		$('#btn_guardar').click(function(){
          $('#btn_guardar').hide();
		  $('#btn_actualizar').hide();
		  $('#txtbusqueda').hide();
		  $('#btbusqueda').hide();
		  $('#barcode').hide();
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
<script>
function printOrder(id_factura) {
	$('#modal_vuelto').modal('hide');
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
                mywindow.document.close(); 
                mywindow.focus(); 
                mywindow.print();
                mywindow.close();
            } 
        });
    } 
} 
</script>
<script>
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
                mywindow.document.close(); 
                mywindow.focus();
                mywindow.print();
                mywindow.close();
            } 

        });
    } 
} 
</script>
<script>
function obtener_caja(user_id) {
		$(".outer_div3").load("../modal/carga_caja.php?user_id=" + user_id);
	function showDiv(select){
		if(select.value==4){
			$("#resultados3").load("../ajax/carga_prima.php");
		} else{
			$("#resultados3").load("../ajax/carga_resibido.php");
		}
	}
	function comprobar(select){
		var rnc = $("#rnc").val();
		if(select.value==1 && rnc==''){
			$.Notification.notify('warning','bottom center','NOTIFICACIÓN', 'AL CLIENTE SELECCIONADO NO SE LE PUEDE IMPRIR LA FACTURA, NO TIENE RNC/DEDULA REGISTRADO')
			$("#resultados4").load("../ajax/tipo_doc.php");
		} else{
		}
	}
	function getval(sel)
  {
    $.Notification.notify('success', 'bottom center', 'NOTIFICACIÓN', 'CAMBIO DE COMPROBANTE')
    $("#outer_comprobante").load("../ajax/carga_correlativos.php?id_comp="+sel.value);

  }
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