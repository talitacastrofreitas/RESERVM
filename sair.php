<?php
// Inicia a sessão
session_start();

// Destroi todas as variáveis de sessão
$_SESSION = array();

// Se você estiver usando cookies de sessão, também pode querer apagá-los
if (ini_get("session.use_cookies")) {
  $params = session_get_cookie_params();
  setcookie(
    session_name(),
    '',
    time() - 42000,
    $params["path"],
    $params["domain"],
    $params["secure"],
    $params["httponly"]
  );
}

// Destrói a sessão
session_destroy();

//RECUPERA URL PARA O LINK
$url = $_SERVER['REQUEST_URI'];
$path = parse_url($url, PHP_URL_PATH);
$directories = explode('/', $path);
array_shift($directories);
$url_sistema = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $directories[0];

header('Location:' . $url_sistema);
exit; // Garante que o script seja interrompido após o redirecionamento
