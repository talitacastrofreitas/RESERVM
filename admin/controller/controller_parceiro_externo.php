<?php
session_start();
include '../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                          CADASTRAR COORDENADOR DO PROJETO
 *****************************************************************************************/
if (isset($dados['CadParceiroExterno'])) {

  $ppe_id            = md5(uniqid(rand(), true)); // GERA UM ID UNICO
  $ppe_proposta_id   = $_POST['ppe_proposta_id'];
  //
  $ppe_nome          = trim($_POST['ppe_nome']);
  $ppe_email         = trim($_POST['ppe_email']);
  $ppe_contato       = $_POST['ppe_contato'];
  $ppe_cnpj          = $_POST['ppe_cnpj'];
  $ppe_responsavel   = trim($_POST['ppe_responsavel']);
  $ppe_area_atuacao  = $_POST['ppe_area_atuacao'];
  $ppe_obs           = nl2br(trim($_POST['ppe_obs']));
  $ppe_convenio      = isset($_POST['ppe_convenio']) ? $_POST['ppe_convenio'] : 0;
  // -------------------------------


  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_parceiro_externo WHERE ppe_email = :ppe_email AND ppe_proposta_id = :ppe_proposta_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":ppe_proposta_id", $ppe_proposta_id);
  $stmt->bindParam(":ppe_email", $ppe_email);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este e-mail já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#pe_ancora");
    return die;
  }
  // -------------------------------

  $sql = "SELECT COUNT(*) FROM propostas_parceiro_externo WHERE ppe_cnpj = :ppe_cnpj AND ppe_proposta_id = :ppe_proposta_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":ppe_proposta_id", $ppe_proposta_id);
  $stmt->bindParam(":ppe_cnpj", $ppe_cnpj);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este CNPJ já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#pe_ancora");
    return die;
  }
  // -------------------------------

  try {
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
                                                      :ppe_email,
                                                      :ppe_contato,
                                                      :ppe_cnpj,
                                                      UPPER(:ppe_responsavel),
                                                      UPPER(:ppe_area_atuacao),
                                                      :ppe_obs,
                                                      :ppe_convenio,
                                                      :ppe_user_id,
                                                      :ppe_data_cad,
                                                      :ppe_data_upd
                                                    )";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":ppe_id", $ppe_id);
    $stmt->bindParam(":ppe_proposta_id", $ppe_proposta_id);
    //
    $stmt->bindParam(":ppe_nome", $ppe_nome);
    $stmt->bindParam(":ppe_email", $ppe_email);
    $stmt->bindParam(":ppe_contato", $ppe_contato);
    $stmt->bindParam(":ppe_cnpj", $ppe_cnpj);
    $stmt->bindParam(":ppe_responsavel", $ppe_responsavel);
    $stmt->bindParam(":ppe_area_atuacao", $ppe_area_atuacao);
    $stmt->bindParam(":ppe_obs", $ppe_obs);
    $stmt->bindParam(":ppe_convenio", $ppe_convenio);
    //
    $stmt->bindParam(":ppe_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":ppe_data_cad", date('Y-m-d H:i:s'));
    $stmt->bindParam(":ppe_data_upd", date('Y-m-d H:i:s'));
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'PROPOSTA - PARCEIRO EXTERNO',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $ppe_id,
      ':dados'      => 'ID Proposta: ' . $ppe_proposta_id . '; Nome: ' . $ppe_nome . '; E-mail: ' . $ppe_email . '; Contato: ' . $ppe_contato . '; CNPJ: ' . $ppe_cnpj . '; Responsável: ' . $ppe_responsavel . '; Área: ' . $ppe_area_atuacao . '; OBS: ' . $ppe_obs . '; Convênio: ' . $ppe_convenio,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => date('Y-m-d H:i:s')
    ));
    // -------------------------------

    $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Cadastro realizado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#pe_ancora");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Cadastro não realizado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}








/*****************************************************************************************
                              EDITAR COORDENADOR DO PROJETO
 *****************************************************************************************/
