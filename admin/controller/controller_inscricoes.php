<?php
session_start();
include '../../conexao/conexao.php';

// NECESSÁRIO PARA ENVIAR O EMAIL
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                              CADASTRAR INSCRIÇÕES
 *****************************************************************************************/
if (isset($dados['CadInscricao'])) {

  $insc_id             = md5(uniqid(rand(), true)); // GERA UM ID UNICO
  $insc_prop_id        = base64_decode($_POST['insc_prop_id']);
  $insc_categoria      = base64_decode($_POST['insc_categoria']);
  $insc_codigo         = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT); // GERA UM CÓDIGO COM 5 DÍGITOS 
  $insc_nome           = trim($_POST['insc_nome']) !== '' ? trim($_POST['insc_nome']) : NULL;
  //
  $cpfDot              = trim($_POST['insc_cpf']) !== '' ? trim($_POST['insc_cpf']) : NULL;
  $insc_cpf            = str_replace(['.', '-'], '', $cpfDot); // RETIRA PONTOS E TRAÇOS
  //
  $insc_email          = trim($_POST['insc_email']) !== '' ? trim($_POST['insc_email']) : NULL;
  $insc_contato        = trim($_POST['insc_contato']) !== '' ? trim($_POST['insc_contato']) : NULL;
  $insc_tipo           = trim($_POST['insc_tipo']) !== '' ? trim($_POST['insc_tipo']) : NULL;
  $insc_titulo         = trim($_POST['insc_titulo']) !== '' ? trim($_POST['insc_titulo']) : NULL;
  $insc_nome_coautor   = trim($_POST['insc_nome_coautor']) !== '' ? trim($_POST['insc_nome_coautor']) : NULL;
  $insc_credenciamento = trim(isset($_POST['insc_credenciamento'])) ? $_POST['insc_credenciamento'] : 0;
  $reservm_admin_id       = $_SESSION['reservm_admin_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM inscricoes WHERE insc_codigo = :insc_codigo AND insc_prop_id = :insc_prop_id AND insc_categoria = :insc_categoria";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':insc_prop_id' => $insc_prop_id,
    ':insc_categoria' => $insc_categoria,
    ':insc_codigo' => $insc_codigo
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este código já foi cadastrado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
  // -------------------------------

  $sql = "SELECT COUNT(*) FROM inscricoes WHERE insc_nome = :insc_nome AND insc_prop_id = :insc_prop_id AND insc_categoria = :insc_categoria";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':insc_prop_id' => $insc_prop_id,
    ':insc_categoria' => $insc_categoria,
    ':insc_nome' => $insc_nome
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este nome já foi cadastrado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
  // -------------------------------

  $sql = "SELECT COUNT(*) FROM inscricoes WHERE insc_cpf = :insc_cpf AND insc_prop_id = :insc_prop_id AND insc_categoria = :insc_categoria";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':insc_prop_id' => $insc_prop_id,
    ':insc_categoria' => $insc_categoria,
    ':insc_cpf' => $insc_cpf
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este CPF já foi cadastrado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
  // -------------------------------

  $sql = "SELECT COUNT(*) FROM inscricoes WHERE insc_email = :insc_email AND insc_prop_id = :insc_prop_id AND insc_categoria = :insc_categoria";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':insc_prop_id' => $insc_prop_id,
    ':insc_categoria' => $insc_categoria,
    ':insc_email' => $insc_email
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este e-mail já foi cadastrado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
  // -------------------------------

  if (!empty($insc_nome_coautor)) {
    $sql = "SELECT COUNT(*) FROM inscricoes WHERE insc_nome_coautor = :insc_nome_coautor AND insc_prop_id = :insc_prop_id AND insc_categoria = :insc_categoria";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':insc_prop_id' => $insc_prop_id,
      ':insc_categoria' => $insc_categoria,
      ':insc_nome_coautor' => $insc_nome_coautor
    ]);
    if ($stmt->fetchColumn() > 0) {
      $_SESSION["erro"] = "Este coautor já foi cadastrado!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      return die;
    }
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "INSERT INTO inscricoes ( 
                                      insc_id,
                                      insc_prop_id,
                                      insc_categoria,
                                      insc_codigo,
                                      insc_nome,
                                      insc_cpf,
                                      insc_email,
                                      insc_contato,
                                      insc_tipo,
                                      insc_titulo,
                                      insc_nome_coautor,
                                      insc_credenciamento,
                                      insc_user_id,
                                      insc_data_cad,
                                      insc_data_upd
                                    ) VALUES (
                                      :insc_id,
                                      :insc_prop_id,
                                      :insc_categoria,
                                      :insc_codigo,
                                      UPPER(:insc_nome),
                                      :insc_cpf,
                                      LOWER(:insc_email),
                                      :insc_contato,
                                      UPPER(:insc_tipo),
                                      UPPER(:insc_titulo),
                                      UPPER(:insc_nome_coautor),
                                      :insc_credenciamento,
                                      :insc_user_id,
                                      GETDATE(),
                                      GETDATE()
                                    )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':insc_id' => $insc_id,
      ':insc_prop_id' => $insc_prop_id,
      ':insc_categoria' => $insc_categoria,
      ':insc_codigo' => $insc_codigo,
      ':insc_nome' => $insc_nome,
      ':insc_cpf' => $insc_cpf,
      ':insc_email' => $insc_email,
      ':insc_contato' => $insc_contato,
      ':insc_tipo' => $insc_tipo,
      ':insc_titulo' => $insc_titulo,
      ':insc_nome_coautor' => $insc_nome_coautor,
      ':insc_credenciamento' => $insc_credenciamento,
      ':insc_user_id' => $reservm_admin_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES (:modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'INSCRIÇÕES',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $insc_id,
      ':dados'      =>
      'ID Proposta: ' . $insc_prop_id .
        '; Código: ' . $insc_codigo .
        '; Categoria: ' . $insc_categoria .
        '; Nome: ' . $insc_nome .
        '; CPF: ' . $insc_cpf .
        '; E-mail: ' . $insc_email .
        '; Contato: ' . $insc_contato .
        '; Tipo: ' . $insc_tipo .
        '; Título: ' . $insc_titulo .
        '; Coautor: ' . $insc_nome_coautor .
        '; Credenciamento: ' . $insc_credenciamento,
      ':user_id'    => $reservm_admin_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Cadastro realizado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Cadastro não realizado!" . $e->getMessage();
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}
















