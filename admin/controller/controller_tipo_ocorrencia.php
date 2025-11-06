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
      if (empty($_POST['cto_tipo_ocorrencia'])) {
        throw new Exception("Preencha os campos obrigatórios!");
      }

      // POST
      $cto_tipo_ocorrencia = trim($_POST['cto_tipo_ocorrencia']);
      $cto_status          = $_POST['cto_status'] === '1' ? 1 : 0;
    }
    $rvm_admin_id = $_SESSION['reservm_admin_id'];


    // -------------------------------
    // CADASTRO
    // -------------------------------
    if ($acao === 'cadastrar') {

      $log_acao = 'Cadastro';

      // IMPEDE CADASTRO DUPLICADO
      $sqlVerifica = "SELECT COUNT(*) FROM conf_tipo_ocorrencia WHERE cto_tipo_ocorrencia = :cto_tipo_ocorrencia";
      $stmtVerifica = $conn->prepare($sqlVerifica);
      $stmtVerifica->execute([':cto_tipo_ocorrencia' => $cto_tipo_ocorrencia]);
      $existe = $stmtVerifica->fetchColumn();
      if ($existe > 0) {
        throw new Exception("Tipo de ocorrência já cadastrada!");
      }

      $sql = "INSERT INTO conf_tipo_ocorrencia (
                                                cto_tipo_ocorrencia,
                                                cto_status, 
                                                cto_user_id,
                                                cto_data_cad,
                                                cto_data_upd
                                              ) VALUES (
                                                UPPER(:cto_tipo_ocorrencia),
                                                :cto_status,
                                                :cto_user_id,
                                                GETDATE(),
                                                GETDATE()
                                              )";
      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':cto_tipo_ocorrencia' => $cto_tipo_ocorrencia,
        ':cto_status'          => $cto_status,
        ':cto_user_id'         => $rvm_admin_id
      ]);

      // ÚLTIMO ID CADASTRADO
      if ($stmt->rowCount() > 0) {
        $cto_id = $conn->lastInsertId();
      } else {
        throw new Exception('Erro ao obter o último ID inserido.');
      }




      // -------------------------------
      // ATUALIZAR
      // -------------------------------
    } elseif ($acao === 'atualizar') {

      if (empty($_POST['cto_id'])) {
        throw new Exception("Erro ao obter o ID!");
      }
      $cto_id = (int) $_POST['cto_id'];
      $log_acao = 'Atualização';

      // IMPEDE CADASTRO DUPLICADO
      $sqlVerifica = "SELECT COUNT(*) FROM conf_tipo_ocorrencia WHERE cto_tipo_ocorrencia = :cto_tipo_ocorrencia AND cto_id != :cto_id";
      $stmtVerifica = $conn->prepare($sqlVerifica);
      $stmtVerifica->execute([':cto_tipo_ocorrencia' => $cto_tipo_ocorrencia, ':cto_id' => $cto_id]);
      $existe = $stmtVerifica->fetchColumn();
      if ($existe > 0) {
        throw new Exception("Tipo de ocorrência já cadastrada!");
      }

      $sql = "UPDATE conf_tipo_ocorrencia SET 
                                              cto_tipo_ocorrencia  = UPPER(:cto_tipo_ocorrencia),
                                              cto_status   = :cto_status,
                                              cto_user_id  = :cto_user_id,
                                              cto_data_upd = GETDATE()
                                        WHERE
                                              cto_id = :cto_id";

      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':cto_id'              => $cto_id,
        ':cto_tipo_ocorrencia' => $cto_tipo_ocorrencia,
        ':cto_status'          => $cto_status,
        ':cto_user_id'         => $rvm_admin_id
      ]);




      // -------------------------------
      // EXCLUIR
      // -------------------------------
    } elseif ($_GET['acao'] === 'deletar') {

      if (empty($_GET['cto_id'])) {
        throw new Exception("ID é obrigatório para exclusão.");
      }
      $cto_id = (int) $_GET['cto_id'];
      $log_acao = 'Exclusão';

      // SE DADO ESTIVER SENDO USADO EM ALGUM CADASTRO, NÃO PODE SER EXCLUÍDO
      $sql = $conn->prepare("SELECT COUNT(*) FROM ocorrencias WHERE ',' + REPLACE(oco_tipo_ocorrencia, ' ', '') + ',' LIKE ?");
      $sql->execute(['%,' . $cto_id . ',%']);
      $existe = $sql->fetchColumn();
      if ($existe > 0) {
        throw new Exception("Este registro não pode ser excluído!");
      }
      // -------------------------------

      $sql = "DELETE FROM conf_tipo_ocorrencia WHERE cto_id = :cto_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':cto_id' => $cto_id]);




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
      ':modulo'  => 'TIPO DE OCORRÊNCIA',
      ':acao'    => $log_acao,
      ':acao_id' => $cto_id,
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
    header("Location: ../admin/tipo_ocorrencia.php");
    exit;
    // -------------------------------

  } catch (Exception $e) {
    $conn->rollBack();
    $_SESSION["erro"] = $e->getMessage();
    $_SESSION["form_cto"] = $_POST; // CRIA SESSÃO PARA OS DADOS DO FORMULÁRIO QUE FORAM ENVIADOS
    header("Location: ../admin/tipo_ocorrencia.php");
    exit;
  }
} else {
  $_SESSION["erro"] = "Requisição inválida.";
  header("Location: ../admin/tipo_ocorrencia.php");
  exit;
}
