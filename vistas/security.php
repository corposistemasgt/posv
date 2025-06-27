<?php
function get_url()
{
    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $dominio = $_SERVER['HTTP_HOST'];
    $ruta = $_SERVER['REQUEST_URI'];
    $url_completa = $protocolo . $dominio. $ruta;
    return $url_completa;
}