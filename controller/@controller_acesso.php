<?php
session_start();
ob_start(); // Limpa o buff de saida
include '../conexao/conexao.php';

// NECESSÁRIO PARA ENVIAR O EMAIL
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require '../vendor/autoload.php';
// -------------------------------

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                            USUÁRIO ACESSA O SISTEMA
 *****************************************************************************************/

// RECEBE OS DADOS DO LOGIN
if (isset($dados['loginUser'])) {

  $user_email = $dados['email'];
  $query_user = "SELECT * FROM usuarios WHERE user_email = :user_email";
  $result_user = $conn->prepare($query_user);
  $result_user->execute([':user_email' => $user_email]);

  try {
    if (($result_user) and ($result_user->rowCount() != 0)) {
      $row_user = $result_user->fetch(PDO::FETCH_ASSOC);

      if ($row_user['user_status'] == 1) { // '1 = ATIVO' => SE O STATUS DO USUÁRIO FOR 'INATIVO', NÃO PODE LOGAR

        // SE LOGIN E SENHA ESTIVEREM CORRETOS, ENTRA NO SISTEMA
        if ($user_email == $row_user['user_email'] && password_verify($dados['senha'], $row_user['user_senha']) == 1) {
          $_SESSION['session_user_logged_in'] = true;
          $_SESSION['reservm_user_id']           = $row_user['user_id'];
          $_SESSION['reservm_user_email']        = $row_user['user_email'];
          header("Location: $url_sistema/painel.php");

          // REGISTRA AÇÃO NO LOG
          $conn->beginTransaction(); // INICIA A TRANSAÇÃO
          $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data)
                                  VALUES (:modulo, :acao, :acao_id, :user_id, GETDATE())');
          $stmt->execute([
            ':modulo'    => 'AUTENTICAÇÃO',
            ':acao'      => 'ACESSO',
            ':acao_id'   => $row_user['user_id'],
            ':user_id'   => $row_user['user_id']
          ]);
          // -------------------------------

          // CADASTRA DA DATA QUE O USUÁRIO LOGA NO SISTEMA
          $stmt = $conn->prepare('UPDATE usuarios SET user_data_acesso = GETDATE() WHERE user_id = :user_id');
          $stmt->execute([':user_id' => $row_user['user_id']]);
        } else {
          $_SESSION["erro"] = "E-mail ou senha incorreta";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
          return die;
        }
      } else {
        $_SESSION["rest"] = "Acesso restrito!";
        header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      }
    } else {
      $_SESSION["erro"] = "E-mail ou senha incorreta";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      return die;
    }

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Erro ao tentar logar!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
}








