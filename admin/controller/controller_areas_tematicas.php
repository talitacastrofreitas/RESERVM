<?php
session_start();
include '../../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// if ($_SERVER["REQUEST_METHOD"] === "POST") {}

/*****************************************************************************************
                            CADASTRAR ÁREA TEMÁTICA
 *****************************************************************************************/
if (isset($dados['CadAreaTematica'])) {

  $at_area_tematica = trim($_POST['at_area_tematica']) !== '' ? trim($_POST['at_area_tematica']) : NULL;
  $at_status        = trim(isset($_POST['at_status'])) ? $_POST['at_status'] : 0;
  $reservm_admin_id    = $_SESSION['reservm_admin_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_areas_tematicas WHERE at_area_tematica = :at_area_tematica";
  $stmt = $conn->prepare($sql);
  $stmt->execute([':at_area_tematica' => $at_area_tematica]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Esta área temática já foi cadastra!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "INSERT INTO conf_areas_tematicas (
                                                at_area_tematica,
                                                at_status,
                                                at_user_id,
                                                at_data_cad,
                                                at_data_upd
                                              ) VALUES (
                                                UPPER(:at_area_tematica),
                                                :at_status,
                                                :at_user_id,
                                                GETDATE(),
                                                GETDATE()
                                              )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':at_area_tematica' => $at_area_tematica,
      ':at_status' => $at_status,
      ':at_user_id' => $reservm_admin_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'ÁREA TEMÁTICA',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $last_id,
      ':dados'      => 'Área Conhecimento: ' . $at_area_tematica . '; Status: ' . $at_status,
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
                              EDITAR ÁREA TEMÁTICA
 *****************************************************************************************/
if (isset($dados['EditarAreaTematica'])) {

  $at_id            = trim($_POST['at_id']) !== '' ? trim($_POST['at_id']) : NULL;
  $at_area_tematica = trim($_POST['at_area_tematica']) !== '' ? trim($_POST['at_area_tematica']) : NULL;
  $at_status        = trim(isset($_POST['at_status'])) ? $_POST['at_status'] : 0;
  $reservm_admin_id    = $_SESSION['reservm_admin_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_areas_tematicas WHERE at_area_tematica = :at_area_tematica AND at_id != :at_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':at_id'            => $at_id,
    ':at_area_tematica' => $at_area_tematica
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Esta área temática já foi cadastra!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "UPDATE
                    conf_areas_tematicas
              SET
                    at_area_tematica = UPPER(:at_area_tematica),
                    at_status        = :at_status,
                    at_user_id       = :at_user_id,
                    at_data_upd      = GETDATE()
              WHERE
                    at_id = :at_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':at_id' => $at_id,
      ':at_area_tematica' => $at_area_tematica,
      ':at_status' => $at_status,
      ':at_user_id' => $reservm_admin_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'ÁREA TEMÁTICA',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $at_id,
      ':dados'      => 'Área Conhecimento: ' . $at_area_tematica . '; Status: ' . $at_status,
      ':user_id'    =>  $reservm_admin_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Dados atualizados com sucesso!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Atualização não realizada!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
}











/*****************************************************************************************
                              EXCLUIR ÁREA TEMÁTICA
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_areaTema") {

  $at_id         = $_GET['at_id'];
  $reservm_admin_id = $_SESSION['reservm_admin_id'];

  // SE DADO ESTIVER SENDO USADO EM ALGUM CADASTRO, NÃO PODE SER EXCLUÍDO
  $sql = "SELECT * FROM propostas WHERE CHARINDEX(:prop_area_tematica, prop_area_tematica) > 0"; // VERIFICA SE ID ESTÁ DENTRO DO ARRAY
  $stmt = $conn->prepare($sql);
  $stmt->execute([':prop_area_tematica' => $at_id]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este registro não pode ser excluído!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "DELETE FROM conf_areas_tematicas WHERE at_id = :at_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':at_id' => $at_id]);

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {

      // REGISTRA AÇÃO NO LOG
      $stmt_log = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                                  VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt_log->execute([
        ':modulo'  => 'ÁREA TEMÁTICA',
        ':acao'    => 'EXCLUSÃO',
        ':acao_id' => $at_id,
        ':user_id' => $reservm_admin_id
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
