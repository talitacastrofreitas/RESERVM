<?php

/**
 * =================================================================
 * ARQUIVO DE CONFIGURAÇÃO E CONEXÃO GLOBAL
 * =================================================================
 */

// --- HEADERS DE SEGURANÇA ---

header("Content-Security-Policy: font-src 'self' fonts.googleapis.com fonts.gstatic.com;");
header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: no-referrer-when-downgrade");
header("X-XSS-Protection: 1; mode=block");

// --- INICIALIZAÇÃO DO COMPOSER E DOTENV ---
// O caminho '/../' indica que o .env está um diretório acima deste arquivo.
require_once __DIR__ . '/../vendor/autoload.php';

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
} catch (\Dotenv\Exception\InvalidPathException $e) {
    // Erro crítico: não foi possível encontrar o arquivo .env
    error_log("Erro crítico: Não foi possível carregar o arquivo .env. " . $e->getMessage());
    die("Erro de configuração do ambiente. Contate o administrador.");
}

// --- CONFIGURAÇÕES GLOBAIS DA APLICAÇÃO ---

// HORA LOCAL DO BRASIL
date_default_timezone_set('America/Sao_Paulo');

// VARIÁVEIS VINDAS DO .ENV
// AJUSTE: Usando a variável APP_URL do .env, que é mais seguro e confiável.
$url_sistema = $_ENV['APP_URL'];
$email_saap = $_ENV['EMAIL_SAAP'];
$view_alunos = $_ENV['VIEW_ALUNOS'];
$view_colaboradores = $_ENV['VIEW_COLABORADORES'];


// --- CONEXÃO COM BANCO DE DADOS ---
try {
    // AJUSTE: Nomes de variáveis padronizados para corresponder ao .env
    $host = $_ENV['DB_HOST'];
    $port = $_ENV['DB_PORT'];
    $database = $_ENV['DB_DATABASE'];
    $user = $_ENV['DB_USERNAME']; // Corrigido de DB_USER para DB_USERNAME
    $password = $_ENV['DB_PASSWORD'];

    // String de conexão para SQL Server (sqlsrv)
    $dsn = "sqlsrv:Server={$host},{$port};Database={$database};TrustServerCertificate=true;";

    $conn = new PDO($dsn, $user, $password);

    // Define o modo de erro do PDO para exceção (ótima prática)
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Grava o erro real em um log para depuração interna (MUITO IMPORTANTE)
    error_log("Erro de conexão com o banco de dados: " . $e->getMessage());

    // Limpa a sessão para evitar que o usuário continue em um estado inconsistente
    if (session_status() == PHP_SESSION_ACTIVE) {
        session_unset();
        session_destroy();
    }

    // Redireciona para uma página de erro genérica e segura
    header("Location: " . $url_sistema . "/error");
    exit; // Garante que o script pare a execução imediatamente
}