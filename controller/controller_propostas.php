<?php
session_start();
include '../conexao/conexao.php';

// NECESSÁRIO PARA ENVIAR O EMAIL
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
// -------------------------------

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                                CADASTRAR PROPOSTA - ETAPA 1
 *****************************************************************************************/
// if (isset($dados['CadProposta'])) {
if (isset($_GET['funcao']) && $_GET['funcao'] == "cad_step1") {

  $prop_id                    = md5(uniqid(rand(), true));  // GERA UM ID UNICO
  $prop_tipo                  = base64_decode($_POST['prop_tipo']);
  $prop_codigo                = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT); // GERA UM CÓDIGO COM 6 DÍGITOS 
  $prop_status_etapa          = 1; // NÚMERO DA ETAPA 
  $prop_titulo                = trim($_POST['prop_titulo']) !== '' ? trim($_POST['prop_titulo']) : NULL;
  $prop_descricao             = trim($_POST['prop_descricao']) !== '' ? nl2br(trim($_POST['prop_descricao'])) : NULL;
  $prop_vinculo_programa      = trim(isset($_POST['prop_vinculo_programa'])) ? $_POST['prop_vinculo_programa'] : 0;
  $prop_qual_vinculo_programa = trim($_POST['prop_qual_vinculo_programa']) !== '' ? nl2br(trim($_POST['prop_qual_vinculo_programa'])) : NULL;
  $prop_curso_vinculo         = trim($_POST['prop_curso_vinculo']) !== '' ? trim($_POST['prop_curso_vinculo']) : NULL;
  $prop_nome_curso_vinculo    = trim($_POST['prop_nome_curso_vinculo']) !== '' ? trim($_POST['prop_nome_curso_vinculo']) : NULL;
  $prop_justificativa         = trim($_POST['prop_justificativa']) !== '' ? nl2br(trim($_POST['prop_justificativa'])) : NULL;
  $prop_obj_pedagogico        = trim($_POST['prop_obj_pedagogico']) !== '' ? nl2br(trim($_POST['prop_obj_pedagogico'])) : NULL;
  $prop_publico_alvo          = trim($_POST['prop_publico_alvo']) !== '' ? nl2br(trim($_POST['prop_publico_alvo'])) : NULL;

  // CADASTRA DADOS DO CHECKBOX
  if (isset($_POST["prop_area_conhecimento"])) {
    foreach ($_POST['prop_area_conhecimento'] as $valor) {
      @$prop_area_conhecimento .= $valor . ",";
    }
  } else {
    $prop_area_conhecimento = NULL;
  }
  // -------------------------------

  // CADASTRA DADOS DO CHECKBOX
  if (isset($_POST["prop_area_tematica"])) {
    foreach ($_POST['prop_area_tematica'] as $valor) {
      @$prop_area_tematica .= $valor . ",";
    }
  } else {
    $prop_area_tematica = NULL;
  }
  // -------------------------------

  // CADASTRA DIAS DA SEMANA
  if (isset($_POST["prop_semana"])) {
    foreach ($_POST['prop_semana'] as $valor) {
      @$prop_semana .= $valor . ",";
    }
  } else {
    $prop_semana = NULL;
  }
  // -------------------------------

  $prop_horario                  = trim($_POST['prop_horario']) !== '' ? nl2br(trim($_POST['prop_horario'])) : NULL;
  $prop_data_inicio              = trim($_POST['prop_data_inicio']) !== '' ? trim($_POST['prop_data_inicio']) : NULL;
  $prop_data_fim                 = trim($_POST['prop_data_fim']) !== '' ? trim($_POST['prop_data_fim']) : NULL;
  $prop_carga_hora               = trim($_POST['prop_carga_hora']) !== '' ? trim($_POST['prop_carga_hora']) : NULL;
  $prop_total_vaga               = trim($_POST['prop_total_vaga']) !== '' ? trim($_POST['prop_total_vaga']) : NULL;
  $prop_quant_turma              = trim($_POST['prop_quant_turma']) !== '' ? trim($_POST['prop_quant_turma']) : NULL;
  $prop_modalidade               = trim($_POST['prop_modalidade']) !== '' ? trim($_POST['prop_modalidade']) : NULL;
  $prop_campus                   = trim($_POST['prop_campus']) !== '' ? trim($_POST['prop_campus']) : NULL;
  $prop_local                    = trim($_POST['prop_local']) !== '' ? trim($_POST['prop_local']) : NULL;
  $prop_forma_acesso             = trim($_POST['prop_forma_acesso']) !== '' ? trim($_POST['prop_forma_acesso']) : NULL;
  $prop_preco                    = trim($_POST['prop_preco']) !== '' ? trim($_POST['prop_preco']) : NULL;
  $prop_preco_parcelas           = trim($_POST['prop_preco_parcelas']) !== '' ? trim($_POST['prop_preco_parcelas']) : NULL;
  $prop_outra_forma_acesso       = trim($_POST['prop_outra_forma_acesso']) !== '' ? trim($_POST['prop_outra_forma_acesso']) : NULL;
  $prop_acao_acessibilidade      = trim(isset($_POST['prop_acao_acessibilidade'])) ? $_POST['prop_acao_acessibilidade'] : 0;
  $prop_desc_acao_acessibilidade = trim($_POST['prop_desc_acao_acessibilidade']) !== '' ? nl2br(trim($_POST['prop_desc_acao_acessibilidade'])) : NULL;
  $prop_ofertas_vagas            = trim(isset($_POST['prop_ofertas_vagas'])) ? $_POST['prop_ofertas_vagas'] : 0;
  $prop_quant_beneficios         = trim($_POST['prop_quant_beneficios']) !== '' ? trim($_POST['prop_quant_beneficios']) : NULL;
  $prop_atendimento_doacao       = trim(isset($_POST['prop_atendimento_doacao'])) ? $_POST['prop_atendimento_doacao'] : 0;
  $prop_desc_beneficios          = trim($_POST['prop_desc_beneficios']) !== '' ? nl2br(trim($_POST['prop_desc_beneficios'])) : NULL;
  $prop_comunidade               = trim($_POST['prop_comunidade']) !== '' ? nl2br(trim($_POST['prop_comunidade'])) : NULL;
  $prop_localidade               = trim($_POST['prop_localidade']) !== '' ? nl2br(trim($_POST['prop_localidade'])) : NULL;
  $prop_responsavel              = trim($_POST['prop_responsavel']) !== '' ? nl2br(trim($_POST['prop_responsavel'])) : NULL;
  $prop_responsavel_contato      = trim($_POST['prop_responsavel_contato']) !== '' ? nl2br(trim($_POST['prop_responsavel_contato'])) : NULL;
  $prop_info_complementar        = trim($_POST['prop_info_complementar']) !== '' ? nl2br(trim($_POST['prop_info_complementar'])) : NULL;
  $reservm_user_id                  = $_SESSION['reservm_user_id'];

  // DATA DE INÍCIO NÃO PODE SER MENOR QUE A DATA DE HOJE
  if (!empty($prop_data_inicio) && strtotime($prop_data_inicio) < strtotime('today')) {
    $_SESSION["erro"] = "A data de início pode ser maior que a data de hoje!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  }
  // -------------------------------

  // DATA DE FIM DEVE SER MENOR QUE A DATA DE INÍCIO
  if (!empty($prop_data_fim) && strtotime($prop_data_fim) < strtotime($prop_data_inicio)) {
    $_SESSION["erro"] = "A data de finalização pode ser maior que a data de início!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  }
  // -------------------------------

  // SE TIPO NÃO FOR ENCONTRADO IMPEDE O CADASTRO
  $tipos = array(1, 2, 3, 4, 5);
  if (!in_array($prop_tipo, $tipos)) {
    $_SESSION["erro"] = "Categoria da proposta não encontrada!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  } else {

    try {
      $conn->beginTransaction(); // INICIA A TRANSAÇÃO
      $sql = "INSERT INTO propostas (
                                      prop_id,
                                      prop_tipo,
                                      prop_codigo,
                                      prop_status_etapa,
                                      prop_titulo,
                                      prop_descricao,
                                      prop_vinculo_programa,
                                      prop_qual_vinculo_programa,
                                      prop_curso_vinculo,
                                      prop_nome_curso_vinculo,
                                      prop_justificativa,
                                      prop_obj_pedagogico,
                                      prop_publico_alvo,
                                      prop_area_conhecimento,
                                      prop_area_tematica,
                                      prop_semana,
                                      prop_horario,
                                      prop_data_inicio,
                                      prop_data_fim,
                                      prop_carga_hora,
                                      prop_total_vaga,
                                      prop_quant_turma,
                                      prop_forma_acesso,
                                      prop_outra_forma_acesso,
                                      prop_modalidade,
                                      prop_campus,
                                      prop_local,
                                      prop_preco,
                                      prop_preco_parcelas,
                                      prop_acao_acessibilidade,
                                      prop_desc_acao_acessibilidade,
                                      prop_ofertas_vagas,
                                      prop_atendimento_doacao,
                                      prop_quant_beneficios,
                                      prop_desc_beneficios,
                                      prop_comunidade,
                                      prop_localidade,
                                      prop_responsavel,
                                      prop_responsavel_contato,
                                      prop_info_complementar,
                                      prop_user_id,
                                      prop_data_cad,
                                      prop_data_upd
                                    ) VALUES (
                                      :prop_id,
                                      :prop_tipo,
                                      :prop_codigo,
                                      :prop_status_etapa,
                                      UPPER(:prop_titulo),
                                      :prop_descricao,
                                      :prop_vinculo_programa,
                                      :prop_qual_vinculo_programa,
                                      :prop_curso_vinculo,
                                      UPPER(:prop_nome_curso_vinculo),
                                      :prop_justificativa,
                                      :prop_obj_pedagogico,
                                      :prop_publico_alvo,
                                      :prop_area_conhecimento,
                                      :prop_area_tematica,
                                      :prop_semana,
                                      :prop_horario,
                                      :prop_data_inicio,
                                      :prop_data_fim,
                                      :prop_carga_hora,
                                      :prop_total_vaga,
                                      :prop_quant_turma,
                                      :prop_forma_acesso,
                                      UPPER(:prop_outra_forma_acesso),
                                      :prop_modalidade,
                                      :prop_campus,
                                      UPPER(:prop_local),
                                      :prop_preco,
                                      :prop_preco_parcelas,
                                      :prop_acao_acessibilidade,
                                      :prop_desc_acao_acessibilidade,
                                      :prop_ofertas_vagas,
                                      :prop_atendimento_doacao,
                                      :prop_quant_beneficios,
                                      :prop_desc_beneficios,
                                      :prop_comunidade,
                                      :prop_localidade,
                                      :prop_responsavel,
                                      :prop_responsavel_contato,
                                      :prop_info_complementar,
                                      :prop_user_id,
                                      GETDATE(),
                                      GETDATE()
                                    )";

      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':prop_id' => $prop_id,
        ':prop_tipo' => $prop_tipo,
        ':prop_codigo' => $prop_codigo,
        ':prop_status_etapa' => $prop_status_etapa,
        ':prop_titulo' => $prop_titulo,
        ':prop_descricao' => $prop_descricao,
        ':prop_vinculo_programa' => $prop_vinculo_programa,
        ':prop_qual_vinculo_programa' => $prop_qual_vinculo_programa,
        ':prop_curso_vinculo' => $prop_curso_vinculo,
        ':prop_nome_curso_vinculo' => $prop_nome_curso_vinculo,
        ':prop_justificativa' => $prop_justificativa,
        ':prop_obj_pedagogico' => $prop_obj_pedagogico,
        ':prop_publico_alvo' => $prop_publico_alvo,
        ':prop_area_conhecimento' => $prop_area_conhecimento,
        ':prop_area_tematica' => $prop_area_tematica,
        ':prop_semana' => $prop_semana,
        ':prop_horario' => $prop_horario,
        ':prop_data_inicio' => $prop_data_inicio,
        ':prop_data_fim' => $prop_data_fim,
        ':prop_carga_hora' => $prop_carga_hora,
        ':prop_total_vaga' => $prop_total_vaga,
        ':prop_quant_turma' => $prop_quant_turma,
        ':prop_forma_acesso' => $prop_forma_acesso,
        ':prop_outra_forma_acesso' => $prop_outra_forma_acesso,
        ':prop_modalidade' => $prop_modalidade,
        ':prop_campus' => $prop_campus,
        ':prop_local' => $prop_local,
        ':prop_preco' => $prop_preco,
        ':prop_preco_parcelas' => $prop_preco_parcelas,
        ':prop_acao_acessibilidade' => $prop_acao_acessibilidade,
        ':prop_desc_acao_acessibilidade' => $prop_desc_acao_acessibilidade,
        ':prop_ofertas_vagas' => $prop_ofertas_vagas,
        ':prop_atendimento_doacao' => $prop_atendimento_doacao,
        ':prop_quant_beneficios' => $prop_quant_beneficios,
        ':prop_desc_beneficios' => $prop_desc_beneficios,
        ':prop_comunidade' => $prop_comunidade,
        ':prop_localidade' => $prop_localidade,
        ':prop_responsavel' => $prop_responsavel,
        ':prop_responsavel_contato' => $prop_responsavel_contato,
        ':prop_info_complementar' => $prop_info_complementar,
        ':prop_user_id' => $reservm_user_id
      ]);

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                              VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'     => 'PROPOSTA - ETAPA 1',
        ':acao'       => 'CADASTRO',
        ':acao_id'    => $prop_id,
        ':dados'      =>  'Etapa: ' . $prop_status_etapa .
          '; Tipo: ' . $prop_tipo .
          '; Código: ' . $prop_codigo .
          '; Etapa: ' . $prop_status_etapa .
          '; Título: ' . $prop_titulo .
          '; Descrição: ' . $prop_descricao .
          '; Vínculo: ' . $prop_vinculo_programa .
          '; Qual Vínculo: ' . $prop_qual_vinculo_programa .
          '; Curso vínculo: ' . $prop_curso_vinculo .
          '; Nome Curso vínculo: ' . $prop_nome_curso_vinculo .
          '; Justificativa: ' . $prop_justificativa .
          '; Objetivos: ' . $prop_obj_pedagogico .
          '; Público Alvo: ' . $prop_publico_alvo .
          '; Área do Conhecimento: ' . $prop_area_conhecimento .
          '; Áreas Temáticas: ' . $prop_area_tematica .
          '; Semana: ' . $prop_semana .
          '; Horário: ' . $prop_horario .
          '; Data Início: ' . $prop_data_inicio .
          '; Data Fim: ' . $prop_data_fim .
          '; Carga Hora: ' . $prop_carga_hora .
          '; Total Vaga: ' . $prop_total_vaga .
          '; Quantidade Turma: ' . $prop_quant_turma .
          '; Forma Acesso: ' . $prop_forma_acesso .
          '; Outra Forma Acesso: ' . $prop_outra_forma_acesso .
          '; Modalidade: ' . $prop_modalidade .
          '; Campus: ' . $prop_campus .
          '; Local: ' . $prop_local .
          '; Preço: ' . $prop_preco .
          '; Preço Parcela: ' . $prop_preco_parcelas .
          '; Acessibilidade: ' . $prop_acao_acessibilidade .
          '; Desc. Acessibilidade: ' . $prop_desc_acao_acessibilidade .
          '; Oferta Vagas: ' . $prop_ofertas_vagas .
          '; Atendimento/Doação: ' . $prop_atendimento_doacao .
          '; Quant. Benefícios: ' . $prop_quant_beneficios .
          '; Benefícios: ' . $prop_desc_beneficios .
          '; Comunidade: ' . $prop_comunidade .
          '; Localidade: ' . $prop_localidade .
          '; Responsável: ' . $prop_responsavel .
          '; Responsável Contato: ' . $prop_responsavel_contato .
          '; Info. Complementar: ' . $prop_info_complementar,
        ':user_id'    => $reservm_user_id
      ]);
      // -------------------------------

      // CADASTRA O STATUS DA PROPOSTA
      $num_status  = 1; // CADASTRO PENDENTE
      $sql = "INSERT INTO propostas_status (
                                            prop_sta_prop_id,
                                            prop_sta_status,
                                            prop_sta_user_id,
                                            prop_sta_data_cad
                                            ) VALUES (
                                            :prop_sta_prop_id,
                                            :prop_sta_status,
                                            :prop_sta_user_id,
                                            GETDATE()
                                            )";
      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':prop_sta_prop_id' => $prop_id,
        ':prop_sta_status'  => $num_status,
        ':prop_sta_user_id' => $reservm_user_id
      ]);
      // -------------------------------

      // CADASTRA O STATUS DA ANÁLISE
      $sql = "INSERT INTO propostas_analise_status (
                                                    sta_an_prop_id,
                                                    sta_an_status,
                                                    sta_an_user_id,
                                                    sta_an_data_cad,
                                                    sta_an_data_upd
                                                    ) VALUES (
                                                    :sta_an_prop_id,
                                                    :sta_an_status,
                                                    :sta_an_user_id,
                                                    GETDATE(),
                                                    GETDATE()
                                                    )";

      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':sta_an_prop_id' => $prop_id,
        ':sta_an_status'  => $num_status,
        ':sta_an_user_id' => $reservm_user_id
      ]);
      // -------------------------------

      // ENVIA E-MAIL PARA USUÁRIO
      $mail = new PHPMailer(true);
      include '../controller/email_conf.php';
      $mail->addAddress($_SESSION['reservm_user_email'], 'RESERVM'); // E-MAIL DO USUÁRIO QUE RECEBERÁ A MENSAGEM
      $mail->isHTML(true);
      $mail->Subject = $prop_codigo . ' - Cadastro de Proposta Iniciada'; //TÍTULO DO E-MAIL

      // CORPO DO EMAIL
      include '../includes/email/email_header.php';
      $email_conteudo .= "
        <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
          <td style='padding: 2em 2rem; display: inline-block; width: 100%'>

          <p style='font-size: 1.3rem; font-weight: 600; margin: 0px 0px 40px 0px;'>Cadastro de proposta iniciado!</p>

          <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 10px 0px;'>Seu cadastro da proposta de código: <strong> $prop_codigo </strong> foi iniciado. <br> Conclua o cadastro para que nossa equipe inicie a análise da proposta.</p>

          <a style='cursor: pointer;' href='$url_sistema'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 30px;' target='_blank'>Acesse o sistema</button></a>
          </td>
        </tr>";
      include '../includes/email/email_footer.php';

      $mail->Body  = $email_conteudo;
      $mail->send();
      // -------------------------------

      // ENVIA E-MAIL PARA ADMINISTRADOR
      $mail = new PHPMailer(true);
      include '../controller/email_conf.php';
      $mail->addAddress($email_extensao, 'RESERVM'); // E-MAIL DA EXTENSÃO
      $mail->isHTML(true);
      $mail->Subject = $prop_codigo . ' - Cadastro de Proposta Iniciada'; //TÍTULO DO E-MAIL

      // CORPO DO EMAIL
      include '../includes/email/email_header.php';
      $email_conteudo .= "
        <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
          <td style='padding: 2em 2rem; display: inline-block; width: 100%'>

          <p style='font-size: 1.3rem; font-weight: 600; margin: 0px 0px 40px 0px;'>Cadastro de proposta iniciado!</p>

          <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 10px 0px;'>O cadastro da proposta de código: <strong> $prop_codigo </strong> foi iniciado.</p>

          <a style='cursor: pointer;' href='$url_sistema/admin'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 30px;' target='_blank'>Acesse o sistema</button></a>
          </td>
        </tr>";
      include '../includes/email/email_footer.php';

      $mail->Body  = $email_conteudo;
      $mail->send();
      // -------------------------------

      $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

      header("Location: ../cad_proposta.php?tp=" . base64_encode($prop_tipo) . "&st=" . base64_encode(2) . "&i=" . base64_encode($prop_id));
    } catch (PDOException $e) {
      //echo 'Error: ' . $e->getMessage();
      $conn->rollBack();
      $_SESSION["erro"] = "Cadastro não realizado!";
      echo "<script> history.go(-1);</script>";
      return die;
    }
  }
}



















/*****************************************************************************************
                                EDITAR PROPOSTA - ETAPA 1
 *****************************************************************************************/
