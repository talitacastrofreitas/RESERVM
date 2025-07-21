<?php
session_start();
include '../../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// if ($_SERVER["REQUEST_METHOD"] === "POST") {}

/*****************************************************************************************
                            CADASTRAR ÁREA DO CONHECIMENTO
 *****************************************************************************************/
if (isset($dados['CadAreaConhecimento'])) {

  $ac_area_conhecimento = trim($_POST['ac_area_conhecimento']) !== '' ? trim($_POST['ac_area_conhecimento']) : NULL;
  $ac_status            = trim(isset($_POST['ac_status'])) ? $_POST['ac_status'] : 0;
  $reservm_admin_id        = $_SESSION['reservm_admin_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_areas_conhecimento WHERE ac_area_conhecimento = :ac_area_conhecimento";
  $stmt = $conn->prepare($sql);
  $stmt->execute([':ac_area_conhecimento' => $ac_area_conhecimento]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "A área do conhecimento informada já foi cadastra!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "INSERT INTO conf_areas_conhecimento (
                                                  ac_area_conhecimento,
                                                  ac_status,
                                                  ac_user_id,
                                                  ac_data_cad,
                                                  ac_data_upd
                                                ) VALUES (
                                                  UPPER(:ac_area_conhecimento),
                                                  :ac_status,
                                                  :ac_user_id,
                                                  GETDATE(),
                                                  GETDATE()
                                                )";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':ac_area_conhecimento' => $ac_area_conhecimento,
      ':ac_status'            => $ac_status,
      ':ac_user_id'           => $reservm_admin_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
    $stmt_log = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                                VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt_log->execute([
      ':modulo'     => 'ÁREA CONHECIMENTO',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $last_id,
      ':dados'      => 'Área Conhecimento: ' . $ac_area_conhecimento . '; Status: ' . $ac_status,
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
                              EDITAR ÁREA DO CONHECIMENTO
 *****************************************************************************************/
if (isset($dados['EditarAreaConhecimento'])) {

  $ac_id                = trim($_POST['ac_id']) !== '' ? trim($_POST['ac_id']) : NULL;
  $ac_area_conhecimento = trim($_POST['ac_area_conhecimento']) !== '' ? trim($_POST['ac_area_conhecimento']) : NULL;
  $ac_status            = trim($_POST['ac_status']) !== '' ? trim($_POST['ac_status']) : 0;
  $reservm_admin_id        = $_SESSION['reservm_admin_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_areas_conhecimento WHERE ac_area_conhecimento = :ac_area_conhecimento AND ac_id != :ac_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':ac_id'                => $ac_id,
    ':ac_area_conhecimento' => $ac_area_conhecimento
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "A área do conhecimento informada já foi cadastra!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "UPDATE
                    conf_areas_conhecimento
              SET
                    ac_area_conhecimento = UPPER(:ac_area_conhecimento),
                    ac_status            = :ac_status,
                    ac_user_id           = :ac_user_id,
                    ac_data_upd          = GETDATE()
              WHERE
                    ac_id = :ac_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':ac_id'                => $ac_id,
      ':ac_area_conhecimento' => $ac_area_conhecimento,
      ':ac_status'            => $ac_status,
      ':ac_user_id'           => $reservm_admin_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt_log = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                                VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt_log->execute([
      ':modulo'     => 'ÁREA CONHECIMENTO',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $ac_id,
      ':dados'      => 'Área Conhecimento: ' . $ac_area_conhecimento . '; Status: ' . $ac_status,
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
                              EXCLUIR ÁREA DO CONHECIMENTO
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_area") {

  $ac_id         = $_GET['ac_id'];
  $reservm_admin_id = $_SESSION['reservm_admin_id'];

  // SE DADO ESTIVER SENDO USADO EM ALGUM CADASTRO, NÃO PODE SER EXCLUÍDO
  $sql = "SELECT * FROM propostas WHERE CHARINDEX(:prop_area_conhecimento, prop_area_conhecimento) > 0"; // VERIFICA SE ID ESTÁ DENTRO DO ARRAY
  $stmt = $conn->prepare($sql);
  $stmt->execute([':prop_area_conhecimento' => $ac_id]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este registro não pode ser excluído!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "DELETE FROM conf_areas_conhecimento WHERE ac_id = :ac_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':ac_id' => $ac_id]);

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {

      // REGISTRA AÇÃO NO LOG
      $stmt_log = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                                  VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt_log->execute([
        ':modulo'  => 'ÁREA CONHECIMENTO',
        ':acao'    => 'EXCLUSÃO',
        ':acao_id' => $ac_id,
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
