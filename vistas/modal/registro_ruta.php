<?php
if (isset($conexion)) {
    ?>
	<div id="nuevaRuta" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title"><i class='fa fa-edit'></i> Nueva Ruta</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" method="post" id="guardar_cliente" name="guardar_cliente">
						<div id="resultados_ajax"></div>
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="nombre" class="control-label">Ruta:</label>
									<input type="text" class="form-control" id="nombre" name="nombre" autocomplete="off" required>
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