/*****************************************************************************************
                                  EDITAR INSCRIÇÕES
 *****************************************************************************************/
if (isset($dados['EditInscricao'])) {

  $insc_id             = base64_decode($_POST['insc_id']);
  $insc_prop_id        = base64_decode($_POST['insc_prop_id']);
  $insc_categoria      = base64_decode($_POST['insc_categoria']);
  //
  $insc_nome           = trim($_POST['insc_nome']) !== '' ? trim($_POST['insc_nome']) : NULL;
  //
  $cpfDot              = trim($_POST['insc_cpf']) !== '' ? trim($_POST['insc_cpf']) : NULL;
  $insc_cpf            = str_replace(['.', '-'], '', $cpfDot); // RETIRA PONTOS E TRAÇOS
  //
  $insc_email          = trim($_POST['insc_email']) !== '' ? trim($_POST['insc_email']) : NULL;
  $insc_contato        = trim($_POST['insc_contato']) !== '' ? trim($_POST['insc_contato']) : NULL;
  $insc_tipo           = trim($_POST['insc_tipo']) !== '' ? trim($_POST['insc_tipo']) : NULL;
  $insc_titulo         = trim($_POST['insc_titulo']) !== '' ? trim($_POST['insc_titulo']) : NULL;
  $insc_nome_coautor   = trim($_POST['insc_nome_coautor']) !== '' ? trim($_POST['insc_nome_coautor']) : NULL;
  $insc_credenciamento = trim($_POST['insc_credenciamento']) !== '' ? trim($_POST['insc_credenciamento']) : 0;
  $reservm_admin_id       = $_SESSION['reservm_admin_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM inscricoes WHERE insc_nome = :insc_nome AND insc_prop_id = :insc_prop_id AND insc_categoria = :insc_categoria AND insc_id != :insc_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':insc_id' => $insc_id,
    ':insc_prop_id' => $insc_prop_id,
    ':insc_categoria' => $insc_categoria,
    ':insc_nome' => $insc_nome
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este nome já foi cadastrado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
  // -------------------------------

  $sql = "SELECT COUNT(*) FROM inscricoes WHERE insc_cpf = :insc_cpf AND insc_prop_id = :insc_prop_id AND insc_categoria = :insc_categoria AND insc_id != :insc_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':insc_id' => $insc_id,
    ':insc_prop_id' => $insc_prop_id,
    ':insc_categoria' => $insc_categoria,
    ':insc_cpf' => $insc_cpf
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este CPF já foi cadastrado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
  // -------------------------------

  $sql = "SELECT COUNT(*) FROM inscricoes WHERE insc_email = :insc_email AND insc_prop_id = :insc_prop_id AND insc_categoria = :insc_categoria AND insc_id != :insc_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':insc_id' => $insc_id,
    ':insc_prop_id' => $insc_prop_id,
    ':insc_categoria' => $insc_categoria,
    ':insc_email' => $insc_email
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este e-mail já foi cadastrado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
  // -------------------------------

  if (!empty($insc_nome_coautor)) {
    $sql = "SELECT COUNT(*) FROM inscricoes WHERE insc_nome_coautor = :insc_nome_coautor AND insc_prop_id = :insc_prop_id AND insc_categoria = :insc_categoria  AND insc_id != :insc_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':insc_id' => $insc_id,
      ':insc_prop_id' => $insc_prop_id,
      ':insc_categoria' => $insc_categoria,
      ':insc_nome_coautor' => $insc_nome_coautor
    ]);
    if ($stmt->fetchColumn() > 0) {
      $_SESSION["erro"] = "Este coautor já foi cadastrado!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      return die;
    }
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "UPDATE
                    inscricoes
              SET
                    insc_nome           = UPPER(:insc_nome),
                    insc_cpf            = :insc_cpf,
                    insc_email          = LOWER(:insc_email),
                    insc_contato        = :insc_contato,
                    insc_tipo           = UPPER(:insc_tipo),
                    insc_titulo         = UPPER(:insc_titulo),
                    insc_nome_coautor   = UPPER(:insc_nome_coautor),
                    insc_credenciamento = :insc_credenciamento,
                    insc_user_id        = :insc_user_id,
                    insc_data_upd       = GETDATE()
              WHERE
                    insc_id = :insc_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':insc_id' => $insc_id,
      ':insc_nome' => $insc_nome,
      ':insc_cpf' => $insc_cpf,
      ':insc_email' => $insc_email,
      ':insc_contato' => $insc_contato,
      ':insc_tipo' => $insc_tipo,
      ':insc_titulo' => $insc_titulo,
      ':insc_nome_coautor' => $insc_nome_coautor,
      ':insc_credenciamento' => $insc_credenciamento,
      ':insc_user_id' => $reservm_admin_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'INSCRIÇÕES',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $insc_id,
      ':dados'      =>
      'ID Proposta: ' . $insc_prop_id .
        '; Nome: ' . $insc_nome .
        '; CPF: ' . $insc_cpf .
        '; E-mail: ' . $insc_email .
        '; Contato: ' . $insc_contato .
        '; Tipo: ' . $insc_tipo .
        '; Título: ' . $insc_titulo .
        '; Coautor: ' . $insc_nome_coautor .
        '; Credenciamento: ' . $insc_credenciamento,
      ':user_id'    => $reservm_admin_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Dados atualizados!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Dados não atualizados!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}















