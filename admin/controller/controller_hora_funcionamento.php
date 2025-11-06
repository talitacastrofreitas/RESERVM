<?php
// session_start();
include '../conexao/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  try {

    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $acao = $_POST['acao']; // AÇÃO: CADASTRAR, ATUALIZAR, DELETAR

    // SE ATUALIZAR
    if ($acao === 'atualizar') {

      // POST
      $chf_hora_inicio = !empty($_POST['chf_hora_inicio']) ? $_POST['chf_hora_inicio'] : null;
      $chf_hora_fim    = !empty($_POST['chf_hora_fim']) ? $_POST['chf_hora_fim'] : null;
    }
    $rvm_admin_id = $_SESSION['reservm_admin_id'];


    // -------------------------------
    // ATUALIZAR
    // -------------------------------
    if ($acao === 'atualizar') {

      if (empty($_POST['chf_id'])) {
        throw new Exception("Erro ao obter o ID!");
      }
      $chf_id    = (int) $_POST['chf_id'];
      $log_acao = 'Atualização';

      $sql = "UPDATE conf_hora_funcionamento SET 
                                                  chf_hora_inicio = :chf_hora_inicio,
                                                  chf_hora_fim    = :chf_hora_fim
                                            WHERE 
                                                  chf_id = :chf_id";

      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':chf_id', $chf_id, PDO::PARAM_INT);
      $stmt->bindParam(':chf_hora_inicio', $chf_hora_inicio, $chf_hora_inicio === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
      $stmt->bindParam(':chf_hora_fim', $chf_hora_fim, $chf_hora_fim === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
      $stmt->execute();


      // -------------------------------
      // AÇÃO INVÁLIDA
      // -------------------------------
    } else {
      throw new Exception("Ação inválida.");
    }

    // REGISTRA NO LOG
    $sqlLog = "INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data)
              VALUES (:modulo, upper(:acao), :acao_id, :dados, :user_id, GETDATE())";
    $stmtLog = $conn->prepare($sqlLog);
    $stmtLog->execute([
      ':modulo'  => 'HORÁRIO DE FUNCIONAMENTO',
      ':acao'    => $log_acao,
      ':acao_id' => $chf_id,
      ':dados'   => json_encode($_POST),
      ':user_id' => $rvm_admin_id
    ]);
    // -------------------------------

    $conn->commit(); // CONFIRMA A TRANSAÇÃO

    $_SESSION["msg"] = "Dados atualizados com sucesso!";
    header("Location: ../admin/hora_funcionamento.php");
    exit;
    // -------------------------------

  } catch (Exception $e) {
    $conn->rollBack();
    $_SESSION["erro"] = $e->getMessage();
    header("Location: ../admin/hora_funcionamento.php");
    exit;
  }
} else {
  $_SESSION["erro"] = "Requisição inválida.";
  header("Location: ../admin/hora_funcionamento.php");
  exit;
}
