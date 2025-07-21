<?php
session_start();
include '../conexao/conexao.php';

// NECESSÁRIO PARA ENVIAR O EMAIL
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                              CADASTRAR PARTICIPANTES
 *****************************************************************************************/
if (isset($dados['CadParticipante'])) {

  $part_id       = md5(uniqid(rand(), true)); // GERA UM ID UNICO
  $part_prop_id  = $_POST['part_prop_id'];

  // GERA UM CÓDIGO
  $part_codigo = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT); // GERA UM CÓDIGO COM 5 DÍGITOS 
  // -------------------------------
  $part_nome    = trim($_POST['part_nome']);
  //
  $cpf_pontos   = trim($_POST['part_cpf']);
  $cpf_pontos   = str_replace('.', '', $cpf_pontos); // RETIRA PONTOS
  $part_cpf     = str_replace('-', '', $cpf_pontos); // RETIRA TRAÇOS
  //
  $part_email   = trim($_POST['part_email']);
  $part_contato = trim($_POST['part_contato']);

  // echo '<pre>';
  // var_dump($dados);
  // echo '</pre>';
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM participantes WHERE part_nome = :part_nome AND part_prop_id = :part_prop_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":part_nome", $part_nome);
  $stmt->bindParam(":part_prop_id", $part_prop_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este nome já foi cadastrado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM participantes WHERE part_codigo = :part_codigo AND part_prop_id = :part_prop_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":part_codigo", $part_codigo);
  $stmt->bindParam(":part_prop_id", $part_prop_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este código já foi cadastrado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  // $sql = "SELECT COUNT(*) FROM participantes WHERE part_cpf = :part_cpf AND part_prop_id = :part_prop_id";
  // $stmt = $conn->prepare($sql);
  // $stmt->bindParam(":part_cpf", $part_cpf);
  // $stmt->bindParam(":part_prop_id", $part_prop_id);
  // $stmt->execute();
  // if ($stmt->fetchColumn() > 0) {
  //   $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este cpf já foi cadastrado!";
  //   header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  //   return die;
  // }
  // -------------------------------

  try {
    $sql = "INSERT INTO participantes ( 
                                        part_id,
                                        part_prop_id,
                                        part_codigo,
                                        part_nome,
                                        part_cpf,
                                        part_email,
                                        part_contato,
                                        part_user_id,
                                        part_data_cad,
                                        part_data_upd
                                      ) VALUES (
                                        :part_id,
                                        :part_prop_id,
                                        :part_codigo,
                                        UPPER(:part_nome),
                                        :part_cpf,
                                        :part_email,
                                        :part_contato,
                                        :part_user_id,
                                        :part_data_cad,
                                        :part_data_upd
                                      )";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":part_id", $part_id);
    $stmt->bindParam(":part_prop_id", $part_prop_id);
    //
    $stmt->bindParam(":part_codigo", $part_codigo);
    $stmt->bindParam(":part_nome", $part_nome);
    $stmt->bindParam(":part_cpf", $part_cpf);
    $stmt->bindParam(":part_email", $part_email);
    $stmt->bindParam(":part_contato", $part_contato);
    //
    $stmt->bindParam(":part_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":part_data_cad", date('Y-m-d H:i:s'));
    $stmt->bindParam(":part_data_upd", date('Y-m-d H:i:s'));
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'PARTICIPANTES',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $part_id,
      ':dados'      => 'ID Proposta: ' . $part_prop_id . '; Código: ' . $part_codigo . '; Nome: ' . $part_nome . '; CPF: ' . $part_cpf . '; Contato: ' . $part_contato,
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
                                  EDITAR PARTICIPANTES
 *****************************************************************************************/
if (isset($dados['EditParticipante'])) {

  $part_id       = $_POST['part_id'];
  $part_prop_id  = $_POST['part_prop_id'];
  //
  $part_nome     = trim($_POST['part_nome']);
  //
  $cpf_pontos    = trim($_POST['part_cpf']);
  $cpf_pontos    = str_replace('.', '', $cpf_pontos); // RETIRA PONTOS
  $part_cpf      = str_replace('-', '', $cpf_pontos); // RETIRA TRAÇOS
  //
  $part_email    = trim($_POST['part_email']);
  $part_contato  = trim($_POST['part_contato']);
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM participantes WHERE part_nome = :part_nome AND part_prop_id = :part_prop_id AND part_id != :part_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":part_id", $part_id);
  $stmt->bindParam(":part_nome", $part_nome);
  $stmt->bindParam(":part_prop_id", $part_prop_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este nome já foi cadastrado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  // $sql = "SELECT COUNT(*) FROM participantes WHERE part_cpf = :part_cpf AND part_prop_id = :part_prop_id AND part_id != :part_id";
  // $stmt = $conn->prepare($sql);
  // $stmt->bindParam(":part_id", $part_id);
  // $stmt->bindParam(":part_cpf", $part_cpf);
  // $stmt->bindParam(":part_prop_id", $part_prop_id);
  // $stmt->execute();
  // if ($stmt->fetchColumn() > 0) {
  //   $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este cpf já foi cadastrado!";
  //   header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  //   return die;
  // }
  // -------------------------------

  try {
    $sql = "UPDATE
                    participantes
              SET
                    part_nome     = UPPER(:part_nome),
                    part_cpf      = :part_cpf,
                    part_email    = :part_email,
                    part_contato  = :part_contato,
                    part_user_id  = :part_user_id,
                    part_data_upd = :part_data_upd
              WHERE
                    part_id = :part_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":part_id", $part_id);
    //
    $stmt->bindParam(":part_nome", $part_nome);
    $stmt->bindParam(":part_cpf", $part_cpf);
    $stmt->bindParam(":part_email", $part_email);
    $stmt->bindParam(":part_contato", $part_contato);
    //
    $stmt->bindParam(":part_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":part_data_upd", date('Y-m-d H:i:s'));
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'PARTICIPANTES',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $part_id,
      ':dados'      => 'Nome: ' . $part_nome . '; CPF: ' . $part_cpf . '; Contato: ' . $part_contato,
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
                              UPLOAD LISTA PARTICIPANTES
 *****************************************************************************************/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['arquivo']) && isset($_POST['part_prop_id'])) {

  $csv_file     = $_FILES['arquivo']['tmp_name'];
  $part_prop_id = $_POST['part_prop_id'];
  if (($handle = fopen($csv_file, 'r')) !== false) {

    // Pule a primeira linha do CSV
    fgetcsv($handle, 1000, ';');

    while (($data = fgetcsv($handle, 1000, ';')) !== false) {
      // Certifique-se de que a linha do CSV tem a estrutura correta
      if (count($data) >= 2 && !empty($data[0])) { // if (count($data) >= 2 && !empty($data[0]) && !empty($data[1])) {
        $part_id = md5(uniqid(rand(), true)); // GERA UM ID UNICO
        // GERA UM CÓDIGO
        $part_codigo = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT); // GERA UM CÓDIGO COM 5 DÍGITOS 
        // -------------------------------
        $part_nome     = trim($data[0]);
        $part_cpf      = trim(substr($data[1], 0, 11)); // LIMITA CARACTERES ATAÉ 11
        $part_email    = trim($data[2]);
        $part_contato  = trim(substr($data[3], 0, 15)); // LIMITA CARACTERES ATAÉ 15
        $part_user_id  = $_SESSION['reservm_admin_id'];
        $part_data_cad = date('Y-m-d H:i:s');
        $part_data_upd = date('Y-m-d H:i:s');

        // IMPEDE CADASTRO DUPLICADO
        $sql = "SELECT COUNT(*) FROM participantes WHERE part_prop_id = :part_prop_id AND part_codigo = :part_codigo";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":part_prop_id", $part_prop_id);
        $stmt->bindParam(":part_codigo", $part_codigo);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
          $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Falha ao tentar realizar o upload!";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
          return die;
        }
        // -------------------------------

        // IMPEDE CADASTRO DUPLICADO
        $sql = "SELECT COUNT(*) FROM participantes WHERE part_prop_id = :part_prop_id AND part_nome = :part_nome";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":part_prop_id", $part_prop_id);
        $stmt->bindParam(":part_nome", $part_nome);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
          $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Foi encontrado um NOME duplicado! O upload foi interrompido";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
          return die;
        }
        // -------------------------------

        // IMPEDE CADASTRO DUPLICADO
        // $sql = "SELECT COUNT(*) FROM participantes WHERE part_prop_id = :part_prop_id AND part_cpf = :part_cpf";
        // $stmt = $conn->prepare($sql);
        // $stmt->bindParam(":part_prop_id", $part_prop_id);
        // $stmt->bindParam(":part_cpf", $part_cpf);
        // $stmt->execute();
        // if ($stmt->fetchColumn() > 0) {
        //   $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Foi encontrado um CPF duplicado! O upload foi interrompido";
        //   header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        //   return die;
        // }
        // -------------------------------

        // Faça a inserção dos dados no banco de dados
        $stmt = $conn->prepare("INSERT INTO participantes (part_id, part_prop_id, part_codigo, part_nome, part_cpf, part_email, part_contato, part_user_id, part_data_cad, part_data_upd) VALUES (?, ?, ?, UPPER(?), ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$part_id, $part_prop_id, $part_codigo, $part_nome, $part_cpf, $part_email, $part_contato, $part_user_id, $part_data_cad, $part_data_upd]);
      } else {
        // Lida com erros ou dados inadequados no CSV
        $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> A linha do CSV não possui a estrutura correta!";
        header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        return die;
      }

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
      $stmt->execute(array(
        ':modulo'     => 'PARTICIPANTES',
        ':acao'       => 'CADASTRO - UPLOAD',
        ':acao_id'    => $part_id,
        ':dados'      => 'ID Proposta: ' . $part_prop_id . '; Código: ' . $part_codigo . '; Nome: ' . $part_nome . '; CPF: ' . $part_cpf . '; Contato: ' . $part_contato,
        ':user_id'    => $_SESSION['reservm_admin_id'],
        ':user_nome'  => $_SESSION['reservm_admin_nome'],
        ':data'       => date('Y-m-d H:i:s')
      ));
      // -------------------------------
    }

    fclose($handle);
    $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Upload e inserção concluídos com sucesso!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } else {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Falha ao abrir o arquivo CSV!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
}

















