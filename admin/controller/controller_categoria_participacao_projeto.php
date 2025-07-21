<?php
session_start();
include '../../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// if ($_SERVER["REQUEST_METHOD"] === "POST") {}

/*****************************************************************************************
                    CADASTRAR CATEGORIA DE PARTICIPAÇÃO EM PROJETOS
 *****************************************************************************************/
if (isset($dados['CadCategPartProjeto'])) {

  $cpp_categoria = trim($_POST['cpp_categoria']) !== '' ? trim($_POST['cpp_categoria']) : NULL;
  $cpp_status    = trim(isset($_POST['cpp_status'])) ? $_POST['cpp_status'] : 0;
  $reservm_admin_id = $_SESSION['reservm_admin_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_categoria_participacao_projeto WHERE cpp_categoria = :cpp_categoria";
  $stmt = $conn->prepare($sql);
  $stmt->execute([':cpp_categoria' => $cpp_categoria]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Esta categoria já foi cadastra!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "INSERT INTO conf_categoria_participacao_projeto (
                                                              cpp_categoria,
                                                              cpp_status,
                                                              cpp_user_id,
                                                              cpp_data_cad,
                                                              cpp_data_upd
                                                            ) VALUES (
                                                              UPPER(:cpp_categoria),
                                                              :cpp_status,
                                                              :cpp_user_id,
                                                              GETDATE(),
                                                              GETDATE()
                                                            )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':cpp_categoria' => $cpp_categoria,
      ':cpp_status'    => $cpp_status,
      ':cpp_user_id'   => $reservm_admin_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'  => 'CATEGORIA PARTICIPAÇÃO PROJETO',
      ':acao'    => 'CADASTRO',
      ':acao_id' => $last_id,
      ':dados'   => 'Categoria: ' . $cpp_categoria . '; Status: ' . $cpp_status,
      ':user_id' => $reservm_admin_id
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
                    EDITAR CATEGORIA DE PARTICIPAÇÃO EM PROJETOS
 *****************************************************************************************/
if (isset($dados['EditCategPartProjeto'])) {

  $cpp_id        = trim($_POST['cpp_id']) !== '' ? trim($_POST['cpp_id']) : NULL;
  $cpp_categoria = trim($_POST['cpp_categoria']) !== '' ? trim($_POST['cpp_categoria']) : NULL;
  $cpp_status    = trim(isset($_POST['cpp_status'])) ? $_POST['cpp_status'] : 0;
  $reservm_admin_id = $_SESSION['reservm_admin_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_categoria_participacao_projeto WHERE cpp_categoria = :cpp_categoria AND cpp_id != :cpp_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':cpp_id'        => $cpp_id,
    ':cpp_categoria' => $cpp_categoria
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Esta categoria já foi cadastra!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "UPDATE
                    conf_categoria_participacao_projeto
              SET
                    cpp_categoria = UPPER(:cpp_categoria),
                    cpp_status    = :cpp_status,
                    cpp_user_id   = :cpp_user_id,
                    cpp_data_upd  = GETDATE()
              WHERE
                    cpp_id = :cpp_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':cpp_id'        => $cpp_id,
      ':cpp_categoria' => $cpp_categoria,
      ':cpp_status'    => $cpp_status,
      ':cpp_user_id'   => $reservm_admin_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'  => 'CATEGORIA PARTICIPAÇÃO PROJETO',
      ':acao'    => 'ATUALIZAÇÃO',
      ':acao_id' => $cpp_id,
      ':dados'   => 'Categoria: ' . $cpp_categoria . '; Status: ' . $cpp_status,
      ':user_id' => $reservm_admin_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Dados atualizados com sucesso!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Atualização não realizada!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
}








/*****************************************************************************************
                    EXCLUIR CATEGORIA DE PARTICIPAÇÃO EM PROJETOS
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_cpp") {

  $cpp_id        = $_GET['cpp_id'];
  $reservm_admin_id = $_SESSION['reservm_admin_id'];

  // SE DADO ESTIVER SENDO USADO EM ALGUM CADASTRO, NÃO PODE SER EXCLUÍDO
  $sql = "SELECT * FROM propostas_equipe_executora WHERE CHARINDEX(:pex_partic_categ, pex_partic_categ) > 0"; // VERIFICA SE ID ESTÁ DENTRO DO ARRAY
  $stmt = $conn->prepare($sql);
  $stmt->execute([':pex_partic_categ' => $cpp_id]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este registro não pode ser excluído!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "DELETE FROM conf_categoria_participacao_projeto WHERE cpp_id = :cpp_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':cpp_id' => $cpp_id]);

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {

      // REGISTRA AÇÃO NO LOG
      $stmt_log = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                                  VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt_log->execute([
        ':modulo'  => 'CATEGORIA PARTICIPAÇÃO PROJETO',
        ':acao'    => 'EXCLUSÃO',
        ':acao_id' => $cpp_id,
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
