<?php
session_start();
include '../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// if ($_SERVER["REQUEST_METHOD"] === "POST") {}

/*****************************************************************************************
                                      CADASTRAR RECURSO
 *****************************************************************************************/
if (isset($dados['CadRecurso'])) {

  $rec_recurso   = trim($_POST['rec_recurso']);
  $rec_categoria = trim($_POST['rec_categoria']);
  $rec_status    = isset($_POST['rec_status']) ? $_POST['rec_status'] : 2;
  $data_real     = date('Y-m-d H:i:s');
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_recursos WHERE rec_recurso = :rec_recurso AND rec_categoria = :rec_categoria";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":rec_recurso", $rec_recurso);
  $stmt->bindParam(":rec_categoria", $rec_categoria);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este recurso já foi cadastro para esta categoria!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $sql = "INSERT INTO conf_recursos (
                                        rec_recurso,
                                        rec_categoria,
                                        rec_status,
                                        rec_user_id,
                                        rec_data_cad,
                                        rec_data_upd
                                      ) VALUES (
                                        UPPER(:rec_recurso),
                                        :rec_categoria,
                                        :rec_status,
                                        :rec_user_id,
                                        :rec_data_cad,
                                        :rec_data_upd
                                      )";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":rec_recurso", $rec_recurso);
    $stmt->bindParam(":rec_categoria", $rec_categoria);
    $stmt->bindParam(":rec_status", $rec_status);
    //
    $stmt->bindParam(":rec_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":rec_data_cad", $data_real);
    $stmt->bindParam(":rec_data_upd", $data_real);
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'RECURSOS',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $last_id,
      ':dados'      => 'Recurso: ' . $rec_recurso . '; Categoria: ' . $rec_categoria . '; Status: ' . $rec_status,
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
                              EDITAR RECURSO
 *****************************************************************************************/
if (isset($dados['EditRecurso'])) {

  $rec_id        = trim($_POST['rec_id']);
  $rec_recurso   = trim($_POST['rec_recurso']);
  $rec_categoria = trim($_POST['rec_categoria']);
  $rec_status    = isset($_POST['rec_status']) ? $_POST['rec_status'] : 2;
  $data_real     = date('Y-m-d H:i:s');
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_recursos WHERE rec_recurso = :rec_recurso AND rec_categoria = :rec_categoria AND rec_id != :rec_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":rec_id", $rec_id);
  $stmt->bindParam(":rec_recurso", $rec_recurso);
  $stmt->bindParam(":rec_categoria", $rec_categoria);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este recurso já foi cadastro para esta categoria!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $sql = "UPDATE
                    conf_recursos
              SET
                    rec_recurso   = UPPER(:rec_recurso),
                    rec_categoria = :rec_categoria,
                    rec_status    = :rec_status,
                    rec_user_id   = :rec_user_id,
                    rec_data_upd  = :rec_data_upd
              WHERE
                    rec_id = :rec_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":rec_id", $rec_id);
    //
    $stmt->bindParam(":rec_recurso", $rec_recurso);
    $stmt->bindParam(":rec_categoria", $rec_categoria);
    $stmt->bindParam(":rec_status", $rec_status);
    //
    $stmt->bindParam(":rec_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":rec_data_upd", $data_real);
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'RECURSOS',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $rec_id,
      ':dados'      => 'Recurso: ' . $rec_recurso . '; Categoria: ' . $rec_categoria . '; Status: ' . $rec_status,
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
                              EXCLUIR RECURSO
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_rec") {

  $rec_id    = $_GET['rec_id'];
  $data_real = date('Y-m-d H:i:s');

  // SE DADO ESTIVER SENDO USADO EM ALGUM CADASTRO, NÃO PODE SER EXLUÍDO
  $sql = "SELECT * FROM propostas WHERE CHARINDEX(:prop_recursos, prop_recursos) > 0"; // VERIFICA SE ID ESTÁ DENTRO DO ARRAY
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":prop_recursos", $rec_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este registro não pode ser excluído!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $sql = "DELETE FROM conf_recursos WHERE rec_id = :rec_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':rec_id', $rec_id);
    $stmt->execute();

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {
      $_SESSION["msg"] = "Dados excluídos!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :user_id, :user_nome, :data )');
      $stmt->execute(array(
        ':modulo'    => 'RECURSOS',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $rec_id,
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
