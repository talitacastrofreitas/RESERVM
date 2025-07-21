<?php
session_start();
include '../conexao/conexao.php';

// NECESSÁRIO PARA ENVIAR O EMAIL
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                              CADASTRAR ORGANIZADORES
 *****************************************************************************************/
if (isset($dados['CadOrganizador'])) {

  $organ_id       = md5(uniqid(rand(), true)); // GERA UM ID UNICO
  $organ_prop_id  = $_POST['organ_prop_id'];

  // GERA UM CÓDIGO
  $organ_codigo   = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT); // GERA UM CÓDIGO COM 5 DÍGITOS 
  // -------------------------------
  $organ_nome     = trim($_POST['organ_nome']);
  //
  $cpf_pontos     = trim($_POST['organ_cpf']);
  $cpf_pontos     = str_replace('.', '', $cpf_pontos); // RETIRA PONTOS
  $organ_cpf      = str_replace('-', '', $cpf_pontos); // RETIRA TRAÇOS
  //
  $organ_email    = trim($_POST['organ_email']);
  $organ_contato  = trim($_POST['organ_contato']);
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM organizadores WHERE organ_nome = :organ_nome AND organ_prop_id = :organ_prop_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":organ_nome", $organ_nome);
  $stmt->bindParam(":organ_prop_id", $organ_prop_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este nome já foi cadastrado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM organizadores WHERE organ_codigo = :organ_codigo AND organ_prop_id = :organ_prop_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":organ_codigo", $organ_codigo);
  $stmt->bindParam(":organ_prop_id", $organ_prop_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este código já foi cadastrado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  if ($organ_cpf != '') {
    $sql = "SELECT COUNT(*) FROM organizadores WHERE organ_cpf = :organ_cpf AND organ_prop_id = :organ_prop_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":organ_cpf", $organ_cpf);
    $stmt->bindParam(":organ_prop_id", $organ_prop_id);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
      $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este cpf já foi cadastrado!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      return die;
    }
  }
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  if ($organ_email != '') {
    $sql = "SELECT COUNT(*) FROM organizadores WHERE organ_email = :organ_email AND organ_prop_id = :organ_prop_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":organ_email", $organ_email);
    $stmt->bindParam(":organ_prop_id", $organ_prop_id);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
      $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este e-mail já foi cadastrado!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      return die;
    }
  }
  // -------------------------------

  try {
    $sql = "INSERT INTO organizadores ( 
                                        organ_id,
                                        organ_prop_id,
                                        organ_codigo,
                                        organ_nome,
                                        organ_cpf,
                                        organ_email,
                                        organ_contato,
                                        organ_user_id,
                                        organ_data_cad,
                                        organ_data_upd
                                      ) VALUES (
                                        :organ_id,
                                        :organ_prop_id,
                                        :organ_codigo,
                                        UPPER(:organ_nome),
                                        :organ_cpf,
                                        :organ_email,
                                        :organ_contato,
                                        :organ_user_id,
                                        :organ_data_cad,
                                        :organ_data_upd
                                      )";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":organ_id", $organ_id);
    $stmt->bindParam(":organ_prop_id", $organ_prop_id);
    //
    $stmt->bindParam(":organ_codigo", $organ_codigo);
    $stmt->bindParam(":organ_nome", $organ_nome);
    $stmt->bindParam(":organ_cpf", $organ_cpf);
    $stmt->bindParam(":organ_email", $organ_email);
    $stmt->bindParam(":organ_contato", $organ_contato);
    //
    $stmt->bindParam(":organ_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":organ_data_cad", date('Y-m-d H:i:s'));
    $stmt->bindParam(":organ_data_upd", date('Y-m-d H:i:s'));
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'ORGANIZADORES',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $organ_id,
      ':dados'      => 'ID Proposta: ' . $organ_prop_id . '; Código: ' . $organ_codigo . '; Nome: ' . $organ_nome . '; CPF: ' . $organ_cpf . '; Contato: ' . $organ_contato . '; E-amil: ' . $organ_email,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => date('Y-m-d H:i:s')
    ));
    // -------------------------------

    $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Cadastro realizado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Cadastro não realizado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}
















/*****************************************************************************************
                                  EDITAR ORGANIZADORES
 *****************************************************************************************/
