<?php
session_start();
include '../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                          CADASTRAR RESPONSÁVEL CONTATO
 *****************************************************************************************/
if (isset($dados['CadResponsavelContato'])) {

  $prc_id          = md5(uniqid(rand(), true)); // GERA UM ID UNICO
  $prc_proposta_id = $_POST['prc_proposta_id'];
  $prc_nome        = trim($_POST['prc_nome']);
  $prc_contato     = trim($_POST['prc_contato']);
  $prc_email       = trim($_POST['prc_email']);
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_extensao_responsavel_contato WHERE prc_nome = :prc_nome AND prc_proposta_id = :prc_proposta_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":prc_nome", $prc_nome);
  $stmt->bindParam(":prc_proposta_id", $prc_proposta_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este responsável já foi cadastrado!";
    echo "<script> history.go(-1);</script>";
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
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este responsável já foi cadastrado!";
    echo "<script> history.go(-1);</script>";
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
                                                                :prc_data_cad,
                                                                :prc_data_upd
                                                              )";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":prc_id", $prc_id);
    $stmt->bindParam(":prc_proposta_id", $prc_proposta_id);
    //
    $stmt->bindParam(":prc_nome", $prc_nome);
    $stmt->bindParam(":prc_contato", $prc_contato);
    $stmt->bindParam(":prc_email", $prc_email);
    //
    $stmt->bindParam(":prc_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":prc_data_cad", date('Y-m-d H:i:s'));
    $stmt->bindParam(":prc_data_upd", date('Y-m-d H:i:s'));
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'PROPOSTA - RESPONSÁVEL CONTATO',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $prc_id,
      ':dados'      => 'ID Proposta: ' . $prc_proposta_id . '; Nome: ' . $prc_nome . '; Contato: ' . $prc_contato . '; E-mail: ' . $prc_email,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => date('Y-m-d H:i:s')
    ));
    // -------------------------------

    $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Cadastro realizado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Cadastro não realizado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}











/*****************************************************************************************
                              EDITAR RESPONSÁVEL CONTATO
 *****************************************************************************************/
if (isset($dados['EditResponsavelContato'])) {

  $prc_id          = $_POST['prc_id'];
  $prc_proposta_id = $_POST['prc_proposta_id'];
  $prc_nome        = trim($_POST['prc_nome']);
  $prc_contato     = trim($_POST['prc_contato']);
  $prc_email       = trim($_POST['prc_email']);
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_extensao_responsavel_contato WHERE prc_nome = :prc_nome AND prc_proposta_id = :prc_proposta_id AND prc_id != :prc_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":prc_id", $prc_id);
  $stmt->bindParam(":prc_nome", $prc_nome);
  $stmt->bindParam(":prc_proposta_id", $prc_proposta_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este responsável já foi cadastrado!";
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
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este responsável já foi cadastrado!";
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
                  prc_data_upd = :prc_data_upd
            WHERE
                  prc_id = :prc_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":prc_id", $prc_id);
    $stmt->bindParam(":prc_nome", $prc_nome);
    $stmt->bindParam(":prc_contato", $prc_contato);
    $stmt->bindParam(":prc_email", $prc_email);
    $stmt->bindParam(":prc_data_upd", date('Y-m-d H:i:s'));
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'PROPOSTA - RESPONSÁVEL CONTATO',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $prc_id,
      ':dados'      => 'ID Proposta: ' . $prc_proposta_id . '; Nome: ' . $prc_nome . '; Contato: ' . $prc_contato . '; E-mail: ' . $prc_email,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => date('Y-m-d H:i:s')
    ));
    // -------------------------------

    $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados atualizados!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#prc_ancora");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados não atualizados!" . $e->getMessage();
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}













/*****************************************************************************************
                              EXCLUIR RESPONSÁVEL CONTATO
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_prc") {

  $prc_id = $_GET['prc_id'];

  try {
    $sql = "DELETE FROM propostas_extensao_responsavel_contato WHERE prc_id = '$prc_id'";
    $conn->exec($sql);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :user_id, :user_nome, :data )');
    $stmt->execute(array(
      ':modulo'    => 'PROPOSTA - RESPONSÁVEL CONTATO',
      ':acao'      => 'EXCLUSÃO',
      ':acao_id'   => $prc_id,
      ':user_id'   => $_SESSION['reservm_admin_id'],
      ':user_nome' => $_SESSION['reservm_admin_nome'],
      ':data'      => date('Y-m-d H:i:s')
    ));
    // -------------------------------

  } catch (PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
  }
  $conn = null;

  $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i>Dados excluídos!";
  $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
  header("Location: $referer#prc_ancora");
}