if (isset($dados['EditProposta'])) {

  $prop_id                    = base64_decode($_POST['prop_id']);
  $prop_tipo                  = base64_decode($_POST['prop_tipo']);
  $prop_status_etapa          = 1; // NÚMERO DA ETAPA 
  $prop_titulo                = trim($_POST['prop_titulo']) !== '' ? trim($_POST['prop_titulo']) : NULL;
  $prop_descricao             = trim($_POST['prop_descricao']) !== '' ? nl2br(trim($_POST['prop_descricao'])) : NULL;
  $prop_vinculo_programa      = trim(isset($_POST['prop_vinculo_programa'])) ? $_POST['prop_vinculo_programa'] : 0;
  $prop_qual_vinculo_programa = trim($_POST['prop_qual_vinculo_programa']) !== '' ? nl2br(trim($_POST['prop_qual_vinculo_programa'])) : NULL;
  $prop_curso_vinculo         = trim($_POST['prop_curso_vinculo']) !== '' ? trim($_POST['prop_curso_vinculo']) : NULL;
  $prop_nome_curso_vinculo    = trim($_POST['prop_nome_curso_vinculo']) !== '' ? trim($_POST['prop_nome_curso_vinculo']) : NULL;
  $prop_justificativa         = trim($_POST['prop_justificativa']) !== '' ? nl2br(trim($_POST['prop_justificativa'])) : NULL;
  $prop_obj_pedagogico        = trim($_POST['prop_obj_pedagogico']) !== '' ? nl2br(trim($_POST['prop_obj_pedagogico'])) : NULL;
  $prop_publico_alvo          = trim($_POST['prop_publico_alvo']) !== '' ? nl2br(trim($_POST['prop_publico_alvo'])) : NULL;

  // CADASTRA DADOS DO CHECKBOX
  if (isset($_POST["prop_area_conhecimento"])) {
    foreach ($_POST['prop_area_conhecimento'] as $valor) {
      @$prop_area_conhecimento .= $valor . ",";
    }
  } else {
    $prop_area_conhecimento = NULL;
  }
  // -------------------------------

  // CADASTRA DADOS DO CHECKBOX
  if (isset($_POST["prop_area_tematica"])) {
    foreach ($_POST['prop_area_tematica'] as $valor) {
      @$prop_area_tematica .= $valor . ",";
    }
  } else {
    $prop_area_tematica = NULL;
  }
  // -------------------------------

  // CADASTRA DIAS DA SEMANA
  if (isset($_POST["prop_semana"])) {
    foreach ($_POST['prop_semana'] as $valor) {
      @$prop_semana .= $valor . ",";
    }
  } else {
    $prop_semana = NULL;
  }
  // -------------------------------

  $prop_horario                  = trim($_POST['prop_horario']) !== '' ? nl2br(trim($_POST['prop_horario'])) : NULL;
  $prop_data_inicio              = trim($_POST['prop_data_inicio']) !== '' ? trim($_POST['prop_data_inicio']) : NULL;
  $prop_data_fim                 = trim($_POST['prop_data_fim']) !== '' ? trim($_POST['prop_data_fim']) : NULL;
  $prop_carga_hora               = trim($_POST['prop_carga_hora']) !== '' ? trim($_POST['prop_carga_hora']) : NULL;
  $prop_total_vaga               = trim($_POST['prop_total_vaga']) !== '' ? trim($_POST['prop_total_vaga']) : NULL;
  $prop_quant_turma              = trim($_POST['prop_quant_turma']) !== '' ? trim($_POST['prop_quant_turma']) : NULL;
  $prop_modalidade               = trim($_POST['prop_modalidade']) !== '' ? trim($_POST['prop_modalidade']) : NULL;
  $prop_campus                   = trim($_POST['prop_campus']) !== '' ? trim($_POST['prop_campus']) : NULL;
  $prop_local                    = trim($_POST['prop_local']) !== '' ? trim($_POST['prop_local']) : NULL;
  $prop_forma_acesso             = trim($_POST['prop_forma_acesso']) !== '' ? trim($_POST['prop_forma_acesso']) : NULL;
  $prop_preco                    = trim($_POST['prop_preco']) !== '' ? trim($_POST['prop_preco']) : NULL;
  $prop_preco_parcelas           = trim($_POST['prop_preco_parcelas']) !== '' ? trim($_POST['prop_preco_parcelas']) : NULL;
  $prop_outra_forma_acesso       = trim($_POST['prop_outra_forma_acesso']) !== '' ? trim($_POST['prop_outra_forma_acesso']) : NULL;
  $prop_acao_acessibilidade      = trim(isset($_POST['prop_acao_acessibilidade'])) ? $_POST['prop_acao_acessibilidade'] : 0;
  $prop_desc_acao_acessibilidade = trim($_POST['prop_desc_acao_acessibilidade']) !== '' ? nl2br(trim($_POST['prop_desc_acao_acessibilidade'])) : NULL;
  $prop_ofertas_vagas            = trim(isset($_POST['prop_ofertas_vagas'])) ? $_POST['prop_ofertas_vagas'] : 0;
  $prop_quant_beneficios         = trim($_POST['prop_quant_beneficios']) !== '' ? trim($_POST['prop_quant_beneficios']) : NULL;
  $prop_atendimento_doacao       = trim(isset($_POST['prop_atendimento_doacao'])) ? $_POST['prop_atendimento_doacao'] : 0;
  $prop_desc_beneficios          = trim($_POST['prop_desc_beneficios']) !== '' ? nl2br(trim($_POST['prop_desc_beneficios'])) : NULL;
  $prop_comunidade               = trim($_POST['prop_comunidade']) !== '' ? nl2br(trim($_POST['prop_comunidade'])) : NULL;
  $prop_localidade               = trim($_POST['prop_localidade']) !== '' ? nl2br(trim($_POST['prop_localidade'])) : NULL;
  $prop_responsavel              = trim($_POST['prop_responsavel']) !== '' ? nl2br(trim($_POST['prop_responsavel'])) : NULL;
  $prop_responsavel_contato      = trim($_POST['prop_responsavel_contato']) !== '' ? nl2br(trim($_POST['prop_responsavel_contato'])) : NULL;
  $prop_info_complementar        = trim($_POST['prop_info_complementar']) !== '' ? nl2br(trim($_POST['prop_info_complementar'])) : NULL;
  $reservm_user_id                  = $_SESSION['reservm_user_id'];
  // -------------------------------

  // SE O CADASTRO JA FOI FINALIZADO, A ETAPA DEVE CONTINUAR COM STATUS 5
  $sql = "SELECT prop_status_etapa FROM propostas WHERE prop_id = :prop_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([':prop_id' => $prop_id]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($result['prop_status_etapa'] == 5) {
    $prop_status_etapa = 5; // NÚMERO DA ETAPA 
  } else {
    $prop_status_etapa = 1; // NÚMERO DA ETAPA 
  }
  // -------------------------------

  // SE TIPO NÃO FOR ENCONTRADO IMPEDE O CADASTRO
  $tipos = array(1, 2, 3, 4, 5);
  if (!in_array($prop_tipo, $tipos)) {
    $_SESSION["erro"] = "Categoria da proposta não encontrada!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  } else {

    try {
      $conn->beginTransaction(); // INICIA A TRANSAÇÃO
      $sql = "UPDATE
                  propostas
              SET
                  prop_status_etapa             = :prop_status_etapa,
                  prop_titulo                   = UPPER(:prop_titulo),
                  prop_descricao                = :prop_descricao,
                  prop_vinculo_programa         = :prop_vinculo_programa,
                  prop_qual_vinculo_programa    = :prop_qual_vinculo_programa,
                  prop_curso_vinculo            = :prop_curso_vinculo,
                  prop_nome_curso_vinculo       = :prop_nome_curso_vinculo,
                  prop_justificativa            = :prop_justificativa,
                  prop_obj_pedagogico           = :prop_obj_pedagogico,
                  prop_publico_alvo             = :prop_publico_alvo,
                  prop_area_conhecimento        = :prop_area_conhecimento,
                  prop_area_tematica            = :prop_area_tematica,
                  prop_semana                   = :prop_semana,
                  prop_horario                  = :prop_horario,
                  prop_data_inicio              = :prop_data_inicio,
                  prop_data_fim                 = :prop_data_fim,
                  prop_carga_hora               = :prop_carga_hora,
                  prop_total_vaga               = :prop_total_vaga,
                  prop_quant_turma              = :prop_quant_turma,
                  prop_forma_acesso             = :prop_forma_acesso,
                  prop_outra_forma_acesso       = UPPER(:prop_outra_forma_acesso),
                  prop_modalidade               = :prop_modalidade,
                  prop_campus                   = :prop_campus,
                  prop_local                    = UPPER(:prop_local),
                  prop_preco                    = :prop_preco,
                  prop_preco_parcelas           = :prop_preco_parcelas,
                  prop_acao_acessibilidade      = :prop_acao_acessibilidade,
                  prop_desc_acao_acessibilidade = :prop_desc_acao_acessibilidade,
                  prop_ofertas_vagas            = :prop_ofertas_vagas,
                  prop_atendimento_doacao       = :prop_atendimento_doacao,
                  prop_quant_beneficios         = :prop_quant_beneficios,
                  prop_desc_beneficios          = :prop_desc_beneficios,
                  prop_comunidade               = :prop_comunidade,
                  prop_localidade               = :prop_localidade,
                  prop_responsavel              = :prop_responsavel,
                  prop_responsavel_contato      = :prop_responsavel_contato,
                  prop_info_complementar        = :prop_info_complementar,
                  prop_data_upd                 = GETDATE()
            WHERE
                  prop_id = :prop_id";

      $stmt = $conn->prepare($sql);
      $stmt->execute([
        'prop_id' => $prop_id,
        'prop_status_etapa' => $prop_status_etapa,
        'prop_titulo' => $prop_titulo,
        'prop_descricao' => $prop_descricao,
        'prop_vinculo_programa' => $prop_vinculo_programa,
        'prop_qual_vinculo_programa' => $prop_qual_vinculo_programa,
        'prop_curso_vinculo' => $prop_curso_vinculo,
        'prop_nome_curso_vinculo' => $prop_nome_curso_vinculo,
        'prop_justificativa' => $prop_justificativa,
        'prop_obj_pedagogico' => $prop_obj_pedagogico,
        'prop_publico_alvo' => $prop_publico_alvo,
        'prop_area_conhecimento' => $prop_area_conhecimento,
        'prop_area_tematica' => $prop_area_tematica,
        'prop_semana' => $prop_semana,
        'prop_horario' => $prop_horario,
        'prop_data_inicio' => $prop_data_inicio,
        'prop_data_fim' => $prop_data_fim,
        'prop_carga_hora' => $prop_carga_hora,
        'prop_total_vaga' => $prop_total_vaga,
        'prop_quant_turma' => $prop_quant_turma,
        'prop_forma_acesso' => $prop_forma_acesso,
        'prop_outra_forma_acesso' => $prop_outra_forma_acesso,
        'prop_modalidade' => $prop_modalidade,
        'prop_campus' => $prop_campus,
        'prop_local' => $prop_local,
        'prop_preco' => $prop_preco,
        'prop_preco_parcelas' => $prop_preco_parcelas,
        'prop_acao_acessibilidade' => $prop_acao_acessibilidade,
        'prop_desc_acao_acessibilidade' => $prop_desc_acao_acessibilidade,
        'prop_ofertas_vagas' => $prop_ofertas_vagas,
        'prop_atendimento_doacao' => $prop_atendimento_doacao,
        'prop_quant_beneficios' => $prop_quant_beneficios,
        'prop_desc_beneficios' => $prop_desc_beneficios,
        'prop_comunidade' => $prop_comunidade,
        'prop_localidade' => $prop_localidade,
        'prop_responsavel' => $prop_responsavel,
        'prop_responsavel_contato' => $prop_responsavel_contato,
        'prop_info_complementar' => $prop_info_complementar
      ]);

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                              VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'     => 'PROPOSTA - ETAPA 1',
        ':acao'       => 'ATUALIZAÇÃO',
        ':acao_id'    => $prop_id,
        ':dados'      => 'Etapa: ' . $prop_status_etapa .
          '; Título: ' . $prop_titulo .
          '; Descrição: ' . $prop_descricao .
          '; Vínculo: ' . $prop_vinculo_programa .
          '; Qual Vínculo: ' . $prop_qual_vinculo_programa .
          '; Curso vínculo: ' . $prop_curso_vinculo .
          '; Nome Curso vínculo: ' . $prop_nome_curso_vinculo .
          '; Justificativa: ' . $prop_justificativa .
          '; Objetivos: ' . $prop_obj_pedagogico .
          '; Público Alvo: ' . $prop_publico_alvo .
          '; Área do Conhecimento: ' . $prop_area_conhecimento .
          '; Áreas Temáticas: ' . $prop_area_tematica .
          '; Semana: ' . $prop_semana .
          '; Horário: ' . $prop_horario .
          '; Data Início: ' . $prop_data_inicio .
          '; Data Fim: ' . $prop_data_fim .
          '; Carga Hora: ' . $prop_carga_hora .
          '; Total Vaga: ' . $prop_total_vaga .
          '; Quantidade Turma: ' . $prop_quant_turma .
          '; Forma Acesso: ' . $prop_forma_acesso .
          '; Outra Forma Acesso: ' . $prop_outra_forma_acesso .
          '; Modalidade: ' . $prop_modalidade .
          '; Campus: ' . $prop_campus .
          '; Local: ' . $prop_local .
          '; Preço: ' . $prop_preco .
          '; Preço Parcela: ' . $prop_preco_parcelas .
          '; Acessibilidade: ' . $prop_acao_acessibilidade .
          '; Desc. Acessibilidade: ' . $prop_desc_acao_acessibilidade .
          '; Oferta Vagas: ' . $prop_ofertas_vagas .
          '; Atendimento/Doação: ' . $prop_atendimento_doacao .
          '; Quant. Benefícios: ' . $prop_quant_beneficios .
          '; Benefícios: ' . $prop_desc_beneficios .
          '; Comunidade: ' . $prop_comunidade .
          '; Localidade: ' . $prop_localidade .
          '; Responsável: ' . $prop_responsavel .
          '; Responsável Contato: ' . $prop_responsavel_contato .
          '; Info. Complementar: ' . $prop_info_complementar,
        ':user_id'    => $reservm_user_id
      ]);
      // -------------------------------

      $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

      header("Location: ../cad_proposta.php?tp=" . base64_encode($prop_tipo) . "&st=" . base64_encode(2) . "&i=" . base64_encode($prop_id));
    } catch (PDOException $e) {
      //echo 'Error: ' . $e->getMessage();
      $conn->rollBack();
      $_SESSION["erro"] = "Cadastro não realizado!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#top_ancora");
      return die;
    }
  }
}



















/*****************************************************************************************
                            CADASTRAR PROPOSTA ETAPA 2
 *****************************************************************************************/
if (isset($dados['CadPropostaStep2'])) {

  $prop_id           = base64_decode($_POST['prop_id']);
  $prop_tipo         = base64_decode($_POST['prop_tipo']);
  $prop_status_etapa = 2;
  $reservm_user_id      = $_SESSION['reservm_user_id'];
  // -------------------------------

  // SE O CADASTRO JA FOI FINALIZADO, A ETAPA DEVE CONTINUAR COM STATUS 5
  $sql = "SELECT prop_status_etapa FROM propostas WHERE prop_id = :prop_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([':prop_id' => $prop_id]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($result['prop_status_etapa'] == 5) {
    $prop_status_etapa = 5; // NÚMERO DA ETAPA 
  } else {
    $prop_status_etapa = 2; // NÚMERO DA ETAPA 
  }
  // -------------------------------

  // IMPEDE QUE O CADASTRO CONTINUE SE NÃO HOUVER UM COORDENADOR CADASTRADO
  $sql = "SELECT COUNT(*) FROM propostas_coordenador_projeto WHERE pcp_proposta_id = :pcp_proposta_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([':pcp_proposta_id' => $prop_id]);
  if ($stmt->fetchColumn() < 1) {
    $_SESSION["erro"] = "Cadastre pelo menos um coordenador para o projeto!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  }
  // -------------------------------

  // SE TIPO NÃO FOR ENCONTRADO IMPEDE O CADASTRO
  $tipos = array(1, 2, 3, 4, 5);
  if (!in_array($prop_tipo, $tipos)) {
    $_SESSION["erro"] = "Categoria da proposta não encontrada!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  } else {

    try {
      $conn->beginTransaction(); // INICIA A TRANSAÇÃO
      $sql = "UPDATE
                    propostas
              SET        
                    prop_status_etapa = :prop_status_etapa,
                    prop_data_upd     = GETDATE()
              WHERE
                    prop_id           = :prop_id";

      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':prop_id'           => $prop_id,
        ':prop_status_etapa' => $prop_status_etapa
      ]);

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'     => 'PROPOSTA - ETAPA 2',
        ':acao'       => 'CADASTRO',
        ':acao_id'    => $prop_id,
        ':dados'      => 'Etapa: ' . $prop_status_etapa,
        ':user_id'    => $reservm_user_id
      ]);
      // -------------------------------

      $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

      header("Location: ../cad_proposta.php?tp=" . base64_encode($prop_tipo) . "&st=" . base64_encode(3) . "&i=" . base64_encode($prop_id));
    } catch (PDOException $e) {
      //echo 'Error: ' . $e->getMessage();
      $conn->rollBack();
      $_SESSION["erro"] = "Cadastro não realizado!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#top_ancora");
      return die;
    }
  }
}


















/*****************************************************************************************
                                CADASTRA PROPOSTA ETAPA 3
 *****************************************************************************************/
if (isset($dados['CadPropostaStep3'])) {

  $prop_id           = base64_decode($_POST['prop_id']);
  $prop_tipo         = base64_decode($_POST['prop_tipo']);
  $prop_status_etapa = 3;
  $prop_custos       = trim($_POST['prop_custos']) !== '' ? nl2br(trim($_POST['prop_custos'])) : NULL;
  // -------------------------------
  if (isset($_POST["prop_recursos"])) {
    foreach ($_POST['prop_recursos'] as $valor) {
      @$prop_recursos .= $valor . ",";
    }
  } else {
    $prop_recursos = NULL;
  }
  // -------------------------------
  $prop_desc_atividade  = trim($_POST['prop_desc_atividade']) !== '' ? nl2br(trim($_POST['prop_desc_atividade'])) : NULL;
  $prop_rec_audio_video = trim($_POST['prop_rec_audio_video']) !== '' ? nl2br(trim($_POST['prop_rec_audio_video'])) : NULL;
  $prop_outros          = trim($_POST['prop_outros']) !== '' ? nl2br(trim($_POST['prop_outros'])) : NULL;
  $reservm_user_id         = $_SESSION['reservm_user_id'];

  // SE O CADASTRO JA FOI FINALIZADO, A ETAPA DEVE CONTINUAR COM STATUS 5
  $sql = "SELECT prop_status_etapa FROM propostas WHERE prop_id = :prop_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([':prop_id' => $prop_id]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($result['prop_status_etapa'] == 5) {
    $prop_status_etapa = 5; // NÚMERO DA ETAPA 
  } else {
    $prop_status_etapa = 3; // NÚMERO DA ETAPA 
  }
  // -------------------------------

  // SE TIPO NÃO FOR ENCONTRADO IMPEDE O CADASTRO
  $tipos = array(1, 2, 3, 4, 5);
  if (!in_array($prop_tipo, $tipos)) {
    $_SESSION["erro"] = "Categoria da proposta não encontrada!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  } else {

    try {
      $conn->beginTransaction(); // INICIA A TRANSAÇÃO
      $sql = "UPDATE
                    propostas
              SET  
                    prop_status_etapa    = :prop_status_etapa,
                    prop_custos          = :prop_custos,
                    prop_recursos        = :prop_recursos,
                    prop_desc_atividade  = :prop_desc_atividade,
                    prop_rec_audio_video = :prop_rec_audio_video,
                    prop_outros          = :prop_outros,
                    prop_data_upd        = GETDATE()
            WHERE
                    prop_id              = :prop_id";

      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':prop_id' => $prop_id,
        ':prop_status_etapa' => $prop_status_etapa,
        ':prop_custos' => $prop_custos,
        ':prop_recursos' => $prop_recursos,
        ':prop_desc_atividade' => $prop_desc_atividade,
        ':prop_rec_audio_video' => $prop_rec_audio_video,
        ':prop_outros' => $prop_outros
      ]);

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                              VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'     => 'PROPOSTA - ETAPA 3',
        ':acao'       => 'CADASTRO',
        ':acao_id'    => $prop_id,
        ':dados'      => 'Etapa: ' . $prop_status_etapa . '; Custos: ' . $prop_custos . '; Recursos: ' . $prop_recursos . '; Atividade: ' . $prop_desc_atividade . '; Recursos Audio/Vídeo: ' . $prop_rec_audio_video . '; Outros: ' . $prop_outros,
        ':user_id'    => $reservm_user_id
      ]);
      // -------------------------------

      $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

      header("Location: ../cad_proposta.php?tp=" . base64_encode($prop_tipo) . "&st=" . base64_encode(4) . "&i=" . base64_encode($prop_id));
    } catch (PDOException $e) {
      //echo 'Error: ' . $e->getMessage();
      $conn->rollBack();
      $_SESSION["erro"] = "Cadastro não realizado!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#top_ancora");
      return die;
    }
  }
}




