if (isset($dados['EditOrganizador'])) {

  $organ_id       = $_POST['organ_id'];
  $organ_prop_id  = $_POST['organ_prop_id'];
  $organ_codigo   = $_POST['organ_codigo'];
  //
  $organ_nome     = trim($_POST['organ_nome']);
  //
  $cpf_pontos     = trim($_POST['organ_cpf']);
  $cpf_pontos     = str_replace('.', '', $cpf_pontos); // RETIRA PONTOS
  $organ_cpf      = str_replace('-', '', $cpf_pontos); // RETIRA TRAÇOS
  //
  $organ_email    = trim($_POST['organ_email']);
  $organ_contato  = trim($_POST['organ_contato']);
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM organizadores WHERE organ_nome = :organ_nome AND organ_prop_id = :organ_prop_id AND organ_id != :organ_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":organ_id", $organ_id);
  $stmt->bindParam(":organ_nome", $organ_nome);
  $stmt->bindParam(":organ_prop_id", $organ_prop_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este nome já foi cadastrado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  if ($organ_cpf != '') {
    $sql = "SELECT COUNT(*) FROM organizadores WHERE organ_cpf = :organ_cpf AND organ_prop_id = :organ_prop_id AND organ_id != :organ_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":organ_id", $organ_id);
    $stmt->bindParam(":organ_cpf", $organ_cpf);
    $stmt->bindParam(":organ_prop_id", $organ_prop_id);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
      $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este cpf já foi cadastrado!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      return die;
    }
  }
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  if ($organ_email != '') {
    $sql = "SELECT COUNT(*) FROM organizadores WHERE organ_email = :organ_email AND organ_prop_id = :organ_prop_id AND organ_id != :organ_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":organ_id", $organ_id);
    $stmt->bindParam(":organ_email", $organ_email);
    $stmt->bindParam(":organ_prop_id", $organ_prop_id);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
      $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este e-mail já foi cadastrado!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      return die;
    }
  }
  // -------------------------------

  try {
    $sql = "UPDATE
                    organizadores
              SET
                    organ_nome     = UPPER(:organ_nome),
                    organ_cpf      = :organ_cpf,
                    organ_email    = :organ_email,
                    organ_contato  = :organ_contato,
                    organ_user_id  = :organ_user_id,
                    organ_data_upd = :organ_data_upd
              WHERE
                    organ_id = :organ_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":organ_id", $organ_id);
    //
    $stmt->bindParam(":organ_nome", $organ_nome);
    $stmt->bindParam(":organ_cpf", $organ_cpf);
    $stmt->bindParam(":organ_email", $organ_email);
    $stmt->bindParam(":organ_contato", $organ_contato);
    //
    $stmt->bindParam(":organ_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":organ_data_upd", date('Y-m-d H:i:s'));
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'ORGANIZADORES',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $organ_id,
      ':dados'      => 'ID Proposta: ' . $organ_prop_id . '; Código: ' . $organ_codigo . '; Nome: ' . $organ_nome . '; CPF: ' . $organ_cpf . '; Contato: ' . $organ_contato . '; E-amil: ' . $organ_email,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => date('Y-m-d H:i:s')
    ));
    // -------------------------------

    $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados atualizados!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados não atualizados!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}





















/*****************************************************************************************
                              EXCLUIR ORGANIZADORES
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_organ") {

  $organ_id = $_GET['organ_id'];

  try {
    $sql = "DELETE FROM organizadores WHERE organ_id = :organ_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':organ_id', $organ_id);
    $stmt->execute();

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {
      $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i>Dados excluídos!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_acao_user_nome, log_data )
                              VALUES ( :modulo, :acao, :acao_id, :user_id, :user_nome, :data )');
      $stmt->execute(array(
        ':modulo'    => 'ORGANIZADORES',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $organ_id,
        ':user_id'   => $_SESSION['reservm_admin_id'],
        ':user_nome' => $_SESSION['reservm_admin_nome'],
        ':data'      => date('Y-m-d H:i:s')
      ));
      // -------------------------------

    } else {
      $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados não excluídos!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    }
  } catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
  }
  $conn = null;
}




















/*****************************************************************************************
                        EXCLUIR ORGANIZADORES SELECIONADOS
 *****************************************************************************************/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($dados['ExcListOrganizadores'])) {

  if (isset($_POST['selecionados']) && is_array($_POST['selecionados'])) {
    $organ_id = $_POST['selecionados'];
    // $organ_id = array_map('intval', $organ_id); // Certifique-se de que os IDs sejam inteiros
    $organ_id_str = implode("','", $organ_id); // Crie uma lista de IDs separada por vírgulas

    $sql = "DELETE FROM organizadores WHERE organ_id IN ('$organ_id_str')";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    foreach ($organ_id as $organ_ident) {
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_acao_user_nome, log_data )
    VALUES ( :modulo, :acao, :acao_id, :user_id, :user_nome, :data )');
      $stmt->execute(array(
        ':modulo'    => 'ORGANIZADORES',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $organ_ident,
        ':user_id'   => $_SESSION['reservm_admin_id'],
        ':user_nome' => $_SESSION['reservm_admin_nome'],
        ':data'      => date('Y-m-d H:i:s')
      ));
    }
    // -------------------------------

    $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i>Dados excluídos!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } else {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Nenhum dado selecionado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}





















