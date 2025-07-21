<?php
session_start();
include '../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                          CADASTRAR COORDENADOR DO PROJETO
 *****************************************************************************************/
if (isset($dados['CadCoordProjeto'])) {

  $pcp_id                = md5(uniqid(rand(), true)); // GERA UM ID UNICO
  $pcp_proposta_id       = $_POST['pcp_proposta_id'];
  //
  $pcp_nome              = trim($_POST['pcp_nome']);
  $pcp_email             = trim($_POST['pcp_email']);
  $pcp_contato           = $_POST['pcp_contato'];
  $pcp_carga_hora        = $_POST['pcp_carga_hora'];

  // PERFIL DO PARTICIPANTE
  $pcp_partic_perfil         = $_POST['pcp_partic_perfil'];
  if ($pcp_partic_perfil    === '9') {
    $pcp_outro_partic_perfil = trim($_POST['pcp_outro_partic_perfil']);
  } else {
    $pcp_outro_partic_perfil = NULL;
  }
  // -------------------------------

  // NOME DO CURSO/ÁREA DE ATUAÇÃO
  $pcp_area_atuacao        = $_POST['pcp_area_atuacao'];
  if ($pcp_area_atuacao    === '20') {
    $pcp_nome_area_atuacao = trim($_POST['pcp_nome_area_atuacao']);
  } else {
    $pcp_nome_area_atuacao = NULL;
  }
  // -------------------------------

  $pcp_formacao          = nl2br(trim($_POST['pcp_formacao']));
  $pcp_lattes            = trim($_POST['pcp_lattes']);
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_coordenador_projeto WHERE pcp_nome = :pcp_nome AND pcp_proposta_id = :pcp_proposta_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":pcp_nome", $pcp_nome);
  $stmt->bindParam(":pcp_proposta_id", $pcp_proposta_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este nome já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#cp_ancora");
    return die;
  }
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_coordenador_projeto WHERE pcp_email = :pcp_email AND pcp_proposta_id = :pcp_proposta_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":pcp_email", $pcp_email);
  $stmt->bindParam(":pcp_proposta_id", $pcp_proposta_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este e-mail já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#cp_ancora");
    return die;
  }
  // -------------------------------

  try {
    $sql = "INSERT INTO propostas_coordenador_projeto (
                                                        pcp_id,
                                                        pcp_proposta_id,
                                                        pcp_nome,
                                                        pcp_email,
                                                        pcp_contato,
                                                        pcp_partic_perfil,
                                                        pcp_outro_partic_perfil,
                                                        pcp_carga_hora,
                                                        pcp_area_atuacao,
                                                        pcp_nome_area_atuacao,
                                                        pcp_formacao,
                                                        pcp_lattes,
                                                        pcp_user_id,
                                                        pcp_data_cad,
                                                        pcp_data_upd
                                                      ) VALUES (
                                                        :pcp_id,
                                                        :pcp_proposta_id,
                                                        UPPER(:pcp_nome),
                                                        LOWER(:pcp_email),
                                                        :pcp_contato,
                                                        :pcp_partic_perfil,
                                                        UPPER(:pcp_outro_partic_perfil),
                                                        :pcp_carga_hora,
                                                        :pcp_area_atuacao,
                                                        UPPER(:pcp_nome_area_atuacao),
                                                        :pcp_formacao,
                                                        :pcp_lattes,
                                                        :pcp_user_id,
                                                        :pcp_data_cad,
                                                        :pcp_data_upd
                                                      )";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":pcp_id", $pcp_id);
    $stmt->bindParam(":pcp_proposta_id", $pcp_proposta_id);
    //
    $stmt->bindParam(":pcp_nome", $pcp_nome);
    $stmt->bindParam(":pcp_email", $pcp_email);
    $stmt->bindParam(":pcp_contato", $pcp_contato);
    $stmt->bindParam(":pcp_partic_perfil", $pcp_partic_perfil);
    $stmt->bindParam(":pcp_outro_partic_perfil", $pcp_outro_partic_perfil);
    $stmt->bindParam(":pcp_carga_hora", $pcp_carga_hora);
    $stmt->bindParam(":pcp_area_atuacao", $pcp_area_atuacao);
    $stmt->bindParam(":pcp_nome_area_atuacao", $pcp_nome_area_atuacao);
    $stmt->bindParam(":pcp_formacao", $pcp_formacao);
    $stmt->bindParam(":pcp_lattes", $pcp_lattes);
    //
    $stmt->bindParam(":pcp_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":pcp_data_cad", date('Y-m-d H:i:s'));
    $stmt->bindParam(":pcp_data_upd", date('Y-m-d H:i:s'));
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'PROPOSTA - COORDENADOR PROJETO',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $pcp_id,
      ':dados'      => 'ID Proposta: ' . $pcp_proposta_id . '; Nome: ' . $pcp_nome . '; E-mail: ' . $pcp_email . '; Contato: ' . $pcp_contato . '; Perfil: ' . $pcp_partic_perfil . '; Outro Perfil: ' . $pcp_outro_partic_perfil . '; Carga horária: ' . $pcp_carga_hora . '; Área: ' . $pcp_area_atuacao . '; Nome Área: ' . $pcp_nome_area_atuacao . '; Formação: ' . $pcp_formacao . '; Lattes: ' . $pcp_lattes,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => date('Y-m-d H:i:s')
    ));
    // -------------------------------

    $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Cadastro realizado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#cp_ancora");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Cadastro não realizado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}















/*****************************************************************************************
                              EDITAR COORDENADOR DO PROJETO
 *****************************************************************************************/
