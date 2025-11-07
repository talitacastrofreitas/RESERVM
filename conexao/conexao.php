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
// O caminho de carregamento do Composer é mantido como relativo.
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Diretórios Candidatos para o arquivo .env
 * O Dotenv tentará carregar o arquivo a partir do primeiro caminho que funcionar.
 */
$dotenvCandidateDirs = [
    '/etc/reservm',                  // Linux recomendado (prioridade 1)
    'C:\\xampp\\etc\\reservm',       // Windows/XAMPP (prioridade 2)
];

try {
    // ALTERAÇÃO CRÍTICA: Busca o arquivo .env nos diretórios candidatos
    // O Dotenv fará a validação das permissões e caminhos.
    $dotenv = Dotenv\Dotenv::createImmutable($dotenvCandidateDirs);
    $dotenv->load();
} catch (\Dotenv\Exception\InvalidPathException $e) {
    // Erro crítico: não foi possível encontrar o arquivo .env em nenhum dos caminhos.
    error_log("Erro crítico: Não foi possível carregar o arquivo .env. Verifique se o arquivo está em um dos caminhos candidatos e se as permissões estão corretas. " . $e->getMessage());
    die("Erro de configuração do ambiente. Contate o administrador.");
}

// --- CONFIGURAÇÕES GLOBAIS DA APLICAÇÃO ---

// HORA LOCAL DO BRASIL
date_default_timezone_set('America/Sao_Paulo');

// VARIÁVEIS DE AMBIENTE (APP_ENV)
// Define o ambiente atual (local, homologacao ou producao). Padrão para 'producao' se não definida.
// Verifica se a variável existe antes de tentar acessar.
$app_env = $_ENV['APP_ENV'] ?? 'producao';

// VARIÁVEIS GLOBAIS VINDAS DO .ENV
$url_sistema = $_ENV['APP_URL'];
$admin_email = $_ENV['EMAIL_SAAP'];
$view_alunos = $_ENV['VIEW_ALUNOS'];
$view_colaboradores = $_ENV['VIEW_COLABORADORES'];


// --- CONEXÃO COM BANCO DE DADOS ---
try {
    // Correção do Erro: A lógica agora verifica se é ambiente local/localhost
    // e busca as variáveis DB_HOST, etc., sem sufixo, conforme o arquivo .env do usuário.
    if (in_array(strtolower($app_env), ['local', 'localhost'])) {
        // Se for ambiente local, usa as variáveis sem sufixo (DB_HOST, DB_PORT, etc.)
        $host = $_ENV["DB_HOST"];
        $port = $_ENV["DB_PORT"];
        $database = $_ENV["DB_DATABASE"];
        $user = $_ENV["DB_USERNAME"];
        $password = $_ENV["DB_PASSWORD"];
    } else {
        // Para homologacao e producao, usa os sufixos (DB_HOST_HOMOLOGACAO, etc.)
        $sufixo_env = strtoupper($app_env);

        $host = $_ENV["DB_HOST_{$sufixo_env}"];
        $port = $_ENV["DB_PORT_{$sufixo_env}"];
        $database = $_ENV["DB_DATABASE_{$sufixo_env}"];
        $user = $_ENV["DB_USERNAME_{$sufixo_env}"];
        $password = $_ENV["DB_PASSWORD_{$sufixo_env}"];
    }

    // String de conexão para SQL Server (sqlsrv)
    $dsn = "sqlsrv:Server={$host},{$port};Database={$database};TrustServerCertificate=true;";

    $conn = new PDO($dsn, $user, $password);

    // Define o modo de erro do PDO para exceção (ótima prática)
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Grava o erro real em um log para depuração interna (MUITO IMPORTANTE)
    error_log("Erro de conexão com o banco de dados ($app_env): " . $e->getMessage());

    // Se NÃO for ambiente local, limpa a sessão e redireciona para um erro seguro.
    if (!in_array(strtolower($app_env), ['local', 'localhost'])) {
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }

        // Redireciona para uma página de erro genérica e segura
        header("Location: " . $url_sistema . "/error");
        exit; // Garante que o script pare a execução imediatamente
    }

    // Se for ambiente local, exibe a mensagem completa para facilitar a depuração.
    die("Falha na Conexão do Banco de Dados em ambiente **{$app_env}**: " . $e->getMessage());
}