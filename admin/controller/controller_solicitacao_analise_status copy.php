<?php
// session_start();
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

include '../conexao/conexao.php';

// NECESSÁRIO PARA ENVIAR O EMAIL
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $acao = $_POST['acao'];

    // ID do usuário logado (SAAP/Admin)
    $rvm_admin_id = $_SESSION['reservm_admin_id'];

    // ----------------------------------------------------------------
    // PASSO CRUCIAL: Buscar os e-mails dos administradores/operadores no banco de dados
    // ESTE BLOCO PRECISA SER REALIZADO PARA AS AÇÕES DE E-MAIL.
    // ----------------------------------------------------------------
    $admin_operator_emails = [];
    // Define o perfil 1 como Admin e 2 como Operador (SAAP). Ajuste conforme seu sistema.
    $sql_get_admins = "SELECT admin_email FROM admin WHERE admin_status = 1 AND (admin_perfil = 1 OR admin_perfil = 2)";
    $stmt_get_admins = $conn->prepare($sql_get_admins);
    $stmt_get_admins->execute();
    $results = $stmt_get_admins->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $row) {
      $admin_operator_emails[] = $row['admin_email'];
    }

    $coordenador_nome = 'SAAP'; // Nome padrão
    // Se a ação não for de deferir/indeferir (que já buscam nome), podemos usar o nome do admin logado.
    $coordenador_row = $conn->prepare("SELECT admin_nome FROM admin WHERE admin_id = :admin_id");
    $coordenador_row->execute([':admin_id' => $rvm_admin_id]);
    $coordenador_fetch = $coordenador_row->fetch(PDO::FETCH_ASSOC);
    if ($coordenador_fetch) {
      $coordenador_nome = htmlspecialchars($coordenador_fetch['admin_nome']);
    }
    // ----------------------------------------------------------------


    // SE DEFERIR OU INDEFERIR
    if ($acao === 'deferir' || $acao === 'indeferir') {

      // POST
      $solic_id = trim($_POST['solic_id']);
      $sta_an_solic_codigo = trim($_POST['sta_an_solic_codigo']);
      $sta_an_user_email = trim($_POST['sta_an_user_email']); // E-mail do solicitante (usuário)
      $sta_an_obs = trim($_POST['sta_an_obs']) !== '' ? nl2br(trim($_POST['sta_an_obs'])) : NULL;

      // Status Final
      // O SAAP ao deferir, define o status como RESERVADO (4) - Aprovação final
      $num_status_defere = 4; // RESERVADO
      $num_status_indefere = 6; // INDEFERIDO

      // --- Lógica de Deferimento para SAAP ---
      if ($acao === 'deferir') {
        $log_acao = 'Deferido SAAP';

        // Se houver reservas, o status final é 4 (RESERVADO). Se não houver, também é 4 (SAAP finalizou a análise).
        // A lógica de contar reservas pode ser usada para LOG ou E-MAIL, mas o status final é 4.
        $num_status_final = $num_status_defere;

        $sql = "INSERT INTO solicitacao_analise_status (
                                                                sta_an_solic_id, sta_an_status, sta_an_obs, sta_an_user_id, sta_an_data_cad, sta_an_data_upd
                                                                ) VALUES (
                                                                :sta_an_solic_id, :sta_an_status, :sta_an_obs, :sta_an_user_id, GETDATE(), GETDATE()
                                                                )";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
          ':sta_an_solic_id' => $solic_id,
          ':sta_an_status' => $num_status_final, // ID 4
          ':sta_an_obs' => $sta_an_obs,
          ':sta_an_user_id' => $rvm_admin_id
        ]);

        // ALTERA O STATUS DA SOLICITAÇÃO
        $sql = "UPDATE solicitacao_status SET solic_sta_status = :solic_sta_status, solic_sta_user_id = :solic_sta_user_id, solic_sta_data_cad = GETDATE()
                        WHERE solic_sta_solic_id = :solic_sta_solic_id";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
          ':solic_sta_solic_id' => $solic_id,
          ':solic_sta_status' => $num_status_final, // ID 4
          ':solic_sta_user_id' => $rvm_admin_id
        ]);

        // ENVIO DE E-MAIL PARA O SOLICITANTE (USUÁRIO)
        $mail = new PHPMailer(true);
        include '../conexao/email.php';
        $mail->addAddress($sta_an_user_email, 'RESERVM');
        $mail->isHTML(true);
        $mail->Subject = 'Solicitação deferida: ' . $sta_an_solic_codigo;
        include '../includes/email/email_header.php';
        $email_conteudo .= "
                <tr style='background-color: #ffffff; text-align: center; color: #515050; display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
                    <td style='padding: 2em 2rem; display: inline-block; width:100%;'>
                        <p style='font-size: 1.188rem; font-weight: 500; margin: 0px 0px 20px 0px;'>
                            <strong>SOLICITAÇÃO DEFERIDA</strong>
                        </p>
                        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 15px 0px;'>
                            A solicitação de código:<strong>" . $sta_an_solic_codigo . "</strong> foi deferida por <strong>" . $coordenador_nome . "</strong>.<br>
                        </p>
                        <a style='cursor: pointer;' href='$url_sistema'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 20px;' target='_blank'>Acesse o sistema</button></a>
                    </td>
                </tr>";
        include '../includes/email/email_footer.php';
        $mail->Body = $email_conteudo;

        try {
          $mail->send();
        } catch (Exception $e) {
          error_log("Erro ao enviar e-mail de deferimento para usuário: " . $e->getMessage());
          // Prossegue com a transação DB, loga o erro de email.
        }
      }
      // --- Fim Lógica Deferimento SAAP ---

      // --- Lógica de Indeferimento (Continua igual, usando ID 6) ---
      elseif ($acao === 'indeferir') {
        $log_acao = 'Indeferido SAAP';
        $num_status_final = $num_status_indefere; // ID 6

        $sql = "INSERT INTO solicitacao_analise_status (
                                                                sta_an_solic_id, sta_an_status, sta_an_obs, sta_an_user_id, sta_an_data_cad, sta_an_data_upd
                                                                ) VALUES (
                                                                :sta_an_solic_id, :sta_an_status, :sta_an_obs, :sta_an_user_id, GETDATE(), GETDATE()
                                                                )";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
          ':sta_an_solic_id' => $solic_id,
          ':sta_an_status' => $num_status_final,
          ':sta_an_obs' => $sta_an_obs,
          ':sta_an_user_id' => $rvm_admin_id
        ]);

        // ALTERA O STATUS DA SOLICITAÇÃO
        $sql = "UPDATE solicitacao_status SET solic_sta_status = :solic_sta_status, solic_sta_user_id = :solic_sta_user_id, solic_sta_data_cad = GETDATE()
                        WHERE solic_sta_solic_id = :solic_sta_solic_id";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
          ':solic_sta_solic_id' => $solic_id,
          ':solic_sta_status' => $num_status_final,
          ':solic_sta_user_id' => $rvm_admin_id
        ]);

        // ENVIO DE E-MAIL PARA O SOLICITANTE (USUÁRIO)
        $mail = new PHPMailer(true);
        include '../conexao/email.php';
        $mail->addAddress($sta_an_user_email, 'RESERVM');
        $mail->isHTML(true);
        $mail->Subject = 'Solicitação indeferida: ' . $sta_an_solic_codigo;
        include '../includes/email/email_header.php';
        $email_conteudo .= "
                <tr style='background-color: #ffffff; text-align: center; color: #515050; display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
                    <td style='padding: 2em 2rem; display: inline-block;  width:100%;'>
                        <p style='font-size: 1.188rem; font-weight: 500; margin: 0px 0px 20px 0px;'>
                            <strong>SOLICITAÇÃO INDEFERIDA</strong>
                        </p>
                        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 15px 0px;'>
                            A solicitação de código:<strong>" . $sta_an_solic_codigo . "</strong> foi indeferida.
                        </p>
                        <p style='font-size: 1rem; text-align: left; background: #F3F6F9; padding: 20px; margin: 20px 0px'>
                            $sta_an_obs
                        </p>
                        <a style='cursor: pointer;' href='$url_sistema'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 20px;' target='_blank'>Acesse o sistema</button></a>
                    </td>
                </tr>";
        include '../includes/email/email_footer.php';
        $mail->Body = $email_conteudo;

        try {
          $mail->send();
        } catch (Exception $e) {
          error_log("Erro ao enviar e-mail de indeferimento para usuário: " . $e->getMessage());
        }
      }
      // --- Fim Lógica Indeferimento SAAP ---


      // -------------------------------
      // INICIAR ANÁLISE SAAP (Status 2/5 -> Status 7)
      // -------------------------------
    } elseif ($acao === 'iniciar_analise_saap') {

      // VALIDA OS CAMPOS OBRIGATÓRIOS
      if (empty($_POST['solic_id']) || empty($_POST['sta_an_solic_codigo'])) {
        throw new Exception("ID da Solicitação e Código são obrigatórios!");
      }

      // POST
      $solic_id = trim($_POST['solic_id']);
      $sta_an_solic_codigo = trim($_POST['sta_an_solic_codigo']);
      $sta_an_obs = trim($_POST['sta_an_obs'] ?? '') !== '' ? nl2br(trim($_POST['sta_an_obs'] ?? '')) : NULL;

      $num_status_novo = 7; // EM ANÁLISE PELO SAAP
      $log_acao = 'Iniciada Análise SAAP';

      // 1. REGISTRA O NOVO STATUS NA TABELA DE HISTÓRICO
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
        ':sta_an_status' => $num_status_novo,
        ':sta_an_obs' => $sta_an_obs,
        ':sta_an_user_id' => $rvm_admin_id
      ]);

      // 2. ATUALIZA O STATUS NA TABELA PRINCIPAL
      $sql = "UPDATE solicitacao_status
                    SET        
                          solic_sta_status   = :solic_sta_status,
                          solic_sta_user_id  = :solic_sta_user_id,
                          solic_sta_data_cad = GETDATE()
                    WHERE
                          solic_sta_solic_id = :solic_sta_solic_id";

      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':solic_sta_solic_id' => $solic_id,
        ':solic_sta_status' => $num_status_novo,
        ':solic_sta_user_id' => $rvm_admin_id
      ]);
      // -------------------------------

      // -------------------------------
      // INICIAR ANÁLISE COORDENADOR (Já existe no código, mas precisa de lógica de Coordenador)
      // -------------------------------
    } elseif ($acao === 'iniciar_analise') {

      // VALIDA OS CAMPOS OBRIGATÓRIOS
      if (empty($_POST['solic_id']) || empty($_POST['sta_an_solic_codigo'])) {
        throw new Exception("Preencha os campos obrigatórios da solicitação!");
      }

      // POST
      $solic_id = trim($_POST['solic_id']);
      $sta_an_solic_codigo = trim($_POST['sta_an_solic_codigo']);
      $sta_an_obs = trim($_POST['sta_an_obs'] ?? '') !== '' ? nl2br(trim($_POST['sta_an_obs'] ?? '')) : NULL;

      // ** AÇÃO DO COORDENADOR **
      $num_status_novo = 3; // EM ANÁLISE PELO COORDENADOR
      $log_acao = 'Iniciada Análise Coordenador';

      // 1. REGISTRA O NOVO STATUS NA TABELA DE HISTÓRICO
      $sql = "INSERT INTO solicitacao_analise_status (
                                                            sta_an_solic_id, sta_an_status, sta_an_obs, sta_an_user_id, sta_an_data_cad, sta_an_data_upd
                                                            ) VALUES (
                                                            :sta_an_solic_id, :sta_an_status, :sta_an_obs, :sta_an_user_id, GETDATE(), GETDATE()
                                                            )";
      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':sta_an_solic_id' => $solic_id,
        ':sta_an_status' => $num_status_novo,
        ':sta_an_obs' => $sta_an_obs,
        ':sta_an_user_id' => $rvm_admin_id // Usa o ID do admin/SAAP logado
      ]);

      // 2. ATUALIZA O STATUS NA TABELA PRINCIPAL
      $sql = "UPDATE solicitacao_status
                    SET        
                          solic_sta_status   = :solic_sta_status,
                          solic_sta_user_id  = :solic_sta_user_id,
                          solic_sta_data_cad = GETDATE()
                    WHERE
                          solic_sta_solic_id = :solic_sta_solic_id";

      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':solic_sta_solic_id' => $solic_id,
        ':solic_sta_status' => $num_status_novo,
        ':solic_sta_user_id' => $rvm_admin_id
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
      ':user_id' => $rvm_admin_id
    ]);
    // -------------------------------

    $conn->commit(); // CONFIRMA A TRANSAÇÃO

    // CONFIGURAÇÃO DE MENSAGEM
    if ($acao === 'deferir') {
      $_SESSION["msg"] = "Solicitação deferida!";
    } elseif ($acao === 'indeferir') {
      $_SESSION["msg"] = "Solicitação indeferida!";
    } elseif ($acao === 'iniciar_analise_saap') {
      $_SESSION["msg"] = "Análise iniciada pelo SAAP!";
    } elseif ($acao === 'iniciar_analise') {
      $_SESSION["msg"] = "Análise iniciada pelo Coordenador!";
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