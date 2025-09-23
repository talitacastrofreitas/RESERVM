<?php
// Inclua seu arquivo de conexão com o banco de dados
include '../conexao/conexao.php'; // **IMPORTANTE: Confirme se este caminho está correto!**



header('Content-Type: application/json'); // Garante que a resposta seja JSON

$space_id = $_GET['space_id'] ?? null;

// Adicione esta linha para verificar o ID recebido
error_log("ID do espaço recebido em get_space_capacity.php: " . $space_id);

if ($space_id) {
    try {
        // Se esp_id no seu banco for int, mas o valor recebido é hash, AQUI ESTÁ O PROBLEMA.
        // A sua query precisa estar de acordo com o tipo de dado da coluna esp_id.
        $stmt = $conn->prepare("SELECT esp_quant_maxima FROM espaco WHERE esp_id = :space_id");
        // Se o ID for INT, use PDO::PARAM_INT. Se for string/hash, use PDO::PARAM_STR.
        $stmt->bindParam(':space_id', $space_id, PDO::PARAM_STR); // Ajuste PDO::PARAM_STR ou PDO::PARAM_INT
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Adicione estas linhas para verificar o resultado da query
        error_log("Resultado da query em get_space_capacity.php: " . print_r($result, true));

        if ($result) {
            echo json_encode(['success' => true, 'max_capacity' => $result['esp_quant_maxima']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Espaço não encontrado no banco de dados.']);
        }
    } catch (PDOException $e) {
        // MUITO IMPORTANTE: logar o erro REAL do banco de dados
        error_log("Erro de PDO em get_space_capacity.php: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Erro interno do servidor ao consultar o banco.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID do espaço não fornecido na URL.']);
}
?>