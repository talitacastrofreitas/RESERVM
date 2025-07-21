<?php
session_start();
include '../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                            CADASTRAR TIPO DE PROGRAMA
 *****************************************************************************************/
if (isset($dados['CadTipoPrograma'])) {

  $ctp_tipo      = trim($_POST['ctp_tipo']);
  $ctp_categoria = trim($_POST['ctp_categoria']);
  $ctp_status    = isset($_POST['ctp_status']) ? $_POST['ctp_status'] : 2;
  $data_real     = date('Y-m-d H:i:s');
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_tipo_programa WHERE ctp_tipo = :ctp_tipo AND ctp_categoria = :ctp_categoria";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":ctp_tipo", $ctp_tipo);
  $stmt->bindParam(":ctp_categoria", $ctp_categoria);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este dado já foi cadastrado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $sql = "INSERT INTO conf_tipo_programa (
                                              ctp_tipo,
                                              ctp_categoria,
                                              ctp_status,
                                              ctp_user_id,
                                              ctp_data_cad,
                                              ctp_data_upd
                                            ) VALUES (
                                              UPPER(:ctp_tipo),
                                              UPPER(:ctp_categoria),
                                              :ctp_status,
                                              :ctp_user_id,
                                              :ctp_data_cad,
                                              :ctp_data_upd
                                            )";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":ctp_tipo", $ctp_tipo);
    $stmt->bindParam(":ctp_categoria", $ctp_categoria);
    $stmt->bindParam(":ctp_status", $ctp_status);
    //
    $stmt->bindParam(":ctp_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":ctp_data_cad", $data_real);
    $stmt->bindParam(":ctp_data_upd", $data_real);
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'TIPO DE PROGRAMA',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $last_id,
      ':dados'      => 'Tipo: ' . $ctp_tipo . '; Categoria: ' . $ctp_categoria . '; Status: ' . $ctp_status,
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
                                EDITAR TIPO DE PROGRAMA
 *****************************************************************************************/
if (isset($dados['EditTipoPrograma'])) {

  $ctp_id        = trim($_POST['ctp_id']);
  $ctp_tipo      = trim($_POST['ctp_tipo']);
  $ctp_categoria = trim($_POST['ctp_categoria']);
  $ctp_status    = isset($_POST['ctp_status']) ? $_POST['ctp_status'] : 2;
  $data_real     = date('Y-m-d H:i:s');
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_tipo_programa WHERE ctp_tipo = :ctp_tipo AND ctp_categoria = :ctp_categoria AND ctp_id != :ctp_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":ctp_id", $ctp_id);
  $stmt->bindParam(":ctp_tipo", $ctp_tipo);
  $stmt->bindParam(":ctp_categoria", $ctp_categoria);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este dado já foi cadastrado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $sql = "UPDATE
                    conf_tipo_programa
              SET
                    ctp_tipo      = UPPER(:ctp_tipo),
                    ctp_categoria = UPPER(:ctp_categoria),
                    ctp_status    = :ctp_status,
                    ctp_user_id   = :ctp_user_id,
                    ctp_data_upd  = :ctp_data_upd
              WHERE
                    ctp_id        = :ctp_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":ctp_id", $ctp_id);
    //
    $stmt->bindParam(":ctp_tipo", $ctp_tipo);
    $stmt->bindParam(":ctp_categoria", $ctp_categoria);
    $stmt->bindParam(":ctp_status", $ctp_status);
    //
    $stmt->bindParam(":ctp_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":ctp_data_upd", $data_real);
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'TIPO DE PROGRAMA',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $ctp_id,
      ':dados'      => 'Tipo: ' . $ctp_tipo . '; Categoria: ' . $ctp_categoria . '; Status: ' . $ctp_status,
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
                        EXCLUIR TIPO DE PROGRAMA
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_ctp") {

  $ctp_id    = $_GET['ctp_id'];
  $data_real = date('Y-m-d H:i:s');

  // SE DADO ESTIVER SENDO USADO EM ALGUM CADASTRO, NÃO PODE SER EXLUÍDO
  $sql = "SELECT COUNT(*) FROM propostas WHERE prop_prog_categoria = :prop_prog_categoria";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":prop_prog_categoria", $ctp_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este registro não pode ser excluído!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $sql = "DELETE FROM conf_tipo_programa WHERE ctp_id = :ctp_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':ctp_id', $ctp_id);
    $stmt->execute();

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {
      $_SESSION["msg"] = "Dados excluídos!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :user_id, :user_nome, :data )');
      $stmt->execute(array(
        ':modulo'    => 'TIPO DE PROGRAMA',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $ctp_id,
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