if (isset($dados['EditCoordProjeto'])) {

  $pcp_id                = $_POST['pcp_id'];
  $pcp_proposta_id       = $_POST['pcp_proposta_id'];
  //
  $pcp_nome              = trim($_POST['pcp_nome']);
  $pcp_email             = trim($_POST['pcp_email']);
  $pcp_contato           = $_POST['pcp_contato'];
  $pcp_carga_hora        = $_POST['pcp_carga_hora'];

  // PERFIL DO PARTICIPANTE
  $pcp_partic_perfil         = $_POST['pcp_partic_perfil'];
  if ($pcp_partic_perfil    === '9') {
    $pcp_outro_partic_perfil = trim($_POST['pcp_outro_partic_perfil']);
  } else {
    $pcp_outro_partic_perfil = NULL;
  }
  // -------------------------------

  // NOME DO CURSO/ÁREA DE ATUAÇÃO
  $pcp_area_atuacao        = $_POST['pcp_area_atuacao'];
  if ($pcp_area_atuacao    === '20') {
    $pcp_nome_area_atuacao = trim($_POST['pcp_nome_area_atuacao']);
  } else {
    $pcp_nome_area_atuacao = NULL;
  }
  // -------------------------------

  $pcp_formacao          = nl2br(trim($_POST['pcp_formacao']));
  $pcp_lattes            = trim($_POST['pcp_lattes']);
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_coordenador_projeto WHERE pcp_nome = :pcp_nome AND pcp_proposta_id = :pcp_proposta_id AND pcp_id != :pcp_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":pcp_id", $pcp_id);
  $stmt->bindParam(":pcp_nome", $pcp_nome);
  $stmt->bindParam(":pcp_proposta_id", $pcp_proposta_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este nome já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#cp_ancora");
    return die;
  }
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_coordenador_projeto WHERE pcp_email = :pcp_email AND pcp_proposta_id = :pcp_proposta_id AND pcp_id != :pcp_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":pcp_id", $pcp_id);
  $stmt->bindParam(":pcp_email", $pcp_email);
  $stmt->bindParam(":pcp_proposta_id", $pcp_proposta_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este e-mail já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#cp_ancora");
    return die;
  }
  // -------------------------------

  try {
    $sql = "UPDATE
                    propostas_coordenador_projeto
              SET
                    pcp_nome                = UPPER(:pcp_nome),
                    pcp_email               = LOWER(:pcp_email),
                    pcp_contato             = :pcp_contato,
                    pcp_partic_perfil       = :pcp_partic_perfil,
                    pcp_outro_partic_perfil = UPPER(:pcp_outro_partic_perfil),
                    pcp_carga_hora          = :pcp_carga_hora,
                    pcp_area_atuacao        = :pcp_area_atuacao,
                    pcp_nome_area_atuacao   = UPPER(:pcp_nome_area_atuacao),
                    pcp_formacao            = :pcp_formacao,
                    pcp_lattes              = :pcp_lattes,
                    pcp_user_id             = :pcp_user_id,
                    pcp_data_upd            = :pcp_data_upd
              WHERE
                    pcp_id = :pcp_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":pcp_id", $pcp_id);
    //
    $stmt->bindParam(":pcp_nome", $pcp_nome);
    $stmt->bindParam(":pcp_email", $pcp_email);
    $stmt->bindParam(":pcp_contato", $pcp_contato);
    $stmt->bindParam(":pcp_partic_perfil", $pcp_partic_perfil);
    $stmt->bindParam(":pcp_outro_partic_perfil", $pcp_outro_partic_perfil);
    $stmt->bindParam(":pcp_carga_hora", $pcp_carga_hora);
    $stmt->bindParam(":pcp_area_atuacao", $pcp_area_atuacao);
    $stmt->bindParam(":pcp_nome_area_atuacao", $pcp_nome_area_atuacao);
    $stmt->bindParam(":pcp_formacao", $pcp_formacao);
    $stmt->bindParam(":pcp_lattes", $pcp_lattes);
    //
    $stmt->bindParam(":pcp_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":pcp_data_upd", date('Y-m-d H:i:s'));
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'PROPOSTA - COORDENADOR PROJETO',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $pcp_id,
      ':dados'      => 'ID Proposta: ' . $pcp_proposta_id . '; Nome: ' . $pcp_nome . '; E-mail: ' . $pcp_email . '; Contato: ' . $pcp_contato . '; Perfil: ' . $pcp_partic_perfil . '; Outro Perfil: ' . $pcp_outro_partic_perfil . '; Carga horária: ' . $pcp_carga_hora . '; Área: ' . $pcp_area_atuacao . '; Nome Área: ' . $pcp_nome_area_atuacao . '; Formação: ' . $pcp_formacao . '; Lattes: ' . $pcp_lattes,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => date('Y-m-d H:i:s')
    ));

    echo $stmt->rowCount();

    $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados atualizados!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#cp_ancora");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados não atualizados!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}













/*****************************************************************************************
                              EXCLUIR COORDENADOR DO PROJETO
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_coord") {

  $pcp_id = $_GET['pcp_id'];

  try {
    $sql = "DELETE FROM propostas_coordenador_projeto WHERE pcp_id = :pcp_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':pcp_id', $pcp_id);
    $stmt->execute();

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {
      $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i>Dados excluídos!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#cp_ancora");

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_acao_user_nome, log_data )
                              VALUES ( :modulo, :acao, :acao_id, :user_id, :user_nome, :data )');
      $stmt->execute(array(
        ':modulo'    => 'PROPOSTA - COORDENADOR PROJETO',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $pcp_id,
        ':user_id'   => $_SESSION['reservm_admin_id'],
        ':user_nome' => $_SESSION['reservm_admin_nome'],
        ':data'      => date('Y-m-d H:i:s')
      ));
      // -------------------------------

    } else {
      $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados não excluídos!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#cp_ancora");
    }
  } catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
  }
  $conn = null;
}
