<?php
if (isset($conexion)) {
	$idsucursal = $_SESSION['idsucursal'];
	$sql2   = "SELECT rutas FROM tbconfiguracion";;
    $query2 = mysqli_query($conexion, $sql2);
    $rutas=0;
    while ($row = mysqli_fetch_array($query2)) {
        $rutas          = $row['rutas'];
    }
    ?>
	<div id="editarUsers" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title"><i class='fa fa-edit'></i> Editar Usuario</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" method="post" id="editar_usuario" name="editar_usuario">
						<div id="resultados_ajax2"></div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="firstname2" class="control-label">Nombres:</label>
									<input type="text" class="form-control UpperCase" id="firstname2" name="firstname2" required>
									<input type="hidden" id="mod_id" name="mod_id">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="lastname2" class="control-label">Apellidos:</label>
									<input type="text" class="form-control UpperCase" id="lastname2" name="lastname2" required>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="user_name2" class="control-label">Usuario:</label>
									<input type="text" class="form-control" id="user_name2" name="user_name2" pattern="[a-zA-Z0-9]{2,64}" title="Nombre de usuario ( sólo letras y números, 2-64 caracteres)"required>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="user_group_id2" class="control-label">Grupo de permisos</label>
									<select class="form-control" name="user_group_id2" id="user_group_id2">
										<?php
$sql_grupos   = "select * from tbgrupo";
    $query_grupos = mysqli_query($conexion, $sql_grupos);
    while ($rw_grupos = mysqli_fetch_array($query_grupos)) {
        ?>
											<option value="<?php echo $rw_grupos['idgrupo']; ?>"><?php echo $rw_grupos['grupo']; ?></option>
											<?php
}
    ?>
									</select>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="user_email2" class="control-label">Email:</label>
									<input type="email" class="form-control" id="user_email2" name="user_email2" required>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="sucursal2" class="control-label">Sucursal:</label>
									<select class="form-control" name="sucursal2" id="sucursal2">
									<option value="">-- Selecciona --</option>
										<?php
$sql_sucursal   = "select * from perfil";
    $query_sucursal = mysqli_query($conexion, $sql_sucursal);
    while ($rw_sucursal = mysqli_fetch_array($query_sucursal)) {
        ?>
											<option value="<?php echo $rw_sucursal['id_perfil']; ?>"><?php echo $rw_sucursal['codigoEstablecimiento'].'-'.$rw_sucursal['giro_empresa']; ?></option>
											<?php
}
    ?>
									</select>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label for="sucursal" class="control-label">Tipo Precio:</label>
									<select class="form-control" name="precio2" id="precio2">
									<option value="">-- Selecciona --</option>
									<option value="1">Precio1</option>
									<option value="2">Precio2</option>
									<option value="3">Precio3</option>
									<option value="4">Precio4</option>
									</select>	
								</div>
							</div>
							<?php if($rutas){ ?>
							<div class="col-md-4">
								<div class="form-group">
									<label for="estado" class="control-label">RUTA:</label>
									<select class="form-control" id="mod_ruta2" name="mod_ruta2">
										<option value="">-- Selecciona una Ruta--</option>
										<option value="">Todas</option>
										<?php
											$query_categoria = mysqli_query($conexion, "select * from tbruta order by ruta");
											while ($rw = mysqli_fetch_array($query_categoria)) {
										?>
											<option value="<?php echo $rw['idruta']; ?>"><?php echo $rw['ruta']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div>
								<label for="agregar" class="control-label">AGREGAR RUTA</label>
								<button type="button" class="btn btn-secondary waves-effect waves-light" onclick="agregar_ruta();">Agregar</button>
								</div>
							</div>
							<?php }?>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="tabla" class="control-label">Rutas Asignadas</label>			
									<div class='outer_divrut'></div>
								</div>								
							</div>
						</div>											
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Cerrar</button>
						<button type="submit" class="btn btn-primary waves-effect waves-light" id="actualizar_datos">Actualizar</button>
					</div>
				</form>
			</div>
		</div>
	</div><!-- /.modal -->
	<?php
}
?>