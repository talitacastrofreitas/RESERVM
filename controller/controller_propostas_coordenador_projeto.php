<?php
session_start();
include '../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                          CADASTRAR COORDENADOR DO PROJETO
 *****************************************************************************************/
if (isset($dados['CadCoordProjeto'])) {

  $pcp_id                  = md5(uniqid(rand(), true)); // GERA UM ID UNICO
  $pcp_proposta_id         = base64_decode($_POST['pcp_proposta_id']);
  //
  $pcp_nome                = trim($_POST['pcp_nome']) !== '' ? trim($_POST['pcp_nome']) : NULL;
  $pcp_email               = trim($_POST['pcp_email']) !== '' ? trim($_POST['pcp_email']) : NULL;
  $pcp_contato             = trim($_POST['pcp_contato']) !== '' ? trim($_POST['pcp_contato']) : NULL;
  $pcp_carga_hora          = trim($_POST['pcp_carga_hora']) !== '' ? trim($_POST['pcp_carga_hora']) : NULL;
  $pcp_partic_perfil       = trim($_POST['pcp_partic_perfil']) !== '' ? trim($_POST['pcp_partic_perfil']) : NULL;
  $pcp_outro_partic_perfil = trim($_POST['pcp_outro_partic_perfil']) !== '' ? trim($_POST['pcp_outro_partic_perfil']) : NULL;
  $pcp_area_atuacao        = trim($_POST['pcp_area_atuacao']) !== '' ? trim($_POST['pcp_area_atuacao']) : NULL;
  $pcp_nome_area_atuacao   = trim($_POST['pcp_nome_area_atuacao']) !== '' ? trim($_POST['pcp_nome_area_atuacao']) : NULL;
  $pcp_formacao            = trim($_POST['pcp_formacao']) !== '' ? nl2br(trim($_POST['pcp_formacao'])) : NULL;
  $pcp_lattes              = trim($_POST['pcp_lattes']) !== '' ? trim($_POST['pcp_lattes']) : NULL;
  $reservm_user_id            = $_SESSION['reservm_user_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_coordenador_projeto WHERE pcp_nome = :pcp_nome AND pcp_proposta_id = :pcp_proposta_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':pcp_nome'        => $pcp_nome,
    ':pcp_proposta_id' => $pcp_proposta_id
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este nome já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  }
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_coordenador_projeto WHERE pcp_email = :pcp_email AND pcp_proposta_id = :pcp_proposta_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':pcp_email'       => $pcp_email,
    ':pcp_proposta_id' => $pcp_proposta_id
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
                                                        GETDATE(),
                                                        GETDATE()
                                                      )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':pcp_id' => $pcp_id,
      ':pcp_proposta_id' => $pcp_proposta_id,
      ':pcp_nome' => $pcp_nome,
      ':pcp_email' => $pcp_email,
      ':pcp_contato' => $pcp_contato,
      ':pcp_partic_perfil' => $pcp_partic_perfil,
      ':pcp_outro_partic_perfil' => $pcp_outro_partic_perfil,
      ':pcp_carga_hora' => $pcp_carga_hora,
      ':pcp_area_atuacao' => $pcp_area_atuacao,
      ':pcp_nome_area_atuacao' => $pcp_nome_area_atuacao,
      ':pcp_formacao' => $pcp_formacao,
      ':pcp_lattes' => $pcp_lattes,
      ':pcp_user_id' => $reservm_user_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt_log = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt_log->execute([
      ':modulo'     => 'PROPOSTA - COORDENADOR PROJETO',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $pcp_id,
      ':dados'      =>
      'ID Proposta: ' . $pcp_proposta_id .
        '; Nome: ' . $pcp_nome .
        '; E-mail: ' . $pcp_email .
        '; Contato: ' . $pcp_contato .
        '; Perfil: ' . $pcp_partic_perfil .
        '; Outro Perfil: ' . $pcp_outro_partic_perfil .
        '; Carga horária: ' . $pcp_carga_hora .
        '; Área: ' . $pcp_area_atuacao .
        '; Nome Área: ' . $pcp_nome_area_atuacao .
        '; Formação: ' . $pcp_formacao .
        '; Lattes: ' . $pcp_lattes,
      ':user_id'    => $reservm_user_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Cadastro realizado com sucesso!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#pcp_ancora");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Cadastro não realizado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  }
}















/*****************************************************************************************
                              EDITAR COORDENADOR DO PROJETO
 *****************************************************************************************/
if (isset($dados['EditCoordProjeto'])) {

  $pcp_id                  = base64_decode($_POST['pcp_id']);
  $pcp_proposta_id         = base64_decode($_POST['pcp_proposta_id']);
  //
  $pcp_nome                = trim($_POST['pcp_nome']) !== '' ? trim($_POST['pcp_nome']) : NULL;
  $pcp_email               = trim($_POST['pcp_email']) !== '' ? trim($_POST['pcp_email']) : NULL;
  $pcp_contato             = trim($_POST['pcp_contato']) !== '' ? trim($_POST['pcp_contato']) : NULL;
  $pcp_carga_hora          = trim($_POST['pcp_carga_hora']) !== '' ? trim($_POST['pcp_carga_hora']) : NULL;
  $pcp_partic_perfil       = trim($_POST['pcp_partic_perfil']) !== '' ? trim($_POST['pcp_partic_perfil']) : NULL;
  $pcp_outro_partic_perfil = trim($_POST['pcp_outro_partic_perfil']) !== '' ? trim($_POST['pcp_outro_partic_perfil']) : NULL;
  $pcp_area_atuacao        = trim($_POST['pcp_area_atuacao']) !== '' ? trim($_POST['pcp_area_atuacao']) : NULL;
  $pcp_nome_area_atuacao   = trim($_POST['pcp_nome_area_atuacao']) !== '' ? trim($_POST['pcp_nome_area_atuacao']) : NULL;
  $pcp_formacao            = trim($_POST['pcp_formacao']) !== '' ? nl2br(trim($_POST['pcp_formacao'])) : NULL;
  $pcp_lattes              = trim($_POST['pcp_lattes']) !== '' ? trim($_POST['pcp_lattes']) : NULL;
  $reservm_user_id            = $_SESSION['reservm_user_id'];
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_coordenador_projeto WHERE pcp_nome = :pcp_nome AND pcp_proposta_id = :pcp_proposta_id AND pcp_id != :pcp_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':pcp_id'          => $pcp_id,
    ':pcp_nome'        => $pcp_nome,
    ':pcp_proposta_id' => $pcp_proposta_id
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este nome já foi cadastrado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  }
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM propostas_coordenador_projeto WHERE pcp_email = :pcp_email AND pcp_proposta_id = :pcp_proposta_id AND pcp_id != :pcp_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':pcp_id'          => $pcp_id,
    ':pcp_email'       => $pcp_email,
    ':pcp_proposta_id' => $pcp_proposta_id
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
                    pcp_data_upd            = GETDATE()
              WHERE
                    pcp_id = :pcp_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':pcp_id' => $pcp_id,
      ':pcp_nome' => $pcp_nome,
      ':pcp_email' => $pcp_email,
      ':pcp_contato' => $pcp_contato,
      ':pcp_partic_perfil' => $pcp_partic_perfil,
      ':pcp_outro_partic_perfil' => $pcp_outro_partic_perfil,
      ':pcp_carga_hora' => $pcp_carga_hora,
      ':pcp_area_atuacao' => $pcp_area_atuacao,
      ':pcp_nome_area_atuacao' => $pcp_nome_area_atuacao,
      ':pcp_formacao' => $pcp_formacao,
      ':pcp_lattes' => $pcp_lattes,
      ':pcp_user_id' => $reservm_user_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt_log = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt_log->execute([
      ':modulo'     => 'PROPOSTA - COORDENADOR PROJETO',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $pcp_id,
      ':dados'      =>
      'ID Proposta: ' . $pcp_proposta_id .
        '; Nome: ' . $pcp_nome .
        '; E-mail: ' . $pcp_email .
        '; Contato: ' . $pcp_contato .
        '; Perfil: ' . $pcp_partic_perfil .
        '; Outro Perfil: ' . $pcp_outro_partic_perfil .
        '; Carga horária: ' . $pcp_carga_hora .
        '; Área: ' . $pcp_area_atuacao .
        '; Nome Área: ' . $pcp_nome_area_atuacao .
        '; Formação: ' . $pcp_formacao .
        '; Lattes: ' . $pcp_lattes,
      ':user_id'    => $reservm_user_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Dados atualizados com sucesso!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#pcp_ancora");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Dados não atualizados!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
  }
}













/*****************************************************************************************
                              EXCLUIR COORDENADOR DO PROJETO
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_coord") {

  $pcp_id       = base64_decode($_GET['pcp_id']);
  $reservm_user_id = $_SESSION['reservm_user_id'];

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "DELETE FROM propostas_coordenador_projeto WHERE pcp_id = :pcp_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':pcp_id' => $pcp_id]);

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {

      // REGISTRA AÇÃO NO LOG
      $stmt_log = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                                  VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt_log->execute([
        ':modulo'  => 'PROPOSTA - COORDENADOR PROJETO',
        ':acao'    => 'EXCLUSÃO',
        ':acao_id' => $pcp_id,
        ':user_id' => $reservm_user_id
      ]);
      // -------------------------------

      $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

      $_SESSION["msg"] = "Dados excluídos com sucesso!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#pcp_ancora");
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
