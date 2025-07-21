<?php
session_start();
ob_start(); // Limpa o buff de saida
include '../../conexao/conexao.php';

// NECESSÁRIO PARA ENVIAR O EMAIL
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require '../../vendor/autoload.php';
// -------------------------------

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                            ACESSA O SISTEMA
 *****************************************************************************************/
// RECEBE OS DADOS DO LOGIN
if (isset($dados['login_admin'])) {

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO

    // VERIFICA SE MATRÍCULA DO ADMINISTRADOR AINDA ESTÁ ATIVA NA BASE DE DADOS DA VISÃO
    $admin_matricula = $dados['matricula'];
    $sql = "SELECT COUNT(*) FROM $view_colaboradores WHERE CHAPA = :CHAPA";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':CHAPA' => $admin_matricula]);
    if ($stmt->fetchColumn() > 0) {

      // VERIFICA SE A MATRÍCULA DO ADMINISTRADOR É VÁLIDA
      $admin_matricula = $dados['matricula'];
      $query_admin = "SELECT * FROM admin WHERE admin_matricula = :admin_matricula AND admin_status IN (1)";
      $result_admin = $conn->prepare($query_admin);
      $result_admin->execute([':admin_matricula' => $admin_matricula]);

      if (($result_admin) and ($result_admin->rowCount() != 0)) {
        $row_admin = $result_admin->fetch(PDO::FETCH_ASSOC);

        if ($row_admin['nivel_acesso'] !== 2) { // SE O NÍVEL DE ACESSO DO ADMIN FOR '2', NÃO PODE ACESSAR. A CONTA FOI EXCLUÍDA

          if ($row_admin['admin_status'] == 1) { // SE O STATUS DO ADMINISTRADOR FOR 'INATIVO', NÃO PODE LOGAR

            // A SENHA DO ADMINISTRADOR EXPIRA EM 90 DIAS (3 MESES)
            $data_hoje  = new DateTime(date('Y-m-d H:i:s')); // RECUPERADA A DATA EM TEMPO REAL
            $data_senha = new DateTime($row_admin['admin_data_reset_senha']); // RECUPERA A DATA DA ULTIMA ATUALIZAÇÃO DA SENHA
            $intervalo = $data_hoje->diff($data_senha); // FAZ A DIFERENÇA ENTRE A DATA DE HOJE E A DATA DA ULTIMA ATUALIZAÇÃO DA SENHA
            $diferenca_data = $intervalo->days;

            if ($diferenca_data > 90 && $admin_matricula == $row_admin['admin_matricula'] && password_verify($dados['senha'], $row_admin['admin_senha']) == 1) {
              header("Location: ../ad-newpass-ex.php?ad-ident=" . base64_encode($row_admin['admin_id']));
            } else {

              // SE LOGIN E SENHA ESTIVEREM CORRETOS, ENTRA NO SISTEMA
              if ($admin_matricula == $row_admin['admin_matricula'] && password_verify($dados['senha'], $row_admin['admin_senha']) == 1) {
                $_SESSION['session_admin_logged_in'] = true;
                $_SESSION['reservm_admin_id']           = $row_admin['admin_id'];
                $_SESSION['reservm_admin_nome']         = $row_admin['admin_nome'];
                $_SESSION['reservm_admin_email']        = $row_admin['admin_email'];
                $_SESSION['reservm_admin_matricula']    = $row_admin['admin_matricula'];
                $_SESSION['reservm_admin_perfil']       = $row_admin['admin_perfil'];
                $_SESSION['reservm_admin_status']       = $row_admin['admin_status'];
                $_SESSION['reservm_admin_nivel_acesso'] = $row_admin['nivel_acesso'];
                header("Location: $url_sistema/admin/painel.php");

                // REGISTRA AÇÃO NO LOG
                $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data)
                                        VALUES (:modulo, :acao, :acao_id, :user_id, GETDATE())');
                $stmt->execute([
                  ':modulo'    => 'AUTENTICAÇÃO',
                  ':acao'      => 'ACESSO',
                  ':acao_id'   => $_SESSION['reservm_admin_id'],
                  ':user_id'   => $_SESSION['reservm_admin_id']
                ]);
                // -------------------------------

                // CADASTRA DA DATA QUE O ADMINISTRADOR LOGA NO SISTEMA
                $stmt = $conn->prepare('UPDATE admin SET admin_data_acesso = GETDATE() WHERE admin_id = :admin_id');
                $stmt->execute([':admin_id' => $row_admin['admin_id']]);
              } else {
                $_SESSION["erro"] = "Matrícula ou senha incorretos";
                echo "<script> history.go(-1);</script>";
                return die;
              }
            }
          } else {
            $_SESSION["rest"] = "Acesso restrito!";
            header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
          }
        } else {
          $_SESSION["erro"] = "Matrícula ou senha incorretos";
          echo "<script> history.go(-1);</script>";
          return die;
        }
      } else {
        $_SESSION["erro"] = "Matrícula ou senha incorretos";
        echo "<script> history.go(-1);</script>";
        return die;
      }
    } else {
      $_SESSION["erro"] = "Acesso restrito!";
      echo "<script> history.go(-1);</script>";
      return die;
    }
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Erro ao tentar executar a ação!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
}








