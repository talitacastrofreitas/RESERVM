<?php
session_start();
include '../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                          CADASTRAR RESPONSÁVEL CONTATO
 *****************************************************************************************/
if (isset($dados['CadResponsavelContato'])) {

  $prc_id          = md5(uniqid(rand(), true)); // GERA UM ID UNICO
  $prc_proposta_id = base64_decode($_POST['prc_proposta_id']);
  $prc_nome        = trim(isset($_POST['prc_nome'])) ? $_POST['prc_nome'] : NULL;
  $prc_email       = trim(isset($_POST['prc_email'])) ? $_POST['prc_email'] : NULL;
  $prc_contato     = trim(isset($_POST['prc_contato'])) ? $_POST['prc_contato'] : NULL;
  $reservm_user_id    = $_SESSION['reservm_user_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_extensao_responsavel_contato WHERE prc_nome = :prc_nome AND prc_proposta_id = :prc_proposta_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":prc_nome", $prc_nome);
  $stmt->bindParam(":prc_proposta_id", $prc_proposta_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este responsável já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  }
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_extensao_responsavel_contato WHERE prc_email = :prc_email AND prc_proposta_id = :prc_proposta_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":prc_email", $prc_email);
  $stmt->bindParam(":prc_proposta_id", $prc_proposta_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este e-mail já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  }
  // -------------------------------

  try {
    $sql = "INSERT INTO propostas_extensao_responsavel_contato (
                                                                prc_id,
                                                                prc_proposta_id,
                                                                prc_nome,
                                                                prc_contato,
                                                                prc_email,
                                                                prc_user_id,
                                                                prc_data_cad,
                                                                prc_data_upd
                                                              ) VALUES (
                                                                :prc_id,
                                                                :prc_proposta_id,
                                                                UPPER(:prc_nome),
                                                                :prc_contato,
                                                                :prc_email,
                                                                :prc_user_id,
                                                                GETDATE(),
                                                                GETDATE()
                                                              )";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":prc_id", $prc_id);
    $stmt->bindParam(":prc_proposta_id", $prc_proposta_id);
    $stmt->bindParam(":prc_nome", $prc_nome);
    $stmt->bindParam(":prc_contato", $prc_contato);
    $stmt->bindParam(":prc_email", $prc_email);
    $stmt->bindParam(":prc_user_id", $reservm_user_id);
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute(array(
      ':modulo'     => 'PROPOSTA - EXT COMUNITÁRIA - RESPONSÁVEL',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $prc_id,
      ':dados'      => 'ID Proposta: ' . $prc_proposta_id .
        '; Nome: ' . $prc_nome .
        '; Contato: ' . $prc_contato .
        '; E-mail: ' . $prc_email,
      ':user_id'    => $reservm_user_id
    ));
    // -------------------------------

    $_SESSION["msg"] = "Cadastro realizado com sucesso!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#prc_ancora");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "Cadastro não realizado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
  }
}











/*****************************************************************************************
                              EDITAR RESPONSÁVEL CONTATO
 *****************************************************************************************/
if (isset($dados['EditResponsavelContato'])) {

  $prc_id          = base64_decode($_POST['prc_id']);
  $prc_proposta_id = base64_decode($_POST['prc_proposta_id']);
  $prc_nome        = trim(isset($_POST['prc_nome'])) ? $_POST['prc_nome'] : NULL;
  $prc_email       = trim(isset($_POST['prc_email'])) ? $_POST['prc_email'] : NULL;
  $prc_contato     = trim(isset($_POST['prc_contato'])) ? $_POST['prc_contato'] : NULL;
  $reservm_user_id    = $_SESSION['reservm_user_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_extensao_responsavel_contato WHERE prc_nome = :prc_nome AND prc_proposta_id = :prc_proposta_id AND prc_id != :prc_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":prc_id", $prc_id);
  $stmt->bindParam(":prc_nome", $prc_nome);
  $stmt->bindParam(":prc_proposta_id", $prc_proposta_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este responsável já foi cadastrado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_extensao_responsavel_contato WHERE prc_email = :prc_email AND prc_proposta_id = :prc_proposta_id AND prc_id != :prc_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":prc_id", $prc_id);
  $stmt->bindParam(":prc_email", $prc_email);
  $stmt->bindParam(":prc_proposta_id", $prc_proposta_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este responsável já foi cadastrado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $sql = "UPDATE
                  propostas_extensao_responsavel_contato
            SET
                  prc_nome     = UPPER(:prc_nome),
                  prc_email    = :prc_email,
                  prc_contato  = :prc_contato,
                  prc_data_upd = GETDATE()
            WHERE
                  prc_id = :prc_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":prc_id", $prc_id);
    $stmt->bindParam(":prc_nome", $prc_nome);
    $stmt->bindParam(":prc_contato", $prc_contato);
    $stmt->bindParam(":prc_email", $prc_email);
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute(array(
      ':modulo'     => 'PROPOSTA - EXT COMUNITÁRIA - RESPONSÁVEL',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $prc_id,
      ':dados'      => 'ID Proposta: ' . $prc_proposta_id . '; Nome: ' . $prc_nome . '; Contato: ' . $prc_contato . '; E-mail: ' . $prc_email,
      ':user_id'    => $reservm_user_id
    ));
    // -------------------------------

    $_SESSION["msg"] = "Dados atualizados com sucesso!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#prc_ancora");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "Dados não atualizados!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
  }
}













/*****************************************************************************************
                              EXCLUIR RESPONSÁVEL CONTATO
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_prc") {

  $prc_id       = base64_decode($_GET['prc_id']);
  $reservm_user_id = $_SESSION['reservm_user_id'];

  try {
    $sql = "DELETE FROM propostas_extensao_responsavel_contato WHERE prc_id = :prc_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':prc_id', $prc_id);
    $stmt->execute();

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {
      $_SESSION["msg"] = "Dados excluídos!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#prc_ancora");

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                              VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt->execute(array(
        ':modulo'    => 'PROPOSTA - EXT COMUNITÁRIA - RESPONSÁVEL',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $prc_id,
        ':user_id'   => $reservm_user_id
      ));
      // -------------------------------

    } else {
      $_SESSION["erro"] = "Dados não excluídos!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#top_ancora");
    }
  } catch (PDOException $e) {
    //echo "Erro: " . $e->getMessage();
    $_SESSION["erro"] = "Dados não excluídos!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
  }

  $conn = null;
}
