<?php
include '../conexao/conexao.php';

/* ---------------------------------------------------
  CADASTRAR
----------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['func']) && $_GET['func'] == "cad_curs") {

  $curs_curso   = trim($_POST['curs_curso']) !== '' ? trim($_POST['curs_curso']) : NULL;
  $curs_status  = trim(isset($_POST['curs_status'])) ? $_POST['curs_status'] : 0;
  $rvm_admin_id = $_SESSION['reservm_admin_id'];
  // -------------------------------

  // VERIFICA DADOS DUPLICADOS
  $sqlVerifica = $conn->prepare("SELECT COUNT(*) as total FROM cursos WHERE curs_curso = :curs_curso");
  $sqlVerifica->execute([':curs_curso' => $curs_curso]);
  $resultado = $sqlVerifica->fetch(PDO::FETCH_ASSOC);
  if ($resultado['total'] > 0) {
    $_SESSION["erro"] = "Curso já cadastrado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } else {

    try {
      $conn->beginTransaction(); // INICIA A TRANSAÇÃO
      $sql = $conn->prepare("INSERT INTO cursos (
                                                  curs_curso,
                                                  curs_status, 
                                                  curs_user_id,
                                                  curs_data_cad,
                                                  curs_data_upd
                                                ) VALUES (
                                                  UPPER(:curs_curso),
                                                  :curs_status,
                                                  :curs_user_id,
                                                  GETDATE(),
                                                  GETDATE()
                                                )");
      $sql->execute([
        ':curs_curso'   => $curs_curso,
        ':curs_status'  => $curs_status,
        ':curs_user_id' => $rvm_admin_id
      ]);

      // REGISTRA AÇÃO NO LOG
      $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'  => 'CURSOS',
        ':acao'    => 'CADASTRO',
        ':acao_id' => $last_id,
        ':dados'   => 'Curso: ' . $curs_curso . '; Status: ' . $curs_status,
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['func']) && $_GET['func'] == "edit_curs") {

  $curs_id      = trim($_POST['curs_id']) !== '' ? trim($_POST['curs_id']) : NULL;
  $curs_curso   = trim($_POST['curs_curso']) !== '' ? trim($_POST['curs_curso']) : NULL;
  $curs_status  = trim(isset($_POST['curs_status'])) ? $_POST['curs_status'] : 0;
  $rvm_admin_id = $_SESSION['reservm_admin_id'];
  // -------------------------------


  // VERIFICA DADOS DUPLICADOS
  $sqlVerifica = $conn->prepare("SELECT COUNT(*) as total FROM cursos WHERE curs_curso = :curs_curso AND curs_id != :curs_id");
  $sqlVerifica->execute([':curs_curso' => $curs_curso, ':curs_id' => $curs_id]);
  $resultado = $sqlVerifica->fetch(PDO::FETCH_ASSOC);
  if ($resultado['total'] > 0) {
    $_SESSION["erro"] = "Curso já cadastrado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } else {

    try {
      $conn->beginTransaction(); // INICIA A TRANSAÇÃO
      $sql = $conn->prepare("UPDATE
                                    cursos
                                SET   
                                    curs_curso    = UPPER(:curs_curso),
                                    curs_status   = :curs_status,
                                    curs_user_id  = :curs_user_id,
                                    curs_data_upd = GETDATE()
                              WHERE
                                    curs_id = :curs_id");

      $sql->execute([
        ':curs_id'      => $curs_id,
        ':curs_curso'   => $curs_curso,
        ':curs_status'  => $curs_status,
        ':curs_user_id' => $rvm_admin_id
      ]);

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                              VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'  => 'CURSOS',
        ':acao'    => 'ATUALIZAÇÃO',
        ':acao_id' => $curs_id,
        ':dados'   => 'Curso: ' . $curs_curso . '; Status: ' . $curs_status,
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
if (isset($_GET['func']) && $_GET['func'] == "exc_curs") {

  $curs_id      = $_GET["curs_id"];
  $rvm_admin_id = $_SESSION['reservm_admin_id'];

  // NÃO PERMITE EXCLUIR VEÍCULO SE HOUVER OCORRÊNCIA ATRELADA A ELE
  $sql = $conn->prepare("SELECT COUNT(*) FROM componente_curricular WHERE compc_curso = ?");
  $sql->execute([$curs_id]);
  if ($sql->fetchColumn()) {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark me-2\"></i> Estes dados não podem ser excluídos!";
    echo "<script> history.go(-1);</script>";
    return die;
  }

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $stmt = $conn->prepare("DELETE FROM cursos WHERE curs_id = ?");
    $stmt->execute([$curs_id]);

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount()) {

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                              VALUES (:modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'     => 'CURSOS',
        ':acao'       => 'EXCLUSÃO',
        ':acao_id'    => $curs_id,
        ':user_id'    => $rvm_admin_id
      ]);

      $conn->commit(); // SE NÃO HOUVER NENHUM ERRO, REALIZA A AÇÃO
      // -------------------------------

      $_SESSION["msg"] = "Dados excluídos com sucesso!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    } else {
      $conn->rollBack();
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
