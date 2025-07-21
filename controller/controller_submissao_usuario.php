<?php
session_start();
include '../conexao/conexao.php';

// NECESSÁRIO PARA ENVIAR O EMAIL
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                              SOLICITAÇÃO DE AUTORIZAÇÃO
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "cad_subm") {

  $subs_solicitacao = trim($_POST['subs_solicitacao']) !== '' ? nl2br(trim($_POST['subs_solicitacao'])) : NULL;
  $user_email       = trim($_POST['user_email']) !== '' ? trim($_POST['user_email']) : NULL;
  $subs_status      = 0;
  $reservm_user_id     = $_SESSION['reservm_user_id'];

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "INSERT INTO submissao_permissao (
                                              subs_solicitacao,
                                              subs_status,
                                              subs_cad,
                                              subs_data_cad
                                            ) VALUES (
                                              :subs_solicitacao,
                                              :subs_status,
                                              :subs_cad,
                                              GETDATE()
                                            )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':subs_solicitacao' => $subs_solicitacao,
      ':subs_status'      => $subs_status,
      ':subs_cad'         => $reservm_user_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'SUBMISSÃO',
      ':acao'       => 'PEDIDO AUTORIZAÇÃO',
      ':acao_id'    => $last_id,
      ':dados'      => 'Status: ' . $subs_status . '; E-mail: ' . $user_email . '; Solicitação: ' . $subs_solicitacao,
      ':user_id'    => $reservm_user_id
    ]);
    // -------------------------------

    // DISPARA E-MAIL
    $mail = new PHPMailer(true);
    include '../controller/email_conf.php';
    $mail->addAddress($email_extensao, 'RESERVM'); // E-MAIL DA EXTENSÃO
    $mail->isHTML(true);
    $mail->Subject = 'Solicitação de licença'; //TÍTULO DO E-MAIL

    // CORPO DO EMAIL
    include '../includes/email/email_header.php';
    $email_conteudo .= "
      <tr style='background-color: #ffffff; text-align: center; color: #252525;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
        <td style='padding: 2em 2rem; display: inline-block; width: 100%'>

        <p style='font-size: 1.3rem; font-weight: 600; margin: 0px 0px 15px 0px;'>
        Dados da solicitação
        </p>

        <p style='font-size: 0.875rem; text-align: left; background: #D6E9F8; padding: 20px; margin: 20px 0px 0px 0px'>
        $subs_solicitacao
        </p>

        <p style='font-size: 0.875rem; font-weight: 400; margin: 40px 0px 0px 0px;'>
        Acesse o módulo de submissão para deferir ou indeferir a proposta.
        </p><br>
        
        <a style='cursor: pointer;' href='$url_sistema/admin'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 10px;' target='_blank'>Acesse o sistema</button></a>
        </td>
      </tr>";

    include '../includes/email/email_footer.php';

    $mail->Body  = $email_conteudo;
    $mail->send();
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Solicitação enviada!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Cadastro não realizado!" . $e->getMessage();
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}
