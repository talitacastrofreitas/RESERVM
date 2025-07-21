<?php
session_start();
include '../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                            CADASTRAR PROPOSTA CURSOS MÓDULO
 *****************************************************************************************/
if (isset($dados['CadDisciplinaModulo'])) {

  $prop_cmod_id                = md5(uniqid(rand(), true)); // GERA UM ID UNICO
  $prop_cmod_prop_id           = $_POST['prop_id'];
  //
  $prop_cmod_tipo_docente      = $_POST['prop_cmod_tipo_docente'];
  if ($prop_cmod_tipo_docente == 1) {
    $prop_cmod_nome_docente    = trim($_POST['prop_cmod_nome_docente_int']);
  } else {
    $prop_cmod_nome_docente    = trim($_POST['prop_cmod_nome_docente_ext']);
  }
  //
  $prop_cmod_titulo            = trim($_POST['prop_cmod_titulo']);
  $prop_cmod_assunto           = nl2br(trim($_POST['prop_cmod_assunto']));
  $prop_cmod_data_hora         = nl2br(trim($_POST['prop_cmod_data_hora']));
  //
  $prop_cmod_organizacao       = $_POST['prop_cmod_organizacao'];
  if ($prop_cmod_organizacao == 7) {
    $prop_cmod_outra_organizacao = nl2br(trim($_POST['prop_cmod_outra_organizacao']));
  } else {
    $prop_cmod_outra_organizacao = NULL;
  }
  //
  $prop_cmod_forma_pagamento   = $_POST['prop_cmod_forma_pagamento'];
  $prop_cmod_curriculo         = nl2br(trim($_POST['prop_cmod_curriculo']));
  // -------------------------------

  try {
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
                                                  :prop_cmod_data_cad,
                                                  :prop_cmod_data_upd
                                                )";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":prop_cmod_id", $prop_cmod_id);
    $stmt->bindParam(":prop_cmod_prop_id", $prop_cmod_prop_id);
    //
    $stmt->bindParam(":prop_cmod_tipo_docente", $prop_cmod_tipo_docente);
    $stmt->bindParam(":prop_cmod_nome_docente", $prop_cmod_nome_docente);
    $stmt->bindParam(":prop_cmod_titulo", $prop_cmod_titulo);
    $stmt->bindParam(":prop_cmod_assunto", $prop_cmod_assunto);
    $stmt->bindParam(":prop_cmod_data_hora", $prop_cmod_data_hora);
    $stmt->bindParam(":prop_cmod_organizacao", $prop_cmod_organizacao);
    $stmt->bindParam(":prop_cmod_outra_organizacao", $prop_cmod_outra_organizacao);
    $stmt->bindParam(":prop_cmod_forma_pagamento", $prop_cmod_forma_pagamento);
    $stmt->bindParam(":prop_cmod_curriculo", $prop_cmod_curriculo);
    //
    $stmt->bindParam(":prop_cmod_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":prop_cmod_data_cad", date('Y-m-d H:i:s'));
    $stmt->bindParam(":prop_cmod_data_upd", date('Y-m-d H:i:s'));
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'PROPOSTA - CURSOS - DISCIPLINA / MÓDULO',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $prop_cmod_id,
      ':dados'      => 'Proposta: ' . $prop_cmod_prop_id . '; Tipo Docente: ' . $prop_cmod_tipo_docente . '; Docente: ' . $prop_cmod_nome_docente . '; Título: ' . $prop_cmod_titulo . '; Data Hora: ' . $prop_cmod_data_hora  . '; Organização: ' . $prop_cmod_organizacao  . '; Outra Organização: ' . $prop_cmod_outra_organizacao . '; Pagamento: ' . $prop_cmod_forma_pagamento,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => date('Y-m-d H:i:s')
    ));
    // -------------------------------

    $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Cadastro realizado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#cmod_ancora");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Cadastro não realizado!" . $e->getMessage();
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}















/*****************************************************************************************
                                EDITAR PROPOSTA CURSOS MÓDULO
 *****************************************************************************************/
