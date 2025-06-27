<?php
session_start();
if (!isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] != 1) {
    header("location: ../../login.php");
    exit;
}
require_once "../db.php"; 
require_once "../php_conexion.php"; 
require_once "../funciones.php";
$user_id   = $_SESSION['id_users'];
$sqlUsuarioACT        = mysqli_query($conexion, "select * from users left join perfil on users.sucursal_users = perfil.id_perfil where id_users = '".$user_id."'");
$row             = mysqli_fetch_array($sqlUsuarioACT);
$nombre_sucursal         = $row['giro_empresa'];
$user_sucursal = $row['id_perfil'];
$meses = intval(mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['meses'], ENT_QUOTES))));
$id_categoria = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['id_categoria'], ENT_QUOTES)));
$id_sucursal =  mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['sucursalid'], ENT_QUOTES)));
$mes=date("m");
$year=date("Y");
$t=$mes+$meses;
if($t>12)
{
    $mes=$t-12;
    $year++;
}
$fecha_final=$year."-".$t."-31 00:00:00";
$fecha_inicial=date("Y-m-d")." 00:00:00";
$cadena = " from productos,lineas,stock where stock.id_producto_stock=productos.id_producto and fecha_vencimiento  between '$fecha_inicial' and '$fecha_final' and lineas.id_linea=productos.id_linea_producto";
if($id_sucursal>0)
{
    $cadena.=" and stock.id_sucursal_stock=".$id_sucursal;
}
if($id_categoria>0)
{
    $cadena.=" and lineas.id_linea=".$id_categoria;
}


$consulta  = "SELECT * ".$cadena;
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
        ->setDescription("Reporte de Pedidos")
        ->setKeywords("Reporte Pedidos")
        ->setCategory("Reporte excel");
    $tituloReporte   = "Reporte de Pedidos";
    $titulosColumnas = array('Codigo', 'Categoria','Nombre', 'Vencimientos' ,'Stock', 'Costo','Subtotal');
    $objPHPExcel->setActiveSheetIndex(0)
        ->mergeCells('A1:F1');
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', $tituloReporte)
        ->setCellValue('A3', $titulosColumnas[0])
        ->setCellValue('B3', $titulosColumnas[1])
        ->setCellValue('C3', $titulosColumnas[2])
        ->setCellValue('D3', $titulosColumnas[3])
        ->setCellValue('E3', $titulosColumnas[4])
        ->setCellValue('F3', $titulosColumnas[5])
        ->setCellValue('G3', $titulosColumnas[6]);
    $i = 4;
    while ($fila = $resultado->fetch_array()) {
        $stock=$fila['cantidad_stock'];
        $fecha=$fila['fecha_vencimiento'];
        $costo=$fila['costo_producto'];
        $sub=$costo*$stock;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, $fila['codigo_producto'])
            ->setCellValue('B' . $i, $fila['nombre_linea'] )
            ->setCellValue('C' . $i, $fila['nombre_producto'] )
            ->setCellValue('D' . $i, $fecha )
            ->setCellValue('E' . $i, $stock)
            ->setCellValue('H' . $i, $costo)
            ->setCellValue('I' . $i, $sub);
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
    $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->applyFromArray($estiloTituloReporte);
    $objPHPExcel->getActiveSheet()->getStyle('A3:I3')->applyFromArray($estiloTituloColumnas);
    for ($i = 'A'; $i <= 'F'; $i++) {
        $objPHPExcel->setActiveSheetIndex(0)
            ->getColumnDimension($i)->setAutoSize(true);
    }
    $objPHPExcel->getActiveSheet()->setTitle('Traslados');
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0, 4);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Vencimientos.xlsx"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
    exit;
} else {
    echo "<script>alert('No hay resultados para mostrar')</script>";
    echo "<script>window.close();</script>";
    echo "<script>window.location.replace('../html/rep_producto_ventas.php');</script>";
    exit;
}