/*****************************************************************************************
                                E-MAIL INDIVIDUAL
 *****************************************************************************************/

if (isset($_GET['funcao']) && $_GET['funcao'] == "send_mail") {

  try {
    $organ_id = $_GET['organ_id'];
    $sql = "SELECT * FROM organizadores
            INNER JOIN propostas ON propostas.prop_id = organizadores.organ_prop_id
            INNER JOIN certificado ON certificado.cert_prop_id = organizadores.organ_prop_id
            WHERE organ_id = '$organ_id'";
    $stmt = $conn->query($sql);
    $part = $stmt->fetch(PDO::FETCH_ASSOC);
    $organ_id      = $part['organ_id'];
    $organ_prop_id = $part['organ_prop_id'];
    $organ_codigo  = $part['organ_codigo'];
    $organ_nome    = $part['organ_nome'];
    $organ_cpf     = $part['organ_cpf'];
    $organ_email   = $part['organ_email'];
    $organ_contato = $part['organ_contato'];
    // PROPOSTA
    $prop_titulo  = $part['prop_titulo'];

    // Configure o e-mail
    $mail = new PHPMailer(true);
    include  '../controller/email_conf.php';

    $mail->addAddress($organ_email, $organ_nome);
    $mail->isHTML(true);
    $mail->Subject = 'Certificado Bahiana - ' . $organ_codigo;

    //RECUPERA URL PARA O LINK DO EMAIL
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
    $url = $_SERVER['REQUEST_URI'];
    $path = parse_url($url, PHP_URL_PATH);
    $directories = explode('/', $path);
    array_shift($directories);
    $pagina = $protocol . $_SERVER['HTTP_HOST'] . '/' . $directories[0] . '/certificados/organizadores.php?organ_id=' . $organ_id;

    $mail->Body = "
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
            <tr style='background: #fff; text-align: center; color: #515050; padding:30px 50px; line-height: 25px;'>
              <td style='padding: 2rem 2rem; display: inline-block;'>

              <p style='width: 100%; font-size: 1.188rem; text-transform: uppercase; font-weight: 600; margin-bottom: 20px; display: inline-block;'>
              Olá, $organ_nome
              </p>

              <p style='font-size: 1rem;'>
              Seu certificado de participação no evento <strong>$prop_titulo</strong> já está disponível!
              </p>

              <p style='font-size: 1rem;'>
              Acesse o link do certificado clicando no botão abaixo.
              </p>

              <a style='cursor: pointer;' href='$pagina'><button style='background: #62B790; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 13px 20px; margin-top: 20px;' target='_blank'>BAIXAR CERTIFICADO</button></a>
            
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

    // Envie o e-mail
    if ($mail->Send()) {

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
      $stmt->execute(array(
        ':modulo'    => 'ORGANIZADORES',
        ':acao'      => 'CERTIFICADO INDIVIDUAL ENVIADO',
        ':acao_id'   => $organ_id,
        ':dados'      => 'ID Proposta: ' . $organ_prop_id . '; Código: ' . $organ_codigo . '; Nome: ' . $organ_nome . '; CPF: ' . $organ_cpf . '; Contato: ' . $organ_contato,
        ':user_id'   => $_SESSION['reservm_admin_id'],
        ':user_nome' => $_SESSION['reservm_admin_nome'],
        ':data'      => date('Y-m-d H:i:s')
      ));
      // -------------------------------

      // echo "E-mail enviado para: $organ_email<br>";
      $_SESSION["msg"] = "O certificado de $organ_nome foi enviado com sucesso!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    } else {
      //echo "Erro ao enviar e-mail para: $organ_email - " . $mail->ErrorInfo . "<br>";
      $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro ao enviar e-mail para: $organ_email - "  . $mail->ErrorInfo . "<br>";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    }

    $conn = null;
  } catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
  }
}























