<?php
// Incluir sua conexão com o banco de dados e manipulação de sessão
include_once '../conexao/conexao.php';
session_start();

header('Content-Type: application/json');

// Definir o tempo limite do bloqueio em minutos (ex: 5 minutos)
$lock_timeout_minutes = 5;

// Obter o ID da solicitação da requisição
$solic_id = $_GET['solic_id'] ?? null;
if (!$solic_id) {
    echo json_encode(['error' => 'ID da Solicitação ausente.']);
    exit;
}

// Obter o ID do usuário atual da sessão
$current_user_id = $_SESSION['admin_id'] ?? null;
if (!$current_user_id) {
    echo json_encode(['error' => 'Usuário não logado.']);
    exit;
}

try {
    // Iniciar uma transação para garantir que a operação seja atômica
    $conn->beginTransaction();

    // Verificar se o registro está bloqueado
    $stmt = $conn->prepare("SELECT solic_editando_por, solic_editando_tempo, admin_nome FROM solicitacao
                            LEFT JOIN admin ON admin.admin_id = solicitacao.solic_editando_por
                            WHERE solic_id = ?");
    $stmt->execute([$solic_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $locked_by = $row['solic_editando_por'];
        $locked_time = new DateTime($row['solic_editando_tempo']);
        $current_time = new DateTime();
        $interval = $current_time->diff($locked_time);

        // Verificar se o bloqueio está ativo (não expirou) e é mantido por outro usuário
        if ($locked_by && $locked_by !== $current_user_id && $interval->i < $lock_timeout_minutes) {
            $conn->rollBack();
            echo json_encode([
                'status' => 'locked',
                'editor_name' => $row['admin_nome']
            ]);
            exit;
        }
    }

    // Se o registro não estiver bloqueado ou o bloqueio tiver expirado, adquira um novo bloqueio
    $stmt = $conn->prepare("UPDATE solicitacao SET solic_editando_por = ?, solic_editando_tempo = GETDATE() WHERE solic_id = ?");
    $stmt->execute([$current_user_id, $solic_id]);

    $conn->commit();

    echo json_encode(['status' => 'unlocked']);

} catch (PDOException $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log("Erro de banco de dados: " . $e->getMessage());
    echo json_encode(['error' => 'Erro de banco de dados.']);
}
?>