<?php
session_start();
include '../../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// if ($_SERVER["REQUEST_METHOD"] === "POST") {}

/*****************************************************************************************
                            CADASTRAR CONFIGURAÇÕES
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "CadConfig") {

  $propc_id      = $_POST['propc'];
  $propc_msg     = trim($_POST['prop_conf_msg']) !== '' ? nl2br(trim($_POST['prop_conf_msg'])) : NULL;
  $reservm_admin_id = $_SESSION['reservm_admin_id'];
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO

    // ATUALIZA OS DADOS
    $sql = "UPDATE propostas_categorias SET propc_status = :propc_status, propc_msg = :propc_msg WHERE propc_id = :propc_id";
    $stmt = $conn->prepare($sql);

    // INICIALIZA TODOS OS REGISTROS COM 0 ANTES DE APLICAR AS ATUALIZAÇÕES DOS CHECHBOXES
    $resetSql = "UPDATE propostas_categorias SET propc_status = 0, propc_msg = :propc_msg";
    $stmtReset = $conn->prepare($resetSql);
    $stmtReset->execute([':propc_msg' => $propc_msg]);

    // LOOP
    foreach ($propc_id as $id => $valor) {
      $stmt->execute([
        ':propc_status' => $valor,
        ':propc_msg' => $propc_msg,
        ':propc_id' => $id
      ]);
    }

    // REGISTRA AÇÃO NO LOG
    // $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
    //                         VALUES (:modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    // $stmt->execute([
    //   ':modulo'    => 'CONFIGURAÇÕES',
    //   ':acao'      => 'CADASTRO',
    //   ':acao_id'   => $propc_id,
    //   ':dados'     => 'ID; ' . $id . '; Status: ' . $valor . '; Mensagem: ' . $propc_msg,
    //   ':user_id'   => $reservm_admin_id
    // ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Configuração realizada!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    // echo "Erro: " . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Configuração não realizado!" . $e->getMessage();
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
  // }
}
