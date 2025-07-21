<?php
session_start();
include '../conexao/conexao.php';

// NECESSÁRIO PARA ENVIAR O EMAIL
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                                    INICIAR ANÁLISE
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "ini_analise") {

  $sta_an_prop_id = $_GET['prop_id'];
  $num_status     = 3; // EM ANÁLISE

  try {
    $sql = "INSERT INTO propostas_analise_status (
                                                    sta_an_prop_id,
                                                    sta_an_status,
                                                    sta_an_user_id,
                                                    sta_an_data_cad,
                                                    sta_an_data_upd
                                                  ) VALUES (
                                                    :sta_an_prop_id,
                                                    :sta_an_status,
                                                    :sta_an_user_id,
                                                    :sta_an_data_cad,
                                                    :sta_an_data_upd
                                                  )";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":sta_an_prop_id", $sta_an_prop_id);
    $stmt->bindParam(":sta_an_status", $num_status);
    //
    $stmt->bindParam(":sta_an_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":sta_an_data_cad", date('Y-m-d H:i:s'));
    $stmt->bindParam(":sta_an_data_upd", date('Y-m-d H:i:s'));
    $stmt->execute();
    // -------------------------------


    // ALTERA O STATUS DA PROPOSTA
    $sql = "UPDATE
                    propostas_status
              SET        
                    prop_sta_status   = :prop_sta_status,
                    prop_sta_user_id  = :prop_sta_user_id,
                    prop_sta_data_cad = :prop_sta_data_cad
              WHERE
                    prop_sta_prop_id  = :prop_sta_prop_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":prop_sta_prop_id", $sta_an_prop_id);
    $stmt->bindParam(":prop_sta_status", $num_status);
    $stmt->bindParam(":prop_sta_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":prop_sta_data_cad", date('Y-m-d H:i:s'));
    $stmt->execute();
    // -------------------------------


    // REGISTRA AÇÃO NO LOG
    $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'ANÁLISE PROPOSTA - EM ANÁLISE',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $last_id,
      ':dados'      => 'Proposta: ' . $_GET['prop_id'] . '; Status: ' . $sta_an_status,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => date('Y-m-d H:i:s')
    ));
    // -------------------------------

    /* -------------------------------
              DISPARA E-MAIL
    ------------------------------- */
    try {
      $mail = new PHPMailer(true); // ENVIA E-MAIL
      include  '../controller/email_conf.php';
      include '../includes/email/send_email.php'; // CONFIGURAÇÃO DE E-MAILS
      $mail->addAddress($email_extensao); // E-MAIL DA EXTENSÃO
      $mail->addAddress($_GET['user_email']); // E-MAIL DO USUÁRIO QUE RECEBERÁ A MENSAGEM
      $mail->isHTML(true);
      $mail->Subject = 'COD:' . $_GET['prop_codigo'] . ' - Análise Iniciada'; //TÍTULO DO E-MAIL

      //RECUPERA URL PARA O LINK DO EMAIL
      $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
      $url = $_SERVER['REQUEST_URI'];
      $path = parse_url($url, PHP_URL_PATH);
      $directories = explode('/', $path);
      array_shift($directories);
      $pagina = $protocol . $_SERVER['HTTP_HOST'] . '/' . $directories[0] . '/admin';

      // CORPO DO EMAIL
      include '../includes/email/email_header_800.php';
      $email_conteudo .= "
      <p style='width: 100%; font-size: 1.188rem; text-transform: uppercase; font-weight: 600; margin-bottom: 20px; display: inline-block;'>
      Análise Iniciada!
      </p>

      <p style='font-size: 1rem;'>
      A análise da proposta de código: " . $_GET['prop_codigo'] . " foi iniciada.<br>
      Após a análise, você receberar o parecer da Pró-Reitoria de Extensão.
      </p>

      <a style='cursor: pointer;' href='$pagina'><button style='background: #62B790; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 13px 20px; margin-top: 20px;' target='_blank'>ACESSE O SISTEMA</button></a>";
      include '../includes/email/email_footer.php';

      $mail->Body  = $email_conteudo;
      $mail->send();
    } catch (Exception $e) {
      //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
      $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro ao tentar enviar o e-mail!";
      echo "<script> history.go(-1);</script>";
      return die;
    }
    // -------------------------------

    header("Location: ../proposta_analise.php?prop_id=" . $_GET['prop_id']);
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro ao tentar realizar a ação!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}