if (isset($dados['EditDisciplinaModulo'])) {

  $prop_cmod_id              = $_POST['prop_cmod_id'];
  $prop_cmod_prop_id     = $_POST['prop_cmod_prop_id'];
  //
  $prop_cmod_tipo_docente    = $_POST['prop_cmod_tipo_docente'];
  if ($prop_cmod_tipo_docente == 1) {
    $prop_cmod_nome_docente  = trim($_POST['prop_cmod_nome_docente_int']);
  } else {
    $prop_cmod_nome_docente  = trim($_POST['prop_cmod_nome_docente_ext']);
  }
  //
  $prop_cmod_titulo          = trim($_POST['prop_cmod_titulo']);
  $prop_cmod_assunto         = nl2br(trim($_POST['prop_cmod_assunto']));
  $prop_cmod_data_hora       = nl2br(trim($_POST['prop_cmod_data_hora']));
  //
  $prop_cmod_organizacao       = $_POST['prop_cmod_organizacao'];
  if ($prop_cmod_organizacao == 7) {
    $prop_cmod_outra_organizacao = nl2br(trim($_POST['prop_cmod_outra_organizacao']));
  } else {
    $prop_cmod_outra_organizacao = NULL;
  }
  //
  $prop_cmod_forma_pagamento = $_POST['prop_cmod_forma_pagamento'];
  $prop_cmod_curriculo       = nl2br(trim($_POST['prop_cmod_curriculo']));
  // -------------------------------

  try {
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
                    prop_cmod_data_upd          = :prop_cmod_data_upd
              WHERE
                    prop_cmod_id                = :prop_cmod_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":prop_cmod_id", $prop_cmod_id);
    //
    $stmt->bindParam(":prop_cmod_tipo_docente", $prop_cmod_tipo_docente);
    $stmt->bindParam(":prop_cmod_nome_docente", $prop_cmod_nome_docente);
    $stmt->bindParam(":prop_cmod_titulo", $prop_cmod_titulo);
    $stmt->bindParam(":prop_cmod_assunto", $prop_cmod_assunto);
    $stmt->bindParam(":prop_cmod_data_hora", $prop_cmod_data_hora);
    $stmt->bindParam(":prop_cmod_organizacao", $prop_cmod_organizacao);
    $stmt->bindParam(":prop_cmod_outra_organizacao", $prop_cmod_outra_organizacao);
    $stmt->bindParam(":prop_cmod_forma_pagamento", $prop_cmod_forma_pagamento);
    $stmt->bindParam(":prop_cmod_curriculo", $prop_cmod_curriculo);
    //
    $stmt->bindParam(":prop_cmod_data_upd", date('Y-m-d H:i:s'));
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'PROPOSTA - CURSOS - DISCIPLINA / MÓDULO',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $prop_cmod_id,
      ':dados'      => 'Proposta: ' . $prop_cmod_prop_id . '; Tipo Docente: ' . $prop_cmod_tipo_docente . '; Docente: ' . $prop_cmod_nome_docente . '; Título: ' . $prop_cmod_titulo . '; Data Hora: ' . $prop_cmod_data_hora  . '; Organização: ' . $prop_cmod_organizacao  . '; Outra Organização: ' . $prop_cmod_outra_organizacao . '; Pagamento: ' . $prop_cmod_forma_pagamento,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => date('Y-m-d H:i:s')
    ));
    // -------------------------------

    $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Cadastro realizado!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#cmod_ancora_edit_$prop_cmod_id");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Cadastro não realizado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}















/*****************************************************************************************
                            EXCLUIR PROPOSTA CURSOS MÓDULO
 *****************************************************************************************/

if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_cmod") {

  $prop_cmod_id          = $_GET['prop_cmod_id'];
  $prop_cmod_prop_id = $_GET['prop_cmod_prop_id'];

  try {
    $sql = "DELETE FROM propostas_cursos_modulo WHERE prop_cmod_id = '$prop_cmod_id'";
    $conn->exec($sql);
  } catch (PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
  }

  // REGISTRA AÇÃO NO LOG
  $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_acao_user_nome, log_data )
  VALUES ( :modulo, :acao, :acao_id, :user_id, :user_nome, :data )');
  $stmt->execute(array(
    ':modulo'    => 'PROPOSTA - CURSOS - DISCIPLINA / MÓDULO',
    ':acao'      => 'EXCLUSÃO',
    ':acao_id'   => $prop_cmod_id,
    ':user_id'   => $_SESSION['reservm_admin_id'],
    ':user_nome' => $_SESSION['reservm_admin_nome'],
    ':data'      => date('Y-m-d H:i:s')
  ));

  // QUANDO A ÚLTIMA DISCIPLINA/MODULO FOR EXCLUÍDA, ESCLUI CURSO, TAMBÉM. 
  $sql = "SELECT COUNT(*) FROM propostas_cursos_modulo WHERE prop_cmod_prop_id = :prop_cmod_prop_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":prop_cmod_prop_id", $prop_cmod_prop_id);
  $stmt->execute();
  if (!$stmt->fetchColumn()) {
    try {
      $sql = "DELETE FROM propostas_cursos WHERE prop_curs_id = '$prop_cmod_prop_id'";
      $conn->exec($sql);
    } catch (PDOException $e) {
      echo $sql . "<br>" . $e->getMessage();
    }

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_acao_user_nome, log_data )
    VALUES ( :modulo, :acao, :acao_id, :user_id, :user_nome, :data )');
    $stmt->execute(array(
      ':modulo'    => 'PROPOSTA - CURSOS',
      ':acao'      => 'EXCLUSÃO',
      ':acao_id'   => $prop_cmod_prop_id,
      ':user_id'   => $_SESSION['reservm_admin_id'],
      ':user_nome' => $_SESSION['reservm_admin_nome'],
      ':data'      => date('Y-m-d H:i:s')
    ));
    // -------------------------------
  }

  $conn = null;

  $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i>Dados excluídos!";
  header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
}
