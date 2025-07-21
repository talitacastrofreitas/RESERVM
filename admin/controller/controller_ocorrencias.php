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
      if (empty($_POST['oco_res_id']) || empty($_POST['oco_tipo_ocorrencia']) || empty($_POST['oco_hora_inicio_realizado']) || empty($_POST['oco_hora_fim_realizado'])) {
        throw new Exception("Preencha os campos obrigatórios!");
      }

      // POST
      $oco_id       = bin2hex(random_bytes(16)); // base para gerar IDs únicos por data
      $oco_codigo   = 'OC' . random_int(100000, 999999);
      $oco_solic_id = $_POST['solic_id'];
      $oco_res_id   = $_POST['oco_res_id'];
      // PROCESSA OS CHECKBOXES COMO STRING
      $oco_tipo_ocorrencia = isset($_POST['oco_tipo_ocorrencia']) && is_array($_POST['oco_tipo_ocorrencia'])
        ? implode(', ', array_map('htmlspecialchars', $_POST['oco_tipo_ocorrencia']))
        : null;
      //
      $oco_hora_inicio_realizado = $_POST['oco_hora_inicio_realizado'];
      $oco_hora_fim_realizado    = $_POST['oco_hora_fim_realizado'];
      $oco_obs                   = trim($_POST['oco_obs']) !== '' ? nl2br(trim($_POST['oco_obs'])) : NULL;
    }

    $rvm_admin_id = $_SESSION['reservm_admin_id'];


    // -------------------------------
    // CADASTRO
    // -------------------------------
    if ($acao === 'cadastrar') {

      $log_acao = 'Cadastro';

      $sql = "INSERT INTO ocorrencias (
                                        oco_id,
                                        oco_codigo,
                                        oco_solic_id,
                                        oco_res_id,
                                        oco_tipo_ocorrencia,
                                        oco_hora_inicio_realizado,
                                        oco_hora_fim_realizado,
                                        oco_obs,
                                        oco_user_id,
                                        oco_data_cad,
                                        oco_data_upd
                                      ) VALUES (
                                        :oco_id,
                                        :oco_codigo,
                                        :oco_solic_id,
                                        :oco_res_id,
                                        :oco_tipo_ocorrencia,
                                        :oco_hora_inicio_realizado,
                                        :oco_hora_fim_realizado,
                                        :oco_obs,
                                        :oco_user_id,
                                        GETDATE(),
                                        GETDATE()
                                      )";
      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':oco_id'                    => $oco_id,
        ':oco_codigo'                => $oco_codigo,
        ':oco_solic_id'              => $oco_solic_id,
        ':oco_res_id'                => $oco_res_id,
        ':oco_tipo_ocorrencia'       => $oco_tipo_ocorrencia,
        ':oco_hora_inicio_realizado' => $oco_hora_inicio_realizado,
        ':oco_hora_fim_realizado'    => $oco_hora_fim_realizado,
        ':oco_obs'                   => $oco_obs,
        ':oco_user_id'               => $rvm_admin_id
      ]);






      // -------------------------------
      // ATUALIZAR
      // -------------------------------
    } elseif ($acao === 'atualizar') {

      if (empty($_POST['oco_id'])) {
        throw new Exception("Erro ao obter o ID!");
      }
      $oco_id   = $_POST['oco_id'];
      $log_acao = 'Atualização';

      $sql = "UPDATE ocorrencias SET 
                                      oco_res_id                = :oco_res_id,
                                      oco_tipo_ocorrencia       = :oco_tipo_ocorrencia,
                                      oco_hora_inicio_realizado = :oco_hora_inicio_realizado,
                                      oco_hora_fim_realizado    = :oco_hora_fim_realizado,
                                      oco_obs                   = :oco_obs,
                                      oco_user_id               = :oco_user_id,
                                      oco_data_upd              = GETDATE()
                                WHERE
                                      oco_id = :oco_id";

      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':oco_id'                    => $oco_id,
        ':oco_res_id'                => $oco_res_id,
        ':oco_tipo_ocorrencia'       => $oco_tipo_ocorrencia,
        ':oco_hora_inicio_realizado' => $oco_hora_inicio_realizado,
        ':oco_hora_fim_realizado'    => $oco_hora_fim_realizado,
        ':oco_obs'                   => $oco_obs,
        ':oco_user_id'               => $rvm_admin_id
      ]);





      // -------------------------------
      // EXCLUIR
      // -------------------------------
    } elseif ($_GET['acao'] === 'deletar') {

      if (empty($_GET['oco_id'])) {
        throw new Exception("ID é obrigatório para exclusão.");
      }
      $oco_id   = $_GET['oco_id'];
      $log_acao = 'Exclusão';

      $sql = "DELETE FROM ocorrencias WHERE oco_id = :oco_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':oco_id' => $oco_id]);




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
      ':modulo'  => 'OCORRÊNCIAS',
      ':acao'    => $log_acao,
      ':acao_id' => $oco_id,
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
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    exit;
    // -------------------------------

  } catch (Exception $e) {
    $conn->rollBack();
    $_SESSION["erro"] = $e->getMessage();
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    exit;
  }
} else {
  $_SESSION["erro"] = "Requisição inválida.";
  header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  exit;
}