/*****************************************************************************************
                                    DEFERIR ANÁLISE
 *****************************************************************************************/
// if (isset($dados['PropDeferir'])) {
if (isset($_GET['funcao']) && $_GET['funcao'] == "cad_prop_def") {

  $sta_an_prop_id    = $_POST['prop_id'];
  //
  $sta_an_codigo     = $_POST['prop_codigo'];
  $sta_an_user_email = trim($_POST['sta_an_user_email']);
  $sta_an_obs        = nl2br(trim($_POST['sta_an_obs']));
  $num_status        = 7; // DEFERIR

  try {
    $sql = "INSERT INTO propostas_analise_status (
                                                  sta_an_prop_id,
                                                  sta_an_status,
                                                  sta_an_obs,
                                                  sta_an_user_id,
                                                  sta_an_data_cad,
                                                  sta_an_data_upd
                                                  ) VALUES (
                                                  :sta_an_prop_id,
                                                  :sta_an_status,
                                                  :sta_an_obs,
                                                  :sta_an_user_id,
                                                  :sta_an_data_cad,
                                                  :sta_an_data_upd
                                                  )";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":sta_an_prop_id", $sta_an_prop_id);
    $stmt->bindParam(":sta_an_status", $num_status);
    $stmt->bindParam(":sta_an_obs", $sta_an_obs);
    $stmt->bindParam(":sta_an_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":sta_an_data_cad", date('Y-m-d H:i:s'));
    $stmt->bindParam(":sta_an_data_upd", date('Y-m-d H:i:s'));
    $stmt->execute();


    // -------------------------------
    // ALTERA O STATUS DA PROPOSTA
    // -------------------------------
    $sql = "UPDATE
                    propostas_status
              SET        
                    prop_sta_status   = :prop_sta_status,
                    prop_sta_user_id  = :prop_sta_user_id,
                    prop_sta_data_cad = :prop_sta_data_cad
              WHERE
                    prop_sta_prop_id  = :prop_sta_prop_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":prop_sta_prop_id", $sta_an_prop_id);
    $stmt->bindParam(":prop_sta_status", $num_status);
    $stmt->bindParam(":prop_sta_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":prop_sta_data_cad", date('Y-m-d H:i:s'));
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'ANÁLISE PROPOSTA - DEFERIDO',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $last_id,
      ':dados'      => 'Proposta: ' . $sta_an_prop_id . '; Status: ' . $sta_an_status,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => date('Y-m-d H:i:s')
    ));

    try {
      $mail = new PHPMailer(true); // ENVIA E-MAIL
      include  '../controller/email_conf.php';
      include '../includes/email/send_email.php'; // CONFIGURAÇÃO DE E-MAILS
      //$mail->addAddress($email_extensao); // E-MAIL DA EXTENSÃO
      $mail->addAddress($sta_an_user_email); // E-MAIL DO USUÁRIO QUE RECEBERÁ A MENSAGEM
      $mail->isHTML(true);
      $mail->Subject = 'COD:' . $sta_an_codigo . ' - Proposta Deferida'; //TÍTULO DO E-MAIL

      //RECUPERA URL PARA O LINK DO EMAIL
      $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
      $url = $_SERVER['REQUEST_URI'];
      $path = parse_url($url, PHP_URL_PATH);
      $directories = explode('/', $path);
      array_shift($directories);
      $pagina = $protocol . $_SERVER['HTTP_HOST'] . '/' . $directories[0];

      // CORPO DO EMAIL
      include '../includes/email/email_header_800.php';
      $email_conteudo .= "
      <p style='width: 100%; font-size: 1.188rem; text-transform: uppercase; font-weight: 600; margin-bottom: 20px; display: inline-block;'>
      <span style='background: rgb(56, 193, 114 ,20%); font-weight: 500; padding: 5px 8px; color: #38C172; border-radius: 4px;'>Proposta Deferida</span>
      </p>

      <p style='font-size: 1rem;'>
      A proposta de código: <strong>" . $sta_an_codigo . "</strong> foi deferida.<br>
      </p>

      <p style='font-size: 1rem;'>
      $sta_an_obs
      </p>

      <p style='font-size: 1rem;'>
      Agradecemos a submissão do seu projeto.<br>
      </p>

      <a style='cursor: pointer;' href='$pagina'><button style='background: #62B790; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 13px 20px; margin-top: 20px;' target='_blank'>ACESSE O SISTEMA</button></a>";
      include '../includes/email/email_footer.php';

      $mail->Body  = $email_conteudo;
      $mail->send();
    } catch (Exception $e) {
      //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
      $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro ao tentar enviar o e-mail!";
      echo "<script> history.go(-1);</script>";
      return die;
    }

    //header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    header("Location: ../proposta_analise.php?prop_id=$sta_an_prop_id");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro ao tentar realizar a ação!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}
