/*****************************************************************************************
                                CADASTRAR PROPOSTA ETAPA 4
 *****************************************************************************************/
if (isset($dados['CadPropostaStep4'])) {

  $prop_id           = base64_decode($_POST['prop_id']);
  $prop_tipo         = base64_decode($_POST['prop_tipo']);
  $prop_codigo       = base64_decode($_POST['prop_codigo']);
  $prop_status_etapa = 4;
  $prop_card         = trim(isset($_POST['prop_card'])) ? $_POST['prop_card'] : 0;
  $reservm_user_id      = $_SESSION['reservm_user_id'];

  // SE O CADASTRO JA FOI FINALIZADO, A ETAPA DEVE CONTINUAR COM STATUS 5
  $sql = "SELECT prop_status_etapa FROM propostas WHERE prop_id = :prop_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':prop_id', $prop_id);
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($result['prop_status_etapa'] == 5) {
    $prop_status_etapa = 5; // NÚMERO DA ETAPA 
  } else {
    $prop_status_etapa = 4; // NÚMERO DA ETAPA 
  }
  // -------------------------------

  // SE O CHECKBOX "CARD PARA DIVULGAÇÃO" FOR DESATIVADO, EXCLUI OS DADOS E ARQUIVOS QUE FORAM CADASTRADOS
  if ($prop_card == 1) {
    $prop_texto_divulgacao = trim($_POST['prop_texto_divulgacao']) !== '' ? nl2br(trim($_POST['prop_texto_divulgacao'])) : NULL;
    $prop_diferenciais     = trim($_POST['prop_diferenciais']) !== '' ? nl2br(trim($_POST['prop_diferenciais'])) : NULL;
    $prop_brienfing        = trim($_POST['prop_brienfing']) !== '' ? nl2br(trim($_POST['prop_brienfing'])) : NULL;
    $prop_parceria         = trim(isset($_POST['prop_parceria'])) ? $_POST['prop_parceria'] : 0;
    $prop_informacoes      = trim($_POST['prop_informacoes']) !== '' ? nl2br(trim($_POST['prop_informacoes'])) : NULL;
    // -------------------------------
  } else {
    $prop_texto_divulgacao = NULL;
    $prop_diferenciais     = NULL;
    $prop_brienfing        = NULL;
    $prop_parceria         = NULL;
    $prop_informacoes      = NULL;

    try {
      $conn->beginTransaction(); // INICIA A TRANSAÇÃO
      $sql = "DELETE FROM propostas_arq WHERE parq_codigo = '$prop_codigo' AND parq_categoria IN (1,2)";
      $conn->exec($sql);

      // APAGA O ARQUIVOS IMAGENS
      $subpasta1    = "../uploads/propostas/$prop_codigo/1";
      $subpasta2 = "../uploads/propostas/$prop_codigo/2";

      // OBTÉM A LISTA DE IMAGENS
      $subarquivos1 = glob($subpasta1 . '/*');
      $subarquivos2 = glob($subpasta2 . '/*');

      // LOOP PARA EXCLUIR OS IMAGENS
      foreach ($subarquivos2 as $subarquivo2) {
        if (is_file($subarquivo2)) {
          unlink($subarquivo2); // EXCLUI O ARQUIVO
        }
      }
      // LOOP PARA EXCLUIR OS ARQUIVOS
      foreach ($subarquivos1 as $subarquivo1) {
        if (is_file($subarquivo1)) {
          unlink($subarquivo1); // EXCLUI O ARQUIVO
        }
      }
      // -------------------------------

      // APÓS EXCLUIR ARQUIVOS, EXCLUI A PASTA
      rmdir($subpasta1);
      rmdir($subpasta2);
      // -------------------------------

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                              VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'    => 'PROPOSTA - ETAPA 4 - IMAGENS',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $prop_id,
        ':user_id'   => $reservm_user_id
      ]);
      // -------------------------------

      $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    } catch (PDOException $e) {
      // echo $sql . "<br>" . $e->getMessage();
      $conn->rollBack();
      $_SESSION["erro"] = "Erro ao tentar excluir o arquivo!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#top_ancora");
      return die;
    }
  }

  // SE O CHECKBOX "PARCERIA" FOR DESATIVADO, EXCLUI OS ARQUIVO QUE FORAM CADASTRADOS
  if ($prop_parceria != 1) {
    try {
      $conn->beginTransaction(); // INICIA A TRANSAÇÃO
      $sql = "DELETE FROM propostas_arq WHERE parq_categoria = 2 AND parq_codigo = '$prop_codigo'";
      $conn->exec($sql);

      // APAGA O ARQUIVOS
      $pasta = "../uploads/propostas/$prop_codigo";

      // OBTÉM A LISTA DE ARQUIVOS
      $arquivos = glob($pasta . '/*');

      // LOOP PARA EXCLUIR OS ARQUIVOS
      foreach ($arquivos as $arquivo) {
        if (is_file($arquivo)) {
          unlink($arquivo); // EXCLUI O ARQUIVO
        }
      }

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'    => 'PROPOSTA - ETAPA 4 - ARQUIVOS',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $prop_id,
        ':user_id'   => $reservm_user_id
      ]);
      // -------------------------------

      $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    } catch (PDOException $e) {
      // echo $sql . "<br>" . $e->getMessage();
      $conn->rollBack();
      $_SESSION["erro"] = "Erro ao tentar excluir o arquivo!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#top_ancora");
      return die;
    }
  }
  // -------------------------------

  // SE TIPO NÃO FOR ENCONTRADO IMPEDE O CADASTRO
  $tipos = array(1, 2, 3, 4, 5);
  if (!in_array($prop_tipo, $tipos)) {
    $_SESSION["erro"] = "Categoria da proposta não encontrada!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  } else {

    try {
      $conn->beginTransaction(); // INICIA A TRANSAÇÃO
      $sql = "UPDATE
                    propostas
              SET  
                    prop_status_etapa     = :prop_status_etapa,
                    prop_card             = :prop_card,
                    prop_texto_divulgacao = :prop_texto_divulgacao,
                    prop_diferenciais     = :prop_diferenciais,
                    prop_brienfing        = :prop_brienfing,
                    prop_parceria         = :prop_parceria,
                    prop_informacoes      = :prop_informacoes,
                    prop_user_id          = :prop_user_id,
                    prop_data_upd         = GETDATE()
            WHERE
                    prop_id               = :prop_id";

      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':prop_id' => $prop_id,
        ':prop_status_etapa' => $prop_status_etapa,
        ':prop_card' => $prop_card,
        ':prop_texto_divulgacao' => $prop_texto_divulgacao,
        ':prop_diferenciais' => $prop_diferenciais,
        ':prop_brienfing' => $prop_brienfing,
        ':prop_parceria' => $prop_parceria,
        ':prop_informacoes' => $prop_informacoes,
        ':prop_user_id' => $reservm_user_id
      ]);

      // CADASTRAR IMAGENS
      if (!empty($_FILES["arquivo_img"]["name"][0])) { // SE ALGUAMA IMAGEM FOR ENVIADA...

        $parq_categoria = 1; // CATEGORIA DE IMAGENS = 1
        $arquivos       = $_FILES["arquivo_img"]; // RECEBE O(S) ARQUIVO(S)

        $maxFileSize = 10 * 1024 * 1024; // 10MB
        foreach ($_FILES["arquivo_img"]["name"] as $key => $fileName) { // CALCULA O TAMANHO DOS ARQUIVOS ENVIADOS
          if (!empty($fileName)) {
            $fileSize = $_FILES["arquivo_img"]["size"][$key];
            if ($fileSize > $maxFileSize) {
              $_SESSION["erro"] = "O arquivo excede o tamanho máximo permitido de 2MB.";
              $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
              header("Location: $referer#top_ancora");
              return die;
            }
          }
        }

        // CRIA AS PASTAS DAS IMAGENS
        $pastaPrincipal = "../uploads/propostas/$prop_codigo";
        $SubPasta = "../uploads/propostas/$prop_codigo/$parq_categoria";
        // CRIA A PASTA PRINCIPAL SE NÃO EXISTIR
        if (!file_exists($pastaPrincipal)) {
          mkdir($pastaPrincipal, 0777, true);
        }
        // -------------------------------

        // CRIA A SUBPASTA
        if (!file_exists($SubPasta)) {
          mkdir($SubPasta, 0777, true);
        }
        // -------------------------------

        $nomes = $arquivos['name']; //CAPTURA NOMES DOS ARQUIVOS

        for ($i = 0; $i < count($nomes); $i++) {
          $extensao = explode('.', $nomes[$i]);
          $extensao = end($extensao);
          $nomes[$i] = rand() . '-' . $nomes[$i];

          $arquivos_permitidos = ['jpg', 'JPG', 'jpeg', 'png', 'PNG']; //FORMATO DE ARQUIVOS PERMITIDOS
          if (in_array($extensao, $arquivos_permitidos)) { // VERIFICAR EXTENSÃO DOS ARQUIVOS

            $sql = "INSERT INTO propostas_arq (
                                                parq_prop_id,
                                                parq_codigo,
                                                parq_categoria,
                                                parq_arquivo,
                                                parq_user_id,
                                                parq_data_cad
                                                ) VALUES (
                                                :parq_prop_id,
                                                :parq_codigo,
                                                :parq_categoria,
                                                :parq_arquivo,
                                                :parq_user_id,
                                                GETDATE()
                                                )";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
              ':parq_prop_id' => $prop_id,
              ':parq_codigo' => $prop_codigo,
              ':parq_categoria' => $parq_categoria,
              ':parq_arquivo' => $nomes[$i],
              ':parq_user_id' => $reservm_user_id
            ]);

            // REGISTRA AÇÃO NO LOG
            // $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
            $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                                      VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
            $stmt->execute([
              ':modulo'     => 'PROPOSTA - ETAPA 4 - IMAGENS',
              ':acao'       => 'CADASTRO',
              ':acao_id'    => $prop_id,
              ':dados'      => 'Código: ' . $prop_codigo . '; Categoria: ' . $parq_categoria . '; Imagens: ' . $nomes[$i],
              ':user_id'    => $reservm_user_id
            ]);
            // -------------------------------

            // MOVE AS IMAGENS PARA A PASTA
            $total_rowns = $stmt->rowCount();
            if ($total_rowns > 0) {
              $mover = move_uploaded_file($_FILES['arquivo_img']['tmp_name'][$i], '../uploads/propostas/' . $prop_codigo . '/' . $parq_categoria . '/' . $nomes[$i]);
            }
            // -------------------------------

          } else {
            $_SESSION["erro"] = "Formato de arquivo inválido!";
            $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
            header("Location: $referer#parq_ancora");
            return die;
          }
        }
      }
      // -------------------------------

      // CADASTRAR ARQUIVOS
      if (!empty($_FILES["arquivo"]["name"][0])) { // SE ALGUM ARQUIVO FOR ENVIADA...

        $parq_categoria = 2; // CATEGORIA DE ARQUIVO = 2
        $arquivos       = $_FILES["arquivo"]; // RECEBE O(S) ARQUIVO(S)

        $maxFileSize = 10 * 1024 * 1024; // 10MB
        foreach ($_FILES["arquivo"]["name"] as $key => $fileName) { // CALCULA O TAMANHO DOS ARQUIVOS ENVIADOS
          if (!empty($fileName)) {
            $fileSize = $_FILES["arquivo"]["size"][$key];
            if ($fileSize > $maxFileSize) {
              $_SESSION["erro"] = "O arquivo excede o tamanho máximo permitido de 2MB.";
              $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
              header("Location: $referer#top_ancora");
              return die;
            }
          }
        }

        // CRIA AS PASTAS DOS ARQUIVOS
        $pastaPrincipal = "../uploads/propostas/$prop_codigo";
        $SubPasta = "../uploads/propostas/$prop_codigo/$parq_categoria";

        // CRIA A PASTA PRINCIPAL SE NÃO EXISTIR
        if (!file_exists($pastaPrincipal)) {
          mkdir($pastaPrincipal, 0777, true);
        }
        // -------------------------------

        // CRIA A SUBPASTA
        if (!file_exists($SubPasta)) {
          mkdir($SubPasta, 0777, true);
        }
        // -------------------------------

        $nomes = $arquivos['name']; //CAPTURA NOMES DOS ARQUIVOS

        for ($i = 0; $i < count($nomes); $i++) {
          $extensao = explode('.', $nomes[$i]);
          $extensao = end($extensao);
          $nomes[$i] = rand() . '-' . $nomes[$i];

          $sql = "INSERT INTO propostas_arq (
                                                parq_prop_id,
                                                parq_codigo,
                                                parq_categoria,
                                                parq_arquivo,
                                                parq_user_id,
                                                parq_data_cad
                                                ) VALUES (
                                                :parq_prop_id,
                                                :parq_codigo,
                                                :parq_categoria,
                                                :parq_arquivo,
                                                :parq_user_id,
                                                GETDATE()
                                                )";
          $stmt = $conn->prepare($sql);
          $stmt->execute([
            ':parq_prop_id' => $prop_id,
            ':parq_codigo' => $prop_codigo,
            ':parq_categoria' => $parq_categoria,
            ':parq_arquivo' => $nomes[$i],
            ':parq_user_id' => $reservm_user_id
          ]);

          // REGISTRA AÇÃO NO LOG
          //$last_arq_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
          $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                                    VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
          $stmt->execute([
            ':modulo'     => 'PROPOSTA - ETAPA 4 - ARQUIVOS',
            ':acao'       => 'CADASTRO',
            ':acao_id'    => $prop_id,
            ':dados'      => 'Código: ' . $prop_codigo . '; Categoria: ' . $parq_categoria . '; Arquivos: ' . $nomes[$i],
            ':user_id'    => $reservm_user_id
          ]);
          // -------------------------------

          // MOVE AS IMAGENS PARA A PASTA
          $total_rowns = $stmt->rowCount();
          if ($total_rowns > 0) {
            $mover = move_uploaded_file($_FILES['arquivo']['tmp_name'][$i], '../uploads/propostas/' . $prop_codigo . '/' . $parq_categoria . '/' . $nomes[$i]);
          }
          // -------------------------------
        }
      }
      // -------------------------------

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                              VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'     => 'PROPOSTA - ETAPA 4',
        ':acao'       => 'CADASTRO',
        ':acao_id'    => $prop_id,
        ':dados'      => 'Etapa: ' . $prop_status_etapa . '; Card: ' . $prop_card . '; Texto Divulgação: ' . $prop_texto_divulgacao . '; Diferenciais: ' . $prop_diferenciais . '; Brienfing: ' . $prop_brienfing . '; Parceria: ' . $prop_parceria . '; Informações: ' . $prop_informacoes,
        ':user_id'    => $reservm_user_id
      ]);
      // -------------------------------

      $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

      header("Location: ../cad_proposta.php?tp=" . base64_encode($prop_tipo) . "&st=" . base64_encode(5) . "&i=" . base64_encode($prop_id));
    } catch (PDOException $e) {
      //echo 'Error: ' . $e->getMessage();
      $conn->rollBack();
      $_SESSION["erro"] = "Cadastro não realizado!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#top_ancora");
      return die;
    }
  }
}


















