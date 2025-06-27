<?php
session_start();
if (!isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] != 1) {
    header("location: ../../login.php");
    exit;
}
require_once "../db.php";
require_once "../php_conexion.php";
require_once "../funciones.php";
$employee_id    = intval($_REQUEST['employee_id']);
$daterange      = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['range'], ENT_QUOTES)));
$tables         = "tbegresos";
$campos         = "idegreso,fecha,
(select concat(nombre_users,' ',apellido_users) from users where id_users=idusuario) as usuario,
(select concat(nombre_users,' ',apellido_users) from users where id_users=idvendedor) as vendedor,
(select giro_empresa from perfil where id_perfil=idsucursal) as sucursal";
$sWhere         = "1=1 ";
if ($employee_id > 0) {
    $sWhere .= " and id_vendedor = '" . $employee_id . "' ";
}
if (!empty($daterange)) {
    list($f_inicio, $f_final)                    = explode(" - ", $daterange); 
    list($dia_inicio, $mes_inicio, $anio_inicio) = explode("/", $f_inicio); 
    $fecha_inicial                               = "$anio_inicio-$mes_inicio-$dia_inicio 00:00:00"; 
    list($dia_fin, $mes_fin, $anio_fin)          = explode("/", $f_final); 
    $fecha_final                                 = "$anio_fin-$mes_fin-$dia_fin 23:59:59";

    $sWhere .= " and fecha between '$fecha_inicial' and '$fecha_final' ";
}
$sWhere .= " order by idegreso";

$consulta  = "SELECT $campos FROM $tables WHERE $sWhere";
$resultado = $conexion->query($consulta);
if ($resultado->num_rows > 0) {
    date_default_timezone_set('America/Guatemala');
    if (PHP_SAPI == 'cli') {
        die('Este archivo solo se puede ver desde un navegador web');
    }
    require_once 'lib/PHPExcel/PHPExcel.php';
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("Corposistemas")
        ->setLastModifiedBy("Corposistemas")
        ->setTitle("Reporte Excel con PHP y MySQL")
        ->setSubject("Reporte Excel con PHP y MySQL")
        ->setDescription("Reporte de Ventas")
        ->setKeywords("reporte ventas")
        ->setCategory("Reporte excel");
    $tituloReporte   = "Reporte de Ventas";
    $titulosColumnas = array('ID', 'VENDEDOR', 'DESPACHADOR', 'FECHA EGRESO', 'SUCURSAL');
    $objPHPExcel->setActiveSheetIndex(0)
        ->mergeCells('A1:E1');
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', $tituloReporte)
        ->setCellValue('A3', $titulosColumnas[0])
        ->setCellValue('B3', $titulosColumnas[1])
        ->setCellValue('C3', $titulosColumnas[2])
        ->setCellValue('D3', $titulosColumnas[3])
        ->setCellValue('E3', $titulosColumnas[4]);
    $i = 4;
    while ($fila = $resultado->fetch_array()) {
       
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, $fila['idegreso'])
            ->setCellValue('B' . $i, $fila['vendedor'])
            ->setCellValue('C' . $i, $fila['usuario'])
            ->setCellValue('D' . $i, date('d/m/Y', strtotime($fila['fecha'])))
            ->setCellValue('E' . $i, $fila['sucursal']);
            $i++;
    }
    $estiloTituloReporte = array(
        'font'      => array(
            'name'   => 'Verdana',
            'bold'   => true,
            'italic' => false,
            'strike' => false,
            'size'   => 16,
            'color'  => array(
                'rgb' => '1C2833', 
            ),
        ),
        'fill'      => array(
            'type'  => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('argb' => 'D6DBDF'),
        ),
        'borders'   => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_NONE,
            ),
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'rotation'   => 0,
            'wrap'       => true,
        ),
    );
    $estiloTituloColumnas = array(
        'font'      => array(
            'name'   => 'Arial',
            'bold'   => true,
            'italic' => false,
            'strike' => false,
            'size'   => 8,
            'color'  => array(
                'rgb' => '1C2833', 
            ),
        ),
        'fill'      => array(
            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
            'rotation'   => 90,
            'startcolor' => array(
                'rgb' => 'D6DBDF', 
            ),
            'endcolor'   => array(
                'argb' => 'D6DBDF', 
            ),
        ),
        'borders'   => array(
            'top'    => array(
                'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                'color' => array(
                    'rgb' => '143860',
                ),
            ),
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                'color' => array(
                    'rgb' => '143860',
                ),
            ),
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'wrap'       => true,
        ));
    $estiloInformacion = new PHPExcel_Style();
    $estiloInformacion->applyFromArray(
        array(
            'font'    => array(
                'name'  => 'Arial',
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'fill'    => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFd9b7f4'),
            ),
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '3a2a47',
                    ),
                ),
            ),
        ));
    $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($estiloTituloReporte);
    $objPHPExcel->getActiveSheet()->getStyle('A3:F3')->applyFromArray($estiloTituloColumnas);
    $objPHPExcel->getActiveSheet()->getStyle('F4:F' . ($i - 1))->getNumberFormat()->setFormatCode('#,##0.00'); //FORMATO NUMERICO
    for ($i = 'A'; $i <= 'F'; $i++) {
        $objPHPExcel->setActiveSheetIndex(0)
            ->getColumnDimension($i)->setAutoSize(true);
    }
    $objPHPExcel->getActiveSheet()->setTitle('Ventas');
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0, 4);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Reporteventas.xlsx"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
    exit;
} else {
    echo "<script>alert('No hay resultados para mostrar')</script>";
    echo "<script>window.close();</script>";
    header("Location:../html/rep_ventas.php");
    exit;
}
