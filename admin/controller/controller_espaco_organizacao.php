<?php
session_start();
include '../../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// if ($_SERVER["REQUEST_METHOD"] === "POST") {}

/*****************************************************************************************
                            CADASTRAR ORGANIZAÇÃO DO ESPAÇO
 *****************************************************************************************/
if (isset($dados['CadEspacoOrganizacao'])) {

  $esporg_espaco_organizacao = trim($_POST['esporg_espaco_organizacao']) !== '' ? trim($_POST['esporg_espaco_organizacao']) : NULL;
  $esporg_status             = trim(isset($_POST['esporg_status'])) ? $_POST['esporg_status'] : 0;
  $reservm_admin_id             = $_SESSION['reservm_admin_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_tipo_espaco_organizacao WHERE esporg_espaco_organizacao = :esporg_espaco_organizacao";
  $stmt = $conn->prepare($sql);
  $stmt->execute([':esporg_espaco_organizacao' => $esporg_espaco_organizacao]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este tipo de organização de espaço já foi cadastrado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------


  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
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
                                                        GETDATE(),
                                                        GETDATE()
                                                      )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':esporg_espaco_organizacao' => $esporg_espaco_organizacao,
      ':esporg_status'             => $esporg_status,
      ':esporg_user_id'            => $reservm_admin_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'ORGANIZAÇÃO DO ESPAÇO',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $last_id,
      ':dados'      => 'Organização do Espaço: ' . $esporg_espaco_organizacao . '; Status: ' . $esporg_status,
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
                              EDITAR ORGANIZAÇÃO DO ESPAÇO
 *****************************************************************************************/
if (isset($dados['EditEspacoOrganizacao'])) {

  $esporg_id                 = trim($_POST['esporg_id']) !== '' ? trim($_POST['esporg_id']) : NULL;
  $esporg_espaco_organizacao = trim($_POST['esporg_espaco_organizacao']) !== '' ? trim($_POST['esporg_espaco_organizacao']) : NULL;
  $esporg_status             = trim(isset($_POST['esporg_status'])) ? $_POST['esporg_status'] : 0;
  $reservm_admin_id             = $_SESSION['reservm_admin_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_tipo_espaco_organizacao WHERE esporg_espaco_organizacao = :esporg_espaco_organizacao AND esporg_id != :esporg_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':esporg_id'                 => $esporg_id,
    ':esporg_espaco_organizacao' => $esporg_espaco_organizacao
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este tipo de organização de espaço já foi cadastrado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "UPDATE
                    conf_tipo_espaco_organizacao
              SET
                    esporg_espaco_organizacao = UPPER(:esporg_espaco_organizacao),
                    esporg_status             = :esporg_status,
                    esporg_user_id            = :esporg_user_id,
                    esporg_data_upd           = GETDATE()
              WHERE
                    esporg_id = :esporg_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':esporg_id'                 => $esporg_id,
      ':esporg_espaco_organizacao' => $esporg_espaco_organizacao,
      ':esporg_status'             => $esporg_status,
      ':esporg_user_id'            => $reservm_admin_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'ORGANIZAÇÃO DO ESPAÇO',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $esporg_id,
      ':dados'      => 'Organização do Espaço: ' . $esporg_espaco_organizacao . '; Status: ' . $esporg_status,
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
                              EXCLUIR ORGANIZAÇÃO DO ESPAÇO
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_esporg") {

  $esporg_id     = $_GET['esporg_id'];
  $reservm_admin_id = $_SESSION['reservm_admin_id'];

  // SE DADO ESTIVER SENDO USADO EM ALGUM CADASTRO, NÃO PODE SER EXCLUÍDO
  $sql = "SELECT * FROM propostas_cursos_modulo WHERE CHARINDEX(:prop_cmod_organizacao, prop_cmod_organizacao) > 0"; // VERIFICA SE ID ESTÁ DENTRO DO ARRAY
  $stmt = $conn->prepare($sql);
  $stmt->execute([':prop_cmod_organizacao' => $esporg_id]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este registro não pode ser excluído!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "DELETE FROM conf_tipo_espaco_organizacao WHERE esporg_id = :esporg_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':esporg_id' => $esporg_id]);

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {

      // REGISTRA AÇÃO NO LOG
      $stmt_log = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                                  VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt_log->execute([
        ':modulo'  => 'ORGANIZAÇÃO DO ESPAÇO',
        ':acao'    => 'EXCLUSÃO',
        ':acao_id' => $esporg_id,
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