/*****************************************************************************************
                              CADASTRAR PROPOSTA - CURSOS
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "cad_prop_curso") {

  $prop_id           = base64_decode($_POST['prop_id']);
  $prop_tipo         = base64_decode($_POST['prop_tipo']);
  $prop_codigo       = base64_decode($_POST['prop_codigo']);
  $prop_status_etapa = 5;
  $reservm_user_id      = $_SESSION['reservm_user_id'];

  // SE TIPO NÃO FOR ENCONTRADO IMPEDE O CADASTRO
  $tipos = array(1, 2, 3, 4, 5);
  if (!in_array($prop_tipo, $tipos)) {
    $_SESSION["erro"] = "Categoria da proposta não encontrada!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  } else {

    try {
      $conn->beginTransaction(); // INICIA A TRANSAÇÃO
      $sql = "UPDATE
                    propostas
              SET 
                    prop_status_etapa = :prop_status_etapa,
                    prop_data_upd     = GETDATE()
              WHERE
                    prop_id           = :prop_id";

      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':prop_id'           => $prop_id,
        ':prop_status_etapa' => $prop_status_etapa
      ]);

      // REGISTRA AÇÃO NO LOG
      $stmt_log = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                              VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
      $stmt_log->execute([
        ':modulo'     => 'PROPOSTA - CURSOS',
        ':acao'       => 'CADASTRO',
        ':acao_id'    => $prop_id,
        ':dados'      => 'Etapa: ' . $prop_status_etapa . '; Tipo: ' . $prop_tipo,
        ':user_id'    => $reservm_user_id
      ]);
      // -------------------------------

      // CADASTRA AS IMAGENS
      if (!empty($_FILES["arquivos"]["name"][0])) { // SE ALGUAMA IMAGEM FOR ENVIADA...

        $parq_categoria = 3; // CATEGORIA DE ARQUIVO - PROPOSTA = 3
        $arquivos       = $_FILES["arquivos"]; // RECEBE O(S) ARQUIVO(S)

        // 10MB
        $maxFileSize = 10 * 1024 * 1024;
        foreach ($_FILES["arquivos"]["name"] as $key => $fileName) { // CALCULA O TAMANHO DOS ARQUIVOS ENVIADOS
          if (!empty($fileName)) {
            $fileSize = $_FILES["arquivos"]["size"][$key];
            if ($fileSize > $maxFileSize) {
              $_SESSION["erro"] = "O arquivo excede o tamanho máximo permitido de 2MB.";
              $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
              header("Location: $referer#parq_ancora");
              return die;
            }
          }
        }
        // -------------------------------

        // IMPEDE QUE MAIS DE 20 ARQUIVOS SEJAM ENVIADOS
        $uploadedFilesCount = count($_FILES["arquivos"]["name"]);
        if ($uploadedFilesCount > 20) {
          $_SESSION["erro"] = "Você pode enviar no máximo 5 arquivos.";
          $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
          header("Location: $referer#parq_ancora");
          return die;
        }
        // -------------------------------

        // CRIA AS PASTAS DOS ARQUIVOS
        $pastaPrincipal = "../uploads/propostas/$prop_codigo";
        $SubPasta = "../uploads/propostas/$prop_codigo/$parq_categoria";
        // CRIA A PASTA PRINCIPAL SE NÃO EXISTIR
        if (!file_exists($pastaPrincipal)) {
          mkdir($pastaPrincipal, 0777, true);
        }
        // -------------------------------

        // CRIA A SUBPASTA
        if (!file_exists($SubPasta)) {
          mkdir($SubPasta, 0777, true);
        }
        // -------------------------------

        $nomes = $arquivos['name']; //CAPTURA NOMES DOS ARQUIVOS

        for ($i = 0; $i < count($nomes); $i++) {
          $extensao = explode('.', $nomes[$i]);
          $extensao = end($extensao);
          $nomes[$i] = rand() . '-' . $nomes[$i];

          $arquivos_permitidos = ['pdf', 'PDF', 'xlsx', 'xls', 'XLSX', 'XLS', 'doc', 'DOC', 'docx', 'DOCX', 'csv', 'CSV', 'jpg', 'JPG', 'jpeg', 'png', 'PNG', 'txt', 'TXT']; //FORMATO DE ARQUIVOS PERMITIDOS
          if (in_array($extensao, $arquivos_permitidos)) { // VERIFICAR EXTENSÃO DOS ARQUIVOS
            $sql = "INSERT INTO propostas_arq (
                                                parq_prop_id,
                                                parq_codigo,
                                                parq_categoria,
                                                parq_arquivo,
                                                parq_user_id,
                                                parq_data_cad
                                                ) VALUES (
                                                :parq_prop_id,
                                                :parq_codigo,
                                                :parq_categoria,
                                                :parq_arquivo,
                                                :parq_user_id,
                                                GETDATE()
                                                )";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
              ':parq_prop_id' => $prop_id,
              ':parq_codigo' => $prop_codigo,
              ':parq_categoria' => $parq_categoria,
              ':parq_arquivo' => $nomes[$i],
              ':parq_user_id' => $reservm_user_id
            ]);

            // REGISTRA AÇÃO NO LOG
            $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
            $stmt_log = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                                    VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
            $stmt_log->execute([
              ':modulo'     => 'PROPOSTA - CURSOS - ARQUIVOS',
              ':acao'       => 'CADASTRO',
              ':acao_id'    => $prop_id,
              ':dados'      => 'ID: ' . $last_id . '; Código: ' . $prop_codigo . '; Categoria: ' . $parq_categoria . '; Arquivo: ' . $nomes[$i],
              ':user_id'    => $reservm_user_id
            ]);
            // -------------------------------


            // MOVE AS IMAGENS PARA A PASTA
            $total_rowns = $stmt->rowCount();
            if ($total_rowns > 0) {
              $mover = move_uploaded_file($_FILES['arquivos']['tmp_name'][$i], '../uploads/propostas/' . $prop_codigo . '/' . $parq_categoria . '/' . $nomes[$i]);
            }
            // -------------------------------

          } else {
            $_SESSION["erro"] = "Formato de arquivo inválido!";
            $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
            header("Location: $referer#parq_ancora");
            return die;
          }
        }
      }

      // SE 'STATUS_ETAPA' FOR DIFERENTE DE 4, QUER DIZER QUE O STATUS AINDA NÃO FOI ATUALIZADO
      if (base64_decode($_POST['prop_status_etapa']) == 4) {

        // STATUS DA ANÁLISE
        $num_status  = 2; // 2 = CONCLUÍDO
        $sql = "INSERT INTO propostas_analise_status (
                                                      sta_an_prop_id,
                                                      sta_an_status,
                                                      sta_an_user_id,
                                                      sta_an_data_cad,
                                                      sta_an_data_upd
                                                      ) VALUES (
                                                      :sta_an_prop_id,
                                                      :sta_an_status,
                                                      :sta_an_user_id,
                                                      GETDATE(),
                                                      GETDATE()
                                                      )";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
          ':sta_an_prop_id' => $prop_id,
          ':sta_an_status' => $num_status,
          ':sta_an_user_id' => $reservm_user_id
        ]);
        // -------------------------------

        // ALTERA O STATUS DA PROPOSTA
        $sql = "UPDATE
                    propostas_status
                SET        
                    prop_sta_status   = :prop_sta_status,
                    prop_sta_user_id  = :prop_sta_user_id,
                    prop_sta_data_cad = GETDATE()
                WHERE
                    prop_sta_prop_id  = :prop_sta_prop_id";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
          ':prop_sta_prop_id' => $prop_id,
          ':prop_sta_status' => $num_status,
          ':prop_sta_user_id' => $reservm_user_id
        ]);
        // -------------------------------

        // ENVIA E-MAIL PARA USUÁRIO
        $mail = new PHPMailer(true);
        include '../controller/email_conf.php';
        $mail->addAddress($_SESSION['reservm_user_email'], 'RESERVM'); // E-MAIL DO USUÁRIO QUE RECEBERÁ A MENSAGEM
        $mail->isHTML(true);
        $mail->Subject = $prop_codigo . ' - Cadastro da proposta concluído'; // TÍTULO DO E-MAIL

        // CORPO DO EMAIL
        include '../includes/email/email_header.php';
        $email_conteudo .= "
          <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
            <td style='padding: 2em 2rem; display: inline-block; width: 100%'>
    
            <p style='font-size: 1.3rem; font-weight: 600; margin: 0px 0px 40px 0px;'>Cadastro da proposta concluído!</p>
    
            <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 10px 0px;'>Seu cadastro da proposta de código: <strong> $prop_codigo </strong> foi concluído. <br> A equipe de extensão irá analisar a sua proposta.</p>
    
            <a style='cursor: pointer;' href='$url_sistema'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 30px;' target='_blank'>Acesse o sistema</button></a>
            </td>
          </tr>";
        include '../includes/email/email_footer.php';

        $mail->Body  = $email_conteudo;
        $mail->send();
        // -------------------------------

        // ENVIA E-MAIL PARA ADMINISTRADOR
        $mail = new PHPMailer(true);
        include '../controller/email_conf.php';
        $mail->addAddress($email_extensao, 'RESERVM'); // E-MAIL DA EXTENSÃO
        $mail->isHTML(true);
        $mail->Subject = $prop_codigo . ' - Cadastro da proposta concluído'; //TÍTULO DO E-MAIL

        // CORPO DO EMAIL
        include '../includes/email/email_header.php';
        $email_conteudo .= "
          <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
            <td style='padding: 2em 2rem; display: inline-block; width: 100%'>
    
            <p style='font-size: 1.3rem; font-weight: 600; margin: 0px 0px 40px 0px;'>Cadastro da proposta concluído!</p>
    
            <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 10px 0px;'>O cadastro da proposta de código: <strong> $prop_codigo </strong> foi concluído. <br> A análise dos dados pode ser iniciada.</p>
    
            <a style='cursor: pointer;' href='$url_sistema/admin'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 30px;' target='_blank'>Acesse o sistema</button></a>
            </td>
          </tr>";
        include '../includes/email/email_footer.php';

        $mail->Body  = $email_conteudo;
        $mail->send();
      }
      // -------------------------------

      $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

      $_SESSION["msg"] = "O cadastro da proposta foi concluído!";
      header("Location: ../painel.php");
      //header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    } catch (PDOException $e) {
      //echo 'Error: ' . $e->getMessage();
      $conn->rollBack();
      $_SESSION["erro"] = "Cadastro não realizado!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#top_ancora");
      return die;
    }
  }
}


















/*****************************************************************************************
                        CADASTRA PROPOSTA - EVENTOS CIENTÍFICOS
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "cad_prop_eve_cient") {

  $prop_id               = base64_decode($_POST['prop_id']);
  $prop_tipo             = base64_decode($_POST['prop_tipo']);
  $prop_codigo           = base64_decode($_POST['prop_codigo']);
  $prop_status_etapa     = 5;
  $prop_event_patrocinio = trim($_POST['prop_event_patrocinio']) !== '' ? trim($_POST['prop_event_patrocinio']) : NULL;
  // -------------------------------
  if ($prop_event_patrocinio === 'SIM') {
    $prop_event_qual_patrocinio = trim($_POST['prop_event_qual_patrocinio']) !== '' ? nl2br(trim($_POST['prop_event_qual_patrocinio'])) : NULL;
  } else {
    $prop_event_qual_patrocinio = NULL;
  }
  // -------------------------------
  $prop_event_parceria = trim($_POST['prop_event_parceria']) !== '' ? trim($_POST['prop_event_parceria']) : NULL;
  if ($prop_event_parceria === 'SIM') {
    $prop_event_qual_parceria = trim($_POST['prop_event_qual_parceria']) !== '' ? nl2br(trim($_POST['prop_event_qual_parceria'])) : NULL;
  } else {
    $prop_event_qual_parceria = NULL;
  }
  // -------------------------------
  $prop_event_contatos = trim($_POST['prop_event_contatos']) !== '' ? nl2br(trim($_POST['prop_event_contatos'])) : NULL;
  $prop_event_sorteio  = trim(isset($_POST['prop_event_sorteio'])) ? $_POST['prop_event_sorteio'] : 0;
  $reservm_user_id        = $_SESSION['reservm_user_id'];

  // SE TIPO NÃO FOR ENCONTRADO IMPEDE O CADASTRO
  $tipos = array(1, 2, 3, 4, 5);
  if (!in_array($prop_tipo, $tipos)) {
    $_SESSION["erro"] = "Categoria da proposta não encontrada!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  } else {

    try {
      $conn->beginTransaction(); // INICIA A TRANSAÇÃO
      $sql = "UPDATE
                  propostas
            SET 
                  prop_status_etapa          = :prop_status_etapa,
                  prop_event_patrocinio      = :prop_event_patrocinio,
                  prop_event_qual_patrocinio = :prop_event_qual_patrocinio,
                  prop_event_parceria        = :prop_event_parceria,
                  prop_event_qual_parceria   = :prop_event_qual_parceria,
                  prop_event_contatos        = :prop_event_contatos,
                  prop_event_sorteio         = :prop_event_sorteio,
                  prop_data_upd              = GETDATE()
            WHERE
                  prop_id                    = :prop_id";

      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':prop_id' => $prop_id,
        ':prop_status_etapa' => $prop_status_etapa,
        ':prop_event_patrocinio' => $prop_event_patrocinio,
        ':prop_event_qual_patrocinio' => $prop_event_qual_patrocinio,
        ':prop_event_parceria' => $prop_event_parceria,
        ':prop_event_qual_parceria' => $prop_event_qual_parceria,
        ':prop_event_contatos' => $prop_event_contatos,
        ':prop_event_sorteio' => $prop_event_sorteio
      ]);

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                              VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'     => 'PROPOSTA - EVENTOS CIENTÍFICOS',
        ':acao'       => 'CADASTRO',
        ':acao_id'    => $prop_id,
        ':dados'      => 'Patrocício: ' . $prop_event_patrocinio .
          '; Qual Patrocício: ' . $prop_event_qual_patrocinio .
          '; Parceria: ' . $prop_event_parceria .
          '; Qual Parceria: ' . $prop_event_qual_parceria .
          '; NOmes e Contatos: ' . $prop_event_contatos .
          '; Sorteios: ' . $prop_event_sorteio,
        ':user_id'    => $reservm_user_id
      ]);
      // -------------------------------

      // CADASTRA AS IMAGENS
      if (!empty($_FILES["arquivos"]["name"][0])) { // SE ALGUAMA IMAGEM FOR ENVIADA...

        $parq_categoria = 3; // CATEGORIA DE ARQUIVO - PROPOSTA = 3
        $arquivos       = $_FILES["arquivos"]; // RECEBE O(S) ARQUIVO(S)

        // 10MB
        $maxFileSize = 10 * 1024 * 1024;
        foreach ($_FILES["arquivos"]["name"] as $key => $fileName) { // CALCULA O TAMANHO DOS ARQUIVOS ENVIADOS
          if (!empty($fileName)) {
            $fileSize = $_FILES["arquivos"]["size"][$key];
            if ($fileSize > $maxFileSize) {
              $_SESSION["erro"] = "O arquivo excede o tamanho máximo permitido de 2MB.";
              $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
              header("Location: $referer#parq_ancora");
              return die;
            }
          }
        }
        // -------------------------------

        // IMPEDE QUE MAIS DE 20 ARQUIVOS SEJAM ENVIADOS
        $uploadedFilesCount = count($_FILES["arquivos"]["name"]);
        if ($uploadedFilesCount > 20) {
          $_SESSION["erro"] = "Você pode enviar no máximo 5 arquivos.";
          $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
          header("Location: $referer#parq_ancora");
          return die;
        }
        // -------------------------------

        // CRIA AS PASTAS DOS ARQUIVOS
        $pastaPrincipal = "../uploads/propostas/$prop_codigo";
        $SubPasta = "../uploads/propostas/$prop_codigo/$parq_categoria";
        // CRIA A PASTA PRINCIPAL SE NÃO EXISTIR
        if (!file_exists($pastaPrincipal)) {
          mkdir($pastaPrincipal, 0777, true);
        }
        // -------------------------------

        // CRIA A SUBPASTA
        if (!file_exists($SubPasta)) {
          mkdir($SubPasta, 0777, true);
        }
        // -------------------------------

        $nomes = $arquivos['name']; //CAPTURA NOMES DOS ARQUIVOS

        for ($i = 0; $i < count($nomes); $i++) {
          $extensao = explode('.', $nomes[$i]);
          $extensao = end($extensao);
          $nomes[$i] = rand() . '-' . $nomes[$i];

          $arquivos_permitidos = ['pdf', 'PDF', 'xlsx', 'xls', 'XLSX', 'XLS', 'doc', 'DOC', 'docx', 'DOCX', 'csv', 'CSV', 'jpg', 'JPG', 'jpeg', 'png', 'PNG', 'txt', 'TXT']; //FORMATO DE ARQUIVOS PERMITIDOS
          if (in_array($extensao, $arquivos_permitidos)) { // VERIFICAR EXTENSÃO DOS ARQUIVOS

            $sql = "INSERT INTO propostas_arq (
                                                parq_prop_id,
                                                parq_codigo,
                                                parq_categoria,
                                                parq_arquivo,
                                                parq_user_id,
                                                parq_data_cad
                                                ) VALUES (
                                                :parq_prop_id,
                                                :parq_codigo,
                                                :parq_categoria,
                                                :parq_arquivo,
                                                :parq_user_id,
                                                GETDATE()
                                                )";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
              ':parq_prop_id' => $prop_id,
              ':parq_codigo' => $prop_codigo,
              ':parq_categoria' => $parq_categoria,
              ':parq_arquivo' => $nomes[$i],
              ':parq_user_id' => $reservm_user_id
            ]);
            // -------------------------------

            // REGISTRA AÇÃO NO LOG
            $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
            $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                                    VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
            $stmt->execute([
              ':modulo'     => 'PROPOSTA - CURSOS - ARQUIVOS',
              ':acao'       => 'CADASTRO',
              ':acao_id'    => $prop_id,
              ':dados'      => 'ID: ' . $last_id . '; Código: ' . $prop_codigo . '; Categoria: ' . $parq_categoria . '; Arquivo: ' . $nomes[$i],
              ':user_id'    => $reservm_user_id
            ]);
            // -------------------------------

            // MOVE AS IMAGENS PARA A PASTA
            $total_rowns = $stmt->rowCount();
            if ($total_rowns > 0) {
              $mover = move_uploaded_file($_FILES['arquivos']['tmp_name'][$i], '../uploads/propostas/' . $prop_codigo . '/' . $parq_categoria . '/' . $nomes[$i]);
            }
            // -------------------------------

          } else {
            $_SESSION["erro"] = "Formato de arquivo inválido!";
            $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
            header("Location: $referer#parq_ancora");
            return die;
          }
        }
      }

      // SE 'STATUS_ETAPA' FOR DIFERENTE DE 4, QUER DIZER QUE O STATUS AINDA NÃO FOI ATUALIZADO
      if (base64_decode($_POST['prop_status_etapa']) == 4) {

        // STATUS DA ANÁLISE
        $num_status  = 2; // 2 = CONCLUÍDO
        $sql = "INSERT INTO propostas_analise_status (
                                                      sta_an_prop_id,
                                                      sta_an_status,
                                                      sta_an_user_id,
                                                      sta_an_data_cad,
                                                      sta_an_data_upd
                                                      ) VALUES (
                                                      :sta_an_prop_id,
                                                      :sta_an_status,
                                                      :sta_an_user_id,
                                                      GETDATE(),
                                                      GETDATE()
                                                      )";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
          ':sta_an_prop_id' => $prop_id,
          ':sta_an_status' => $num_status,
          ':sta_an_user_id' => $reservm_user_id
        ]);
        // -------------------------------

        // ALTERA O STATUS DA PROPOSTA
        $sql = "UPDATE
                    propostas_status
              SET        
                    prop_sta_status   = :prop_sta_status,
                    prop_sta_user_id  = :prop_sta_user_id,
                    prop_sta_data_cad = GETDATE()
              WHERE
                    prop_sta_prop_id  = :prop_sta_prop_id";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
          ':prop_sta_prop_id' => $prop_id,
          ':prop_sta_status' => $num_status,
          ':prop_sta_user_id' => $reservm_user_id
        ]);
        // -------------------------------

        // ENVIA E-MAIL PARA USUÁRIO
        $mail = new PHPMailer(true);
        include '../controller/email_conf.php';
        $mail->addAddress($_SESSION['reservm_user_email'], 'RESERVM'); // E-MAIL DO USUÁRIO QUE RECEBERÁ A MENSAGEM
        $mail->isHTML(true);
        $mail->Subject = $prop_codigo . ' - Cadastro da proposta concluído'; // TÍTULO DO E-MAIL

        // CORPO DO EMAIL
        include '../includes/email/email_header.php';
        $email_conteudo .= "
          <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
            <td style='padding: 2em 2rem; display: inline-block; width: 100%'>
    
            <p style='font-size: 1.3rem; font-weight: 600; margin: 0px 0px 40px 0px;'>Cadastro da proposta concluído!</p>
    
            <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 10px 0px;'>Seu cadastro da proposta de código: <strong> $prop_codigo </strong> foi concluído. <br> A equipe de extensão irá analisar a sua proposta.</p>
    
            <a style='cursor: pointer;' href='$url_sistema'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 30px;' target='_blank'>Acesse o sistema</button></a>
            </td>
          </tr>";
        include '../includes/email/email_footer.php';

        $mail->Body  = $email_conteudo;
        $mail->send();
        // -------------------------------

        // ENVIA E-MAIL PARA ADMINISTRADOR
        $mail = new PHPMailer(true);
        include '../controller/email_conf.php';
        $mail->addAddress($email_extensao, 'RESERVM'); // E-MAIL DA EXTENSÃO
        $mail->isHTML(true);
        $mail->Subject = $prop_codigo . ' - Cadastro da proposta concluído'; //TÍTULO DO E-MAIL

        // CORPO DO EMAIL
        include '../includes/email/email_header.php';
        $email_conteudo .= "
          <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
            <td style='padding: 2em 2rem; display: inline-block; width: 100%'>
    
            <p style='font-size: 1.3rem; font-weight: 600; margin: 0px 0px 40px 0px;'>Cadastro da proposta concluído!</p>
    
            <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 10px 0px;'>O cadastro da proposta de código: <strong> $prop_codigo </strong> foi concluído. <br> A análise dos dados pode ser iniciada.</p>
    
            <a style='cursor: pointer;' href='$url_sistema/admin'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 30px;' target='_blank'>Acesse o sistema</button></a>
            </td>
          </tr>";
        include '../includes/email/email_footer.php';

        $mail->Body  = $email_conteudo;
        $mail->send();
      }
      // -------------------------------

      $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

      $_SESSION["msg"] = "O cadastro da proposta foi concluído!";
      header("Location: ../painel.php");
    } catch (PDOException $e) {
      //echo 'Error: ' . $e->getMessage();
      $conn->rollBack();
      $_SESSION["erro"] = "Cadastro não realizado!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#top_ancora");
      return die;
    }
  }
}



















/*****************************************************************************************
                        CADASTRAR PROPOSTA - PARCERIA
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "cad_prop_parc") {

  $prop_id                          = md5(uniqid(rand(), true));  // GERA UM ID UNICO
  $prop_tipo                        = base64_decode($_POST['prop_tipo']);
  $prop_codigo                      = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT); // GERA UM CÓDIGO COM 6 DÍGITOS 
  $prop_status_etapa                = 5; // NÚMERO DA ETAPA 
  $reservm_user_id                     = $_SESSION['reservm_user_id'];
  $prop_parc_nome_empresa           = trim($_POST['prop_parc_nome_empresa']) !== '' ? trim($_POST['prop_parc_nome_empresa']) : NULL;
  $prop_parc_tipo_empresa           = trim($_POST['prop_parc_tipo_empresa']) !== '' ? trim($_POST['prop_parc_tipo_empresa']) : NULL;
  // -------------------------------
  if ($prop_parc_tipo_empresa == '1') {
    $prop_parc_tipo_outro           = trim($_POST['prop_parc_tipo_outro']) !== '' ? trim($_POST['prop_parc_tipo_outro']) : NULL;
  } else {
    $prop_parc_tipo_outro           = NULL;
  }
  // -------------------------------
  if ($prop_parc_tipo_empresa == '4') {
    $prop_parc_orgao_empresa        = trim($_POST['prop_parc_orgao_empresa']) !== '' ? trim($_POST['prop_parc_orgao_empresa']) : NULL;
  } else {
    $prop_parc_orgao_empresa        = NULL;
  }
  // ------------------------------- 
  $prop_parc_email                  = trim($_POST['prop_parc_email']) !== '' ? trim($_POST['prop_parc_email']) : NULL;
  $prop_parc_telefone               = trim($_POST['prop_parc_telefone']) !== '' ? trim($_POST['prop_parc_telefone']) : NULL;
  $prop_parc_cep                    = trim($_POST['prop_parc_cep']) !== '' ? trim($_POST['prop_parc_cep']) : NULL;
  $prop_parc_logradouro             = trim($_POST['prop_parc_logradouro']) !== '' ? trim($_POST['prop_parc_logradouro']) : NULL;
  $prop_parc_numero                 = trim($_POST['prop_parc_numero']) !== '' ? trim($_POST['prop_parc_numero']) : NULL;
  $prop_parc_bairro                 = trim($_POST['prop_parc_bairro']) !== '' ? trim($_POST['prop_parc_bairro']) : NULL;
  $prop_parc_municipio              = trim($_POST['prop_parc_municipio']) !== '' ? trim($_POST['prop_parc_municipio']) : NULL;
  $prop_parc_estado                 = trim($_POST['prop_parc_estado']) !== '' ? trim($_POST['prop_parc_estado']) : NULL;
  $prop_parc_pais                   = trim($_POST['prop_parc_pais']) !== '' ? trim($_POST['prop_parc_pais']) : NULL;
  $prop_parc_responsavel            = trim($_POST['prop_parc_responsavel']) !== '' ? trim($_POST['prop_parc_responsavel']) : NULL;
  $prop_parc_cargo                  = trim($_POST['prop_parc_cargo']) !== '' ? trim($_POST['prop_parc_cargo']) : NULL;
  $prop_parc_contato_referencia     = trim($_POST['prop_parc_contato_referencia']) !== '' ? trim($_POST['prop_parc_contato_referencia']) : NULL;
  $prop_parc_possui_convenio        = trim(isset($_POST['prop_parc_possui_convenio'])) ? $_POST['prop_parc_possui_convenio'] : 0;
  $prop_parc_tipo_parceria          = trim($_POST['prop_parc_tipo_parceria']) !== '' ? trim($_POST['prop_parc_tipo_parceria']) : NULL;
  $prop_parc_titulo_atividade       = trim($_POST['prop_parc_titulo_atividade']) !== '' ? trim($_POST['prop_parc_titulo_atividade']) : NULL;
  $prop_parc_objetivo_atividade     = trim($_POST['prop_parc_objetivo_atividade']) !== '' ? nl2br(trim($_POST['prop_parc_objetivo_atividade'])) : NULL;
  $prop_parc_carga_hora             = trim($_POST['prop_parc_carga_hora']) !== '' ? trim($_POST['prop_parc_carga_hora']) : NULL;
  $prop_parc_data_atividade         = trim($_POST['prop_parc_data_atividade']) !== '' ? trim($_POST['prop_parc_data_atividade']) : NULL;
  $prop_parc_hora_atividade_inicial = trim($_POST['prop_parc_hora_atividade_inicial']) !== '' ? trim($_POST['prop_parc_hora_atividade_inicial']) : NULL;
  $prop_parc_hora_atividade_final   = trim($_POST['prop_parc_hora_atividade_final']) !== '' ? trim($_POST['prop_parc_hora_atividade_final']) : NULL;
  $prop_parc_local_atividade        = trim($_POST['prop_parc_local_atividade']) !== '' ? trim($_POST['prop_parc_local_atividade']) : NULL;
  $prop_parc_tipo_espaco            = trim($_POST['prop_parc_tipo_espaco']) !== '' ? trim($_POST['prop_parc_tipo_espaco']) : NULL;
  $prop_parc_campus_atividade       = trim($_POST['prop_parc_campus_atividade']) !== '' ? trim($_POST['prop_parc_campus_atividade']) : NULL;
  $prop_parc_numero_participantes   = trim($_POST['prop_parc_numero_participantes']) !== '' ? trim($_POST['prop_parc_numero_participantes']) : NULL;
  $prop_parc_recursos_necessarios   = trim($_POST['prop_parc_recursos_necessarios']) !== '' ? nl2br(trim($_POST['prop_parc_recursos_necessarios'])) : NULL;
  $prop_parc_organizacao_espaco     = trim($_POST['prop_parc_organizacao_espaco']) !== '' ? trim($_POST['prop_parc_organizacao_espaco']) : NULL;
  $prop_parc_beneficios             = trim($_POST['prop_parc_beneficios']) !== '' ? trim($_POST['prop_parc_beneficios']) : NULL;
  // -------------------------------
  if ($prop_parc_beneficios == 'SIM') {
    $prop_parc_beneficios_quantidade = trim($_POST['prop_parc_beneficios_quantidade']) !== '' ? nl2br(trim($_POST['prop_parc_beneficios_quantidade'])) : NULL;
  } else {
    $prop_parc_beneficios_quantidade = NULL;
  }
  // -------------------------------
  $prop_parc_comentarios            = trim($_POST['prop_parc_comentarios']) !== '' ? nl2br(trim($_POST['prop_parc_comentarios'])) : NULL;
  // -------------------------------

  // SE TIPO NÃO FOR ENCONTRADO IMPEDE O CADASTRO
  $tipos = array(1, 2, 3, 4, 5);
  if (!in_array($prop_tipo, $tipos)) {
    $_SESSION["erro"] = "Categoria da proposta não encontrada!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  } else {

    try {
      $conn->beginTransaction(); // INICIA A TRANSAÇÃO
      $sql = "INSERT INTO propostas (
                                    prop_id,
                                    prop_tipo,
                                    prop_codigo,
                                    prop_status_etapa,
                                    prop_titulo,
                                    prop_parc_nome_empresa,
                                    prop_parc_tipo_empresa,
                                    prop_parc_tipo_outro,
                                    prop_parc_orgao_empresa,
                                    prop_parc_email,
                                    prop_parc_telefone,
                                    prop_parc_cep,
                                    prop_parc_logradouro,
                                    prop_parc_numero,
                                    prop_parc_bairro,
                                    prop_parc_municipio,
                                    prop_parc_estado,
                                    prop_parc_pais,
                                    prop_parc_responsavel,
                                    prop_parc_cargo,
                                    prop_parc_contato_referencia,
                                    prop_parc_possui_convenio,
                                    prop_parc_tipo_parceria,
                                    prop_parc_titulo_atividade,
                                    prop_parc_objetivo_atividade,
                                    prop_parc_local_atividade,
                                    prop_parc_tipo_espaco,
                                    prop_parc_campus_atividade,
                                    prop_parc_carga_hora,
                                    prop_parc_data_atividade,
                                    prop_parc_hora_atividade_inicial,
                                    prop_parc_hora_atividade_final,
                                    prop_parc_numero_participantes,
                                    prop_parc_recursos_necessarios,
                                    prop_parc_beneficios,
                                    prop_parc_beneficios_quantidade,
                                    prop_parc_organizacao_espaco,
                                    prop_parc_comentarios,
                                    prop_user_id,
                                    prop_data_cad,
                                    prop_data_upd
                                  ) VALUES (
                                    :prop_id,
                                    :prop_tipo,
                                    :prop_codigo,
                                    :prop_status_etapa,
                                    UPPER(:prop_titulo),
                                    UPPER(:prop_parc_nome_empresa),
                                    :prop_parc_tipo_empresa,
                                    :prop_parc_tipo_outro,
                                    UPPER(:prop_parc_orgao_empresa),
                                    LOWER(:prop_parc_email),
                                    :prop_parc_telefone,
                                    :prop_parc_cep,
                                    UPPER(:prop_parc_logradouro),
                                    UPPER(:prop_parc_numero),
                                    UPPER(:prop_parc_bairro),
                                    UPPER(:prop_parc_municipio),
                                    UPPER(:prop_parc_estado),
                                    UPPER(:prop_parc_pais),
                                    UPPER(:prop_parc_responsavel),
                                    UPPER(:prop_parc_cargo),
                                    UPPER(:prop_parc_contato_referencia),
                                    :prop_parc_possui_convenio,
                                    :prop_parc_tipo_parceria,
                                    UPPER(:prop_parc_titulo_atividade),
                                    :prop_parc_objetivo_atividade,
                                    UPPER(:prop_parc_local_atividade),
                                    UPPER(:prop_parc_tipo_espaco),
                                    :prop_parc_campus_atividade,
                                    :prop_parc_carga_hora,
                                    :prop_parc_data_atividade,
                                    :prop_parc_hora_atividade_inicial,
                                    :prop_parc_hora_atividade_final,
                                    UPPER(:prop_parc_numero_participantes),
                                    :prop_parc_recursos_necessarios,
                                    UPPER(:prop_parc_beneficios),
                                    :prop_parc_beneficios_quantidade,
                                    :prop_parc_organizacao_espaco,
                                    :prop_parc_comentarios,
                                    :prop_user_id,
                                    GETDATE(),
                                    GETDATE()
                                  )";

      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':prop_id' => $prop_id,
        ':prop_tipo' => $prop_tipo,
        ':prop_codigo' => $prop_codigo,
        ':prop_status_etapa' => $prop_status_etapa,
        ':prop_titulo' => $prop_parc_nome_empresa,
        ':prop_parc_nome_empresa' => $prop_parc_nome_empresa,
        ':prop_parc_tipo_empresa' => $prop_parc_tipo_empresa,
        ':prop_parc_tipo_outro' => $prop_parc_tipo_outro,
        ':prop_parc_orgao_empresa' => $prop_parc_orgao_empresa,
        ':prop_parc_email' => $prop_parc_email,
        ':prop_parc_telefone' => $prop_parc_telefone,
        ':prop_parc_cep' => $prop_parc_cep,
        ':prop_parc_logradouro' => $prop_parc_logradouro,
        ':prop_parc_numero' => $prop_parc_numero,
        ':prop_parc_bairro' => $prop_parc_bairro,
        ':prop_parc_municipio' => $prop_parc_municipio,
        ':prop_parc_estado' => $prop_parc_estado,
        ':prop_parc_pais' => $prop_parc_pais,
        ':prop_parc_responsavel' => $prop_parc_responsavel,
        ':prop_parc_cargo' => $prop_parc_cargo,
        ':prop_parc_contato_referencia' => $prop_parc_contato_referencia,
        ':prop_parc_possui_convenio' => $prop_parc_possui_convenio,
        ':prop_parc_tipo_parceria' => $prop_parc_tipo_parceria,
        ':prop_parc_titulo_atividade' => $prop_parc_titulo_atividade,
        ':prop_parc_objetivo_atividade' => $prop_parc_objetivo_atividade,
        ':prop_parc_local_atividade' => $prop_parc_local_atividade,
        ':prop_parc_tipo_espaco' => $prop_parc_tipo_espaco,
        ':prop_parc_campus_atividade' => $prop_parc_campus_atividade,
        ':prop_parc_carga_hora' => $prop_parc_carga_hora,
        ':prop_parc_data_atividade' => $prop_parc_data_atividade,
        ':prop_parc_hora_atividade_inicial' => $prop_parc_hora_atividade_inicial,
        ':prop_parc_hora_atividade_final' => $prop_parc_hora_atividade_final,
        ':prop_parc_numero_participantes' => $prop_parc_numero_participantes,
        ':prop_parc_recursos_necessarios' => $prop_parc_recursos_necessarios,
        ':prop_parc_beneficios' => $prop_parc_beneficios,
        ':prop_parc_beneficios_quantidade' => $prop_parc_beneficios_quantidade,
        ':prop_parc_organizacao_espaco' => $prop_parc_organizacao_espaco,
        ':prop_parc_comentarios' => $prop_parc_comentarios,
        ':prop_user_id' => $reservm_user_id
      ]);

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                              VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'     => 'PROPOSTA - PARCERIAS',
        ':acao'       => 'CADASTRO',
        ':acao_id'    => $prop_id,
        ':dados'      => 'Tipo Proposta: ' . $prop_tipo .
          '; Código: ' . $prop_codigo .
          '; Etapa: ' . $prop_status_etapa .
          '; Empresa: ' . $prop_parc_nome_empresa .
          '; Tipo empresa: ' . $prop_parc_tipo_empresa .
          '; Tipo (outro): ' . $prop_parc_tipo_outro .
          '; Órgão: ' . $prop_parc_orgao_empresa .
          '; E-amil: ' . $prop_parc_email .
          '; Contato: ' . $prop_parc_telefone .
          '; prop_parc_cep: ' . $prop_parc_cep .
          '; CEP: ' . $prop_parc_logradouro .
          '; Rua: ' . $prop_parc_numero .
          '; Bairro: ' . $prop_parc_bairro .
          '; Município: ' . $prop_parc_municipio .
          '; Estado: ' . $prop_parc_estado .
          '; PAis: ' . $prop_parc_pais .
          '; Responsável: ' . $prop_parc_responsavel .
          '; Cargo: ' . $prop_parc_cargo .
          '; Cont. referência: ' . $prop_parc_contato_referencia .
          '; Possui convêncio: ' . $prop_parc_possui_convenio .
          '; Tipo parceria: ' . $prop_parc_tipo_parceria .
          '; Tit. atividade: ' . $prop_parc_titulo_atividade .
          '; Objetivo Atividade: ' . $prop_parc_objetivo_atividade .
          '; Local atividade: ' . $prop_parc_local_atividade .
          '; Tipo Espaço: ' . $prop_parc_tipo_espaco .
          '; Campus: ' . $prop_parc_campus_atividade .
          '; Carga horária: ' . $prop_parc_carga_hora .
          '; Data Atividade: ' . $prop_parc_data_atividade .
          '; Hora Inicial: ' . $prop_parc_hora_atividade_inicial .
          '; Hora Final: ' . $prop_parc_hora_atividade_final .
          '; Numero Participantes: ' . $prop_parc_numero_participantes .
          '; Recursos Necessários: ' . $prop_parc_recursos_necessarios .
          '; Benefícios: ' . $prop_parc_beneficios .
          '; Quant. Benefícios: ' . $prop_parc_beneficios_quantidade .
          '; Organização: ' . $prop_parc_organizacao_espaco .
          '; Comentários: ' . $prop_parc_comentarios,
        ':user_id'    => $reservm_user_id
      ]);
      // -------------------------------

      // CADASTRA AS IMAGENS
      if (!empty($_FILES["arquivos"]["name"][0])) { // SE ALGUAMA IMAGEM FOR ENVIADA...

        $parq_categoria = 3; // CATEGORIA DE ARQUIVO - PROPOSTA = 3
        $arquivos       = $_FILES["arquivos"]; // RECEBE O(S) ARQUIVO(S)

        // 10MB
        $maxFileSize = 10 * 1024 * 1024;
        foreach ($_FILES["arquivos"]["name"] as $key => $fileName) { // CALCULA O TAMANHO DOS ARQUIVOS ENVIADOS
          if (!empty($fileName)) {
            $fileSize = $_FILES["arquivos"]["size"][$key];
            if ($fileSize > $maxFileSize) {
              $_SESSION["erro"] = "O arquivo excede o tamanho máximo permitido de 2MB.";
              $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
              header("Location: $referer#parq_ancora");
              return die;
            }
          }
        }
        // -------------------------------

        // IMPEDE QUE MAIS DE 20 ARQUIVOS SEJAM ENVIADOS
        $uploadedFilesCount = count($_FILES["arquivos"]["name"]);
        if ($uploadedFilesCount > 20) {
          $_SESSION["erro"] = "Você pode enviar no máximo 5 arquivos.";
          $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
          header("Location: $referer#parq_ancora");
          return die;
        }
        // -------------------------------

        // CRIA AS PASTAS DOS ARQUIVOS
        $pastaPrincipal = "../uploads/propostas/$prop_codigo";
        $SubPasta = "../uploads/propostas/$prop_codigo/$parq_categoria";
        // CRIA A PASTA PRINCIPAL SE NÃO EXISTIR
        if (!file_exists($pastaPrincipal)) {
          mkdir($pastaPrincipal, 0777, true);
        }
        // -------------------------------

        // CRIA A SUBPASTA
        if (!file_exists($SubPasta)) {
          mkdir($SubPasta, 0777, true);
        }
        // -------------------------------

        $nomes = $arquivos['name']; //CAPTURA NOMES DOS ARQUIVOS

        for ($i = 0; $i < count($nomes); $i++) {
          $extensao = explode('.', $nomes[$i]);
          $extensao = end($extensao);
          $nomes[$i] = rand() . '-' . $nomes[$i];

          $arquivos_permitidos = ['pdf', 'PDF', 'xlsx', 'xls', 'XLSX', 'XLS', 'doc', 'DOC', 'docx', 'DOCX', 'csv', 'CSV', 'jpg', 'JPG', 'jpeg', 'png', 'PNG', 'txt', 'TXT']; //FORMATO DE ARQUIVOS PERMITIDOS
          if (in_array($extensao, $arquivos_permitidos)) { // VERIFICAR EXTENSÃO DOS ARQUIVOS
            $sql = "INSERT INTO propostas_arq (
                                                  parq_prop_id,
                                                  parq_codigo,
                                                  parq_categoria,
                                                  parq_arquivo,
                                                  parq_user_id,
                                                  parq_data_cad
                                                  ) VALUES (
                                                  :parq_prop_id,
                                                  :parq_codigo,
                                                  :parq_categoria,
                                                  :parq_arquivo,
                                                  :parq_user_id,
                                                  GETDATE()
                                                  )";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
              ':parq_prop_id' => $prop_id,
              ':parq_codigo' => $prop_codigo,
              ':parq_categoria' => $parq_categoria,
              ':parq_arquivo' => $nomes[$i],
              ':parq_user_id' => $reservm_user_id
            ]);

            // REGISTRA AÇÃO NO LOG
            $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
            $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                                      VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
            $stmt->execute([
              ':modulo'     => 'PROPOSTA - CURSOS - ARQUIVOS',
              ':acao'       => 'CADASTRO',
              ':acao_id'    => $prop_id,
              ':dados'      => 'ID: ' . $last_id . '; Código: ' . $prop_codigo . '; Categoria: ' . $parq_categoria . '; Arquivo: ' . $nomes[$i],
              ':user_id'    => $reservm_user_id
            ]);
            // -------------------------------

            // MOVE AS IMAGENS PARA A PASTA
            $total_rowns = $stmt->rowCount();
            if ($total_rowns > 0) {
              $mover = move_uploaded_file($_FILES['arquivos']['tmp_name'][$i], '../uploads/propostas/' . $prop_codigo . '/' . $parq_categoria . '/' . $nomes[$i]);
            }
            // -------------------------------

          } else {
            $_SESSION["erro"] = "Formato de arquivo inválido!";
            $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
            header("Location: $referer#parq_ancora");
            return die;
          }
        }
      }

      // CADASTRA O STATUS DA PROPOSTA
      $num_status  = 2; // 2 = CONCLUÍDO
      $sql = "INSERT INTO propostas_status (
                                              prop_sta_prop_id,
                                              prop_sta_status,
                                              prop_sta_user_id,
                                              prop_sta_data_cad
                                            ) VALUES (
                                              :prop_sta_prop_id,
                                              :prop_sta_status,
                                              :prop_sta_user_id,
                                              GETDATE()
                                            )";
      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':prop_sta_prop_id' => $prop_id, // ID DA PROPOSTA
        ':prop_sta_status' => $num_status,
        ':prop_sta_user_id' => $reservm_user_id
      ]);
      // -------------------------------

      // CADASTRA O STATUS DA ANÁLISE
      $sql = "INSERT INTO propostas_analise_status (
                                                    sta_an_prop_id,
                                                    sta_an_status,
                                                    sta_an_user_id,
                                                    sta_an_data_cad,
                                                    sta_an_data_upd
                                                    ) VALUES (
                                                    :sta_an_prop_id,
                                                    :sta_an_status,
                                                    :sta_an_user_id,
                                                    GETDATE(),
                                                    GETDATE()
                                                    )";

      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':sta_an_prop_id' => $prop_id,
        ':sta_an_status' => $num_status,
        ':sta_an_user_id' => $reservm_user_id
      ]);
      // -------------------------------

      // ENVIA E-MAIL PARA USUÁRIO
      $mail = new PHPMailer(true);
      include '../controller/email_conf.php';
      $mail->addAddress($_SESSION['reservm_user_email'], 'RESERVM'); // E-MAIL DO USUÁRIO QUE RECEBERÁ A MENSAGEM
      $mail->isHTML(true);
      $mail->Subject = $prop_codigo . ' - Cadastro da proposta concluído'; // TÍTULO DO E-MAIL

      // CORPO DO EMAIL
      include '../includes/email/email_header.php';
      $email_conteudo .= "
          <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
            <td style='padding: 2em 2rem; display: inline-block; width: 100%'>
    
            <p style='font-size: 1.3rem; font-weight: 600; margin: 0px 0px 40px 0px;'>Cadastro da proposta concluído!</p>
    
            <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 10px 0px;'>Seu cadastro da proposta de código: <strong> $prop_codigo </strong> foi concluído. <br> A equipe de extensão irá analisar a sua proposta.</p>
    
            <a style='cursor: pointer;' href='$url_sistema'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 30px;' target='_blank'>Acesse o sistema</button></a>
            </td>
          </tr>";
      include '../includes/email/email_footer.php';

      $mail->Body  = $email_conteudo;
      $mail->send();
      // -------------------------------

      // ENVIA E-MAIL PARA ADMINISTRADOR
      $mail = new PHPMailer(true);
      include '../controller/email_conf.php';
      $mail->addAddress($email_extensao, 'RESERVM'); // E-MAIL DA EXTENSÃO
      $mail->isHTML(true);
      $mail->Subject = $prop_codigo . ' - Cadastro da proposta concluído'; //TÍTULO DO E-MAIL

      // CORPO DO EMAIL
      include '../includes/email/email_header.php';
      $email_conteudo .= "
          <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
            <td style='padding: 2em 2rem; display: inline-block; width: 100%'>
    
            <p style='font-size: 1.3rem; font-weight: 600; margin: 0px 0px 40px 0px;'>Cadastro da proposta concluído!</p>
    
            <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 10px 0px;'>O cadastro da proposta de código: <strong> $prop_codigo </strong> foi concluído. <br> A análise dos dados pode ser iniciada.</p>
    
            <a style='cursor: pointer;' href='$url_sistema/admin'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 30px;' target='_blank'>Acesse o sistema</button></a>
            </td>
          </tr>";
      include '../includes/email/email_footer.php';

      $mail->Body  = $email_conteudo;
      $mail->send();
      // -------------------------------

      $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

      $_SESSION["msg"] = "O cadastro da proposta foi concluído!";
      header("Location: ../painel.php");
      //header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    } catch (PDOException $e) {
      //echo 'Error: ' . $e->getMessage();
      $conn->rollBack();
      $_SESSION["erro"] = "Cadastro não realizado!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#top_ancora");
      return die;
    }
  }
}















/*****************************************************************************************
                                EDITA PROPOSTA PARCERIAS
 *****************************************************************************************/