/*****************************************************************************************
                              EXCLUIR PARTICIPANTES
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_part") {

  $part_id = $_GET['part_id'];

  try {
    $sql = "DELETE FROM participantes WHERE part_id = :part_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':part_id', $part_id);
    $stmt->execute();

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {
      $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i>Dados excluídos!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_acao_user_nome, log_data )
                              VALUES ( :modulo, :acao, :acao_id, :user_id, :user_nome, :data )');
      $stmt->execute(array(
        ':modulo'    => 'PARTICIPANTES',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $part_id,
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
                        EXCLUIR PARTICIPANTES SELECIONADOS
 *****************************************************************************************/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($dados['ExcListParticipante'])) {

  if (isset($_POST['selecionados']) && is_array($_POST['selecionados'])) {
    $part_id = $_POST['selecionados'];
    // $part_id = array_map('intval', $part_id); // Certifique-se de que os IDs sejam inteiros
    $part_id_str = implode("','", $part_id); // Crie uma lista de IDs separada por vírgulas

    $sql = "DELETE FROM participantes WHERE part_id IN ('$part_id_str')";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

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
    $part_id = $_GET['part_id'];
    $sql = "SELECT * FROM participantes
            INNER JOIN propostas ON propostas.prop_id = participantes.part_prop_id
            INNER JOIN certificado ON certificado.cert_prop_id = participantes.part_prop_id
            WHERE part_id = '$part_id'";
    $stmt = $conn->query($sql);
    $part = $stmt->fetch(PDO::FETCH_ASSOC);
    $part_id      = $part['part_id'];
    $part_prop_id = $part['part_prop_id'];
    $part_codigo  = $part['part_codigo'];
    $part_nome    = $part['part_nome'];
    $part_cpf     = $part['part_cpf'];
    $part_email   = $part['part_email'];
    $part_contato = $part['part_contato'];
    // PROPOSTA
    $prop_titulo  = $part['prop_titulo'];

    // Configure o e-mail
    $mail = new PHPMailer(true);
    include  '../controller/email_conf.php';

    $mail->addAddress($part_email, $part_nome);
    $mail->isHTML(true);
    $mail->Subject = 'Certificado Bahiana - ' . $part_codigo;

    //RECUPERA URL PARA O LINK DO EMAIL
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
    $url = $_SERVER['REQUEST_URI'];
    $path = parse_url($url, PHP_URL_PATH);
    $directories = explode('/', $path);
    array_shift($directories);
    $pagina = $protocol . $_SERVER['HTTP_HOST'] . '/' . $directories[0] . '/certificados/participantes.php?part_id=' . $part_id;

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
              Olá, $part_nome
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
        ':modulo'    => 'PARTICIPANTES',
        ':acao'      => 'CERTIFICADO INDIVIDUAL ENVIADO',
        ':acao_id'   => $part_id,
        ':dados'      => 'ID Proposta: ' . $part_prop_id . '; Código: ' . $part_codigo . '; Nome: ' . $part_nome . '; CPF: ' . $part_cpf . '; Contato: ' . $part_contato,
        ':user_id'   => $_SESSION['reservm_admin_id'],
        ':user_nome' => $_SESSION['reservm_admin_nome'],
        ':data'      => date('Y-m-d H:i:s')
      ));
      // -------------------------------

      // echo "E-mail enviado para: $part_email<br>";
      $_SESSION["msg"] = "O certificado de $part_nome foi enviado com sucesso!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    } else {
      //echo "Erro ao enviar e-mail para: $part_email - " . $mail->ErrorInfo . "<br>";
      $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro ao enviar e-mail para: $part_email - "  . $mail->ErrorInfo . "<br>";
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

