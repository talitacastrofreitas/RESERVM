<?php
session_start();
include '../../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                            CADASTRAR TIPO DE PROGRAMA
 *****************************************************************************************/
if (isset($dados['CadTipoPrograma'])) {

  $ctp_tipo      = trim($_POST['ctp_tipo']) !== '' ? trim($_POST['ctp_tipo']) : NULL;
  $ctp_categoria = trim($_POST['ctp_categoria']) !== '' ? trim($_POST['ctp_categoria']) : NULL;
  $ctp_status    = trim(isset($_POST['ctp_status'])) ? $_POST['ctp_status'] : 0;
  $reservm_admin_id = $_SESSION['reservm_admin_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_tipo_programa WHERE ctp_tipo = :ctp_tipo AND ctp_categoria = :ctp_categoria";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':ctp_tipo'      => $ctp_tipo,
    ':ctp_categoria' => $ctp_categoria
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este programa já foi cadastrado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
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
                                              GETDATE(),
                                              GETDATE()
                                            )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':ctp_tipo'      => $ctp_tipo,
      ':ctp_categoria' => $ctp_categoria,
      ':ctp_status'    => $ctp_status,
      ':ctp_user_id'   => $reservm_admin_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'TIPO DE PROGRAMA',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $last_id,
      ':dados'      => 'Tipo: ' . $ctp_tipo . '; Categoria: ' . $ctp_categoria . '; Status: ' . $ctp_status,
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
                                EDITAR TIPO DE PROGRAMA
 *****************************************************************************************/
if (isset($dados['EditTipoPrograma'])) {

  $ctp_id        = trim($_POST['ctp_id']) !== '' ? trim($_POST['ctp_id']) : NULL;
  $ctp_tipo      = trim($_POST['ctp_tipo']) !== '' ? trim($_POST['ctp_tipo']) : NULL;
  $ctp_categoria = trim($_POST['ctp_categoria']) !== '' ? trim($_POST['ctp_categoria']) : NULL;
  $ctp_status    = trim(isset($_POST['ctp_status'])) ? $_POST['ctp_status'] : 0;
  $reservm_admin_id = $_SESSION['reservm_admin_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_tipo_programa WHERE ctp_tipo = :ctp_tipo AND ctp_categoria = :ctp_categoria AND ctp_id != :ctp_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':ctp_id'        => $ctp_id,
    ':ctp_tipo'      => $ctp_tipo,
    ':ctp_categoria' => $ctp_categoria
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este programa já foi cadastrado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "UPDATE
                    conf_tipo_programa
              SET
                    ctp_tipo      = UPPER(:ctp_tipo),
                    ctp_categoria = UPPER(:ctp_categoria),
                    ctp_status    = :ctp_status,
                    ctp_user_id   = :ctp_user_id,
                    ctp_data_upd  = GETDATE()
              WHERE
                    ctp_id        = :ctp_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':ctp_id'        => $ctp_id,
      ':ctp_tipo'      => $ctp_tipo,
      ':ctp_categoria' => $ctp_categoria,
      ':ctp_status'    => $ctp_status,
      ':ctp_user_id'   => $reservm_admin_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'TIPO DE PROGRAMA',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $ctp_id,
      ':dados'      => 'Tipo: ' . $ctp_tipo . '; Categoria: ' . $ctp_categoria . '; Status: ' . $ctp_status,
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
                        EXCLUIR TIPO DE PROGRAMA
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_ctp") {

  $ctp_id        = $_GET['ctp_id'];
  $reservm_admin_id = $_SESSION['reservm_admin_id'];;

  // SE DADO ESTIVER SENDO USADO EM ALGUM CADASTRO, NÃO PODE SER EXCLUÍDO
  $sql = "SELECT * FROM propostas WHERE CHARINDEX(:prop_prog_categoria, prop_prog_categoria) > 0"; // VERIFICA SE ID ESTÁ DENTRO DO ARRAY
  $stmt = $conn->prepare($sql);
  $stmt->execute([':prop_prog_categoria' => $ctp_id]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este registro não pode ser excluído!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "DELETE FROM conf_tipo_programa WHERE ctp_id = :ctp_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':ctp_id' => $ctp_id]);

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {

      // REGISTRA AÇÃO NO LOG
      $stmt_log = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                                  VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt_log->execute([
        ':modulo'  => 'TIPO DE PROGRAMA',
        ':acao'    => 'EXCLUSÃO',
        ':acao_id' => $ctp_id,
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
    $_SESSION["erro"] = "Dados não excluídos!";
    $conn->rollBack();
    echo "<script> history.go(-1);</script>";
    return die;
  }
}