if (isset($dados['EditParceiroExterno'])) {

  $ppe_id            = $_POST['ppe_id'];
  $ppe_proposta_id   = $_POST['ppe_proposta_id'];
  //
  $ppe_nome          = trim($_POST['ppe_nome']);
  $ppe_email         = trim($_POST['ppe_email']);
  $ppe_contato       = $_POST['ppe_contato'];
  $ppe_cnpj          = $_POST['ppe_cnpj'];
  $ppe_responsavel   = trim($_POST['ppe_responsavel']);
  $ppe_area_atuacao  = $_POST['ppe_area_atuacao'];
  $ppe_obs           = nl2br(trim($_POST['ppe_obs']));
  $ppe_convenio      = isset($_POST['ppe_convenio']) ? $_POST['ppe_convenio'] : 0;
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_parceiro_externo WHERE ppe_email = :ppe_email AND ppe_proposta_id = :ppe_proposta_id AND ppe_id != :ppe_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":ppe_id", $ppe_id);
  $stmt->bindParam(":ppe_proposta_id", $ppe_proposta_id);
  $stmt->bindParam(":ppe_email", $ppe_email);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este e-mail já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#pe_ancora");
    return die;
  }
  // -------------------------------

  $sql = "SELECT COUNT(*) FROM propostas_parceiro_externo WHERE ppe_cnpj = :ppe_cnpj AND ppe_proposta_id = :ppe_proposta_id AND ppe_id != :ppe_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":ppe_id", $ppe_id);
  $stmt->bindParam(":ppe_proposta_id", $ppe_proposta_id);
  $stmt->bindParam(":ppe_cnpj", $ppe_cnpj);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este CNPJ já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#pe_ancora");
    return die;
  }
  // -------------------------------

  try {
    $sql = "UPDATE
                    propostas_parceiro_externo
              SET
                    ppe_nome         = UPPER(:ppe_nome),
                    ppe_email        = :ppe_email,
                    ppe_contato      = :ppe_contato,
                    ppe_cnpj         = :ppe_cnpj,
                    ppe_responsavel  = :ppe_responsavel,
                    ppe_area_atuacao = UPPER(:ppe_area_atuacao),
                    ppe_obs          = :ppe_obs,
                    ppe_convenio     = :ppe_convenio,
                    ppe_user_id      = :ppe_user_id,
                    ppe_data_upd     = :ppe_data_upd
              WHERE
                    ppe_id = :ppe_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":ppe_id", $ppe_id);
    //
    $stmt->bindParam(":ppe_nome", $ppe_nome);
    $stmt->bindParam(":ppe_email", $ppe_email);
    $stmt->bindParam(":ppe_contato", $ppe_contato);
    $stmt->bindParam(":ppe_cnpj", $ppe_cnpj);
    $stmt->bindParam(":ppe_responsavel", $ppe_responsavel);
    $stmt->bindParam(":ppe_area_atuacao", $ppe_area_atuacao);
    $stmt->bindParam(":ppe_obs", $ppe_obs);
    $stmt->bindParam(":ppe_convenio", $ppe_convenio);
    //
    $stmt->bindParam(":ppe_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":ppe_data_upd", date('Y-m-d H:i:s'));
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'PROPOSTA - PARCEIRO EXTERNO',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $ppe_id,
      ':dados'      => 'ID Proposta: ' . $ppe_proposta_id . '; Nome: ' . $ppe_nome . '; E-mail: ' . $ppe_email . '; Contato: ' . $ppe_contato . '; CNPJ: ' . $ppe_cnpj . '; Responsável: ' . $ppe_responsavel . '; Área: ' . $ppe_area_atuacao . '; OBS: ' . $ppe_obs . '; Convênio: ' . $ppe_convenio,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => date('Y-m-d H:i:s')
    ));
    // -------------------------------

    $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados atualizados!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#pe_ancora");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados não atualizados!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}











/*****************************************************************************************
                              EXCLUIR COORDENADOR DO PROJETO
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_ppeo") {

  $ppe_id = $_GET['ppe_id'];

  try {
    $sql = "DELETE FROM propostas_parceiro_externo WHERE ppe_id = :ppe_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':ppe_id', $ppe_id);
    $stmt->execute();

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {
      $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i>Dados excluídos!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#pe_ancora");

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :user_id, :user_nome, :data )');
      $stmt->execute(array(
        ':modulo'    => 'PROPOSTA - PARCEIRO EXTERNO',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $ppe_id,
        ':user_id'   => $_SESSION['reservm_admin_id'],
        ':user_nome' => $_SESSION['reservm_admin_nome'],
        ':data'      => date('Y-m-d H:i:s')
      ));
      // -------------------------------

    } else {
      $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados não excluídos!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#pe_ancora");
    }
  } catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
  }
  $conn = null;
}