/*****************************************************************************************
                              UPLOAD LISTA INSCRIÇÕES
 *****************************************************************************************/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['arquivo']) && isset($_POST['insc_prop_id'])) {

  $csv_file       = $_FILES['arquivo']['tmp_name'];
  $insc_prop_id   = base64_decode($_POST['insc_prop_id']);
  $insc_categoria = base64_decode($_POST['insc_categoria']);
  $reservm_admin_id  = $_SESSION['reservm_admin_id'];

  if (($handle = fopen($csv_file, 'r')) !== false) {

    // Pule a primeira linha do CSV
    fgetcsv($handle, 1000, ';');

    while (($data = fgetcsv($handle, 1000, ';')) !== false) {
      // Certifique-se de que a linha do CSV tem a estrutura correta
      if (count($data) >= 2 && !empty($data[0]) && !empty($data[1]) && !empty($data[2])) {

        $insc_id             = md5(uniqid(rand(), true)); // GERA UM ID UNICO
        $insc_codigo         = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT); // GERA UM CÓDIGO COM 6 DÍGITOS 
        // -------------------------------
        $insc_nome           = $data[0];
        $insc_cpf            = trim(substr($data[1], 0, 11)); // LIMITA CARACTERES ATÉ 11
        $insc_email          = trim($data[2]);
        $insc_contato        = trim(substr($data[3], 0, 15)); // LIMITA CARACTERES ATÉ 15

        if ($insc_categoria == 3) { // SE MINISTRANTES (CAT = 3), "TITULO" NÃO PODE SER VAZIO
          if (!empty($data[4])) {
            $insc_titulo = $data[4];
          } else {
            $_SESSION["erro"] = "Alguns dados não foram cadastrados! Uma linha do arquivo não possui a estrutura correta.";
            header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
            return die;
          }
        } else if ($insc_categoria == 4) { // SE APRESENTAÇÃO DE TRABALHO (CAT = 4), "TIPO", "TITULO" E "COAUTOR" NÃO PODE SER VAZIO
          if (!empty($data[4]) && !empty($data[5]) && !empty($data[6])) {
            $insc_tipo           = $data[4];
            $insc_titulo         = $data[5];
            $insc_nome_coautor   = $data[6];
            $insc_credenciamento = $data[7];
          } else {
            $_SESSION["erro"] = "Alguns dados não foram cadastrados! Uma linha do arquivo não possui a estrutura correta.";
            header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
            return die;
          }
        } else if ($insc_categoria == 6) { // SE ATIVIDADE EXTRA (CAT = 6), "TIPO" NÃO PODE SER VAZIO
          if (!empty($data[4])) {
            $insc_tipo           = trim($data[4]);
            $insc_credenciamento = $data[5];
          } else {
            $_SESSION["erro"] = "Alguns dados não foram cadastrados! Uma linha do arquivos não possui a estrutura correta.";
            header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
            return die;
          }
        } else {
          $insc_tipo           = NULL;
          $insc_titulo         = NULL;
          $insc_nome_coautor   = NULL;
          $insc_credenciamento = $data[4];
        }

        // IMPEDE CADASTRO DUPLICADO
        try {
          $sql = "SELECT COUNT(*) FROM inscricoes WHERE insc_codigo = :insc_codigo AND insc_prop_id = :insc_prop_id AND insc_categoria = :insc_categoria";
          $stmt = $conn->prepare($sql);
          $stmt->execute([
            ':insc_prop_id' => $insc_prop_id,
            ':insc_categoria' => $insc_categoria,
            ':insc_codigo' => $insc_codigo
          ]);
          if ($stmt->fetchColumn() > 0) {
            $_SESSION["erro"] = "O código $insc_codigo já foi cadastrado!";
            header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
            return die;
          }
        } catch (PDOException $e) {
          //echo 'Error: ' . $e->getMessage();
          $_SESSION["erro"] = "Erro ao tentar realizar a importação!";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
          return die;
        }

        try {
          $sql = "SELECT COUNT(*) FROM inscricoes WHERE insc_nome = :insc_nome AND insc_prop_id = :insc_prop_id AND insc_categoria = :insc_categoria";
          $stmt = $conn->prepare($sql);
          $stmt->execute([
            ':insc_prop_id' => $insc_prop_id,
            ':insc_categoria' => $insc_categoria,
            ':insc_nome' => $insc_nome
          ]);
          if ($stmt->fetchColumn() > 0) {
            $_SESSION["erro"] = "O nome $insc_nome já foi cadastrado!";
            header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
            return die;
          }
        } catch (PDOException $e) {
          //echo 'Error: ' . $e->getMessage();
          $_SESSION["erro"] = "Erro ao tentar realizar a importação!";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
          return die;
        }

        try {
          $sql = "SELECT COUNT(*) FROM inscricoes WHERE insc_cpf = :insc_cpf AND insc_prop_id = :insc_prop_id AND insc_categoria = :insc_categoria";
          $stmt = $conn->prepare($sql);
          $stmt->execute([
            ':insc_prop_id' => $insc_prop_id,
            ':insc_categoria' => $insc_categoria,
            ':insc_cpf' => $insc_cpf
          ]);
          if ($stmt->fetchColumn() > 0) {
            $_SESSION["erro"] = "O CPF $insc_cpf já foi cadastrado!";
            header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
            return die;
          }
        } catch (PDOException $e) {
          //echo 'Error: ' . $e->getMessage();
          $_SESSION["erro"] = "Erro ao tentar realizar a importação!";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
          return die;
        }

        try {
          $sql = "SELECT COUNT(*) FROM inscricoes WHERE insc_email = :insc_email AND insc_prop_id = :insc_prop_id AND insc_categoria = :insc_categoria";
          $stmt = $conn->prepare($sql);
          $stmt->execute([
            ':insc_prop_id' => $insc_prop_id,
            ':insc_categoria' => $insc_categoria,
            ':insc_email' => $insc_email
          ]);
          if ($stmt->fetchColumn() > 0) {
            $_SESSION["erro"] = "O e-mail $insc_email já foi cadastrado!";
            header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
            return die;
          }
        } catch (PDOException $e) {
          //echo 'Error: ' . $e->getMessage();
          $_SESSION["erro"] = "Erro ao tentar realizar a importação!";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
          return die;
        }

        try {
          if (!empty($insc_nome_coautor)) {
            $sql = "SELECT COUNT(*) FROM inscricoes WHERE insc_nome_coautor = :insc_nome_coautor AND insc_prop_id = :insc_prop_id AND insc_categoria = :insc_categoria";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
              ':insc_prop_id' => $insc_prop_id,
              ':insc_categoria' => $insc_categoria,
              ':insc_nome_coautor' => $insc_nome_coautor
            ]);
            if ($stmt->fetchColumn() > 0) {
              $_SESSION["erro"] = "O coautor $insc_nome_coautor já foi cadastrado!";
              header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
              return die;
            }
          }
        } catch (PDOException $e) {
          //echo 'Error: ' . $e->getMessage();
          $_SESSION["erro"] = "Erro ao tentar realizar a importação!";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
          return die;
        }
        // -------------------------------

        // Faça a inserção dos dados no banco de dados
        try {
          $sql = "INSERT INTO inscricoes (
                                            insc_id,
                                            insc_prop_id,
                                            insc_categoria,
                                            insc_codigo,
                                            insc_nome,
                                            insc_cpf,
                                            insc_email,
                                            insc_contato,
                                            insc_tipo,
                                            insc_titulo,
                                            insc_nome_coautor,
                                            insc_credenciamento,
                                            insc_user_id,
                                            insc_data_cad,
                                            insc_data_upd
                                          ) VALUES (
                                            :insc_id,
                                            :insc_prop_id,
                                            :insc_categoria,
                                            :insc_codigo,
                                            UPPER(:insc_nome),
                                            :insc_cpf,
                                            LOWER(:insc_email),
                                            :insc_contato,
                                            UPPER(:insc_tipo),
                                            UPPER(:insc_titulo),
                                            UPPER(:insc_nome_coautor),
                                            :insc_credenciamento,
                                            :insc_user_id,
                                            GETDATE(),
                                            GETDATE()
                                          )";
          $stmt = $conn->prepare($sql);
          $stmt->execute([
            ':insc_id' => $insc_id,
            ':insc_prop_id' => $insc_prop_id,
            ':insc_categoria' => $insc_categoria,
            ':insc_codigo' => $insc_codigo,
            ':insc_nome' => $insc_nome,
            ':insc_cpf' => $insc_cpf,
            ':insc_email' => $insc_email,
            ':insc_contato' => $insc_contato,
            ':insc_tipo' => $insc_tipo,
            ':insc_titulo' => $insc_titulo,
            ':insc_nome_coautor' => $insc_nome_coautor,
            ':insc_credenciamento' => $insc_credenciamento,
            ':insc_user_id' => $reservm_admin_id
          ]);
        } catch (PDOException $e) {
          //echo 'Error: ' . $e->getMessage();
          $_SESSION["erro"] = "Erro ao tentar realizar a importação!";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
          return die;
        }
      } else {
        // Lida com erros ou dados inadequados no CSV
        $_SESSION["erro"] = "A linha do CSV não possui a estrutura correta!";
        header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        return die;
      }

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'     => 'INSCRIÇÕES',
        ':acao'       => 'CADASTRO - UPLOAD',
        ':acao_id'    => $insc_id,
        ':dados'      =>
        'ID Proposta: ' . $insc_prop_id .
          '; Código: ' . $insc_codigo .
          '; Categoria: ' . $insc_categoria .
          '; Nome: ' . $insc_nome .
          '; CPF: ' . $insc_cpf .
          '; E-mail: ' . $insc_email .
          '; Contato: ' . $insc_contato .
          '; Tipo: ' . $insc_tipo .
          '; Título: ' . $insc_titulo .
          '; Coautor: ' . $insc_nome_coautor .
          '; Credenciamento: ' . $insc_credenciamento,
        ':user_id'    => $reservm_admin_id
      ]);
      // -------------------------------
    }

    fclose($handle);
    $_SESSION["msg"] = "A importação foi concluída com sucesso!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    //echo "<script> history.go(-1);</script>";
  } else {
    $_SESSION["erro"] = "Falha ao abrir o arquivo CSV!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
}








