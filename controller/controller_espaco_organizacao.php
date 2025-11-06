<?php
session_start();
include '../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// if ($_SERVER["REQUEST_METHOD"] === "POST") {}

/*****************************************************************************************
                            CADASTRAR ORGANIZAÇÃO DO ESPAÇO
 *****************************************************************************************/
if (isset($dados['CadEspacoOrganizacao'])) {

  $esporg_espaco_organizacao = trim($_POST['esporg_espaco_organizacao']);
  $esporg_status             = isset($_POST['esporg_status']) ? $_POST['esporg_status'] : 2;
  $data_real                 = date('Y-m-d H:i:s');
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_tipo_espaco_organizacao WHERE esporg_espaco_organizacao = :esporg_espaco_organizacao";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":esporg_espaco_organizacao", $esporg_espaco_organizacao);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este tipo de espaço já foi cadastrado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $sql = "INSERT INTO conf_tipo_espaco_organizacao (
                                                        esporg_espaco_organizacao,
                                                        esporg_status,
                                                        esporg_user_id,
                                                        esporg_data_cad,
                                                        esporg_data_upd
                                                      ) VALUES (
                                                        UPPER(:esporg_espaco_organizacao),
                                                        :esporg_status,
                                                        :esporg_user_id,
                                                        :esporg_data_cad,
                                                        :esporg_data_upd
                                                      )";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":esporg_espaco_organizacao", $esporg_espaco_organizacao);
    $stmt->bindParam(":esporg_status", $esporg_status);
    //
    $stmt->bindParam(":esporg_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":esporg_data_cad", $data_real);
    $stmt->bindParam(":esporg_data_upd", $data_real);
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'ORGANIZAÇÃO DO ESPAÇO',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $last_id,
      ':dados'      => 'Organização do Espaço: ' . $esporg_espaco_organizacao . '; Status: ' . $esporg_status,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => $data_real
    ));
    // -------------------------------

    $_SESSION["msg"] = "Cadastro realizado com sucesso!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "Cadastro não realizado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
}









/*****************************************************************************************
                              EDITAR ORGANIZAÇÃO DO ESPAÇO
 *****************************************************************************************/
if (isset($dados['EditEspacoOrganizacao'])) {

  $esporg_id                 = trim($_POST['esporg_id']);
  $esporg_espaco_organizacao = trim($_POST['esporg_espaco_organizacao']);
  $esporg_status             = isset($_POST['esporg_status']) ? $_POST['esporg_status'] : 2;
  $data_real                 = date('Y-m-d H:i:s');
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_tipo_espaco_organizacao WHERE esporg_espaco_organizacao = :esporg_espaco_organizacao AND esporg_id != :esporg_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":esporg_id", $esporg_id);
  $stmt->bindParam(":esporg_espaco_organizacao", $esporg_espaco_organizacao);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este tipo de espaço já foi cadastrado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $sql = "UPDATE
                    conf_tipo_espaco_organizacao
              SET
                    esporg_espaco_organizacao = UPPER(:esporg_espaco_organizacao),
                    esporg_status             = :esporg_status,
                    esporg_user_id            = :esporg_user_id,
                    esporg_data_upd           = :esporg_data_upd
              WHERE
                    esporg_id = :esporg_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":esporg_id", $esporg_id);
    //
    $stmt->bindParam(":esporg_espaco_organizacao", $esporg_espaco_organizacao);
    $stmt->bindParam(":esporg_status", $esporg_status);
    //
    $stmt->bindParam(":esporg_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":esporg_data_upd", $data_real);
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'ORGANIZAÇÃO DO ESPAÇO',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $esporg_id,
      ':dados'      => 'Organização do Espaço: ' . $esporg_espaco_organizacao . '; Status: ' . $esporg_status,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => $data_real
    ));
    // -------------------------------

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
                              EXCLUIR ORGANIZAÇÃO DO ESPAÇO
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_esporg") {

  $esporg_id = $_GET['esporg_id'];
  $data_real = date('Y-m-d H:i:s');

  // SE DADO ESTIVER SENDO USADO EM ALGUM CADASTRO, NÃO PODE SER EXLUÍDO
  $sql = "SELECT COUNT(*) FROM propostas_cursos_modulo WHERE prop_cmod_organizacao = :prop_cmod_organizacao";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":prop_cmod_organizacao", $esporg_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este registro não pode ser excluído!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $sql = "DELETE FROM conf_tipo_espaco_organizacao WHERE esporg_id = :esporg_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':esporg_id', $esporg_id);
    $stmt->execute();

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {
      $_SESSION["msg"] = "Dados excluídos!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :user_id, :user_nome, :data )');
      $stmt->execute(array(
        ':modulo'    => 'ORGANIZAÇÃO DO ESPAÇO',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $esporg_id,
        ':user_id'   => $_SESSION['reservm_admin_id'],
        ':user_nome' => $_SESSION['reservm_admin_nome'],
        ':data'      => $data_real
      ));
      // -------------------------------

    } else {
      $_SESSION["erro"] = "Dados não excluídos!";
      echo "<script> history.go(-1);</script>";
      return die;
    }
  } catch (PDOException $e) {
    //echo "Erro: " . $e->getMessage();
    $_SESSION["erro"] = "Dados não excluídos!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  $conn = null;
}
