<?php
if ($conexion) {
    $idsucursal   = $_SESSION['idsk'];
    $sql2           = mysqli_query($conexion, "SELECT * FROM perfil where codigoEstablecimiento = $idsucursal");
    $row            = mysqli_fetch_array($sql2);
    $moneda        = $row["moneda"];
    $bussines_name = $row["nombre_empresa"];
    $address       = $row["direccion"];
    $city          = $row["ciudad"];
    $state         = $row["estado"];
    $postal_code   = $row["codigo_postal"];
    $phone         = $row["telefono"];
    $email         = $row["email"];
    ?>
    <table cellspacing="0" style="width: 100%;"  border="0">
        <tr>
            <td style="width: 18%;">
            </td>
              <td style="width: 12%;"></td>
            <td style="width: 45%; font-size:12px;text-align:center">
                <span style="font-size:14px;font-weight:bold"><?php echo $bussines_name; ?></span>
                <br><?php echo $address . ', ' . $city . ', ' . $state; ?><br>
                Teléfono: <?php echo $phone; ?><br>
                Email: <?php echo $email; ?>
            </td>
            <td style="width: 30%;text-align:right; color:#ff0000">
            </td>
        </tr>
    </table>
    <?php }?>