/*****************************************************************************************
                                  INDEFERIR ANÁLISE
 *****************************************************************************************/
// if (isset($dados['PropIndeferir'])) {
if (isset($_GET['funcao']) && $_GET['funcao'] == "cad_prop_indef") {

  $sta_an_prop_id    = $_POST['prop_id'];
  //
  $sta_an_codigo     = $_POST['prop_codigo'];
  $sta_an_user_email = trim($_POST['sta_an_user_email']);
  $sta_an_obs        = nl2br(trim($_POST['sta_an_obs']));
  $num_status        = 8; // INDEFERIR
  $data_real         = date('Y-m-d H:i:s');

  try {
    $sql = "INSERT INTO propostas_analise_status (
                                                sta_an_prop_id,
                                                sta_an_status,
                                                sta_an_obs,
                                                sta_an_user_id,
                                                sta_an_data_cad,
                                                sta_an_data_upd
                                                ) VALUES (
                                                :sta_an_prop_id,
                                                :sta_an_status,
                                                :sta_an_obs,
                                                :sta_an_user_id,
                                                :sta_an_data_cad,
                                                :sta_an_data_upd
                                                )";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":sta_an_prop_id", $sta_an_prop_id);
    $stmt->bindParam(":sta_an_status", $num_status);
    $stmt->bindParam(":sta_an_obs", $sta_an_obs);
    $stmt->bindParam(":sta_an_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":sta_an_data_cad", $data_real);
    $stmt->bindParam(":sta_an_data_upd", $data_real);
    $stmt->execute();

    // -------------------------------
    // ALTERA O STATUS DA PROPOSTA
    // -------------------------------
    $sql = "UPDATE
                    propostas_status
              SET        
                    prop_sta_status   = :prop_sta_status,
                    prop_sta_user_id  = :prop_sta_user_id,
                    prop_sta_data_cad = :prop_sta_data_cad
              WHERE
                    prop_sta_prop_id  = :prop_sta_prop_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":prop_sta_prop_id", $sta_an_prop_id);
    $stmt->bindParam(":prop_sta_status", $num_status);
    $stmt->bindParam(":prop_sta_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":prop_sta_data_cad", $data_real);
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'ANÁLISE PROPOSTA - INDEFERIDO',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $last_id,
      ':dados'      => 'Proposta: ' . $sta_an_prop_id . '; Status: ' . $sta_an_status,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => $data_real
    ));


    try {
      $mail = new PHPMailer(true); // ENVIA E-MAIL
      include  '../controller/email_conf.php';
      include '../includes/email/send_email.php'; // CONFIGURAÇÃO DE E-MAILS
      //$mail->addAddress($email_extensao); // E-MAIL DA EXTENSÃO
      $mail->addAddress($sta_an_user_email); // E-MAIL DO USUÁRIO QUE RECEBERÁ A MENSAGEM
      $mail->isHTML(true);
      $mail->Subject = 'COD:' . $sta_an_codigo . ' - Proposta Indeferida'; //TÍTULO DO E-MAIL

      //RECUPERA URL PARA O LINK DO EMAIL
      $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
      $url = $_SERVER['REQUEST_URI'];
      $path = parse_url($url, PHP_URL_PATH);
      $directories = explode('/', $path);
      array_shift($directories);
      $pagina = $protocol . $_SERVER['HTTP_HOST'] . '/' . $directories[0];

      // CORPO DO EMAIL
      include '../includes/email/email_header_800.php';
      $email_conteudo .= "
      <p style='width: 100%; font-size: 1.188rem; text-transform: uppercase; font-weight: 600; margin-bottom: 20px; display: inline-block;'>
      <span style='background: rgb(239, 109, 117 ,20%); font-weight: 500; padding: 5px 8px; color: #ef6d75; border-radius: 4px;'>Proposta Indeferida</span>
      </p>

      <p style='font-size: 1rem;'>
      A proposta de código: <strong>" . $sta_an_codigo . "</strong> foi Indeferida.<br>
      </p>

      <p style='font-size: 1rem;'>
      $sta_an_obs
      </p>

      <a style='cursor: pointer;' href='$pagina'><button style='background: #62B790; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 13px 20px; margin-top: 20px;' target='_blank'>ACESSE O SISTEMA</button></a>";
      include '../includes/email/email_footer.php';

      $mail->Body  = $email_conteudo;
      $mail->send();
    } catch (Exception $e) {
      //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
      $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro ao tentar enviar o e-mail!";
      echo "<script> history.go(-1);</script>";
      return die;
    }

    //header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    header("Location: ../proposta_analise.php?prop_id=$sta_an_prop_id");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro ao tentar realizar a ação!";
    //header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}















