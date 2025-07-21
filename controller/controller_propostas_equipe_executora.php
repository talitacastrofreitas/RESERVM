<?php
session_start();
include '../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                          CADASTRAR EQUIPE EXECUTORA
 *****************************************************************************************/
if (isset($dados['CadEquipeExec'])) {

  $pex_id                  = md5(uniqid(rand(), true)); // GERA UM ID UNICO
  $pex_proposta_id         = base64_decode($_POST['pex_proposta_id']);
  //
  $pex_nome                = trim($_POST['pex_nome']) !== '' ? trim($_POST['pex_nome']) : NULL;
  $pex_email               = trim($_POST['pex_email']) !== '' ? trim($_POST['pex_email']) : NULL;
  $pex_contato             = trim($_POST['pex_contato']) !== '' ? trim($_POST['pex_contato']) : NULL;
  $pex_carga_hora          = trim($_POST['pex_carga_hora']) !== '' ? trim($_POST['pex_carga_hora']) : NULL;
  $pex_partic_perfil       = trim($_POST['pex_partic_perfil']) !== '' ? trim($_POST['pex_partic_perfil']) : NULL;
  $pex_outro_partic_perfil = trim($_POST['pex_outro_partic_perfil']) !== '' ? trim($_POST['pex_outro_partic_perfil']) : NULL;
  $pex_area_atuacao        = trim($_POST['pex_area_atuacao']) !== '' ? trim($_POST['pex_area_atuacao']) : NULL;
  $pex_nome_area_atuacao   = trim($_POST['pex_nome_area_atuacao']) !== '' ? trim($_POST['pex_nome_area_atuacao']) : NULL;
  $pex_partic_categ        = trim($_POST['pex_partic_categ']) !== '' ? trim($_POST['pex_partic_categ']) : NULL;
  $pex_qual_partic_categ   = trim($_POST['pex_qual_partic_categ']) !== '' ? trim($_POST['pex_qual_partic_categ']) : NULL;
  $pex_formacao            = trim($_POST['pex_formacao']) !== '' ? nl2br(trim($_POST['pex_formacao'])) : NULL;
  $pex_lattes              = trim($_POST['pex_lattes']) !== '' ? trim($_POST['pex_lattes']) : NULL;
  $reservm_user_id            = $_SESSION['reservm_user_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_equipe_executora WHERE pex_nome = :pex_nome AND pex_proposta_id = :pex_proposta_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':pex_nome'        => $pex_nome,
    ':pex_proposta_id' => $pex_proposta_id
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este nome já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  }
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_equipe_executora WHERE pex_email = :pex_email AND pex_proposta_id = :pex_proposta_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':pex_email'       => $pex_email,
    ':pex_proposta_id' => $pex_proposta_id
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este e-mail já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
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
                                                      GETDATE(),
                                                      GETDATE()
                                                      )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':pex_id' => $pex_id,
      ':pex_proposta_id' => $pex_proposta_id,
      ':pex_nome' => $pex_nome,
      ':pex_email' => $pex_email,
      ':pex_contato' => $pex_contato,
      ':pex_partic_categ' => $pex_partic_categ,
      ':pex_qual_partic_categ' => $pex_qual_partic_categ,
      ':pex_partic_perfil' => $pex_partic_perfil,
      ':pex_outro_partic_perfil' => $pex_outro_partic_perfil,
      ':pex_carga_hora' => $pex_carga_hora,
      ':pex_area_atuacao' => $pex_area_atuacao,
      ':pex_nome_area_atuacao' => $pex_nome_area_atuacao,
      ':pex_formacao' => $pex_formacao,
      ':pex_lattes' => $pex_lattes,
      ':pex_user_id' => $reservm_user_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'PROPOSTA - EQUIPE EXECUTORA',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $pex_id,
      ':dados'      =>
      'ID Proposta: ' . $pex_proposta_id .
        '; Nome: ' . $pex_nome .
        '; E-mail: ' . $pex_email .
        '; Contato: ' . $pex_contato .
        '; Categoria de Participação: ' . $pex_partic_categ .
        '; Outra Categoria de Participação: ' . $pex_qual_partic_categ .
        '; Perfil: ' . $pex_partic_perfil .
        '; Outro Perfil: ' . $pex_outro_partic_perfil .
        '; Carga horária: ' . $pex_carga_hora .
        '; Área: ' . $pex_area_atuacao .
        '; Nome Área: ' . $pex_nome_area_atuacao .
        '; Formação: ' . $pex_formacao .
        '; Lattes: ' . $pex_lattes,
      ':user_id'    => $reservm_user_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Cadastro realizado com sucesso!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#pex_ancora");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Cadastro não realizado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
  }
}















/*****************************************************************************************
                              EDITAR EQUIPE EXECUTORA
 *****************************************************************************************/
