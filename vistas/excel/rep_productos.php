<?php
session_start();
if (!isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] != 1) {
    header("location: ../../login.php");
    exit;
}
require_once "../db.php";
require_once "../php_conexion.php"; 
require_once "../funciones.php";
$user_id = $_SESSION['id_users'];
$sucur=$_GET['sucursalid'];
$q = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['q'], ENT_QUOTES)));
$sqlUsuarioACT        = mysqli_query($conexion, "select * from users inner join perfil on sucursal_users = perfil.id_perfil 
inner join user_group on users.cargo_users = user_group.user_group_id 
where id_users = '".$user_id."'");
    $rw         = mysqli_fetch_array($sqlUsuarioACT);
    $id_sucursal = $rw['sucursal_users'];
    $nombreSucursal = $rw['giro_empresa'];
    $nombreCargo      = $rw['name'];

$sWhere    = " stock.id_producto_stock=productos.id_producto and stock.id_sucursal_stock=".$sucur;
 if ($_GET['q'] != "") {
    $sWhere    .= " and (productos.codigo_producto like '%$q%' or productos.nombre_producto like '%$q%')  ";

}
$consulta  = "SELECT * FROM productos left join lineas on productos.id_linea_producto = lineas.id_linea 
left join proveedores on productos.id_proveedor = proveedores.id_proveedor  ,stock WHERE $sWhere  order by productos.id_producto DESC";
$resultado = $conexion->query($consulta);
if ($resultado->num_rows > 0) {
    date_default_timezone_set('America/Mexico_City');
    if (PHP_SAPI == 'cli') {
        die('Este archivo solo se puede ver desde un navegador web');
    }
    require_once 'lib/PHPExcel/PHPExcel.php';
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("Codedrinks") //Autor
        ->setLastModifiedBy("Codedrinks") //Ultimo usuario que lo modificó
        ->setTitle("Reporte Excel con PHP y MySQL")
        ->setSubject("Reporte Excel con PHP y MySQL")
        ->setDescription("Reporte de Productos")
        ->setKeywords("reporte productos")
        ->setCategory("Reporte excel");
    $tituloReporte   = "Reporte de Productos";
    $titulosColumnas = array('ID', 'CODIGO', 'NOMBRE', 'EXISTENCIA', 'COSTO', 'P.VENTA', 'P.MAYOREO', 'P.ESPECIAL','P.MAYORISTA', 'CATEGORIA',  'VENCIMIENTO','PROVEEDOR','STOCK MINIMO','DESCIPCION');
    $objPHPExcel->setActiveSheetIndex(0)
        ->mergeCells('A1:J1');
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', $tituloReporte)
        ->setCellValue('A3', $titulosColumnas[0])
        ->setCellValue('B3', $titulosColumnas[1])
        ->setCellValue('C3', $titulosColumnas[2])
        ->setCellValue('D3', $titulosColumnas[3])
        ->setCellValue('E3', $titulosColumnas[4])
        ->setCellValue('F3', $titulosColumnas[5])
        ->setCellValue('G3', $titulosColumnas[6])
        ->setCellValue('H3', $titulosColumnas[7])
        ->setCellValue('I3', $titulosColumnas[8])
        ->setCellValue('J3', $titulosColumnas[9])
        ->setCellValue('K3', $titulosColumnas[10])
        ->setCellValue('L3', $titulosColumnas[11])
	->setCellValue('M3', $titulosColumnsa[12])
        ->setCellValue('N3', $titulosColumnas[13]);
    $i = 4;
    while ($fila = $resultado->fetch_array()) {
        $id_producto = $fila['id_producto'];
        $stock_producto= $fila['id_producto'];
        if($fila['fecha_vencimiento'] == 0)
            {
                $date_vence = "no";
            }else{
                $date_vence           = date('d/m/Y', strtotime($fila['fecha_vencimiento']));
            }
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, $fila['id_producto'])
            ->setCellValue('B' . $i, $fila['codigo_producto'])
            ->setCellValue('C' . $i, $fila['nombre_producto'])
            ->setCellValue('D' . $i, $fila['cantidad_stock'])
            ->setCellValue('E' . $i, $fila['costo_producto'])
            ->setCellValue('F' . $i, $fila['valor1_producto'])
            ->setCellValue('G' . $i, $fila['valor2_producto'])
            ->setCellValue('H' . $i, $fila['valor3_producto'])
	    ->setCellValue('I' . $i, $fila['valor4_producto'])
            ->setCellValue('J' . $i, $fila['nombre_linea'])
            ->setCellValue('K' . $i, $date_vence)
            ->setCellValue('L' . $i, $fila['nombre_proveedor'])
            ->setCellValue('M' . $i, $fila['stock_min_producto'])
            ->setCellValue('N' . $i, $fila['descripcion_producto']);
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
            'size'   => 10,
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
    $objPHPExcel->getActiveSheet()->getStyle('A1:L1')->applyFromArray($estiloTituloReporte);
    $objPHPExcel->getActiveSheet()->getStyle('A3:L3')->applyFromArray($estiloTituloColumnas);
    $objPHPExcel->getActiveSheet()->getStyle('E4:E' . ($i - 1))->getNumberFormat()->setFormatCode('#,##0.00'); //FORMATO NUMERICO
    $objPHPExcel->getActiveSheet()->getStyle('F4:F' . ($i - 1))->getNumberFormat()->setFormatCode('#,##0.00'); //FORMATO NUMERICO
    $objPHPExcel->getActiveSheet()->getStyle('G4:G' . ($i - 1))->getNumberFormat()->setFormatCode('#,##0.00'); //FORMATO NUMERICO
    $objPHPExcel->getActiveSheet()->getStyle('H4:H' . ($i - 1))->getNumberFormat()->setFormatCode('#,##0.00'); //FORMATO NUMERICO
 $objPHPExcel->getActiveSheet()->getStyle('I4:I' . ($i - 1))->getNumberFormat()->setFormatCode('#,##0.00'); //FORMATO NUMERICO
    for ($i = 'A'; $i <= 'L'; $i++) {
        $objPHPExcel->setActiveSheetIndex(0)
            ->getColumnDimension($i)->setAutoSize(true);
    }
    $objPHPExcel->getActiveSheet()->setTitle('Productos');
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0, 4);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Reporteproductos.xlsx"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
    exit;
} else {
    echo("consulta = ".$consulta);
}
