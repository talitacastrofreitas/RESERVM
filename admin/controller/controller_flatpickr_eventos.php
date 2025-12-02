<?php
// Limpa qualquer saída anterior para evitar corromper o JSON
ob_start();
header('Content-Type: application/json; charset=utf-8');

try {
    // --- TENTATIVA DE CONEXÃO INTELIGENTE ---
    // Tenta encontrar o arquivo de conexão em dois locais comuns:
    $caminhos_possiveis = [
        __DIR__ . '/../conexao/conexao.php',    // Se estiver na mesma pasta "admin"
        __DIR__ . '/../../conexao/conexao.php'  // Se estiver na raiz do projeto
    ];

    $conectou = false;
    foreach ($caminhos_possiveis as $caminho) {
        if (file_exists($caminho)) {
            include $caminho;
            $conectou = true;
            break;
        }
    }

    if (!$conectou) {
        throw new Exception("Arquivo de conexão não encontrado. Verifique se a pasta 'conexao' está correta.");
    }

    if (!isset($conn)) {
        throw new Exception("A variável \$conn não foi iniciada. Verifique o arquivo de conexão.");
    }

    // --- A CONSULTA ---
    $sql = "SELECT 
                e.dbloq_data, 
                e.dbloq_status,
                motivo.dbloqm_motivo
            FROM conf_dias_bloqueadas e
            LEFT JOIN conf_dias_bloqueadas_motivo AS motivo ON e.dbloq_motivo = motivo.dbloqm_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $eventos = [];
    foreach ($rows as $r) {
        $motivo = $r['dbloqm_motivo'] ?? 'Evento';
        
        // CORREÇÃO: Pega apenas os 10 primeiros caracteres (YYYY-MM-DD)
        // Isso resolve caso o banco esteja salvando como DATETIME (YYYY-MM-DD HH:MM:SS)
        $data_limpa = substr($r['dbloq_data'], 0, 10);

        // Tratamento de encoding
        $titulo = mb_check_encoding($motivo, 'UTF-8') ? $motivo : utf8_encode($motivo);

        $eventos[] = [
            'date' => $data_limpa,
            'title' => $titulo, 
            'ativo' => (int)$r['dbloq_status']
        ];
    }
    // Limpa o buffer e envia o JSON
    ob_end_clean();
    echo json_encode(['success' => true, 'events' => $eventos]);

} catch (Exception $e) {
    // Em caso de erro, limpa o buffer e envia o erro em JSON (e não HTML)
    ob_end_clean();
    http_response_code(500); // Avisa ao navegador que deu erro
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]); 
}
exit;
?>