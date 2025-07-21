<?php
session_start();
include '../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                          CADASTRAR PARCEIRO / PATROCINADORES
 *****************************************************************************************/
if (isset($dados['CadParceiroExterno'])) {

  $ppe_id           = md5(uniqid(rand(), true)); // GERA UM ID UNICO
  $ppe_proposta_id  = base64_decode($_POST['ppe_proposta_id']);
  //
  $ppe_nome         = trim($_POST['ppe_nome']) !== '' ? trim($_POST['ppe_nome']) : NULL;
  $ppe_email        = trim($_POST['ppe_email']) !== '' ? trim($_POST['ppe_email']) : NULL;
  $ppe_contato      = trim($_POST['ppe_contato']) !== '' ? trim($_POST['ppe_contato']) : NULL;
  $ppe_cnpj         = trim($_POST['ppe_cnpj']) !== '' ? trim($_POST['ppe_cnpj']) : NULL;
  $ppe_responsavel  = trim($_POST['ppe_responsavel']) !== '' ? trim($_POST['ppe_responsavel']) : NULL;
  $ppe_area_atuacao = trim($_POST['ppe_area_atuacao']) !== '' ? trim($_POST['ppe_area_atuacao']) : NULL;
  $ppe_obs          = trim($_POST['ppe_obs']) !== '' ? nl2br(trim($_POST['ppe_obs'])) : NULL;
  $ppe_convenio     = trim(isset($_POST['ppe_convenio'])) ? $_POST['ppe_convenio'] : 0;
  $reservm_user_id     = $_SESSION['reservm_user_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_parceiro_externo WHERE ppe_nome = :ppe_nome AND ppe_proposta_id = :ppe_proposta_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':ppe_nome'        => $ppe_nome,
    ':ppe_proposta_id' => $ppe_proposta_id
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este nome já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  }
  // -------------------------------

  $sql = "SELECT COUNT(*) FROM propostas_parceiro_externo WHERE ppe_email = :ppe_email AND ppe_proposta_id = :ppe_proposta_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':ppe_email'       => $ppe_email,
    ':ppe_proposta_id' => $ppe_proposta_id
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este e-mail já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  }
  // -------------------------------

  $sql = "SELECT COUNT(*) FROM propostas_parceiro_externo WHERE ppe_cnpj = :ppe_cnpj AND ppe_proposta_id = :ppe_proposta_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':ppe_cnpj'        => $ppe_cnpj,
    ':ppe_proposta_id' => $ppe_proposta_id
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este CNPJ já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "INSERT INTO propostas_parceiro_externo (
                                                      ppe_id,
                                                      ppe_proposta_id,
                                                      ppe_nome,
                                                      ppe_email,
                                                      ppe_contato,
                                                      ppe_cnpj,
                                                      ppe_responsavel,
                                                      ppe_area_atuacao,
                                                      ppe_obs,
                                                      ppe_convenio,
                                                      ppe_user_id,
                                                      ppe_data_cad,
                                                      ppe_data_upd
                                                    ) VALUES (
                                                      :ppe_id,
                                                      :ppe_proposta_id,
                                                      UPPER(:ppe_nome),
                                                      LOWER(:ppe_email),
                                                      :ppe_contato,
                                                      :ppe_cnpj,
                                                      UPPER(:ppe_responsavel),
                                                      UPPER(:ppe_area_atuacao),
                                                      :ppe_obs,
                                                      :ppe_convenio,
                                                      :ppe_user_id,
                                                      GETDATE(),
                                                      GETDATE()
                                                    )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':ppe_id' => $ppe_id,
      ':ppe_proposta_id' => $ppe_proposta_id,
      ':ppe_nome' => $ppe_nome,
      ':ppe_email' => $ppe_email,
      ':ppe_contato' => $ppe_contato,
      ':ppe_cnpj' => $ppe_cnpj,
      ':ppe_responsavel' => $ppe_responsavel,
      ':ppe_area_atuacao' => $ppe_area_atuacao,
      ':ppe_obs' => $ppe_obs,
      ':ppe_convenio' => $ppe_convenio,
      ':ppe_user_id' => $reservm_user_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'PROPOSTA - PARCEIRO / PATROCINADORES',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $ppe_id,
      ':dados'      =>
      'ID Proposta: ' . $ppe_proposta_id .
        '; INstituicao: ' . $ppe_nome .
        '; E-mail: ' . $ppe_email .
        '; Contato Responsavel: ' . $ppe_contato .
        '; CNPJ: ' . $ppe_cnpj .
        '; Responsavel: ' . $ppe_responsavel .
        '; Area Atuacao: ' . $ppe_area_atuacao .
        '; Obs: ' . $ppe_obs .
        '; Convenio: ' . $ppe_convenio,
      ':user_id'    => $reservm_user_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Cadastro realizado com sucesso!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#ppe_ancora");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Cadastro não realizado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
  }
}















/*****************************************************************************************
                              EDITAR PARCEIRO / PATROCINADORES
 *****************************************************************************************/
