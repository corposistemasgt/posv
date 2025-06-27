<?php
session_start();
if (!isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] != 1) {
    header("location: ../../login.php");
    exit;
}
require_once "../db.php";
require_once "../php_conexion.php";
require_once "../funciones.php";
$idcliente = intval($_REQUEST['idcliente']);
$consulta  = "SELECT id_credito,facturas_ventas.numero_factura,fecha_credito,
    factura_nombre_cliente,correos,nombre_users,apellido_users,estado_credito,
    clientes.limite_credito,monto_credito,saldo_credito,id_factura as idf,(select 
    sum(monto_factura-costo_producto * cantidad) from facturas_ventas,detalle_fact_ventas,
    productos  where facturas_ventas.id_factura=detalle_fact_ventas.id_factura and 
productos.id_producto =detalle_fact_ventas.id_producto and facturas_ventas.id_factura =idf) 
as ganancia  FROM creditos,facturas_ventas,users,clientes where 
creditos.numero_factura =facturas_ventas.numero_factura and 
facturas_ventas.id_vendedor =users.id_users and 
facturas_ventas.id_cliente=clientes.id_cliente and clientes.id_cliente=".$idcliente ." 
order by creditos.id_credito desc";
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
        ->setDescription("Reporte de Compras")
        ->setKeywords("reporte compras")
        ->setCategory("Reporte excel");
    $tituloReporte   = "Reporte de Creditos";
    $titulosColumnas = array('FACTURA', 'FECHA', 'ESTADO', 'DIAS DE CREDITO', 'DIAS RESTANTES','CREDITO','SALDO','GANANCIA');
    $objPHPExcel->setActiveSheetIndex(0)
        ->mergeCells('A1:H1');
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', $tituloReporte)
        ->setCellValue('A3', $titulosColumnas[0])
        ->setCellValue('B3', $titulosColumnas[1])
        ->setCellValue('C3', $titulosColumnas[2])
        ->setCellValue('D3', $titulosColumnas[3])
        ->setCellValue('E3', $titulosColumnas[4])
        ->setCellValue('F3', $titulosColumnas[5])
        ->setCellValue('G3', $titulosColumnas[6])
        ->setCellValue('H3', $titulosColumnas[7]);
    $i = 4;
    while ($fila = $resultado->fetch_array()) {
        $total            = $fila['monto_credito'];
        $saldo            = $fila['saldo_credito'];
        $limite           = $fila['limite_credito'];
        $ganancia           = $fila['ganancia'];
        if ($fila['estado_credito'] != 2) {
            $estado = 'Pagado';
        } else { $estado = 'pendiente';}

        $fecha1 = new DateTime($fila['fecha_credito']);
        $fecha1->modify("+$limite days");
        $fechaActual = new DateTime();
        $diferencia = $fechaActual->diff($fecha1);
        $diasr=$diferencia->days;

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, $fila['numero_factura'])
            ->setCellValue('B' . $i, date('d/m/Y', strtotime($fila['fecha_credito'])))
            ->setCellValue('C' . $i, $estado)
            ->setCellValue('D' . $i, $limite)
            ->setCellValue('E' . $i, $diasr)
            ->setCellValue('F' . $i, $total)
            ->setCellValue('G' . $i, $saldo)
            ->setCellValue('H' . $i, $ganancia);
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
    $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($estiloTituloReporte);
    $objPHPExcel->getActiveSheet()->getStyle('A3:H3')->applyFromArray($estiloTituloColumnas);
    $objPHPExcel->getActiveSheet()->getStyle('F4:H' . ($i - 1))->getNumberFormat()->setFormatCode('#,##0.00'); //FORMATO NUMERICO
    for ($i = 'A'; $i <= 'H'; $i++) {
        $objPHPExcel->setActiveSheetIndex(0)
            ->getColumnDimension($i)->setAutoSize(true);
    }
    $objPHPExcel->getActiveSheet()->setTitle('Ventas');
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0, 4);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="ReporteCreditos.xlsx"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
    exit;
} else {
    echo "<script>alert('No hay resultados para mostrar')</script>";
    echo "<script>window.close();</script>";
    header("Location:../html/cxr.php");
    exit;
}