/*****************************************************************************************
                        USUÁRIO PEDE PARA RECUPERAR A SENHA
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "reset") {

  $user_email = $_POST['email'];

  // BUSCA OS DADOS DO USUÁRIO CONFORME E-MAIL INFORMADO
  $query = "SELECT * FROM usuarios WHERE user_email = :user_email AND user_status = 1";
  $result = $conn->prepare($query);
  $result->execute([':user_email' => $user_email]);



  if (($result) and ($result->rowCount() != 0)) {
    $row = $result->fetch(PDO::FETCH_ASSOC);
    extract($row);

    // CADASTRA O TOKEN PARA VALIDAÇÃO DA CONTA

    $codigo      = str_pad(mt_rand(0, 9999999), 7, '0', STR_PAD_LEFT); // GERA UM CÓDIGO DE 7 DÍGITOS
    $codigo_hash = password_hash($codigo, PASSWORD_DEFAULT); // CRIPTOGRAFA O CÓDIGO

    // VERIFICA SE JÁ EXISTE UM TOKEN PARA O ID DO USUÁRIO
    $sql = "SELECT COUNT(*) FROM token WHERE tok_user_id = :tok_user_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':tok_user_id' => $user_id]);
    if ($stmt->fetchColumn() > 0) {

      // SE HOUVER, ATUALIZA O TOKEN E A DATA DE VALIDADE
      $sql = "UPDATE token SET tok_codigo = :tok_codigo, tok_data_valida = DATEADD(day, 1, GETDATE()) WHERE tok_user_id = :tok_user_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':tok_user_id' => $user_id,
        ':tok_codigo' => $codigo_hash
      ]);
    } else {

      // CRIA UM TOKEN PARA O ID DO USUÁRIO, CASO NÃO EXISTA
      $stmt = $conn->prepare('INSERT INTO token (
                                                    tok_codigo,
                                                    tok_user_id,
                                                    tok_data_valida
                                                  ) VALUES (
                                                    :tok_codigo,
                                                    :tok_user_id,
                                                    DATEADD(day, 3, GETDATE())
                                                  )');
      $stmt->execute([
        ':tok_codigo'      => $codigo_hash,
        ':tok_user_id'     => $user_id
      ]);
    }
    // -------------------------------

    // ENVIAR O TOKEN PARA O E-MAIL DO USUÁRIO
    $user_id_cript = base64_encode($user_id); // CRIPTOGRAFA ID

    $mail = new PHPMailer(true);
    include '../conexao/email.php';

    // E-MAIL QUE VAI RECEBER A MENSAGEM
    $mail->addAddress($user_email, 'RESERVM');

    // CONTEÚDO
    $mail->isHTML(true);
    $mail->Subject = 'Seu código de acesso é ' . $codigo;

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
        Ao copiá-lo volte à página de acesso e insira o código abaixo para confirmar sua identidade.
        </p>

        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 50px 0px;'>
        Lembrando que essa etapa é muito importante para mantermos a segurança dos seus dados e cumprirmos nosso compromisso com você.
        </p>

        <p style='font-size: 1.3rem; font-weight: 600; margin: 0px 0px 15px 0px;'>
        Seu código de acesso é:
        </p>

        <span style='color: #285FAB; font-size: 2rem; letter-spacing: 0.20rem; font-weight: 600; margin-bottom: 20px; display: inline-block;'>$codigo</span><br>
        </td>
      </tr>";
    include '../includes/email/email_footer.php';

    $mail->Body  = $email_conteudo;
    $mail->send();
    // -------------------------------   

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES (:modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'AUTENTICAÇÃO',
      ':acao'       => 'ESQUECI MINHA SENHA',
      ':acao_id'    => $user_id,
      ':dados'      => 'E-mail: ' . $user_email,
      ':user_id'    => $user_id
    ]);
    // -------------------------------

    header("Location: ../us-validcod.php?us-ident=" . $user_id_cript);
  } else {
    $_SESSION["erro"] = "E-mail não encontrados!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
}










/*****************************************************************************************
                            VALIDA CÓDIGO PARA CRIAR SENHA
 *****************************************************************************************/
if (isset($dados['ValCod'])) {

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO

    $user_id = $dados['i']; // DECODIFICA O ID DO USUÁRIO
    $query_tok = "SELECT * FROM token
                  INNER JOIN usuarios ON usuarios.user_id COLLATE SQL_Latin1_General_CP1_CI_AI = token.tok_user_id
                  WHERE tok_user_id = :tok_user_id";
    $result_tok = $conn->prepare($query_tok);
    $result_tok->execute([':tok_user_id' => $user_id]);

    $row_tok = $result_tok->fetch(PDO::FETCH_ASSOC);

    $val_token = $conn->query("SELECT COUNT(*) FROM token WHERE tok_user_id = '$user_id'")->fetchColumn(); // VERIFICA SE O ID EXISTE NA TABELA

    // SE TOKEN EXISTIR E O TOKEN FOR DO ID INFORMADO
    if ((!empty($row_tok)) && ($val_token != 0)) {

      // O TOKEN TEM VALIDADE DE 24 HORAS
      $dataReal    = new DateTime(); // DATA EM TEMPO REAL
      $data_valida = new DateTime($row_tok['tok_data_valida']); // DATA DE VALIDADE DO TOKEN

      // SE A DATA DE VALIDADE DO TOKEN FOR MAIOR QUE A DATA REAL, SEGUE A VALIDAÇÃO
      if ($data_valida >= $dataReal) {

        $tok_codigo = $dados['cod1'] . $dados['cod2'] . $dados['cod3'] . $dados['cod4'] . $dados['cod5'] . $dados['cod6'] . $dados['cod7'];

        // SE O CÓDIGO ESTIVER CORRETO, SEGUE PARA FORMULÁRIO DE CRIAÇÃO DE SENHA
        if (password_verify($tok_codigo, $row_tok['tok_codigo']) == 1) {
          header("Location: ../us-creatpass.php?us-creat-pass=" . $dados['i']);

          // REGISTRA AÇÃO NO LOG
          $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                            VALUES (:modulo, :acao, :acao_id, :user_id, GETDATE())');
          $stmt->execute([
            ':modulo'     => 'AUTENTICAÇÃO',
            ':acao'       => 'VALIDA TOKEN',
            ':acao_id'    => $user_id,
            ':user_id'    => $user_id
          ]);
          // -------------------------------

        } else {
          $_SESSION["erro"] = "Código inválido!";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
          return die;
        }
      } else {

        // APÓS ATUALIZAR A SENHA, O TOKEN REFERENTE AO USUÁRIO É EXCLUÍDO
        $sql = "DELETE FROM token WHERE tok_user_id = '$user_id'";
        $conn->exec($sql);
        // -------------------------------

        $_SESSION["erro"] = "O código informado expirou!";
        header("Location: " . $url_sistema);
      }
    } else {
      $_SESSION["erro"] = "Código inválido!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    }
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

  } catch (PDOException $e) {
    // echo "Erro: " . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Não foi possível validar o código!";
    header("Location: " . $url_sistema);
  }
}













