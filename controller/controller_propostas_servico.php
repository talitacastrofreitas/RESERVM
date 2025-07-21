<?php
session_start();
include '../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                          CADASTRAR SERVIÇO
 *****************************************************************************************/
if (isset($dados['CadServico'])) {

  $ps_id          = md5(uniqid(rand(), true)); // GERA UM ID UNICO
  $ps_proposta_id = base64_decode($_POST['ps_proposta_id']);
  $ps_mat_serv_id = trim($_POST['ps_mat_serv_id']) !== '' ? trim($_POST['ps_mat_serv_id']) : NULL;
  $ps_quantidade  = trim($_POST['ps_quantidade']) !== '' ? trim($_POST['ps_quantidade']) : NULL;
  $reservm_user_id   = $_SESSION['reservm_user_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_servico WHERE ps_mat_serv_id = :ps_mat_serv_id AND ps_proposta_id = :ps_proposta_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':ps_proposta_id' => $ps_proposta_id,
    ':ps_mat_serv_id' => $ps_mat_serv_id
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este serviço já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "INSERT INTO propostas_servico (
                                            ps_id,
                                            ps_proposta_id,
                                            ps_mat_serv_id,
                                            ps_quantidade,
                                            ps_user_id,
                                            ps_data_cad,
                                            ps_data_upd
                                          ) VALUES (
                                            :ps_id,
                                            :ps_proposta_id,
                                            UPPER(:ps_mat_serv_id),
                                            :ps_quantidade,
                                            :ps_user_id,
                                            GETDATE(),
                                            GETDATE()
                                          )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':ps_id' => $ps_id,
      ':ps_proposta_id' => $ps_proposta_id,
      ':ps_mat_serv_id' => $ps_mat_serv_id,
      ':ps_quantidade' => $ps_quantidade,
      ':ps_user_id' => $reservm_user_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'PROPOSTA - SERVIÇO',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $ps_id,
      ':dados'      => 'ID Proposta: ' . $ps_proposta_id .
        '; Serviço: ' . $ps_mat_serv_id .
        '; Quantidade: ' . $ps_quantidade,
      ':user_id'    => $reservm_user_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Cadastro realizado com sucesso!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#ps_ancora");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Cadastro não realizado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
  }
}











/*****************************************************************************************
                              EDITAR SERVIÇO
 *****************************************************************************************/
if (isset($dados['EditServico'])) {

  $ps_id          = base64_decode($_POST['ps_id']);
  $ps_proposta_id = base64_decode($_POST['ps_proposta_id']);
  $ps_mat_serv_id = trim($_POST['ps_mat_serv_id']) !== '' ? trim($_POST['ps_mat_serv_id']) : NULL;
  $ps_quantidade  = trim($_POST['ps_quantidade']) !== '' ? trim($_POST['ps_quantidade']) : NULL;
  $reservm_user_id   = $_SESSION['reservm_user_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_servico WHERE ps_mat_serv_id = :ps_mat_serv_id AND ps_proposta_id = :ps_proposta_id AND ps_id != :ps_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':ps_id'          => $ps_id,
    ':ps_proposta_id' => $ps_proposta_id,
    ':ps_mat_serv_id' => $ps_mat_serv_id
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este serviço já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "UPDATE
                    propostas_servico
              SET
                    ps_mat_serv_id = UPPER(:ps_mat_serv_id),
                    ps_quantidade  = :ps_quantidade,
                    ps_user_id     = :ps_user_id,
                    ps_data_upd    = GETDATE()
              WHERE
                    ps_id = :ps_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':ps_id' => $ps_id,
      ':ps_mat_serv_id' => $ps_mat_serv_id,
      ':ps_quantidade' => $ps_quantidade,
      ':ps_user_id' => $reservm_user_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'PROPOSTA - SERVIÇO',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $ps_id,
      ':dados'      => 'ID Proposta: ' . $ps_proposta_id .
        '; Serviço: ' . $ps_mat_serv_id .
        '; Quantidade: ' . $ps_quantidade,
      ':user_id'    => $reservm_user_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Dados atualizados com sucesso!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#ps_ancora");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Dados não atualizados!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
  }
}













/*****************************************************************************************
                              EXCLUIR SERVIÇO
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_servico") {

  $ps_id       = base64_decode($_GET['ps_id']);
  $reservm_user_id = $_SESSION['reservm_user_id'];

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "DELETE FROM propostas_servico WHERE ps_id = :ps_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':ps_id' => $ps_id]);

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {

      // REGISTRA AÇÃO NO LOG
      $stmt_log = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                                  VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt_log->execute([
        ':modulo'    => 'PROPOSTA - SERVIÇO',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $ps_id,
        ':user_id'   => $reservm_user_id
      ]);
      // -------------------------------

      $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

      $_SESSION["msg"] = "Dados excluídos com sucesso!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#pmc_ancora");
    } else {
      $conn->rollBack();
      $_SESSION["erro"] = "Dados não excluídos!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#top_ancora");
    }
  } catch (PDOException $e) {
    //echo "Erro: " . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Dados não excluídos!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
  }
}
