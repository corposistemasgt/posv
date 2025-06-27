<?php
function get_url2()
{
    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $dominio = $_SERVER['HTTP_HOST'];
    $ruta = $_SERVER['REQUEST_URI'];
    $url_completa = $protocolo . $dominio. $ruta;
    $url_completa=trim($url_completa,'?logout');
    $url_completa=trim($url_completa,'/login.php');
    return $url_completa;
}
function get_pass($pass,$clave)
{
    $metodo = 'AES-128-CBC';
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($metodo));
    $ciphertext = openssl_encrypt($pass, $metodo, $clave, 0, $iv);
    return base64_encode($ciphertext.$iv);
}