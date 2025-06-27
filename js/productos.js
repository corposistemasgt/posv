		$(document).ready(function() {
		    $("#cod_resultado").load("../ajax/incrementa_cod_prod.php");
		    load(1);
		}); 

		function load(page) {
		    var q = $("#q").val();
		    var categoria=$("#categoria").val();
			var sucursal=$("#sucursals").val();
			var casa=$("#casas").val();
		    $("#loader").fadeIn('slow');
		    $.ajax({
		        url: '../ajax/buscar_productos.php?action=ajax&page=' + page + '&q=' + q + '&categoria=' + categoria+ '&sucursal=' + sucursal+'&casa=' + casa,	
		        beforeSend: function(objeto) {
		            $('#loader').html('<img src="../../img/ajax-loader.gif"> Cargando...');
		        }, 
		        success: function(data) {
		            $(".outer_div").html(data).fadeIn('slow');
		            $('#loader').html('');
		        }
		    }) 
		}  
		$("#guardar_producto").submit(function(event) {
		    $('#guardar_datos').attr("disabled", true);
		    var parametros = $(this).serialize();
		    $.ajax({
		        type: "POST",
		        url: "../ajax/nuevo_producto.php",
		        data: parametros,
		        beforeSend: function(objeto) {
		            $("#resultados_ajax").html('<img src="../../img/ajax-loader.gif"> Cargando...');
		        },
		        success: function(datos) {
					console.log(datos);
		            $("#resultados_ajax").html(datos);
		            $('#guardar_datos').attr("disabled", false);
		            $("#cod_resultado").load("../ajax/incrementa_cod_prod.php");
		            load(1);
		            $("#guardar_producto")[0].reset();
		            window.setTimeout(function() {
		                $(".alert").fadeTo(500, 0).slideUp(500, function() {
		                    $(this).remove();
		                });
		            }, 5000);
		        }
		    });
		    event.preventDefault();
		})
		$("#editar_producto").submit(function(event) {
		    $('#actualizar_datos').attr("disabled", true);
			console.log("edito");
		    var parametros = $(this).serialize();
		    $.ajax({
		        type: "POST",
		        url: "../ajax/editar_producto.php",
		        data: parametros,
		        beforeSend: function(objeto) {
		            $("#resultados_ajax2").html('<img src="../../img/ajax-loader.gif"> Cargando...');
		        },
		        success: function(datos) {
		            $("#resultados_ajax2").html(datos);
		            $('#actualizar_datos').attr("disabled", false);
		            load(1);
		            //desaparecer la alerta
		            window.setTimeout(function() {
		                $(".alert").fadeTo(500, 0).slideUp(500, function() {
		                    $(this).remove();
		                });
		            }, 5000);
		        }
		    });
		    event.preventDefault();
		})
		$('#dataDelete').on('show.bs.modal', function(event) {
		    var button = $(event.relatedTarget) // Botón que activó el modal
		    var id = button.data('id') // Extraer la información de atributos de datos
		    var modal = $(this)
		    modal.find('#id_producto').val(id) 
		})
		$("#eliminarDatos").submit(function(event) { 
		    var parametros = $(this).serialize();
		    $.ajax({
		        type: "POST",
		        url: "../ajax/eliminar_producto.php",
		        data: parametros,
		        beforeSend: function(objeto) {
		            $(".datos_ajax_delete").html('<img src="../../img/ajax-loader.gif"> Cargando...');
		        },
		        success: function(datos) {
		            $(".datos_ajax_delete").html(datos);
		            $('#dataDelete').modal('hide');
		            load(1);
		            //desaparecer la alerta
		            window.setTimeout(function() {
		                $(".alert").fadeTo(200, 0).slideUp(200, function() {
		                    $(this).remove();
		                });
		            }, 2000);
		        }
		    });
		    event.preventDefault();
		});
		function borrar(id) {
			console.log(id+"-a");
			swal({
				title: "Estas Seguro?",
				text: "Deseas borrar este producto, ya no podras ver los detalles de ventas de este producto",
				icon: "warning",
				buttons: true,
				dangerMode: true,
			  })
			  .then((willDelete) => {
				if (willDelete) {
					$.ajax({
						type: "POST",
						url: "../ajax/eliminar_producto.php",
						data: {
							id_producto: id
						},
						beforeSend: function(objeto) {
							$(".datos_ajax_delete").html('<img src="../../img/ajax-loader.gif"> Cargando...');
						},
						success: function(datos) {
							$(".datos_ajax_delete").html(datos);
							swal("Exito", "Producto Eliminado Correctamente", "success");
							load(1);
							//desaparecer la alerta
							window.setTimeout(function() {
								$(".alert").fadeTo(200, 0).slideUp(200, function() {
									$(this).remove();
								});
							}, 2000);
						}
					});
				  
				} else {
					swal("Cuidado", "Se ha cancelado la eliminacion", "info");
			
				}
			  });
		}
		function obtener_datos(id) {
		    var codigo_producto = $("#codigo_producto" + id).val();
		    var nombre_producto = $("#nombre_producto" + id).val();
		    var descripcion_producto = $("#descripcion_producto" + id).val();
		    var linea_producto = $("#linea_producto" + id).val();
		    var proveedor_producto = $("#proveedor_producto" + id).val();
			var casa= $("#casa" + id).val();
			//proveedor_producto = proveedor_producto.replace("0", "");
		    var inv_producto = $("#inv_producto" + id).val();
			var medida = $("#medida" + id).val();
		    var costo_producto = $("#costo_producto" + id).val();
		    var utilidad_producto = $("#utilidad_producto" + id).val();
		    var precio_producto = $("#precio_producto" + id).val();
		    var precio_mayoreo = $("#precio_mayoreo" + id).val();
		    var precio_especial = $("#precio_especial" + id).val();
			var precio4 = $("#precio_4" + id).val();
		    var stock_producto = $("#stock_producto" + id).val();
			var sucursal_producto = $("#sucursal"+id).val();
		    var stock_min_producto = $("#stock_min_producto" + id).val();
		    var id_imp_producto = $("#id_imp_producto" + id).val();
			var esGenerico = $("#esGenerico"+ id).val();
			var fecha = $("#fecha"+ id).val();
		    var estado = $("#estado" + id).val();
			var bien = $("#bien" + id).val();
			var idcasa = $("#idcasa" + id).val();
		    $("#mod_id").val(id);
		    $("#mod_codigo").val(codigo_producto);
		    $("#mod_nombre").val(nombre_producto);
		    $("#mod_descripcion").val(descripcion_producto);
		    $("#mod_linea").val(linea_producto);
		    $("#mod_proveedor").val(proveedor_producto);
		    $("#mod_inv").val(inv_producto);
			$("#mod_medida").val(medida);
		    $("#mod_costo").val(costo_producto);
		    $("#mod_utilidad").val(utilidad_producto);
		    $("#mod_precio").val(precio_producto);
		    $("#mod_preciom").val(precio_mayoreo);
		    $("#mod_precioe").val(precio_especial);
			$("#mod_precioc").val(precio4);
		    $("#mod_stock").val(stock_producto);
			$("#mod_sucursal").val(sucursal_producto);
			$("#mod_casa").val(casa);
		    $("#mod_minimo").val(stock_min_producto);
		    $("#id_imp2").val(id_imp_producto);
		    $("#mod_estado").val(estado);
			$("#mod_generico").val(esGenerico);
			$("#mod_fecha").val(fecha);
			$("#mod_bien").val(bien);
			$("#mod_casa").val(idcasa);
		}
		function kardex(a,b) {
			console.log(a+" "+b);
			window.open("../html/kardex.php?a="+a+"&b="+b, "_self");   
		}  
		function ajuste(a,b) {
			console.log(a+" "+b);
			window.open("../html/ajustes.php?a="+a+"&b="+b, "_self");   
		} 