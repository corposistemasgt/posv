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
$permisos_ver =getpermiso(24);
$nombre_usuario = get_row('users', 'usuario_users', 'id_users', $user_id);
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
									Nueva Cotización
								</h3>
								<div class="portlet-widgets">
									<a href="javascript:;" data-toggle="reload"><i class="ion-refresh"></i></a>
									
								</div>
								<div class="clearfix"></div>
							</div>
							<div id="bg-primary" class="panel-collapse collapse show">
								<div class="portlet-body">
									<?php
include "../modal/buscar_productos_ventas.php";
    include "../modal/registro_cliente.php";
    include "../modal/registro_producto.php";
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
														<div class="form-group row">
															<label class="col-2 col-form-label"></label>
															<div class="col-12">
																<div class="input-group">
																	<input type="text" id="nombre_cliente" name="nombre_cliente" class="form-control" placeholder="Buscar Cliente" required  tabindex="2">
																	<span class="input-group-btn">
																		<button type="button" class="btn waves-effect waves-light btn-success" data-toggle="modal" data-target="#nuevoCliente"><li class="fa fa-plus"></li></button>
																	</span>
																	<input id="id_cliente" name="id_cliente" type='hidden'>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-6">
																<div class="form-group">
																	<label for="fiscal"> Nit/Código</label>
																	<input type="text" class="form-control" autocomplete="off" id="tel1" name="tel1" >
																</div>
															</div>
															<div class="col-md-6">
																<div class="form-group">
																	<label for="fiscal">No. Cotización</label>
																	<div id="f_resultado"></div><!-- Carga los datos ajax del incremento de la fatura -->
																</div>
															</div>
														</div>

														<div class="row">
															<div class="col-md-6">
																<div class="form-group">
																	<label for="condiciones">Pago</label>
																	<select class="form-control input-sm condiciones" id="condiciones" name="condiciones" onchange="showDiv(this)">
																		<option value="1">Contado</option>
																	</select>
																</div>
															</div>
															<div class="col-md-6">
																<div class="form-group">
																	<label for="validez">Periodo de Validez</label>
																	<select class="form-control" id="validez" name="validez">
																		<option value="1">5 días</option>
																		<option value="2">10 días</option>
																		<option value="3">15 días</option>
																		<option value="4">30 días</option>
																		<option value="5">60 días</option>
																	</select>
																</div>
															</div>
														</div>
														<div class="col-md-12" align="center">
															<button type="submit" id="guardar_factura" class="btn btn-danger btn-block btn-lg waves-effect waves-light" aria-haspopup="true" aria-expanded="false"><span class="fa fa-save"></span> Guardar</button>
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
<script type="text/javascript" src="../../js/cotizacion.js"></script>
<script>
	$(function() {
		$("#nombre_cliente").autocomplete({
			source: "../ajax/autocomplete/clientes.php",
			minLength: 2,
			select: function(event, ui) {
				event.preventDefault();
				$('#id_cliente').val(ui.item.id_cliente);
				$('#nombre_cliente').val(ui.item.nombre_cliente);
				$('#tel1').val(ui.item.fiscal_cliente);
				$('#em').val(ui.item.email_cliente);
				$.Notification.notify('success','bottom right','EXITO!', 'CLIENTE AGREGADO CORRECTAMENTE')
			}
		});
	});
	$("#tel1").keydown( function(e) {
    if(e.which == 13) {
        
        e.preventDefault();
		buscar_nit();     
    }
    });
	$("#nombre_cliente" ).on( "keydown", function( event ) {
		if (event.keyCode== $.ui.keyCode.LEFT || event.keyCode== $.ui.keyCode.RIGHT || event.keyCode== $.ui.keyCode.UP || event.keyCode== $.ui.keyCode.DOWN || event.keyCode== $.ui.keyCode.DELETE || event.keyCode== $.ui.keyCode.BACKSPACE )
		{
			$("#id_cliente" ).val("");
			$("#tel1" ).val("");
			$("#em" ).val("");
		}
		if (event.keyCode==$.ui.keyCode.DELETE){
			$("#nombre_cliente" ).val("");
			$("#id_cliente" ).val("");
			$("#tel1" ).val("");
			$("#em" ).val("");
		}
	});
	function buscar_nit()
{
    var nit = $('#tel1').val();
    if(nit=='CF' ||nit=='cF' || nit=='Cf' ||nit=='cf')
    {
        $('#nombre_cliente').val("Consumidor Final");
        $('#tel1').val("CF");
    }
    else
    {
       
        $.ajax({
            type: 'GET',
            url: '../ajax/consultar_cliente.php',
            data: "nit=" + nit,// + "&param2=" + param2,
            beforeSend: function(objeto) {
                $(".datos_ajax_delete").html('<img src="../../img/ajax-loader.gif"> Cargando...');
            },
            success: function(data) {
                console.log(data);
                $(".datos_ajax_delete").html('');               
                const obj = JSON.parse(data);
                if(obj.resultado=="false")
                {
                    swal("Nit Invalido", "No se pudo localizar el numero de Nit", "error");
                    $('#nombre_cliente').val('');
                }
                else
                {	
                    $('#nombre_cliente').val('');
                    $('#nombre_cliente').val(obj.nombre);
                }
            }
        });

    }
}
</script>
<script>
	function isMobile(){
    return (
        (navigator.userAgent.match(/Android/i)) ||
        (navigator.userAgent.match(/webOS/i)) ||
        (navigator.userAgent.match(/iPhone/i)) ||
        (navigator.userAgent.match(/iPod/i)) ||
        (navigator.userAgent.match(/iPad/i)) ||
        (navigator.userAgent.match(/BlackBerry/i))
    );
}
	</script>
<script>
function printFactura(id_factura,tipo) {  
	if ('Android'==isMobile())
        {    
            const objetoJSON = {factura: id_factura,open: open};
			console.log("corpoprint-coti"+JSON.stringify(objetoJSON));
        }
        else
        {
			let url="";
            if(tipo==1)
            {
                url='../pdf/documentos/imprimir_cotizacion.php';
                
            }
            else
            {
                url='../pdf/documentos/imprimir_cotizacion_ticket.php';
            }
			$('#modal_vuelto').modal('hide');
    if (id_factura) {
        $.ajax({
            url: url,
            type: 'post',
            data: {
                id_factura: id_factura 
            },
            dataType: 'text',
            success: function (response) {
                var mywindow = window.open('', 'Stock Management System', 'height=400,width=600');

                if (mywindow) {
                    mywindow.document.write('<html><head><title>Facturación</title>');
                    mywindow.document.write('</head><body>');
                    mywindow.document.write(response);
                    mywindow.document.write('</body></html>');
                    mywindow.document.close(); // necessary for IE >= 10
                    mywindow.focus(); // necessary for IE >= 10
                    mywindow.print();
                    mywindow.close();
                } else {
                    alert('No se pudo abrir la ventana de impresión. Asegúrese de desactivar el bloqueo emergente.');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error al obtener la factura:', error);
                alert('Error al obtener la factura. Por favor, inténtelo de nuevo.');
            }
        });
    } else {
        alert('ID de factura no válido.');
    }
		}
   
}
</script>
<script>
</script>
<script>
	function obtener_caja(user_id) {
		$(".outer_div3").load("../modal/carga_caja.php?user_id=" + user_id);//carga desde el ajax
	}
</script>
<script>
	function showDiv(select){
		if(select.value==4){
			$("#resultados3").load("../ajax/carga_prima.php");
		} else{
			$("#resultados3").load("../ajax/carga_resibido.php");
		}
	}
</script>
<?php require 'includes/footer_end.php'
?>