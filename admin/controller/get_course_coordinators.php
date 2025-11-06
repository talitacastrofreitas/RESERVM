<?php
// ATENÇÃO: Adicione as linhas abaixo TEMPORARIAMENTE para depuração.
// REMOVA-AS quando o problema for resolvido em ambiente de produção.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// FIM DAS LINHAS DE DEPURAÇÃO TEMPORÁRIAS

// Ajuste o caminho conforme necessário para o seu arquivo de conexão
include '../../conexao/conexao.php'; // Verifique se este caminho está correto

// Certifique-se de que $view_colaboradores está definida.
// Se não estiver definida em conexao.php, defina-a aqui:
// Exemplo: $view_colaboradores = "sua_view_colaboradores"; // Substitua pelo nome real

header('Content-Type: application/json'); // Garante que a resposta seja JSON

if (isset($_GET['curs_id'])) {
    $curs_id = (int) $_GET['curs_id'];

    try {
        $stmt = $conn->prepare("SELECT cco.coordenador_matricula AS CHAPA, col.NOMESOCIAL FROM curso_coordenador cco INNER JOIN $view_colaboradores col ON cco.coordenador_matricula = col.CHAPA WHERE cco.curs_id = :curs_id");
        $stmt->execute([':curs_id' => $curs_id]);
        $coordinators = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($coordinators); // Envia o JSON

    } catch (PDOException $e) {
        // Em caso de erro no banco de dados, retorna um JSON de erro
        http_response_code(500); // Erro interno do servidor
        echo json_encode(['error' => 'Erro ao buscar coordenadores do curso: ' . $e->getMessage()]);
        // Não use 'exit;' aqui a menos que você queira parar a execução completamente.
    }
} else {
    http_response_code(400); // Requisição inválida
    echo json_encode(['error' => 'ID do Curso é obrigatório.']);
}
// Não deve haver HTML ou outros caracteres após o fechamento da tag PHP, se houver.
// Se você tem a tag de fechamento 