if (isset($dados['EditParceiroExterno'])) {

  $ppe_id           = base64_decode($_POST['ppe_id']);
  $ppe_proposta_id  = base64_decode($_POST['ppe_proposta_id']);
  //
  $ppe_nome         = trim($_POST['ppe_nome']) !== '' ? trim($_POST['ppe_nome']) : NULL;
  $ppe_email        = trim($_POST['ppe_email']) !== '' ? trim($_POST['ppe_email']) : NULL;
  $ppe_contato      = trim($_POST['ppe_contato']) !== '' ? trim($_POST['ppe_contato']) : NULL;
  $ppe_cnpj         = trim($_POST['ppe_cnpj']) !== '' ? trim($_POST['ppe_cnpj']) : NULL;
  $ppe_responsavel  = trim($_POST['ppe_responsavel']) !== '' ? trim($_POST['ppe_responsavel']) : NULL;
  $ppe_area_atuacao = trim($_POST['ppe_area_atuacao']) !== '' ? trim($_POST['ppe_area_atuacao']) : NULL;
  $ppe_obs          = trim($_POST['ppe_obs']) !== '' ? nl2br(trim($_POST['ppe_obs'])) : NULL;
  $ppe_convenio     = trim(isset($_POST['ppe_convenio'])) ? $_POST['ppe_convenio'] : 0;
  $reservm_user_id     = $_SESSION['reservm_user_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_parceiro_externo WHERE ppe_nome = :ppe_nome AND ppe_proposta_id = :ppe_proposta_id AND ppe_id != :ppe_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':ppe_id'          => $ppe_id,
    ':ppe_nome'        => $ppe_nome,
    ':ppe_proposta_id' => $ppe_proposta_id
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este nome já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  }
  // -------------------------------

  $sql = "SELECT COUNT(*) FROM propostas_parceiro_externo WHERE ppe_email = :ppe_email AND ppe_proposta_id = :ppe_proposta_id AND ppe_id != :ppe_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':ppe_id'          => $ppe_id,
    ':ppe_email'       => $ppe_email,
    ':ppe_proposta_id' => $ppe_proposta_id
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este e-mail já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  }
  // -------------------------------

  $sql = "SELECT COUNT(*) FROM propostas_parceiro_externo WHERE ppe_cnpj = :ppe_cnpj AND ppe_proposta_id = :ppe_proposta_id AND ppe_id != :ppe_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':ppe_id'          => $ppe_id,
    ':ppe_cnpj'        => $ppe_cnpj,
    ':ppe_proposta_id' => $ppe_proposta_id
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este CNPJ já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "UPDATE
                    propostas_parceiro_externo
              SET
                    ppe_nome         = UPPER(:ppe_nome),
                    ppe_email        = LOWER(:ppe_email),
                    ppe_contato      = :ppe_contato,
                    ppe_cnpj         = :ppe_cnpj,
                    ppe_responsavel  = UPPER(:ppe_responsavel),
                    ppe_area_atuacao = UPPER(:ppe_area_atuacao),
                    ppe_obs          = :ppe_obs,
                    ppe_convenio     = :ppe_convenio,
                    ppe_user_id      = :ppe_user_id,
                    ppe_data_upd     = GETDATE()
              WHERE
                    ppe_id = :ppe_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':ppe_id' => $ppe_id,
      ':ppe_nome' => $ppe_nome,
      ':ppe_email' => $ppe_email,
      ':ppe_contato' => $ppe_contato,
      ':ppe_cnpj' => $ppe_cnpj,
      ':ppe_responsavel' => $ppe_responsavel,
      ':ppe_area_atuacao' => $ppe_area_atuacao,
      ':ppe_obs' => $ppe_obs,
      ':ppe_convenio' => $ppe_convenio,
      ':ppe_user_id' => $reservm_user_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'PROPOSTA - PARCEIRO / PATROCINADORES',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $ppe_id,
      ':dados'      =>
      'ID Proposta: ' . $ppe_proposta_id .
        '; INstituicao: ' . $ppe_nome .
        '; E-mail: ' . $ppe_email .
        '; Contato Responsavel: ' . $ppe_contato .
        '; CNPJ: ' . $ppe_cnpj .
        '; Responsavel: ' . $ppe_responsavel .
        '; Area Atuacao: ' . $ppe_area_atuacao .
        '; Obs: ' . $ppe_obs .
        '; Convenio: ' . $ppe_convenio,
      ':user_id'    => $reservm_user_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Dados atualizados com sucesso!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#ppe_ancora");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Dados não atualizados!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
  }
}













/*****************************************************************************************
                              EXCLUIR PARCEIRO / PATROCINADORES
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_ppeo") {

  $ppe_id       = base64_decode($_GET['ppe_id']);
  $reservm_user_id = $_SESSION['reservm_user_id'];

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "DELETE FROM propostas_parceiro_externo WHERE ppe_id = :ppe_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':ppe_id' => $ppe_id]);

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {

      // REGISTRA AÇÃO NO LOG
      $stmt_log = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                                  VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt_log->execute([
        ':modulo'    => 'PROPOSTA - PARCEIRO / PATROCINADORES',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $ppe_id,
        ':user_id'   => $reservm_user_id
      ]);
      // -------------------------------

      $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

      $_SESSION["msg"] = "Dados excluídos com sucesso!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#ppe_ancora");
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
