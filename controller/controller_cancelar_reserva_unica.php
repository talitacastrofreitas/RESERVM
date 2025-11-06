<?php
session_start();
// Inclua seu arquivo de configuração do banco de dados e funções
include '../conexao/conexao.php';
// include 'includes/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['res_id_cancelar']) && isset($_POST['motivo_cancelamento_reserva'])) {
    $res_id = filter_var($_POST['res_id_cancelar'], FILTER_SANITIZE_STRING);
    $motivo_cancelamento = filter_var($_POST['motivo_cancelamento_reserva'], FILTER_SANITIZE_STRING);

    if (empty($res_id) || empty($motivo_cancelamento)) {
        $_SESSION["erro"] = "Dados incompletos para o cancelamento.";
        header('Location: ../programacao_diaria.php');
        exit;
    }

    try {
        $conn->beginTransaction();

        // 1. Atualizar o status da reserva única
        // Assumindo '6' é o status para 'CANCELADA' na sua tabela 'reservas'
        $stmt_reserva = $conn->prepare("UPDATE reservas SET res_status = 7, res_motivo_cancelamento = :motivo WHERE res_id = :res_id");
        $stmt_reserva->bindParam(':motivo', $motivo_cancelamento);
        $stmt_reserva->bindParam(':res_id', $res_id);
        $stmt_reserva->execute();

        // 2. Notificar Administradores do SAAP via Email
        // Busque os detalhes da reserva para o e-mail
        $stmt_details = $conn->prepare("SELECT res_codigo, res_professor FROM reservas WHERE res_id = :res_id");
        $stmt_details->bindParam(':res_id', $res_id);
        $stmt_details->execute();
        $reserva_details = $stmt_details->fetch(PDO::FETCH_ASSOC);

        $subject = "CANCELAMENTO DE RESERVA #{$reserva_details['res_codigo']}";
        $body = "A reserva #{$reserva_details['res_codigo']} do professor {$reserva_details['res_professor']} foi solicitada para cancelamento.\\n\\n";
        $body .= "Motivo do Cancelamento: " . $motivo_cancelamento . "\\n\\n";
        $body .= "Acesse o painel para aprovar ou rejeitar o cancelamento.";

        // Busque os e-mails dos administradores para enviar a notificação
        $admin_emails = [];
        $stmt_admins = $conn->query("SELECT admin_email FROM admin WHERE nivel_acesso = 1");
        while ($row_admin = $stmt_admins->fetch(PDO::FETCH_ASSOC)) {
            $admin_emails[] = $row_admin['admin_email'];
        }

        foreach ($admin_emails as $email) {
            // Assumindo que você tem uma função para envio de e-mail
            // enviarEmail($email, $subject, $body);
        }

        $conn->commit();
        $_SESSION["msg"] = "Solicitação de cancelamento enviada para o SAAP. A reserva será removida da programação diária após a aprovação.";;
        header('Location: ../programacao_diaria.php');
        exit;
        exit;
    } catch (PDOException $e) {
        $conn->rollBack();
        $_SESSION["erro"] = "Erro ao processar cancelamento de reserva única: " . $e->getMessage();
        header('Location: ../programacao_diaria.php');
        exit;
    }
} else {
    $_SESSION["erro"] = "Requisição inválida.";
    header('Location: ../programacao_diaria.php');
    exit;
}
