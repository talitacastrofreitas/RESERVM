<?php
session_start();
include '../conexao/conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['solic_id_cancelar']) && isset($_POST['motivo_cancelamento_solicitacao'])) {
    $solic_id = filter_var($_POST['solic_id_cancelar'], FILTER_SANITIZE_STRING);
    $motivo_cancelamento = filter_var($_POST['motivo_cancelamento_solicitacao'], FILTER_SANITIZE_STRING);

    if (empty($solic_id) || empty($motivo_cancelamento)) {
        $_SESSION["erro"] = "Dados incompletos para o cancelamento da solicitação.";
        header('Location: ../admin/solicitacoes_saap.php');
        exit;
    }

    try {
        $conn->beginTransaction();

        // 1. Mudar o status da SOLICITAÇÃO para "Cancelado" (status 8)
        $stmt_solicitacao = $conn->prepare("UPDATE solicitacao_status SET solic_sta_status = 8, solic_motivo_cancelamento = :motivo WHERE solic_sta_solic_id = :solic_id");
        $stmt_solicitacao->bindParam(':motivo', $motivo_cancelamento);
        $stmt_solicitacao->bindParam(':solic_id', $solic_id);
        $stmt_solicitacao->execute();

        // 2. Mudar o status das RESERVAS para "Cancelado" (status 8)
        $stmt_reservas = $conn->prepare("UPDATE reservas SET res_status = 8, res_motivo_cancelamento = :motivo WHERE res_solic_id = :solic_id");
        $stmt_reservas->bindParam(':motivo', $motivo_cancelamento);
        $stmt_reservas->bindParam(':solic_id', $solic_id);
        $stmt_reservas->execute();

        $conn->commit();
        $_SESSION["msg"] = "Solicitação e reservas foram canceladas com sucesso pelo SAAP!";
        header('Location: ../admin/solicitacoes_saap.php');
        exit;
    } catch (PDOException $e) {
        $conn->rollBack();
        $_SESSION["erro"] = "Erro ao processar o cancelamento da solicitação: " . $e->getMessage();
        header('Location: ../admin/solicitacoes_saap.php');
        exit;
    }
} else {
    $_SESSION["erro"] = "Requisição inválida. Motivo do cancelamento não foi enviado.";
    header('Location: ../admin/solicitacoes_saap.php');
    exit;
}