/*****************************************************************************************
                                SOLICITA PARECER TÉCNICO
 *****************************************************************************************/
if (isset($dados['CadSolicitaParecerTecnico'])) {

  $sta_an_prop_id     = $_POST['prop_id'];
  //
  $num_status = 5; //AGUARDAR PARECER TÉCNICO
  $sta_an_user_suport = $_POST['sta_an_user_suport'];
  $sta_an_user_email  = $_POST['sta_an_user_email'];
  $sta_an_obs         = nl2br(trim($_POST['sta_an_obs']));

  try {
    $sql = "INSERT INTO propostas_analise_status (
                                                  sta_an_prop_id,
                                                  sta_an_status,
                                                  sta_an_user_suport,
                                                  sta_an_obs,
                                                  sta_an_user_id,
                                                  sta_an_data_cad,
                                                  sta_an_data_upd
                                                  ) VALUES (
                                                  :sta_an_prop_id,
                                                  :sta_an_status,
                                                  UPPER(:sta_an_user_suport),
                                                  :sta_an_obs,
                                                  :sta_an_user_id,
                                                  :sta_an_data_cad,
                                                  :sta_an_data_upd
                                                  )";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":sta_an_prop_id", $sta_an_prop_id);
    //
    $stmt->bindParam(":sta_an_status", $num_status);
    $stmt->bindParam(":sta_an_user_suport", $sta_an_user_suport);
    $stmt->bindParam(":sta_an_obs", $sta_an_obs);
    //
    $stmt->bindParam(":sta_an_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":sta_an_data_cad", date('Y-m-d H:i:s'));
    $stmt->bindParam(":sta_an_data_upd", date('Y-m-d H:i:s'));
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'ANÁLISE PROPOSTA - PARECER TÉCNICO',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $last_id,
      ':dados'      => 'Proposta: ' . $sta_an_prop_id . '; Status: ' . $num_status . '; Técnico: ' . $sta_an_user_suport,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => date('Y-m-d H:i:s')
    ));


    // -------------------------------
    // ALTERA O STATUS DA PROPOSTA
    // -------------------------------
    $sql = "UPDATE
                    propostas_status
              SET        
                    prop_sta_status   = :prop_sta_status,
                    prop_sta_user_id  = :prop_sta_user_id,
                    prop_sta_data_cad = :prop_sta_data_cad
              WHERE
                    prop_sta_prop_id  = :prop_sta_prop_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":prop_sta_prop_id", $sta_an_prop_id);
    $stmt->bindParam(":prop_sta_status", $num_status);
    $stmt->bindParam(":prop_sta_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":prop_sta_data_cad", date('Y-m-d H:i:s'));
    $stmt->execute();


    try {
      $mail = new PHPMailer(true); // ENVIA E-MAIL

      include '../controller/email_conf.php';
      include '../includes/email/send_email.php'; // CONFIGURAÇÃO DE E-MAILS
      $mail->addAddress($email_extensao); // E-MAIL DA EXTENSÃO
      $mail->addAddress($sta_an_user_email); // E-MAIL DO USUÁRIO QUE RECEBERÁ A MENSAGEM
      $mail->isHTML(true);
      $mail->Subject = 'Ajustes solicitados';

      //RECUPERA URL PARA O LINK DO EMAIL
      $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
      $url = $_SERVER['REQUEST_URI'];
      $path = parse_url($url, PHP_URL_PATH);
      $directories = explode('/', $path);
      array_shift($directories);
      $pagina = $protocol . $_SERVER['HTTP_HOST'] . '/' . $directories[0] . '/admin';

      // CORPO DO EMAIL
      $email_conteudo .= "
      <div style='width: 100%; background-color: #cacaca; display: block; padding: 20px 0; font-family: Arial, Helvetica, sans-serif; border-collapse: collapse;'>
        <table width='800' border='0' align='center' cellpadding='0' cellspacing='0'>
          <thead>
            <tr style='background: #001A35;color: #fff;  display: flex; justify-content: space-between; padding: 20px 45px;'>
              <td>
                <span style='font-size: 1.6rem !important; font-weight: 600; margin: 0;'>RESERVM BAHIANA</span>
                <P style='font-size: 0.775rem; font-weight: 500; margin: 0; color: #fff;'>SISTEMA DE EXTENSÃO</P>
              </td>
              <td>
                <img src='dist/img/logo_bahiana_login.svg' width='180' alt=''>
              </td>
            </tr>
          </thead>
          <tbody>
            <tr style='background: #fff; text-align: center; color: #515050; display: inline-block; padding:30px 50px; line-height: 25px;'>
              <td style='padding: 2em 2rem; display: inline-block;'>
                <span style='font-size: 1.188rem;  text-transform: uppercase; font-weight: 600; margin-bottom: 20px; display: inline-block;'>Olá, $sta_an_user_nome! <br><br> Sua proposta está sendo analisada e precisa de ajuestes!</span>
                <p style='font-size: 0.875rem;'>$sta_an_obs</p>

                <p style='font-size: 0.875rem;'>Ao realizar seu primeiro acesso, será preciso alterar esta senha. Acesse o sistema e digite suas credenciais.</p>
                <a style='cursor: pointer;' href='$pagina'><button style='background: #62B790; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 13px 20px; margin-top: 20px;' target='_blank'>ACESSE O SISTEMA</button></a>
              </td>
            </tr>
            <tr style='background: #F3F6F9; display: block; text-align: center; padding: 40px;'>
              <td>
                <img src='dist/img/logo_bahiana_login_azul.svg' width='180' alt=''>
              </td>
            </tr>
          </tbody>
        </table>
      </div>";

      $mail->Body  = $email_conteudo;
      $mail->send();
    } catch (Exception $e) {
      //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
      $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro ao tentar enviar o e-mail!";
      echo "<script> history.go(-1);</script>";
      return die;
    }

    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    // header("Location: ../proposta_analise.php?prop_id=$sta_an_prop_id");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro ao tentar realizar a ação!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}



/*****************************************************************************************
                                EDITAR PARECER TÉCNICO
 *****************************************************************************************/
if (isset($dados['EditSolicitaParecerTecnico'])) {

  $sta_an_id          = $_POST['sta_an_id'];
  $sta_an_prop_id     = $_POST['sta_an_prop_id'];
  //
  $sta_an_status      = $_POST['sta_an_status'];
  $sta_an_user_suport = $_POST['sta_an_user_suport'];
  $sta_an_obs         = nl2br(trim($_POST['sta_an_obs']));
  //
  $sta_an_user_id     = $_SESSION['reservm_admin_id'];
  $data_real          = date('Y-m-d H:i:s');

  try {
    $sql = "UPDATE
                    propostas_analise_status
              SET
                    sta_an_user_suport = :sta_an_user_suport,
                    sta_an_obs         = :sta_an_obs,
                    sta_an_user_id     = :sta_an_user_id,
                    sta_an_data_upd    = :sta_an_data_upd
              WHERE
                    sta_an_id = :sta_an_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":sta_an_id", $sta_an_id);
    //
    $stmt->bindParam(":sta_an_user_suport", $sta_an_user_suport);
    $stmt->bindParam(":sta_an_obs", $sta_an_obs);
    //
    $stmt->bindParam(":sta_an_user_id", $sta_an_user_id);
    $stmt->bindParam(":sta_an_data_upd", $data_real);
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'ANÁLISE PROPOSTA - PARECER TÉCNICO',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $sta_an_id,
      ':dados'      => 'Proposta: ' . $sta_an_prop_id . '; Status: ' . $sta_an_status . '; Técnico: ' . $sta_an_user_suport,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => date('Y-m-d H:i:s')
    ));

    $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados atualizados!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro ao tentar realizar a ação!";
    // header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}



















