<?php
if (isset($conexion)) {
	$idsucursal = $_SESSION['idsucursal'];
	$sql2   = "SELECT rutas FROM tbconfiguracion";
    $query2 = mysqli_query($conexion, $sql2);
    $rutas=0;
    while ($row = mysqli_fetch_array($query2)) {
        $rutas          = $row['rutas'];
    }
    ?>
	<div id="nuevoUsers" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title"><i class='fa fa-edit'></i> Nuevo Usuario</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" method="post" id="guardar_usuario" name="guardar_usuario">
						<div id="resultados_ajax"></div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="firstname" class="control-label">Nombres:</label>
									<input type="text" class="form-control UpperCase" id="firstname" name="firstname" required>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="lastname" class="control-label">Apellidos:</label>
									<input type="text" class="form-control UpperCase" id="lastname" name="lastname" required>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="user_name" class="control-label">Usuario:</label>
									<input type="text" class="form-control" id="user_name" name="user_name" pattern="[a-zA-Z0-9]{2,64}" title="Nombre de usuario ( sólo letras y números, 2-64 caracteres)"required>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="user_group_id" class="control-label">Grupo de permisos</label>
									<select class="form-control" name="user_group_id" id="user_group_id">
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
									<label for="user_email" class="control-label">Email:</label>
									<input type="email" class="form-control" id="user_email" name="user_email">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="sucursal" class="control-label">Sucursal:</label>
									<select class="form-control" name="sucursal" id="sucursal">
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
							<div class="col-md-6">
								<div class="form-group">
									<label for="user_password_neww" class="control-label">Clave:</label>
									<input type="password" class="form-control" id="user_password_neww" name="user_password_neww" pattern=".{6,}" title="Contraseña ( min . 6 caracteres)" required>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="user_password_repeatt" class="control-label">Repite Clave:</label>
									<input type="password" class="form-control" id="user_password_repeatt" name="user_password_repeatt" pattern=".{6,}" required>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="sucursal" class="control-label">Tipo Precio:</label>
									<select class="form-control" name="tipo_precio" id="tipo_precio">
									<option value="">-- Selecciona --</option>
									<option value="1">Precio1</option>
									<option value="2">Precio2</option>
									<option value="3">Precio3</option>
									<option value="4">Precio4</option>
									</select>	
								</div>
							</div>
									
						</div>
					
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Cerrar</button>
						<button type="submit" class="btn btn-primary waves-effect waves-light" id="guardar_datos">Guardar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<?php
}
?>