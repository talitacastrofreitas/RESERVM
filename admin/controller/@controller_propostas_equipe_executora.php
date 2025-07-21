<?php
session_start();
include '../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// if ($_SERVER["REQUEST_METHOD"] === "POST") {}

/*****************************************************************************************
                          CADASTRAR EQUIPE EXECUTORA
 *****************************************************************************************/
if (isset($dados['CadEquipeExec'])) {

  $pex_id                = md5(uniqid(rand(), true)); // GERA UM ID UNICO
  $pex_proposta_id       = $_POST['pex_proposta_id'];
  //
  $pex_nome              = trim($_POST['pex_nome']);
  $pex_email             = trim($_POST['pex_email']);
  $pex_contato           = $_POST['pex_contato'];

  // DESCRIÇÃO DO PRFIL DO PARTICIPANTE
  $pex_partic_categ = trim($_POST['pex_partic_categ']);
  if ($pex_partic_categ === '11') {
    $pex_qual_partic_categ = trim($_POST['pex_qual_partic_categ']);
  } else {
    $pex_qual_partic_categ = NULL;
  }
  // -------------------------------

  // PERFIL DO PARTICIPANTE
  $pex_partic_perfil         = trim($_POST['pex_partic_perfil']);
  if ($pex_partic_perfil === '9') {
    $pex_outro_partic_perfil = trim($_POST['pex_outro_partic_perfil']);
  } else {
    $pex_outro_partic_perfil = NULL;
  }
  // -------------------------------

  $pex_carga_hora        = $_POST['pex_carga_hora'];

  // DESCRIÇÃO DA ÁREA DE ATUAÇÃO
  $pex_area_atuacao = trim($_POST['pex_area_atuacao']);
  if ($pex_area_atuacao === '20') {
    $pex_nome_area_atuacao = trim($_POST['pex_nome_area_atuacao']);
  } else {
    $pex_nome_area_atuacao = NULL;
  }
  // -------------------------------

  $pex_formacao          = nl2br(trim($_POST['pex_formacao']));
  $pex_lattes            = trim($_POST['pex_lattes']);
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_equipe_executora WHERE (pex_nome = :pex_nome OR pex_email = :pex_email) AND pex_proposta_id = :pex_proposta_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":pex_proposta_id", $pex_proposta_id);
  $stmt->bindParam(":pex_nome", $pex_nome);
  $stmt->bindParam(":pex_email", $pex_email);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este nome ou e-mail já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#ex_ancora");
    return die;
  }
  // -------------------------------

  try {
    $sql = "INSERT INTO propostas_equipe_executora (
                                                      pex_id,
                                                      pex_proposta_id,
                                                      pex_nome,
                                                      pex_email,
                                                      pex_contato,
                                                      pex_partic_categ,
                                                      pex_qual_partic_categ,
                                                      pex_partic_perfil,
                                                      pex_outro_partic_perfil,
                                                      pex_carga_hora,
                                                      pex_area_atuacao,
                                                      pex_nome_area_atuacao,
                                                      pex_formacao,
                                                      pex_lattes,
                                                      pex_user_id,
                                                      pex_data_cad,
                                                      pex_data_upd
                                                    ) VALUES (
                                                      :pex_id,
                                                      :pex_proposta_id,
                                                      UPPER(:pex_nome),
                                                      LOWER(:pex_email),
                                                      :pex_contato,
                                                      :pex_partic_categ,
                                                      UPPER(:pex_qual_partic_categ),
                                                      :pex_partic_perfil,
                                                      UPPER(:pex_outro_partic_perfil),
                                                      :pex_carga_hora,
                                                      :pex_area_atuacao,
                                                      UPPER(:pex_nome_area_atuacao),
                                                      :pex_formacao,
                                                      :pex_lattes,
                                                      :pex_user_id,
                                                      :pex_data_cad,
                                                      :pex_data_upd
                                                    )";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":pex_id", $pex_id);
    $stmt->bindParam(":pex_proposta_id", $pex_proposta_id);
    //
    $stmt->bindParam(":pex_nome", $pex_nome);
    $stmt->bindParam(":pex_email", $pex_email);
    $stmt->bindParam(":pex_contato", $pex_contato);
    $stmt->bindParam(":pex_partic_categ", $pex_partic_categ);
    $stmt->bindParam(":pex_qual_partic_categ", $pex_qual_partic_categ);
    $stmt->bindParam(":pex_partic_perfil", $pex_partic_perfil);
    $stmt->bindParam(":pex_outro_partic_perfil", $pex_outro_partic_perfil);
    $stmt->bindParam(":pex_carga_hora", $pex_carga_hora);
    $stmt->bindParam(":pex_area_atuacao", $pex_area_atuacao);
    $stmt->bindParam(":pex_nome_area_atuacao", $pex_nome_area_atuacao);
    $stmt->bindParam(":pex_formacao", $pex_formacao);
    $stmt->bindParam(":pex_lattes", $pex_lattes);
    //
    $stmt->bindParam(":pex_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":pex_data_cad", date('Y-m-d H:i:s'));
    $stmt->bindParam(":pex_data_upd", date('Y-m-d H:i:s'));
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'PROPOSTA - EQUIPE EXECUTORA',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $pex_id,
      ':dados'      => 'ID Proposta: ' . $pex_proposta_id . '; Nome: ' . $pex_nome . '; E-mail: ' . $pex_email . '; Contato: ' . $pex_contato . '; Categoria: ' . $pex_partic_categ . '; Qual Categoria: ' . $pex_qual_partic_categ . '; Perfil: ' . $pex_partic_perfil . '; Nome Perfil: ' . $pex_outro_partic_perfil . '; Carga horária: ' . $pex_carga_hora . '; Área: ' . $pex_area_atuacao . '; Nome Área: ' . $pex_nome_area_atuacao . '; Formação: ' . $pex_formacao . '; Lattes: ' . $pex_lattes,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => date('Y-m-d H:i:s')
    ));
    // -------------------------------

    $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Cadastro realizado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#ex_ancora");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Cadastro não realizado!" . $e->getMessage();
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}














