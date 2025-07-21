<?php
session_start();
include '../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                      CADASTRAR PROGRAMA DE EXTENSÃO COMUNITÁRIA
 *****************************************************************************************/
if (isset($dados['CadExtensaoComunitaria'])) {

  $cec_extensao_comunitaria = trim($_POST['cec_extensao_comunitaria']);
  $cec_desc                 = trim($_POST['cec_desc']);
  $cec_status               = isset($_POST['cec_status']) ? $_POST['cec_status'] : 2;
  $data_real                = date('Y-m-d H:i:s');

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_extensao_comunitaria WHERE cec_extensao_comunitaria = :cec_extensao_comunitaria";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":cec_extensao_comunitaria", $cec_extensao_comunitaria);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "O programa informada já foi cadastra!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $sql = "INSERT INTO conf_extensao_comunitaria (
                                                    cec_extensao_comunitaria,
                                                    cec_desc,
                                                    cec_status,
                                                    cec_user_id,
                                                    cec_data_cad,
                                                    cec_data_upd
                                                  ) VALUES (
                                                    UPPER(:cec_extensao_comunitaria),
                                                    :cec_desc,
                                                    :cec_status,
                                                    :cec_user_id,
                                                    :cec_data_cad,
                                                    :cec_data_upd
                                                  )";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":cec_extensao_comunitaria", $cec_extensao_comunitaria);
    $stmt->bindParam(":cec_desc", $cec_desc);
    $stmt->bindParam(":cec_status", $cec_status);
    //
    $stmt->bindParam(":cec_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":cec_data_cad", $data_real);
    $stmt->bindParam(":cec_data_upd", $data_real);
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'PROPOSTA - EXTENSÃO COMUNITÁRIA',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $last_id,
      ':dados'      => 'Programa: ' . $cec_extensao_comunitaria . 'Descrição: ' . $cec_desc . 'Status: ' . $cec_status,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => $data_real
    ));

    $_SESSION["msg"] = "Cadastro realizado com sucesso!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "Cadastro não realizado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}






/*****************************************************************************************
                          EDITAR PROGRAMA DE EXTENSÃO COMUNITÁRIA
 *****************************************************************************************/
if (isset($dados['EditExtensaoComunitaria'])) {

  $cec_id                   = trim($_POST['cec_id']);
  $cec_extensao_comunitaria = trim($_POST['cec_extensao_comunitaria']);
  $cec_desc                 = trim($_POST['cec_desc']);
  $cec_status               = isset($_POST['cec_status']) ? $_POST['cec_status'] : 2;
  $data_real                = date('Y-m-d H:i:s');

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_extensao_comunitaria WHERE cec_extensao_comunitaria = :cec_extensao_comunitaria AND cec_id != :cec_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":cec_id", $cec_id);
  $stmt->bindParam(":cec_extensao_comunitaria", $cec_extensao_comunitaria);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "O programa informada já foi cadastra!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $sql = "UPDATE
                    conf_extensao_comunitaria
              SET
                    cec_extensao_comunitaria = UPPER(:cec_extensao_comunitaria),
                    cec_desc                 = :cec_desc,
                    cec_status               = :cec_status,
                    cec_user_id              = :cec_user_id,
                    cec_data_upd             = :cec_data_upd
              WHERE
                    cec_id = :cec_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":cec_id", $cec_id);
    //
    $stmt->bindParam(":cec_extensao_comunitaria", $cec_extensao_comunitaria);
    $stmt->bindParam(":cec_desc", $cec_desc);
    $stmt->bindParam(":cec_status", $cec_status);
    //
    $stmt->bindParam(":cec_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":cec_data_upd", $data_real);
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'PROPOSTA - EXTENSÃO COMUNITÁRIA',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $cec_id,
      ':dados'      => 'Programa: ' . $cec_extensao_comunitaria . 'Descrição: ' . $cec_desc . 'Status: ' . $cec_status,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => $data_real
    ));

    $_SESSION["msg"] = "Dados atualizados com sucesso!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "Dados não atualizados!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
}








/*****************************************************************************************
                        EXCLUIR PROGRAMA DE EXTENSÃO COMUNITÁRIA
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_cec") {

  $cec_id    = $_GET['cec_id'];
  $data_real = date('Y-m-d H:i:s');

  try {
    $sql = "DELETE FROM conf_extensao_comunitaria WHERE cec_id = '$cec_id'";
    $conn->exec($sql);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :user_id, :user_nome, :data )');
    $stmt->execute(array(
      ':modulo'    => 'PROPOSTA - EXTENSÃO COMUNITÁRIA',
      ':acao'      => 'EXCLUSÃO',
      ':acao_id'   => $cec_id,
      ':user_id'   => $_SESSION['reservm_admin_id'],
      ':user_nome' => $_SESSION['reservm_admin_nome'],
      ':data'      => $data_real
    ));
  } catch (PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
  }
  $conn = null;

  $_SESSION["msg"] = "Dados excluídos!";
  header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
}
