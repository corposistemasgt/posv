<?php
session_start();
if ( strpos(get_url(), $_SESSION['ruta']) === false||isset($_SESSION['ruta']) == false) {
    header("location: ../../login.php?logout");
    exit;
}
if (!isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] != 1) {
    header("location: ../../login.php");
    exit;
}
function get_url()
{
    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $dominio = $_SERVER['HTTP_HOST'];
    $ruta = $_SERVER['REQUEST_URI'];
    $url_completa = $protocolo . $dominio. $ruta;
    return $url_completa;
}