// if (isset($dados['CadPropostasParcerias'])) {
if (isset($_GET['funcao']) && $_GET['funcao'] == "edit_prop_parc") {

  $prop_id                          = base64_decode($_POST['prop_id']);
  $prop_tipo                        = base64_decode($_POST['prop_tipo']);
  $prop_codigo                      = base64_decode($_POST['prop_codigo']);
  $prop_status_etapa                = 5;
  $reservm_user_id                     = $_SESSION['reservm_user_id'];
  $prop_parc_nome_empresa           = trim($_POST['prop_parc_nome_empresa']) !== '' ? trim($_POST['prop_parc_nome_empresa']) : NULL;
  $prop_parc_tipo_empresa           = trim($_POST['prop_parc_tipo_empresa']) !== '' ? trim($_POST['prop_parc_tipo_empresa']) : NULL;
  // -------------------------------
  if ($prop_parc_tipo_empresa == '1') {
    $prop_parc_tipo_outro           = trim($_POST['prop_parc_tipo_outro']) !== '' ? trim($_POST['prop_parc_tipo_outro']) : NULL;
  } else {
    $prop_parc_tipo_outro           = NULL;
  }
  // -------------------------------
  if ($prop_parc_tipo_empresa == '4') {
    $prop_parc_orgao_empresa        = trim($_POST['prop_parc_orgao_empresa']) !== '' ? trim($_POST['prop_parc_orgao_empresa']) : NULL;
  } else {
    $prop_parc_orgao_empresa        = NULL;
  }
  // ------------------------------- 
  $prop_parc_email                  = trim($_POST['prop_parc_email']) !== '' ? trim($_POST['prop_parc_email']) : NULL;
  $prop_parc_telefone               = trim($_POST['prop_parc_telefone']) !== '' ? trim($_POST['prop_parc_telefone']) : NULL;
  $prop_parc_cep                    = trim($_POST['prop_parc_cep']) !== '' ? trim($_POST['prop_parc_cep']) : NULL;
  $prop_parc_logradouro             = trim($_POST['prop_parc_logradouro']) !== '' ? trim($_POST['prop_parc_logradouro']) : NULL;
  $prop_parc_numero                 = trim($_POST['prop_parc_numero']) !== '' ? trim($_POST['prop_parc_numero']) : NULL;
  $prop_parc_bairro                 = trim($_POST['prop_parc_bairro']) !== '' ? trim($_POST['prop_parc_bairro']) : NULL;
  $prop_parc_municipio              = trim($_POST['prop_parc_municipio']) !== '' ? trim($_POST['prop_parc_municipio']) : NULL;
  $prop_parc_estado                 = trim($_POST['prop_parc_estado']) !== '' ? trim($_POST['prop_parc_estado']) : NULL;
  $prop_parc_pais                   = trim($_POST['prop_parc_pais']) !== '' ? trim($_POST['prop_parc_pais']) : NULL;
  $prop_parc_responsavel            = trim($_POST['prop_parc_responsavel']) !== '' ? trim($_POST['prop_parc_responsavel']) : NULL;
  $prop_parc_cargo                  = trim($_POST['prop_parc_cargo']) !== '' ? trim($_POST['prop_parc_cargo']) : NULL;
  $prop_parc_contato_referencia     = trim($_POST['prop_parc_contato_referencia']) !== '' ? trim($_POST['prop_parc_contato_referencia']) : NULL;
  $prop_parc_possui_convenio        = trim(isset($_POST['prop_parc_possui_convenio'])) ? $_POST['prop_parc_possui_convenio'] : 0;
  $prop_parc_tipo_parceria          = trim($_POST['prop_parc_tipo_parceria']) !== '' ? trim($_POST['prop_parc_tipo_parceria']) : NULL;
  $prop_parc_titulo_atividade       = trim($_POST['prop_parc_titulo_atividade']) !== '' ? trim($_POST['prop_parc_titulo_atividade']) : NULL;
  $prop_parc_objetivo_atividade     = trim($_POST['prop_parc_objetivo_atividade']) !== '' ? nl2br(trim($_POST['prop_parc_objetivo_atividade'])) : NULL;
  $prop_parc_carga_hora             = trim($_POST['prop_parc_carga_hora']) !== '' ? trim($_POST['prop_parc_carga_hora']) : NULL;
  $prop_parc_data_atividade         = trim($_POST['prop_parc_data_atividade']) !== '' ? trim($_POST['prop_parc_data_atividade']) : NULL;
  $prop_parc_hora_atividade_inicial = trim($_POST['prop_parc_hora_atividade_inicial']) !== '' ? trim($_POST['prop_parc_hora_atividade_inicial']) : NULL;
  $prop_parc_hora_atividade_final   = trim($_POST['prop_parc_hora_atividade_final']) !== '' ? trim($_POST['prop_parc_hora_atividade_final']) : NULL;
  $prop_parc_local_atividade        = trim($_POST['prop_parc_local_atividade']) !== '' ? trim($_POST['prop_parc_local_atividade']) : NULL;
  $prop_parc_tipo_espaco            = trim($_POST['prop_parc_tipo_espaco']) !== '' ? trim($_POST['prop_parc_tipo_espaco']) : NULL;
  $prop_parc_campus_atividade       = trim($_POST['prop_parc_campus_atividade']) !== '' ? trim($_POST['prop_parc_campus_atividade']) : NULL;
  $prop_parc_numero_participantes   = trim($_POST['prop_parc_numero_participantes']) !== '' ? trim($_POST['prop_parc_numero_participantes']) : NULL;
  $prop_parc_recursos_necessarios   = trim($_POST['prop_parc_recursos_necessarios']) !== '' ? nl2br(trim($_POST['prop_parc_recursos_necessarios'])) : NULL;
  $prop_parc_organizacao_espaco     = trim($_POST['prop_parc_organizacao_espaco']) !== '' ? trim($_POST['prop_parc_organizacao_espaco']) : NULL;
  $prop_parc_beneficios             = trim($_POST['prop_parc_beneficios']) !== '' ? trim($_POST['prop_parc_beneficios']) : NULL;
  // -------------------------------
  if ($prop_parc_beneficios == 'SIM') {
    $prop_parc_beneficios_quantidade = trim($_POST['prop_parc_beneficios_quantidade']) !== '' ? nl2br(trim($_POST['prop_parc_beneficios_quantidade'])) : NULL;
  } else {
    $prop_parc_beneficios_quantidade = NULL;
  }
  // -------------------------------
  $prop_parc_comentarios            = trim($_POST['prop_parc_comentarios']) !== '' ? nl2br(trim($_POST['prop_parc_comentarios'])) : NULL;
  // -------------------------------

  // SE TIPO NÃO FOR ENCONTRADO IMPEDE O CADASTRO
  $tipos = array(1, 2, 3, 4, 5);
  if (!in_array($prop_tipo, $tipos)) {
    $_SESSION["erro"] = "Categoria da proposta não encontrada!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  } else {

    try {
      $conn->beginTransaction(); // INICIA A TRANSAÇÃO
      $sql = "UPDATE
                      propostas
                SET
                      prop_status_etapa                = :prop_status_etapa,
                      prop_titulo                      = UPPER(:prop_titulo),
                      prop_parc_nome_empresa           = UPPER(:prop_parc_nome_empresa),
                      prop_parc_tipo_empresa           = :prop_parc_tipo_empresa,
                      prop_parc_tipo_outro             = UPPER(:prop_parc_tipo_outro),
                      prop_parc_orgao_empresa          = UPPER(:prop_parc_orgao_empresa),
                      prop_parc_email                  = LOWER(:prop_parc_email),
                      prop_parc_telefone               = :prop_parc_telefone,
                      prop_parc_cep                    = :prop_parc_cep,
                      prop_parc_logradouro             = UPPER(:prop_parc_logradouro),
                      prop_parc_numero                 = UPPER(:prop_parc_numero),
                      prop_parc_bairro                 = UPPER(:prop_parc_bairro),
                      prop_parc_municipio              = UPPER(:prop_parc_municipio),
                      prop_parc_estado                 = UPPER(:prop_parc_estado),
                      prop_parc_pais                   = UPPER(:prop_parc_pais),
                      prop_parc_responsavel            = UPPER(:prop_parc_responsavel),
                      prop_parc_cargo                  = UPPER(:prop_parc_cargo),
                      prop_parc_contato_referencia     = UPPER(:prop_parc_contato_referencia),
                      prop_parc_possui_convenio        = :prop_parc_possui_convenio,
                      prop_parc_tipo_parceria          = :prop_parc_tipo_parceria,
                      prop_parc_titulo_atividade       = UPPER(:prop_parc_titulo_atividade),
                      prop_parc_objetivo_atividade     = :prop_parc_objetivo_atividade,
                      prop_parc_carga_hora             = :prop_parc_carga_hora,
                      prop_parc_data_atividade         = :prop_parc_data_atividade,
                      prop_parc_hora_atividade_inicial = :prop_parc_hora_atividade_inicial,
                      prop_parc_hora_atividade_final   = :prop_parc_hora_atividade_final,
                      prop_parc_local_atividade        = UPPER(:prop_parc_local_atividade),
                      prop_parc_tipo_espaco            = UPPER(:prop_parc_tipo_espaco),
                      prop_parc_campus_atividade       = :prop_parc_campus_atividade,
                      prop_parc_numero_participantes   = :prop_parc_numero_participantes,
                      prop_parc_recursos_necessarios   = :prop_parc_recursos_necessarios,
                      prop_parc_beneficios             = UPPER(:prop_parc_beneficios),
                      prop_parc_beneficios_quantidade  = :prop_parc_beneficios_quantidade,
                      prop_parc_organizacao_espaco     = :prop_parc_organizacao_espaco,
                      prop_parc_comentarios            = :prop_parc_comentarios,
                      prop_data_upd                    = GETDATE()
                WHERE
                      prop_id                          = :prop_id";

      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':prop_id' => $prop_id,
        ':prop_status_etapa' => $prop_status_etapa,
        ':prop_titulo' => $prop_parc_nome_empresa,
        ':prop_parc_nome_empresa' => $prop_parc_nome_empresa,
        ':prop_parc_tipo_empresa' => $prop_parc_tipo_empresa,
        ':prop_parc_tipo_outro' => $prop_parc_tipo_outro,
        ':prop_parc_orgao_empresa' => $prop_parc_orgao_empresa,
        ':prop_parc_email' => $prop_parc_email,
        ':prop_parc_telefone' => $prop_parc_telefone,
        ':prop_parc_cep' => $prop_parc_cep,
        ':prop_parc_logradouro' => $prop_parc_logradouro,
        ':prop_parc_numero' => $prop_parc_numero,
        ':prop_parc_bairro' => $prop_parc_bairro,
        ':prop_parc_municipio' => $prop_parc_municipio,
        ':prop_parc_estado' => $prop_parc_estado,
        ':prop_parc_pais' => $prop_parc_pais,
        ':prop_parc_responsavel' => $prop_parc_responsavel,
        ':prop_parc_cargo' => $prop_parc_cargo,
        ':prop_parc_contato_referencia' => $prop_parc_contato_referencia,
        ':prop_parc_possui_convenio' => $prop_parc_possui_convenio,
        ':prop_parc_tipo_parceria' => $prop_parc_tipo_parceria,
        ':prop_parc_titulo_atividade' => $prop_parc_titulo_atividade,
        ':prop_parc_objetivo_atividade' => $prop_parc_objetivo_atividade,
        ':prop_parc_local_atividade' => $prop_parc_local_atividade,
        ':prop_parc_tipo_espaco' => $prop_parc_tipo_espaco,
        ':prop_parc_campus_atividade' => $prop_parc_campus_atividade,
        ':prop_parc_carga_hora' => $prop_parc_carga_hora,
        ':prop_parc_data_atividade' => $prop_parc_data_atividade,
        ':prop_parc_hora_atividade_inicial' => $prop_parc_hora_atividade_inicial,
        ':prop_parc_hora_atividade_final' => $prop_parc_hora_atividade_final,
        ':prop_parc_numero_participantes' => $prop_parc_numero_participantes,
        ':prop_parc_recursos_necessarios' => $prop_parc_recursos_necessarios,
        ':prop_parc_beneficios' => $prop_parc_beneficios,
        ':prop_parc_beneficios_quantidade' => $prop_parc_beneficios_quantidade,
        ':prop_parc_organizacao_espaco' => $prop_parc_organizacao_espaco,
        ':prop_parc_comentarios' => $prop_parc_comentarios
      ]);


      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                              VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'     => 'PROPOSTA - PARCERIAS',
        ':acao'       => 'ATUALIZAÇÃO',
        ':acao_id'    => $prop_id,
        ':dados'      => 'Tipo Proposta: ' . $prop_tipo .
          '; Código: ' . $prop_codigo .
          '; Etapa: ' . $prop_status_etapa .
          '; Empresa: ' . $prop_parc_nome_empresa .
          '; Tipo empresa: ' . $prop_parc_tipo_empresa .
          '; Tipo (outro): ' . $prop_parc_tipo_outro .
          '; Órgão: ' . $prop_parc_orgao_empresa .
          '; E-amil: ' . $prop_parc_email .
          '; Contato: ' . $prop_parc_telefone .
          '; prop_parc_cep: ' . $prop_parc_cep .
          '; CEP: ' . $prop_parc_logradouro .
          '; Rua: ' . $prop_parc_numero .
          '; Bairro: ' . $prop_parc_bairro .
          '; Município: ' . $prop_parc_municipio .
          '; Estado: ' . $prop_parc_estado .
          '; PAis: ' . $prop_parc_pais .
          '; Responsável: ' . $prop_parc_responsavel .
          '; Cargo: ' . $prop_parc_cargo .
          '; Cont. referência: ' . $prop_parc_contato_referencia .
          '; Possui convêncio: ' . $prop_parc_possui_convenio .
          '; Tipo parceria: ' . $prop_parc_tipo_parceria .
          '; Tit. atividade: ' . $prop_parc_titulo_atividade .
          '; Objetivo Atividade: ' . $prop_parc_objetivo_atividade .
          '; Local atividade: ' . $prop_parc_local_atividade .
          '; Tipo Espaço: ' . $prop_parc_tipo_espaco .
          '; Campus: ' . $prop_parc_campus_atividade .
          '; Carga horária: ' . $prop_parc_carga_hora .
          '; Data Atividade: ' . $prop_parc_data_atividade .
          '; Hora Inicial: ' . $prop_parc_hora_atividade_inicial .
          '; Hora Final: ' . $prop_parc_hora_atividade_final .
          '; Numero Participantes: ' . $prop_parc_numero_participantes .
          '; Recursos Necessários: ' . $prop_parc_recursos_necessarios .
          '; Benefícios: ' . $prop_parc_beneficios .
          '; Quant. Benefícios: ' . $prop_parc_beneficios_quantidade .
          '; Organização: ' . $prop_parc_organizacao_espaco .
          '; Comentários: ' . $prop_parc_comentarios,
        ':user_id'    => $reservm_user_id
      ]);
      // -------------------------------

      // CADASTRA AS IMAGENS
      if (!empty($_FILES["arquivos"]["name"][0])) { // SE ALGUAMA IMAGEM FOR ENVIADA...

        $parq_categoria = 3; // CATEGORIA DE ARQUIVO - PROPOSTA = 3
        $arquivos       = $_FILES["arquivos"]; // RECEBE O(S) ARQUIVO(S)

        // 10MB
        $maxFileSize = 10 * 1024 * 1024;
        foreach ($_FILES["arquivos"]["name"] as $key => $fileName) { // CALCULA O TAMANHO DOS ARQUIVOS ENVIADOS
          if (!empty($fileName)) {
            $fileSize = $_FILES["arquivos"]["size"][$key];
            if ($fileSize > $maxFileSize) {
              $_SESSION["erro"] = "O arquivo excede o tamanho máximo permitido de 2MB.";
              $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
              header("Location: $referer#parq_ancora");
              return die;
            }
          }
        }
        // -------------------------------

        // IMPEDE QUE MAIS DE 20 ARQUIVOS SEJAM ENVIADOS
        $uploadedFilesCount = count($_FILES["arquivos"]["name"]);
        if ($uploadedFilesCount > 20) {
          $_SESSION["erro"] = "Você pode enviar no máximo 5 arquivos.";
          $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
          header("Location: $referer#parq_ancora");
          return die;
        }
        // -------------------------------

        // CRIA AS PASTAS DOS ARQUIVOS
        $pastaPrincipal = "../uploads/propostas/$prop_codigo";
        $SubPasta = "../uploads/propostas/$prop_codigo/$parq_categoria";
        // CRIA A PASTA PRINCIPAL SE NÃO EXISTIR
        if (!file_exists($pastaPrincipal)) {
          mkdir($pastaPrincipal, 0777, true);
        }
        // -------------------------------

        // CRIA A SUBPASTA
        if (!file_exists($SubPasta)) {
          mkdir($SubPasta, 0777, true);
        }
        // -------------------------------

        $nomes = $arquivos['name']; //CAPTURA NOMES DOS ARQUIVOS

        for ($i = 0; $i < count($nomes); $i++) {
          $extensao = explode('.', $nomes[$i]);
          $extensao = end($extensao);
          $nomes[$i] = rand() . '-' . $nomes[$i];

          $arquivos_permitidos = ['pdf', 'PDF', 'xlsx', 'xls', 'XLSX', 'XLS', 'doc', 'DOC', 'docx', 'DOCX', 'csv', 'CSV', 'jpg', 'JPG', 'jpeg', 'png', 'PNG', 'txt', 'TXT']; //FORMATO DE ARQUIVOS PERMITIDOS
          if (in_array($extensao, $arquivos_permitidos)) { // VERIFICAR EXTENSÃO DOS ARQUIVOS
            $sql = "INSERT INTO propostas_arq (
                                                  parq_prop_id,
                                                  parq_codigo,
                                                  parq_categoria,
                                                  parq_arquivo,
                                                  parq_user_id,
                                                  parq_data_cad
                                                  ) VALUES (
                                                  :parq_prop_id,
                                                  :parq_codigo,
                                                  :parq_categoria,
                                                  :parq_arquivo,
                                                  :parq_user_id,
                                                  GETDATE()
                                                  )";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
              ':parq_prop_id' => $prop_id,
              ':parq_codigo' => $prop_codigo,
              ':parq_categoria' => $parq_categoria,
              ':parq_arquivo' => $nomes[$i],
              ':parq_user_id' => $reservm_user_id
            ]);

            // REGISTRA AÇÃO NO LOG
            $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
            $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                                    VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
            $stmt->execute([
              ':modulo'     => 'PROPOSTA - PARCERIAS - ARQUIVOS',
              ':acao'       => 'CADASTRO',
              ':acao_id'    => $prop_id,
              ':dados'      => 'ID: ' . $last_id . '; Código: ' . $prop_codigo . '; Categoria: ' . $parq_categoria . '; Arquivo: ' . $nomes[$i],
              ':user_id'    => $reservm_user_id
            ]);
            // -------------------------------

            // MOVE AS IMAGENS PARA A PASTA
            $total_rowns = $stmt->rowCount();
            if ($total_rowns > 0) {
              $mover = move_uploaded_file($_FILES['arquivos']['tmp_name'][$i], '../uploads/propostas/' . $prop_codigo . '/' . $parq_categoria . '/' . $nomes[$i]);
            }
            // -------------------------------

          } else {
            $_SESSION["erro"] = "Formato de arquivo inválido!";
            $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
            header("Location: $referer#parq_ancora");
            return die;
          }
        }
      }

      // SE 'STATUS_ETAPA' FOR DIFERENTE DE 4, QUER DIZER QUE O STATUS AINDA NÃO FOI ATUALIZADO
      if (base64_decode($_POST['prop_status_etapa']) == 4 || base64_decode($_POST['prop_status_etapa']) == 0) {

        // STATUS DA ANÁLISE
        $num_status  = 2; // 2 = CONCLUÍDO
        $sql = "INSERT INTO propostas_analise_status (
                                                      sta_an_prop_id,
                                                      sta_an_status,
                                                      sta_an_user_id,
                                                      sta_an_data_cad,
                                                      sta_an_data_upd
                                                      ) VALUES (
                                                      :sta_an_prop_id,
                                                      :sta_an_status,
                                                      :sta_an_user_id,
                                                      GETDATE(),
                                                      GETDATE()
                                                      )";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
          ':sta_an_prop_id' => $prop_id,
          ':sta_an_status' => $num_status,
          ':sta_an_user_id' => $reservm_user_id
        ]);
        // -------------------------------

        // ALTERA O STATUS DA PROPOSTA
        $sql = "UPDATE
                    propostas_status
                SET        
                    prop_sta_status   = :prop_sta_status,
                    prop_sta_user_id  = :prop_sta_user_id,
                    prop_sta_data_cad = GETDATE()
                WHERE
                    prop_sta_prop_id  = :prop_sta_prop_id";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
          ':prop_sta_prop_id' => $prop_id,
          ':prop_sta_status' => $num_status,
          ':prop_sta_user_id' => $reservm_user_id
        ]);
        // -------------------------------

        // ENVIA E-MAIL PARA USUÁRIO
        $mail = new PHPMailer(true);
        include '../controller/email_conf.php';
        $mail->addAddress($_SESSION['reservm_user_email'], 'RESERVM'); // E-MAIL DO USUÁRIO QUE RECEBERÁ A MENSAGEM
        $mail->isHTML(true);
        $mail->Subject = $prop_codigo . ' - Cadastro da proposta concluído'; // TÍTULO DO E-MAIL

        // CORPO DO EMAIL
        include '../includes/email/email_header.php';
        $email_conteudo .= "
          <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
            <td style='padding: 2em 2rem; display: inline-block; width: 100%'>
    
            <p style='font-size: 1.3rem; font-weight: 600; margin: 0px 0px 40px 0px;'>Cadastro da proposta concluído!</p>
    
            <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 10px 0px;'>Seu cadastro da proposta de código: <strong> $prop_codigo </strong> foi concluído. <br> A equipe de extensão irá analisar a sua proposta.</p>
    
            <a style='cursor: pointer;' href='$url_sistema'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 30px;' target='_blank'>Acesse o sistema</button></a>
            </td>
          </tr>";
        include '../includes/email/email_footer.php';

        $mail->Body  = $email_conteudo;
        $mail->send();
        // -------------------------------

        // ENVIA E-MAIL PARA ADMINISTRADOR
        $mail = new PHPMailer(true);
        include '../controller/email_conf.php';
        $mail->addAddress($email_extensao, 'RESERVM'); // E-MAIL DA EXTENSÃO
        $mail->isHTML(true);
        $mail->Subject = $prop_codigo . ' - Cadastro da proposta concluído'; //TÍTULO DO E-MAIL

        // CORPO DO EMAIL
        include '../includes/email/email_header.php';
        $email_conteudo .= "
          <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
            <td style='padding: 2em 2rem; display: inline-block; width: 100%'>
    
            <p style='font-size: 1.3rem; font-weight: 600; margin: 0px 0px 40px 0px;'>Cadastro da proposta concluído!</p>
    
            <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 10px 0px;'>O cadastro da proposta de código: <strong> $prop_codigo </strong> foi concluído. <br> A análise dos dados pode ser iniciada.</p>
    
            <a style='cursor: pointer;' href='$url_sistema/admin'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 30px;' target='_blank'>Acesse o sistema</button></a>
            </td>
          </tr>";
        include '../includes/email/email_footer.php';

        $mail->Body  = $email_conteudo;
        $mail->send();
      }
      // -------------------------------

      $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

      $_SESSION["msg"] = "Dados atualizados com sucesso!";
      //header("Location: ../painel.php");
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    } catch (PDOException $e) {
      //echo 'Error: ' . $e->getMessage();
      $conn->rollBack();
      $_SESSION["erro"] = "Cadastro não realizado!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#top_ancora");
      return die;
    }
  }
}




















