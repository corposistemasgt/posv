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
require_once "../funciones.php";
$permisos_ver =getpermiso(45);
$query_empresa = mysqli_query($conexion, "select * from perfil where id_perfil=".$_SESSION['idsucursal'] );
while($row1           = mysqli_fetch_array($query_empresa)){
	$comercial=$row1['nombre_empresa'];
	$emisor=$row1['giro_empresa'];
	$telefono=$row1['telefono'];
	$correo=$row1['email'];
	$direccion=$row1['direccion'];
	$municipio=$row1['ciudad'];
	$departamento=$row1['estado'];
}

require 'includes/header_start.php';
require 'includes/header_end.php';?>
<div id="wrapper">
	<?php require 'includes/menu.php';?>
	<div class="content-page">
		<div class="content">
			<div class="container">
<?php if ($permisos_ver == 1) {
    ?>
				<div class="col-lg-12">
					<div class="portlet">
						<div class="portlet-heading bg-secondtabla">
							<h3 class="portlet-title">
								Datos de Empresa
							</h3>
							<div class="clearfix"></div>
						</div>
						<div id="bg-primary" class="panel-collapse collapse show">
							<div class="portlet-body">

							<form class="form-horizontal" role="form" id="perfil">
								<div class="row">
									<div class="col-md-3">
										<div align="center">
											<img src="<?php echo $row1['logo_url']; ?>" class="img-responsive" alt="profile-image" width="200px" height="200px">
										</div>
										<div class="form-group">
											<input class="form-control" data-buttonText="Logo" type="file" name="imagefile" id="imagefile" onchange="upload_image();">
										</div>
										<div>
											<?php
											if($_SESSION['sunmi']==1){
												?>
										<button  class="btn btn-info waves-effect waves-light" onclick='abrir()'><i class="fa fa-cog"></i> Configurar Sunmi</button>
												<?php } 
												?>
									</div>
									</div>
									<div class="col-md-9">
										<div class="card-box">
												<input type="hidden" class="form-control" name="id_perfil" value="<?php echo $row1['id_perfil'] ?>" required autocomplete="off">
												<div class="form-group row">
													<label for="inputPassword3" class="col-sm-3  col-form-label">Nombre Comercial:</label>
													<div class="col-sm-9">
														<input type="text" class="form-control UpperCase" name="nombre_empresa" value="<?php echo $comercial; ?>" required autocomplete="off">
													</div>
												</div>
												<div class="form-group row">
													<label for="giro" class="col-sm-3  col-form-label">Emisor:</label>
													<div class="col-sm-9">
														<input type="text" class="form-control UpperCase" name="giro" value="<?php echo $emisor; ?>" required autocomplete="off">
													</div>
												</div>
												<div class="form-group row">
													<label for="fiscal" class="col-sm-3 col-form-label"> Nit:</label>
													<div class="col-sm-6">
														<input type="text" class="form-control" required name="fiscal" value="<?php echo $_SESSION['nit']; ?>" autocomplete="off" >
													</div>
												</div>
												<div class="form-group row">
													<label for="inputPassword3" class="col-sm-3 col-form-label">Teléfono:</label>
													<div class="col-sm-6">
														<input type="text" class="form-control" name="telefono" value="<?php echo $telefono; ?>" required autocomplete="off">
													</div>
												</div>
												<div class="form-group row">
													<label for="inputEmail3" class="col-sm-3 col-form-label">Correo:</label>
													<div class="col-sm-9">
														<input type="email" class="form-control" name="email" value="<?php echo $correo; ?>" autocomplete="off" >
													</div>
												</div>
										
												<div class="form-group row">
													<label for="inputPassword3" class="col-sm-3 col-form-label">Dirección:</label>
													<div class="col-sm-9">
														<input type="text" class="form-control UpperCase" name="direccion" value="<?php echo $direccion; ?>" required autocomplete="off" >
													</div>
												</div>
												<div class="form-group row">
													<label for="inputPassword3" class="col-sm-3 col-form-label">Ciudad:</label>
													<div class="col-sm-9">
														<input type="text" class="form-control UpperCase" name="ciudad" value="<?php echo $municipio; ?>">
													</div>
												</div>
												<div class="form-group row">
													<label for="inputPassword3" class="col-sm-3 col-form-label">Departamento:</label>
													<div class="col-sm-9">
														<input type="text" class="form-control UpperCase" name="estado" value="<?php echo $departamento; ?>">
													</div>
												</div>											
												<div class="form-group m-b-0 row">
													<div class="offset-3 col-sm-9">
															</div>
												</div>				
												<div class='col-md-12' id="resultados_ajax"></div>
											</form>
									</div>
								</div>
							</div>
						</div>
						<?php
} else {
    ?>
		<section class="content">
			<div class="alert alert-danger" align="center">
				<h3>Acceso denegado! </h3>
				<p>No cuentas con los permisos necesario para acceder a este módulo.</p>
			</div>
		</section>
		<?php
}
?>
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
<script>
  function upload_image(){
    var inputFileImage = document.getElementById("imagefile");
    var file = inputFileImage.files[0];
    if( (typeof file === "object") && (file !== null) )
    {
      $("#load_img").html('<img src="../../img/ajax-loader.gif"> Cargando...');
      var data = new FormData();
      data.append('imagefile',file);
      $.ajax({
            url: "../ajax/imagen_ajax.php",       
            type: "POST",            
            data: data,         
            contentType: false,       
            cache: false,             
            processData:false,        
            success: function(data) 
            {
              $("#load_img").html(data);
            }
          });
    }
  }
</script>
<script>
  function abrir(){
    console.log("corpoprint-confi");
  }
</script>
<script>
       $(document).ready( function () {
        $(".UpperCase").on("keypress", function () {
         $input=$(this);
         setTimeout(function () {
          $input.val($input.val().toUpperCase());
         },50);
        })
       })
      </script>
	<?php require 'includes/footer_end.php'
?>