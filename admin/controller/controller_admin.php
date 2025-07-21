<?php
// session_start();
include '../conexao/conexao.php';

// NECESSÁRIO PARA ENVIAR O EMAIL
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {
  try {

    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $acao = $_POST['acao']; // AÇÃO: CADASTRAR, ATUALIZAR, DELETAR

    // SE CADASTRAR
    if ($acao === 'cadastrar' || $acao === 'atualizar') {

      // VALIDA OS CAMPOS OBRIGATÓRIOS
      if ($acao === 'cadastrar' && (empty($_POST['admin_matricula_nome']) || empty($_POST['admin_email']) || empty($_POST['admin_perfil']))) {
        throw new Exception("Preencha os campos obrigatórios!");
      }

      // POST
      $admin_id             = bin2hex(random_bytes(16)); // GERA ID COM 32 CARACTERES HEXADECIMAIS
      $admin_matricula_nome = trim($_POST['admin_matricula_nome']);
      //
      if ($acao === 'cadastrar') {
        $parts = explode(' - ', $admin_matricula_nome, 2);
        if (count($parts) === 2 && is_numeric(trim($parts[0])) && !empty(trim($parts[1]))) {
          $admin_matricula = trim($parts[0]);
          $admin_nome      = trim($parts[1]);
        } else {
          throw new Exception("Formato inválido. Informe matrícula e nome!");
        }
      }
      //
      $admin_email          = filter_var(trim($_POST['admin_email']), FILTER_SANITIZE_EMAIL);
      $admin_perfil         = $_POST['admin_perfil'];
      $admin_status         = $_POST['admin_status'] === '1' ? 1 : 0;
      $nivel_acesso         = 1;
    }

    $reservm_admin_id = $_SESSION['reservm_admin_id'];






    // -------------------------------
    // CADASTRO
    // -------------------------------
    if ($acao === 'cadastrar') {

      $log_acao = 'Cadastro';

      // IMPEDE CADASTRO DUPLICADO
      $sqlVerifica = "SELECT COUNT(*) FROM admin WHERE admin_matricula = :admin_matricula AND admin_email = :admin_email AND admin_status != 2";
      $stmtVerifica = $conn->prepare($sqlVerifica);
      $stmtVerifica->execute([':admin_matricula' => $admin_matricula, ':admin_email' => $admin_email]);
      $existe = $stmtVerifica->fetchColumn();
      if ($existe > 0) {
        throw new Exception("Falha ao cadastrar os dados: Matrícula ou e-mail já existe!");
      }

      $sql = "INSERT INTO admin (
                                  admin_id,
                                  admin_matricula,
                                  admin_nome,
                                  admin_email,
                                  admin_perfil,
                                  admin_status,
                                  nivel_acesso,
                                  admin_user_id,
                                  admin_data_cad,
                                  admin_data_upd
                                ) VALUES (
                                  :admin_id,
                                  :admin_matricula,
                                  UPPER(:admin_nome),
                                  LOWER(:admin_email),
                                  :admin_perfil,
                                  :admin_status,
                                  :nivel_acesso,
                                  :admin_user_id,
                                  GETDATE(),
                                  GETDATE()
                                )";
      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':admin_id'        => $admin_id,
        ':admin_matricula' => $admin_matricula,
        ':admin_nome'      => $admin_nome,
        ':admin_email'     => $admin_email,
        ':admin_perfil'    => $admin_perfil,
        ':admin_status'    => $admin_status,
        ':nivel_acesso'    => $nivel_acesso,
        ':admin_user_id'   => $reservm_admin_id
      ]);

      // CRIA O TOKEN DE VALIDAÇÃO
      $codigo      = str_pad(random_int(1000000, 9999999), 7, '0', STR_PAD_LEFT); // GERA UM CÓDIGO DE 7 DÍGITOS
      $codigo_hash = password_hash($codigo, PASSWORD_DEFAULT); // CRIPTOGRAFA O CÓDIGO

      // CRIA UM TOKEN PARA O ID DO ADMINISTRADOR, CASO NÃO EXISTA
      $stmt = $conn->prepare('INSERT INTO token (tok_codigo, tok_user_id, tok_data_valida) VALUES (:tok_codigo, :tok_user_id, DATEADD(day, 3, GETDATE()))');
      $stmt->execute([':tok_codigo' => $codigo_hash, ':tok_user_id' => $admin_id]);
      // -------------------------------

      // DISPARA E-MAIL
      $mail = new PHPMailer(true);
      include '../conexao/email.php';
      $mail->addAddress($email_saap); // E-MAIL DO ADMINISTRADOR
      $mail->isHTML(true);
      $mail->Subject = 'Seu código de acesso chegou'; //TÍTULO DO E-MAIL

      // CORPO DO EMAIL
      include '../includes/email/email_header.php';
      $email_conteudo .= "
      <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
        <td style='padding: 2em 2rem; display: inline-block;'>

        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 20px 0px;'>
        Seu código do RESERVM chegou!
        </p>

        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 20px 0px;'>
        Ao copiá-lo vá à página de acesso e insira o código abaixo para confirmar sua identidade.
        </p>

        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 20px 0px;'>
        Lembrando que essa etapa é muito importante para mantermos a segurança dos seus dados e cumprirmos nosso compromisso com você.
        </p>

        <p style='font-size: 1.3rem; font-weight: 600; margin: 0px 0px 15px 0px;'>
        Seu código de acesso é:
        </p>

        <span style='color: #285FAB; font-size: 2rem; letter-spacing: 0.20rem; font-weight: 600; margin-bottom: 20px; display: inline-block;'>$codigo</span><br>
        <a style='cursor: pointer;' href='$url_sistema/admin/ad-validcod.php?tk=$admin_id'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 20px;' target='_blank'>Acesse o sistema e valide seu código.</button></a>
        </td>
      </tr>";
      include '../includes/email/email_footer.php';

      $mail->Body  = $email_conteudo;

      try {
        $mail->send();
      } catch (Exception $e) {
        $conn->rollBack();
        throw new Exception("Erro ao enviar o e-mail. Tente novamente!");
      }
      // -------------------------------








      // -------------------------------
      // ATUALIZAR
      // -------------------------------
    } elseif ($acao === 'atualizar') {

      if (empty($_POST['admin_id'])) {
        throw new Exception("Erro ao obter o ID!");
      }
      $admin_id = $_POST['admin_id'];
      $log_acao = 'Atualização';

      $sql = "UPDATE admin SET 
                                admin_perfil   = :admin_perfil,
                                admin_status   = :admin_status,
                                admin_user_id  = :admin_user_id,
                                admin_data_upd = GETDATE()
                          WHERE 
                                admin_id = :admin_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':admin_id'      => $admin_id,
        ':admin_perfil'  => $admin_perfil,
        ':admin_status'  => $admin_status,
        ':admin_user_id' => $reservm_admin_id
      ]);







      // -------------------------------
      // EXCLUIR
      // -------------------------------
    } elseif ($_GET['acao'] === 'deletar') {

      if (empty($_GET['admin_id'])) {
        throw new Exception("ID é obrigatório para exclusão.");
      }
      $admin_id = $_GET['admin_id'];
      $log_acao = 'Exclusão';

      // SE DADO ESTIVER SENDO USADO EM ALGUM CADASTRO, NÃO PODE SER EXCLUÍDO
      $sql = $conn->prepare("SELECT COUNT(*) FROM admin WHERE admin_id = :admin_id AND admin_data_acesso IS NOT NULL");
      $sql->execute([':admin_id' => $admin_id]);
      $existe = $sql->fetchColumn();
      if ($existe > 0) {
        throw new Exception("Este registro não pode ser excluído!");
      }
      // -------------------------------

      $sql = "DELETE FROM admin WHERE admin_id = :admin_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':admin_id' => $admin_id]);















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
      ':modulo'  => 'ADMINISTRADOR',
      ':acao'    => $log_acao,
      ':acao_id' => $admin_id,
      ':dados'   => json_encode($log_dados, JSON_UNESCAPED_UNICODE),
      ':user_id' => $reservm_admin_id
    ]);
    // -------------------------------

    $conn->commit(); // CONFIRMA A TRANSAÇÃO

    // CONFIGURAÇÃO DE MENSAGEM
    if ($acao === 'cadastrar') {
      $_SESSION["msg"] = "Dados cadastrados com sucesso!";
    } elseif ($acao === 'atualizar') {
      $_SESSION["msg"] = "Dados atualizados com sucesso!";
    } elseif ($_GET['acao'] === 'deletar') {
      $_SESSION["msg"] = "Dados excluídos com sucesso!";
    } elseif ($acao === 'deletar_conta') {
      header("Location: ../admin/perfil.php");
      exit;
    } else {
      // QUANDO CONTA EXCLUÍDA PELO PERFIL!
      header("Location: ../admin/sair.php");
      exit;
    }
    // -------------------------------
    header("Location: ../admin/admin.php");
    exit;
    // -------------------------------

  } catch (Exception $e) {
    $conn->rollBack();
    $_SESSION["erro"] = $e->getMessage();
    $_SESSION["form_admin"] = $_POST; // CRIA SESSÃO PARA OS DADOS DO FORMULÁRIO QUE FORAM ENVIADOS
    header("Location: ../admin/admin.php");
    exit;
  }
} else {
  $_SESSION["erro"] = "Requisição inválida.";
  header("Location: ../admin/admin.php");
  exit;
}
