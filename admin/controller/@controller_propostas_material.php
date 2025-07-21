<?php
session_start();
include '../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                          CADASTRAR MATERIAIS
 *****************************************************************************************/
if (isset($dados['CadMaterial'])) {

  $pm_id          = md5(uniqid(rand(), true)); // GERA UM ID UNICO
  $pm_proposta_id = $_POST['pm_proposta_id'];
  //
  $pm_natureza    = $_POST['pm_natureza'];
  $pm_material    = trim($_POST['pm_material']);
  $pm_quantidade  = $_POST['pm_quantidade'];
  $pm_obs         = nl2br(trim($_POST['pm_obs']));
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_material WHERE pm_material = :pm_material AND pm_proposta_id = :pm_proposta_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":pm_proposta_id", $pm_proposta_id);
  $stmt->bindParam(":pm_material", $pm_material);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este item já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#pm_ancora");
    return die;
  }
  // -------------------------------

  try {
    $sql = "INSERT INTO propostas_material (
                                              pm_id,
                                              pm_proposta_id,
                                              pm_natureza,
                                              pm_material,
                                              pm_quantidade,
                                              pm_obs,
                                              pm_user_id,
                                              pm_data_cad,
                                              pm_data_upd
                                            ) VALUES (
                                              :pm_id,
                                              :pm_proposta_id,
                                              :pm_natureza,
                                              UPPER(:pm_material),
                                              :pm_quantidade,
                                              :pm_obs,
                                              :pm_user_id,
                                              :pm_data_cad,
                                              :pm_data_upd
                                            )";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":pm_id", $pm_id);
    $stmt->bindParam(":pm_proposta_id", $pm_proposta_id);
    //
    $stmt->bindParam(":pm_natureza", $pm_natureza);
    $stmt->bindParam(":pm_material", $pm_material);
    $stmt->bindParam(":pm_quantidade", $pm_quantidade);
    $stmt->bindParam(":pm_obs", $pm_obs);
    //
    $stmt->bindParam(":pm_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":pm_data_cad", date('Y-m-d H:i:s'));
    $stmt->bindParam(":pm_data_upd", date('Y-m-d H:i:s'));
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'PROPOSTA - MATERIAIS',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $pm_id,
      ':dados'      => 'ID Proposta: ' . $pm_proposta_id . '; Natureza: ' . $pm_natureza . '; Material: ' . $pm_material . '; Quantidade: ' . $pm_quantidade . '; Obs: ' . $pm_obs,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => date('Y-m-d H:i:s')
    ));
    // -------------------------------

    $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Cadastro realizado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#pm_ancora");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Cadastro não realizado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}











/*****************************************************************************************
                              EDITAR MATERIAIS
 *****************************************************************************************/
if (isset($dados['EditMaterial'])) {

  $pm_id          = $_POST['pm_id'];
  $pm_proposta_id = $_POST['pm_proposta_id'];
  //
  $pm_natureza    = $_POST['pm_natureza'];
  $pm_material    = trim($_POST['pm_material']);
  $pm_quantidade  = $_POST['pm_quantidade'];
  $pm_obs         = nl2br(trim($_POST['pm_obs']));
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_material WHERE pm_material = :pm_material AND pm_proposta_id = :pm_proposta_id AND pm_id != :pm_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":pm_id", $pm_id);
  $stmt->bindParam(":pm_proposta_id", $pm_proposta_id);
  $stmt->bindParam(":pm_material", $pm_material);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este item já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#pm_ancora");
    return die;
  }
  // -------------------------------

  try {
    $sql = "UPDATE
                    propostas_material
              SET
                    pm_natureza   = :pm_natureza,
                    pm_material   = UPPER(:pm_material),
                    pm_quantidade = :pm_quantidade,
                    pm_obs        = :pm_obs,
                    pm_user_id    = :pm_user_id,
                    pm_data_upd   = :pm_data_upd
              WHERE
                    pm_id = :pm_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":pm_id", $pm_id);
    //
    $stmt->bindParam(":pm_natureza", $pm_natureza);
    $stmt->bindParam(":pm_material", $pm_material);
    $stmt->bindParam(":pm_quantidade", $pm_quantidade);
    $stmt->bindParam(":pm_obs", $pm_obs);
    //
    $stmt->bindParam(":pm_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":pm_data_upd", date('Y-m-d H:i:s'));
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'PROPOSTA - MATERIAIS',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $pm_id,
      ':dados'      => 'ID Proposta: ' . $pm_proposta_id . '; Natureza: ' . $pm_natureza . '; Material: ' . $pm_material . '; Quantidade: ' . $pm_quantidade . '; Obs: ' . $pm_obs,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => date('Y-m-d H:i:s')
    ));
    // -------------------------------

    $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados atualizados!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#pm_ancora");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados não atualizados!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}













/*****************************************************************************************
                              EXCLUIR MATERIAIS
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_material") {

  $pm_id = $_GET['pm_id'];

  try {
    $sql = "DELETE FROM propostas_material WHERE pm_id = :pm_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':pm_id', $pm_id);
    $stmt->execute();

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {
      $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i>Dados excluídos!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#pm_ancora");

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_acao_user_nome, log_data )
                              VALUES ( :modulo, :acao, :acao_id, :user_id, :user_nome, :data )');
      $stmt->execute(array(
        ':modulo'    => 'PROPOSTA - MATERIAIS',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $pm_id,
        ':user_id'   => $_SESSION['reservm_admin_id'],
        ':user_nome' => $_SESSION['reservm_admin_nome'],
        ':data'      => date('Y-m-d H:i:s')
      ));
      // -------------------------------

    } else {
      $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados não excluídos!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#pm_ancora");
    }
  } catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
  }

  $conn = null;
}
