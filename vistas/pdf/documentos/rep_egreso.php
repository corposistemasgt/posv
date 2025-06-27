<?php
session_start();
require_once "../../db.php";
require_once "../../php_conexion.php";
require_once "../../funciones.php";
$idusuario    = intval($_REQUEST['employee_id']);
$daterange      = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['range'], ENT_QUOTES)));
$tables         = "tbegresos";
$campos         = "idegreso,fecha,
(select concat(nombre_users,' ',apellido_users) from users where id_users=idusuario) as usuario,
(select concat(nombre_users,' ',apellido_users) from users where id_users=idvendedor) as vendedor,
(select giro_empresa from perfil where id_perfil=idsucursal) as sucursal";
$sWhere         = " 1=1 ";
if (!empty($daterange)) {
    list($f_inicio, $f_final)                    = explode(" - ", $daterange); //Extrae la fecha inicial y la fecha final en formato espa?ol
    list($dia_inicio, $mes_inicio, $anio_inicio) = explode("/", $f_inicio); //Extrae fecha inicial
    $fecha_inicial                               = "$anio_inicio-$mes_inicio-$dia_inicio 00:00:00"; //Fecha inicial formato ingles
    list($dia_fin, $mes_fin, $anio_fin)          = explode("/", $f_final); //Extrae la fecha final
    $fecha_final                                 = "$anio_fin-$mes_fin-$dia_fin 23:59:59";
    $sWhere .= " and fecha between '$fecha_inicial' and '$fecha_final' ";
}
if (!empty($idusuario)) {
    $sWhere .= " and idvendedor = '" . $idusuario . "' ";
}
$sWhere .= " order by idegreso";
$query = mysqli_query($conexion, "SELECT $campos FROM  $tables where $sWhere ");
echo "SELECT $campos FROM  $tables where $sWhere ";
ob_start();
include dirname(__FILE__) . '/res/rep_egresos_html.php';
$content = ob_get_clean();
require_once dirname(__FILE__) . '/../html2pdf.class.php';
try
{
    $html2pdf = new HTML2PDF('P', 'A4', 'es', true, 'UTF-8', 3);
    $html2pdf->pdf->SetDisplayMode('fullpage');
    $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
    ob_end_clean();
    $html2pdf->Output('usuarios.pdf');
} catch (HTML2PDF_exception $e) {
    echo $e;
    exit;
}
