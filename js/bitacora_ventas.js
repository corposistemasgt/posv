		$(document).ready(function() {
		    load(1);
		});
		function load(page) {
		    var q = $("#q").val();
			var fecha = $("#range").val();
		    $("#loader").fadeIn('slow');
		    $.ajax({
		        url: '../ajax/buscar_ventas.php?action=ajax&page=' + page + '&q=' + q+'&range='+fecha,
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

		function generar_ticket(id_factura,open) {
			const objetoJSON = {factura: id_factura,open: open};
			console.log("corpoprint-ticket"+objetoJSON);
		}

		function pedirNit(id_factura){ 
			var page = $('#pagina_actual').val();
		        $.ajax({
		            url: '../pdf/documentos/pedir_nit_facturar_listado.php?id_factura=' + id_factura,
		            type: 'GET',
		            dataType: 'text',
		            success: function(response) {
						var data = JSON.parse(response);
						if(data['certificado'] == "0"){
							$('#modal-container').load('../modal/modal_consultar_nit.php', function() {
								$('#modal_consultar_nit').modal('show');
								$('#rnc').val(data['nitExistente']);
								$('#nombre_cliente').val(data['nombreExistente']);
								$('#idfactura').val(id_factura);
								$('#pageActual').val(page);
							});
						}else{
							if ('Android'==isMobile())
							{	
								console.log("corpoprint"+id_factura);
								
							}	
							else
							{
								let date = new Date();
								let formatter = new Intl.DateTimeFormat('en-US',{timeStyle: "long"  },{ timeZone: "America/Guatemala" });  							
								let usDate = formatter.format(date);
								let text = usDate.replace("/", "-");
								let textoFinal = text.replace(":", "-");
								var link = document.createElement('a');
								link.href = '../pdf/documentos/descargar_factura.php?id_factura='+id_factura;
								link.download = 'dte '+textoFinal+'.pdf';
								link.dispatchEvent(	new MouseEvent('click'));
							}
							
						}
						
		            } 
		        }); 
		}

		$("#form_consultar_nit").submit(function(event){
			var parametros = $(this).serialize();
			idFactura = $('#idfactura').val();
			pagina = $('#pageActual').val();
			direccion = $('#direccion_cliente').val();
			$.ajax({
				type: "POST",
				url: '../pdf/documentos/guardar_nit_direccion.php',
				data: parametros,
				success: function(response) {
					console.log(response);
					const obj = JSON.parse(response);
					if(obj.resultado=="true"){
						console.log(obj.sucursal);
						imprimir_factura(idFactura,1,obj.sucursal); 
					}else{
						swal("Error de Datos", "Ha ocurrido un error al actualizar los datos de facturacion", "error");
						
					}
				}
			});
			event.preventDefault();
		});
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
		function print_ticket(id_factura,origen) {
		    if (id_factura) 
			{ 
				var open=0;
				if(origen=='1')
				{
					open=1;
				}
				if ('Android'==isMobile())
				{
					const objetoJSON = {factura: id_factura,open: open};
					console.log("corpoprint-ticket"+JSON.stringify(objetoJSON));
				}
				else
				{
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
		}
		function anular_factura(id_factura, numero_factura) {
			var page = $('#pagina_actual').val();
			swal({
				title: "Estas Seguro?",
				text: "Se anulara la factura: "+numero_factura+" y se reintegrara la cantidad al inventario",
				icon: "warning",
				buttons: true,
				dangerMode: true,
			  })
			  .then((willDelete) => {
				if (willDelete) {
					$.ajax({
						url: '../pdf/documentos/anular_factura.php',
						type: 'post',
						data: {
							id_factura: id_factura
						},
						dataType: 'text',
						success: function(response) {
							console.log(response);
							const obj = JSON.parse(response);
					
							if(obj.resultado=="false")
							{
								swal("Error de Anulacion", "Notifica a Soporte Tecnico, el error es: "+atob(obj.detalle), "error");
								console.log("falso");
							}
							else
							{
								swal("Exito", "Factura anulada Correctamente", "success");
								load(page);
							}	
						} 
					}); 
				  
				} else {
					swal("Cuidado", "Se ha cancelado la anulacion!", "info");
			
				}
			  });
			}
		function buscar_nit()
		{
			var nit = $('#rnc').val();
			if(nit=='CF' ||nit=='cF' || nit=='Cf' ||nit=='cf')
			{
				$('#nombre_cliente').val("Consumidor Final");
				$('#rnc').val("CF");
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
						{	$('#nombre_cliente').val('');
							$('#nombre_cliente').val(obj.nombre);
							
							$('#direccion_cliente').val('');
							if(obj.direccion==undefined)
							{
								$('#direccion_cliente').val('Ciudad');
							}else
							{
								$('#direccion_cliente').val(obj.direccion);
							}
							if(obj.correos!=undefined)
							{
								$('#correo_cliente').val(obj.correos);
							}
							if(obj.telefono!=undefined)
							{
								$('#telefono_cliente').val(obj.telefono);
							}
						}
					}
				});
			}
		}
		$("#rnc").keydown( function(e) {
			if(e.which == 13) {
				e.preventDefault();
				buscar_nit();
			}
			});
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
		function imprimir_factura(id_factura,origen,idsucursal) {
			console.log("-"+id_factura+"-"+idsucursal);
			var open=0;
			if(origen=='1'){open=1;}
			$.ajax({
				url: '../certificar.php?id_factura=' + id_factura+'&idsucursal='+idsucursal, 
				type: 'GET',
				dataType: 'text',
				success: function(response) {
					console.log(response);
					const obj = JSON.parse(response);
					if(obj.resultado=="false")
					{
						swal("Error de Certificacion", "Notifica a Soporte Tecnico, el error es: "+atob(obj.descripcion), "error");
						console.log("falso");
					}
					else
					{
						location.href =obj.link;
						enviar_documentos(response);
						$('#modal_consultar_nit').modal('hide');
						load(1);
					}					
				} 
			,
				error:function(data,e){ swal("Error",data.responseText, "error");  }
			});
		
		}
		function enviar_documentos(response)
		{
			var corrr=$('#correo_cliente').val();
    var tel=$('#telefono_cliente').val();
    const obj = JSON.parse(response);
    $.ajax({
     
        url: '../enviardocumentos.php?datos=' +  btoa(response)+'&tel='+btoa(tel)+'&corr='+btoa(corrr)+'&receptor='+btoa(obj.receptor)+'&emisor='+btoa(obj.emisor)+
        '&nitemisor='+btoa(obj.nitemisor)+'&requestor='+btoa(obj.requestor)+'&guid='+btoa(obj.guid)+'&fecha='+btoa(obj.fecha)+'&tipo='+btoa(obj.tipo)+'&total='+btoa(obj.total)+
        '&link='+btoa(obj.link), 
        type: 'GET',
        dataType: 'text',
        success: function(response) {
            console.log(response);
          			
        } 
    });
}