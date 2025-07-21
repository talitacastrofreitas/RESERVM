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
      if (empty($_POST['compc_componente']) || empty($_POST['compc_curso'])) {
        throw new Exception("Preencha os campos obrigatórios!");
      }

      // POST
      $compc_componente = trim($_POST['compc_componente']);
      $compc_curso      = trim($_POST['compc_curso']);
      $compc_status     = $_POST['compc_status'] === '1' ? 1 : 0;
    }
    $rvm_admin_id = $_SESSION['reservm_admin_id'];


    // -------------------------------
    // CADASTRO
    // -------------------------------
    if ($acao === 'cadastrar') {

      $log_acao = 'Cadastro';

      // IMPEDE CADASTRO DUPLICADO
      $sqlVerifica = "SELECT COUNT(*) FROM componente_curricular WHERE compc_componente = :compc_componente AND compc_curso = :compc_curso";
      $stmtVerifica = $conn->prepare($sqlVerifica);
      $stmtVerifica->execute([':compc_componente' => $compc_componente, ':compc_curso' => $compc_curso]);
      $existe = $stmtVerifica->fetchColumn();
      if ($existe > 0) {
        throw new Exception("Componente curricular já cadastrada!");
      }

      $sql = "INSERT INTO componente_curricular (
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
                                                )";
      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':compc_componente' => $compc_componente,
        ':compc_curso'      => $compc_curso,
        ':compc_status'     => $compc_status,
        ':compc_user_id'    => $rvm_admin_id
      ]);

      // ÚLTIMO ID CADASTRADO
      if ($stmt->rowCount() > 0) {
        $compc_id = $conn->lastInsertId();
      } else {
        throw new Exception('Erro ao obter o último ID inserido.');
      }




      // -------------------------------
      // ATUALIZAR
      // -------------------------------
    } elseif ($acao === 'atualizar') {

      if (empty($_POST['compc_id'])) {
        throw new Exception("Erro ao obter o ID!");
      }
      $compc_id = (int) $_POST['compc_id'];
      $log_acao = 'Atualização';

      // IMPEDE CADASTRO DUPLICADO
      $sqlVerifica = "SELECT COUNT(*) FROM componente_curricular WHERE compc_componente = :compc_componente AND compc_curso = :compc_curso AND compc_id != :compc_id";
      $stmtVerifica = $conn->prepare($sqlVerifica);
      $stmtVerifica->execute([':compc_componente' => $compc_componente, ':compc_curso' => $compc_curso, ':compc_id' => $compc_id]);
      $existe = $stmtVerifica->fetchColumn();
      if ($existe > 0) {
        throw new Exception("Componente curricular já cadastrada!");
      }

      $sql = "UPDATE componente_curricular SET 
                                                compc_componente = UPPER(:compc_componente),
                                                compc_curso      = :compc_curso,
                                                compc_status     = :compc_status,
                                                compc_user_id    = :compc_user_id,
                                                compc_data_upd   = GETDATE()
                                          WHERE
                                                compc_id = :compc_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':compc_id'         => $compc_id,
        ':compc_componente' => $compc_componente,
        ':compc_curso'      => $compc_curso,
        ':compc_status'     => $compc_status,
        ':compc_user_id'    => $rvm_admin_id
      ]);




      // -------------------------------
      // EXCLUIR
      // -------------------------------
    } elseif ($_GET['acao'] === 'deletar') {

      if (empty($_GET['compc_id'])) {
        throw new Exception("ID é obrigatório para exclusão.");
      }
      $compc_id = (int) $_GET['compc_id'];
      $log_acao = 'Exclusão';

      // SE DADO ESTIVER SENDO USADO EM ALGUM CADASTRO, NÃO PODE SER EXCLUÍDO
      $sql = $conn->prepare("SELECT COUNT(*) FROM solicitacao WHERE CHARINDEX(:solic_comp_curric, solic_comp_curric) > 0");
      $sql->execute([':solic_comp_curric' => $compc_id]);
      $existe = $sql->fetchColumn();
      if ($existe > 0) {
        throw new Exception("Este registro não pode ser excluído!");
      }
      // -------------------------------

      $sql = "DELETE FROM componente_curricular WHERE compc_id = :compc_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':compc_id' => $compc_id]);




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
      ':modulo'  => 'COMPONENTE CURRICULAR',
      ':acao'    => $log_acao,
      ':acao_id' => $compc_id,
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
    header("Location: ../admin/componente_curricular.php");
    exit;
    // -------------------------------

  } catch (Exception $e) {
    $conn->rollBack();
    $_SESSION["erro"] = $e->getMessage();
    $_SESSION["form_compc"] = $_POST; // CRIA SESSÃO PARA OS DADOS DO FORMULÁRIO QUE FORAM ENVIADOS
    header("Location: ../admin/componente_curricular.php");
    exit;
  }
} else {
  $_SESSION["erro"] = "Requisição inválida.";
  header("Location: ../admin/componente_curricular.php");
  exit;
}
