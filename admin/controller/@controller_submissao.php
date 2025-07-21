<?php
session_start();
include '../../conexao/conexao.php';

// NECESSÁRIO PARA ENVIAR O EMAIL
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                        RESPOSTA DA SOLICITAÇÃO DE AUTORIZAÇÃO
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "cad_subm_autoriza") {

  $subs_id            = trim($_POST['subs_id']) !== '' ? trim($_POST['subs_id']) : NULL;
  $user_user_id       = trim($_POST['user_user_id']) !== '' ? trim($_POST['user_user_id']) : NULL;
  $subs_status        = trim($_POST['subs_status']) !== '' ? trim($_POST['subs_status']) : NULL;
  $user_email         = trim($_POST['user_email']) !== '' ? trim($_POST['user_email']) : NULL;
  $subs_data_validade = trim($_POST['subs_data_validade']) !== '' ? trim($_POST['subs_data_validade']) : NULL;
  $subs_obs           = trim($_POST['subs_obs']) !== '' ? nl2br(trim($_POST['subs_obs'])) : NULL;
  $reservm_admin_id      = $_SESSION['reservm_admin_id'];

  // DATA DA VALIDADE NÃO PODE SER MENOR QUE A DATA DE HOJE
  if (!empty($subs_data_validade) && strtotime($subs_data_validade) < strtotime('today')) {
    $_SESSION["erro"] = "A data de validade dever ser maior que a data de hoje!";
    echo "<script> history.go(-1);</script>";
    return die;
  } else {

    // SE HOUVER UMA SOLICITAÇÃO PENDENTE, OUTRAS SOLICITAÇÕES NÃO PODERÃO SER EDITADAS
    $query = "SELECT * FROM submissao_permissao
              WHERE subs_cad = '$user_user_id'
              AND subs_status = 0
              AND subs_id != '$subs_id'";
    $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
    $stmt->execute();
    $row_count = $stmt->rowCount();
    if ($row_count >= 1) {
      $_SESSION["erro"] = "Este usuário possui uma solicitação pendente de aprovação!";
      echo "<script> history.go(-1);</script>";
      return die;
    } else {

      // SE O USUÁRIO JÁ TIVER UMA SOLICITAÇÃO APROVADA SEM DATA DE VALIDADE, NÃO PODE APROVAR ANTIGAS SOLICITAÇÕES
      $query = "SELECT * FROM submissao_permissao
                WHERE subs_cad = '$user_user_id'
                AND subs_data_validade IS NULL
                AND subs_status = 1
                AND subs_id != '$subs_id'";
      $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
      $stmt->execute();
      $row_count = $stmt->rowCount();
      if ($row_count >= 1) {
        $_SESSION["erro"] = "Este usuário já tem autorização para propor seu projeto!";
        echo "<script> history.go(-1);</script>";
        return die;
      } else {

        // SE O USUÁRIO JÁ TIVER UMA SOLICITAÇÃO APROVADA COM DATA VÁLIDA, NÃO PODE EDITAR ANTIGAS SOLICITAÇÕES 
        $query = "SELECT * FROM submissao_permissao
                  WHERE subs_cad = '$user_user_id'
                  AND subs_data_validade IS NOT NULL
                  AND subs_data_validade >= GETDATE()
                  AND subs_status = 1
                  AND subs_id != '$subs_id'";
        $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->execute();
        $row_count = $stmt->rowCount();
        if ($row_count >= 1) {
          $_SESSION["erro"] = "Este usuário já tem autorização para propor seu projeto!";
          echo "<script> history.go(-1);</script>";
          return die;
        }
      }
    }
  }

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "UPDATE
                    submissao_permissao
              SET
                    subs_status        = :subs_status,
                    subs_data_validade = :subs_data_validade,
                    subs_obs           = :subs_obs,
                    subs_cad_resp      = :subs_cad_resp,
                    subs_data_resp     = GETDATE()
              WHERE
                    subs_id = :subs_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':subs_id'            => $subs_id,
      ':subs_status'        => $subs_status,
      ':subs_data_validade' => $subs_data_validade,
      ':subs_obs'           => $subs_obs,
      ':subs_cad_resp'      => $reservm_admin_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'SUBMISSÃO',
      ':acao'       => 'RESPOSTA SOLICITAÇÃO',
      ':acao_id'    => $subs_id,
      ':dados'      => 'Status: ' . $subs_status . '; Data Validade ' . $subs_data_validade . '; Obs ' . $subs_obs,
      ':user_id'    => $reservm_admin_id
    ]);

    // DISPARA E-MAIL
    $mail = new PHPMailer(true);
    include '../../conexao/email.php';
    $mail->addAddress($user_email, 'RESERVM'); // E-MAIL DO ADMINISTRADOR
    $mail->isHTML(true);
    $mail->Subject = 'Resultado da solicitação da licença'; //TÍTULO DO E-MAIL

    if ($subs_status == 1) {
      $aviso = "Sua solicitação foi <span style='background-color: #D7F3E3; color: #38C172; border-radius: 4px; padding: 3px 10px; margin: 0px; font-weight: 400;'>deferida</span>";
    } else {
      $aviso = "Sua solicitação foi <span style='background-color: #F3DAD8; color: #C4453E; border-radius: 4px; padding: 3px 10px; margin: 0px; font-weight: 400;'>indeferida</span>";
    }

    // CORPO DO EMAIL
    include '../../includes/email/email_header.php';
    $email_conteudo .= "
      <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
        <td style='padding: 2em 2rem; display: inline-block;'>

        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 20px 0px;'>
        $aviso
        </p>

        <p style='font-size: 1rem; text-align: left; background: #D6E9F8; padding: 20px; margin: 20px 0px 30px 0px'>
        $subs_obs
        </p>

        <a style='cursor: pointer;' href='$url_sistema'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 20px;' target='_blank'>Acesse o sistema</button></a>
        </td>
      </tr>";
    include '../../includes/email/email_footer.php';

    $mail->Body  = $email_conteudo;
    $mail->send();
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Cadastro realizado com sucesso!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Cadastro não realizado!";
    echo "<script> history.go(-1);</script>";
  }
}









/*****************************************************************************************
                                    EXCLUIR DADOS
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_subs") {

  $subs_id       = $_GET['id'];
  $reservm_admin_id = $_SESSION['reservm_admin_id'];

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "DELETE FROM submissao_permissao WHERE subs_id = :subs_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':subs_id' => $subs_id]);

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {

      // REGISTRA AÇÃO NO LOG
      $stmt_log = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                                  VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt_log->execute([
        ':modulo'  => 'SUBMISSÃO',
        ':acao'    => 'EXCLUSÃO',
        ':acao_id' => $subs_id,
        ':user_id' => $reservm_admin_id
      ]);
      // -------------------------------

      $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

      $_SESSION["msg"] = "Dados excluídos com sucesso!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    } else {
      $conn->rollBack();
      $_SESSION["erro"] = "Dados não excluídos!";
      echo "<script> history.go(-1);</script>";
      return die;
    }
  } catch (PDOException $e) {
    //echo "Erro: " . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Dados não excluídos!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
}
