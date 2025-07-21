<?php
include '../conexao/conexao.php';

/* ---------------------------------------------------
  CADASTRAR
----------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['func']) && $_GET['func'] == "cad_compc") {

  $compc_componente = trim($_POST['compc_componente']) !== '' ? trim($_POST['compc_componente']) : NULL;
  $compc_curso      = trim($_POST['compc_curso']) !== '' ? trim($_POST['compc_curso']) : NULL;
  $compc_status     = trim(isset($_POST['compc_status'])) ? $_POST['compc_status'] : 0;
  $rvm_admin_id     = $_SESSION['reservm_admin_id'];
  // -------------------------------

  // VERIFICA DADOS DUPLICADOS
  $sqlVerifica = $conn->prepare("SELECT COUNT(*) as total FROM componente_curricular WHERE compc_componente = :compc_componente");
  $sqlVerifica->execute([':compc_componente' => $compc_componente]);
  $resultado = $sqlVerifica->fetch(PDO::FETCH_ASSOC);
  if ($resultado['total'] > 0) {
    $_SESSION["erro"] = "Componente curricular já cadastrado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } else {

    try {
      $conn->beginTransaction(); // INICIA A TRANSAÇÃO
      $sql = $conn->prepare("INSERT INTO componente_curricular (
                                                                  compc_componente,
                                                                  compc_curso,
                                                                  compc_status, 
                                                                  compc_user_id,
                                                                  compc_data_cad,
                                                                  compc_data_upd
                                                                ) VALUES (
                                                                  UPPER(:compc_componente),
                                                                  :compc_curso,
                                                                  :compc_status,
                                                                  :compc_user_id,
                                                                  GETDATE(),
                                                                  GETDATE()
                                                                )");
      $sql->execute([
        ':compc_componente' => $compc_componente,
        ':compc_curso'      => $compc_curso,
        ':compc_status'     => $compc_status,
        ':compc_user_id'    => $rvm_admin_id
      ]);

      // REGISTRA AÇÃO NO LOG
      $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                              VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'  => 'COMPONENTE CURRICULAR',
        ':acao'    => 'CADASTRO',
        ':acao_id' => $last_id,
        ':dados'   => 'Componente: ' . $compc_componente . '; Curso: ' . $compc_curso . '; Status: ' . $compc_status,
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['func']) && $_GET['func'] == "edit_compc") {

  $compc_id         = trim($_POST['compc_id']) !== '' ? trim($_POST['compc_id']) : NULL;
  $compc_componente = trim($_POST['compc_componente']) !== '' ? trim($_POST['compc_componente']) : NULL;
  $compc_curso      = trim($_POST['compc_curso']) !== '' ? trim($_POST['compc_curso']) : NULL;
  $compc_status     = trim(isset($_POST['compc_status'])) ? $_POST['compc_status'] : 0;
  $rvm_admin_id     = $_SESSION['reservm_admin_id'];
  // -------------------------------


  // VERIFICA DADOS DUPLICADOS
  $sqlVerifica = $conn->prepare("SELECT COUNT(*) as total FROM componente_curricular WHERE compc_componente = :compc_componente AND compc_id != :compc_id");
  $sqlVerifica->execute([':compc_componente' => $compc_componente, ':compc_id' => $compc_id]);
  $resultado = $sqlVerifica->fetch(PDO::FETCH_ASSOC);
  if ($resultado['total'] > 0) {
    $_SESSION["erro"] = "Componente curricular já cadastrado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } else {

    try {
      $conn->beginTransaction(); // INICIA A TRANSAÇÃO
      $sql = $conn->prepare("UPDATE
                                    componente_curricular
                                SET   
                                    compc_componente    = UPPER(:compc_componente),
                                    compc_curso = :compc_curso,
                                    compc_status   = :compc_status,
                                    compc_user_id  = :compc_user_id,
                                    compc_data_upd = GETDATE()
                              WHERE
                                    compc_id = :compc_id");

      $sql->execute([
        ':compc_id'         => $compc_id,
        ':compc_componente' => $compc_componente,
        ':compc_curso'      => $compc_curso,
        ':compc_status'     => $compc_status,
        ':compc_user_id'    => $rvm_admin_id
      ]);

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                              VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'  => 'COMPONENTE CURRICULAR',
        ':acao'    => 'ATUALIZAÇÃO',
        ':acao_id' => $compc_id,
        ':dados'   => 'Componente: ' . $compc_componente . '; Curso: ' . $compc_curso . '; Status: ' . $compc_status,
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
if (isset($_GET['func']) && $_GET['func'] == "exc_compc") {

  $compc_id      = $_GET["compc_id"];
  $rvm_admin_id  = $_SESSION['reservm_admin_id'];

  // NÃO PERMITE EXCLUIR VEÍCULO SE HOUVER OCORRÊNCIA ATRELADA A ELE
  $sql = $conn->prepare("SELECT COUNT(*) FROM componente_curricular WHERE compc_curso = ?");
  $sql->execute([$compc_id]);
  if ($sql->fetchColumn()) {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark me-2\"></i> Estes dados não podem ser excluídos!";
    echo "<script> history.go(-1);</script>";
    return die;
  }

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $stmt = $conn->prepare("DELETE FROM componente_curricular WHERE compc_id = ?");
    $stmt->execute([$compc_id]);

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount()) {

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                              VALUES (:modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'     => 'COMPONENTE CURRICULAR',
        ':acao'       => 'EXCLUSÃO',
        ':acao_id'    => $compc_id,
        ':user_id'    => $rvm_admin_id
      ]);

      $conn->commit(); // SE NÃO HOUVER NENHUM ERRO, REALIZA A AÇÃO
      // -------------------------------

      $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check me-2\"></i> Dados excluídos com sucesso!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    } else {
      $conn->rollBack();
      $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark me-2\"></i> Erro ao tentar excluir o registro!";
      echo "<script> history.go(-1);</script>";
      return die;
    }
  } catch (PDOException $e) {
    $conn->rollBack();
    //echo "Erro: " . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark me-2\"></i> Erro ao tentar excluir o registro!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
}