/*****************************************************************************************
                            VALIDA CÓDIGO PARA CRIAR SENHA
 *****************************************************************************************/
if (isset($dados['ValCod'])) {

  $admin_id = base64_decode($dados['cod']); // DECODIFICA O ID DO ADMINISTRADOR

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO

    $query_tok = "SELECT * FROM token
                  INNER JOIN admin ON admin.admin_id COLLATE SQL_Latin1_General_CP1_CI_AI = token.tok_user_id
                  WHERE tok_user_id = :tok_user_id";
    $result_tok = $conn->prepare($query_tok);
    $result_tok->execute([':tok_user_id' => $admin_id]);
    $row_tok = $result_tok->fetch(PDO::FETCH_ASSOC);

    $val_token = $conn->query("SELECT COUNT(*) FROM token WHERE tok_user_id = '$admin_id'")->fetchColumn(); // VERIFICA SE O ID EXISTE NA TABELA

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

          // REGISTRA AÇÃO NO LOG
          $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                                  VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE() )');
          $stmt->execute([
            ':modulo'     => 'AUTENTICAÇÃO',
            ':acao'       => 'VALIDA TOKEN',
            ':acao_id'    => $admin_id,
            ':user_id'    => $admin_id
          ]);
          // -------------------------------

          header("Location: ../ad-creatpass.php?ad-ident=" . $dados['cod']);
        } else {
          $_SESSION["erro"] = "Código inválido!";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
          return die;
        }
      } else {
        $_SESSION["erro"] = "Este código expirou!";
        header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
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
    $_SESSION["erro"] = "Erro ao tentar executar a ação!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}








/*****************************************************************************************
              ALTERAR A SENHA DO ADMINISTRADOR SE A SENHA EXPIRAR
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "alterSenhaExpira") {

  $admin_id         = base64_decode($_POST['cod']); // DECODIFICA O ID DO ADMINISTRADOR
  $admin_senha      = password_hash($_POST['senha'], PASSWORD_DEFAULT); //CODIFICA A NOVA SENHA DIGITADA
  $admin_conf_senha = password_verify($_POST['conf_senha'], $admin_senha); //VERIFICA SE A SENHA DE CONFIRMAÇÃO ESTÁ IGUAL A DIGITADA.

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO

    // BUSCA OS DADOS DO ADMINISTRADOR CONFORME ID INFORMADO
    $query = "SELECT * FROM admin WHERE admin_id = :admin_id";
    $result = $conn->prepare($query);
    $result->execute([':admin_id' => $admin_id]);
    if (($result) and ($result->rowCount() != 0)) {
      $row = $result->fetch(PDO::FETCH_ASSOC);
      $admin_nome  = $row['admin_nome'];
    } else {
      $_SESSION["erro"] = "Erro ao tentar atualizar a senha!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      return die;
    }
    // -------------------------------

    // NOVA SENHA PRECISA SER DIFERENTE DA ATUAL
    if ($_POST['senha_atual'] == $_POST['senha']) {
      $_SESSION["erro"] = "A nova senha precisa ser diferente da atual!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      return die;
      // -------------------------------
    } else {

      // VALIDA SENHA ATUAL
      $stmt = $conn->prepare("SELECT admin_id, admin_senha FROM admin WHERE admin_id = :admin_id");
      $stmt->execute([':admin_id' => $admin_id]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      $admin_senha_atual  = password_verify($_POST['senha_atual'], $result['admin_senha']); //CONFERE SE A SENHA ATUAL DIGITADA ESTÁ IGUAL A DO BANCO
      if ($admin_senha_atual != 1) {
        $_SESSION["erro"] = "A senha atual está incorreta!";
        header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        return die;
        // -------------------------------

      } else {
        // AS DUAS SENHAS DIGITADAS PRECISAM SER IGUAIS
        if ($admin_conf_senha != 1) {
          $_SESSION["erro"] = "As senhas digitadas estão diferentes!";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
          return die;
          // -------------------------------

        } else {

          // EDITA SENHA 
          $stmt = $conn->prepare('UPDATE admin SET admin_senha = :admin_senha, admin_data_reset_senha = GETDATE() WHERE admin_id = :admin_id');
          $stmt->execute([
            ':admin_id' => $admin_id,
            ':admin_senha' => $admin_senha
          ]);

          // REGISTRA AÇÃO NO LOG
          $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                                  VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
          $stmt->execute([
            ':modulo'     => 'AUTENTICAÇÃO',
            ':acao'       => 'ALTERA A SENHA EXPIRADA',
            ':acao_id'    => $admin_id,
            ':user_id'    => $admin_id
          ]);
          // -------------------------------

          $_SESSION["msg"] = "Senha atualizada com sucesso!";
          header("Location: $url_sistema/admin");
        }
      }
    }
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Erro ao tentar executar a ação!" . $e->getMessage();
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
}










/*****************************************************************************************
                    ALTERAR A SENHA DO ADMINISTRADOR PELO PERFIL
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "alterSenhaPerfil") {

  $admin_id         = $_POST['cod']; // DECODIFICA O ID DO ADMINISTRADOR
  $admin_senha      = password_hash($_POST['senha'], PASSWORD_DEFAULT); //CODIFICA A NOVA SENHA DIGITADA
  $admin_conf_senha = password_verify($_POST['conf_senha'], $admin_senha); //VERIFICA SE A SENHA DE CONFIRMAÇÃO ESTÁ IGUAL A DIGITADA.

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO

    // NOVA SENHA PRECISAR SER DIFERENTE DA AUAL
    if ($_POST['senha_atual'] == $_POST['senha']) {
      $_SESSION["erro"] = "A nova senha precisa ser diferente da atual!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      return die;
      // -------------------------------
    } else {

      // VALIDA SENHA ATUAL
      $stmt = $conn->prepare("SELECT admin_id, admin_senha FROM admin WHERE admin_id = :admin_id");
      $stmt->execute([':admin_id' => $admin_id]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      $admin_senha_atual  = password_verify($_POST['senha_atual'], $result['admin_senha']); //CONFERE SE A SENHA ATUAL DIGITADA ESTÁ IGUAL A DO BANCO
      if ($admin_senha_atual != 1) {
        $_SESSION["erro"] = "A senha atual está incorreta!";
        header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        return die;
        // -------------------------------

      } else {
        // AS DUAS SENHAS DIGITADAS PRECISAM SER IGUAIS
        if ($admin_conf_senha != 1) {
          $_SESSION["erro"] = "As senhas digitadas estão diferentes!";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
          return die;
          // -------------------------------

        } else {

          // EDITA SENHA 
          $stmt = $conn->prepare('UPDATE admin SET admin_senha = :admin_senha, admin_data_reset_senha = GETDATE() WHERE admin_id = :admin_id');
          $stmt->execute([
            ':admin_id' => $admin_id,
            ':admin_senha' => $admin_senha
          ]);

          // REGISTRA AÇÃO NO LOG
          $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                                  VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
          $stmt->execute([
            ':modulo'     => 'AUTENTICAÇÃO',
            ':acao'       => 'ALTERA A SENHA PELO PERFIL',
            ':acao_id'    => $admin_id,
            ':user_id'    => $admin_id
          ]);
          // -------------------------------

          //ENVIA MENSAGEM
          $_SESSION["msg"] = "Senha atualizada com sucesso!";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        }
      }
    }

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

  } catch (PDOException $e) {
    // echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Erro ao tentar atualizar a senha!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
}









/*****************************************************************************************
                        ADMINISTRADOR PEDE PARA RECUPERAR A SENHA
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "reset") {

  $admin_email = trim($_POST['email']);

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO

    // BUSCA OS DADOS DO USUÁRIO CONFORME E-MAIL INFORMADO
    $query = "SELECT * FROM admin WHERE admin_email = :admin_email AND admin_status = 1";
    $result = $conn->prepare($query);
    $result->execute([':admin_email' => $admin_email]);
    if (($result) and ($result->rowCount() != 0)) {
      $row = $result->fetch(PDO::FETCH_ASSOC);
      extract($row);

      // CADASTRA O TOKEN PARA VALIDAÇÃO DA CONTA
      $codigo      = str_pad(mt_rand(0, 9999999), 7, '0', STR_PAD_LEFT); // GERA UM CÓDIGO DE 7 DÍGITOS
      $codigo_hash = password_hash($codigo, PASSWORD_DEFAULT); // CRIPTOGRAFA O CÓDIGO

      // VERIFICA SE JÁ EXISTE UM TOKEN PARA O ID DO ADMINISTRADOR
      $sql = "SELECT COUNT(*) FROM token WHERE tok_user_id = :tok_user_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':tok_user_id' => $admin_id]);
      if ($stmt->fetchColumn() > 0) {

        // SE HOUVER, ATUALIZA O TOKEN E A DATA DE VALIDADE
        $sql = "UPDATE token SET tok_codigo = :tok_codigo, tok_data_valida = DATEADD(day, 3, GETDATE()) WHERE tok_user_id = :tok_user_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
          ':tok_user_id' => $admin_id,
          ':tok_codigo' => $codigo_hash
        ]);
      } else {

        // CRIA UM TOKEN PARA O ID DO ADMINISTRADOR, CASO NÃO EXISTA
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
          ':tok_codigo'  => $codigo_hash,
          ':tok_user_id' => $admin_id
        ]);
      }
      // -------------------------------

      // ENVIAR O TOKEN PARA O E-MAIL DO USUÁRIO
      $admin_id_cript = base64_encode($admin_id); // CRIPTOGRAFA ID

      $mail = new PHPMailer(true);
      include '../../conexao/email.php';

      // E-MAIL QUE VAI RECEBER A MENSAGEM
      $mail->addAddress($admin_email, 'RESERVM');

      // CONTEÚDO
      $mail->isHTML(true);
      $mail->Subject = 'Seu código de acesso chegou';
      include '../../includes/email/email_header.php';
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

        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 20px 0px;'>
        Lembrando que essa etapa é muito importante para mantermos a segurança dos seus dados e cumprirmos nosso compromisso com você.
        </p>

        <p style='font-size: 1.3rem; font-weight: 600; margin: 0px 0px 15px 0px;'>
        Seu código de acesso é:
        </p>

        <span style='color: #285FAB; font-size: 2rem; letter-spacing: 0.20rem; font-weight: 600; margin-bottom: 20px; display: inline-block;'>$codigo</span><br>
        </td>
      </tr>";
      include '../../includes/email/email_footer.php';

      $mail->Body  = $email_conteudo;
      $mail->send();
      // -------------------------------   

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'     => 'AUTENTICAÇÃO',
        ':acao'       => 'ESQUECI MINHA SENHA',
        ':acao_id'    => $admin_id,
        ':dados'      => 'E-mail: ' . $admin_email,
        ':user_id'    => $admin_id
      ]);
      // -------------------------------

      //$_SESSION["link"] = 1;
      header("Location: ../ad-validcod.php?ad-ident=$admin_id_cript");
    } else {
      $_SESSION["erro"] = "E-mail não encontrados!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      return die;
    }
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Erro ao tentar executar a ação!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
}










/*****************************************************************************************
                      ALTERAR A SENHA DO ADMINISTRADOR
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "alterSenha") {

  $admin_id    = base64_decode($_POST['cod']); // DECOFICIA O ID DO ADMINISTRADOR
  $admin_senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); //CODIFICA A NOVA SENHA DIGITADA

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO

    // BUSCA OS DADOS DO ADMINISTRADOR CONFORME ID INFORMADO
    $query = "SELECT * FROM admin WHERE admin_id = :admin_id";
    $result = $conn->prepare($query);
    $result->execute([':admin_id' => $admin_id]);
    if (($result) and ($result->rowCount() != 0)) {
      $row = $result->fetch(PDO::FETCH_ASSOC);
      $admin_nome  = $row['admin_nome'];
    } else {
      $_SESSION["erro"] = "Erro ao tentar alterar a senha!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      return die;
    }
    // -------------------------------

    // EDITA SENHA 
    $sql = "UPDATE
                    admin
              SET
                    admin_senha            = :admin_senha,
                    admin_data_reset_senha = GETDATE()
              WHERE
                    admin_id = :admin_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':admin_id' => $admin_id,
      ':admin_senha' => $admin_senha
    ]);

    // APÓS ATUALIZAR A SENHA, O TOKEN REFERENTE AO ADMINISTRADOR É EXCLUÍDO
    $sql = "DELETE FROM token WHERE tok_user_id = '$admin_id'";
    $conn->exec($sql);
    // -------------------------------

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'AUTENTICAÇÃO',
      ':acao'       => 'CRIA NOVA SENHA',
      ':acao_id'    => $admin_id,
      ':user_id'    => $admin_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    //ENVIA MENSAGEM
    $_SESSION["msg"] = "Senha alterada com sucesso!";
    header("Location: $url_sistema/admin");
  } catch (PDOException $e) {
    // echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Erro ao tentar executar a ação!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
}
