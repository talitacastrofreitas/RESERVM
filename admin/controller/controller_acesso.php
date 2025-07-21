<?php
// session_start();
ob_start(); // Limpa o buff de saida
include '../conexao/conexao.php';

// NECESSÁRIO PARA ENVIAR O EMAIL
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require '../vendor/autoload.php';
// -------------------------------

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                            ACESSA O SISTEMA
 *****************************************************************************************/
// RECEBE OS DADOS DO LOGIN
if (isset($dados['login_admin'])) {

  // VALIDAÇÃO DA MATRÍCULA (DEVE CONTER APENAS NÚMEROS)
  // Evite possíveis SQL Injections, apesar de usar prepared statements, sempre sanitizando a entrada.
  if (!isset($dados['matricula']) || !ctype_digit($dados['matricula'])) {
    $_SESSION["erro"] = "Credenciais inválidas!";
    $url_retorno = $_SERVER['HTTP_REFERER'] ?? '/';
    header('Location: ' . filter_var($url_retorno, FILTER_SANITIZE_URL));
    exit();
  }

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO

    $admin_matricula = $dados['matricula'];

    // VERIFICA SE MATRÍCULA DO ADMINISTRADOR AINDA ESTÁ ATIVA NA BASE DE DADOS DA VISÃO    
    $sql = $conn->prepare("SELECT COUNT(*) FROM $view_colaboradores WHERE CHAPA = ?");
    $sql->execute([$admin_matricula]);
    if ($sql->fetchColumn()) {

      // VERIFICA SE A MATRÍCULA DO ADMINISTRADOR É VÁLIDA
      $query_admin = $conn->prepare("SELECT * FROM admin WHERE admin_matricula = :admin_matricula AND admin_status IN (1)");
      $query_admin->execute([':admin_matricula' => $admin_matricula]);

      if (($query_admin) and ($query_admin->rowCount() != 0)) {
        $row_admin = $query_admin->fetch(PDO::FETCH_ASSOC);

        if ($row_admin['nivel_acesso'] !== 2) { // SE O NÍVEL DE ACESSO DO ADMIN FOR '2', NÃO PODE ACESSAR. A CONTA FOI EXCLUÍDA

          if ($row_admin['admin_status'] == 1) { // SE O STATUS DO ADMINISTRADOR FOR 'INATIVO', NÃO PODE LOGAR

            // A SENHA DO ADMINISTRADOR EXPIRA EM 90 DIAS (3 MESES)
            $data_hoje  = new DateTime(date('Y-m-d H:i:s')); // RECUPERADA A DATA EM TEMPO REAL
            $data_senha = new DateTime($row_admin['admin_data_reset_senha']); // RECUPERA A DATA DA ULTIMA ATUALIZAÇÃO DA SENHA
            $intervalo = $data_hoje->diff($data_senha); // FAZ A DIFERENÇA ENTRE A DATA DE HOJE E A DATA DA ULTIMA ATUALIZAÇÃO DA SENHA
            $diferenca_data = $intervalo->days;

            if ($diferenca_data > 90 && $admin_matricula == $row_admin['admin_matricula'] && password_verify($dados['senha'], $row_admin['admin_senha']) == 1) {
              header("Location: ../admin/ad-newpass-ex.php?ad-ident=" . $row_admin['admin_id']);
              exit();
            } else {

              // SE LOGIN E SENHA ESTIVEREM CORRETOS, ENTRA NO SISTEMA
              if ($admin_matricula == $row_admin['admin_matricula'] && password_verify($dados['senha'], $row_admin['admin_senha'])) {
                $_SESSION['session_admin_logged_in'] = true;
                $_SESSION['reservm_admin_id']           = $row_admin['admin_id'];
                $_SESSION['reservm_admin_nome']         = $row_admin['admin_nome'];
                $_SESSION['reservm_admin_email']        = $row_admin['admin_email'];
                $_SESSION['reservm_admin_matricula']    = $row_admin['admin_matricula'];
                $_SESSION['reservm_admin_perfil']       = $row_admin['admin_perfil'];
                $_SESSION['reservm_admin_status']       = $row_admin['admin_status'];
                $_SESSION['reservm_admin_nivel_acesso'] = $row_admin['nivel_acesso'];
                header("Location: $url_sistema/admin/solicitacoes.php");

                // REGISTRA AÇÃO NO LOG
                $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data)
                                        VALUES (:modulo, :acao, :acao_id, :user_id, GETDATE())');
                $stmt->execute([
                  ':modulo'  => 'AUTENTICAÇÃO',
                  ':acao'    => 'ACESSO',
                  ':acao_id' => $_SESSION['reservm_admin_id'],
                  ':user_id' => $_SESSION['reservm_admin_id']
                ]);
                // -------------------------------

                // CADASTRA DA DATA QUE O ADMINISTRADOR LOGA NO SISTEMA
                $agora = date('Y-m-d H:i:s');
                $stmt = $conn->prepare('UPDATE admin SET admin_data_acesso = :data WHERE admin_id = :admin_id');
                $stmt->execute([':data' => $agora, ':admin_id' => $row_admin['admin_id']]);
              } else {
                $_SESSION["erro"] = "Matrícula ou senha incorretos";
                $url_retorno = $_SERVER['HTTP_REFERER'] ?? '/';
                header('Location: ' . filter_var($url_retorno, FILTER_SANITIZE_URL));
                exit();
              }
            }
          } else {
            $_SESSION["rest"] = "Acesso restrito!";
            $url_retorno = $_SERVER['HTTP_REFERER'] ?? '/';
            header('Location: ' . filter_var($url_retorno, FILTER_SANITIZE_URL));
            exit();
          }
        } else {
          $_SESSION["erro"] = "Matrícula ou senha incorretos";
          $url_retorno = $_SERVER['HTTP_REFERER'] ?? '/';
          header('Location: ' . filter_var($url_retorno, FILTER_SANITIZE_URL));
          exit();
        }
      } else {
        $_SESSION["erro"] = "Matrícula ou senha incorretos";
        $url_retorno = $_SERVER['HTTP_REFERER'] ?? '/';
        header('Location: ' . filter_var($url_retorno, FILTER_SANITIZE_URL));
        exit();
      }
    } else {
      $_SESSION["erro"] = "Acesso restrito!";
      $url_retorno = $_SERVER['HTTP_REFERER'] ?? '/';
      header('Location: ' . filter_var($url_retorno, FILTER_SANITIZE_URL));
      exit();
    }
    // -------------------------------

    $conn->commit(); // CONFIRMA A TRANSAÇÃO

  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Erro ao tentar executar a ação!";
    $url_retorno = $_SERVER['HTTP_REFERER'] ?? '/';
    header('Location: ' . filter_var($url_retorno, FILTER_SANITIZE_URL));
    exit();
  }
}





