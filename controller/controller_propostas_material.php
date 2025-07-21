<?php
session_start();
include '../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                          CADASTRAR MATERIAIS
 *****************************************************************************************/
if (isset($dados['CadMaterial'])) {

  $pmc_id               = md5(uniqid(rand(), true)); // GERA UM ID UNICO
  $pmc_proposta_id      = base64_decode($_POST['pmc_proposta_id']);
  $pmc_material_consumo = trim($_POST['pmc_material_consumo']) !== '' ? trim($_POST['pmc_material_consumo']) : NULL;
  $pmc_quantidade       = trim($_POST['pmc_quantidade']) !== '' ? trim($_POST['pmc_quantidade']) : NULL;
  $reservm_user_id         = $_SESSION['reservm_user_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_material_consumo WHERE pmc_material_consumo = :pmc_material_consumo AND pmc_proposta_id = :pmc_proposta_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':pmc_proposta_id'      => $pmc_proposta_id,
    ':pmc_material_consumo' => $pmc_material_consumo
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este material já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "INSERT INTO propostas_material_consumo (
                                                      pmc_id,
                                                      pmc_proposta_id,
                                                      pmc_material_consumo,
                                                      pmc_quantidade,
                                                      pmc_user_id,
                                                      pmc_data_cad,
                                                      pmc_data_upd
                                                    ) VALUES (
                                                      :pmc_id,
                                                      :pmc_proposta_id,
                                                      UPPER(:pmc_material_consumo),
                                                      :pmc_quantidade,
                                                      :pmc_user_id,
                                                      GETDATE(),
                                                      GETDATE()
                                                    )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':pmc_id' => $pmc_id,
      ':pmc_proposta_id' => $pmc_proposta_id,
      ':pmc_material_consumo' => $pmc_material_consumo,
      ':pmc_quantidade' => $pmc_quantidade,
      ':pmc_user_id' => $reservm_user_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'PROPOSTA - MATERIAIS',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $pmc_id,
      ':dados'      => 'ID Proposta: ' . $pmc_proposta_id .
        '; Material: ' . $pmc_material_consumo .
        '; Quantidade: ' . $pmc_quantidade,
      ':user_id'    => $reservm_user_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Cadastro realizado com sucesso!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#pmc_ancora");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Cadastro não realizado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
  }
}











/*****************************************************************************************
                              EDITAR MATERIAIS
 *****************************************************************************************/
if (isset($dados['EditMaterial'])) {

  $pmc_id               = base64_decode($_POST['pmc_id']);
  $pmc_proposta_id      = base64_decode($_POST['pmc_proposta_id']);
  $pmc_material_consumo = trim($_POST['pmc_material_consumo']) !== '' ? trim($_POST['pmc_material_consumo']) : NULL;
  $pmc_quantidade       = trim($_POST['pmc_quantidade']) !== '' ? trim($_POST['pmc_quantidade']) : NULL;
  $reservm_user_id         = $_SESSION['reservm_user_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_material_consumo WHERE pmc_material_consumo = :pmc_material_consumo AND pmc_proposta_id = :pmc_proposta_id AND pmc_id != :pmc_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':pmc_id'               => $pmc_id,
    ':pmc_proposta_id'      => $pmc_proposta_id,
    ':pmc_material_consumo' => $pmc_material_consumo
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este material já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "UPDATE
                    propostas_material_consumo
              SET
                    pmc_material_consumo = UPPER(:pmc_material_consumo),
                    pmc_quantidade       = :pmc_quantidade,
                    pmc_user_id          = :pmc_user_id,
                    pmc_data_upd         = GETDATE()
              WHERE
                    pmc_id = :pmc_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':pmc_id' => $pmc_id,
      ':pmc_material_consumo' => $pmc_material_consumo,
      ':pmc_quantidade' => $pmc_quantidade,
      ':pmc_user_id' => $reservm_user_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'PROPOSTA - MATERIAIS',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $pmc_id,
      ':dados'      => 'ID Proposta: ' . $pmc_proposta_id .
        '; Material: ' . $pmc_material_consumo .
        '; Quantidade: ' . $pmc_quantidade,
      ':user_id'    => $reservm_user_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Dados atualizados com sucesso!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#pmc_ancora");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Dados não atualizados!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
  }
}













/*****************************************************************************************
                              EXCLUIR MATERIAIS
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_material") {

  $pmc_id       = base64_decode($_GET['pmc_id']);
  $reservm_user_id = $_SESSION['reservm_user_id'];

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "DELETE FROM propostas_material_consumo WHERE pmc_id = :pmc_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':pmc_id' => $pmc_id]);

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {

      // REGISTRA AÇÃO NO LOG
      $stmt_log = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                                  VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt_log->execute([
        ':modulo'    => 'PROPOSTA - MATERIAIS',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $pmc_id,
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
