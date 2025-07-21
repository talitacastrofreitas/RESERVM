<?php
session_start();
include '../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// if ($_SERVER["REQUEST_METHOD"] === "POST") {}

/*****************************************************************************************
                    CADASTRAR CATEGORIA DE PARTICIPAÇÃO EM PROJETOS
 *****************************************************************************************/
if (isset($dados['CadCategPartProjeto'])) {

  $cpp_categoria = trim($_POST['cpp_categoria']);
  $cpp_status    = isset($_POST['cpp_status']) ? $_POST['cpp_status'] : 2;
  $data_real     = date('Y-m-d H:i:s');
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_categoria_participacao_projeto WHERE cpp_categoria = :cpp_categoria";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":cpp_categoria", $cpp_categoria);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Esta categoria já foi cadastra!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
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
                                                              :cpp_data_cad,
                                                              :cpp_data_upd
                                                            )";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":cpp_categoria", $cpp_categoria);
    $stmt->bindParam(":cpp_status", $cpp_status);
    //
    $stmt->bindParam(":cpp_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":cpp_data_cad", $data_real);
    $stmt->bindParam(":cpp_data_upd", $data_real);
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'CATEGORIA PARTICIPAÇÃO PROJETO',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $last_id,
      ':dados'      => 'Categoria: ' . $cpp_categoria . '; Status: ' . $cpp_status,
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
                    EDITAR CATEGORIA DE PARTICIPAÇÃO EM PROJETOS
 *****************************************************************************************/
if (isset($dados['EditCategPartProjeto'])) {

  $cpp_id        = trim($_POST['cpp_id']);
  $cpp_categoria = trim($_POST['cpp_categoria']);
  $cpp_status    = isset($_POST['cpp_status']) ? $_POST['cpp_status'] : 2;
  $data_real     = date('Y-m-d H:i:s');
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_categoria_participacao_projeto WHERE cpp_categoria = :cpp_categoria AND cpp_id != :cpp_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":cpp_id", $cpp_id);
  $stmt->bindParam(":cpp_categoria", $cpp_categoria);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Esta categoria já foi cadastra!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $sql = "UPDATE
                    conf_categoria_participacao_projeto
              SET
                    cpp_categoria = UPPER(:cpp_categoria),
                    cpp_status    = :cpp_status,
                    cpp_user_id   = :cpp_user_id,
                    cpp_data_upd  = :cpp_data_upd
              WHERE
                    cpp_id = :cpp_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":cpp_id", $cpp_id);
    //
    $stmt->bindParam(":cpp_categoria", $cpp_categoria);
    $stmt->bindParam(":cpp_status", $cpp_status);
    //
    $stmt->bindParam(":cpp_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":cpp_data_upd", $data_real);
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'CATEGORIA PARTICIPAÇÃO PROJETO',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $cpp_id,
      ':dados'      => 'Categoria: ' . $cpp_categoria . '; Status: ' . $cpp_status,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => $data_real
    ));
    // -------------------------------

    $_SESSION["msg"] = "Dados atualizados com sucesso!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "Atualização não realizada!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
}








/*****************************************************************************************
                    EXCLUIR CATEGORIA DE PARTICIPAÇÃO EM PROJETOS
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_cpp") {

  $cpp_id = $_GET['cpp_id'];

  // SE DADO ESTIVER SENDO USADO EM ALGUM CADASTRO, NÃO PODE SER EXLUÍDO
  $sql = "SELECT COUNT(*) FROM propostas_equipe_executora WHERE pex_partic_categ = :pex_partic_categ";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":pex_partic_categ", $cpp_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este registro não pode ser excluído!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $sql = "DELETE FROM conf_categoria_participacao_projeto WHERE cpp_id = :cpp_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':cpp_id', $cpp_id);
    $stmt->execute();

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {
      $_SESSION["msg"] = "Dados excluídos com sucesso!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :user_id, :user_nome, :data )');
      $stmt->execute(array(
        ':modulo'    => 'CATEGORIA PARTICIPAÇÃO PROJETO',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $cpp_id,
        ':user_id'   => $_SESSION['reservm_admin_id'],
        ':user_nome' => $_SESSION['reservm_admin_nome'],
        ':data'      => date('Y-m-d H:i:s')
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
