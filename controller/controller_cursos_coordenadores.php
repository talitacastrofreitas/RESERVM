<?php
session_start();
include '../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// if ($_SERVER["REQUEST_METHOD"] === "POST") {}

/*****************************************************************************************
                                    CADASTRAR CURSO
 *****************************************************************************************/
if (isset($dados['CadCursoCoordenador'])) {

  $cc_curso       = trim($_POST['cc_curso']);
  $cc_coordenador = trim($_POST['cc_coordenador']);
  $cc_email       = trim($_POST['cc_email']);
  $cc_status      = isset($_POST['cc_status']) ? $_POST['cc_status'] : 2;
  $data_real      = date('Y-m-d H:i:s');
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_cursos_coordenadores WHERE cc_curso = :cc_curso";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":cc_curso", $cc_curso);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este curso já foi cadastrado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $sql = "INSERT INTO conf_cursos_coordenadores (
                                                    cc_curso,
                                                    cc_coordenador,
                                                    cc_email,
                                                    cc_status,
                                                    cc_user_id,
                                                    cc_data_cad,
                                                    cc_data_upd
                                                  ) VALUES (
                                                    UPPER(:cc_curso),
                                                    UPPER(:cc_coordenador),
                                                    :cc_email,
                                                    :cc_status,
                                                    :cc_user_id,
                                                    :cc_data_cad,
                                                    :cc_data_upd
                                                  )";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":cc_curso", $cc_curso);
    $stmt->bindParam(":cc_coordenador", $cc_coordenador);
    $stmt->bindParam(":cc_email", $cc_email);
    $stmt->bindParam(":cc_status", $cc_status);
    //
    $stmt->bindParam(":cc_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":cc_data_cad", $data_real);
    $stmt->bindParam(":cc_data_upd", $data_real);
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'CURSO COORDENADOR',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $last_id,
      ':dados'      => 'Curso: ' . $cc_curso . '; Coordenador: ' . $cc_coordenador . '; E-mail: ' . $cc_email . '; Status: ' . $cc_status,
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
                                    EDITAR DADOS
 *****************************************************************************************/
if (isset($dados['EditarCursoCoordenador'])) {

  $cc_id          = trim($_POST['cc_id']);
  $cc_curso       = trim($_POST['cc_curso']);
  $cc_coordenador = trim($_POST['cc_coordenador']);
  $cc_email       = trim($_POST['cc_email']);
  $cc_status      = isset($_POST['cc_status']) ? $_POST['cc_status'] : 2;
  $data_real      = date('Y-m-d H:i:s');
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_cursos_coordenadores WHERE cc_curso = :cc_curso AND cc_id != :cc_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":cc_id", $cc_id);
  $stmt->bindParam(":cc_curso", $cc_curso);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este curso já foi cadastrado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $sql = "UPDATE
                    conf_cursos_coordenadores
              SET
                    cc_curso       = UPPER(:cc_curso),
                    cc_coordenador = UPPER(:cc_coordenador),
                    cc_email       = :cc_email,
                    cc_status      = :cc_status,
                    cc_user_id     = :cc_user_id,
                    cc_data_upd    = :cc_data_upd
              WHERE
                    cc_id = :cc_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":cc_id", $cc_id);
    //
    $stmt->bindParam(":cc_curso", $cc_curso);
    $stmt->bindParam(":cc_coordenador", $cc_coordenador);
    $stmt->bindParam(":cc_email", $cc_email);
    $stmt->bindParam(":cc_status", $cc_status);
    //
    $stmt->bindParam(":cc_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":cc_data_upd", $data_real);
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, UPPER(:dados), :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'CURSO COORDENADOR',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $cc_id,
      ':dados'      => 'Curso: ' . $cc_curso . '; Coordenador: ' . $cc_coordenador . '; E-mail: ' . $cc_email . '; Status: ' . $cc_status,
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
                                    EXCLUIR DADOS
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_curso") {

  $cc_id     = $_GET['cc_id'];
  $data_real = date('Y-m-d H:i:s');

  // SE DADO ESTIVER SENDO USADO EM ALGUM CADASTRO, NÃO PODE SER EXLUÍDO
  $sql = "SELECT COUNT(*) FROM propostas WHERE prop_curso_vinculo = :prop_curso_vinculo";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":prop_curso_vinculo", $cc_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este registro não pode ser excluído!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  $sql = "SELECT COUNT(*) FROM propostas_coordenador_projeto WHERE pcp_area_atuacao = :pcp_area_atuacao";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":pcp_area_atuacao", $cc_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este registro não pode ser excluído!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  $sql = "SELECT COUNT(*) FROM propostas_equipe_executora WHERE pex_area_atuacao = :pex_area_atuacao";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":pex_area_atuacao", $cc_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este registro não pode ser excluído!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  $sql = "SELECT COUNT(*) FROM propostas_parceiro_externo WHERE ppe_area_atuacao = :ppe_area_atuacao";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":ppe_area_atuacao", $cc_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este registro não pode ser excluído!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $sql = "DELETE FROM conf_cursos_coordenadores WHERE cc_id = :cc_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':cc_id', $cc_id);
    $stmt->execute();

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {
      $_SESSION["msg"] = "Dados excluídos com sucesso!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :user_id, :user_nome, :data )');
      $stmt->execute(array(
        ':modulo'    => 'CURSO COORDENADOR',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $cc_id,
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
