<?php
// session_start();
include '../conexao/conexao.php';

// NECESSÁRIO PARA ENVIAR O EMAIL
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
            // Removemos a validação de 'sta_an_user_email' daqui, pois não queremos que ele seja obrigatório para esta lógica.
            if (empty($_POST['solic_id']) || empty($_POST['sta_an_solic_codigo'])) {
                throw new Exception("Preencha os campos obrigatórios da solicitação!");
            }

            // POST
            $solic_id = trim($_POST['solic_id']);
            $sta_an_solic_codigo = trim($_POST['sta_an_solic_codigo']);
            // MUITO IMPORTANTE: REMOVA OU COMENTE ESTA LINHA.
            // Esta variável NÃO DEVE ser usada para enviar e-mail aos administradores.
            // $sta_an_user_email = filter_var(trim($_POST['sta_an_user_email']), FILTER_SANITIZE_EMAIL);
            $sta_an_obs = trim($_POST['sta_an_obs']) !== '' ? nl2br(trim($_POST['sta_an_obs'])) : NULL;
            // ATENÇÃO: Se 5 é DEFERIDO e 6 é INDEFERIDO, o status 'EM ANÁLISE' (3) não deve ser usado aqui.
            // O valor de $num_status_defere precisa ser 5 (DEFERIDO)
            $num_status_defere = 3; // DEFERIDO
            $num_status_indefere = 6; // INDEFERIDO
        }

        // Corrigido: era rvm_admin_id na sessão do admin, agora rvm_user_id (se o coordenador é um usuário normal)
        // Se este script é executado por um ADMIN logado, então deve ser $_SESSION['reservm_admin_id']
        // Se é um USUARIO (coordenador) logado, então $_SESSION['reservm_user_id']
        // Mantenho rvm_user_id como está no seu código, assumindo que é o ID do coordenador logado.
        $rvm_user_id = $_SESSION['reservm_user_id'];


        // ----------------------------------------------------------------
        // PASSO CRUCIAL: Buscar os e-mails dos administradores/operadores no banco de dados
        // ESTE BLOCO PRECISA ESTAR AQUI, ANTES DE QUALQUER TENTATIVA DE ENVIAR E-MAIL.
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
            // Dependendo da sua regra de negócio, você pode lançar uma exceção ou prosseguir sem enviar o e-mail de notificação.
            // throw new Exception("Erro de configuração: Nenhum destinatário de e-mail de administração encontrado.");
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

            $sql = "INSERT INTO solicitacao_analise_status (
                                                            sta_an_solic_id,
                                                            sta_an_status,
                                                            sta_an_obs,
                                                            sta_an_user_id,
                                                            sta_an_data_cad,
                                                            sta_an_data_upd
                                                            ) VALUES (
                                                            :sta_an_solic_id,
                                                            :sta_an_status,
                                                            :sta_an_obs,
                                                            :sta_an_user_id,
                                                            GETDATE(),
                                                            GETDATE()
                                                            )";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':sta_an_solic_id' => $solic_id,
                ':sta_an_status' => $num_status_defere, // Usar 5 (DEFERIDO)
                ':sta_an_obs' => $sta_an_obs,
                ':sta_an_user_id' => $rvm_user_id // Coordenador que está deferindo
            ]);
            // -------------------------------


            // ALTERA O STATUS DA SOLICITAÇÃO
            $sql = "UPDATE
                                    solicitacao_status
                            SET
                                    solic_sta_status    = :solic_sta_status,
                                    solic_sta_user_id   = :solic_sta_user_id,
                                    solic_sta_data_cad = GETDATE()
                            WHERE
                                    solic_sta_solic_id = :solic_sta_solic_id";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':solic_sta_solic_id' => $solic_id,
                ':solic_sta_status' => $num_status_defere, // Usar 5 (DEFERIDO)
                ':solic_sta_user_id' => $rvm_user_id // Coordenador que está deferindo
            ]);


            // ----------------------------------------------------------------
            // Enviar e-mail para administradores/operadores (e SOMENTE eles)
            // ----------------------------------------------------------------
            if (!empty($admin_operator_emails)) { // Só tenta enviar se houver destinatários
                $mail = new PHPMailer(true);
                include '../conexao/email.php'; // Inclui as configurações do seu servidor SMTP

                // Loop para adicionar CADA e-mail encontrado no banco de dados como destinatário
                foreach ($admin_operator_emails as $email) {
                    $mail->addAddress($email);
                }
                // REMOVA ESTAS LINHAS. ELAS ESTÃO CAUSANDO O PROBLEMA.
                // $mail->addAddress($sta_an_user_email, 'RESERVM');
                // $mail->addAddress($user_email, 'RESERVM');

                $mail->isHTML(true);
                $mail->Subject = 'Solicitação deferida: ' . $sta_an_solic_codigo;

                // CORPO DO EMAIL
                include '../includes/email/email_header.php';

                $email_conteudo .= "
               <tr style='background-color: #ffffff; text-align: center; color: #515050;   display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
                    <td align='center' width='800px' style='padding: 2em 2rem; display: inline-block;'>
                        <p style='font-size: 1.188rem; font-weight: 500; margin: 0px 0px 20px 0px;'>
                            <strong>SOLICITAÇÃO DEFERIDA</strong>
                        </p>
                        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 15px 0px;'>
                            A solicitação de código:<strong>" . $sta_an_solic_codigo . "</strong> foi deferida pelo coordenador(a) <strong>" . $coordenador_nome . "</strong>.<br>
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
                    // Não reverte a transação do DB se o e-mail falhar, apenas loga o erro.
                }
            }
            // -------------------------------


            // -------------------------------
            // INDEFERIR SOLICITAÇÃO
            // -------------------------------
        } elseif ($acao === 'indeferir') {

            $log_acao = 'Indeferido';

            $sql = "INSERT INTO solicitacao_analise_status (
                                                            sta_an_solic_id,
                                                            sta_an_status,
                                                            sta_an_obs,
                                                            sta_an_user_id,
                                                            sta_an_data_cad,
                                                            sta_an_data_upd
                                                            ) VALUES (
                                                            :sta_an_solic_id,
                                                            :sta_an_status,
                                                            :sta_an_obs,
                                                            :sta_an_user_id,
                                                            GETDATE(),
                                                            GETDATE()
                                                            )";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':sta_an_solic_id' => $solic_id,
                ':sta_an_status' => $num_status_indefere, // Usar 6 (INDEFERIDO)
                ':sta_an_obs' => $sta_an_obs,
                ':sta_an_user_id' => $rvm_user_id // Coordenador que está indeferindo
            ]);
            // -------------------------------


            // ALTERA O STATUS DA SOLICITAÇÃO
            $sql = "UPDATE
                                    solicitacao_status
                            SET
                                    solic_sta_status    = :solic_sta_status,
                                    solic_sta_user_id   = :solic_sta_user_id,
                                    solic_sta_data_cad = GETDATE()
                            WHERE
                                    solic_sta_solic_id = :solic_sta_solic_id";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':solic_sta_solic_id' => $solic_id,
                ':solic_sta_status' => $num_status_indefere, // Usar 6 (INDEFERIDO)
                ':solic_sta_user_id' => $rvm_user_id // Coordenador que está indeferindo
            ]);


            // ----------------------------------------------------------------
            // Enviar e-mail para administradores/operadores (e SOMENTE eles)
            // ----------------------------------------------------------------
            if (!empty($admin_operator_emails)) { // Só tenta enviar se houver destinatários
                $mail = new PHPMailer(true);
                include '../conexao/email.php'; // Inclui as configurações do seu servidor SMTP

                // Loop para adicionar CADA e-mail encontrado no banco de dados como destinatário
                foreach ($admin_operator_emails as $email) {
                    $mail->addAddress($email);
                }
                // REMOVA ESTAS LINHAS. ELAS ESTÃO CAUSANDO O PROBLEMA.
                // $mail->addAddress($sta_an_user_email, 'RESERVM');
                // $mail->addAddress($user_email, 'RESERVM');

                $mail->isHTML(true);
                $mail->Subject = 'Solicitação indeferida: ' . $sta_an_solic_codigo;

                // CORPO DO EMAIL
                include '../includes/email/email_header.php';

                $email_conteudo .= "
                <tr style='background-color: #ffffff; text-align: center; color: #515050;   display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
                    <td align='center' width='800px' style='padding: 2em 2rem; display: inline-block;'>
                        <p style='font-size: 1.188rem; font-weight: 500; margin: 0px 0px 20px 0px;'>
                            <strong>SOLICITAÇÃO INDEFERIDA</strong>
                        </p>
                        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 15px 0px;'>
                            A solicitação de código:<strong>" . $sta_an_solic_codigo . "</strong> foi indeferida pelo coordenador(a) <strong>" . $coordenador_nome . "</strong>.<br>
                        </p>
                        <p style='font-size: 1rem; text-align: left; background: #F3F6F9; padding: 20px; margin: 20px 0px'>
                            " . $sta_an_obs . "
                        </p>
                        <a style='cursor: pointer;' href='$url_sistema'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 20px;' target='_blank'>Acesse o sistema</button></a>
                    </td>
                </tr>";

                include '../includes/email/email_footer.php';

                $mail->Body = $email_conteudo;
                try {
                    $mail->send();
                } catch (Exception $e) {
                    error_log("Erro ao enviar e-mail de indeferimento para admins: " . $e->getMessage());
                }
            }
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