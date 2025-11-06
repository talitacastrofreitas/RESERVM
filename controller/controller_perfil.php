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

      $user_id         = $_POST['i'];
      $user_senha      = $_POST['user_senha'];
      $log_acao        = 'Exclusão Conta';
      $reservm_user_id = $_SESSION['reservm_user_id'];


      // BUSCA OS DADOS DO USUÁRIO COM O ID INFORMADO
      $result_user = $conn->prepare("SELECT user_id, user_senha FROM usuarios WHERE user_id = ?");
      $result_user->execute([$user_id]);
      $row_user = $result_user->fetch(PDO::FETCH_ASSOC);
      if (!$row_user) {
        throw new Exception("Dados não encontrados!");
      }

      // SENHA INCORRETO
      if (!password_verify($user_senha, $row_user['user_senha'])) {
        throw new Exception("Senha incorreta!");
      }

      $stmt = $conn->prepare('UPDATE
                                      usuarios
                                SET
                                      user_status           = :user_status,
                                      user_senha            = :user_senha,
                                      user_data_reset_senha = :user_data_reset_senha,
                                      user_data_acesso      = :user_data_acesso,
                                      nivel_acesso          = :nivel_acesso,
                                      user_user_id          = :user_user_id,
                                      user_data_upd         = GETDATE()
                                WHERE
                                      user_id = :user_id');
      $stmt->execute([
        ':user_id'               => $user_id,
        ':user_status'           => 2, // STATUS = 2 SIGNIFICA QUE O USUÁRIO FOI EXCLUÍDO
        ':user_senha'            => null,
        ':user_data_reset_senha' => null,
        ':user_data_acesso'      => null,
        ':nivel_acesso'           => 2, // 2 = NÍVEL DE ACESSO QUANDO A CONTA FOR EXCLUÍDA
        ':user_user_id'          => $reservm_user_id
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
      ':modulo'  => 'USUÁRIOS',
      ':acao'    => $log_acao,
      ':acao_id' => $user_id,
      ':dados'   => json_encode($log_dados, JSON_UNESCAPED_UNICODE),
      ':user_id' => $reservm_user_id
    ]);
    // -------------------------------

    $conn->commit(); // CONFIRMA A TRANSAÇÃO

    // CONFIGURAÇÃO DE MENSAGEM
    header("Location: ../sair.php");
    exit;
    // -------------------------------

  } catch (Exception $e) {
    $conn->rollBack();
    $_SESSION["erro"] = $e->getMessage();
    header("Location: ../perfil.php");
    exit;
  }
} else {
  $_SESSION["erro"] = "Requisição inválida.";
  header("Location: ../perfil.php");
  exit;
}
