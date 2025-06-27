		$(document).ready(function() {
		    load(1);
		});
 
		function load(page) {
		    var q = $("#q").val();
		    $("#loader").fadeIn('slow');
		    $.ajax({
		        url: '../ajax/buscar_usuarios.php?action=ajax&page=' + page + '&q=' + q,
		        beforeSend: function(objeto) {
		            $('#loader').html('<img src="../../img/ajax-loader.gif"> Cargando...');
		        },
		        success: function(data) {
		            $(".outer_div").html(data).fadeIn('slow');
		            $('#loader').html('');
		        }
		    })
		}
		function agregar_ruta()
		{
			console.log("entro al metodo");
			var iduser = $("#mod_id").val();
			var idruta = $("#mod_ruta2").val();
			console.log(iduser+"entro al metodo"+idruta);
			$("#loader").fadeIn('slow');
		    $.ajax({
		        url: '../ajax/agregar_asignacion.php?action=ajax&page=1&iduser=' + iduser+"&idruta="+idruta,
		        beforeSend: function(objeto) {
		        },
		        success: function(data) {
		            cargar();
		        }
		    })
		    event.preventDefault();
		}
		function cargar()
		{
			var id = $("#mod_id").val();
		    $("#loader").fadeIn('slow');
		    $.ajax({
		        url: '../ajax/asignacion_rutas.php?action=ajax&page=1&id=' + id,
		        beforeSend: function(objeto) {
		        },
		        success: function(data) {
		            $(".outer_divrut").html(data).fadeIn('slow');
		        }
		    })
		}
		$("#guardar_usuario").submit(function(event) {
		    $('#guardar_datos').attr("disabled", true);
		    var parametros = $(this).serialize();
		    $.ajax({
		        type: "POST",
		        url: "../ajax/nuevo_usuario.php",
		        data: parametros,
		        beforeSend: function(objeto) {
		            $("#resultados_ajax").html('<img src="../../img/ajax-loader.gif"> Cargando...');
		        },
		        success: function(datos) {
		            $("#resultados_ajax").html(datos);
		            $('#guardar_datos').attr("disabled", false);
		            load(1);
		            $("#guardar_usuario")[0].reset();
		            $("#firstname").focus();
		            //desaparecer la alerta
		            window.setTimeout(function() {
		                $(".alert").fadeTo(200, 0).slideUp(200, function() {
		                    $(this).remove();
		                });
		            }, 2000);
		        }
		    });
		    event.preventDefault();
		})
		$("#editar_usuario").submit(function(event) {
		    $('#actualizar_datos2').attr("disabled", true);
		    var parametros = $(this).serialize();
		    $.ajax({
		        type: "POST",
		        url: "../ajax/editar_usuario.php",
		        data: parametros,
		        beforeSend: function(objeto) {
		            $("#resultados_ajax2").html('<img src="../../img/ajax-loader.gif"> Cargando...');
		        },
		        success: function(datos) {
		            $("#resultados_ajax2").html(datos);
		            $('#actualizar_datos2').attr("disabled", false);
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
		})
		$("#editar_password").submit(function(event) {
			console.log("asdasd");
		    $('#actualizar_datos3').attr("disabled", true);
		    var parametros = $(this).serialize();
		    $.ajax({
		        type: "POST",
		        url: "../ajax/editar_password.php",
		        data: parametros,
		        beforeSend: function(objeto) {
		            $("#resultados_ajax3").html('<img src="../../img/ajax-loader.gif"> Cargando...');
		        },
		        success: function(datos) {
		            $("#resultados_ajax3").html(datos);
		            $('#actualizar_datos3').attr("disabled", false);
		            load(1);
		            //resetea el formulario
		            $("#editar_password")[0].reset();
		            //desaparecer la alerta
		            window.setTimeout(function() {
		                $(".alert").fadeTo(200, 0).slideUp(200, function() {
		                    $(this).remove();
		                });
		            }, 2000);
		        }
		    });
		    event.preventDefault();
		})
		$('#dataDelete').on('show.bs.modal', function(event) {
		    var button = $(event.relatedTarget) // Botón que activó el modal
		    var id = button.data('id') // Extraer la información de atributos de datos
		    var modal = $(this)
		    modal.find('#id_usuario').val(id)
		})
		$("#eliminarDatos").submit(function(event) {
		    var parametros = $(this).serialize();
		    $.ajax({
		        type: "POST",
		        url: "../ajax/eliminar_usuario.php",
		        data: parametros,
		        beforeSend: function(objeto) {
		            $(".datos_ajax_delete").html('<img src="../../img/ajax-loader.gif"> Cargando...');
		        },
		        success: function(datos) {
		            $(".datos_ajax_delete").html(datos);
		            $('#dataDelete').modal('hide');
		            load(1);
		            window.setTimeout(function() {
		                $(".alert").fadeTo(200, 0).slideUp(200, function() {
		                    $(this).remove();
		                });
		            }, 2000);
		        }
		    });
		    event.preventDefault();
		});

		function get_user_id(id) {
		    $("#user_id_mod").val(id);
		}

		function obtener_datos(id) {
		    var nombres = $("#nombres" + id).val();
		    var apellidos = $("#apellidos" + id).val();
		    var usuario = $("#usuario" + id).val();
		    var email = $("#email" + id).val();
		    var cargo = $("#cargo" + id).val();
		    var sucursal = $("#sucursal" + id).val();
			var tipo_precio = $("#tipoprecio" + id).val();
			var ruta = $("#idruta" + id).val();
			var precio = "1";
			if(tipo_precio == '4'){
				precio = "Precio4";
			}else if(tipo_precio == '3'){
				precio = "Precio3";
			}else if(tipo_precio == '2'){
				precio = "Precio2";	
			}else{
				precio = "Precio1";
			}
		    $("#mod_id").val(id);
		    $("#firstname2").val(nombres);
		    $("#lastname2").val(apellidos);
		    $("#user_name2").val(usuario);
		    $("#user_email2").val(email);
			$("#mod_ruta2").val(ruta);
		    $("#user_group_id2").val(cargo);
		    $("#sucursal2").val(sucursal);
			$("#precio2").val(tipo_precio);
			cargar();
		}
		