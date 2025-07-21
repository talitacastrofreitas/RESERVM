<?php
session_start();
include '../../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                                CADASTRAR MATERIAL / SERVIÇO
 *****************************************************************************************/
if (isset($dados['CadMaterialServico'])) {

  $cms_material_servico = trim($_POST['cms_material_servico']) !== '' ? trim($_POST['cms_material_servico']) : NULL;
  $cms_natureza         = trim($_POST['cms_natureza']) !== '' ? trim($_POST['cms_natureza']) : NULL;
  $cms_valor            = trim($_POST['cms_valor']) !== '' ? trim($_POST['cms_valor']) : NULL;
  $cms_status           = trim(isset($_POST['cms_status'])) ? $_POST['cms_status'] : 0;
  $reservm_admin_id        = $_SESSION['reservm_admin_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_material_servico WHERE cms_material_servico = :cms_material_servico AND cms_natureza = :cms_natureza";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':cms_material_servico' => $cms_material_servico,
    ':cms_natureza'         => $cms_natureza
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este item já foi cadastrado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
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
                                                GETDATE(),
                                                GETDATE()
                                              )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':cms_material_servico' => $cms_material_servico,
      ':cms_natureza'         => $cms_natureza,
      ':cms_valor'            => $cms_valor,
      ':cms_status'           => $cms_status,
      ':cms_user_id'          => $reservm_admin_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'MATERIAIS E SERVIÇOS',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $last_id,
      ':dados'      => 'Item: ' . $cms_material_servico . '; Natureza: ' . $cms_natureza . '; Valor: ' . $cms_valor . '; Status: ' . $cms_status,
      ':user_id'    => $reservm_admin_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Cadastro realizado com sucesso!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Cadastro não realizado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
}











/*****************************************************************************************
                                EDITAR MATERIAL / SERVIÇO
 *****************************************************************************************/
if (isset($dados['EditMaterialServico'])) {

  $cms_id               = trim($_POST['cms_id']) !== '' ? trim($_POST['cms_id']) : NULL;
  $cms_material_servico = trim($_POST['cms_material_servico']) !== '' ? trim($_POST['cms_material_servico']) : NULL;
  $cms_natureza         = trim($_POST['cms_natureza']) !== '' ? trim($_POST['cms_natureza']) : NULL;
  $cms_valor            = trim($_POST['cms_valor']) !== '' ? trim($_POST['cms_valor']) : NULL;
  $cms_status           = trim(isset($_POST['cms_status'])) ? $_POST['cms_status'] : 0;
  $reservm_admin_id        = $_SESSION['reservm_admin_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_material_servico WHERE cms_material_servico = :cms_material_servico AND cms_natureza = :cms_natureza AND cms_id != :cms_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':cms_id'               => $cms_id,
    ':cms_material_servico' => $cms_material_servico,
    ':cms_natureza'         => $cms_natureza
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este item já foi cadastrado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "UPDATE
                    conf_material_servico
              SET
                    cms_material_servico  = UPPER(:cms_material_servico),
                    cms_natureza          = UPPER(:cms_natureza),
                    cms_valor             = :cms_valor,
                    cms_status            = :cms_status,
                    cms_user_id           = :cms_user_id,
                    cms_data_upd          = GETDATE()
              WHERE
                    cms_id = :cms_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':cms_id'               => $cms_id,
      ':cms_material_servico' => $cms_material_servico,
      ':cms_natureza'         => $cms_natureza,
      ':cms_valor'            => $cms_valor,
      ':cms_status'           => $cms_status,
      ':cms_user_id'          => $reservm_admin_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'MATERIAIS E SERVIÇOS',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $cms_id,
      ':dados'      => 'Item: ' . $cms_material_servico . '; Natureza: ' . $cms_natureza . '; Valor: ' . $cms_valor . '; Status: ' . $cms_status,
      ':user_id'    => $reservm_admin_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Dados atualizados com sucesso!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Dados não atualizado!";
    //echo "<script> history.go(-1);</script>";
    return die;
  }
}








/*****************************************************************************************
                                EXCLUIR MATERIAL / SERVIÇO
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_cms") {

  $cms_id        = $_GET['cms_id'];
  $reservm_admin_id = $_SESSION['reservm_admin_id'];

  // SE DADO ESTIVER SENDO USADO EM ALGUM CADASTRO, NÃO PODE SER EXCLUÍDO
  $sql = "SELECT * FROM propostas_material_consumo WHERE CHARINDEX(:pmc_material_consumo, pmc_material_consumo) > 0"; // VERIFICA SE ID ESTÁ DENTRO DO ARRAY
  $stmt = $conn->prepare($sql);
  $stmt->execute([':pmc_material_consumo' => $cms_id]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este registro não pode ser excluído!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "DELETE FROM conf_material_servico WHERE cms_id = :cms_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':cms_id' => $cms_id]);

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {

      // REGISTRA AÇÃO NO LOG
      $stmt_log = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                                  VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt_log->execute([
        ':modulo'  => 'MATERIAIS E SERVIÇOS',
        ':acao'    => 'EXCLUSÃO',
        ':acao_id' => $cms_id,
        ':user_id' => $reservm_admin_id
      ]);
      // -------------------------------

      $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

      $_SESSION["msg"] = "Dados excluídos com sucesso!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    } else {
      $conn->rollBack();
      $_SESSION["erro"] = "Dados não excluídos!";
      echo "<script> history.go(-1);</script>";
      return die;
    }
  } catch (PDOException $e) {
    //echo "Erro: " . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Dados não excluídos!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
}
