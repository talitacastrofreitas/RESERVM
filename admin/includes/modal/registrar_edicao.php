<?php
session_start();
require_once '../../../conexao/conexao.php'; // Ajuste o caminho conforme sua estrutura

header('Content-Type: application/json');

$solic_id = $_POST['solic_id'] ?? null;
$user_id = $_SESSION['reservm_admin_id'] ?? null;

if (!$solic_id || !$user_id) {
    echo json_encode(['success' => false, 'message' => 'Dados insuficientes.']);
    exit;
}

try {
    // 1. Verificar se a solicitação já está sendo editada por outro usuário
    $stmt = $conn->prepare("SELECT solic_editando_por, solic_editando_tempo FROM solicitacao WHERE solic_id = :solic_id");
    $stmt->execute([':solic_id' => $solic_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $editandoPorId = $result['solic_editando_por'];
    $editandoTempo = $result['solic_editando_tempo'];

    // Se a solicitação estiver sendo editada por outro usuário (ID diferente do atual) e o tempo for recente (ex: nos últimos 5 minutos)
    $agora = new DateTime();
    $tempoEdicao = new DateTime($editandoTempo);
    $intervalo = $agora->getTimestamp() - $tempoEdicao->getTimestamp();

    if ($editandoPorId && $editandoPorId != $user_id && $intervalo < 300) { // 300 segundos = 5 minutos
        // Buscar o nome do usuário que está editando
        $stmt_user = $conn->prepare("SELECT admin_nome FROM admin WHERE admin_id = :user_id");
        $stmt_user->execute([':user_id' => $editandoPorId]);
        $nomeUsuario = $stmt_user->fetchColumn();

        echo json_encode([
            'success' => false,
            'message' => "Esta reserva já está sendo editada por" . " <strong>" . $nomeUsuario . "</strong> " . "Por favor, tente novamente mais tarde."
        ]);
        exit;
    }

    // 2. Atualizar a solicitação com o usuário atual e o timestamp
    $stmt_update = $conn->prepare("UPDATE solicitacao SET solic_editando_por = :user_id, solic_editando_tempo = GETDATE() WHERE solic_id = :solic_id");
    $stmt_update->execute([
        ':user_id' => $user_id,
        ':solic_id' => $solic_id
    ]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao registrar a edição: ' . $e->getMessage()]);
}
?>