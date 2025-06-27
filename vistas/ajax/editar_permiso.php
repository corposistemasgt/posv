<?php
include 'is_logged.php'; 
require_once "../db.php"; 
require_once "../php_conexion.php"; 
$id = base64_decode($_POST["user_group_id"]);
if (empty($_POST["nombres"])) {
    $errors[] = "Nombres vacío";
} else if ($id == 1) {
    $errors[] = "No se pueden editar los permisos del grupo de usuario super administrador.";
} elseif (!empty($_POST['nombres'])) {

    $idgrupo = intval($id);

    $numero2 = count($_POST);
    $tags2 = array_keys($_POST);
    $valores2 = array_values($_POST);

  //  $query = mysqli_query($conexion, "SELECT max(idpermiso) as idpermiso FROM tbpermiso");
    //while ($row = mysqli_fetch_array($query)) 
      //  {
            //$idpermiso   = $row['idpermiso'];
        //}
        //for($i=1;$i<=$idpermiso;$i++)
        //{ 
            $sql = "update tbasignacionpermiso set valor=0 where idgrupo='$idgrupo';";
            $query = mysqli_query($conexion, $sql);
        //}
 
    for($i=0;$i<$numero2;$i++)
    { 
        $$tags2[$i]=$valores2[$i];  
        if(strpos($tags2[$i],"permi_")!== false)
        {
            $id=trim($tags2[$i],"permi_");
            $sql = "update tbasignacionpermiso set valor=1 where idpermiso='$id' and idgrupo='$idgrupo';";
            $query = mysqli_query($conexion, $sql);
          
        }
        else
        {
            $id=trim($tags2[$i],"permi_");
            echo "aa" . $id;
        }
    }
    $nombres    = mysqli_real_escape_string($conexion, (strip_tags($_POST['nombres'], ENT_QUOTES)));
    $sql = "UPDATE tbgrupo SET grupo='" . $nombres . "'  WHERE idgrupo='$idgrupo';";
    $query_update = mysqli_query($conexion, $sql);
    $messages[] = "Cargo ha sido actualizado satisfactoriamente.";

} else {
    $errors[] = "Error desconocido.";
}

if (isset($errors)) {

    ?>
    <div class="alert alert-danger" role="alert">
        <strong>Error!</strong>
        <?php
foreach ($errors as $error) {
        echo $error;
    }
    ?>
    </div>
    <?php
}
if (isset($messages)) {

    ?>
    <div class="alert alert-success" role="alert">
        <strong>¡Bien hecho!</strong>
        <?php
foreach ($messages as $message) {
        echo $message;
    }
    ?>
    </div>
    <?php
}

?>