/*****************************************************************************************
                        ADMINISTRADOR PEDE PARA RECUPERAR A SENHA
 *****************************************************************************************/
if (isset($_GET['func']) && $_GET['func'] == "reset_pass") {

  if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $_SESSION["erro"] = "E-mail inválido!";
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }
  $admin_email = $_POST['email'];

  $conn->beginTransaction();

  try {

    // BUSCA OS DADOS DO USUÁRIO CONFORME E-MAIL INFORMADO
    $result = $conn->prepare("SELECT admin_id, admin_email FROM admin WHERE admin_email = ? AND admin_status = 1");
    $result->execute([$admin_email]);
    $row = $result->fetch(PDO::FETCH_ASSOC);

    if ($row) {
      // CADASTRA O TOKEN PARA VALIDAÇÃO DA CONTA
      $codigo      = str_pad(random_int(1000000, 9999999), 7, '0', STR_PAD_LEFT); // GERA UM CÓDIGO DE 7 DÍGITOS
      $codigo_hash = password_hash($codigo, PASSWORD_DEFAULT); // CRIPTOGRAFA O CÓDIGO

      // VERIFICA SE JÁ EXISTE UM TOKEN PARA O ID DO ADMINISTRADOR
      $sql = $conn->prepare("SELECT COUNT(*) FROM token WHERE tok_user_id = ?");
      $sql->execute([$row['admin_id']]);

      if ($sql->fetchColumn() > 0) {
        // SE HOUVER, ATUALIZA O TOKEN E A DATA DE VALIDADE
        $sql = $conn->prepare("UPDATE token SET tok_codigo = :tok_codigo, tok_data_valida = DATEADD(day, 1, GETDATE()) WHERE tok_user_id = :tok_user_id");
      } else {
        // CRIA UM TOKEN PARA O ID DO USUÁRIO, CASO NÃO EXISTA
        $sql = $conn->prepare('INSERT INTO token (tok_codigo, tok_user_id, tok_data_valida) VALUES (:tok_codigo, :tok_user_id, DATEADD(day, 1, GETDATE()))');
      }
      $sql->execute([':tok_codigo' => $codigo_hash, ':tok_user_id' => $row['admin_id']]);
      // -------------------------------

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data)
                              VALUES (:modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'  => 'AUTENTICAÇÃO',
        ':acao'    => 'ESQUECI MINHA SENHA',
        ':acao_id' => $row['admin_id'],
        ':dados'   => "E-mail: {$admin_email}",
        ':user_id' => $row['admin_id']
      ]);
      // -------------------------------

      $conn->commit(); // CONFIRMA A TRANSAÇÃO

      // ENVIAR O TOKEN PARA O E-MAIL DO USUÁRIO
      $admin_id = $row['admin_id']; // CRIPTOGRAFA ID

      $mail = new PHPMailer(true);
      include '../conexao/email.php';

      // E-MAIL QUE VAI RECEBER A MENSAGEM
      //$mail->addAddress($admin_email, 'RESERVM');
      $mail->addAddress($email_saap, 'RESERVM');

      // CONTEÚDO
      $mail->isHTML(true);
      $mail->Subject = 'Seu código de acesso chegou';
      include '../includes/email/email_header.php';
      $email_conteudo .= "
      <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
        <td style='padding: 2em 2rem; display: inline-block;'>

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
      include '../includes/email/email_footer.php';

      $mail->Body  = $email_conteudo;
      $mail->send();

      // -------------------------------   
      // header("Location: ../admin/ad-validcod.php?ad-ident=$admin_id_cript");

      session_start();
      $_SESSION['admin_id_cript_cod'] = $admin_id;
      header("Location: ../admin/ad-validcod.php");
      exit();
      // -------------------------------   

    } else {
      $_SESSION["erro"] = 'Usuário não encontrado!';
      header("Location: ../admin/ad-forgot-pass.php");
      exit;
    }
    // -------------------------------

  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = $e->getMessage();
    header("Location: ../admin/ad-forgot-pass.php");
    exit;
  }
  // -------------------------------
}





