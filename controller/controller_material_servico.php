<?php
session_start();
include '../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                                CADASTRAR MATERIAL / SERVIÇO
 *****************************************************************************************/
if (isset($dados['CadMaterialServico'])) {

  $cms_material_servico = trim($_POST['cms_material_servico']);
  $cms_natureza         = trim($_POST['cms_natureza']);
  $cms_valor            = trim($_POST['cms_valor']);
  $cms_status           = isset($_POST['cms_status']) ? $_POST['cms_status'] : 2;
  $data_real            = date('Y-m-d H:i:s');
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_material_servico WHERE cms_material_servico = :cms_material_servico AND cms_natureza = :cms_natureza";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":cms_material_servico", $cms_material_servico);
  $stmt->bindParam(":cms_natureza", $cms_natureza);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este item já foi cadastrado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $sql = "INSERT INTO conf_material_servico (
                                                cms_material_servico,
                                                cms_natureza,
                                                cms_valor,
                                                cms_status,
                                                cms_user_id,
                                                cms_data_cad,
                                                cms_data_upd
                                              ) VALUES (
                                                UPPER(:cms_material_servico),
                                                UPPER(:cms_natureza),
                                                :cms_valor,
                                                :cms_status,
                                                :cms_user_id,
                                                :cms_data_cad,
                                                :cms_data_upd
                                              )";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":cms_material_servico", $cms_material_servico);
    $stmt->bindParam(":cms_natureza", $cms_natureza);
    $stmt->bindParam(":cms_valor", $cms_valor);
    $stmt->bindParam(":cms_status", $cms_status);
    //
    $stmt->bindParam(":cms_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":cms_data_cad", $data_real);
    $stmt->bindParam(":cms_data_upd", $data_real);
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'MATERIAIS E SERVIÇOS',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $last_id,
      ':dados'      => 'Item: ' . $cms_material_servico . '; Natureza: ' . $cms_natureza . '; Valor: ' . $cms_valor . '; Status: ' . $cms_status,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => $data_real
    ));
    // -------------------------------

    $_SESSION["msg"] = "Cadastro realizado com sucesso!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "Cadastro não realizado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
}











/*****************************************************************************************
                                EDITAR MATERIAL / SERVIÇO
 *****************************************************************************************/
if (isset($dados['EditMaterialServico'])) {

  $cms_id               = trim($_POST['cms_id']);
  $cms_material_servico = trim($_POST['cms_material_servico']);
  $cms_natureza         = trim($_POST['cms_natureza']);
  $cms_valor            = trim($_POST['cms_valor']);
  $cms_status           = isset($_POST['cms_status']) ? $_POST['cms_status'] : 2;
  $data_real            = date('Y-m-d H:i:s');
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_material_servico WHERE cms_material_servico = :cms_material_servico AND cms_natureza = :cms_natureza AND cms_id != :cms_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":cms_id", $cms_id);
  $stmt->bindParam(":cms_material_servico", $cms_material_servico);
  $stmt->bindParam(":cms_natureza", $cms_natureza);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este item já foi cadastrado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $sql = "UPDATE
                    conf_material_servico
              SET
                    cms_material_servico  = UPPER(:cms_material_servico),
                    cms_natureza          = UPPER(:cms_natureza),
                    cms_valor             = :cms_valor,
                    cms_status            = :cms_status,
                    cms_user_id           = :cms_user_id,
                    cms_data_upd          = :cms_data_upd
              WHERE
                    cms_id = :cms_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":cms_id", $cms_id);
    //
    $stmt->bindParam(":cms_material_servico", $cms_material_servico);
    $stmt->bindParam(":cms_natureza", $cms_natureza);
    $stmt->bindParam(":cms_valor", $cms_valor);
    $stmt->bindParam(":cms_status", $cms_status);
    //
    $stmt->bindParam(":cms_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":cms_data_upd", $data_real);
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'MATERIAIS E SERVIÇOS',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $cms_id,
      ':dados'      => 'Item: ' . $cms_material_servico . '; Natureza: ' . $cms_natureza . '; Valor: ' . $cms_valor . '; Status: ' . $cms_status,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => $data_real
    ));
    // -------------------------------

    $_SESSION["msg"] = "Dados atualizados com sucesso!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "Dados não atualizado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
}








/*****************************************************************************************
                                EXCLUIR MATERIAL / SERVIÇO
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_cms") {

  $cms_id    = $_GET['cms_id'];
  $data_real = date('Y-m-d H:i:s');

  try {
    $sql = "DELETE FROM conf_material_servico WHERE cms_id = :cms_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':cms_id', $cms_id);
    $stmt->execute();

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {
      $_SESSION["msg"] = "Dados excluídos com sucesso!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :user_id, :user_nome, :data )');
      $stmt->execute(array(
        ':modulo'    => 'MATERIAIS E SERVIÇOS',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $cms_id,
        ':user_id'   => $_SESSION['reservm_admin_id'],
        ':user_nome' => $_SESSION['reservm_admin_nome'],
        ':data'      => $data_real
      ));
      // -------------------------------

    } else {
      $_SESSION["erro"] = "Dados não excluídos!";
      echo "<script> history.go(-1);</script>";
      return die;
    }
  } catch (PDOException $e) {
    //echo "Erro: " . $e->getMessage();
    $_SESSION["erro"] = "Dados não excluídos!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  $conn = null;
}
