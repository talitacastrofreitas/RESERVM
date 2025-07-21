<?php
session_start();
include '../../conexao/conexao.php';

// NECESSÁRIO PARA ENVIAR O EMAIL
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                                CADASTRAR ADMINISTRADOR
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "cad_admin") {

  $admin_id             = md5(uniqid(rand(), true)); // GERA UM ID ÚNICO  
  // MATRICULA - NOME
  $admin_matricula_nome = trim($_POST['admin_matricula_nome']) !== '' ? trim($_POST['admin_matricula_nome']) : NULL;
  $parts = explode(' - ', $admin_matricula_nome, 2);
  $admin_matricula      = trim($parts[0]) !== '' ? trim($parts[0]) : NULL;
  $admin_nome           = trim($parts[1]) !== '' ? trim($parts[1]) : NULL;
  // -------------------------------
  $admin_email          = trim($_POST['admin_email']) !== '' ? trim($_POST['admin_email']) : NULL;
  $admin_perfil         = trim($_POST['admin_perfil']) !== '' ? trim($_POST['admin_perfil']) : NULL;
  $admin_status         = trim(isset($_POST['admin_status'])) ? $_POST['admin_status'] : 0;
  //$admin_status         = 1; // 1 = STATUS DO ADMINISTRADOR QUANDO ESTÁ ATIVO
  $nivel_acesso         = 1; // 1 = NÍVEL DE ACESSO DO ADMINISTRADOR
  $reservm_admin_id        = $_SESSION['reservm_admin_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM admin WHERE (admin_matricula = :admin_matricula OR admin_email = :admin_email) AND admin_status = :admin_status";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':admin_matricula' => $admin_matricula,
    ':admin_email'    => $admin_email,
    ':admin_status'   => $admin_status
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Falha ao cadastrar os dados: Matrícula ou e-mail já existe!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
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

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'    => 'ADMINISTRADOR',
      ':acao'      => 'CADASTRO',
      ':acao_id'   => $admin_id,
      ':dados'     =>
      'Matrícula: ' . $admin_matricula .
        '; Nome: ' . $admin_nome .
        '; E-mail: ' . $admin_email .
        '; Perfil: ' . $admin_perfil .
        '; Status: ' . $admin_status .
        '; Nível: ' . $nivel_acesso,
      ':user_id'   => $reservm_admin_id
    ]);

    // CRIA O TOKEN DE VALIDAÇÃO
    //$data      = date('Y-m-d H:i:s', strtotime('+1 day')); // DATA ATUAL + 1 DIA
    $codigo      = str_pad(mt_rand(0, 9999999), 7, '0', STR_PAD_LEFT); // GERA UM CÓDIGO DE 7 DÍGITOS
    $codigo_hash = password_hash($codigo, PASSWORD_DEFAULT); // CRIPTOGRAFA O CÓDIGO

    // CRIA UM TOKEN PARA O ID DO ADMINISTRADOR, CASO NÃO EXISTA
    $stmt = $conn->prepare('INSERT INTO token (
                                                tok_codigo,
                                                tok_user_id,
                                                tok_data_valida
                                              ) VALUES (
                                                :tok_codigo,
                                                :tok_user_id,
                                                DATEADD(day, 3, GETDATE()) -- ADICIONA 3 DIA A DATA ATUAL
                                              )');
    $stmt->execute([
      ':tok_codigo'  => $codigo_hash,
      ':tok_user_id' => $admin_id
    ]);
    // -------------------------------

    // DISPARA E-MAIL
    $admin_id_cript = base64_encode($admin_id); // CRIPTOGRAFA ID

    $mail = new PHPMailer(true);
    include '../../conexao/email.php';
    $mail->addAddress($admin_email); // E-MAIL DO ADMINISTRADOR
    $mail->isHTML(true);
    $mail->Subject = 'Seu código de acesso chegou'; //TÍTULO DO E-MAIL

    // CORPO DO EMAIL
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
        Ao copiá-lo vá à página de acesso e insira o código abaixo para confirmar sua identidade.
        </p>

        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 20px 0px;'>
        Lembrando que essa etapa é muito importante para mantermos a segurança dos seus dados e cumprirmos nosso compromisso com você.
        </p>

        <p style='font-size: 1.3rem; font-weight: 600; margin: 0px 0px 15px 0px;'>
        Seu código de acesso é:
        </p>

        <span style='color: #285FAB; font-size: 2rem; letter-spacing: 0.20rem; font-weight: 600; margin-bottom: 20px; display: inline-block;'>$codigo</span><br>
        <a style='cursor: pointer;' href='$url_sistema/admin/ad-validcod.php?ad-ident=$admin_id_cript'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 20px;' target='_blank'>Acesse o sistema e valide seu código.</button></a>
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
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
}












/*****************************************************************************************
                                EDITAR ADMINISTRADOR
 *****************************************************************************************/
