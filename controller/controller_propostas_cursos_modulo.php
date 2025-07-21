<?php
session_start();
include '../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                            CADASTRAR PROPOSTA CURSOS MÓDULO
 *****************************************************************************************/
if (isset($dados['CadDisciplinaModulo'])) {

  $prop_cmod_id                = md5(uniqid(rand(), true)); // GERA UM ID UNICO
  $prop_cmod_prop_id           = base64_decode($_POST['prop_id']);
  // -------------------------------
  $prop_cmod_tipo_docente      = trim($_POST['prop_cmod_tipo_docente']) !== '' ? trim($_POST['prop_cmod_tipo_docente']) : NULL;
  if ($prop_cmod_tipo_docente  == 1) {
    $prop_cmod_nome_docente    = trim($_POST['prop_cmod_nome_docente_int']) !== '' ? trim($_POST['prop_cmod_nome_docente_int']) : NULL;
  } else {
    $prop_cmod_nome_docente    = trim($_POST['prop_cmod_nome_docente_ext']) !== '' ? trim($_POST['prop_cmod_nome_docente_ext']) : NULL;
  }
  // -------------------------------
  $prop_cmod_titulo            = trim($_POST['prop_cmod_titulo']) !== '' ? trim($_POST['prop_cmod_titulo']) : NULL;
  $prop_cmod_assunto           = trim($_POST['prop_cmod_assunto']) !== '' ? nl2br(trim($_POST['prop_cmod_assunto'])) : NULL;
  $prop_cmod_data_hora         = trim($_POST['prop_cmod_data_hora']) !== '' ? nl2br(trim($_POST['prop_cmod_data_hora'])) : NULL;
  $prop_cmod_organizacao       = trim($_POST['prop_cmod_organizacao']) !== '' ? trim($_POST['prop_cmod_organizacao']) : NULL;
  $prop_cmod_outra_organizacao = trim($_POST['prop_cmod_outra_organizacao']) !== '' ? nl2br(trim($_POST['prop_cmod_outra_organizacao'])) : NULL;
  $prop_cmod_forma_pagamento   = trim($_POST['prop_cmod_forma_pagamento']) !== '' ? trim($_POST['prop_cmod_forma_pagamento']) : NULL;
  $prop_cmod_curriculo         = trim($_POST['prop_cmod_curriculo']) !== '' ? nl2br(trim($_POST['prop_cmod_curriculo'])) : NULL;
  $reservm_user_id                = $_SESSION['reservm_user_id'];

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "INSERT INTO propostas_cursos_modulo (
                                                  prop_cmod_id,
                                                  prop_cmod_prop_id,
                                                  prop_cmod_tipo_docente,
                                                  prop_cmod_nome_docente,
                                                  prop_cmod_titulo,
                                                  prop_cmod_assunto,
                                                  prop_cmod_data_hora,
                                                  prop_cmod_organizacao,
                                                  prop_cmod_outra_organizacao,
                                                  prop_cmod_forma_pagamento,
                                                  prop_cmod_curriculo,
                                                  prop_cmod_user_id,
                                                  prop_cmod_data_cad,
                                                  prop_cmod_data_upd
                                                ) VALUES (
                                                  :prop_cmod_id,
                                                  :prop_cmod_prop_id,
                                                  :prop_cmod_tipo_docente,
                                                  UPPER(:prop_cmod_nome_docente),
                                                  UPPER(:prop_cmod_titulo),
                                                  :prop_cmod_assunto,
                                                  :prop_cmod_data_hora,
                                                  :prop_cmod_organizacao,
                                                  :prop_cmod_outra_organizacao,
                                                  :prop_cmod_forma_pagamento,
                                                  :prop_cmod_curriculo,
                                                  :prop_cmod_user_id,
                                                  GETDATE(),
                                                  GETDATE()
                                                )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':prop_cmod_id' => $prop_cmod_id,
      ':prop_cmod_prop_id' => $prop_cmod_prop_id,
      ':prop_cmod_tipo_docente' => $prop_cmod_tipo_docente,
      ':prop_cmod_nome_docente' => $prop_cmod_nome_docente,
      ':prop_cmod_titulo' => $prop_cmod_titulo,
      ':prop_cmod_assunto' => $prop_cmod_assunto,
      ':prop_cmod_data_hora' => $prop_cmod_data_hora,
      ':prop_cmod_organizacao' => $prop_cmod_organizacao,
      ':prop_cmod_outra_organizacao' => $prop_cmod_outra_organizacao,
      ':prop_cmod_forma_pagamento' => $prop_cmod_forma_pagamento,
      ':prop_cmod_curriculo' => $prop_cmod_curriculo,
      ':prop_cmod_user_id' => $reservm_user_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'PROPOSTA - CURSOS - DISCIPLINA / MÓDULO',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $prop_cmod_id,
      ':dados'      => 'Proposta: ' . $prop_cmod_prop_id . '; Tipo Docente: ' . $prop_cmod_tipo_docente . '; Docente: ' . $prop_cmod_nome_docente . '; Título: ' . $prop_cmod_titulo . '; Data Hora: ' . $prop_cmod_data_hora  . '; Organização: ' . $prop_cmod_organizacao  . '; Outra Organização: ' . $prop_cmod_outra_organizacao . '; Pagamento: ' . $prop_cmod_forma_pagamento,
      ':user_id'    => $reservm_user_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Cadastro realizado com sucesso!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#cmod_ancora");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Cadastro não realizado!" . $e->getMessage();
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
  }
}















/*****************************************************************************************
                                EDITAR PROPOSTA CURSOS MÓDULO
 *****************************************************************************************/
