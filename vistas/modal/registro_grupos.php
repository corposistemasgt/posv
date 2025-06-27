<?php
if (isset($conexion)) {
    ?>
	<div id="nuevoGrupo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog">
			<div class="modal-content" style="width: 140%; position: relative; left: -120px;">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title"><i class='fa fa-edit'></i> Nuevo Grupo de Usuarios</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" method="post" id="guardar_permisos" name="guardar_permisos">
						<div id="resultados_ajax"></div>

						<div class="form-group  ">
							<label for="nombres" class="col-sm-3 control-label">Nombre:</label>
							<div class="col-sm-12">
								<input type="text" class="form-control" id="nombres" name="nombres" placeholder="Nombre del Grupo" required autocomplete="off">
							</div>
						</div>
						<div class="table-responsive">
						<table class="table table-sm table-hover">
							<thead>
								<tr>
									<th> Módulo</th>
									<th> </th>
									<th> </th>
									<th> </th>
									<th> </th>
									<th> </th>
								</tr>
							</thead>
							<tbody>
								<?php
									$sql   = "select * from modulos";
									$query = mysqli_query($conexion, $sql);
    								$num   = 1;
    								while ($row = mysqli_fetch_array($query)) 
									{
										$idm = $row["id_modulo"];
        								$modulo = $row["nombre_modulo"];
        							?>
									<tr>
									<td><p><?php echo $modulo; ?></p></td>
										<?php
										$sql2   = "select * from tbpermiso where idmodulo='$idm'";
										$query2 = mysqli_query($conexion, $sql2);
										$val=5;
										while ($row1 = mysqli_fetch_array($query2)) 
										{
											$idper= $row1["idpermiso"];
											$permiso= $row1["nombre"];
											$val--;
											?>
											
											<td> <p><?php echo $permiso; ?></p><input  type ='checkbox' name='perm_<?php echo $idper; ?>'  value='1'  class='ck'></td>
											
											<?php
										}
										for($i=0; $i<$val; $i++)
										{
											?>
											<td> </td>
											<?php
										}
									?>
										
									</tr>
									<?php
									$num++;
   	 								}
    							?>
							</tbody>
						</table>
						</div>



					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Cerrar</button>
						<button type="submit" class="btn btn-primary waves-effect waves-light" id="guardar_datos">Guardar</button>
					</div>
				</form>
			</div>
		</div>
	</div><!-- /.modal -->
	<?php
}
?>