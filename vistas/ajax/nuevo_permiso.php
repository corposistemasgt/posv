<?php
    include 'is_logged.php'; 
    require_once "../db.php";
    require_once "../php_conexion.php";
    if (empty($_POST["nombres"])) 
    {
        $errors[] = "Nombres vacío";
    } 
    elseif (!empty($_POST['nombres'])) 
    {
        $numero2 = count($_POST);
        $tags2 = array_keys($_POST);
        $valores2 = array_values($_POST);
        $nombres    = mysqli_real_escape_string($conexion, (strip_tags($_POST['nombres'], ENT_QUOTES)));
        $sql = "INSERT INTO tbgrupo (grupo,estado)  VALUES ('$nombres', '1');";
        $query_new_user_insert = mysqli_query($conexion, $sql);
        $query = mysqli_query($conexion, "SELECT max(idgrupo) as idgrupo FROM tbgrupo");
        while ($row = mysqli_fetch_array($query)) 
        {
            $idgrupo    = $row['idgrupo'];
        }
        $query = mysqli_query($conexion, "SELECT max(idpermiso) as idpermiso FROM tbpermiso");
        while ($row = mysqli_fetch_array($query)) 
        {
            $idpermiso   = $row['idpermiso'];
        }
        for($i=1;$i<=$idpermiso;$i++)
        { 
            $sql = "INSERT into tbasignacionpermiso(idpermiso, idgrupo,valor) values('$i','$idgrupo','0');";
            $query = mysqli_query($conexion, $sql);
        }
        for($i=0;$i<$numero2;$i++)
        { 
            $$tags2[$i]=$valores2[$i];  
            if(strpos($tags2[$i],"perm_")!== false)
            {
                $id=trim($tags2[$i],"perm_");
                $sql = "update tbasignacionpermiso set valor=1 where idpermiso='$id' and idgrupo='$idgrupo';";
                $query = mysqli_query($conexion, $sql);
            }
        }
        $messages[] = "Datos han sido registrados satisfactoriamente.";
    }

if (isset($errors)) {
    ?>
        <div class="alert alert-error">
            <strong>Error! </strong>
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
        <div class="alert alert-success">
            <strong>Aviso! </strong>
    <?php
foreach ($messages as $message) {
        echo $message;
    }
    ?>
        </div>
    <?php
}

?>