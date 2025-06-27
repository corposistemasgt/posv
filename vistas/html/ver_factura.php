<?php
require_once "../security.php"; 
session_start();
if ( strpos(get_url(), $_SESSION['ruta']) === false||isset($_SESSION['ruta']) == false) {
    header("location: ../../login.php?logout");
    exit;
}
if (!isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] != 1) {
    header("location: ../../login.php");
    exit;
}
require_once "../db.php"; 
require_once "../php_conexion.php"; 
require 'includes/header_start.php';
require 'includes/header_end.php';?>
<div id="wrapper">
	<?php require 'includes/menu.php';?>
	<div class="content-page">
		<div class="content">
			<div class="container">
					<div class="col-lg-12">
						<div class="portlet">
							<div class="portlet-heading bg-secondtabla">
								<h3 class="portlet-title">
									Detalles de Factura # <?php echo $_GET['numero']?>
								</h3>
									
								<div class="clearfix"></div>
							</div>
							<div id="bg-primary" class="panel-collapse collapse show">
								<div class="portlet-body">
								<?php
									$query_categoria = mysqli_query($conexion, "select factura_nombre_cliente,factura_nit_cliente, serie_factura, 
									guid_factura,numero_certificacion, fechaCertificacion,monto_factura from facturas_ventas where id_factura =".$_GET['id']);
									while ($rw = mysqli_fetch_array($query_categoria)) 
									{
										$nombre        = $rw['factura_nombre_cliente'];
										$nit           = $rw['factura_nit_cliente'];
										$serie         = $rw['serie_factura'];
										$numero        = $rw['numero_certificacion'];    
										$autorizacion  = $rw['guid_factura'];
										$fecha         = $rw['fechaCertificacion'];
										$monto         = $rw['monto_factura']; 
									}
		  							?>
                    <div class="row">
						<div class="col-md-9">
                        	<label for="field-2" class="control-label">Nombre Cliente:</label>
						    <label for="field-2" class="control-label"><?php echo $nombre;?></label>                   
                        </div>
						<div class="col-md-3">                   
                        	<label for="field-2" class="control-label">Nit:</label>
							<label for="field-2" class="control-label"><?php echo $nit;?></label>                           
                        </div>
					</div>
					<?php 
						if(strcmp($autorizacion,'')!=0)
						{
						?>

						<div class="row">
                       
						
						<div class="col-md-2">
                        	<label for="field-2" class="control-label">Serie:</label>
						    <label for="field-2" class="control-label"><?php echo $serie;?></label>                   
                        </div>
						<div class="col-md-2">                   
                        	<label for="field-2" class="control-label">Numero:</label>
							<label for="field-2" class="control-label"><?php echo $numero;?></label>                           
                        </div>
						<div class="col-md-5">                   
                        	<label for="field-2" class="control-label">Autorizacion:</label>
							<label for="field-2" class="control-label"><?php echo $autorizacion;?></label>                           
                        </div>
						<div class="col-md-3">                   
                        	<label for="field-2" class="control-label">Fecha:</label>
							<label for="field-2" class="control-label"><?php $date = strtotime($fecha); echo date('d/m/Y h:i:s', $date);?></label>                           
                        </div>
                    </div>
					<?php	
					}
					?>		
		<form class="form-horizontal" method="post" id="guardar_gasto" name="guardar_gasto">			
            <div class="table-responsive" style="padding-top: 20px; padding-right: 20px; padding-left: 20px;">
                <table class="table table-sm table-striped" >
                    <tr  class="info">
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Descuento</th>
                        <th>Precio Venta</th>
                    </tr>
                    <?php
                    $query_categoria = mysqli_query($conexion, "select nombre_producto,cantidad,desc_venta,precio_venta from detalle_fact_ventas,productos 
                    where detalle_fact_ventas.id_producto =productos.id_producto and id_factura =".$_GET['id']);
					while ($rw = mysqli_fetch_array($query_categoria)) 
					{
                            $nombre     = $rw['nombre_producto'];
                            $cantidad   = $rw['cantidad'];
                            $descuento   = $rw['desc_venta'];
                            $precio     = $rw['precio_venta'];                        
                            $simbolo_moneda = "Q. ";
                        ?>
                    <tr>
                        <td><?php echo $nombre ?></td>
                        <td><?php echo $cantidad; ?></td>
                        <td><?php echo $simbolo_moneda . '' . number_format($descuento, 2); ?></td>
                        <td><?php echo $simbolo_moneda . '' . number_format($precio, 2); ?></td>                    
                    </tr>
                    <?php
                        }
                    ?>
                </table>
        </form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
		</div>
	</div>
	<?php require 'includes/pie.php';?>
</div>
</div>
<?php require 'includes/footer_start.php'
?>
<?php require 'includes/footer_end.php'
?>