/*****************************************************************************************
                        EDITAR PROPOSTA - PROGRAMAS
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "cad_prop_prog") {

  $prop_id                     = base64_decode($_POST['prop_id']);
  $prop_tipo                   = base64_decode($_POST['prop_tipo']);
  $prop_codigo                 = base64_decode($_POST['prop_codigo']);
  $prop_status_etapa           = 5;
  $reservm_user_id                = $_SESSION['reservm_user_id'];
  //
  $prop_prog_tipo = trim($_POST['prop_prog_tipo']) !== '' ? trim($_POST['prop_prog_tipo']) : NULL;
  //
  if ($prop_prog_tipo == 1) {
    $prop_prog_categoria       = trim($_POST['prop_pprop_prog_categoriarog_tipo']) !== '' ? trim($_POST['prop_prog_categoria']) : NULL;
    //
    $prop_prog_docente         = NULL;
    $prop_prog_area_atuacao    = NULL;
    $prop_prog_local_atuacao   = NULL;
    $prop_prog_valor_inscricao = NULL;
    $prop_prog_data_inicio     = NULL;
    $prop_prog_data_fim        = NULL;
  }
  if ($prop_prog_tipo == 2) {
    $prop_prog_categoria       = trim($_POST['prop_pprop_prog_categoriarog_tipo']) !== '' ? trim($_POST['prop_prog_categoria']) : NULL;
    //
    $prop_prog_docente         = trim($_POST['prop_prog_docente']) !== '' ? trim($_POST['prop_prog_docente']) : NULL;
    $prop_prog_area_atuacao    = trim($_POST['prop_prog_area_atuacao']) !== '' ? trim($_POST['prop_prog_area_atuacao']) : NULL;
    $prop_prog_local_atuacao   = trim($_POST['prop_prog_local_atuacao']) !== '' ? trim($_POST['prop_prog_local_atuacao']) : NULL;
    $prop_prog_valor_inscricao = trim($_POST['prop_prog_valor_inscricao']) !== '' ? trim($_POST['prop_prog_valor_inscricao']) : NULL;
    $prop_prog_data_inicio     = trim($_POST['prop_prog_data_inicio']) !== '' ? trim($_POST['prop_prog_data_inicio']) : NULL;
    $prop_prog_data_fim        = trim($_POST['prop_prog_data_fim']) !== '' ? trim($_POST['prop_prog_data_fim']) : NULL;

    if ($prop_prog_tipo == 2 || $prop_prog_tipo == 3) {

      $sql = "DELETE FROM propostas_arq WHERE parq_codigo = '$prop_codigo' AND parq_categoria = 4";
      $conn->exec($sql);
      $subpasta    = "../uploads/propostas/$prop_codigo/4"; // APAGA O ARQUIVOS IMAGENS
      $subarquivos = glob($subpasta . '/*'); // OBTÉM A LISTA DE IMAGENS

      // LOOP PARA EXCLUIR OS ARQUIVOS
      foreach ($subarquivos as $subarquivo) {
        if (is_file($subarquivo)) {
          unlink($subarquivo); // EXCLUI O ARQUIVO
        }
      }
      rmdir($subpasta); // APÓS EXCLUIR ARQUIVOS, EXCLUI A PASTA

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                              VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'    => 'PROPOSTA - PROGRAMAS - ARQUIVOS',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $prop_id,
        ':user_id'   => $reservm_user_id
      ]);
      // -------------------------------
    }
  }
  // -------------------------------
  $prop_prog_obs = trim($_POST['prop_prog_obs']) !== '' ? nl2br(trim($_POST['prop_prog_obs'])) : NULL;

  // SE TIPO NÃO FOR ENCONTRADO IMPEDE O CADASTRO
  $tipos = array(1, 2, 3, 4, 5);
  if (!in_array($prop_tipo, $tipos)) {
    $_SESSION["erro"] = "Categoria da proposta não encontrada!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  } else {

    try {
      $conn->beginTransaction(); // INICIA A TRANSAÇÃO
      $sql = "UPDATE
                  propostas
              SET 
                  prop_status_etapa         = :prop_status_etapa,
                  prop_prog_tipo            = :prop_prog_tipo,
                  prop_prog_categoria       = :prop_prog_categoria,
                  prop_prog_valor_inscricao = :prop_prog_valor_inscricao,
                  prop_prog_docente         = UPPER(:prop_prog_docente),
                  prop_prog_area_atuacao    = :prop_prog_area_atuacao,
                  prop_prog_local_atuacao   = UPPER(:prop_prog_local_atuacao),
                  prop_prog_data_inicio     = :prop_prog_data_inicio,
                  prop_prog_data_fim        = :prop_prog_data_fim,
                  prop_prog_obs             = :prop_prog_obs,
                  prop_data_upd             = GETDATE()
              WHERE
                  prop_id                   = :prop_id";

      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':prop_id' => $prop_id,
        ':prop_status_etapa' => $prop_status_etapa,
        ':prop_prog_tipo' => $prop_prog_tipo,
        ':prop_prog_categoria' => $prop_prog_categoria,
        ':prop_prog_valor_inscricao' => $prop_prog_valor_inscricao,
        ':prop_prog_docente' => $prop_prog_docente,
        ':prop_prog_area_atuacao' => $prop_prog_area_atuacao,
        ':prop_prog_local_atuacao' => $prop_prog_local_atuacao,
        ':prop_prog_data_inicio' => $prop_prog_data_inicio,
        ':prop_prog_data_fim' => $prop_prog_data_fim,
        ':prop_prog_obs' => $prop_prog_obs
      ]);

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                              VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'  => 'PROPOSTA - PROGRAMAS',
        ':acao'    => 'ATUALIZAÇÃO',
        ':acao_id' => $prop_id,
        ':dados'   =>
        'Tipo: ' . $prop_prog_tipo .
          '; Categoria: ' . $prop_prog_categoria .
          '; Valor: ' . $prop_prog_valor_inscricao .
          '; Docente: ' . $prop_prog_docente .
          '; Área atuação: ' . $prop_prog_area_atuacao .
          '; Local atuação: ' . $prop_prog_local_atuacao .
          '; Data Inicio: ' . $prop_prog_data_inicio .
          '; Data Fim: ' . $prop_prog_data_fim .
          '; Obs: ' . $prop_prog_obs,
        ':user_id' => $reservm_user_id
      ]);
      // -------------------------------

      // CADASTRA ARQUIVO - PROGRAMAS INTEGRADO AO ENSINO
      if (!empty($_FILES['arquivo']['name'])) {
        if ($_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {

          $parq_categoria    = 4; // PROGRAMAS INTEGRADO AO ENSINO = 4
          $nomeArquivo       = $_FILES['arquivo']['name'];
          $caminhoTemporario = $_FILES['arquivo']['tmp_name'];

          // CRIA A PASTA DO ARQUIVO
          $pastaPrincipal = "../uploads/propostas/$prop_codigo";
          $SubPasta = "../uploads/propostas/$prop_codigo/$parq_categoria";
          // CRIA A PASTA PRINCIPAL SE NÃO EXISTIR
          if (!file_exists($pastaPrincipal)) {
            mkdir($pastaPrincipal, 0777, true);
          }
          // -------------------------------

          // CRIA A SUBPASTA
          if (!file_exists($SubPasta)) {
            mkdir($SubPasta, 0777, true);
          }
          // -------------------------------

          // CALCULA O TAMANHO DOS ARQUIVOS ENVIADOS
          $tamanhoMaximo = 10 * 1024 * 1024; // 10MB
          if ($_FILES['arquivo']['size'] >= $tamanhoMaximo) {
            $_SESSION["erro"] = "O arquivo excede o tamanho máximo permitido de 2MB.";
            $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
            header("Location: $referer#prop_prog_ancora");
            return die;
          }
          // -------------------------------

          //FORMATO DE ARQUIVOS PERMITIDOS
          $extensoesPermitidas = ['pdf', 'PDF', 'xlsx', 'xls', 'XLSX', 'XLS', 'doc', 'DOC', 'docx', 'DOCX', 'csv', 'CSV', 'jpg', 'JPG', 'jpeg', 'png', 'PNG', 'txt', 'TXT'];
          $extensao = strtolower(pathinfo($nomeArquivo, PATHINFO_EXTENSION));
          if (!in_array($extensao, $extensoesPermitidas)) {
            $_SESSION["erro"] = "O arquivo não está no formato permitido.";
            $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
            header("Location: $referer#prop_prog_ancora");
            return die;
          }
          // -------------------------------

          $sql = "INSERT INTO propostas_arq (
                                              parq_prop_id,
                                              parq_codigo,
                                              parq_categoria,
                                              parq_arquivo,
                                              parq_user_id,
                                              parq_data_cad
                                            ) VALUES (
                                              :parq_prop_id,
                                              :parq_codigo,
                                              :parq_categoria,
                                              :parq_arquivo,
                                              :parq_user_id,
                                              GETDATE()
                                            )";
          $stmt = $conn->prepare($sql);
          $stmt->execute([
            ':parq_prop_id' => $prop_id,
            ':parq_codigo' => $prop_codigo,
            ':parq_categoria' => $parq_categoria,
            ':parq_arquivo' => $nomeArquivo,
            ':parq_user_id' => $reservm_user_id
          ]);

          // MOVE AS IMAGENS PARA A PASTA
          $mover = move_uploaded_file($caminhoTemporario, $SubPasta . '/' . $nomeArquivo);
          // -------------------------------

          // REGISTRA AÇÃO NO LOG
          $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
          $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                                  VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
          $stmt->execute([
            ':modulo'     => 'PROPOSTA - PROGRAMAS - ARQUIVO PROGRAMAS INTEGRADO',
            ':acao'       => 'CADASTRO',
            ':acao_id'    => $prop_id,
            ':dados'      => 'ID: ' . $last_id . '; Código: ' . $prop_codigo . '; Categoria: ' . $parq_categoria . '; Arquivo: ' . $nomeArquivo,
            ':user_id'    => $reservm_user_id
          ]);
          // -------------------------------

        } else {
          $_SESSION["erro"] = "Erro no upload do arquivo!";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        }
      }
      // -------------------------------

      // CADASTRA AS IMAGENS
      if (!empty($_FILES["arquivos"]["name"][0])) { // SE ALGUAMA IMAGEM FOR ENVIADA...

        $parq_categoria = 3; // CATEGORIA DE ARQUIVO - PROPOSTA = 3
        $arquivos       = $_FILES["arquivos"]; // RECEBE O(S) ARQUIVO(S)

        // 10MB
        $maxFileSize = 10 * 1024 * 1024;
        foreach ($_FILES["arquivos"]["name"] as $key => $fileName) { // CALCULA O TAMANHO DOS ARQUIVOS ENVIADOS
          if (!empty($fileName)) {
            $fileSize = $_FILES["arquivos"]["size"][$key];
            if ($fileSize > $maxFileSize) {
              $_SESSION["erro"] = "O arquivo excede o tamanho máximo permitido de 2MB.";
              $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
              header("Location: $referer#parq_ancora");
              return die;
            }
          }
        }
        // -------------------------------

        // IMPEDE QUE MAIS DE 20 ARQUIVOS SEJAM ENVIADOS
        $uploadedFilesCount = count($_FILES["arquivos"]["name"]);
        if ($uploadedFilesCount > 20) {
          $_SESSION["erro"] = "Você pode enviar no máximo 5 arquivos.";
          $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
          header("Location: $referer#parq_ancora");
          return die;
        }
        // -------------------------------

        // CRIA AS PASTAS DOS ARQUIVOS
        $pastaPrincipal = "../uploads/propostas/$prop_codigo";
        $SubPasta = "../uploads/propostas/$prop_codigo/$parq_categoria";
        // CRIA A PASTA PRINCIPAL SE NÃO EXISTIR
        if (!file_exists($pastaPrincipal)) {
          mkdir($pastaPrincipal, 0777, true);
        }
        // -------------------------------

        // CRIA A SUBPASTA
        if (!file_exists($SubPasta)) {
          mkdir($SubPasta, 0777, true);
        }
        // -------------------------------

        $nomes = $arquivos['name']; //CAPTURA NOMES DOS ARQUIVOS

        for ($i = 0; $i < count($nomes); $i++) {
          $extensao = explode('.', $nomes[$i]);
          $extensao = end($extensao);
          $nomes[$i] = rand() . '-' . $nomes[$i];

          $arquivos_permitidos = ['pdf', 'PDF', 'xlsx', 'xls', 'XLSX', 'XLS', 'doc', 'DOC', 'docx', 'DOCX', 'csv', 'CSV', 'jpg', 'JPG', 'jpeg', 'png', 'PNG', 'txt', 'TXT']; //FORMATO DE ARQUIVOS PERMITIDOS
          if (in_array($extensao, $arquivos_permitidos)) { // VERIFICAR EXTENSÃO DOS ARQUIVOS

            $sql = "INSERT INTO propostas_arq (
                                                parq_prop_id,
                                                parq_codigo,
                                                parq_categoria,
                                                parq_arquivo,
                                                parq_user_id,
                                                parq_data_cad
                                                ) VALUES (
                                                :parq_prop_id,
                                                :parq_codigo,
                                                :parq_categoria,
                                                :parq_arquivo,
                                                :parq_user_id,
                                                GETDATE()
                                              )";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
              ':parq_prop_id' => $prop_id,
              ':parq_codigo' => $prop_codigo,
              ':parq_categoria' => $parq_categoria,
              ':parq_arquivo' => $nomes[$i],
              ':parq_user_id' => $reservm_user_id
            ]);

            // REGISTRA AÇÃO NO LOG
            $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
            $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                                      VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
            $stmt->execute([
              ':modulo'     => 'PROPOSTA - CURSOS - ARQUIVOS',
              ':acao'       => 'CADASTRO',
              ':acao_id'    => $prop_id,
              ':dados'      => 'ID: ' . $last_id . '; Código: ' . $prop_codigo . '; Categoria: ' . $parq_categoria . '; Arquivo: ' . $nomes[$i],
              ':user_id'    => $reservm_user_id
            ]);
            // -------------------------------

            // MOVE AS IMAGENS PARA A PASTA
            $total_rowns = $stmt->rowCount();
            if ($total_rowns > 0) {
              $mover = move_uploaded_file($_FILES['arquivos']['tmp_name'][$i], '../uploads/propostas/' . $prop_codigo . '/' . $parq_categoria . '/' . $nomes[$i]);
            }
            // -------------------------------

          } else {
            $_SESSION["erro"] = "Formato de arquivo inválido!";
            $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
            header("Location: $referer#parq_ancora");
            return die;
          }
        }
      }

      // SE 'STATUS_ETAPA' FOR DIFERENTE DE 4, QUER DIZER QUE O STATUS AINDA NÃO FOI ATUALIZADO
      if (base64_decode($_POST['prop_status_etapa']) == 4) {

        // STATUS DA ANÁLISE
        $num_status  = 2; // 2 = CONCLUÍDO
        $sql = "INSERT INTO propostas_analise_status (
                                                      sta_an_prop_id,
                                                      sta_an_status,
                                                      sta_an_user_id,
                                                      sta_an_data_cad,
                                                      sta_an_data_upd
                                                      ) VALUES (
                                                      :sta_an_prop_id,
                                                      :sta_an_status,
                                                      :sta_an_user_id,
                                                      GETDATE(),
                                                      GETDATE()
                                                      )";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
          ':sta_an_prop_id' => $prop_id,
          ':sta_an_status' => $num_status,
          ':sta_an_user_id' => $reservm_user_id
        ]);
        // -------------------------------

        // ALTERA O STATUS DA PROPOSTA
        $sql = "UPDATE
                    propostas_status
              SET        
                    prop_sta_status   = :prop_sta_status,
                    prop_sta_user_id  = :prop_sta_user_id,
                    prop_sta_data_cad = GETDATE()
              WHERE
                    prop_sta_prop_id  = :prop_sta_prop_id";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
          ':prop_sta_prop_id' => $prop_id,
          ':prop_sta_status' => $num_status,
          ':prop_sta_user_id' => $reservm_user_id
        ]);
        // -------------------------------

        // ENVIA E-MAIL PARA USUÁRIO
        $mail = new PHPMailer(true);
        include '../controller/email_conf.php';
        $mail->addAddress($_SESSION['reservm_user_email'], 'RESERVM'); // E-MAIL DO USUÁRIO QUE RECEBERÁ A MENSAGEM
        $mail->isHTML(true);
        $mail->Subject = $prop_codigo . ' - Cadastro da proposta concluído'; // TÍTULO DO E-MAIL

        // CORPO DO EMAIL
        include '../includes/email/email_header.php';
        $email_conteudo .= "
          <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
            <td style='padding: 2em 2rem; display: inline-block; width: 100%'>
    
            <p style='font-size: 1.3rem; font-weight: 600; margin: 0px 0px 40px 0px;'>Cadastro da proposta concluído!</p>
    
            <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 10px 0px;'>Seu cadastro da proposta de código: <strong> $prop_codigo </strong> foi concluído. <br> A equipe de extensão irá analisar a sua proposta.</p>
    
            <a style='cursor: pointer;' href='$url_sistema'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 30px;' target='_blank'>Acesse o sistema</button></a>
            </td>
          </tr>";
        include '../includes/email/email_footer.php';

        $mail->Body  = $email_conteudo;
        $mail->send();
        // -------------------------------

        // ENVIA E-MAIL PARA ADMINISTRADOR
        $mail = new PHPMailer(true);
        include '../controller/email_conf.php';
        $mail->addAddress($email_extensao, 'RESERVM'); // E-MAIL DA EXTENSÃO
        $mail->isHTML(true);
        $mail->Subject = $prop_codigo . ' - Cadastro da proposta concluído'; //TÍTULO DO E-MAIL

        // CORPO DO EMAIL
        include '../includes/email/email_header.php';
        $email_conteudo .= "
          <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
            <td style='padding: 2em 2rem; display: inline-block; width: 100%'>
    
            <p style='font-size: 1.3rem; font-weight: 600; margin: 0px 0px 40px 0px;'>Cadastro da proposta concluído!</p>
    
            <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 10px 0px;'>O cadastro da proposta de código: <strong> $prop_codigo </strong> foi concluído. <br> A análise dos dados pode ser iniciada.</p>
    
            <a style='cursor: pointer;' href='$url_sistema/admin'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 30px;' target='_blank'>Acesse o sistema</button></a>
            </td>
          </tr>";
        include '../includes/email/email_footer.php';

        $mail->Body  = $email_conteudo;
        $mail->send();
      }
      // -------------------------------

      $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

      $_SESSION["msg"] = "O cadastro da proposta foi concluído!";
      header("Location: ../painel.php");
      //header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    } catch (PDOException $e) {
      //echo 'Error: ' . $e->getMessage();
      $conn->rollBack();
      $_SESSION["erro"] = "Cadastro não realizado!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#top_ancora");
      return die;
    }
  }
}


















/*****************************************************************************************
                        CADASTRAR PROPOSTA - EXTENSÃO COMUNITÁRIA
 *****************************************************************************************/