if (isset($dados['EditAdmin'])) {

  $admin_id      = base64_decode($_POST['admin_id']);
  $admin_perfil  = trim($_POST['admin_perfil']) !== '' ? trim($_POST['admin_perfil']) : NULL;
  $admin_status  = trim(isset($_POST['admin_status'])) ? $_POST['admin_status'] : 0;
  $reservm_admin_id = $_SESSION['reservm_admin_id'];
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "UPDATE
                    admin
              SET        
                    admin_perfil    = :admin_perfil,
                    admin_status    = :admin_status,
                    admin_user_id   = :admin_user_id,
                    admin_data_upd  = GETDATE()
              WHERE
                    admin_id = :admin_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':admin_id'      => $admin_id,
      ':admin_perfil'  => $admin_perfil,
      ':admin_status'  => $admin_status,
      ':admin_user_id' => $reservm_admin_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'    => 'ADMINISTRADOR',
      ':acao'      => 'ATUALIZAÇÃO',
      ':acao_id'   => $admin_id,
      ':dados'     => 'Perfil: ' . $admin_perfil . '; Status: ' . $admin_status,
      ':user_id'   => $reservm_admin_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Dados atualizados com sucesso!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Dados não atualizados!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
}












/*****************************************************************************************
                        EXCLUIR ADMINISTRADOR
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_admin") {

  $admin_id      = base64_decode($_GET['admin_id']);
  $reservm_admin_id = $_SESSION['reservm_admin_id'];

  // SE O ADMINISTRADOR JÁ ACESSOU O SISTEMA, NÃO PODERÁ SER EXCLUÍDO
  $sql = "SELECT COUNT(*) FROM admin WHERE admin_id = :admin_id AND admin_data_acesso IS NOT NULL";
  $stmt = $conn->prepare($sql);
  $stmt->execute([':admin_id' => $admin_id]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este usuário não pode ser excluído!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "DELETE FROM admin WHERE admin_id = :admin_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':admin_id' => $admin_id]);

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {

      // EXCLUI O TOKEN
      $sql = "DELETE FROM token WHERE tok_user_id = :tok_user_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':tok_user_id' => $admin_id]);
      // -------------------------------

      // REGISTRA AÇÃO NO LOG
      $stmt_log = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                                  VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt_log->execute([
        ':modulo'  => 'ADMINISTRADOR',
        ':acao'    => 'EXCLUSÃO',
        ':acao_id' => $admin_id,
        ':user_id' => $reservm_admin_id
      ]);
      // -------------------------------

      $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

      $_SESSION["msg"] = "Dados excluídos com sucesso!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    } else {
      $conn->rollBack();
      $_SESSION["erro"] = "Dados não excluídos!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      return die;
    }
  } catch (PDOException $e) {
    //echo "Erro: " . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Dados não excluídos!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
}















/*****************************************************************************************
                        EXCLUIR ACESSO A CONTA DO ADMIN PELO PERFIL
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_admin_conta") {

  $id_cript               = $_GET['cod']; // ID DO USUÁRIO CRIPTOGRAFADO
  $admin_id               = base64_decode($id_cript); // DECODIFICA O ID DO USUÁRIO
  //
  $admin_status           = 2; // STATUS = 2 SIGNIFICA QUE O ADMINISTRADOR FOI EXCLUÍDO
  $admin_senha            = NULL;
  $admin_data_reset_senha = NULL;
  $admin_data_acesso      = NULL;
  $nivel_acesso           = 2; // 2 = NÍVEL DE ACESSO QUANDO A CONTA FOR EXCLUÍDA
  $admin_user_id          = $_SESSION['reservm_admin_id'];

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO

    $sql = "UPDATE
                    admin
                SET   
                    admin_status           = :admin_status,
                    admin_senha            = :admin_senha,
                    admin_data_reset_senha = :admin_data_reset_senha,
                    admin_data_acesso      = :admin_data_acesso,
                    nivel_acesso           = :nivel_acesso,
                    admin_user_id          = :admin_user_id,
                    admin_data_upd         = GETDATE()
              WHERE
                    admin_id = :admin_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':admin_id' => $admin_id,
      ':admin_status' => $admin_status,
      ':admin_senha' => $admin_senha,
      ':admin_data_reset_senha' => $admin_data_reset_senha,
      ':admin_data_acesso' => $admin_data_acesso,
      ':nivel_acesso' => $nivel_acesso,
      ':admin_user_id' => $admin_user_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'    => 'ADMINISTRADOR',
      ':acao'      => 'EXCLUSÃO CONTA PERFIL',
      ':acao_id'   => $admin_id,
      ':user_id'   => $admin_user_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    header("Location: ../sair.php");
  } catch (PDOException $e) {
    //echo $sql . "<br>" . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Erro ao tentar excluir esta conta!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
}
