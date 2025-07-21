<?php
session_start();
include '../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// if ($_SERVER["REQUEST_METHOD"] === "POST") {}

/*****************************************************************************************
                            CADASTRAR TIPO EVENTO SOCIAL
 *****************************************************************************************/
if (isset($dados['CadTipoEventoSocial'])) {

  $tes_evento_social = trim($_POST['tes_evento_social']);
  $tes_status        = isset($_POST['tes_status']) ? $_POST['tes_status'] : 2;
  $data_real         = date('Y-m-d H:i:s');
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_tipo_evento_social WHERE tes_evento_social = :tes_evento_social";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":tes_evento_social", $tes_evento_social);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este tipo de evento social já foi cadastrado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $sql = "INSERT INTO conf_tipo_evento_social (
                                                  tes_evento_social,
                                                  tes_status,
                                                  tes_user_id,
                                                  tes_data_cad,
                                                  tes_data_upd
                                                ) VALUES (
                                                  UPPER(:tes_evento_social),
                                                  :tes_status,
                                                  :tes_user_id,
                                                  :tes_data_cad,
                                                  :tes_data_upd
                                                )";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":tes_evento_social", $tes_evento_social);
    $stmt->bindParam(":tes_status", $tes_status);
    //
    $stmt->bindParam(":tes_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":tes_data_cad", $data_real);
    $stmt->bindParam(":tes_data_upd", $data_real);
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'PROPOSTA - TIPO EVENTO SOCIAL',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $last_id,
      ':dados'      => 'Tipo de Evento Social: ' . $tes_evento_social . '; Status: ' . $tes_status,
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
                              EDITAR TIPO EVENTO SOCIAL
 *****************************************************************************************/
if (isset($dados['EditTipoEventoSocial'])) {

  $tes_id            = trim($_POST['tes_id']);
  $tes_evento_social = trim($_POST['tes_evento_social']);
  $tes_status        = isset($_POST['tes_status']) ? $_POST['tes_status'] : 2;
  $data_real         = date('Y-m-d H:i:s');
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_tipo_evento_social WHERE tes_evento_social = :tes_evento_social AND tes_id != :tes_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":tes_id", $tes_id);
  $stmt->bindParam(":tes_evento_social", $tes_evento_social);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este tipo de evento social já foi cadastrado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $sql = "UPDATE
                    conf_tipo_evento_social
              SET
                    tes_evento_social = UPPER(:tes_evento_social),
                    tes_status        = :tes_status,
                    tes_user_id       = :tes_user_id,
                    tes_data_upd      = :tes_data_upd
              WHERE
                    tes_id = :tes_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":tes_id", $tes_id);
    //
    $stmt->bindParam(":tes_evento_social", $tes_evento_social);
    $stmt->bindParam(":tes_status", $tes_status);
    //
    $stmt->bindParam(":tes_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":tes_data_upd", $data_real);
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'PROPOSTA - TIPO EVENTO SOCIAL',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $tes_id,
      ':dados'      => 'Tipo de Evento Social: ' . $tes_evento_social . '; Status: ' . $tes_status,
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
                              EXCLUIR TIPO EVENTO SOCIAL
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_tes") {

  $tes_id    = $_GET['tes_id'];
  $data_real = date('Y-m-d H:i:s');

  // SE DADO ESTIVER SENDO USADO EM ALGUM CADASTRO, NÃO PODE SER EXLUÍDO
  $sql = "SELECT COUNT(*) FROM propostas WHERE prop_ext_categoria_evento = :prop_ext_categoria_evento";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":prop_ext_categoria_evento", $tes_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este registro não pode ser excluído!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $sql = "DELETE FROM conf_tipo_evento_social WHERE tes_id = :tes_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':tes_id', $tes_id);
    $stmt->execute();

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {
      $_SESSION["msg"] = "Dados excluídos!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :user_id, :user_nome, :data )');
      $stmt->execute(array(
        ':modulo'    => 'PROPOSTA - TIPO EVENTO SOCIAL',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $tes_id,
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
