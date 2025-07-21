<?php
session_start();
include '../../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// if ($_SERVER["REQUEST_METHOD"] === "POST") {}

/*****************************************************************************************
                                    CADASTRAR CURSO
 *****************************************************************************************/
if (isset($dados['CadCursoCoordenador'])) {

  $cc_curso       = trim($_POST['cc_curso']) !== '' ? trim($_POST['cc_curso']) : NULL;
  $cc_coordenador = trim($_POST['cc_coordenador']) !== '' ? trim($_POST['cc_coordenador']) : NULL;
  $cc_email       = trim($_POST['cc_email']) !== '' ? trim($_POST['cc_email']) : NULL;
  $cc_status      = trim(isset($_POST['cc_status'])) ? $_POST['cc_status'] : 0;
  $reservm_admin_id  = $_SESSION['reservm_admin_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_cursos_coordenadores WHERE cc_curso = :cc_curso";
  $stmt = $conn->prepare($sql);
  $stmt->execute([':cc_curso' => $cc_curso]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este curso já foi cadastrado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
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
                                                    LOWER(:cc_email),
                                                    :cc_status,
                                                    :cc_user_id,
                                                    GETDATE(),
                                                    GETDATE()
                                                  )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':cc_curso'       => $cc_curso,
      ':cc_coordenador' => $cc_coordenador,
      ':cc_email'       => $cc_email,
      ':cc_status'      => $cc_status,
      ':cc_user_id'     => $reservm_admin_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'CURSO COORDENADOR',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $last_id,
      ':dados'      => 'Curso: ' . $cc_curso . '; Coordenador: ' . $cc_coordenador . '; E-mail: ' . $cc_email . '; Status: ' . $cc_status,
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
                                    EDITAR DADOS
 *****************************************************************************************/
if (isset($dados['EditarCursoCoordenador'])) {

  $cc_id          = trim($_POST['cc_id']) !== '' ? trim($_POST['cc_id']) : NULL;
  $cc_curso       = trim($_POST['cc_curso']) !== '' ? trim($_POST['cc_curso']) : NULL;
  $cc_coordenador = trim($_POST['cc_coordenador']) !== '' ? trim($_POST['cc_coordenador']) : NULL;
  $cc_email       = trim($_POST['cc_email']) !== '' ? trim($_POST['cc_email']) : NULL;
  $cc_status      = trim(isset($_POST['cc_status'])) ? $_POST['cc_status'] : 0;
  $reservm_admin_id  = $_SESSION['reservm_admin_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_cursos_coordenadores WHERE cc_curso = :cc_curso AND cc_id != :cc_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':cc_id'    => $cc_id,
    ':cc_curso' => $cc_curso
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este curso já foi cadastrado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "UPDATE
                    conf_cursos_coordenadores
              SET
                    cc_curso       = UPPER(:cc_curso),
                    cc_coordenador = UPPER(:cc_coordenador),
                    cc_email       = :cc_email,
                    cc_status      = :cc_status,
                    cc_user_id     = :cc_user_id,
                    cc_data_upd    = GETDATE()
              WHERE
                    cc_id = :cc_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':cc_id'          => $cc_id,
      ':cc_curso'       => $cc_curso,
      ':cc_coordenador' => $cc_coordenador,
      ':cc_email'       => $cc_email,
      ':cc_status'      => $cc_status,
      ':cc_user_id'     => $reservm_admin_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'CURSO COORDENADOR',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $cc_id,
      ':dados'      => 'Curso: ' . $cc_curso . '; Coordenador: ' . $cc_coordenador . '; E-mail: ' . $cc_email . '; Status: ' . $cc_status,
      ':user_id'    => $reservm_admin_id
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
                                    EXCLUIR DADOS
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_curso") {

  $cc_id         = $_GET['cc_id'];
  $reservm_admin_id = $_SESSION['reservm_admin_id'];

  // SE DADO ESTIVER SENDO USADO EM ALGUM CADASTRO, NÃO PODE SER EXLUÍDO
  $sql = "SELECT * FROM propostas WHERE CHARINDEX(:prop_curso_vinculo, prop_curso_vinculo) > 0"; // VERIFICA SE ID ESTÁ DENTRO DO ARRAY
  $stmt = $conn->prepare($sql);
  $stmt->execute([':prop_curso_vinculo' => $cc_id]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este registro não pode ser excluído!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  $sql = "SELECT * FROM propostas_coordenador_projeto WHERE CHARINDEX(:pcp_area_atuacao, pcp_area_atuacao) > 0"; // VERIFICA SE ID ESTÁ DENTRO DO ARRAY
  $stmt = $conn->prepare($sql);
  $stmt->execute([':pcp_area_atuacao' => $cc_id]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este registro não pode ser excluído!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  $sql = "SELECT * FROM propostas_equipe_executora WHERE CHARINDEX(:pex_area_atuacao, pex_area_atuacao) > 0"; // VERIFICA SE ID ESTÁ DENTRO DO ARRAY
  $stmt = $conn->prepare($sql);
  $stmt->execute([':pex_area_atuacao' => $cc_id]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este registro não pode ser excluído!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  $sql = "SELECT * FROM propostas_parceiro_externo WHERE CHARINDEX(:ppe_area_atuacao, ppe_area_atuacao) > 0"; // VERIFICA SE ID ESTÁ DENTRO DO ARRAY
  $stmt = $conn->prepare($sql);
  $stmt->execute([':ppe_area_atuacao' => $cc_id]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este registro não pode ser excluído!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "DELETE FROM conf_cursos_coordenadores WHERE cc_id = :cc_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':cc_id' => $cc_id]);

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {

      // REGISTRA AÇÃO NO LOG
      $stmt_log = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                                  VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt_log->execute([
        ':modulo'  => 'CURSO COORDENADOR',
        ':acao'    => 'EXCLUSÃO',
        ':acao_id' => $cc_id,
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