if (isset($dados['EditDisciplinaModulo'])) {

  $prop_cmod_id                = base64_decode($_POST['prop_cmod_id']);
  $prop_cmod_prop_id           = base64_decode($_POST['prop_cmod_prop_id']);
  // -------------------------------
  $prop_cmod_tipo_docente      = trim($_POST['prop_cmod_tipo_docente']) !== '' ? trim($_POST['prop_cmod_tipo_docente']) : NULL;
  if ($prop_cmod_tipo_docente  == 1) {
    $prop_cmod_nome_docente    = trim($_POST['prop_cmod_nome_docente_int']) !== '' ? trim($_POST['prop_cmod_nome_docente_int']) : NULL;
  } else {
    $prop_cmod_nome_docente    = trim($_POST['prop_cmod_nome_docente_ext']) !== '' ? trim($_POST['prop_cmod_nome_docente_ext']) : NULL;
  }
  // -------------------------------
  $prop_cmod_titulo            = trim($_POST['prop_cmod_titulo']) !== '' ? trim($_POST['prop_cmod_titulo']) : NULL;
  $prop_cmod_assunto           = trim($_POST['prop_cmod_assunto']) !== '' ? nl2br(trim($_POST['prop_cmod_assunto'])) : NULL;
  $prop_cmod_data_hora         = trim($_POST['prop_cmod_data_hora']) !== '' ? nl2br(trim($_POST['prop_cmod_data_hora'])) : NULL;
  $prop_cmod_organizacao       = trim($_POST['prop_cmod_organizacao']) !== '' ? trim($_POST['prop_cmod_organizacao']) : NULL;
  $prop_cmod_outra_organizacao = trim($_POST['prop_cmod_outra_organizacao']) !== '' ? nl2br(trim($_POST['prop_cmod_outra_organizacao'])) : NULL;
  $prop_cmod_forma_pagamento   = trim($_POST['prop_cmod_forma_pagamento']) !== '' ? trim($_POST['prop_cmod_forma_pagamento']) : NULL;
  $prop_cmod_curriculo         = trim($_POST['prop_cmod_curriculo']) !== '' ? nl2br(trim($_POST['prop_cmod_curriculo'])) : NULL;
  $reservm_user_id                = $_SESSION['reservm_user_id'];

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "UPDATE
                    propostas_cursos_modulo
              SET
                    prop_cmod_tipo_docente      = :prop_cmod_tipo_docente,
                    prop_cmod_nome_docente      = UPPER(:prop_cmod_nome_docente),
                    prop_cmod_titulo            = UPPER(:prop_cmod_titulo),
                    prop_cmod_assunto           = :prop_cmod_assunto,
                    prop_cmod_data_hora         = :prop_cmod_data_hora,
                    prop_cmod_organizacao       = :prop_cmod_organizacao,
                    prop_cmod_outra_organizacao = :prop_cmod_outra_organizacao,
                    prop_cmod_forma_pagamento   = :prop_cmod_forma_pagamento,
                    prop_cmod_curriculo         = :prop_cmod_curriculo,
                    prop_cmod_data_upd          = GETDATE()
              WHERE
                    prop_cmod_id                = :prop_cmod_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':prop_cmod_id' => $prop_cmod_id,
      ':prop_cmod_tipo_docente' => $prop_cmod_tipo_docente,
      ':prop_cmod_nome_docente' => $prop_cmod_nome_docente,
      ':prop_cmod_titulo' => $prop_cmod_titulo,
      ':prop_cmod_assunto' => $prop_cmod_assunto,
      ':prop_cmod_data_hora' => $prop_cmod_data_hora,
      ':prop_cmod_organizacao' => $prop_cmod_organizacao,
      ':prop_cmod_outra_organizacao' => $prop_cmod_outra_organizacao,
      ':prop_cmod_forma_pagamento' => $prop_cmod_forma_pagamento,
      ':prop_cmod_curriculo' => $prop_cmod_curriculo,
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'PROPOSTA - CURSOS - DISCIPLINA / MÓDULO',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $prop_cmod_id,
      ':dados'      => 'Proposta: ' . $prop_cmod_prop_id . '; Tipo Docente: ' . $prop_cmod_tipo_docente . '; Docente: ' . $prop_cmod_nome_docente . '; Título: ' . $prop_cmod_titulo . '; Data Hora: ' . $prop_cmod_data_hora  . '; Organização: ' . $prop_cmod_organizacao  . '; Outra Organização: ' . $prop_cmod_outra_organizacao . '; Pagamento: ' . $prop_cmod_forma_pagamento,
      ':user_id'    => $reservm_user_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Cadastro realizado com sucesso!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#cmod_ancora");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Cadastro não realizado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
  }
}















/*****************************************************************************************
                            EXCLUIR PROPOSTA CURSOS MÓDULO
 *****************************************************************************************/

if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_cmod") {

  $prop_cmod_id      = base64_decode($_GET['ident']);
  $prop_cmod_prop_id = base64_decode($_GET['i']);
  $reservm_user_id      = $_SESSION['reservm_user_id'];

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "DELETE FROM propostas_cursos_modulo WHERE prop_cmod_id = :prop_cmod_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':prop_cmod_id' => $prop_cmod_id]);

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {

      // REGISTRA AÇÃO NO LOG
      $stmt_log = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                                  VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt_log->execute([
        ':modulo'  => 'PROPOSTA - CURSOS - DISCIPLINA / MÓDULO',
        ':acao'    => 'EXCLUSÃO',
        ':acao_id' => $prop_cmod_id,
        ':user_id' => $reservm_user_id
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
