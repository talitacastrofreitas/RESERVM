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
    $acao = $_POST['acao'];


    // -------------------------------
    // ACESSO
    // -------------------------------
    if ($acao === 'acesso') {

      $log_acao = 'Acesso';

      // POST
      $user_email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
      $user_senha = $_POST['senha'];

      // BUSCA OS DADOS DO USUÁRIO COM O E-MAIL INFORMADO
      $result_user = $conn->prepare("SELECT user_id, user_matricula, user_nome, user_email, user_senha, user_status FROM usuarios WHERE user_email = ?");
      $result_user->execute([$user_email]);
      $row_user = $result_user->fetch(PDO::FETCH_ASSOC);

      $user_id = $row_user['user_id']; // VARIÁVEL PARA O LOG

      // SE E-MAIL OU SENHA INCORRETO
      if (!$row_user || !password_verify($user_senha, $row_user['user_senha'])) {
        throw new Exception("Dados incorretos!");
      }

      // VERIFICA SE O USUÁRIO ESTÁ ATIVO
      if ($row_user['user_status'] != 1) {
        throw new Exception("Acesso restrito!");
      }

      // SE TUDO DER CERTO, INICIA A CESSÃO
      $_SESSION['session_user_logged_in'] = true;
      $_SESSION['reservm_user_id'] = $row_user['user_id'];
      $_SESSION['reservm_user_matricula'] = $row_user['user_matricula'];
      $_SESSION['reservm_user_nome'] = $row_user['user_nome'];
      $_SESSION['reservm_user_email'] = $row_user['user_email'];

      // ATUALIZA A DATA DE ACESSO DO USUÁRIO
      $stmt = $conn->prepare('UPDATE usuarios SET user_data_acesso = GETDATE() WHERE user_id = :user_id');
      $stmt->execute([':user_id' => $row_user['user_id']]);









      // -------------------------------
      // RECUPERAR SENHA
      // -------------------------------
    } elseif ($acao === 'recuperar') {

      $log_acao = 'Recuperar Senha';

      // POST
      if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("E-mail inválido!");
      }
      $user_email = $_POST['email'];

      // BUSCA OS DADOS DO USUÁRIO CONFORME E-MAIL INFORMADO
      $result = $conn->prepare("SELECT user_id, user_email FROM usuarios WHERE user_email = ? AND user_status = 1");
      $result->execute([$user_email]);
      $row = $result->fetch(PDO::FETCH_ASSOC);
      $user_id = $row['user_id']; // VARIÁVEL PARA O LOG

      if (!$row) {
        throw new Exception("E-mail não encontrado!");
      }

      // VERIFICA SE JÁ EXISTE UM TOKEN PARA O ID DO USUÁRIO
      $stmt = $conn->prepare("SELECT COUNT(*) FROM token WHERE tok_user_id = ?");
      $stmt->execute([$user_id]);

      // GERA UM TOKEN DE VALIDAÇÃO
      $codigo      = str_pad(random_int(1000000, 9999999), 7, '0', STR_PAD_LEFT); // GERA UM CÓDIGO DE 7 DÍGITOS
      $codigo_hash = password_hash($codigo, PASSWORD_DEFAULT); // CRIPTOGRAFA O CÓDIGO
      $expira_em   = date('Y-m-d H:i:s', strtotime('+2 minutes'));

      if ($stmt->fetchColumn() > 0) {
        // SE HOUVER, ATUALIZA O TOKEN E A DATA DE VALIDADE
        $stmt = $conn->prepare("UPDATE token SET tok_codigo = :tok_codigo, tok_data_valida = :tok_data_valida WHERE tok_user_id = :tok_user_id");
      } else {
        // CRIA UM TOKEN PARA O ID DO USUÁRIO, CASO NÃO EXISTA
        $stmt = $conn->prepare('INSERT INTO token (tok_codigo, tok_user_id, tok_data_valida) VALUES (:tok_codigo, :tok_user_id, :tok_data_valida)');
      }
      $stmt->execute([':tok_codigo' => $codigo_hash, ':tok_user_id' => $user_id, ':tok_data_valida' => $expira_em]);
      // -------------------------------

      // DISPARA E-MAIL
      $mail = new PHPMailer(true);
      include '../conexao/email.php';
      // $mail->addAddress($row['user_email'], 'RESERVM');
      $mail->addAddress($email_saap, 'RESERVM');
      $mail->isHTML(true);
      $mail->Subject = 'Seu código de acesso chegou'; //TÍTULO DO E-MAIL

      // CORPO DO EMAIL
      include '../includes/email/email_header.php';
      $email_conteudo .= "
        <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
          <td style='padding: 2em 2rem; display: inline-block;'>

          <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 20px 0px;'>
          Olá,
          </p>

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
          <a style='cursor: pointer;' href='$url_sistema/us-validcod.php?i=$user_id'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 20px;' target='_blank'>Acesse o sistema e valide seu código.</button></a>
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
      // VALIDAR CÓDIGO
      // -------------------------------
    } elseif ($acao === 'validar') {

      $log_acao = 'Validar Código';

      // POST
      $user_id = $_POST['i']; // DECODIFICA O ID DO USUÁRIO - 'true' EVITA ERROS SILENCIOSOS

      // VERIFICA SE É UM NÚMERO (CHAR(32)) VÁLIDO
      if ($user_id === false || $user_id === "" || !preg_match('/^[a-f0-9]{32}$/i', $user_id)) {
        $_SESSION["erro"] = "Código inválido!";
        header("Location: ../us-validcod.php");
        exit;
      }

      $result_tok = $conn->prepare("SELECT * FROM token
                                    INNER JOIN usuarios ON usuarios.user_id COLLATE SQL_Latin1_General_CP1_CI_AI = token.tok_user_id
                                    WHERE tok_user_id = ?");
      $result_tok->execute([$user_id]);
      $row_tok = $result_tok->fetch(PDO::FETCH_ASSOC);

      // VERIFICA SE O ID EXISTE NA TABELA
      $stmt = $conn->prepare("SELECT COUNT(*) FROM token WHERE tok_user_id = :user_id");
      $stmt->execute([':user_id' => $user_id]);
      $val_token = $stmt->fetchColumn();
      // -------------------------------

      // SE TOKEN EXISTIR E O TOKEN FOR DO ID INFORMADO
      if ((!empty($row_tok)) && ($val_token != 0)) {

        // O TOKEN TEM VALIDADE DE 24 HORAS
        $dataReal    = new DateTime(); // DATA EM TEMPO REAL
        $data_valida = new DateTime($row_tok['tok_data_valida']); // DATA DE VALIDADE DO TOKEN

        $tok_codigo = $_POST['cod1'] . $_POST['cod2'] . $_POST['cod3'] . $_POST['cod4'] . $_POST['cod5'] . $_POST['cod6'] . $_POST['cod7'];

        // SE A DATA DE VALIDADE DO TOKEN FOR MAIOR QUE A DATA REAL, SEGUE A VALIDAÇÃO
        if ($data_valida < $dataReal && password_verify($tok_codigo, $row_tok['tok_codigo'])) {

          $sql = "DELETE FROM token WHERE tok_user_id = :tok_user_id";
          $stmt = $conn->prepare($sql);
          $stmt->execute([':tok_user_id' => $user_id]);

          // $_SESSION["erro"] = "O código informado expirou!";
          // header("Location: ../us-validcod.php");
          // exit;

          throw new Exception("O código informado expirou!");
        }

        // SE O CÓDIGO ESTIVER CORRETO, SEGUE PARA FORMULÁRIO DE CRIAÇÃO DE SENHA
        if (!password_verify($tok_codigo, $row_tok['tok_codigo'])) {
          throw new Exception("Código inválido!");
        }
      } else {
        throw new Exception("Código inválido!");
      }













      // -------------------------------
      // CRIAR SENHA
      // -------------------------------
    } elseif ($acao === 'password') {

      $log_acao = 'Criar Senha';

      // POST
      $user_id    = $_POST['i']; // DECODIFICA O ID DO USUÁRIO - 'true' EVITA ERROS SILENCIOSOS
      $user_senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); //CODIFICA A NOVA SENHA DIGITADA

      // VERIFICA SE É UM NÚMERO (CHAR(32)) VÁLIDO
      if ($user_id === false || $user_id === "" || !preg_match('/^[a-f0-9]{32}$/i', $user_id)) {
        $_SESSION["erro"] = "Código inválido!";
        header("Location: ../us-validcod.php");
        exit;
      }

      $sql = "UPDATE usuarios SET user_senha = :user_senha, user_data_reset_senha = GETDATE(), user_status = :user_status WHERE user_id = :user_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':user_id' => $user_id, ':user_senha' => $user_senha, ':user_status' => 1]);

      // APÓS ATUALIZAR A SENHA, O TOKEN REFERENTE AO USUÁRIO É EXCLUÍDO
      $sql = "DELETE FROM token WHERE tok_user_id = :tok_user_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':tok_user_id' => $user_id]);
      // -------------------------------


      $result_tok = $conn->prepare("SELECT * FROM token
                                    INNER JOIN usuarios ON usuarios.user_id COLLATE SQL_Latin1_General_CP1_CI_AI = token.tok_user_id
                                    WHERE tok_user_id = ?");
      $result_tok->execute([$user_id]);
      $row_tok = $result_tok->fetch(PDO::FETCH_ASSOC);

      // VERIFICA SE O ID EXISTE NA TABELA
      $stmt = $conn->prepare("SELECT COUNT(*) FROM token WHERE tok_user_id = :user_id");
      $stmt->execute([':user_id' => $user_id]);
      $val_token = $stmt->fetchColumn();
      // -------------------------------












      // -------------------------------
      // REENVIA O CÓDIGO
      // -------------------------------
    } elseif ($_GET['acao'] === 'SendCod') {

      if (empty($_GET['i'])) {
        throw new Exception("Erro ao obter o ID!");
      }
      $user_id  = $_GET['i'];
      $log_acao = 'Reenviar Código';

      // BUSCA O E-MAIL DO USUÁRIO
      $stmt = $conn->prepare("SELECT user_email FROM usuarios WHERE user_id = :user_id");
      $stmt->execute([':user_id' => $user_id]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $user_email = $row['user_email'];

      // GERA UM TOKEN DE VALIDAÇÃO
      $codigo      = str_pad(random_int(1000000, 9999999), 7, '0', STR_PAD_LEFT); // GERA UM CÓDIGO DE 7 DÍGITOS
      $codigo_hash = password_hash($codigo, PASSWORD_DEFAULT); // CRIPTOGRAFA O CÓDIGO
      $expira_em   = date('Y-m-d H:i:s', strtotime('+2 minutes'));

      // ATUALIZA O TOKEN DO ID DO USUÁRIO
      $stmt = $conn->prepare("UPDATE token SET tok_codigo = :tok_codigo, tok_data_valida = :tok_data_valida WHERE tok_user_id = :tok_user_id");
      $stmt->execute([':tok_codigo' => $codigo_hash, ':tok_user_id' => $user_id, ':tok_data_valida' => $expira_em]);
      // -------------------------------

      // DISPARA E-MAIL
      $user_id_cript = $user_id; // CRIPTOGRAFA ID

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
        Olá,
        </p>

        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 20px 0px;'>
        Seu código do SIGAEXT chegou!
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

        <a style='cursor: pointer;' href='$url_sistema/us-validcod.php?tk=$user_id_cript'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 20px;' target='_blank'>Acesse o sistema e valide seu código.</button></a>

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
      // AÇÃO INVÁLIDA
      // -------------------------------
    } else {
      throw new Exception("Ação inválida.");
    }

    // REGISTRA NO LOG
    // MODIFICAÇÃO PARA O LOG NÃO REGISTRAR A SENHA DIGITADA
    $log_post = $_POST;
    $log_post['senha'] = '****'; // MÁSCARA DA SENHA
    $log_dados = ['POST' => $log_post, 'GET' => $_GET];
    //
    $sqlLog = "INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data)
              VALUES (:modulo, upper(:acao), :acao_id, :dados, :user_id, GETDATE())";
    $stmtLog = $conn->prepare($sqlLog);
    $stmtLog->execute([
      ':modulo'  => 'AUTENTICAÇÃO',
      ':acao'    => $log_acao,
      ':acao_id' => $user_id,
      ':dados'   => json_encode($log_dados, JSON_UNESCAPED_UNICODE),
      ':user_id' => $user_id
    ]);
    // -------------------------------

    $conn->commit(); // CONFIRMA A TRANSAÇÃO

    // CONFIGURAÇÃO DE MENSAGEM  
    if ($acao === 'acesso') {
      header("Location: $url_sistema/painel.php");
      exit();
      //
    } elseif (isset($_GET['acao']) && $_GET['acao'] === 'SendCod') {
      header("Location: ../us-validcod.php");
      exit();
      //
    } elseif ($acao === 'recuperar') {
      session_start();
      $_SESSION['user_val_cod'] = $user_id;
      header("Location: ../us-validcod.php");
      exit();
      //
    } elseif ($acao === 'validar') {
      session_start();
      $_SESSION['user_id_cript'] = $user_id;
      header("Location: ../us-creatpass.php");
      exit();
      //
    } elseif ($acao === 'password') {
      $_SESSION["msg"] = "Senha criada com sucesso!";
      header("Location: $url_sistema");
      exit();
      //
    } else {
      // QUANDO CONTA EXCLUÍDA PELO PERFIL!
      header("Location: ../sair.php");
      exit();
    }




    // -------------------------------
    //header("Location: ../admin/admin.php");
    //header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    //exit;
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
