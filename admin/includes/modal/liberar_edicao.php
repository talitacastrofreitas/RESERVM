<?php
session_start();
require_once '../../conexao/conexao.php';

header('Content-Type: application/json');

$solic_id = $_POST['solic_id'] ?? null;
$user_id = $_SESSION['reservm_admin_id'] ?? null;

if (!$solic_id || !$user_id) {
    echo json_encode(['success' => false, 'message' => 'Dados insuficientes.']);
    exit;
}

try {
    // Apenas limpa as colunas se o usuário que está fechando for o mesmo que as definiu
    $stmt_update = $conn->prepare("UPDATE solicitacao SET solic_editando_por = NULL, solic_editando_tempo = NULL WHERE solic_id = :solic_id AND solic_editando_por = :user_id");
    $stmt_update->execute([
        ':solic_id' => $solic_id,
        ':user_id' => $user_id
    ]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao liberar a edição: ' . $e->getMessage()]);
}
?>