<?php
session_start();
include '../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                          CADASTRAR MATERIAL DE CONSUMO
 *****************************************************************************************/
if (isset($dados['CadMaterialConsumo'])) {

  $pmc_id               = md5(uniqid(rand(), true)); // GERA UM ID UNICO
  $pmc_proposta_id      = $_POST['pmc_proposta_id'];
  //
  $pmc_material_consumo = trim($_POST['pmc_material_consumo']);
  $pmc_quantidade       = $_POST['pmc_quantidade'];
  $pmc_obs              = nl2br(trim($_POST['pmc_obs']));
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_material_consumo WHERE pmc_material_consumo = :pmc_material_consumo AND pmc_proposta_id = :pmc_proposta_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":pmc_proposta_id", $pmc_proposta_id);
  $stmt->bindParam(":pmc_material_consumo", $pmc_material_consumo);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este item já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#pmc_ancora");
    return die;
  }
  // -------------------------------

  try {
    $sql = "INSERT INTO propostas_material_consumo (
                                                      pmc_id,
                                                      pmc_proposta_id,
                                                      pmc_material_consumo,
                                                      pmc_quantidade,
                                                      pmc_obs,
                                                      pmc_user_id,
                                                      pmc_data_cad,
                                                      pmc_data_upd
                                                    ) VALUES (
                                                      :pmc_id,
                                                      :pmc_proposta_id,
                                                      UPPER(:pmc_material_consumo),
                                                      :pmc_quantidade,
                                                      :pmc_obs,
                                                      :pmc_user_id,
                                                      :pmc_data_cad,
                                                      :pmc_data_upd
                                                    )";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":pmc_id", $pmc_id);
    $stmt->bindParam(":pmc_proposta_id", $pmc_proposta_id);
    //
    $stmt->bindParam(":pmc_material_consumo", $pmc_material_consumo);
    $stmt->bindParam(":pmc_quantidade", $pmc_quantidade);
    $stmt->bindParam(":pmc_obs", $pmc_obs);
    //
    $stmt->bindParam(":pmc_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":pmc_data_cad", date('Y-m-d H:i:s'));
    $stmt->bindParam(":pmc_data_upd", date('Y-m-d H:i:s'));
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'PROPOSTA - MATERIAIS DE CONSUMO',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $pmc_id,
      ':dados'      => 'ID Proposta: ' . $pmc_proposta_id . '; Material: ' . $pmc_material_consumo . '; Quantidade: ' . $pmc_quantidade . '; Obs: ' . $pmc_obs,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => date('Y-m-d H:i:s')
    ));
    // -------------------------------

    $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Cadastro realizado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#pmc_ancora");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Cadastro não realizado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}










/*****************************************************************************************
                              EDITAR MATERIAL DE CONSUMO
 *****************************************************************************************/
if (isset($dados['EditMaterialConsumo'])) {

  $pmc_id               = $_POST['pmc_id'];
  $pmc_proposta_id      = $_POST['pmc_proposta_id'];
  //
  $pmc_material_consumo = trim($_POST['pmc_material_consumo']);
  $pmc_quantidade       = $_POST['pmc_quantidade'];
  $pmc_obs              = nl2br(trim($_POST['pmc_obs']));
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_material_consumo WHERE pmc_material_consumo = :pmc_material_consumo AND pmc_proposta_id = :pmc_proposta_id AND pmc_id != :pmc_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":pmc_id", $pmc_id);
  $stmt->bindParam(":pmc_proposta_id", $pmc_proposta_id);
  $stmt->bindParam(":pmc_material_consumo", $pmc_material_consumo);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este item já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#pmc_ancora");
    return die;
  }
  // -------------------------------

  try {
    $sql = "UPDATE
                    propostas_material_consumo
              SET
                    pmc_material_consumo = UPPER(:pmc_material_consumo),
                    pmc_quantidade       = :pmc_quantidade,
                    pmc_obs              = :pmc_obs,
                    pmc_user_id          = :pmc_user_id,
                    pmc_data_upd         = :pmc_data_upd
              WHERE
                    pmc_id = :pmc_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":pmc_id", $pmc_id);
    //
    $stmt->bindParam(":pmc_material_consumo", $pmc_material_consumo);
    $stmt->bindParam(":pmc_quantidade", $pmc_quantidade);
    $stmt->bindParam(":pmc_obs", $pmc_obs);
    //
    $stmt->bindParam(":pmc_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":pmc_data_upd", date('Y-m-d H:i:s'));
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'PROPOSTA - MATERIAIS DE CONSUMO',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $pmc_id,
      ':dados'      => 'ID Proposta: ' . $pmc_proposta_id . '; Material: ' . $pmc_material_consumo . '; Quantidade: ' . $pmc_quantidade . '; Obs: ' . $pmc_obs,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => date('Y-m-d H:i:s')
    ));
    // -------------------------------

    $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados atualizados!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#pmc_ancora");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados não atualizados!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}










/*****************************************************************************************
                              EXCLUIR MATERIAL DE CONSUMO
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_mat_consumo") {

  $pmc_id = $_GET['pmc_id'];

  try {
    $sql = "DELETE FROM propostas_material_consumo WHERE pmc_id = :pmc_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':pmc_id', $pmc_id);
    $stmt->execute();

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {
      $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i>Dados excluídos!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#pmc_ancora");

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_acao_user_nome, log_data )
    VALUES ( :modulo, :acao, :acao_id, :user_id, :user_nome, :data )');
      $stmt->execute(array(
        ':modulo'    => 'PROPOSTA - MATERIAIS DE CONSUMO',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $pmc_id,
        ':user_id'   => $_SESSION['reservm_admin_id'],
        ':user_nome' => $_SESSION['reservm_admin_nome'],
        ':data'      => date('Y-m-d H:i:s')
      ));
      // -------------------------------

    } else {
      $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados não excluídos!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#pmc_ancora");
    }
  } catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
  }
  $conn = null;
}
