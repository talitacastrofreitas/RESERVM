<?php
session_start();
include '../../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                      CADASTRAR PROGRAMA DE EXTENSÃO COMUNITÁRIA
 *****************************************************************************************/
if (isset($dados['CadExtensaoComunitaria'])) {

  $cec_extensao_comunitaria = trim($_POST['cec_extensao_comunitaria']) !== '' ? trim($_POST['cec_extensao_comunitaria']) : NULL;
  $cec_desc                 = trim($_POST['cec_desc']) !== '' ? trim($_POST['cec_desc']) : NULL;
  $cec_status               = trim(isset($_POST['cec_status'])) ? $_POST['cec_status'] : 0;
  $reservm_admin_id            = $_SESSION['reservm_admin_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_extensao_comunitaria WHERE cec_extensao_comunitaria = :cec_extensao_comunitaria";
  $stmt = $conn->prepare($sql);
  $stmt->execute([':cec_extensao_comunitaria' => $cec_extensao_comunitaria]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "O programa informada já foi cadastra!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "INSERT INTO conf_extensao_comunitaria (
                                                    cec_extensao_comunitaria,
                                                    cec_desc,
                                                    cec_status,
                                                    cec_user_id,
                                                    cec_data_cad,
                                                    cec_data_upd
                                                  ) VALUES (
                                                    UPPER(:cec_extensao_comunitaria),
                                                    LOWER(:cec_desc),
                                                    :cec_status,
                                                    :cec_user_id,
                                                    GETDATE(),
                                                    GETDATE()
                                                  )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':cec_extensao_comunitaria' => $cec_extensao_comunitaria,
      ':cec_desc'                 => $cec_desc,
      ':cec_status'               => $cec_status,
      ':cec_user_id'              => $reservm_admin_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'EXTENSÃO COMUNITÁRIA',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $last_id,
      ':dados'      => 'Programa: ' . $cec_extensao_comunitaria . '; Descrição: ' . $cec_desc . '; Status: ' . $cec_status,
      ':user_id'    => $reservm_admin_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Cadastro realizado com sucesso!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Cadastro não realizado!" . $e->getMessage();
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}






/*****************************************************************************************
                          EDITAR PROGRAMA DE EXTENSÃO COMUNITÁRIA
 *****************************************************************************************/
if (isset($dados['EditExtensaoComunitaria'])) {

  $cec_id                   = trim($_POST['cec_id']) !== '' ? trim($_POST['cec_id']) : NULL;
  $cec_extensao_comunitaria = trim($_POST['cec_extensao_comunitaria']) !== '' ? trim($_POST['cec_extensao_comunitaria']) : NULL;
  $cec_desc                 = trim($_POST['cec_desc']) !== '' ? trim($_POST['cec_desc']) : NULL;
  $cec_status               = trim(isset($_POST['cec_status'])) ? $_POST['cec_status'] : 0;
  $reservm_admin_id            = $_SESSION['reservm_admin_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_extensao_comunitaria WHERE cec_extensao_comunitaria = :cec_extensao_comunitaria AND cec_id != :cec_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':cec_id'                   => $cec_id,
    ':cec_extensao_comunitaria' => $cec_extensao_comunitaria
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "O programa informada já foi cadastra!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "UPDATE
                    conf_extensao_comunitaria
              SET
                    cec_extensao_comunitaria = UPPER(:cec_extensao_comunitaria),
                    cec_desc                 = :cec_desc,
                    cec_status               = :cec_status,
                    cec_user_id              = :cec_user_id,
                    cec_data_upd             = GETDATE()
              WHERE
                    cec_id = :cec_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':cec_id'                   => $cec_id,
      ':cec_extensao_comunitaria' => $cec_extensao_comunitaria,
      ':cec_desc'                 => $cec_desc,
      ':cec_status'               => $cec_status,
      ':cec_user_id'              => $reservm_admin_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'EXTENSÃO COMUNITÁRIA',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $cec_id,
      ':dados'      => 'Programa: ' . $cec_extensao_comunitaria . '; Descrição: ' . $cec_desc . '; Status: ' . $cec_status,
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
                        EXCLUIR PROGRAMA DE EXTENSÃO COMUNITÁRIA
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_cec") {

  $cec_id        = $_GET['cec_id'];
  $reservm_admin_id = $_SESSION['reservm_admin_id'];

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "DELETE FROM conf_extensao_comunitaria WHERE cec_id = :cec_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':cec_id' => $cec_id]);

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {

      // REGISTRA AÇÃO NO LOG
      $stmt_log = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                                  VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt_log->execute([
        ':modulo'  => 'EXTENSÃO COMUNITÁRIA',
        ':acao'    => 'EXCLUSÃO',
        ':acao_id' => $cec_id,
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
