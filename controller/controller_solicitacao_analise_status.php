<?php
// session_start();
include '../conexao/conexao.php';

// NECESSÁRIO PARA ENVIAR O EMAIL

use FontLib\Table\Type\post;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    try {
        $conn->beginTransaction(); // INICIA A TRANSAÇÃO
        $acao = $_POST['acao'];

        // SE DEFERIR OU INDEFERIR
        if ($acao === 'deferir' || $acao === 'indeferir') {

            // VALIDA OS CAMPOS OBRIGATÓRIOS
            if (empty($_POST['solic_id']) || empty($_POST['sta_an_solic_codigo'])) {
                throw new Exception("Preencha os campos obrigatórios da solicitação!");
            }

            // POST
            $solic_id = trim($_POST['solic_id']);
            $sta_an_solic_codigo = trim($_POST['sta_an_solic_codigo']);
            $sta_an_obs = trim($_POST['sta_an_obs']) !== '' ? nl2br(trim($_POST['sta_an_obs'])) : NULL;

            $num_status_defere = 5; // DEFERIDO
            $num_status_indefere = 6; // INDEFERIDO
        }

        $rvm_user_id = $_SESSION['reservm_user_id'];


        // ----------------------------------------------------------------
        // PASSO CRUCIAL: Buscar os e-mails dos administradores/operadores no banco de dados
        // ----------------------------------------------------------------
        $admin_operator_emails = [];
        $sql_get_admins = "SELECT admin_email FROM admin WHERE admin_status = 1 AND (admin_perfil = 1 OR admin_perfil = 2)";
        $stmt_get_admins = $conn->prepare($sql_get_admins);
        $stmt_get_admins->execute();
        $results = $stmt_get_admins->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $row) {
            $admin_operator_emails[] = $row['admin_email']; // Corrigido para 'admin_email'
        }

        if (empty($admin_operator_emails)) {
            error_log("Nenhum e-mail de administrador/operador encontrado para notificações de solicitação (ID: " . ($solic_id ?? 'N/A') . "). Verifique a tabela 'admin'.");
        }
        // ----------------------------------------------------------------
        $coordenador_nome = 'Desconhecido'; // Valor padrão caso não encontre
        $sql_get_coordenador_nome = "SELECT user_nome FROM usuarios WHERE user_id = :user_id"; // Assumindo tabela 'usuarios'
        $stmt_get_coordenador_nome = $conn->prepare($sql_get_coordenador_nome);
        $stmt_get_coordenador_nome->execute([':user_id' => $rvm_user_id]);
        $coordenador_row = $stmt_get_coordenador_nome->fetch(PDO::FETCH_ASSOC);

        if ($coordenador_row) {
            $coordenador_nome = htmlspecialchars($coordenador_row['user_nome']);
        }
        // ----------------------------------------------------------------


        // -------------------------------
        // DEFERIR SOLICITAÇÃO
        // -------------------------------
        if ($acao === 'deferir') {

            $log_acao = 'Deferido';

            // 1. REGISTRA O NOVO STATUS NA TABELA DE HISTÓRICO
            $sql = "INSERT INTO solicitacao_analise_status (sta_an_solic_id, sta_an_status, sta_an_obs, sta_an_user_id, sta_an_data_cad, sta_an_data_upd) 
                    VALUES (:sta_an_solic_id, :sta_an_status, :sta_an_obs, :sta_an_user_id, GETDATE(), GETDATE())";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':sta_an_solic_id' => $solic_id,
                ':sta_an_status' => $num_status_defere,
                ':sta_an_obs' => $sta_an_obs,
                ':sta_an_user_id' => $rvm_user_id
            ]);

            // 2. ALTERA O STATUS DA SOLICITAÇÃO
            $sql = "UPDATE solicitacao_status SET solic_sta_status = :solic_sta_status, solic_sta_user_id = :solic_sta_user_id, solic_sta_data_cad = GETDATE()
                    WHERE solic_sta_solic_id = :solic_sta_solic_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':solic_sta_solic_id' => $solic_id,
                ':solic_sta_status' => $num_status_defere,
                ':solic_sta_user_id' => $rvm_user_id
            ]);
            // -------------------------------


            // ----------------------------------------------------------------
            // Enviar e-mail para administradores/operadores (e SOMENTE eles)
            // ----------------------------------------------------------------
            if (!empty($admin_operator_emails)) { // Só tenta enviar se houver destinatários
                $mail = new PHPMailer(true);
                include '../conexao/email.php'; // Inclui as configurações do seu servidor SMTP

                // Adicionar destinatários
                foreach ($admin_operator_emails as $email) {
                    $mail->addAddress($email);
                }

                $mail->isHTML(true);
                $mail->Subject = 'Solicitação aprovada: ' . $sta_an_solic_codigo;

                $email_conteudo = ''; // Limpa a variável
                include '../includes/email/email_header.php';

                $email_conteudo .= "
                <tr style='background-color: #ffffff; text-align: center; color: #515050;   display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
                    <td align='center' width='800px' style='padding: 2em 2rem; display: inline-block;'>
                        <p style='font-size: 1.188rem; font-weight: 500; margin: 0px 0px 20px 0px;'>
                            <strong>SOLICITAÇÃO APROVADA</strong>
                        </p>
                        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 15px 0px;'>
                            A solicitação de código:<strong>" . $sta_an_solic_codigo . "</strong> foi aprovada pelo coordenador(a) <strong>" . $coordenador_nome . "</strong>.<br>
                        </p>
                        <a style='cursor: pointer;' href='$url_sistema'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 20px;' target='_blank'>Acesse o sistema</button></a>
                    </td>
                </tr>";
                include '../includes/email/email_footer.php';

                $mail->Body = $email_conteudo;
                try {
                    $mail->send();
                } catch (Exception $e) {
                    error_log("Erro ao enviar e-mail de deferimento para admins: " . $e->getMessage());
                }

                // CRUCIAL: Destrói o objeto e a variável de conteúdo após o envio
                unset($mail);
                unset($email_conteudo);
            }
            // -------------------------------


            // -------------------------------
            // INDEFERIR SOLICITAÇÃO
            // -------------------------------
        } elseif ($acao === 'indeferir') {
            $log_acao = 'Indeferido SAAP';
            $num_status_final = $num_status_indefere; // ID 6

            // 1. REGISTRA O NOVO STATUS NA TABELA DE HISTÓRICO
            $sql = "INSERT INTO solicitacao_analise_status (sta_an_solic_id, sta_an_status, sta_an_obs, sta_an_user_id, sta_an_data_cad, sta_an_data_upd) 
                    VALUES (:sta_an_solic_id, :sta_an_status, :sta_an_obs, :sta_an_user_id, GETDATE(), GETDATE())";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':sta_an_solic_id' => $solic_id, ':sta_an_status' => $num_status_final, ':sta_an_obs' => $sta_an_obs, ':sta_an_user_id' => $rvm_user_id]);

            // 2. ALTERA O STATUS DA SOLICITAÇÃO
            $sql = "UPDATE solicitacao_status SET solic_sta_status = :solic_sta_status, solic_sta_user_id = :solic_sta_user_id, solic_sta_data_cad = GETDATE()
                WHERE solic_sta_solic_id = :solic_sta_solic_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':solic_sta_solic_id' => $solic_id, ':solic_sta_status' => $num_status_final, ':solic_sta_user_id' => $rvm_user_id]);


            // ======================================================================
            // ENVIO DE E-MAIL 1: PARA O SOLICITANTE (Com Motivo SIMPLES)
            // ======================================================================
            // if (!empty($sta_an_user_email)) {
            //     $mail = new PHPMailer(true); // Instancia o objeto $mail
            //     include '../conexao/email.php'; // Configuração SMTP

            //     $mail->addAddress($sta_an_user_email, 'SOLICITANTE RESERVM');
            //     $mail->isHTML(true);
            //     $mail->Subject = 'URGENTE: Solicitacao Indeferida - ' . $sta_an_solic_codigo;

            //     $email_conteudo = ''; // Limpa a variável
            //     include '../includes/email/email_header.php';

            //     // Corpo do Solicitante (Mensagem genérica)
            //     $email_conteudo .= "
            //     <tr style='background-color: #ffffff; text-align: center; color: #515050; display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
            //         <td style='padding: 2em 2rem; display: inline-block;  width:100%;'>
            //             <p style='font-size: 1.188rem; font-weight: 500; margin: 0px 0px 20px 0px;'>
            //                 <strong>SOLICITAÇÃO INDEFERIDA</strong>
            //             </p>
            //             <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 15px 0px; text-align: left;'>
            //                 Prezado(a) solicitante,
            //             </p>
            //             <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 15px 0px; text-align: left;'>
            //                 A sua solicitação de código <strong>" . $sta_an_solic_codigo . "</strong> foi indeferida pelo(a) administrador(a) <strong>" . $coordenador_nome . "</strong>.
            //                 <br>O motivo foi: " . ($sta_an_obs ?: 'Nenhuma observação foi fornecida.') . "
            //             </p>
            //             <a style='cursor: pointer;' href='$url_sistema'><button style='background: #C4453E; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 20px;' target='_blank'>Acesse o sistema</button></a>
            //         </td>
            //     </tr>";

            //     include '../includes/email/email_footer.php';

            //     $mail->Body = $email_conteudo;

            //     try {
            //         $mail->send();
            //     } catch (Exception $e) {
            //         error_log("Erro ao enviar e-mail de indeferimento para usuário: " . $e->getMessage());
            //     }

            //     // CRUCIAL: Destrói o objeto e a variável de conteúdo após o envio
            //     unset($mail);
            //     unset($email_conteudo);
            // }

            // ======================================================================
            // ENVIO DE E-MAIL 2: PARA ADMINS/SAAP (Notificação Interna)
            // ======================================================================
            if (!empty($admin_operator_emails)) {
                $mail = new PHPMailer(true); // Instancia NOVO objeto $mail
                include '../conexao/email.php'; // Configuração SMTP

                // Adicionar todos os admins/operadores
                foreach ($admin_operator_emails as $email) {
                    $mail->addAddress($email);
                }

                $mail->isHTML(true);
                $mail->Subject = 'Solicitação Indeferida ' . $sta_an_solic_codigo;

                $email_conteudo = ''; // Limpa a variável
                include '../includes/email/email_header.php';

                // Corpo do Admin
                $email_conteudo .= "
                <tr style='background-color: #ffffff; text-align: center; color: #515050; display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
                    <td style='padding: 2em 2rem; display: inline-block; width:100%;'>
                        <p style='font-size: 1.188rem; font-weight: 500; margin: 0px 0px 20px 0px;'>
                            <strong>SOLICITAÇÃO INDEFERIDA</strong>
                        </p>
                        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 15px 0px;'>
                            A solicitação <strong>" . $sta_an_solic_codigo . "</strong> foi INDEFERIDA por <strong>" . $coordenador_nome . "</strong>.
                        </p>

                           <p style='font-size: 1rem; text-align: left; background: #F3F6F9; padding: 20px; margin: 20px 0px'>
                        <strong>Motivo do Indeferimento:</strong><br>" . ($sta_an_obs ?: 'Nenhuma observação foi fornecida.') . "
                    </p>
                        <a style='cursor: pointer;' href='$url_sistema'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 20px;' target='_blank'>Acesse o sistema</button></a>
                    </td>
                </tr>";

                include '../includes/email/email_footer.php';

                $mail->Body = $email_conteudo;

                try {
                    $mail->send();
                } catch (Exception $e) {
                    error_log("Erro ao enviar e-mail de notificação para admins: " . $e->getMessage());
                }

                // Não precisa de unset aqui, pois é o último envio de e-mail do script
            }

            // --- Fim Lógica de E-mail ---

            // -------------------------------
            // INICIAR ANÁLISE (Status 2 -> Status 3)
            // -------------------------------
        } elseif ($acao === 'iniciar_analise') {

            // VALIDA OS CAMPOS OBRIGATÓRIOS
            if (empty($_POST['solic_id']) || empty($_POST['sta_an_solic_codigo'])) {
                throw new Exception("Preencha os campos obrigatórios da solicitação!");
            }

            // POST
            $solic_id = trim($_POST['solic_id']);
            $sta_an_solic_codigo = trim($_POST['sta_an_solic_codigo']);
            $sta_an_obs = trim($_POST['sta_an_obs']) !== '' ? nl2br(trim($_POST['sta_an_obs'])) : NULL;

            $num_status_novo = 3; // EM ANÁLISE PELO COORDENADOR
            $log_acao = 'Iniciada Análise Coordenador';

            // 1. REGISTRA O NOVO STATUS NA TABELA DE HISTÓRICO
            $sql = "INSERT INTO solicitacao_analise_status (sta_an_solic_id, sta_an_status, sta_an_obs, sta_an_user_id, sta_an_data_cad, sta_an_data_upd) 
                    VALUES (:sta_an_solic_id, :sta_an_status, :sta_an_obs, :sta_an_user_id, GETDATE(), GETDATE())";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':sta_an_solic_id' => $solic_id,
                ':sta_an_status' => $num_status_novo,
                ':sta_an_obs' => $sta_an_obs,
                ':sta_an_user_id' => $rvm_user_id
            ]);

            // 2. ATUALIZA O STATUS NA TABELA PRINCIPAL
            $sql = "UPDATE solicitacao_status SET solic_sta_status = :solic_sta_status, solic_sta_user_id = :solic_sta_user_id, solic_sta_data_cad = GETDATE()
                    WHERE solic_sta_solic_id = :solic_sta_solic_id";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':solic_sta_solic_id' => $solic_id,
                ':solic_sta_status' => $num_status_novo,
                ':solic_sta_user_id' => $rvm_user_id
            ]);
            // -------------------------------


            // -------------------------------
            // AÇÃO INVÁLIDA
            // -------------------------------
        } else {
            throw new Exception("Ação inválida.");
        }

        // REGISTRA NO LOG
        $log_dados = ['POST' => $_POST, 'GET' => $_GET, 'FILES' => $_FILES];
        $sqlLog = "INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data)
                    VALUES (:modulo, upper(:acao), :acao_id, :dados, :user_id, GETDATE())";
        $stmtLog = $conn->prepare($sqlLog);
        $stmtLog->execute([
            ':modulo' => 'SOLICITAÇÃO STATUS',
            ':acao' => $log_acao,
            ':acao_id' => $solic_id,
            ':dados' => json_encode($log_dados, JSON_UNESCAPED_UNICODE),
            ':user_id' => $rvm_user_id // ID do usuário (coordenador) que realizou a ação
        ]);
        // -------------------------------

        $conn->commit(); // CONFIRMA A TRANSAÇÃO

        // CONFIGURAÇÃO DE MENSAGEM
        if ($acao === 'deferir') {
            $_SESSION["msg"] = "Solicitação deferida!";
        } elseif ($acao === 'indeferir') {
            $_SESSION["msg"] = "Solicitação indeferida!";
        }
        // -------------------------------
        header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        exit;
        // -------------------------------

    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION["erro"] = $e->getMessage();
        header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        exit;
    }
} else {
    $_SESSION["erro"] = "Requisição inválida.";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    exit;
}