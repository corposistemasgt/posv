<?php
session_start();
if (isset($conexion)) {
    ?>
    <!-- Modal -->
    <script >
      function cambiar_password() {
        var nueva= $("#user_password_new3").val();
        var confirma = $("#user_password_repeat3").val();
		    $('#actualizar_datos3').attr("disabled", true);
	
		    $.ajax({
		        type: "POST",
		        url: "../ajax/editar_password_global.php",
		        data:"&user_password_new3=" +nueva+"&user_password_repeat3=" + confirma,
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
      }

    </script>
    <div class="modal fade" id="password_edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel"><i class='fa fa-unlock'></i> Cambiar contraseña</h4>
          </div>
          <div class="modal-body">
            <form class="form-horizontal" method="post" id="editar_password" name="editar_password">
              <div id="resultados_ajax3"></div>

              <div class="form-group">
                <label for="user_password_new3" class="col-sm-4 control-label">Nueva contraseña</label>
                <div class="col-sm-8">
                  <input type="password" class="form-control" id="user_password_new3" name="user_password_new3" placeholder="Nueva contraseña" pattern=".{6,}" title="Contraseña ( min . 6 caracteres)" required>
                  <div class="outer_div3"></div>
                </div>
              </div>
              <div class="form-group">
                <label for="user_password_repeat3" class="col-sm-4 control-label">Repite contraseña</label>
                <div class="col-sm-8">
                  <input type="password" class="form-control" id="user_password_repeat3" name="user_password_repeat3" placeholder="Repite contraseña" pattern=".{6,}" required>
                </div>
              </div>

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default waves-effect waves-light" data-dismiss="modal">Cerrar</button>
              <button type="button" class="btn btn-primary waves-effect waves-light"  onclick="cambiar_password();" >Cambiar contraseña</button>
            </div>
          </form>
        </div>
      </div>
    </div>
<?php
}
?>
