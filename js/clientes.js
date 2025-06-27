		$(document).ready(function() {
		    load(1);
		});
		function load(page) {
		    var q = $("#q").val();
			var r = $("#ruta1").val();
			var rutero = $("#rutero").val();
		
		    $("#loader").fadeIn('slow');
		    $.ajax({ 
		        url: '../ajax/buscar_cliente.php?action=ajax&page=' + page + '&q=' + q + '&r=' + r
				+ '&rutero=' + rutero,
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

		document.addEventListener("DOMContentLoaded", function() {
			document.getElementById("ruta").addEventListener("change", function() {
				console.log("Evento onchange activado");
				console.log("Valor seleccionado:", this.value);
			});
		});
		$("#guardar_cliente").submit(function(event) {
		    $('#guardar_datos').attr("disabled", true);
		    var parametros = $(this).serialize();
		    $.ajax({
		        type: "POST",
		        url: "../ajax/nuevo_cliente.php",
		        data: parametros,
		        beforeSend: function(objeto) {
		            $("#resultados_ajax").html('<img src="../../img/ajax-loader.gif"> Cargando...');
		        },
		        success: function(datos) {
		            $("#resultados_ajax").html(datos);
		            $('#guardar_datos').attr("disabled", false);
		            load(1);
		            //resetea el formulario
		            $("#guardar_cliente")[0].reset();
		            $("#nombre").focus();
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
		$("#editar_cliente").submit(function(event) {
		    $('#actualizar_datos').attr("disabled", true);
		    var parametros = $(this).serialize();
		    $.ajax({
		        type: "POST",
		        url: "../ajax/editar_cliente.php",
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
		    modal.find('#id_cliente').val(id)
		})
		$("#eliminarDatos").submit(function(event) {
		    var parametros = $(this).serialize();
		    $.ajax({
		        type: "POST",
		        url: "../ajax/eliminar_cliente.php",
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

		function obtener_datos(id) {
		    var nombre_cliente = $("#nombre_cliente" + id).val();
		    var fiscal_cliente = $("#fiscal_cliente" + id).val();
		    var telefono_cliente = $("#telefono_cliente" + id).val();
		    var email_cliente = $("#email_cliente" + id).val();
		    var direccion_cliente = $("#direccion_cliente" + id).val();
		    var status_cliente = $("#status_cliente" + id).val();
			var idruta = $("#idruta" + id).val();
			var credito = $("#credito" + id).val();
			var limite_credito = $("#limite_credito" + id).val();
			console.log(idruta);
			console.log(credito);
		    $("#mod_nombre").val(nombre_cliente);
		    $("#mod_fiscal").val(fiscal_cliente);
		    $("#mod_telefono").val(telefono_cliente);
		    $("#mod_email").val(email_cliente);
		    $("#mod_direccion").val(direccion_cliente);
		    $("#mod_estado").val(status_cliente);
			$("#mod_ruta").val(idruta);
		    $("#mod_credito").val(credito);
			$("#mod_limite_credito").val(limite_credito);
		    $("#mod_id").val(id);
		}
		