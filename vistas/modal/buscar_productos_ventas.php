<?php
if (isset($conexion)) {
    ?>
	<div id="buscar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title"><i class='fa fa-search'></i> Buscar Productos</h4>
				</div>
				<div class="modal-body">

					<form class="form-horizontal">
						<div class="form-group row">

							<div class="col-md-5">
								<div class="input-group">
									<input type="text" autocomplete="off" class="form-control" id="q" placeholder="Buscar por Codigo/nombre" onkeyup="load(1)">
									
									
									<span class="input-group-btn">
										<button type="submit" class="btn btn-primary waves-effect waves-light" onclick="load(1)"><span class="fa fa-search"></span></button>
									</span>
								</div>
							</div>
							<div class="col-md-5">
												<div class="input-group">
													<select name='categoria' id='categoria' class="form-control" onchange="load(1);">
														<option value="">--Categorias--</option>
														<option value="">Todos</option>
														<?php

																$query_categoria = mysqli_query($conexion, "select * from lineas order by nombre_linea");
																while ($rw = mysqli_fetch_array($query_categoria)) {
														?>
																		<option value="<?php echo $rw['id_linea']; ?>"><?php echo $rw['nombre_linea']; ?></option>
																		<?php
																}
  																		?>
													</select>
													<span class="input-group-btn">
														<button class="btn btn-outline-info btn-rounded waves-effect waves-light" type="button" onclick='load(1);'><i class='fa fa-search'></i></button>
													</span>
												</div>
											</div>
							<div class="col-md-2">
								<div id="loader"></div><!-- Carga gif animado -->
							</div>
						</div>
					</form>
					<div class="outer_div" ></div><!-- Datos ajax Final -->

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</div><!-- /.modal -->
	<?php
}
?>