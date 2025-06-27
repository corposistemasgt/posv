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
$daterange      = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['range'], ENT_QUOTES)));
$id_sucursal      = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['sucursalid'], ENT_QUOTES)));
$id_categoria      = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['id_categoria'], ENT_QUOTES)));
$agrupar           = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['agrupar'], ENT_QUOTES)));
$tables       = "detalle_fact_ventas AS detf inner join facturas_ventas 
                     on detf.id_factura = facturas_ventas.id_factura inner join productos 
                     on detf.id_producto = productos.id_producto inner join lineas 
                     on productos.id_linea_producto = lineas.id_linea inner join perfil on facturas_ventas.id_sucursal = perfil.id_perfil 
                     inner join users on facturas_ventas.id_vendedor = users.id_users ";
    $tablesCount  = "productos,  lineas";
    if($agrupar == "si"){
        $campos       = "productos.codigo_producto, 
                     productos.nombre_producto, productos.id_producto, 
                     productos.id_linea_producto, lineas.id_linea, lineas.nombre_linea, 
                     productos.estado_producto, facturas_ventas.fecha_factura, 
                     facturas_ventas.id_factura, facturas_ventas.condiciones, detf.*, sum(detf.cantidad) as cantidad_vendida, 
                     sum(cantidad*precio_venta*desc_venta/100) as descuento, users.usuario_users, sum(detf.precio_venta*cantidad) as precio_venta,
                     sum(importe_venta) as importe_venta, perfil.* ";
    }else{
        $campos       = "productos.codigo_producto, 
        productos.nombre_producto, productos.id_producto, 
        productos.id_linea_producto, lineas.id_linea, lineas.nombre_linea, 
        productos.estado_producto, facturas_ventas.fecha_factura, 
        facturas_ventas.id_factura, facturas_ventas.condiciones, detf.*, detf.cantidad as cantidad_vendida, users.usuario_users, perfil.* ";
    }
if (!empty($daterange)) {
    list($f_inicio, $f_final)                    = explode(" - ", $daterange); //Extrae la fecha inicial y la fecha final en formato espa?ol
    list($dia_inicio, $mes_inicio, $anio_inicio) = explode("/", $f_inicio); //Extrae fecha inicial
    $fecha_inicial                               = "$anio_inicio-$mes_inicio-$dia_inicio 00:00:00"; //Fecha inicial formato ingles
    list($dia_fin, $mes_fin, $anio_fin)          = explode("/", $f_final); //Extrae la fecha final
    $fecha_final                                 = "$anio_fin-$mes_fin-$dia_fin 23:59:59";
    $fecha_impresion_inicial = date("d/m/y", strtotime($fecha_inicial));
    $fecha_impresion_final   = date("d/m/y", strtotime($fecha_final));
    $sWhere = " facturas_ventas.fecha_factura between '$fecha_inicial' and '$fecha_final' ";
    

    if ($id_sucursal > 0) {
        $sWhere .= " and facturas_ventas.id_sucursal = '" . $id_sucursal . "' ";
    }


    if ($id_categoria > 0) {
        $sWhere .= " and productos.id_linea_producto = '" . $id_categoria . "' ";
    }

    if($agrupar == "si"){
        $sWhere .= " group by productos.id_producto ";
    }
   
}
$sWhere .= " order by (productos.id_producto)";
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
        ->setDescription("Reporte de Ventas por Productos")
        ->setKeywords("reporte ventas")
        ->setCategory("Reporte excel");
    $tituloReporte   = "Reporte de Productos Vendidos";
    if($agrupar == "si"){
       $precio_titulo = "Precio V. promedio";
    }else{
       $precio_titulo = "Precio Venta";
    }

    $titulosColumnas = array('Codigo', 'Nombre', 'Categoria','Sucursal' ,'Usuario','Fecha', 'Cantidad', $precio_titulo, 'Descuento Q','Importe Venta','Tipo Pago');
    $objPHPExcel->setActiveSheetIndex(0)
        ->mergeCells('A1:F1');
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', $tituloReporte)
        ->setCellValue('B2',"Rango: $fecha_impresion_inicial - $fecha_impresion_final")
        ->setCellValue('A3', $titulosColumnas[0])
        ->setCellValue('B3', $titulosColumnas[1])
        ->setCellValue('C3', $titulosColumnas[2])
        ->setCellValue('D3', $titulosColumnas[3])
        ->setCellValue('E3', $titulosColumnas[4])//usuario
        ->setCellValue('F3', $titulosColumnas[5])
        ->setCellValue('G3', $titulosColumnas[6])
        ->setCellValue('H3', $titulosColumnas[7])
        ->setCellValue('I3', $titulosColumnas[8])
        ->setCellValue('J3', $titulosColumnas[9])
        ->setCellValue('K3', $titulosColumnas[10]);
    $i = 4;
    while ($fila = $resultado->fetch_array()) {
        $fecha            = date("d/m/Y", strtotime($fila['fecha_factura']));
        $pago    = condicion($fila['condiciones']);
        $cantidad         = $fila['cantidad_vendida'];
        $precio_venta     = $fila['precio_venta'];
        $porcentaje_descuento = $fila['desc_venta'];
        $cantidad_sinDescuento = $cantidad*$precio_venta;
        $monto_descuento       = ($cantidad_sinDescuento*$porcentaje_descuento)/100;
        if($agrupar == "si"){
            $fecha_factura         = "agrupado";
            $precio_venta     =$fila['precio_venta']/$cantidad;  
            $monto_descuento = $fila['descuento'];
        }else{
            $fecha_factura         = $fila['fecha_factura'];
            $precio_venta     = $fila['precio_venta'];
            $porcentaje_descuento = $fila['desc_venta'];
            $cantidad_sinDescuento = $cantidad*$precio_venta;
            $monto_descuento       = ($cantidad_sinDescuento*$porcentaje_descuento)/100;
        }
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, $fila['codigo_producto'])
            ->setCellValue('B' . $i, $fila['nombre_producto'] )
            ->setCellValue('C' . $i, $fila['nombre_linea'] )
            ->setCellValue('D' . $i, $fila['giro_empresa'] )
            ->setCellValue('E' . $i, $fila['usuario_users'] )///USUARIO
            ->setCellValue('F' . $i,date("d/m/Y", strtotime($fila['fecha_factura'])) )
            ->setCellValue('G' . $i, $fila['cantidad_vendida'])
            ->setCellValue('H' . $i, $precio_venta)
            ->setCellValue('I' . $i, $monto_descuento)
            ->setCellValue('J' . $i, $fila['importe_venta'])
            ->setCellValue('K' . $i, $pago);
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
    $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->applyFromArray($estiloTituloReporte);
    $objPHPExcel->getActiveSheet()->getStyle('A3:K3')->applyFromArray($estiloTituloColumnas);
    for ($i = 'A'; $i <= 'F'; $i++) {
        $objPHPExcel->setActiveSheetIndex(0)
            ->getColumnDimension($i)->setAutoSize(true);
    }
    $objPHPExcel->getActiveSheet()->setTitle('Traslados');
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0, 4);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="ReporteVentas.xlsx"');
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
