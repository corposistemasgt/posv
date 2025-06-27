<?php
session_start();
$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != null) ? $_REQUEST['action'] : '';
if ($action == 'ajax') {
    require_once "../db.php";
    require_once "../php_conexion.php";
    include "../funciones.php";
    $periodo = intval($_REQUEST['periodo']);
    $txt_mes = array("1" => "Ene", "2" => "Feb", "3" => "Mar", "4"  => "Abr", "5"  => "May", "6"  => "Jun",
        "7"                  => "Jul", "8" => "Ago", "9" => "Sep", "10" => "Oct", "11" => "Nov", "12" => "Dic",
    );
    $categorias[] = array('Mes', "Ventas $periodo", "Compras $periodo ");
    for ($inicio = 1; $inicio <= 12; $inicio++) {
        $mes          = $txt_mes[$inicio];
        $ingresos     = monto('facturas_ventas', $inicio, $periodo);
        $egresos      = monto('facturas_compras', $inicio, $periodo);
        $categorias[] = array($mes, $ingresos, $egresos);

    }
    echo json_encode(($categorias));
}