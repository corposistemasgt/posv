		$(document).ready(function() {
			load(1);
		});
		function load(page) {
			var q = $("#q").val();
			var id = $("#ruta").val();
			$("#loader").fadeIn('slow');
			$.ajax({ 
				url: '../ajax/buscar_cxr.php?action=ajax&page=' + page + '&q=' + q+ '&idruta=' + id,
				beforeSend: function(objeto) {
					$('#loader').html('<img src="../../img/ajax-loader.gif"> Cargando...');
				},
				success: function(data) {
					$(".outer_div").html(data).fadeIn('slow');
					$('#loader').html('');
					$('[data-toggle="tooltip"]').tooltip({
						html: true
					});
				}
			})
		}
		$('#dataDelete').on('show.bs.modal', function(event) {
		    var button = $(event.relatedTarget)
		    var id = button.data('id')
		    var modal = $(this)
		    modal.find('#id_factura').val(id)
		})
		$("#eliminarDatos").submit(function(event) {
			var parametros = $(this).serialize();
			$.ajax({
				type: "POST",
				url: "../ajax/eliminar_factura.php",
				data: parametros,
				beforeSend: function(objeto) {
					$(".datos_ajax_delete").html('<img src="../../img/ajax-loader.gif"> Cargando...');
				},
				success: function(datos) {
					$(".datos_ajax_delete").html(datos);
					$('#dataDelete').modal('hide');
		            $(".alert-success").delay(400).show(10, function() {
		            	$(this).delay(2000).hide(10, function() {
		            		$(this).remove();
		            	});
		            });
		            load(1);
		        }
		    });
			event.preventDefault();
		});
$("#add_abono").submit(function(event) {
$('#guardar_datos').attr("disabled", true);
var abono = $("#abono").val();
    if (isNaN(abono)) {
    	$.Notification.notify('error','bottom center','NOTIFICACIÓN', 'El ABONO NO ES UN DATO VALIDO, INTENTAR DE NUEVO')
    	$("#abono").focus();
    	$('#guardar_datos').attr("disabled", false);
    	return false;
    }
    var parametros = $(this).serialize();
    $.ajax({
    	type: "POST",
    	url: "../ajax/agregar_abono.php",
    	data: parametros,
    	beforeSend: function(objeto) {
    		$("#resultados_ajax").html('<img src="../../img/ajax-loader.gif"> Cargando...');
    	},
    	success: function(datos) {
    		$("#resultados_ajax").html(datos);
    		$('#guardar_datos').attr("disabled", false);
    		load(1);
            $("#add_abono")[0].reset();
            $('#add-pago').modal('hide');
            window.setTimeout(function() {
            	$(".alert").fadeTo(500, 0).slideUp(500, function() {
            		$(this).remove();
            	});
            }, 5000);
        }
    });
    event.preventDefault();
})
		function imprimir_factura(id_factura) {
			VentanaCentrada('../pdf/documentos/ver_factura.php?id_factura=' + id_factura, 'Factura', '', '724', '568', 'true');
		}
		function printOrder(id_factura) {
			if (id_factura) {
				$.ajax({
					url: '../pdf/documentos/imprimir_factura.php',
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
		function print_ticket(id_factura) {
			if (id_factura) {
				$.ajax({
					url: '../pdf/documentos/imprimir_venta_edit.php',
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