if (isset($dados['EditCoordProjeto'])) {

  $pex_id                  = base64_decode($_POST['pex_id']);
  $pex_proposta_id         = base64_decode($_POST['pex_proposta_id']);
  //
  $pex_nome                = trim($_POST['pex_nome']) !== '' ? trim($_POST['pex_nome']) : NULL;
  $pex_email               = trim($_POST['pex_email']) !== '' ? trim($_POST['pex_email']) : NULL;
  $pex_contato             = trim($_POST['pex_contato']) !== '' ? trim($_POST['pex_contato']) : NULL;
  $pex_carga_hora          = trim($_POST['pex_carga_hora']) !== '' ? trim($_POST['pex_carga_hora']) : NULL;
  $pex_partic_perfil       = trim($_POST['pex_partic_perfil']) !== '' ? trim($_POST['pex_partic_perfil']) : NULL;
  $pex_outro_partic_perfil = trim($_POST['pex_outro_partic_perfil']) !== '' ? trim($_POST['pex_outro_partic_perfil']) : NULL;
  $pex_area_atuacao        = trim($_POST['pex_area_atuacao']) !== '' ? trim($_POST['pex_area_atuacao']) : NULL;
  $pex_nome_area_atuacao   = trim($_POST['pex_nome_area_atuacao']) !== '' ? trim($_POST['pex_nome_area_atuacao']) : NULL;
  $pex_partic_categ        = trim($_POST['pex_partic_categ']) !== '' ? trim($_POST['pex_partic_categ']) : NULL;
  $pex_qual_partic_categ   = trim($_POST['pex_qual_partic_categ']) !== '' ? trim($_POST['pex_qual_partic_categ']) : NULL;
  $pex_formacao            = trim($_POST['pex_formacao']) !== '' ? nl2br(trim($_POST['pex_formacao'])) : NULL;
  $pex_lattes              = trim($_POST['pex_lattes']) !== '' ? trim($_POST['pex_lattes']) : NULL;
  $reservm_user_id            = $_SESSION['reservm_user_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_equipe_executora WHERE pex_nome = :pex_nome AND pex_proposta_id = :pex_proposta_id AND pex_id != :pex_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':pex_id'          => $pex_id,
    ':pex_nome'        => $pex_nome,
    ':pex_proposta_id' => $pex_proposta_id
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este nome já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  }
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_equipe_executora WHERE pex_email = :pex_email AND pex_proposta_id = :pex_proposta_id AND pex_id != :pex_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':pex_id'          => $pex_id,
    ':pex_email'       => $pex_email,
    ':pex_proposta_id' => $pex_proposta_id
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este e-mail já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "UPDATE
                    propostas_equipe_executora
              SET
                    pex_nome                = UPPER(:pex_nome),
                    pex_email               = LOWER(:pex_email),
                    pex_contato             = :pex_contato,
                    pex_partic_perfil       = :pex_partic_perfil,
                    pex_outro_partic_perfil = UPPER(:pex_outro_partic_perfil),
                    pex_carga_hora          = :pex_carga_hora,
                    pex_area_atuacao        = :pex_area_atuacao,
                    pex_nome_area_atuacao   = UPPER(:pex_nome_area_atuacao),
                    pex_partic_categ        = :pex_partic_categ,
                    pex_qual_partic_categ   = UPPER(:pex_qual_partic_categ),
                    pex_formacao            = :pex_formacao,
                    pex_lattes              = :pex_lattes,
                    pex_user_id             = :pex_user_id,
                    pex_data_upd            = GETDATE()
              WHERE
                    pex_id = :pex_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':pex_id' => $pex_id,
      ':pex_nome' => $pex_nome,
      ':pex_email' => $pex_email,
      ':pex_contato' => $pex_contato,
      ':pex_partic_perfil' => $pex_partic_perfil,
      ':pex_outro_partic_perfil' => $pex_outro_partic_perfil,
      ':pex_carga_hora' => $pex_carga_hora,
      ':pex_area_atuacao' => $pex_area_atuacao,
      ':pex_nome_area_atuacao' => $pex_nome_area_atuacao,
      ':pex_partic_categ' => $pex_partic_categ,
      ':pex_qual_partic_categ' => $pex_qual_partic_categ,
      ':pex_formacao' => $pex_formacao,
      ':pex_lattes' => $pex_lattes,
      ':pex_user_id' => $reservm_user_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'PROPOSTA - EQUIPE EXECUTORA',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $pex_id,
      ':dados'      =>
      'ID Proposta: ' . $pex_proposta_id .
        '; Nome: ' . $pex_nome .
        '; E-mail: ' . $pex_email .
        '; Contato: ' . $pex_contato .
        '; Perfil: ' . $pex_partic_perfil .
        '; Outro Perfil: ' . $pex_outro_partic_perfil .
        '; Carga horária: ' . $pex_carga_hora .
        '; Área: ' . $pex_area_atuacao .
        '; Nome Área: ' . $pex_nome_area_atuacao .
        '; Categoria de Participação: ' . $pex_partic_categ .
        '; Outra Categoria de Participação: ' . $pex_qual_partic_categ .
        '; Formação: ' . $pex_formacao .
        '; Lattes: ' . $pex_lattes,
      ':user_id'    => $reservm_user_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Dados atualizados com sucesso!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#pex_ancora");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Dados não atualizados!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
  }
}













/*****************************************************************************************
                              EXCLUIR EQUIPE EXECUTORA
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_equipe_exec") {

  $pex_id       = base64_decode($_GET['pex_id']);
  $reservm_user_id = $_SESSION['reservm_user_id'];

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "DELETE FROM propostas_equipe_executora WHERE pex_id = :pex_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':pex_id' => $pex_id]);

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {

      // REGISTRA AÇÃO NO LOG
      $stmt_log = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                                  VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt_log->execute([
        ':modulo'  => 'PROPOSTA - EQUIPE EXECUTORA',
        ':acao'    => 'EXCLUSÃO',
        ':acao_id' => $pex_id,
        ':user_id' => $reservm_user_id
      ]);
      // -------------------------------

      $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

      $_SESSION["msg"] = "Dados excluídos com sucesso!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#pex_ancora");
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