// if (isset($dados['CadPropostasExtensaoComunitaria'])) {
if (isset($_GET['funcao']) && $_GET['funcao'] == "cad_prop_ext_comun") {

  $prop_id                           = base64_decode($_POST['prop_id']);
  $prop_tipo                         = base64_decode($_POST['prop_tipo']);
  $prop_codigo                       = base64_decode($_POST['prop_codigo']);
  $prop_status_etapa                 = 5;
  $reservm_user_id                      = $_SESSION['reservm_user_id'];
  $prop_ext_tipo_programa            = trim($_POST['prop_ext_tipo_programa']) !== '' ? trim($_POST['prop_ext_tipo_programa']) : NULL;
  $prop_ext_categoria_evento         = trim($_POST['prop_ext_categoria_evento']) !== '' ? trim($_POST['prop_ext_categoria_evento']) : NULL;

  // OUTRO TIPO DE PROGRAMA DE EXTENSÃO
  if ($prop_ext_categoria_evento === '10') {
    $prop_ext_categoria_evento_outro = trim($_POST['prop_ext_categoria_evento_outro']) !== '' ? trim($_POST['prop_ext_categoria_evento_outro']) : NULL;
  } else {
    $prop_ext_categoria_evento_outro = NULL;
  }
  // -------------------------------
  $prop_ext_inst_atendida            = trim($_POST['prop_ext_inst_atendida']) !== '' ? nl2br(trim($_POST['prop_ext_inst_atendida'])) : NULL;
  $prop_ext_atividades               = trim($_POST['prop_ext_atividades']) !== '' ? nl2br(trim($_POST['prop_ext_atividades'])) : NULL;
  $prop_ext_datas_horas              = trim($_POST['prop_ext_datas_horas']) !== '' ? nl2br(trim($_POST['prop_ext_datas_horas'])) : NULL;
  $prop_ext_mob_equipamento          = trim($_POST['prop_ext_mob_equipamento']) !== '' ? nl2br(trim($_POST['prop_ext_mob_equipamento'])) : NULL;
  $prop_ext_dinamica                 = trim($_POST['prop_ext_dinamica']) !== '' ? nl2br(trim($_POST['prop_ext_dinamica'])) : NULL;
  $prop_ext_quant_atendimento        = trim($_POST['prop_ext_quant_atendimento']) !== '' ? trim($_POST['prop_ext_quant_atendimento']) : NULL;
  $prop_ext_forma_ingresso           = trim($_POST['prop_ext_forma_ingresso']) !== '' ? trim($_POST['prop_ext_forma_ingresso']) : NULL;

  // VALOR DA BOLSA
  if ($prop_ext_forma_ingresso === '1') {
    $prop_ext_valor_bolsa = trim($_POST['prop_ext_valor_bolsa']) !== '' ? trim($_POST['prop_ext_valor_bolsa']) : NULL;
  } else {
    $prop_ext_valor_bolsa = NULL;
  }
  // -------------------------------
  $prop_ext_atendimento_ofertado     = trim($_POST['prop_ext_atendimento_ofertado']) !== '' ? nl2br(trim($_POST['prop_ext_atendimento_ofertado'])) : NULL;
  $prop_ext_impacto_social           = trim($_POST['prop_ext_impacto_social']) !== '' ? nl2br(trim($_POST['prop_ext_impacto_social'])) : NULL;
  $prop_ext_obs                      = trim($_POST['prop_ext_obs']) !== '' ? nl2br(trim($_POST['prop_ext_obs'])) : NULL;
  // -------------------------------

  // SE TIPO NÃO FOR ENCONTRADO IMPEDE O CADASTRO
  $tipos = array(1, 2, 3, 4, 5);
  if (!in_array($prop_tipo, $tipos)) {
    $_SESSION["erro"] = "Categoria da proposta não encontrada!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
    header("Location: $referer#top_ancora");
    return die;
  } else {

    try {
      $conn->beginTransaction(); // INICIA A TRANSAÇÃO
      $sql = "UPDATE
                  propostas
              SET 
                  prop_status_etapa               = :prop_status_etapa,
                  prop_ext_tipo_programa          = :prop_ext_tipo_programa,
                  prop_ext_categoria_evento       = :prop_ext_categoria_evento,
                  prop_ext_categoria_evento_outro = UPPER(:prop_ext_categoria_evento_outro),
                  prop_ext_inst_atendida          = :prop_ext_inst_atendida,
                  prop_ext_atividades             = :prop_ext_atividades,
                  prop_ext_datas_horas            = :prop_ext_datas_horas,
                  prop_ext_mob_equipamento        = :prop_ext_mob_equipamento,
                  prop_ext_dinamica               = :prop_ext_dinamica,
                  prop_ext_quant_atendimento      = :prop_ext_quant_atendimento,
                  prop_ext_forma_ingresso         = :prop_ext_forma_ingresso,
                  prop_ext_valor_bolsa            = :prop_ext_valor_bolsa,
                  prop_ext_atendimento_ofertado   = :prop_ext_atendimento_ofertado,
                  prop_ext_impacto_social         = :prop_ext_impacto_social,
                  prop_ext_obs                    = :prop_ext_obs,
                  prop_data_upd                   = GETDATE()
            WHERE
                  prop_id                         = :prop_id";

      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':prop_id' => $prop_id,
        ':prop_status_etapa' => $prop_status_etapa,
        ':prop_ext_tipo_programa' => $prop_ext_tipo_programa,
        ':prop_ext_categoria_evento' => $prop_ext_categoria_evento,
        ':prop_ext_categoria_evento_outro' => $prop_ext_categoria_evento_outro,
        ':prop_ext_inst_atendida' => $prop_ext_inst_atendida,
        ':prop_ext_atividades' => $prop_ext_atividades,
        ':prop_ext_datas_horas' => $prop_ext_datas_horas,
        ':prop_ext_mob_equipamento' => $prop_ext_mob_equipamento,
        ':prop_ext_dinamica' => $prop_ext_dinamica,
        ':prop_ext_quant_atendimento' => $prop_ext_quant_atendimento,
        ':prop_ext_forma_ingresso' => $prop_ext_forma_ingresso,
        ':prop_ext_valor_bolsa' => $prop_ext_valor_bolsa,
        ':prop_ext_atendimento_ofertado' => $prop_ext_atendimento_ofertado,
        ':prop_ext_impacto_social' => $prop_ext_impacto_social,
        ':prop_ext_obs' => $prop_ext_obs
      ]);

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                              VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'     => 'PROPOSTA - EXTENSÃO COMUNITÁRIA',
        ':acao'       => 'CADASTRO',
        ':acao_id'    => $prop_id,
        ':dados'      =>
        'Tipo de programa: ' . $prop_ext_tipo_programa .
          '; Categoria do evento: ' . $prop_ext_categoria_evento .
          '; Outra Categoria do evento: ' . $prop_ext_categoria_evento_outra .
          '; Quant. atendimento: ' . $prop_ext_quant_atendimento .
          '; Forma ingresso: ' . $prop_ext_forma_ingresso .
          '; Valor: ' . $prop_ext_valor_bolsa,
        ':user_id'    => $reservm_user_id
      ]);
      // -------------------------------

      // CADASTRA AS IMAGENS
      if (!empty($_FILES["arquivos"]["name"][0])) { // SE ALGUAMA IMAGEM FOR ENVIADA...

        $parq_categoria = 3; // CATEGORIA DE ARQUIVO - PROPOSTA = 3
        $arquivos       = $_FILES["arquivos"]; // RECEBE O(S) ARQUIVO(S)

        // 10MB
        $maxFileSize = 10 * 1024 * 1024;
        foreach ($_FILES["arquivos"]["name"] as $key => $fileName) { // CALCULA O TAMANHO DOS ARQUIVOS ENVIADOS
          if (!empty($fileName)) {
            $fileSize = $_FILES["arquivos"]["size"][$key];
            if ($fileSize > $maxFileSize) {
              $_SESSION["erro"] = "O arquivo excede o tamanho máximo permitido de 2MB.";
              $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
              header("Location: $referer#parq_ancora");
              return die;
            }
          }
        }
        // -------------------------------

        // IMPEDE QUE MAIS DE 20 ARQUIVOS SEJAM ENVIADOS
        $uploadedFilesCount = count($_FILES["arquivos"]["name"]);
        if ($uploadedFilesCount > 20) {
          $_SESSION["erro"] = "Você pode enviar no máximo 5 arquivos.";
          $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
          header("Location: $referer#parq_ancora");
          return die;
        }
        // -------------------------------

        // CRIA AS PASTAS DOS ARQUIVOS
        $pastaPrincipal = "../uploads/propostas/$prop_codigo";
        $SubPasta = "../uploads/propostas/$prop_codigo/$parq_categoria";
        // CRIA A PASTA PRINCIPAL SE NÃO EXISTIR
        if (!file_exists($pastaPrincipal)) {
          mkdir($pastaPrincipal, 0777, true);
        }
        // -------------------------------

        // CRIA A SUBPASTA
        if (!file_exists($SubPasta)) {
          mkdir($SubPasta, 0777, true);
        }
        // -------------------------------

        $nomes = $arquivos['name']; //CAPTURA NOMES DOS ARQUIVOS

        for ($i = 0; $i < count($nomes); $i++) {
          $extensao = explode('.', $nomes[$i]);
          $extensao = end($extensao);
          $nomes[$i] = rand() . '-' . $nomes[$i];

          $arquivos_permitidos = ['pdf', 'PDF', 'xlsx', 'xls', 'XLSX', 'XLS', 'doc', 'DOC', 'docx', 'DOCX', 'csv', 'CSV', 'jpg', 'JPG', 'jpeg', 'png', 'PNG', 'txt', 'TXT']; //FORMATO DE ARQUIVOS PERMITIDOS

          if (in_array($extensao, $arquivos_permitidos)) { // VERIFICAR EXTENSÃO DOS ARQUIVOS

            $sql = "INSERT INTO propostas_arq (
                                                parq_prop_id,
                                                parq_codigo,
                                                parq_categoria,
                                                parq_arquivo,
                                                parq_user_id,
                                                parq_data_cad
                                                ) VALUES (
                                                :parq_prop_id,
                                                :parq_codigo,
                                                :parq_categoria,
                                                :parq_arquivo,
                                                :parq_user_id,
                                                GETDATE()
                                                )";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
              ':parq_prop_id' => $prop_id,
              ':parq_codigo' => $prop_codigo,
              ':parq_categoria' => $parq_categoria,
              ':parq_arquivo' => $nomes[$i],
              ':parq_user_id' => $reservm_user_id
            ]);

            // REGISTRA AÇÃO NO LOG
            $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
            $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                                      VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
            $stmt->execute([
              ':modulo'     => 'PROPOSTA - CURSOS - ARQUIVOS',
              ':acao'       => 'CADASTRO',
              ':acao_id'    => $prop_id,
              ':dados'      => 'ID: ' . $last_id . '; Código: ' . $prop_codigo . '; Categoria: ' . $parq_categoria . '; Arquivo: ' . $nomes[$i],
              ':user_id'    => $reservm_user_id
            ]);
            // -------------------------------

            // MOVE AS IMAGENS PARA A PASTA
            $total_rowns = $stmt->rowCount();
            if ($total_rowns > 0) {
              $mover = move_uploaded_file($_FILES['arquivos']['tmp_name'][$i], '../uploads/propostas/' . $prop_codigo . '/' . $parq_categoria . '/' . $nomes[$i]);
            }
            // -------------------------------

          } else {
            $_SESSION["erro"] = "Formato de arquivo inválido!";
            $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
            header("Location: $referer#parq_ancora");
            return die;
          }
        }
      }

      // SE 'STATUS_ETAPA' FOR DIFERENTE DE 4, QUER DIZER QUE O STATUS AINDA NÃO FOI ATUALIZADO
      if (base64_decode($_POST['prop_status_etapa']) == 4) {

        // STATUS DA ANÁLISE
        $num_status  = 2; // 2 = CONCLUÍDO
        $sql = "INSERT INTO propostas_analise_status (
                                                      sta_an_prop_id,
                                                      sta_an_status,
                                                      sta_an_user_id,
                                                      sta_an_data_cad,
                                                      sta_an_data_upd
                                                      ) VALUES (
                                                      :sta_an_prop_id,
                                                      :sta_an_status,
                                                      :sta_an_user_id,
                                                      GETDATE(),
                                                      GETDATE()
                                                      )";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
          ':sta_an_prop_id' => $prop_id,
          ':sta_an_status' => $num_status,
          ':sta_an_user_id' => $reservm_user_id
        ]);
        // -------------------------------

        // ALTERA O STATUS DA PROPOSTA
        $sql = "UPDATE
                    propostas_status
                SET        
                    prop_sta_status   = :prop_sta_status,
                    prop_sta_user_id  = :prop_sta_user_id,
                    prop_sta_data_cad = GETDATE()
                WHERE
                    prop_sta_prop_id  = :prop_sta_prop_id";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
          ':prop_sta_prop_id' => $prop_id,
          ':prop_sta_status' => $num_status,
          ':prop_sta_user_id' => $reservm_user_id
        ]);
        // -------------------------------

        // ENVIA E-MAIL PARA USUÁRIO
        $mail = new PHPMailer(true);
        include '../controller/email_conf.php';
        $mail->addAddress($_SESSION['reservm_user_email'], 'RESERVM'); // E-MAIL DO USUÁRIO QUE RECEBERÁ A MENSAGEM
        $mail->isHTML(true);
        $mail->Subject = $prop_codigo . ' - Cadastro da proposta concluído'; // TÍTULO DO E-MAIL

        // CORPO DO EMAIL
        include '../includes/email/email_header.php';
        $email_conteudo .= "
          <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
            <td style='padding: 2em 2rem; display: inline-block; width: 100%'>
    
            <p style='font-size: 1.3rem; font-weight: 600; margin: 0px 0px 40px 0px;'>Cadastro da proposta concluído!</p>
    
            <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 10px 0px;'>Seu cadastro da proposta de código: <strong> $prop_codigo </strong> foi concluído. <br> A equipe de extensão irá analisar a sua proposta.</p>
    
            <a style='cursor: pointer;' href='$url_sistema'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 30px;' target='_blank'>Acesse o sistema</button></a>
            </td>
          </tr>";
        include '../includes/email/email_footer.php';

        $mail->Body  = $email_conteudo;
        $mail->send();
        // -------------------------------

        // ENVIA E-MAIL PARA ADMINISTRADOR
        $mail = new PHPMailer(true);
        include '../controller/email_conf.php';
        $mail->addAddress($email_extensao, 'RESERVM'); // E-MAIL DA EXTENSÃO
        $mail->isHTML(true);
        $mail->Subject = $prop_codigo . ' - Cadastro da proposta concluído'; //TÍTULO DO E-MAIL

        // CORPO DO EMAIL
        include '../includes/email/email_header.php';
        $email_conteudo .= "
          <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
            <td style='padding: 2em 2rem; display: inline-block; width: 100%'>
    
            <p style='font-size: 1.3rem; font-weight: 600; margin: 0px 0px 40px 0px;'>Cadastro da proposta concluído!</p>
    
            <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 10px 0px;'>O cadastro da proposta de código: <strong> $prop_codigo </strong> foi concluído. <br> A análise dos dados pode ser iniciada.</p>
    
            <a style='cursor: pointer;' href='$url_sistema/admin'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 30px;' target='_blank'>Acesse o sistema</button></a>
            </td>
          </tr>";
        include '../includes/email/email_footer.php';

        $mail->Body  = $email_conteudo;
        $mail->send();
      }
      // -------------------------------

      $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

      $_SESSION["msg"] = "O cadastro da proposta foi concluído!";
      header("Location: ../painel.php");
      //header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    } catch (PDOException $e) {
      //echo 'Error: ' . $e->getMessage();
      $conn->rollBack();
      $_SESSION["erro"] = "Cadastro não realizado!";
      $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
      header("Location: $referer#top_ancora");
      return die;
    }
  }
}
















