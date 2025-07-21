<?php
include '../conexao/conexao.php';

/* ---------------------------------------------------
  CADASTRAR
----------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['func']) && $_GET['func'] == "cad_recur") {

  $rec_recurso  = trim($_POST['rec_recurso']) !== '' ? trim($_POST['rec_recurso']) : NULL;
  $rec_status   = trim(isset($_POST['rec_status'])) ? $_POST['rec_status'] : 0;
  $rvm_admin_id = $_SESSION['reservm_admin_id'];
  // -------------------------------

  // VERIFICA DADOS DUPLICADOS
  $sqlVerifica = $conn->prepare("SELECT COUNT(*) as total FROM recursos WHERE rec_recurso = :rec_recurso");
  $sqlVerifica->execute([':rec_recurso' => $rec_recurso]);
  $resultado = $sqlVerifica->fetch(PDO::FETCH_ASSOC);
  if ($resultado['total'] > 0) {
    $_SESSION["erro"] = "Recurso já cadastrado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } else {

    try {
      $conn->beginTransaction(); // INICIA A TRANSAÇÃO
      $sql = $conn->prepare("INSERT INTO recursos (
                                                    rec_recurso,
                                                    rec_status, 
                                                    rec_user_id,
                                                    rec_data_cad,
                                                    rec_data_upd
                                                  ) VALUES (
                                                    UPPER(:rec_recurso),
                                                    :rec_status,
                                                    :rec_user_id,
                                                    GETDATE(),
                                                    GETDATE()
                                                  )");
      $sql->execute([
        ':rec_recurso'   => $rec_recurso,
        ':rec_status'  => $rec_status,
        ':rec_user_id' => $rvm_admin_id
      ]);

      // REGISTRA AÇÃO NO LOG
      $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'  => 'RECURSOS',
        ':acao'    => 'CADASTRO',
        ':acao_id' => $last_id,
        ':dados'   => 'Recurso: ' . $rec_recurso . '; Status: ' . $rec_status,
        ':user_id' => $rvm_admin_id
      ]);

      $conn->commit(); // SE NÃO HOUVER NENHUM ERRO, REALIZA A AÇÃO
      // -------------------------------

      $_SESSION["msg"] = "Cadastro realizado com sucesso!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    } catch (PDOException $e) {
      //echo 'Error: ' . $e->getMessage();
      $conn->rollBack();
      $_SESSION["erro"] = "Cadastro não realizado!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      return die;
    }
  }
}










/* ---------------------------------------------------
  EDITAR
----------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['func']) && $_GET['func'] == "edit_recur") {

  $rec_id       = trim($_POST['rec_id']) !== '' ? trim($_POST['rec_id']) : NULL;
  $rec_recurso  = trim($_POST['rec_recurso']) !== '' ? trim($_POST['rec_recurso']) : NULL;
  $rec_status   = trim(isset($_POST['rec_status'])) ? $_POST['rec_status'] : 0;
  $rvm_admin_id = $_SESSION['reservm_admin_id'];
  // -------------------------------


  // VERIFICA DADOS DUPLICADOS
  $sqlVerifica = $conn->prepare("SELECT COUNT(*) as total FROM recursos WHERE rec_recurso = :rec_recurso AND rec_id != :rec_id");
  $sqlVerifica->execute([':rec_recurso' => $rec_recurso, ':rec_id' => $rec_id]);
  $resultado = $sqlVerifica->fetch(PDO::FETCH_ASSOC);
  if ($resultado['total'] > 0) {
    $_SESSION["erro"] = "Recurso já cadastrado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } else {

    try {
      $conn->beginTransaction(); // INICIA A TRANSAÇÃO
      $sql = $conn->prepare("UPDATE
                                    recursos
                                SET   
                                    rec_recurso    = UPPER(:rec_recurso),
                                    rec_status   = :rec_status,
                                    rec_user_id  = :rec_user_id,
                                    rec_data_upd = GETDATE()
                              WHERE
                                    rec_id = :rec_id");

      $sql->execute([
        ':rec_id'      => $rec_id,
        ':rec_recurso'   => $rec_recurso,
        ':rec_status'  => $rec_status,
        ':rec_user_id' => $rvm_admin_id
      ]);

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                              VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'  => 'RECURSOS',
        ':acao'    => 'ATUALIZAÇÃO',
        ':acao_id' => $rec_id,
        ':dados'   => 'Recurso: ' . $rec_recurso . '; Status: ' . $rec_status,
        ':user_id' => $rvm_admin_id
      ]);

      $conn->commit(); // SE NÃO HOUVER NENHUM ERRO, REALIZA A AÇÃO
      // -------------------------------

      $_SESSION["msg"] = "Dados atualizados com sucesso!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    } catch (PDOException $e) {
      //echo 'Error: ' . $e->getMessage();
      $conn->rollBack();
      $_SESSION["erro"] = "Dados não atualizados!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      return die;
    }
  }
}










/* ---------------------------------------------------
  EXCLUIR
----------------------------------------------------- */
if (isset($_GET['func']) && $_GET['func'] == "exc_recur") {

  $rec_id       = $_GET["rec_id"];
  $rvm_admin_id = $_SESSION['reservm_admin_id'];

  // NÃO PERMITE EXCLUIR VEÍCULO SE HOUVER OCORRÊNCIA ATRELADA A ELE
  // $sql = $conn->prepare("SELECT COUNT(*) FROM componente_curricular WHERE compc_curso = ?");
  // $sql->execute([$rec_id]);
  // if ($sql->fetchColumn()) {
  //   $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark me-2\"></i> Estes dados não podem ser excluídos!";
  //   echo "<script> history.go(-1);</script>";
  //   return die;
  // }

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $stmt = $conn->prepare("DELETE FROM recursos WHERE rec_id = ?");
    $stmt->execute([$rec_id]);

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount()) {

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                              VALUES (:modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'     => 'RECURSOS',
        ':acao'       => 'EXCLUSÃO',
        ':acao_id'    => $rec_id,
        ':user_id'    => $rvm_admin_id
      ]);

      $conn->commit(); // SE NÃO HOUVER NENHUM ERRO, REALIZA A AÇÃO
      // -------------------------------

      $_SESSION["msg"] = "Dados excluídos com sucesso!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    } else {
      $_SESSION["erro"] = "Erro ao tentar excluir o registro!";
      echo "<script> history.go(-1);</script>";
      return die;
    }
  } catch (PDOException $e) {
    $conn->rollBack();
    //echo "Erro: " . $e->getMessage();
    $_SESSION["erro"] = "Erro ao tentar excluir o registro!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
}
