<?php
// session_start();
include '../conexao/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $acao = $_POST['acao'];

    if ($acao === 'deletar_conta') {

      if (empty($_POST['i'])) {
        throw new Exception("ID é obrigatório para exclusão.");
      }

      $admin_id         = $_POST['i'];
      $admin_senha      = $_POST['admin_senha'];
      $log_acao         = 'Exclusão Conta';
      $reservm_admin_id = $_SESSION['reservm_admin_id'];


      // BUSCA OS DADOS DO USUÁRIO COM O ID INFORMADO
      $result_admin = $conn->prepare("SELECT admin_id, admin_senha FROM admin WHERE admin_id = ?");
      $result_admin->execute([$admin_id]);
      $row_admin = $result_admin->fetch(PDO::FETCH_ASSOC);
      if (!$row_admin) {
        throw new Exception("Dados não encontrados!");
      }

      // SENHA INCORRETO
      if (!password_verify($admin_senha, $row_admin['admin_senha'])) {
        throw new Exception("Senha incorreta!");
      }

      $stmt = $conn->prepare('UPDATE
                                      admin
                                SET
                                      admin_status           = :admin_status,
                                      admin_senha            = :admin_senha,
                                      admin_data_reset_senha = :admin_data_reset_senha,
                                      admin_data_acesso      = :admin_data_acesso,
                                      nivel_acesso           = :nivel_acesso,
                                      admin_user_id          = :admin_user_id,
                                      admin_data_upd         = GETDATE()
                                WHERE
                                      admin_id = :admin_id');
      $stmt->execute([
        ':admin_id'               => $admin_id,
        ':admin_status'           => 2, // STATUS = 2 SIGNIFICA QUE O ADMINISTRADOR FOI EXCLUÍDO
        ':admin_senha'            => null,
        ':admin_data_reset_senha' => null,
        ':admin_data_acesso'      => null,
        ':nivel_acesso'           => 2, // 2 = NÍVEL DE ACESSO QUANDO A CONTA FOR EXCLUÍDA
        ':admin_user_id'          => $reservm_admin_id
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
      ':modulo'  => 'ADMINISTRADOR',
      ':acao'    => $log_acao,
      ':acao_id' => $admin_id,
      ':dados'   => json_encode($log_dados, JSON_UNESCAPED_UNICODE),
      ':user_id' => $reservm_admin_id
    ]);
    // -------------------------------

    $conn->commit(); // CONFIRMA A TRANSAÇÃO

    // CONFIGURAÇÃO DE MENSAGEM
    header("Location: ../admin/sair.php");
    exit;
    // -------------------------------

  } catch (Exception $e) {
    $conn->rollBack();
    $_SESSION["erro"] = $e->getMessage();
    header("Location: ../admin/perfil.php");
    exit;
  }
} else {
  $_SESSION["erro"] = "Requisição inválida.";
  header("Location: ../admin/perfil.php");
  exit;
}