/*****************************************************************************************
                              EDITAR EQUIPE EXECUTORA
 *****************************************************************************************/
if (isset($dados['EditEquipeExec'])) {

  $pex_id                = $_POST['pex_id'];
  $pex_proposta_id       = $_POST['pex_proposta_id'];
  //
  $pex_nome              = trim($_POST['pex_nome']);
  $pex_email             = trim($_POST['pex_email']);
  $pex_contato           = $_POST['pex_contato'];

  // DESCRIÇÃO DO PRFIL DO PARTICIPANTE
  $pex_partic_categ = trim($_POST['pex_partic_categ']);
  if ($pex_partic_categ === '11') {
    $pex_qual_partic_categ = trim($_POST['pex_qual_partic_categ']);
  } else {
    $pex_qual_partic_categ = NULL;
  }
  // -------------------------------

  // PERFIL DO PARTICIPANTE
  $pex_partic_perfil         = trim($_POST['pex_partic_perfil']);
  if ($pex_partic_perfil === '9') {
    $pex_outro_partic_perfil = trim($_POST['pex_outro_partic_perfil']);
  } else {
    $pex_outro_partic_perfil = NULL;
  }
  // -------------------------------

  $pex_carga_hora        = $_POST['pex_carga_hora'];

  // DESCRIÇÃO DA ÁREA DE ATUAÇÃO
  $pex_area_atuacao = trim($_POST['pex_area_atuacao']);
  if ($pex_area_atuacao === '20') {
    $pex_nome_area_atuacao = trim($_POST['pex_nome_area_atuacao']);
  } else {
    $pex_nome_area_atuacao = NULL;
  }
  // -------------------------------

  $pex_formacao          = nl2br(trim($_POST['pex_formacao']));
  $pex_lattes            = trim($_POST['pex_lattes']);
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_equipe_executora WHERE (pex_nome = :pex_nome OR pex_email = :pex_email) AND pex_proposta_id = :pex_proposta_id AND pex_id != :pex_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":pex_id", $pex_id);
  $stmt->bindParam(":pex_proposta_id", $pex_proposta_id);
  $stmt->bindParam(":pex_nome", $pex_nome);
  $stmt->bindParam(":pex_email", $pex_email);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Este nome ou e-mail já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#ex_ancora");
    return die;
  }
  // -------------------------------

  try {
    $sql = "UPDATE
                    propostas_equipe_executora
              SET
                    pex_nome                = UPPER(:pex_nome),
                    pex_email               = LOWER(:pex_email),
                    pex_contato             = :pex_contato,
                    pex_partic_categ        = :pex_partic_categ,
                    pex_qual_partic_categ   = UPPER(:pex_qual_partic_categ),
                    pex_partic_perfil       = :pex_partic_perfil,
                    pex_outro_partic_perfil = UPPER(:pex_outro_partic_perfil),
                    pex_carga_hora          = :pex_carga_hora,
                    pex_area_atuacao        = :pex_area_atuacao,
                    pex_nome_area_atuacao   = UPPER(:pex_nome_area_atuacao),
                    pex_formacao            = :pex_formacao,
                    pex_lattes              = :pex_lattes,
                    pex_user_id             = :pex_user_id,
                    pex_data_upd            = :pex_data_upd
              WHERE
                    pex_id = :pex_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":pex_id", $pex_id);
    //
    $stmt->bindParam(":pex_nome", $pex_nome);
    $stmt->bindParam(":pex_email", $pex_email);
    $stmt->bindParam(":pex_contato", $pex_contato);
    $stmt->bindParam(":pex_partic_categ", $pex_partic_categ);
    $stmt->bindParam(":pex_qual_partic_categ", $pex_qual_partic_categ);
    $stmt->bindParam(":pex_partic_perfil", $pex_partic_perfil);
    $stmt->bindParam(":pex_outro_partic_perfil", $pex_outro_partic_perfil);
    $stmt->bindParam(":pex_carga_hora", $pex_carga_hora);
    $stmt->bindParam(":pex_area_atuacao", $pex_area_atuacao);
    $stmt->bindParam(":pex_nome_area_atuacao", $pex_nome_area_atuacao);
    $stmt->bindParam(":pex_formacao", $pex_formacao);
    $stmt->bindParam(":pex_lattes", $pex_lattes);
    //
    $stmt->bindParam(":pex_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":pex_data_upd", date('Y-m-d H:i:s'));
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'PROPOSTA - EQUIPE EXECUTORA',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $pex_id,
      ':dados'      => 'ID Proposta: ' . $pex_proposta_id . '; Nome: ' . $pex_nome . '; E-mail: ' . $pex_email . '; Contato: ' . $pex_contato . '; Categoria: ' . $pex_partic_categ . '; Qual Categoria: ' . $pex_qual_partic_categ . '; Perfil: ' . $pex_partic_perfil . '; Nome Perfil: ' . $pex_outro_partic_perfil . '; Carga horária: ' . $pex_carga_hora . '; Área: ' . $pex_area_atuacao . '; Nome Área: ' . $pex_outro_partic_perfil . '; Carga horária: ' . $pex_carga_hora . '; Área: ' . $pex_area_atuacao . '; Nome Área: ' . $pex_nome_area_atuacao . '; Formação: ' . $pex_formacao . '; Lattes: ' . $pex_lattes,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => date('Y-m-d H:i:s')
    ));
    // -------------------------------

    $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados atualizados!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#ex_ancora");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados não atualizados!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
}















/*****************************************************************************************
                              EXCLUIR EQUIPE EXECUTORA
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_equipe_exec") {

  $pex_id = $_GET['pex_id'];

  try {
    $sql = "DELETE FROM propostas_equipe_executora WHERE pex_id = :pex_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':pex_id', $pex_id);
    $stmt->execute();

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {
      $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i>Dados excluídos!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#ex_ancora");

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_acao_user_nome, log_data )
      VALUES ( :modulo, :acao, :acao_id, :user_id, :user_nome, :data )');
      $stmt->execute(array(
        ':modulo'    => 'PROPOSTA - EQUIPE EXECUTORA',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $pex_id,
        ':user_id'   => $_SESSION['reservm_admin_id'],
        ':user_nome' => $_SESSION['reservm_admin_nome'],
        ':data'      => date('Y-m-d H:i:s')
      ));
      // -------------------------------

    } else {
      $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados não excluídos!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#ex_ancora");
      return die;
    }
  } catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
  }
  $conn = null;
}