/*****************************************************************************************
              ALTERAR A SENHA DO USUÁRIO SE A SENHA EXPIRAR
 *****************************************************************************************/
// if (isset($_GET['funcao']) && $_GET['funcao'] == "alterSenhaExpira") {

//   $user_id         = base64_decode($_POST['cod']); // DECODIFICA O ID DO USUÁRIO
//   $user_senha      = password_hash($_POST['senha'], PASSWORD_DEFAULT); //CODIFICA A NOVA SENHA DIGITADA
//   $user_conf_senha = password_verify($_POST['conf_senha'], $user_senha); //VERIFICA SE A SENHA DE CONFIRMAÇÃO ESTÁ IGUAL A DIGITADA.

//   // BUSCA OS DADOS DO USUÁRIO CONFORME ID INFORMADO
//   $query = "SELECT * FROM usuarios WHERE user_id = :user_id";
//   $result = $conn->prepare($query);
//   $result->execute([':user_id' => $user_id]);

//   if (($result) and ($result->rowCount() != 0)) {
//     $row = $result->fetch(PDO::FETCH_ASSOC);
//     $user_nome  = $row['user_nome'];
//   } else {
//     $_SESSION["erro"] = "Erro ao tentar atualizar a senha!";
//     header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
//     return die;
//   }
//   // -------------------------------

//   // NOVA SENHA PRECISA SER DIFERENTE DA ATUAL
//   if ($_POST['senha_atual'] == $_POST['senha']) {
//     $_SESSION["erro"] = "A nova senha precisa ser diferente da atual!";
//     header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
//     return die;
//     // -------------------------------
//   } else {

//     // VALIDA SENHA ATUAL
//     $stmt = $conn->prepare("SELECT user_id, user_senha FROM usuarios WHERE user_id = :user_id");
//     $stmt->execute([':user_id' => $user_id]);

//     $result = $stmt->fetch(PDO::FETCH_ASSOC);
//     $user_senha_atual  = password_verify($_POST['senha_atual'], $result['user_senha']); //CONFERE SE A SENHA ATUAL DIGITADA ESTÁ IGUAL A DO BANCO
//     if ($user_senha_atual != 1) {
//       $_SESSION["erro"] = "A senha atual está incorreta!";
//       header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
//       return die;
//       // -------------------------------

//     } else {
//       // AS DUAS SENHAS DIGITADAS PRECISAM SER IGUAIS
//       if ($user_conf_senha != 1) {
//         $_SESSION["erro"] = "As senhas digitadas estão diferentes!";
//         header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
//         return die;
//         // -------------------------------

//       } else {

//         // EDITA SENHA 
//         try {
//           $stmt = $conn->prepare('UPDATE usuarios SET user_senha = :user_senha, user_data_reset_senha = GETDATE() WHERE user_id = :user_id');
//           $stmt->execute([
//             ':user_id' => $user_id,
//             ':user_senha' => $user_senha
//           ]);

//           //  REGISTRA AÇÃO NO LOG
//           $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
//                                   VALUES (:modulo, :acao, :acao_id, :user_id, GETDATE())');
//           $stmt->execute([
//             ':modulo'     => 'AUTENTICAÇÃO',
//             ':acao'       => 'ALTERA A SENHA EXPIRADA',
//             ':acao_id'    => $user_id,
//             ':user_id'    => $user_id
//           ]);
//           // -------------------------------