/*****************************************************************************************
                                EXCLUIR ARQUIVOS
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_arq") {

  $parq_id      = $_GET['ident'];
  $parq_pasta   = $_GET['p'];
  $parq_codigo  = $_GET['c'];
  $parq_arquivo = $_GET['f'];
  $reservm_user_id = $_SESSION['reservm_user_id'];

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "DELETE FROM propostas_arq WHERE parq_id = :parq_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':parq_id' => $parq_id]);

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {

      $apaga_img = unlink("../uploads/propostas/$parq_codigo/$parq_pasta/$parq_arquivo"); //APAGA O ARQUIVO ANTIGO

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'    => 'PROPOSTA - ARQUIVOS',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $parq_id,
        ':user_id'   => $reservm_user_id
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









/*****************************************************************************************
                                EXCLUIR ARQUIVO PROPOSTA ETAPA 4
 *****************************************************************************************/
// if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_parq") {

//   $parq_id        = $_GET['parq_id'];
//   $parq_codigo    = $_GET['parq_codigo'];
//   $parq_categoria = $_GET['parq_categoria'];
//   $parq_arquivo   = $_GET['parq_arquivo'];

//   try {
//     $sql = "DELETE FROM propostas_arq WHERE parq_id = :parq_id";
//     $stmt = $conn->prepare($sql);
//     $stmt->bindParam(':parq_id', $parq_id);
//     $stmt->execute();

//     // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
//     if ($stmt->rowCount() > 0) {

//       $apaga_img = unlink("../uploads/propostas/$parq_codigo/$parq_categoria/$parq_arquivo"); //APAGA O ARQUIVO ANTIGO

//       // REGISTRA AÇÃO NO LOG
//       $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_acao_user_nome, log_data )
//                             VALUES ( :modulo, :acao, :acao_id, :user_id, :user_nome, :data )');
//       $stmt->execute(array(
//         ':modulo'    => 'PROPOSTA - ARQUIVOS',
//         ':acao'      => 'EXCLUSÃO',
//         ':acao_id'   => $parq_id,
//         ':user_id'   => $_SESSION['reservm_admin_id'],
//         ':user_nome' => $_SESSION['reservm_admin_nome'],
//         ':data'      => date('Y-m-d H:i:s')
//       ));
//       // -------------------------------

//       $_SESSION["msg"] = "Dados excluídos!";
//       $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
//       header("Location: $referer#parq_ancora_arq");
//     } else {
//       $_SESSION["erro"] = "Dados não excluídos!";
//       $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
//       header("Location: $referer#parq_ancora_arq");
//     }
//     // -------------------------------

//   } catch (PDOException $e) {
//     echo "Erro: " . $e->getMessage();
//   }
//   $conn = null;
// }
