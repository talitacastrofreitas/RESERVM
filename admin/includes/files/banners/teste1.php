<?php
echo 'Usuário atual do processo PHP: ' . get_current_user() . '<br>';
echo 'ID do usuário atual do processo PHP: ' . getmyuid() . '<br>';
echo 'ID do grupo atual do processo PHP: ' . getmygid() . '<br>';

$dir = realpath(__DIR__); // Apenas pegará o caminho atual do script
echo 'Caminho absoluto do diretório de banners: ' . $dir . '<br>';

if (is_dir($dir)) {
    echo 'O diretório de banners existe.<br>';
} else {
    echo 'O diretório de banners NÃO existe.<br>';
}

if (is_writable($dir)) {
    echo 'O diretório de banners TEM permissão de escrita.<br>';
} else {
    echo 'O diretório de banners NÃO tem permissão de escrita.<br>';
    // Tentar criar um arquivo para depuração
    $testFile = $dir . '/test_write_' . uniqid() . '.txt';
    if (file_put_contents($testFile, 'test')) {
        echo 'Consegui escrever um arquivo de teste: ' . $testFile . '<br>';
        unlink($testFile); // Remover o arquivo de teste
    } else {
        echo 'FALHA ao escrever arquivo de teste no diretório. Verifique os logs do servidor.<br>';
    }
}

// Para ver as permissões do diretório pelo PHP (funciona apenas em sistemas Unix-like)
clearstatcache(); // Limpa o cache para obter as informações mais recentes
$perms = fileperms($dir);
echo 'Permissões Octal detectadas pelo PHP: ' . substr(sprintf('%o', $perms), -4) . '<br>';
?>