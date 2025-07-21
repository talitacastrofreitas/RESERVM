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
      if (empty($_POST['dbloq_data']) || empty($_POST['dbloq_dia']) || empty($_POST['dbloq_mes']) || empty($_POST['dbloq_ano']) || empty($_POST['dbloq_motivo'])) {
        throw new Exception("Preencha os campos obrigatórios!");
      }

      // POST
      $dbloq_data   = $_POST['dbloq_data'];
      $dbloq_dia    = $_POST['dbloq_dia'];
      $dbloq_mes    = $_POST['dbloq_mes'];
      $dbloq_ano    = $_POST['dbloq_ano'];
      $dbloq_motivo = $_POST['dbloq_motivo'];
      $dbloq_status = $_POST['dbloq_status'] && $_POST['dbloq_status'] === '1' ? 1 : 0;

      // O VALOR DO DIA PRECISA SER ENTRE 1 E 7
      if (!in_array($dbloq_dia, ['1', '2', '3', '4', '5', '6', '7'], true)) {
        throw new Exception("Valor inválido para o dia do bloqueio!");
      }
    }

    $rvm_admin_id = $_SESSION['reservm_admin_id'];


    // -------------------------------
    // CADASTRO
    // -------------------------------
    if ($acao === 'cadastrar') {

      $log_acao = 'Cadastro';

      // IMPEDE CADASTRO DUPLICADO
      $sqlVerifica = "SELECT COUNT(*) FROM conf_dias_bloqueadas WHERE dbloq_data = :dbloq_data";
      $stmtVerifica = $conn->prepare($sqlVerifica);
      $stmtVerifica->execute([':dbloq_data' => $dbloq_data]);
      $existe = $stmtVerifica->fetchColumn();
      if ($existe > 0) {
        throw new Exception("Data já cadastrada!");
      }

      $sql = "INSERT INTO conf_dias_bloqueadas (
                                                dbloq_data,
                                                dbloq_dia,
                                                dbloq_mes,
                                                dbloq_ano,
                                                dbloq_motivo,
                                                dbloq_status,
                                                dbloq_user_id,
                                                dbloq_data_cad,
                                                dbloq_data_upd
                                              ) VALUES (
                                                :dbloq_data,
                                                :dbloq_dia,
                                                :dbloq_mes,
                                                :dbloq_ano,
                                                :dbloq_motivo,
                                                :dbloq_status,
                                                :dbloq_user_id,
                                                GETDATE(),
                                                GETDATE()
                                              )";

      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':dbloq_data' => $dbloq_data,
        ':dbloq_dia' => $dbloq_dia,
        ':dbloq_mes' => $dbloq_mes,
        ':dbloq_ano' => $dbloq_ano,
        ':dbloq_motivo' => $dbloq_motivo,
        ':dbloq_status' => $dbloq_status,
        ':dbloq_user_id' => $rvm_admin_id
      ]);

      // ÚLTIMO ID CADASTRADO
      if ($stmt->rowCount() > 0) {
        $dbloq_id = $conn->lastInsertId();
      } else {
        throw new Exception('Erro ao obter o último ID inserido.');
      }




      // -------------------------------
      // ATUALIZAR
      // -------------------------------
    } elseif ($acao === 'atualizar') {

      if (empty($_POST['dbloq_id'])) {
        throw new Exception("Erro ao obter o ID!");
      }
      $dbloq_id  = (int) $_POST['dbloq_id'];
      $log_acao = 'Atualização';

      // IMPEDE CADASTRO DUPLICADO
      $sqlVerifica = "SELECT COUNT(*) FROM conf_dias_bloqueadas WHERE dbloq_data = :dbloq_data AND dbloq_id != :dbloq_id";
      $stmtVerifica = $conn->prepare($sqlVerifica);
      $stmtVerifica->execute([':dbloq_data' => $dbloq_data, ':dbloq_id' => $dbloq_id]);
      $existe = $stmtVerifica->fetchColumn();
      if ($existe > 0) {
        throw new Exception("Data já cadastrada!");
      }

      $sql = "UPDATE conf_dias_bloqueadas SET 
                                              dbloq_data     = :dbloq_data,
                                              dbloq_dia      = :dbloq_dia,
                                              dbloq_mes      = :dbloq_mes,
                                              dbloq_ano      = :dbloq_ano,
                                              dbloq_motivo   = :dbloq_motivo,
                                              dbloq_status   = :dbloq_status,
                                              dbloq_user_id  = :dbloq_user_id,
                                              dbloq_data_upd = GETDATE()
                                        WHERE 
                                              dbloq_id = :dbloq_id";

      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':dbloq_id'      => $dbloq_id,
        ':dbloq_data'    => $dbloq_data,
        ':dbloq_dia'     => $dbloq_dia,
        ':dbloq_mes'     => $dbloq_mes,
        ':dbloq_ano'     => $dbloq_ano,
        ':dbloq_motivo'  => $dbloq_motivo,
        ':dbloq_status'  => $dbloq_status,
        ':dbloq_user_id' => $rvm_admin_id
      ]);




      // -------------------------------
      // EXCLUIR
      // -------------------------------
    } elseif ($_GET['acao'] === 'deletar') {

      if (empty($_GET['dbloq_id'])) {
        throw new Exception("ID é obrigatório para exclusão.");
      }
      $dbloq_id  = (int) $_GET['dbloq_id'];
      $log_acao = 'Exclusão';

      $sql = "DELETE FROM conf_dias_bloqueadas WHERE dbloq_id = :dbloq_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':dbloq_id' => $dbloq_id]);




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
      ':modulo'  => 'DATAS BLOQUEADAS',
      ':acao'    => $log_acao,
      ':acao_id' => $dbloq_id,
      ':dados'   => json_encode($log_dados, JSON_UNESCAPED_UNICODE),
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
    header("Location: ../admin/datas_bloqueadas.php");
    exit;
    // -------------------------------

  } catch (Exception $e) {
    $conn->rollBack();
    $_SESSION["erro"] = $e->getMessage();
    $_SESSION["form_dbloq"] = $_POST; // CRIA SESSÃO PARA OS DADOS DO FORMULÁRIO QUE FORAM ENVIADOS
    header("Location: ../admin/datas_bloqueadas.php");
    exit;
  }
} else {
  $_SESSION["erro"] = "Requisição inválida.";
  header("Location: ../admin/datas_bloqueadas.php");
  exit;
}
