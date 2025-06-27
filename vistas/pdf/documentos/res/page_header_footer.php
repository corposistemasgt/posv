<?php
if ($conexion) {
    ?>
    <table cellspacing="0" style="width: 100%;">
        <tr>

            <td style="width: 25%; color: #444444;">
           
            </td>
            <td style="width: 50%; color: #34495e;font-size:12px;text-align:center">
                <span style="color: #34495e;font-size:20px;font-weight:bold"><?php echo "$bussines_name ";
    echo $anio = date('Y'); ?></span>


            </td>
            <td style="width: 25%;text-align:right">
            <?php echo $title_report; ?>
            </td>

        </tr>
    </table>
    <?php }?>