/*****************************************************************************************
                              CREDENCIAR INSCRITOS
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "adm_credenciamento" && isset($_POST['cred_selecionados'])) {

  $insc_prop_id        = base64_decode($_GET['i']);
  $insc_credenciamento = 1;
  $reservm_admin_id       = $_SESSION['reservm_admin_id'];
  //
  $insc_id             = $_POST['cred_selecionados'];
  $insc_id_str         = implode("','", $insc_id); // Crie uma lista de IDs separada por vírgulas

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "UPDATE
                    inscricoes
              SET
                    insc_credenciamento = :insc_credenciamento,
                    insc_user_id        = :insc_user_id,
                    insc_data_upd       = GETDATE()
              WHERE
                    insc_id IN ('$insc_id_str') AND insc_prop_id = :insc_prop_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':insc_prop_id' => $insc_prop_id,
      ':insc_credenciamento' => $insc_credenciamento,
      ':insc_user_id' => $reservm_admin_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES (:modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'INSCRIÇÕES',
      ':acao'       => 'CREDENCIAMENTO',
      ':acao_id'    => $insc_prop_id,
      ':dados'      => 'ID: ' . $insc_id_str,
      ':user_id'    => $reservm_admin_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Os inscritos selecionados foram credenciados!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Erro ao tentar credenciar os inscritos!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}

















/*****************************************************************************************
                              EXCLUIR INSCRIÇÕES
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_insc") {

  $insc_id       = base64_decode($_GET['insc_id']);
  $reservm_admin_id = $_SESSION['reservm_admin_id'];

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "DELETE FROM inscricoes WHERE insc_id = :insc_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':insc_id' => $insc_id]);

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                              VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'    => 'INSCRIÇÕES',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $insc_id,
        ':user_id'   => $reservm_admin_id
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


















/*****************************************************************************************
                        EXCLUIR INSCRIÇÕES SELECIONADOS
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "adm_inscricoes" && isset($_POST['exc_selecionados'])) {

  $prop_id       = base64_decode($_GET['i']);
  $reservm_admin_id = $_SESSION['reservm_admin_id'];

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO

    if (isset($_POST['exc_selecionados']) && is_array($_POST['exc_selecionados'])) {
      $insc_id = $_POST['exc_selecionados'];
      $insc_id_str = implode("','", $insc_id);

      $sql = "DELETE FROM inscricoes WHERE insc_id IN ('$insc_id_str')";
      $stmt = $conn->prepare($sql);
      $stmt->execute();

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES (:modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'     => 'INSCRIÇÕES',
        ':acao'       => 'EXCLUSÃO SELECIONADOS',
        ':acao_id'    => $prop_id,
        ':dados'      => 'IDs: ' . $insc_id_str,
        ':user_id'    => $reservm_admin_id
      ]);
      // -------------------------------

      $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

      $_SESSION["msg"] = "Dados excluídos!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    } else {
      $conn->rollBack();
      $_SESSION["erro"] = "Nenhum dado selecionado!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    }
  } catch (PDOException $e) {
    //echo "Erro: " . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Dados não excluídos!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
}










/*****************************************************************************************
                                E-MAIL INDIVIDUAL
 *****************************************************************************************/

