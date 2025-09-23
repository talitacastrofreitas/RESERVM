<?php
// Este arquivo conterá uma função para enviar e-mails aos coordenadores de um curso.
// Ele precisará ter acesso à conexão com o banco de dados ($conn) e à variável $view_colaboradores.

// Inclua as classes do PHPMailer. Ajuste o caminho conforme onde você instalou/colocou o PHPMailer.
// Se você usa Composer e seu autoload está configurado no arquivo principal (como o seu solicitacao.php já faz com 'require ../vendor/autoload.php;'),
// você não precisa destes requires individuais aqui.
// Caso contrário, ajuste os caminhos:
// require 'path/to/PHPMailer/src/PHPMailer.php';
// require 'path/to/PHPMailer/src/SMTP.php';
// require 'path/to/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include '../conexao/conexao.php';


// Função para enviar e-mails aos coordenadores de um curso
function sendCourseNotificationEmail($conn, $curs_id, $subject, $message, $view_colaboradores)
{
    $coordenador_emails = [];

    try {
        // 1. Buscar todos os e-mails dos coordenadores associados a este curso
        $stmt = $conn->prepare("SELECT
                                    col.EMAIL
                                FROM curso_coordenador cco
                                INNER JOIN colaboradores col ON cco.coordenador_matricula = col.CHAPA
                                WHERE cco.curs_id = :curs_id");
        $stmt->execute([':curs_id' => $curs_id]);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!empty($row['EMAIL'])) {
                $coordenador_emails[] = $row['EMAIL'];
            }
        }
    } catch (PDOException $e) {
        error_log("Erro PDO ao buscar e-mails dos coordenadores para o curso " . $curs_id . ": " . $e->getMessage());
        return false; // Indica falha na obtenção dos e-mails
    }

    if (empty($coordenador_emails)) {
        error_log("Nenhum e-mail de coordenador encontrado para o curso " . $curs_id);
        return false; // Ninguém para quem enviar
    }

    // 2. Preparar e enviar o e-mail usando suas configurações PHPMailer
    $mail = new PHPMailer(true); // true habilita exceções

    try {
        // Configurações do Servidor SMTP (Suas configurações do Gmail)
        // $mail->SMTPDebug = SMTP::DEBUG_OFF; // Mantenha OFF em produção. Use SMTP::DEBUG_SERVER para depurar.
        $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Mude para DEBUG_SERVER
        $mail->CharSet = "UTF-8";
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        // Ajuste para Gmail: porta 587 com STARTTLS (TLS explícito)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->Host = 'smtp.gmail.com';
        $mail->Username = 'talitacastrofreitas@gmail.com';
        $mail->Password = 'vmvottyedfzursuw'; // <<< SUBSTITUA PELA SUA SENHA REAL OU SENHA DE APLICATIVO DO GMAIL

        $mail->setFrom('talitacastrofreitas@gmail.com', 'RESERVM - Sistema de Reservas de Espaços');

        // Adicionar os destinatários (coordenadores)
        foreach ($coordenador_emails as $email) {
            $mail->addAddress($email);
        }

        // Conteúdo do E-mail
        $mail->isHTML(true); // Definir formato de e-mail como HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = strip_tags($message); // Versão em texto plano

        $mail->send();
        error_log("E-mail de notificação enviado com sucesso para coordenadores do curso " . $curs_id . " para: " . implode(', ', $coordenador_emails));
        return true; // E-mail enviado com sucesso
    } catch (Exception $e) {
        error_log("Erro PHPMailer ao enviar e-mail para curso " . $curs_id . ": {$e->getMessage()}");
        return false; // Falha no envio
    }
}
// // NUNCA FECHE A TAG PHP (