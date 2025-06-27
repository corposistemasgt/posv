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
	<div id="nuevoCliente" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title"><i class='fa fa-edit'></i> Nuevo Cliente</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" method="post" id="guardar_cliente" name="guardar_cliente">
						<div id="resultados_ajax"></div>
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="nombre" class="control-label">Nombre:</label>
									<input type="text" class="form-control" id="nombre" name="nombre" autocomplete="off" required>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="fiscal" class="control-label"> Nit/Código:</label>
									<input type="text" class="form-control" id="fiscal" name="fiscal" autocomplete="off">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="telefono" class="control-label">Telefono:</label>
									<input type="text" class="form-control" id="telefono" name="telefono" autocomplete="off">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="direccion" class="control-label">Dirección:</label>
									<textarea class="form-control"  id="direccion" name="direccion" maxlength="255" autocomplete="off"></textarea>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-8">
								<div class="form-group">
									<label for="encargado" class="control-label">Email:</label>
									<input type="email" class="form-control" id="email" name="email" autocomplete="off">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="estado" class="control-label">Estado:</label>
									<select class="form-control" id="estado" name="estado" required>
										<option value="">-- Selecciona --</option>
										<option value="1" selected>Activo</option>
										<option value="0">Inactivo</option>
									</select>
								</div>
							</div>
						</div>
					
						<?php if($rutas){?>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label for="estado" class="control-label">Activar Credito?:</label>
									<select class="form-control" id="credito" name="credito" required>
										<option value="">-- Selecciona --</option>
										<option value="1" selected>Permitir</option>
										<option value="0">Denegar</option>
									</select>
								</div>
							
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="estado" class="control-label">Limite de Credito:</label>
									<input type="text" class="form-control" id="limite_credito" name="limite_credito" autocomplete="off">
								</div>
							
							</div>
							<div class="col-md-4">
							<div class="form-group">
									<label for="estado" class="control-label">RUTA:</label>
									<select class="form-control" id="ruta" name="ruta" required>
										<option value="">-- Selecciona una Ruta--</option>
										<option value="">Todas</option>
														<?php
																$query_categoria = mysqli_query($conexion, "select * from tbruta order by ruta");
																while ($rw = mysqli_fetch_array($query_categoria)) {
														?>
																		<option value="<?php echo $rw['idruta']; ?>"><?php echo $rw['ruta']; ?></option>
																		<?php
																}
  																		?>
									</select>
								</div>
							</div>
						</div>
						<?php }?>
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