if (isset($_GET['funcao']) && $_GET['funcao'] == "send_mail") {

  $reservm_admin_id = $_SESSION['reservm_admin_id'];

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO

    $insc_id = base64_decode($_GET['insc_id']);
    $sql = "SELECT * FROM inscricoes
            INNER JOIN propostas ON propostas.prop_id = inscricoes.insc_prop_id
            INNER JOIN certificado ON certificado.cert_prop_id COLLATE SQL_Latin1_General_CP1_CI_AI = inscricoes.insc_prop_id
            WHERE insc_id = '$insc_id'";
    $stmt = $conn->query($sql);
    $insc = $stmt->fetch(PDO::FETCH_ASSOC);
    $insc_id                = $insc['insc_id'];
    $insc_prop_id           = $insc['insc_prop_id'];
    $insc_categoria         = $insc['insc_categoria'];
    $insc_codigo            = $insc['insc_codigo'];
    $insc_nome              = $insc['insc_nome'];
    $insc_cpf               = $insc['insc_cpf'];
    $insc_email             = $insc['insc_email'];
    $insc_contato           = $insc['insc_contato'];
    $insc_tipo              = $insc['insc_tipo'];
    $insc_titulo            = $insc['insc_titulo'];
    $insc_nome_coautor      = $insc['insc_nome_coautor'];
    $insc_user_id           = $insc['insc_user_id'];
    $insc_data_cad          = $insc['insc_data_cad'];
    $insc_data_upd          = $insc['insc_data_upd'];
    // PROPOSTA
    $prop_titulo            = $insc['prop_titulo'];
    // CERTIFICADO
    $cert_id                = $insc['cert_id'];
    $cert_prop_id           = $insc['cert_prop_id'];
    $cert_categoria         = $insc['cert_categoria'];
    $cert_nome_comissao     = $insc['cert_nome_comissao'];
    $cert_texto             = $insc['cert_texto'];
    $cert_titulo_trabalho   = $insc['cert_titulo_trabalho'];
    $cert_area_tematica     = $insc['cert_area_tematica'];
    $cert_autores           = $insc['cert_autores'];
    $cert_coautores         = $insc['cert_coautores'];
    $cert_modalidade        = $insc['cert_modalidade'];
    $cert_conteudo_programa = $insc['cert_conteudo_programa'];
    $cert_data_inicio       = $insc['cert_data_inicio'];
    $cert_data_fim          = $insc['cert_data_fim'];
    $cert_carga             = $insc['cert_carga'];
    $cert_user_id           = $insc['cert_user_id'];
    $cert_data_cad          = $insc['cert_data_cad'];
    $cert_data_upd          = $insc['cert_data_upd'];

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES (:modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'    => 'INSCRIÇÕES',
      ':acao'      => 'CERTIFICADO INDIVIDUAL ENVIADO',
      ':acao_id'   => $insc_id,
      ':dados'      => 'ID Proposta: ' . $insc_prop_id . '; Código: ' . $insc_codigo . '; Nome: ' . $insc_nome . '; CPF: ' . $insc_cpf . '; Contato: ' . $insc_contato,
      ':user_id'   => $reservm_admin_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    // Configure o e-mail
    $mail = new PHPMailer(true);
    include '../../conexao/email.php';
    $mail->addAddress($insc_email, $insc_nome);
    $mail->isHTML(true);
    $mail->Subject = 'Certificado - ' . $prop_titulo;

    //RECUPERA URL PARA O LINK DO EMAIL
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
    $url = $_SERVER['REQUEST_URI'];
    $path = parse_url($url, PHP_URL_PATH);
    $directories = explode('/', $path);
    array_shift($directories);
    $pagina = $protocol . $_SERVER['HTTP_HOST'] . '/' . $directories[0] . '/certificados/inscricoes.php?cat=' . base64_encode($insc_categoria) . '&insc_id=' . base64_encode($insc_id);

    // CORPO DO EMAIL
    include '../../includes/email/email_header.php';
    $email_conteudo .= "
        <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
        
            <td style='padding: 2em 2rem; display: inline-block;'>
    
            <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 20px 0px;'>
            Olá, $insc_nome
            </p>
    
            <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 20px 0px;'>
            Seu certificado de participação no evento <strong>$prop_titulo</strong> já está disponível!
            </p>
    
            <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 15px 0px;'>
            Por favor, acesse o link abaixo para baixá-lo.
            </p>
    
            <a style='cursor: pointer;' href='$pagina'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 20px;' target='_blank'>Baixar certificado</button></a>
            </td>
          </tr>";

    include '../../includes/email/email_footer.php';

    $mail->Body  = $email_conteudo;
    $mail->send();
    // -------------------------------

    $_SESSION["msg"] = "O certificado de $insc_nome foi enviado com sucesso!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo "Erro: " . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Erro ao tentar realizar a operação!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
}









/*****************************************************************************************
                                        E-MAIL
 *****************************************************************************************/

if (isset($_GET['funcao']) && $_GET['funcao'] == "email_insc") {

  $reservm_admin_id = $_SESSION['reservm_admin_id'];

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO

    $prop_id = base64_decode($_GET['i']);
    $insc_categoria = base64_decode($_GET['c']);
    $sql = "SELECT * FROM certificado
            INNER JOIN propostas ON propostas.prop_id = certificado.cert_prop_id
            INNER JOIN inscricoes ON inscricoes.insc_prop_id = certificado.cert_prop_id
            WHERE cert_categoria = $insc_categoria AND insc_categoria = $insc_categoria AND cert_prop_id = '$prop_id' AND insc_credenciamento = 1";
    $stmt = $conn->query($sql);
    $inscricoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($inscricoes as $insc) {
      $insc_id                = $insc['insc_id'];
      $insc_prop_id           = $insc['insc_prop_id'];
      $insc_categoria         = $insc['insc_categoria'];
      $insc_codigo            = $insc['insc_codigo'];
      $insc_nome              = $insc['insc_nome'];
      $insc_cpf               = $insc['insc_cpf'];
      $insc_email             = $insc['insc_email'];
      $insc_contato           = $insc['insc_contato'];
      $insc_tipo              = $insc['insc_tipo'];
      $insc_titulo            = $insc['insc_titulo'];
      $insc_nome_coautor      = $insc['insc_nome_coautor'];
      $insc_user_id           = $insc['insc_user_id'];
      $insc_data_cad          = $insc['insc_data_cad'];
      $insc_data_upd          = $insc['insc_data_upd'];
      // PROPOSTA
      $prop_titulo            = $insc['prop_titulo'];
      // CERTIFICADO
      $cert_id                = $insc['cert_id'];
      $cert_prop_id           = $insc['cert_prop_id'];
      $cert_categoria         = $insc['cert_categoria'];
      $cert_nome_comissao     = $insc['cert_nome_comissao'];
      $cert_texto             = $insc['cert_texto'];
      $cert_titulo_trabalho   = $insc['cert_titulo_trabalho'];
      $cert_area_tematica     = $insc['cert_area_tematica'];
      $cert_autores           = $insc['cert_autores'];
      $cert_coautores         = $insc['cert_coautores'];
      $cert_modalidade        = $insc['cert_modalidade'];
      $cert_conteudo_programa = $insc['cert_conteudo_programa'];
      $cert_data_inicio       = $insc['cert_data_inicio'];
      $cert_data_fim          = $insc['cert_data_fim'];
      $cert_carga             = $insc['cert_carga'];
      $cert_user_id           = $insc['cert_user_id'];
      $cert_data_cad          = $insc['cert_data_cad'];
      $cert_data_upd          = $insc['cert_data_upd'];

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                              VALUES (:modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'    => 'INSCRIÇÕES',
        ':acao'      => 'CERTIFICADO ENVIADO',
        ':acao_id'   => $insc_id,
        ':dados'      => 'ID Proposta: ' . $insc_prop_id . '; Código: ' . $insc_codigo . '; Nome: ' . $insc_nome . '; CPF: ' . $insc_cpf . '; Contato: ' . $insc_contato,
        ':user_id'   => $reservm_admin_id
      ]);
      // -------------------------------

      // Configure o e-mail
      $mail = new PHPMailer(true);
      include '../../conexao/email.php';

      $mail->addAddress($insc_email, $insc_nome);
      $mail->isHTML(true);
      $mail->Subject = 'Certificado - ' . $prop_titulo;

      //RECUPERA URL PARA O LINK DO EMAIL
      $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
      $url = $_SERVER['REQUEST_URI'];
      $path = parse_url($url, PHP_URL_PATH);
      $directories = explode('/', $path);
      array_shift($directories);
      $pagina = $protocol . $_SERVER['HTTP_HOST'] . '/' . $directories[0] . '/certificados/inscricoes.php?cat=' . base64_encode($insc_categoria) . '&insc_id=' . base64_encode($insc_id);

      // CORPO DO EMAIL
      include '../../includes/email/email_header.php';
      $email_conteudo .= "
      <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
        <td style='padding: 2em 2rem; display: inline-block;'>

        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 20px 0px;'>
        Olá, $insc_nome
        </p>

        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 20px 0px;'>
        Seu certificado de participação no evento <strong>$prop_titulo</strong> já está disponível!
        </p>

        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 15px 0px;'>
        Por favor, acesse o link abaixo para baixá-lo.
        </p>

        <a style='cursor: pointer;' href='$pagina'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 20px;' target='_blank'>Baixar certificado</button></a>
        </td>
      </tr>";

      include '../../includes/email/email_footer.php';

      // Envie o e-mail
      $mail->Body  = $email_conteudo;
      if ($mail->Send()) {
        // echo "E-mail enviado para: $insc_email<br>";
        $_SESSION["msg"] = "Certificados enviados com sucesso!";
        header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      } else {
        //echo "Erro ao enviar e-mail para: $insc_email - " . $mail->ErrorInfo . "<br>";
        $_SESSION["erro"] = "Erro ao enviar e-mail para: $insc_email - "  . $mail->ErrorInfo . "<br>";
        header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      }
      $mail->clearAddresses(); // Limpar os destinatários para o próximo loop
    }

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    //$conn = null;
  } catch (PDOException $e) {
    //echo "Erro: " . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Erro ao tentar realizar a operação!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
}