/*****************************************************************************************
                            VALIDA CÓDIGO PARA CRIAR SENHA
 *****************************************************************************************/
if (isset($dados['ValCod'])) {

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO

    $admin_id = $dados['cod']; // DECODIFICA O ID DO USUÁRIO - 'true' EVITA ERROS SILENCIOSOS

    // VERIFICA SE É UM NÚMERO (CHAR(32)) VÁLIDO
    if ($admin_id === false || $admin_id === "" || !preg_match('/^[a-f0-9]{32}$/i', $admin_id)) {
      $_SESSION["erro"] = "Código inválido!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      exit;
    }

    $query_tok = $conn->prepare("SELECT * FROM token
                                INNER JOIN admin ON admin.admin_id COLLATE SQL_Latin1_General_CP1_CI_AI = token.tok_user_id
                                WHERE tok_user_id = ?");
    $query_tok->execute([$admin_id]);
    $row_tok = $query_tok->fetch(PDO::FETCH_ASSOC);

    // VERIFICA SE O ID EXISTE NA TABELA
    $sql = $conn->prepare("SELECT COUNT(*) FROM token WHERE tok_user_id = :admin_id");
    $sql->execute([':admin_id' => $admin_id]);
    $val_token = $sql->fetchColumn();

    // SE TOKEN EXISTIR E O TOKEN FOR DO ID INFORMADO
    if ((!empty($row_tok)) && ($val_token != 0)) {

      // O TOKEN TEM VALIDADE DE 3 DIAS
      $dataReal    = new DateTime(); // DATA EM TEMPO REAL
      $data_valida = new DateTime($row_tok['tok_data_valida']); // DATA DE VALIDADE DO TOKEN

      // SE A DATA DE VALIDADE DO TOKEN FOR MAIOR QUE A DATA REAL, SEGUE A VALIDAÇÃO
      if ($data_valida >= $dataReal) {

        $tok_codigo = $dados['cod1'] . $dados['cod2'] . $dados['cod3'] . $dados['cod4'] . $dados['cod5'] . $dados['cod6'] . $dados['cod7'];

        // SE O CÓDIGO ESTIVER CORRETO, SEGUE PARA FORMULÁRIO DE CRIAÇÃO DE SENHA
        if (password_verify($tok_codigo, $row_tok['tok_codigo'])) {

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

          // DESTRÓI TODAS AS SESSÕES ATIVA
          unset($_SESSION['admin_id_cript_cod']);
          session_destroy();

          // header("Location: ../admin/ad-creatpass.php?ad-ident=" . $dados['cod']);
          session_start();
          $_SESSION['admin_id_cript_pass'] = $admin_id;
          header("Location: ../admin/ad-creatpass.php");
          exit;
        } else {
          $_SESSION["erro"] = "Código inválido!";
          header('Location: ' . filter_var($_SERVER['HTTP_REFERER'] ?? '/', FILTER_SANITIZE_URL));
          exit();
        }
      } else {
        $_SESSION["erro"] = "Este código expirou!";
        header('Location: ' . filter_var($_SERVER['HTTP_REFERER'] ?? '/', FILTER_SANITIZE_URL));
        exit();
      }
    } else {
      $_SESSION["erro"] = "Código inválido!";
      header('Location: ' . filter_var($_SERVER['HTTP_REFERER'] ?? '/', FILTER_SANITIZE_URL));
      exit();
    }
    // -------------------------------

    $conn->commit(); // CONFIRMA A TRANSAÇÃO

  } catch (PDOException $e) {
    // echo "Erro: " . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Erro ao tentar executar a ação!";
    header('Location: ' . filter_var($_SERVER['HTTP_REFERER'] ?? '/', FILTER_SANITIZE_URL));
    exit();
  }
}





