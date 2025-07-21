<?php
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
$url = $_SERVER['REQUEST_URI'];
$path = parse_url($url, PHP_URL_PATH);
$directories = explode('/', $path);
array_shift($directories);
$pagina = $protocol . $_SERVER['HTTP_HOST']  . '/' . $directories[0];
header("Location: $pagina/sair.php");
