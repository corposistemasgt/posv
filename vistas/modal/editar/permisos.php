<?php
session_start();
if (!isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] != 1) {
    header("location: ../../login.php");
    exit; 
}
require_once "../../db.php";
require_once "../../php_conexion.php";
if (isset($_GET["id"])) {
    $id    = $_GET["id"];
    $id    = intval($id);
    $sql   = "select * from tbgrupo where idgrupo='$id'";
    $query = mysqli_query($conexion, $sql);
    $num   = mysqli_num_rows($query);
    if ($num == 1) { 
        $rw       = mysqli_fetch_array($query);
        $name     = $rw['grupo'];
    }
} else {echo "<script>location.replace('../../permisos.php')</script>";}
?>
      <div class="form-group  ">
		<label for="nombres" class="col-sm-3 control-label">Nombre</label>
		<div class="col-sm-8">
		  <input type="text" class="form-control " id="nombres" name="nombres" value="<?php echo $name; ?>" required>
		  <input type="hidden" id="user_group_id" name="user_group_id" value="<?php echo base64_encode($id) ?>">
		</div>
	  </div>
	  <table class="table table-sm table-hover table-nomargin">
		<thead>
		<tr>
			<th >Módulo</th>
			<th ><p></p></th>
			<th ><p></p></th>
			<th ><p></p></th>
			<th ><p></p></th>
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
				$sql2   = "select * from tbasignacionpermiso,tbpermiso where tbasignacionpermiso.idpermiso=tbpermiso.idpermiso and idgrupo ='$id' and idmodulo='$idm'";
				$query2 = mysqli_query($conexion, $sql2);
				$val=5;
				while ($row1 = mysqli_fetch_array($query2)) 
				{
					$idper= $row1["idpermiso"];
					$permiso= $row1["nombre"];
					$estado= $row1["valor"];
					$esta="";
					if($estado==1)
					{
						$esta="checked";
					}
					$val--;
				?>
					<td> <p><?php echo $permiso; ?></p><input  type ='checkbox' name='permi_<?php echo $idper; ?>'  value='1' <?php echo $esta; ?> chec class='ck'></td>
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