/*****************************************************************************************
                      ALTERAR A SENHA DO ADMINISTRADOR
 *****************************************************************************************/
if (isset($_GET['func']) && $_GET['func'] == "upd_pass") {

  $admin_id    = $_POST['cod']; // DECOFICIA O ID DO ADMINISTRADOR
  $admin_senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); //CODIFICA A NOVA SENHA DIGITADA

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO

    // EDITA SENHA 
    $sql = $conn->prepare("UPDATE admin SET admin_senha = :admin_senha, admin_data_reset_senha = GETDATE() WHERE admin_id = :admin_id");
    $sql->execute([':admin_id' => $admin_id, ':admin_senha' => $admin_senha]);

    // APÓS ATUALIZAR A SENHA, O TOKEN REFERENTE AO ADMINISTRADOR É EXCLUÍDO
    $sql = $conn->prepare("DELETE FROM token WHERE tok_user_id = ?");
    $sql->execute([$admin_id]);
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

    $conn->commit(); // CONFIRMA A TRANSAÇÃO

    // REMOVE VARIÁVEL ESPECÍFICA
    unset($_SESSION['admin_id_cript_pass']);

    //ENVIA MENSAGEM
    $_SESSION["msg"] = "Senha alterada com sucesso!";
    header("Location: $url_sistema/admin");
    exit();

    // -------------------------------

  } catch (PDOException $e) {
    // echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Erro ao tentar executar a ação!";
    header('Location: ' . filter_var($_SERVER['HTTP_REFERER'] ?? '/', FILTER_SANITIZE_URL));
    exit();
  }
}





/*****************************************************************************************
              ALTERAR A SENHA DO ADMINISTRADOR SE A SENHA EXPIRAR
 *****************************************************************************************/