//           //ENVIA MENSAGEM
//           $_SESSION["msg"] = "Senha atualizada!";
//           header("Location: $url_sistema");
//         } catch (PDOException $e) {
//           // echo 'Error: ' . $e->getMessage();
//           $_SESSION["erro"] = "Erro ao tentar atualizar a senha!";
//           header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
//           return die;
//         }
//       }
//     }
//   }
// }










/*****************************************************************************************
                    ALTERAR A SENHA DO USUÁRIO PELO PERFIL
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "alterSenhaPerfil") {

  $admin_id         = $_POST['cod']; // DECODIFICA O ID DO USUÁRIO
  $admin_senha      = password_hash($_POST['senha'], PASSWORD_DEFAULT); //CODIFICA A NOVA SENHA DIGITADA
  $admin_conf_senha = password_verify($_POST['conf_senha'], $admin_senha); //VERIFICA SE A SENHA DE CONFIRMAÇÃO ESTÁ IGUAL A DIGITADA.

  // NOVA SENHA PRECISAR SER DIFERENTE DA AUAL
  if ($_POST['senha_atual'] == $_POST['senha']) {
    $_SESSION["erro_perfil"] = "A nova senha precisa ser diferente da atual!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
    // -------------------------------
  } else {

    // VALIDA SENHA ATUAL
    $stmt = $conn->prepare("SELECT admin_id, admin_senha FROM admin WHERE admin_id = :admin_id");
    $result_tok->execute([':admin_id' => $admin_id]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $admin_senha_atual = password_verify($_POST['senha_atual'], $result['admin_senha']); //CONFERE SE A SENHA ATUAL DIGITADA ESTÁ IGUAL A DO BANCO
    if ($admin_senha_atual != 1) {
      $_SESSION["erro_perfil"] = "A senha atual está incorreta!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      return die;
      // -------------------------------

    } else {
      // AS DUAS SENHAS DIGITADAS PRECISAM SER IGUAIS
      if ($admin_conf_senha != 1) {
        $_SESSION["erro_perfil"] = "As senhas digitadas estão diferentes!";
        header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        return die;
        // -------------------------------
      } else {

        // EDITA SENHA 
        try {
          $conn->beginTransaction(); // INICIA A TRANSAÇÃO
          $stmt = $conn->prepare('UPDATE admin SET admin_senha = :admin_senha, admin_data_reset_senha = GETDATE() WHERE admin_id = :admin_id');
          $stmt->execute([
            ':admin_id' => $admin_id,
            ':admin_senha' => $admin_senha
          ]);

          // REGISTRA AÇÃO NO LOG
          $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                                  VALUES (:modulo, :acao, :acao_id, :user_id, GETDATE())');
          $stmt->execute([
            ':modulo'     => 'AUTENTICAÇÃO',
            ':acao'       => 'ALTERA A SENHA PELO PERFIL',
            ':acao_id'    => $admin_id,
            ':user_id'    => $admin_id
          ]);
          // -------------------------------

          $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

          //ENVIA MENSAGEM
          $_SESSION["msg_perfil"] = "Senha atualizada!";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        } catch (PDOException $e) {
          // echo 'Error: ' . $e->getMessage();
          $conn->rollBack();
          $_SESSION["erro_perfil"] = "Erro ao tentar atualizar a senha!";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
          return die;
        }
      }
    }
  }
}









/*****************************************************************************************
                      CRIA A SENHA DO USUÁRIO
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "alterSenha") {

  $user_id    = $_POST['cod']; // DECOFICIA O ID DO USUÁRIO
  $user_senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); //CODIFICA A NOVA SENHA DIGITADA

  // EDITA SENHA 
  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "UPDATE
                    usuarios
              SET
                    user_senha            = :user_senha,
                    user_data_reset_senha = GETDATE()
              WHERE
                    user_id = :user_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':user_id' => $user_id,
      ':user_senha' => $user_senha
    ]);

    // APÓS ATUALIZAR A SENHA, O TOKEN REFERENTE AO USUÁRIO É EXCLUÍDO
    $sql = "DELETE FROM token WHERE tok_user_id = '$user_id'";
    $conn->exec($sql);
    // -------------------------------

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                              VALUES (:modulo, :acao, :acao_id, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'AUTENTICAÇÃO',
      ':acao'       => 'CRIA NOVA SENHA',
      ':acao_id'    => $user_id,
      ':user_id'    => $user_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Senha redefinida!";
    header("Location: $url_sistema");
  } catch (PDOException $e) {
    // echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Erro ao tentar criar a senha!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
}