/*****************************************************************************************
                                        E-MAIL
 *****************************************************************************************/

if (isset($_GET['funcao']) && $_GET['funcao'] == "email_organ") {

  try {
    $prop_id = $_GET['prop_id'];
    $sql = "SELECT * FROM organizadores
            INNER JOIN propostas ON propostas.prop_id = organizadores.organ_prop_id
            INNER JOIN certificado ON certificado.cert_prop_id = organizadores.organ_prop_id
            WHERE cert_categoria = 2 AND organ_prop_id = '$prop_id'";
    $stmt = $conn->query($sql);
    $organizadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($organizadores as $part) {
      $organ_id      = $part['organ_id'];
      $organ_prop_id = $part['organ_prop_id'];
      $organ_codigo  = $part['organ_codigo'];
      $organ_nome    = $part['organ_nome'];
      $organ_email   = $part['organ_email'];
      $organ_cpf     = $part['organ_cpf'];
      $organ_contato = $part['organ_contato'];

      // Verifique a conformidade com regulamentações de privacidade de dados e obtenha consentimento do cliente, se necessário.

      // Configure o e-mail
      $mail = new PHPMailer(true);
      include  '../controller/email_conf.php';

      $mail->addAddress($organ_email, $organ_nome);
      $mail->isHTML(true);
      $mail->Subject = 'Certificado Bahiana - ' . $organ_codigo;

      //RECUPERA URL PARA O LINK DO EMAIL
      $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
      $url = $_SERVER['REQUEST_URI'];
      $path = parse_url($url, PHP_URL_PATH);
      $directories = explode('/', $path);
      array_shift($directories);
      $pagina = $protocol . $_SERVER['HTTP_HOST'] . '/' . $directories[0] . '/certificados/organizadores.php?organ_id=' . $organ_id;

      $mail->Body = "
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
            <tr style='background: #fff; text-align: center; color: #515050; padding:30px 50px; line-height: 25px;'>
              <td style='padding: 2rem 2rem; display: inline-block;'>

              <p style='width: 100%; font-size: 1.188rem; text-transform: uppercase; font-weight: 600; margin-bottom: 20px; display: inline-block;'>
              Olá, {$part['organ_nome']}
              </p>

              <p style='font-size: 1rem;'>
              Seu certificado de participação no evento <strong>{$part['prop_titulo']}</strong> já está disponível!
              </p>

              <p style='font-size: 1rem;'>
              Acesse o link do certificado clicando no botão abaixo.
              </p>

              <a style='cursor: pointer;' href='$pagina'><button style='background: #62B790; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 13px 20px; margin-top: 20px;' target='_blank'>BAIXAR CERTIFICADO</button></a>
            
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

      // Olá $organ_nome,\n\nAqui estão seus dados:\n\nNome: $organ_nome\nE-mail: $organ_email\nCPF: {$part['organ_cpf']}\nTelefone: {$part['organ_contato']}

      // Anexar o PDF gerado
      // require '../certificados/participantes.php?organ_id=' . $organ_id;
      // $mail->addStringAttachment($pagina, 'exemplo.pdf');

      // Envie o e-mail
      if ($mail->Send()) {

        // REGISTRA AÇÃO NO LOG
        $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
      VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
        $stmt->execute(array(
          ':modulo'    => 'ORGANIZADORES',
          ':acao'      => 'CERTIFICADO ENVIADO',
          ':acao_id'   => $organ_id,
          ':dados'      => 'ID Proposta: ' . $organ_prop_id . '; Código: ' . $organ_codigo . '; Nome: ' . $organ_nome . '; CPF: ' . $organ_cpf . '; Contato: ' . $organ_contato,
          ':user_id'   => $_SESSION['reservm_admin_id'],
          ':user_nome' => $_SESSION['reservm_admin_nome'],
          ':data'      => date('Y-m-d H:i:s')
        ));
        // -------------------------------

        // echo "E-mail enviado para: $organ_email<br>";
        $_SESSION["msg"] = "Certificados enviados com sucesso!";
        header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      } else {
        //echo "Erro ao enviar e-mail para: $organ_email - " . $mail->ErrorInfo . "<br>";
        $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro ao enviar e-mail para: $organ_email - "  . $mail->ErrorInfo . "<br>";
        header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      }
    }

    $conn = null;
  } catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
  }
}