if (isset($_GET['func']) && $_GET['func'] == "upd_pass_ex") {

  $admin_id    = base64_decode($_POST['cod']); // DECODIFICA O ID DO ADMINISTRADOR
  $nova_senha  = $_POST['senha'];
  $conf_senha  = $_POST['conf_senha'];
  $senha_atual = $_POST['senha_atual'];

  // VERIFICA SE AS SENHAS DIGITADAS SÃO IGUAIS ANTES DE HASH
  if ($nova_senha !== $conf_senha) {
    $_SESSION["erro"] = "As senhas digitadas estão diferentes!";
    header('Location: ' . filter_var($_SERVER['HTTP_REFERER'] ?? '/', FILTER_SANITIZE_URL));
    exit();
  }

  // VERIFICA SE A SENHA ATUAL É DIFERENTE DA NOVA SENHA
  if ($nova_senha === $senha_atual) {
    $_SESSION["erro"] = "A nova senha precisa ser diferente da atual!";
    header('Location: ' . filter_var($_SERVER['HTTP_REFERER'] ?? '/', FILTER_SANITIZE_URL));
    exit();
  }

  // VERIFICA SE A SENHA ATENDE A REQUISITOS DE SEGURANÇA
  // if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/', $nova_senha)) {
  //   $_SESSION["erro"] = "A senha deve ter no mínimo 8 caracteres, incluindo letras, números e um caractere especial.";
  //   header('Location: ' . filter_var($_SERVER['HTTP_REFERER'] ?? '/', FILTER_SANITIZE_URL));
  //   exit();
  // }

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO

    // BUSCA A SENHA ATUAL NO BANCO
    $stmt = $conn->prepare("SELECT admin_senha FROM admin WHERE admin_id = ?");
    $stmt->execute([$admin_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
      $_SESSION["erro"] = "Usuário não encontrado!";
      header('Location: ' . filter_var($_SERVER['HTTP_REFERER'] ?? '/', FILTER_SANITIZE_URL));
      exit();
    }

    // VERIFICA A SENHA ATUAL
    if (!password_verify($senha_atual, $result['admin_senha'])) {
      $_SESSION["erro"] = "A senha atual está incorreta!";
      header('Location: ' . filter_var($_SERVER['HTTP_REFERER'] ?? '/', FILTER_SANITIZE_URL));
      exit();
    }

    // HASH DA NOVA SENHA
    $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

    // ATUALIZA A SENHA
    $stmt = $conn->prepare('UPDATE admin SET admin_senha = :admin_senha, admin_data_reset_senha = GETDATE() WHERE admin_id = :admin_id');
    $stmt->execute([':admin_id' => $admin_id, ':admin_senha' => $nova_senha_hash]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data)
                            VALUES (:modulo, :acao, :acao_id, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'  => 'AUTENTICAÇÃO',
      ':acao'    => 'ALTERA A SENHA EXPIRADA',
      ':acao_id' => $admin_id,
      ':user_id' => $admin_id
    ]);

    $conn->commit(); // CONFIRMA A TRANSAÇÃO

    $_SESSION["msg"] = "Senha atualizada com sucesso!";
    header("Location: $url_sistema/admin");
    exit();
  } catch (PDOException $e) {
    $conn->rollBack();
    $_SESSION["erro"] = "Erro ao tentar executar a ação. Tente novamente!";
    header('Location: ' . filter_var($_SERVER['HTTP_REFERER'] ?? '/', FILTER_SANITIZE_URL));
    exit();
  }
}




/*****************************************************************************************
                    ALTERAR A SENHA DO ADMINISTRADOR PELO PERFIL
 *****************************************************************************************/
if (isset($_GET['func']) && $_GET['func'] === "upd_pass_perf") {

  $admin_id    = $_POST['cod']; // ID do administrador
  $senha_atual = $_POST['senha_atual'];
  $nova_senha  = $_POST['senha'];
  $conf_senha  = $_POST['conf_senha'];

  try {
    // Inicia transação
    $conn->beginTransaction();

    // Verifica se nova senha é igual à senha atual (texto plano)
    if ($senha_atual === $nova_senha) {
      $_SESSION["erro"] = "A nova senha precisa ser diferente da atual!";
      header("Location: ../admin/perfil.php?tab=tabSenha");
      exit();
    }

    // Verifica se nova senha e confirmação coincidem
    if ($nova_senha !== $conf_senha) {
      $_SESSION["erro"] = "As senhas digitadas estão diferentes!";
      header("Location: ../admin/perfil.php?tab=tabSenha");
      exit();
    }

    // Busca hash da senha atual no banco
    $stmt = $conn->prepare("SELECT admin_senha FROM admin WHERE admin_id = ?");
    $stmt->execute([$admin_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result || !password_verify($senha_atual, $result['admin_senha'])) {
      $_SESSION["erro"] = "A senha atual está incorreta!";
      header("Location: ../admin/perfil.php?tab=tabSenha");
      exit();
    }

    // Gera novo hash da senha
    $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

    // Atualiza senha no banco
    $stmt = $conn->prepare("UPDATE admin SET admin_senha = :senha, admin_data_reset_senha = GETDATE() WHERE admin_id = :id");
    $stmt->execute([
      ':senha' => $nova_senha_hash,
      ':id'    => $admin_id
    ]);

    // Registra no log
    $stmt = $conn->prepare("INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data)
                            VALUES (:modulo, :acao, :acao_id, :user_id, GETDATE())");
    $stmt->execute([
      ':modulo'   => 'AUTENTICAÇÃO',
      ':acao'     => 'ALTERA A SENHA PELO PERFIL',
      ':acao_id'  => $admin_id,
      ':user_id'  => $admin_id
    ]);

    // Confirma a transação
    $conn->commit();

    $_SESSION["msg"] = "Senha atualizada com sucesso!";
    header("Location: ../admin/perfil.php?tab=tabDados");
    exit();
  } catch (PDOException $e) {
    $conn->rollBack();
    $_SESSION["erro"] = "Erro ao tentar atualizar a senha!";
    header("Location: ../admin/perfil.php?tab=tabSenha");
    exit();
  }
}
