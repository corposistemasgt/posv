<?php
if (isset($conexion)) { 

    $genericos     =$_SESSION['genericos'];
    $vencimientos  =$_SESSION['vencimientos'];
	$medidas       =$_SESSION['medidas'];
	$casas		 =$_SESSION['casas'];
	?>
	<div id="editarProducto" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title"><i class='fa fa-edit'></i> Editar Productos</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" method="post" id="editar_producto" name="editar_producto">
						<div id="resultados_ajax2"></div>

						<ul class="nav nav-tabs">
							<li class="nav-item">
								<a href="#mod_info" data-toggle="tab" aria-expanded="false" class="nav-link active">
									Datos Básicos
								</a>
							</li>
							<li class="nav-item">
								<a href="#mod_precios" data-toggle="tab" aria-expanded="true" class="nav-link">
									Precios y Stock
								</a>
							</li>
							<li class="nav-item">
								<a href="#img2" data-toggle="tab" aria-expanded="true" class="nav-link">
									Imagen
								</a>
							</li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane fade show active" id="mod_info">

								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label for="mod_codigo" class="control-label">Código:</label>
											<input type="text" class="form-control" id="mod_codigo" name="mod_codigo"  autocomplete="off" required>
											<input id="mod_id" name="mod_id" type='hidden'>
										</div>
									</div>
									<div class="col-md-8">
										<div class="form-group">
											<label for="mod_nombre" class="control-label">Nombre:</label>
											<input type="text" class="form-control" id="mod_nombre" name="mod_nombre" autocomplete="off" required>
											<input type="text" hidden id="mod_sucursal" name="mod_sucursal">
										</div>
									</div>
									<?php 
									if($genericos==1)
									{?>
									<div class="col-md-4">
										<div class="form-group">
											<label for="descripcion" class="control-label">Es Genérico:</label>
											<select class="form-control" id="mod_generico" name="mod_generico">
												<option value="1">Si</option>
												<option value="0">No</option>
											</select>	
										</div>
									</div>
									<?php
									}
									?>
									<?php
									if($medidas==1)
									{?>
									<div class="col-md-4">
										<div class="form-group">
											<label for="mod_descripcion" hidden class="control-label">Bien o Servicio</label>
											<select class="form-control" id="mod_bien" name="mod_bien">
												<option value="B">Bien</option>
												<option value="S">Servicio</option>
											</select>	
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="mod_descripcion" class="control-label">Medida</label>
											<input type="text" class="form-control" maxlength="3" id="mod_medida" name="mod_medida" autocomplete="off" required>
										</div>
									</div>
									<?php
									}
									?>
										
									<div class="col-md-12">
										<div class="form-group">
											<label for="mod_descripcion" class="control-label">Descripción</label>
											<textarea class="form-control"  id="mod_descripcion" name="mod_descripcion" maxlength="255"  autocomplete="off"></textarea>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-4	">
										<div class="form-group">
											<label for="mod_linea" class="control-label">Linea:</label>
											<select class='form-control' name='mod_linea' id='mod_linea' required>
												<option value="">-- Selecciona --</option>
												<?php
    												$query_categoria = mysqli_query($conexion, "select * from lineas order by nombre_linea");
    												while ($rw = mysqli_fetch_array($query_categoria)) {
        										?>
													<option value="<?php echo $rw['id_linea']; ?>"><?php echo $rw['nombre_linea']; ?></option>
												<?php
													}
    											?>
											</select>
										</div>
									</div>
									<?php 
										if($casas==1)
										{
											?>
									<div class="col-md-4">
										<div class="form-group">
											<label for="mod_casa" class="control-label">Casa</label>
											<select class='form-control' name='mod_casa' id='mod_casa' required>
												<option value="">-- Selecciona --</option>
												<?php
    												$query_proveedor = mysqli_query($conexion, "select idcasa,casa from tbcasa order by casa");
    												while ($rw = mysqli_fetch_array($query_proveedor)) {
        										?>
													<option value="<?php echo $rw['idcasa']; ?>"><?php echo $rw['casa']; ?></option>
													<?php
												}
    											?>
											</select>
										</div>
									</div>
									<?php
										}	
									?>
									<div class="col-md-4">
										<div class="form-group">
											<label for="mod_proveedor" class="control-label">Proveedor:</label>
											<select class='form-control' name='mod_proveedor' id='mod_proveedor' required>
												<option value="0">-- Selecciona --</option>
												<?php

    $query_proveedor = mysqli_query($conexion, "select * from proveedores order by nombre_proveedor");
    while ($rw = mysqli_fetch_array($query_proveedor)) {
        ?>
													<option value="<?php echo $rw['id_proveedor']; ?>"><?php echo $rw['nombre_proveedor']; ?></option>
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
											<label for="mod_estado" class="control-label">Estado:</label>
											<select class="form-control" id="mod_estado" name="mod_estado" required>
												<option value="">-- Selecciona --</option>
												<option value="1" selected>Activo</option>
												<option value="0">Inactivo</option>
											</select>
										</div>
									</div>
								</div>

							</div>
							<div class="tab-pane fade" id="mod_precios">

								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="mod_costo" class="control-label">Ultimo Costo:</label>
											<input type="text" class="form-control" id="mod_costo" name="mod_costo" autocomplete="off" pattern="^[0-9]{1,5}(\.[0-9]{0,2})?$" title="Ingresa sólo números con 0 ó 2 decimales" maxlength="8" required>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="mod_utilidad" class="control-label">Utilidad %:</label>
											<input type="text" class="form-control" id="mod_utilidad" name="mod_utilidad" pattern="\d{1,4}"  maxlength="4" onkeyup="precio_venta_edit();" >
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-3">
										<div class="form-group">
											<label for="mod_precio" class="control-label">Precio Publico:</label>
											<input type="text" class="form-control" id="mod_precio" name="mod_precio" autocomplete="off" pattern="^[0-9]{1,5}(\.[0-9]{0,2})?$" title="Ingresa sólo números con 0 ó 2 decimales" maxlength="8">
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label for="mod_preciom" class="control-label">Precio Promotor:</label>
											<input type="text" class="form-control" id="mod_preciom" name="mod_preciom" autocomplete="off" pattern="^[0-9]{1,5}(\.[0-9]{0,2})?$" title="Ingresa sólo números con 0 ó 2 decimales" maxlength="8">
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label for="mod_precioe" class="control-label">Precio Mayorista:</label>
											<input type="text" class="form-control" id="mod_precioe" name="mod_precioe" autocomplete="off" pattern="^[0-9]{1,5}(\.[0-9]{0,2})?$" title="Ingresa sólo números con 0 ó 2 decimales" maxlength="8">
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label for="mod_precioc" class="control-label">Precio Especial:</label>
											<input type="text" class="form-control" id="mod_precioc" name="mod_precioc" autocomplete="off" pattern="^[0-9]{1,5}(\.[0-9]{0,2})?$" title="Ingresa sólo números con 0 ó 2 decimales" maxlength="8">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label for="mod_inv" class="control-label">Maneja Inventario:</label>
											<select class="form-control" id="mod_inv" name="mod_inv" required>
												<option value="">- Selecciona -</option>
												<option value="0">Si</option>
												<option value="1">No</option>
											</select>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="mod_stock" class="control-label">Stock Inicial:</label>
											<input type="text" class="form-control" id="mod_stock" name="mod_stock" autocomplete="off" pattern="^[0-9]{1,5}(\.[0-9]{0,2})?$" title="Ingresa sólo números con 0 ó 2 decimales" maxlength="8" readonly="true">
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="mod_minimo" class="control-label">Stock Minimo:</label>
											<input type="text" class="form-control" id="mod_minimo" name="mod_minimo" autocomplete="off" pattern="^[0-9]{1,5}(\.[0-9]{0,2})?$" title="Ingresa sólo números con 0 ó 2 decimales" maxlength="8">
										</div>
									</div>

								</div>
								<?php 
									if($vencimientos==1)
									{?>
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label for="example-date-input" class="col-md-3 col-form-label">Fecha Vencimiento</label>
											<div class="col-md-12">					<!--2011-08-19-->
											<input id="mod_fecha" name="mod_fecha"class="form-control" type="date"  >
											</div>
										</div>
									</div>	
								</div>
								<?php
									}
									?>

							</div>
							<div class="tab-pane fade" id="img2">

								<div class="outer_img"></div>


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