if (isset($_GET['funcao']) && $_GET['funcao'] == "email_part") {

  // $prop_id = $_GET['prop_id'];
  // $sql = $conn->query("SELECT * FROM participantes
  //                     LEFT JOIN certificado ON certificado.cert_prop_id = participantes.part_prop_id
  //                     WHERE part_prop_id = '$prop_id'");
  // while ($part = $sql->fetch(PDO::FETCH_ASSOC)) {
  //   $part_id                = $part['part_id'];
  //   $part_prop_id           = $part['part_prop_id'];
  //   $part_codigo            = $part['part_codigo'];
  //   $part_nome              = $part['part_nome'];
  //   $part_cpf               = $part['part_cpf'];
  //   $part_email             = $part['part_email'];
  //   $part_contato           = $part['part_contato'];
  //   $part_user_id           = $part['part_user_id'];
  //   $part_data_cad          = $part['part_data_cad'];
  //   $part_data_upd          = $part['part_data_upd'];

  //   // $mails = $part_email . ', ';
  //   // echo $mails;
  //   set_time_limit(300);

  //   try {
  //     $mail = new PHPMailer(true); // ENVIA E-MAIL
  //     include '../controller/email_conf.php';
  //     include '../includes/email/send_email.php'; // CONFIGURAÇÃO DE E-MAILS
  //     $mail->addAddress($part_email); // E-MAIL DO ADMINISTRADOR
  //     //$mail->addAddress($_SESSION['email']); // E-MAIL DO USUÁRIO QUE RECEBERÁ A MENSAGEM 
  //     $mail->isHTML(true);
  //     $mail->Subject = 'E-mail'; //TÍTULO DO E-MAIL

  //RECUPERA URL PARA O LINK DO EMAIL
  // $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
  // $url = $_SERVER['REQUEST_URI'];
  // $path = parse_url($url, PHP_URL_PATH);
  // $directories = explode('/', $path);
  // array_shift($directories);
  // $pagina = $protocol . $_SERVER['HTTP_HOST'] . '/' . $directories[0] . '/certificados/participantes.php?part_id=' . $part_id;

  //     // CORPO DO EMAIL
  //     include '../includes/email/email_header.php';
  //     $email_conteudo .= "
  //     <p style='width: 100%; font-size: 1.188rem; text-transform: uppercase; font-weight: 600; margin-bottom: 20px; display: inline-block;'>
  //     Certificado
  //     </p>

  //     <p style='font-size: 1rem;'>
  //     Conclua o cadastro para que nossa equipe inicie a análise da proposta.
  //     </p>

  //     <a style='cursor: pointer;' href='$pagina'><button style='background: #62B790; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 13px 20px; margin-top: 20px;' target='_blank'>BAIXAR CERTIFICADO</button></a>";
  //     include '../includes/email/email_footer.php';

  //     $mail->Body  = $email_conteudo;
  //     $mail->send();

  //     $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i>E-mails enviados!";
  //     header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  //   } catch (Exception $e) {
  //     echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  //     // $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro ao tentar enviar o e-mail!";
  //     // echo "<script> history.go(-1);</script>";
  //     // return die;
  //   }
  // }











  //Configuração do PHPMailer
  // $mail = new PHPMailer(true);
  // try {
  //   include  '../controller/email_conf.php';
  //   // Lista de destinatários
  //   $destinatarios = array(
  //     'andre.silveira.freitas@gmail.com' => 'Nome Destinatário 1',
  //     'casfreitas@outlook.com' => 'Nome Destinatário 2',
  //     'casfreitas@yahoo.com.br' => 'Nome Destinatário 3',
  //     'nti-andrefreitas@bahiana.edu.br' => 'Nome Destinatário 4',
  //     'andre.silveira.freitas4@gmail.com' => 'Nome Destinatário 1',
  //     'andre.silveira.freitas5@gmail.com' => 'Nome Destinatário 1',
  //     'andre.silveira.freitas6@gmail.com' => 'Nome Destinatário 1',
  //     'andre.silveira.freitas7@gmail.com' => 'Nome Destinatário 1',
  //     'andre.silveira.freitas8@gmail.com' => 'Nome Destinatário 1',
  //     'andre.silveira.freitas9@gmail.com' => 'Nome Destinatário 1',
  //     'andre.silveira.freitas10@gmail.com' => 'Nome Destinatário 1',
  //     'andre.silveira.freitas11@gmail.com' => 'Nome Destinatário 1',

  //     // Adicione mais destinatários conforme necessário
  //   );

  //   //RECUPERA URL PARA O LINK DO EMAIL
  //   $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
  //   $url = $_SERVER['REQUEST_URI'];
  //   $path = parse_url($url, PHP_URL_PATH);
  //   $directories = explode('/', $path);
  //   array_shift($directories);
  //   $pagina = $protocol . $_SERVER['HTTP_HOST'] . '/' . $directories[0] . '/certificados/participantes.php?part_id=' . $part_id;

  //   $batchSize = 10; // Enviar em lotes de 10 e-mails
  //   $delay = 10; // Atraso de 5 minutos (300 segundos) entre os lotes
  //   set_time_limit(300);

  //   foreach (array_chunk($destinatarios, $batchSize, true) as $batch) {
  //     foreach ($batch as $part_email => $part_nome) {
  //       $mail->clearAllRecipients();
  //       $mail->addAddress($part_email, $part_nome);
  //       $mail->isHTML(true);
  //       $mail->Subject = 'Certificado Bahiana - ' . $part_nome;

  //       // CORPO DO EMAIL
  //       include '../includes/email/email_header_800.php';
  //       $email_conteudo .= "
  //       <p style='width: 100%; font-size: 1.188rem; text-transform: uppercase; font-weight: 600; margin-bottom: 20px; display: inline-block;'>
  //       Certificado $part_nome
  //       </p>

  //       <p style='font-size: 1rem;'>
  //       Conclua o cadastro para que nossa equipe inicie a análise da proposta.
  //       </p>

  //       <a style='cursor: pointer;' href='$pagina'><button style='background: #62B790; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 13px 20px; margin-top: 20px;' target='_blank'>BAIXAR CERTIFICADO</button></a>";
  //       include '../includes/email/email_footer.php';

  //       $mail->Body = $email_conteudo;
  //       $mail->send();
  //       echo "E-mail enviado para $part_nome ($part_email) com sucesso.<br>";
  //     }

  //     sleep($delay); // Atraso entre os lotes
  //   }
  // } catch (Exception $e) {
  //   //echo 'Erro ao enviar email: ' . $mail->ErrorInfo;
  //   $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro ao tentar enviar o e-mail!";
  //   echo "<script> history.go(-1);</script>";
  //   return die;
  // }




  try {
    $prop_id = $_GET['prop_id'];
    $sql = "SELECT * FROM participantes
            INNER JOIN propostas ON propostas.prop_id = participantes.part_prop_id
            INNER JOIN certificado ON certificado.cert_prop_id = participantes.part_prop_id
            WHERE part_prop_id = '$prop_id'";
    $stmt = $conn->query($sql);
    $participantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($participantes as $part) {
      $part_id     = $part['part_id'];
      $part_codigo = $part['part_codigo'];
      $part_nome   = $part['part_nome'];
      $part_email  = $part['part_email'];

      // Verifique a conformidade com regulamentações de privacidade de dados e obtenha consentimento do cliente, se necessário.

      // Configure o e-mail
      $mail = new PHPMailer(true);
      include  '../controller/email_conf.php';

      $mail->addAddress($part_email, $part_nome);
      $mail->isHTML(true);
      $mail->Subject = 'Certificado Bahiana - ' . $part_codigo;

      //RECUPERA URL PARA O LINK DO EMAIL
      $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
      $url = $_SERVER['REQUEST_URI'];
      $path = parse_url($url, PHP_URL_PATH);
      $directories = explode('/', $path);
      array_shift($directories);
      $pagina = $protocol . $_SERVER['HTTP_HOST'] . '/' . $directories[0] . '/certificados/participantes.php?part_id=' . $part_id;

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
              Olá, {$part['part_nome']}
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

      // Olá $part_nome,\n\nAqui estão seus dados:\n\nNome: $part_nome\nE-mail: $part_email\nCPF: {$part['part_cpf']}\nTelefone: {$part['part_contato']}

      // Anexar o PDF gerado
      // require '../certificados/participantes.php?part_id=' . $part_id;
      // $mail->addStringAttachment($pagina, 'exemplo.pdf');

      // Envie o e-mail
      if ($mail->Send()) {
        // echo "E-mail enviado para: $part_email<br>";
        $_SESSION["msg"] = "Certificados enviados com sucesso!";
        header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      } else {
        //echo "Erro ao enviar e-mail para: $part_email - " . $mail->ErrorInfo . "<br>";
        $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro ao enviar e-mail para: $part_email - "  . $mail->ErrorInfo . "<br>";
        header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      }
    }

    $conn = null;
  } catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
  }
}
