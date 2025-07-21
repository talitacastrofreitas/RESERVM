<?php
session_start();
include '../../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// if ($_SERVER["REQUEST_METHOD"] === "POST") {}

/*****************************************************************************************
                                      CADASTRAR RECURSO
 *****************************************************************************************/
if (isset($dados['CadRecurso'])) {

  $rec_recurso   = trim($_POST['rec_recurso']) !== '' ? trim($_POST['rec_recurso']) : NULL;
  $rec_categoria = trim($_POST['rec_categoria']) !== '' ? trim($_POST['rec_categoria']) : NULL;
  $rec_status    = trim(isset($_POST['rec_status'])) ? $_POST['rec_status'] : 0;
  $reservm_admin_id = $_SESSION['reservm_admin_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_recursos WHERE rec_recurso = :rec_recurso AND rec_categoria = :rec_categoria";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':rec_recurso'   => $rec_recurso,
    ':rec_categoria' => $rec_categoria
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este recurso já foi cadastro para esta categoria!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
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
                                        GETDATE(),
                                        GETDATE()
                                      )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':rec_recurso'   => $rec_recurso,
      ':rec_categoria' => $rec_categoria,
      ':rec_status'    => $rec_status,
      ':rec_user_id'   => $reservm_admin_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'RECURSOS',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $last_id,
      ':dados'      => 'Recurso: ' . $rec_recurso . '; Categoria: ' . $rec_categoria . '; Status: ' . $rec_status,
      ':user_id'    => $reservm_admin_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Cadastro realizado com sucesso!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Cadastro não realizado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
}









/*****************************************************************************************
                              EDITAR RECURSO
 *****************************************************************************************/
if (isset($dados['EditRecurso'])) {

  $rec_id        = trim($_POST['rec_id']) !== '' ? trim($_POST['rec_id']) : NULL;
  $rec_recurso   = trim($_POST['rec_recurso']) !== '' ? trim($_POST['rec_recurso']) : NULL;
  $rec_categoria = trim($_POST['rec_categoria']) !== '' ? trim($_POST['rec_categoria']) : NULL;
  $rec_status    = trim(isset($_POST['rec_status'])) ? $_POST['rec_status'] : 0;
  $reservm_admin_id = $_SESSION['reservm_admin_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_recursos WHERE rec_recurso = :rec_recurso AND rec_categoria = :rec_categoria AND rec_id != :rec_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':rec_id'        => $rec_id,
    ':rec_recurso'   => $rec_recurso,
    ':rec_categoria' => $rec_categoria
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este recurso já foi cadastro para esta categoria!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "UPDATE
                    conf_recursos
              SET
                    rec_recurso   = UPPER(:rec_recurso),
                    rec_categoria = :rec_categoria,
                    rec_status    = :rec_status,
                    rec_user_id   = :rec_user_id,
                    rec_data_upd  = GETDATE()
              WHERE
                    rec_id = :rec_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':rec_id'        => $rec_id,
      ':rec_recurso'   => $rec_recurso,
      ':rec_categoria' => $rec_categoria,
      ':rec_status'    => $rec_status,
      ':rec_user_id'   => $reservm_admin_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'RECURSOS',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $rec_id,
      ':dados'      => 'Recurso: ' . $rec_recurso . '; Categoria: ' . $rec_categoria . '; Status: ' . $rec_status,
      ':user_id'    => $reservm_admin_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Dados atualizados com sucesso!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Dados não atualizados!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
}








/*****************************************************************************************
                              EXCLUIR RECURSO
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_rec") {

  $rec_id        = $_GET['rec_id'];
  $reservm_admin_id = $_SESSION['reservm_admin_id'];

  // SE DADO ESTIVER SENDO USADO EM ALGUM CADASTRO, NÃO PODE SER EXCLUÍDO
  $sql = "SELECT * FROM propostas WHERE CHARINDEX(:prop_recursos, prop_recursos) > 0"; // VERIFICA SE ID ESTÁ DENTRO DO ARRAY
  $stmt = $conn->prepare($sql);
  $stmt->execute([':prop_recursos' => $rec_id]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este registro não pode ser excluído!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "DELETE FROM conf_recursos WHERE rec_id = :rec_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':rec_id' => $rec_id]);

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {

      // REGISTRA AÇÃO NO LOG
      $stmt_log = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                                  VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt_log->execute([
        ':modulo'  => 'RECURSOS',
        ':acao'    => 'EXCLUSÃO',
        ':acao_id' => $rec_id,
        ':user_id' => $reservm_admin_id
      ]);
      // -------------------------------

      $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

      $_SESSION["msg"] = "Dados excluídos!";
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