/*****************************************************************************************
                                CADASTRAR SOLICITAR AJUSTES
 *****************************************************************************************/
//if (isset($dados['CadSolicitaAjustes'])) {
if (isset($_GET['funcao']) && $_GET['funcao'] == "cad_prop_ajus") {

  $sta_an_prop_id     = $_POST['prop_id'];
  //
  //$sta_an_status      = $_POST['sta_an_status'];
  $num_status = 4; //AGUARDAR AJUSTES
  $sta_an_user_suport = $_POST['sta_an_user_suport'];
  //$sta_an_user_nome   = $_POST['sta_an_user_nome'];
  $sta_an_user_email  = $_POST['sta_an_user_email'];
  $sta_an_obs         = nl2br(trim($_POST['sta_an_obs']));
  $data_real          = date('Y-m-d H:i:s');

  try {
    $sql = "INSERT INTO propostas_analise_status (
                                                  sta_an_prop_id,
                                                  sta_an_status,
                                                  sta_an_user_suport,
                                                  sta_an_obs,
                                                  sta_an_user_id,
                                                  sta_an_data_cad,
                                                  sta_an_data_upd
                                                  ) VALUES (
                                                  :sta_an_prop_id,
                                                  :sta_an_status,
                                                  UPPER(:sta_an_user_suport),
                                                  :sta_an_obs,
                                                  :sta_an_user_id,
                                                  :sta_an_data_cad,
                                                  :sta_an_data_upd
                                                  )";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":sta_an_prop_id", $sta_an_prop_id);
    //
    $stmt->bindParam(":sta_an_status", $num_status);
    $stmt->bindParam(":sta_an_user_suport", $sta_an_user_suport);
    $stmt->bindParam(":sta_an_obs", $sta_an_obs);
    //
    $stmt->bindParam(":sta_an_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":sta_an_data_cad", $data_real);
    $stmt->bindParam(":sta_an_data_upd", $data_real);
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'ANÁLISE PROPOSTA - SOLICITAR AJUSTES',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $last_id,
      ':dados'      => 'Proposta: ' . $sta_an_prop_id . '; Status: ' . $sta_an_status . '; Proponente: ' . $sta_an_user_nome . '; E-mail: ' . $sta_an_user_email,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => $data_real
    ));

    // -------------------------------
    // ALTERA O STATUS DA PROPOSTA
    // -------------------------------
    $sql = "UPDATE
                    propostas_status
              SET        
                    prop_sta_status   = :prop_sta_status,
                    prop_sta_user_id  = :prop_sta_user_id,
                    prop_sta_data_cad = :prop_sta_data_cad
              WHERE
                    prop_sta_prop_id  = :prop_sta_prop_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":prop_sta_prop_id", $sta_an_prop_id);
    $stmt->bindParam(":prop_sta_status", $num_status);
    $stmt->bindParam(":prop_sta_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":prop_sta_data_cad", date('Y-m-d H:i:s'));
    $stmt->execute();
    // -------------------------------


    // -------------------------------
    // ALTERA O STATUS DA ETAPA DA PROPOSTA
    // -------------------------------
    $status_etapa = 1;
    $sql = "UPDATE propostas SET prop_status_etapa = :prop_status_etapa WHERE prop_id  = :prop_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":prop_id", $sta_an_prop_id);
    $stmt->bindParam(":prop_status_etapa", $status_etapa);
    $stmt->execute();
    // -------------------------------

    try {
      $mail = new PHPMailer(true); // ENVIA E-MAIL

      include  '../controller/email_conf.php';
      include '../includes/email/send_email.php'; // CONFIGURAÇÃO DE E-MAILS
      // $mail->addAddress($email_extensao); // E-MAIL DA EXTENSÃO
      $mail->addAddress($sta_an_user_email); // E-MAIL DO USUÁRIO QUE RECEBERÁ A MENSAGEM
      $mail->isHTML(true);
      $mail->Subject = 'Ajustes solicitados';

      //RECUPERA URL PARA O LINK DO EMAIL
      $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
      $url = $_SERVER['REQUEST_URI'];
      $path = parse_url($url, PHP_URL_PATH);
      $directories = explode('/', $path);
      array_shift($directories);
      $pagina = $protocol . $_SERVER['HTTP_HOST'] . '/' . $directories[0];

      // CORPO DO EMAIL
      include '../includes/email/email_header_800.php';

      $email_conteudo .= "
      <p style='width: 100%; font-size: 1.188rem; text-transform: uppercase; font-weight: 600; margin-bottom: 20px; display: inline-block;'>
      Descrição do Ajuste
      </p>

      <p style='font-size: 1rem; text-align: left; background: #F3F6F9; padding: 20px; margin: 20px 0px'>
      $sta_an_obs
      </p>
      
      <p style='font-size: 1rem;'>
      Atualize os dados da submissão de acordo com a solicitação da equipe de extensão. 
      </p>

      <a style='cursor: pointer;' href='$pagina'><button style='background: #62B790; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 13px 20px; margin-top: 20px;' target='_blank'>ACESSE O SISTEMA</button></a>";

      include '../includes/email/email_footer.php';

      $mail->Body  = $email_conteudo;
      $mail->send();
      echo 'Message has been sent';
    } catch (Exception $e) {
      echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }


    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    // header("Location: ../proposta_analise.php?prop_id=$sta_an_prop_id");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro ao tentar realizar a ação!";
    // header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}





































































/*****************************************************************************************
                                EDITAR TIPO DE PROGRAMA
 *****************************************************************************************/
if (isset($dados['EditTipoPrograma'])) {

  $ctp_id        = trim($_POST['ctp_id']);
  $ctp_tipo      = trim($_POST['ctp_tipo']);
  $ctp_categoria = trim($_POST['ctp_categoria']);
  $ctp_user_id   = trim($_SESSION['reservm_admin_id']);
  $data_real     = date('Y-m-d H:i:s');

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_tipo_programa WHERE ctp_tipo = :ctp_tipo AND ctp_categoria = :ctp_categoria AND ctp_id != :ctp_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":ctp_id", $ctp_id);
  $stmt->bindParam(":ctp_tipo", $ctp_tipo);
  $stmt->bindParam(":ctp_categoria", $ctp_categoria);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este dado já foi cadastrado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }

  try {
    $sql = "UPDATE
                    conf_tipo_programa
              SET
                    ctp_tipo      = UPPER(:ctp_tipo),
                    ctp_categoria = UPPER(:ctp_categoria),
                    ctp_user_id   = :ctp_user_id,
                    ctp_data_upd  = :ctp_data_upd
              WHERE
                    ctp_id        = :ctp_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":ctp_id", $ctp_id);
    //
    $stmt->bindParam(":ctp_tipo", $ctp_tipo);
    $stmt->bindParam(":ctp_categoria", $ctp_categoria);
    //
    $stmt->bindParam(":ctp_user_id", $ctp_user_id);
    $stmt->bindParam(":ctp_data_upd", $data_real);
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'TIPO DE PROGRAMA',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $ctp_id,
      ':dados'      => 'Tipo: ' . $ctp_tipo . '; Categoria: ' . $ctp_categoria,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => date('Y-m-d H:i:s')
    ));

    $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados atualizados!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados não atualizados!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}








/*****************************************************************************************
                                  EXCLUIR ITEM DA ANÁLISE
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_sta_an") {

  $sta_an_id = $_GET['sta_an_id'];

  try {
    $sql = "DELETE FROM propostas_analise_status WHERE sta_an_id = '$sta_an_id'";
    $conn->exec($sql);
  } catch (PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
  }

  // REGISTRA AÇÃO NO LOG
  $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_acao_user_nome, log_data )
                          VALUES ( :modulo, :acao, :acao_id, :user_id, :user_nome, :data )');
  $stmt->execute(array(
    ':modulo'    => 'ANÁLISE PROPOSTA',
    ':acao'      => 'DEFERIDO - EXCLUSÃO',
    ':acao_id'   => $sta_an_id,
    ':user_id'   => $_SESSION['reservm_admin_id'],
    ':user_nome' => $_SESSION['reservm_admin_nome'],
    ':data'      => date('Y-m-d H:i:s')
  ));

  $conn = null;

  //$_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i>Dados excluídos!";
  header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
}
