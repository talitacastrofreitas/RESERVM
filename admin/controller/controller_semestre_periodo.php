<?php
// session_start();
include '../conexao/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  try {

    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $acao = $_POST['acao']; // AÇÃO: CADASTRAR, ATUALIZAR, DELETAR

    // SE ATUALIZAR
    if ($acao === 'atualizar') {

      // VALIDA OS CAMPOS OBRIGATÓRIOS
      if (empty($_POST['semp_data_inicio']) || empty($_POST['semp_data_fim'])) {
        throw new Exception("Preencha os campos obrigatórios!");
      }

      // POST
      $semp_data_inicio = trim($_POST['semp_data_inicio']);
      $semp_data_fim    = trim($_POST['semp_data_fim']);
    }
    $rvm_admin_id = $_SESSION['reservm_admin_id'];

    // -------------------------------
    // ATUALIZAR
    // -------------------------------
    if ($acao === 'atualizar') {

      if (empty($_POST['semp_id'])) {
        throw new Exception("Erro ao obter o ID!");
      }
      $semp_id = (int) $_POST['semp_id'];
      $log_acao = 'Atualização';

      $sql = "UPDATE conf_semestre_periodo SET 
                                                semp_data_inicio = :semp_data_inicio,
                                                semp_data_fim    = :semp_data_fim,
                                                semp_cad_id      = :semp_cad_id,
                                                semp_data_upd    = GETDATE()
                                          WHERE
                                                semp_id = :semp_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':semp_id'          => $semp_id,
        ':semp_data_inicio' => $semp_data_inicio,
        ':semp_data_fim'    => $semp_data_fim,
        ':semp_cad_id'      => $rvm_admin_id
      ]);


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
      ':modulo'  => 'SEMESTRE PERÍODO',
      ':acao'    => $log_acao,
      ':acao_id' => $semp_id,
      ':dados'   => json_encode($log_dados, JSON_UNESCAPED_UNICODE),
      ':user_id' => $rvm_admin_id
    ]);
    // -------------------------------

    $conn->commit(); // CONFIRMA A TRANSAÇÃO

    $_SESSION["msg"] = "Dados atualizados com sucesso!";
    header("Location: ../admin/config.php");
    exit;
    // -------------------------------

  } catch (Exception $e) {
    $conn->rollBack();
    $_SESSION["erro"] = $e->getMessage();
    header("Location: ../admin/config.php");
    exit;
  }
} else {
  $_SESSION["erro"] = "Requisição inválida.";
  header("Location: ../admin/config.php");
  exit;
}
