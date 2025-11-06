<?php
// ATENÇÃO: Adicione as linhas abaixo TEMPORARIAMENTE para depuração.
// REMOVA-AS quando o problema for resolvido em ambiente de produção.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// FIM DAS LINHAS DE DEPURAÇÃO TEMPORÁRIAS

include '../../../conexao/conexao.php'; // Verifique e ajuste este caminho
header('Content-Type: application/json');

// =================================================================
// LÓGICA PARA BUSCAR TODOS OS COLABORADORES (QUANDO É UMA REQUISIÇÃO GET SEM PARÂMETROS ESPECÍFICOS)
// =================================================================
if ($_SERVER["REQUEST_METHOD"] == "GET") { // Se a requisição é GET
    try {
        $stmt = $conn->prepare("SELECT CHAPA, NOMESOCIAL FROM " . $view_colaboradores . " ORDER BY NOMESOCIAL");
        $stmt->execute();
        $colaboradores = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($colaboradores);
    } catch (PDOException $e) {
        error_log("Erro PDO ao buscar TODOS os colaboradores: " . $e->getMessage());
        echo json_encode(['error' => 'Erro interno ao buscar lista completa de colaboradores.']);
        http_response_code(500);
    }
    exit; // Termina a execução após processar esta requisição
}

// =================================================================
// LÓGICA PARA BUSCAR MÚLTIPLOS E-MAILS (QUANDO É UM POST COM 'matriculas')
// =================================================================
if (isset($_POST['matriculas']) && is_array($_POST['matriculas'])) {
    $matriculas = $_POST['matriculas'];
    $emails = [];

    $clean_matriculas = [];
    foreach ($matriculas as $m) {
        $clean_matriculas[] = trim($m); // Simplesmente trime, já que será bindado com PDO::PARAM_STR
    }
    $clean_matriculas = array_filter($clean_matriculas);

    if (empty($clean_matriculas)) {
        echo json_encode([]);
        exit;
    }

    $placeholders = implode(',', array_fill(0, count($clean_matriculas), '?'));

    try {
        $sql = "SELECT EMAIL FROM " . $view_colaboradores . " WHERE CHAPA IN (" . $placeholders . ")";
        $stmt = $conn->prepare($sql);
        foreach ($clean_matriculas as $key => $matricula) {
            $stmt->bindValue(($key + 1), $matricula, PDO::PARAM_STR);
        }
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!empty($row['EMAIL'])) {
                $emails[] = $row['EMAIL'];
            }
        }
        echo json_encode($emails);
    } catch (PDOException $e) {
        error_log("Erro PDO ao buscar múltiplos e-mails de colaboradores: " . $e->getMessage());
        echo json_encode(['error' => 'Erro interno ao buscar e-mails.']);
        http_response_code(500);
    }
    exit;
}

// =================================================================
// LÓGICA PARA BUSCAR UM ÚNICO COLABORADOR (QUANDO É UM POST COM 'matricula')
// =================================================================
if (isset($_POST['matricula'])) { // Recebe uma ÚNICA matrícula
    $matricula = trim($_POST['matricula']);

    try {
        $stmt = $conn->prepare("SELECT CHAPA, NOMESOCIAL, EMAIL FROM " . $view_colaboradores . " WHERE CHAPA = :matricula");
        $stmt->execute([':matricula' => $matricula]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo json_encode($result);
        } else {
            echo json_encode(['CHAPA' => '', 'NOMESOCIAL' => '', 'EMAIL' => '']);
        }
    } catch (PDOException $e) {
        error_log("Erro PDO ao buscar dados do colaborador único: " . $e->getMessage());
        echo json_encode(['error' => 'Erro interno ao buscar dados do colaborador.']);
        http_response_code(500);
    }
    exit;
}

// =================================================================
// SE NENHUM DOS PARÂMETROS ESPERADOS FOREM FORNECIDOS
// (Isso só deve acontecer se a requisição não se encaixar em GET ou nos POSTs acima)
// =================================================================
echo json_encode(['error' => 'Parâmetro(s) inválido(s) ou não fornecido(s). Requisição não processada.']);
http_response_code(400);

// NÃO FECHE A TAG PHP (
