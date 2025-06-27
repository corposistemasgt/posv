<?php
use PhpOffice\PhpSpreadsheet\IOFactory;
    if(is_array($_FILES['archivoexcel']) && count($_FILES['archivoexcel']) > 0)
    {
        session_start();
        require_once "../db.php";
        require_once "../php_conexion.php"; 
        require_once '../excel/lib/PHPExcel/PHPExcel.php';
        $tmpfname = $_FILES['archivoexcel']['tmp_name'];
        $leerExcel = PHPExcel_IOFactory::createReader('Excel2007');
        $excelobj = $leerExcel->load($tmpfname);
        $hoja = $excelobj->getSheet(0);
        $filas = $hoja->getHighestRow();
        echo "<table id = 'tabla_detalle' class ='table' style='width:100%';
                table-layout:fixed>
                <thead>
                    <tr bgColor = 'black' style ='color:#FFF'>
                        <td>Categoria</td>
                        <td>Codigo</td>
                        <td>Producto</td>
                        <td>Descripción</td>
                        <td>Costo</td>
                        <td>Precio1</td>
                        <td>Precio2</td>
                        <td>Precio3</td>
                        <td>Precio4</td>
                        <td>Cantidad Inv.</td>
                        <td>Cant. Mínima</td>
                        <td>es Genérico</td>
                        <td>F. Vencimiento</td>
                        <td>Proveedor</td>
                    </tr>
                </thead>
                <tbody id='tbody_tabla_detalle'>";
        
        for($i = 2; $i <= $filas; $i++)
        {
            $categoria = $hoja ->getCell('A'.$i)->getValue();
            $codigo = $hoja ->getCell('B'.$i)->getValue();
            $producto = $hoja ->getCell('C'.$i)->getValue();
            $descripcion = $hoja ->getCell('D'.$i)->getValue();
            $costo = $hoja ->getCell('E'.$i)->getValue();
            $precio1 = $hoja ->getCell('F'.$i)->getValue();
            $precio2 = $hoja ->getCell('G'.$i)->getValue();
            $precio3 = $hoja ->getCell('H'.$i)->getValue();
            $precio4 = $hoja ->getCell('I'.$i)->getValue();
            $cantInventario = $hoja ->getCell('J'.$i)->getValue();
            $cantMin = $hoja ->getCell('K'.$i)->getValue();
            $generico = $hoja ->getCell('L'.$i)->getValue();
            $vence = $hoja ->getCell('M'.$i)->getValue();
            $prove = $hoja ->getCell('N'.$i)->getValue();
            echo " <tr>";
            echo "<td>".$categoria." </td>";
            echo "<td>".$codigo." </td>";
            echo "<td>".$producto." </td>";
            echo "<td>".$descripcion." </td>";
            echo "<td>".$costo." </td>";
            echo "<td>".$precio1." </td>";
            echo "<td>".$precio2." </td>";
            echo "<td>".$precio3." </td>";
            echo "<td>".$precio4." </td>";
            echo "<td>".$cantInventario." </td>";
            echo "<td>".$cantMin." </td>";
            echo "<td>".$generico." </td>";
            echo "<td>".$vence." </td>";
            echo "<td>".$prove." </td>";
            echo "</tr>";
        }
        echo"</tbody> </table>";
    }