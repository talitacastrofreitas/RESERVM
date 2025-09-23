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

      // POST
      $solic_id = trim($_POST['solic_id']);
      $sta_an_solic_codigo = trim($_POST['sta_an_solic_codigo']);
      $sta_an_user_email = trim($_POST['sta_an_user_email']);
      $sta_an_obs = trim($_POST['sta_an_obs']) !== '' ? nl2br(trim($_POST['sta_an_obs'])) : NULL;
      $num_status_defere = 5; // DEFERIDO
      $num_status_indefere = 6; // INDEFERIDO
    }

    $rvm_admin_id = $_SESSION['reservm_admin_id'];


    // -------------------------------
    // DEFERIR SOLICITAÇÃO
    // -------------------------------
    if ($acao === 'deferir') {

      $log_acao = 'Deferido';


      // LÓGICA ADICIONADA: Verificar se já existem reservas para esta solicitação
      $sql_check_reservas = "SELECT COUNT(*) FROM reservas WHERE res_solic_id = :solic_id";
      $stmt_check_reservas = $conn->prepare($sql_check_reservas);
      $stmt_check_reservas->execute([':solic_id' => $solic_id]);
      $num_reservas = $stmt_check_reservas->fetchColumn();

      if ($num_reservas > 0) {
        $num_status_defere = 4; // Se já houver reservas, mude para 'Reservado'
        // Você pode adicionar um log aqui também, se desejar, para registrar essa decisão.
      } else {
        $num_status_defere = 5; // Se não houver reservas, o status será 'Aguardando Reserva'
      }


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
        ':sta_an_status' => $num_status_defere,
        ':sta_an_obs' => $sta_an_obs,
        ':sta_an_user_id' => $rvm_admin_id
      ]);
      // -------------------------------


      // ALTERA O STATUS DA SOLICITAÇÃO
      $sql = "UPDATE
                    solicitacao_status
              SET        
                    solic_sta_status   = :solic_sta_status,
                    solic_sta_user_id  = :solic_sta_user_id,
                    solic_sta_data_cad = GETDATE()
              WHERE
                    solic_sta_solic_id = :solic_sta_solic_id";

      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':solic_sta_solic_id' => $solic_id,
        ':solic_sta_status' => $num_status_defere,
        ':solic_sta_user_id' => $rvm_admin_id
      ]);


      $mail = new PHPMailer(true); // ENVIA E-MAIL
      include '../conexao/email.php';

      $mail->addAddress($sta_an_user_email, 'RESERVM'); // E-MAIL DO USUÁRIO QUE RECEBERÁ A MENSAGEM
      // $mail->addAddress($$admin_email, 'RESERVM'); // E-MAIL DO SAAP
      $mail->isHTML(true);
      $mail->Subject = 'Solicitação deferida: ' . $sta_an_solic_codigo;

      // CORPO DO EMAIL
      include '../includes/email/email_header.php';

      $email_conteudo .= "
      <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
      
        <td style='padding: 2em 2rem; display: inline-block; width:100%;'>

        <p style='font-size: 1.188rem; font-weight: 500; margin: 0px 0px 20px 0px;'>
        <strong>SOLICITAÇÃO DEFERIDA</strong>
        </p>

        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 15px 0px;'>
        A solicitação de código:<strong>" . $sta_an_solic_codigo . "</strong> foi deferida.<br>
        </p>
 
        <a style='cursor: pointer;' href='$url_sistema'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 20px;' target='_blank'>Acesse o sistema</button></a>
        </td>
      </tr>";

      include '../includes/email/email_footer.php';

      $mail->Body = $email_conteudo;

      try {
        $mail->send();
      } catch (Exception $e) {
        $conn->rollBack();
        throw new Exception("Erro ao enviar o e-mail. Tente novamente!");
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
        ':sta_an_status' => $num_status_indefere,
        ':sta_an_obs' => $sta_an_obs,
        ':sta_an_user_id' => $rvm_admin_id
      ]);
      // -------------------------------


      // ALTERA O STATUS DA SOLICITAÇÃO
      $sql = "UPDATE
                    solicitacao_status
              SET        
                    solic_sta_status   = :solic_sta_status,
                    solic_sta_user_id  = :solic_sta_user_id,
                    solic_sta_data_cad = GETDATE()
              WHERE
                    solic_sta_solic_id = :solic_sta_solic_id";

      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':solic_sta_solic_id' => $solic_id,
        ':solic_sta_status' => $num_status_indefere,
        ':solic_sta_user_id' => $rvm_admin_id
      ]);


      $mail = new PHPMailer(true); // ENVIA E-MAIL
      include '../conexao/email.php';

      $mail->addAddress($sta_an_user_email, 'RESERVM'); // E-MAIL DO USUÁRIO QUE RECEBERÁ A MENSAGEM
      // $mail->addAddress($$admin_email, 'RESERVM'); // E-MAIL DO SAAP
      $mail->isHTML(true);
      $mail->Subject = 'Solicitação indeferida: ' . $sta_an_solic_codigo;

      // CORPO DO EMAIL
      include '../includes/email/email_header.php';

      $email_conteudo .= "
      <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
        <td style='padding: 2em 2rem; display: inline-block;  width:100%;'>

        <p style='font-size: 1.188rem; font-weight: 500; margin: 0px 0px 20px 0px;'>
        <strong>SOLICITAÇÃO INDEFERIDA</strong>
        </p>

        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 15px 0px;'>
        A solicitação de código:<strong>" . $sta_an_solic_codigo . "</strong> foi indeferida.<br>
        </p>

        <p style='font-size: 1rem; text-align: left; background: #F3F6F9; padding: 20px; margin: 20px 0px'>
        $sta_an_obs
        </p>

        <a style='cursor: pointer;' href='$url_sistema'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 20px;' target='_blank'>Acesse o sistema</button></a>
        </td>
      </tr>";

      include '../includes/email/email_footer.php';

      $mail->Body = $email_conteudo;
      $mail->send();
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
