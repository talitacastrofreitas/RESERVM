<?php
// session_start();
include '../conexao/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {
  try {

    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $acao = $_POST['acao']; // AÇÃO: CADASTRAR, ATUALIZAR, DELETAR

    // SE CADASTRAR OU ATUALIZAR
    if ($acao === 'cadastrar' || $acao === 'atualizar') {

      // VALIDA OS CAMPOS OBRIGATÓRIOS
      if (empty($_POST['rec_recurso'])) {
        throw new Exception("Preencha os campos obrigatórios!");
      }

      // POST
      $rec_recurso = trim($_POST['rec_recurso']);
      $rec_status = $_POST['rec_status'] === '1' ? 1 : 0;
    }
    $rvm_admin_id = $_SESSION['reservm_admin_id'];


    // -------------------------------
    // CADASTRO
    // -------------------------------
    if ($acao === 'cadastrar') {

      $log_acao = 'Cadastro';

      // IMPEDE CADASTRO DUPLICADO
      $sqlVerifica = "SELECT COUNT(*) FROM recursos WHERE rec_recurso = :rec_recurso";
      $stmtVerifica = $conn->prepare($sqlVerifica);
      $stmtVerifica->execute([':rec_recurso' => $rec_recurso]);
      $existe = $stmtVerifica->fetchColumn();
      if ($existe > 0) {
        throw new Exception("Recurso já cadastrada!");
      }

      $sql = "INSERT INTO recursos (
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
                                    )";
      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':rec_recurso' => $rec_recurso,
        ':rec_status' => $rec_status,
        ':rec_user_id' => $rvm_admin_id
      ]);

      // ÚLTIMO ID CADASTRADO
      if ($stmt->rowCount() > 0) {
        $rec_id = $conn->lastInsertId();
      } else {
        throw new Exception('Erro ao obter o último ID inserido.');
      }




      // -------------------------------
      // ATUALIZAR
      // -------------------------------
    } elseif ($acao === 'atualizar') {

      if (empty($_POST['rec_id'])) {
        throw new Exception("Erro ao obter o ID!");
      }
      $rec_id = (int) $_POST['rec_id'];
      $log_acao = 'Atualização';

      // IMPEDE CADASTRO DUPLICADO
      $sqlVerifica = "SELECT COUNT(*) FROM recursos WHERE rec_recurso = :rec_recurso AND rec_id != :rec_id";
      $stmtVerifica = $conn->prepare($sqlVerifica);
      $stmtVerifica->execute([':rec_recurso' => $rec_recurso, ':rec_id' => $rec_id]);
      $existe = $stmtVerifica->fetchColumn();
      if ($existe > 0) {
        throw new Exception("Recurso já cadastrada!");
      }

      $sql = "UPDATE recursos SET 
                                  rec_recurso  = UPPER(:rec_recurso),
                                  rec_status   = :rec_status,
                                  rec_user_id  = :rec_user_id,
                                  rec_data_upd = GETDATE()
                            WHERE
                                  rec_id = :rec_id";

      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':rec_id' => $rec_id,
        ':rec_recurso' => $rec_recurso,
        ':rec_status' => $rec_status,
        ':rec_user_id' => $rvm_admin_id
      ]);




      // -------------------------------
      // EXCLUIR
      // -------------------------------
    } elseif ($_GET['acao'] === 'deletar') {

      echo "Tentando excluir...<br>";
      if (empty($_GET['rec_id'])) {
        echo "Erro: rec_id está vazio.<br>";
        throw new Exception("ID é obrigatório para exclusão.");
      }
      echo "ID do Recurso a ser excluído: " . $rec_id . "<br>";
      $rec_id = (int) $_GET['rec_id'];
      $log_acao = 'Exclusão';

      echo "Nenhum registro relacionado encontrado. Prosseguindo com a exclusão.<br>";

      // SE DADO ESTIVER SENDO USADO EM ALGUM CADASTRO, NÃO PODE SER EXCLUÍDO
      $sql = $conn->prepare("SELECT COUNT(*) FROM reservas WHERE ',' + REPLACE(res_recursos_add, ' ', '') + ',' LIKE ?");
      $sql->execute(['%,' . $rec_id . ',%']);
      $existe = $sql->fetchColumn();
      if ($existe > 0) {
        throw new Exception("Este registro não pode ser excluído!");
      }
      // -------------------------------
      $sql = $conn->prepare("SELECT COUNT(*) FROM espaco WHERE ',' + REPLACE(esp_recursos, ' ', '') + ',' LIKE ?");
      $sql->execute(['%,' . $rec_id . ',%']);
      $existe = $sql->fetchColumn();
      if ($existe > 0) {
        throw new Exception("Este registro não pode ser excluído!");
      }
      // -------------------------------

      $sql = "DELETE FROM recursos WHERE rec_id = :rec_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':rec_id' => $rec_id]);




      // -------------------------------
      // AÇÃO INVÁLIDA
      // -------------------------------
    } else {
      throw new Exception("Ação inválida.");
    }

    // REGISTRA NO LOG
    $log_dados = ['POST' => $_POST, 'GET' => $_GET, 'FILES' => $_FILES];
    $sqlLog = "INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data)
              VALUES (:modulo, upper(:acao), :acao_id, :dados, :user_id, GETDATE())";
    $stmtLog = $conn->prepare($sqlLog);
    $stmtLog->execute([
      ':modulo' => 'RECURSOS',
      ':acao' => $log_acao,
      ':acao_id' => $rec_id,
      ':dados' => json_encode($log_dados, JSON_UNESCAPED_UNICODE),
      ':user_id' => $rvm_admin_id
    ]);
    // -------------------------------

    $conn->commit(); // CONFIRMA A TRANSAÇÃO

    // CONFIGURAÇÃO DE MENSAGEM
    if ($acao === 'cadastrar') {
      $_SESSION["msg"] = "Dados cadastrados com sucesso!";
    } elseif ($acao === 'atualizar') {
      $_SESSION["msg"] = "Dados atualizados com sucesso!";
    } else {
      $_SESSION["msg"] = "Dados excluídos com sucesso!";
    }
    // -------------------------------
    header("Location: ../admin/recursos.php");
    exit;
    // -------------------------------

  } catch (Exception $e) {
    $conn->rollBack();
    $_SESSION["erro"] = $e->getMessage();
    $_SESSION["form_rec"] = $_POST; // CRIA SESSÃO PARA OS DADOS DO FORMULÁRIO QUE FORAM ENVIADOS
    header("Location: ../admin/recursos.php");
    exit;
  }
} else {
  $_SESSION["erro"] = "Requisição inválida.";
  header("Location: ../admin/recursos.php");
  exit;
}
