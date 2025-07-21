<?php
session_start();
include '../conexao/conexao.php';

// NECESSÁRIO PARA ENVIAR O EMAIL
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
// -------------------------------

if ($_SERVER["REQUEST_METHOD"] === "GET") {

  $prop_id      = base64_decode($_GET['i']);
  $prop_tipo    = base64_decode($_GET['pt']);
  $novo_prop_id = substr(md5(uniqid()), 0, 32); // GERA NOVO ID 
  $prop_codigo  = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT); // GERA UM CÓDIGO COM 6 DÍGITOS 
  //$reservm_user_id = $_SESSION['reservm_admin_id'];
  $reservm_user_id = $_SESSION['reservm_user_id'];


  /*****************************************************************************************
                                    PROPOSTA STEP 1
   *****************************************************************************************/

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    // SELECIONA A TABELA A SER CLONADA CONFORME ID
    $sql_prop = "SELECT * FROM propostas WHERE prop_id = '$prop_id'";
    $stmt = $conn->prepare($sql_prop);
    $stmt->execute();
    $rows_prop = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($rows_prop) > 0) {
      foreach ($rows_prop as $prop) {

        $prop_titulo              = '(CÓPIA) - ' . $prop['prop_titulo'];
        $prop_status_etapa        = 0;
        $prop_parc_data_atividade = trim($_POST['prop_parc_data_atividade']) !== '' ? trim($_POST['prop_parc_data_atividade']) : NULL;

        $sql_insert_prop = "INSERT INTO propostas (
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
                                                    --prop_data_inicio,
                                                    --prop_data_fim,
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
                                                    prop_quant_beneficios,
                                                    prop_atendimento_doacao,
                                                    prop_desc_beneficios,
                                                    prop_comunidade,
                                                    prop_localidade,
                                                    prop_responsavel,
                                                    prop_responsavel_contato,
                                                    prop_info_complementar,
                                                    prop_custos,
                                                    prop_recursos,
                                                    prop_desc_atividade,
                                                    prop_rec_audio_video,
                                                    prop_outros,
                                                    prop_card,
                                                    prop_texto_divulgacao,
                                                    prop_diferenciais,
                                                    prop_brienfing,
                                                    prop_parceria,
                                                    prop_informacoes,
                                                    prop_event_patrocinio,
                                                    prop_event_qual_patrocinio,
                                                    prop_event_parceria,
                                                    prop_event_qual_parceria,
                                                    prop_event_contatos,
                                                    prop_event_sorteio,
                                                    prop_prog_tipo,
                                                    prop_prog_categoria,
                                                    prop_prog_valor_inscricao,
                                                    prop_prog_docente,
                                                    prop_prog_area_atuacao,
                                                    prop_prog_local_atuacao,
                                                    prop_prog_data_inicio,
                                                    prop_prog_data_fim,
                                                    prop_prog_obs,
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
                                                    prop_ext_tipo_programa,
                                                    prop_ext_categoria_evento,
                                                    prop_ext_categoria_evento_outro,
                                                    prop_ext_inst_atendida,
                                                    prop_ext_atividades,
                                                    prop_ext_datas_horas,
                                                    prop_ext_mob_equipamento,
                                                    prop_ext_dinamica,
                                                    prop_ext_quant_atendimento,
                                                    prop_ext_forma_ingresso,
                                                    prop_ext_valor_bolsa,
                                                    prop_ext_atendimento_ofertado,
                                                    prop_ext_impacto_social,
                                                    prop_ext_obs,
                                                    prop_user_id,
                                                    prop_data_cad,
                                                    prop_data_upd
                                                  ) VALUES (
                                                    :prop_id,
                                                    :prop_tipo,
                                                    :prop_codigo,
                                                    :prop_status_etapa,
                                                    :prop_titulo,
                                                    :prop_descricao,
                                                    :prop_vinculo_programa,
                                                    :prop_qual_vinculo_programa,
                                                    :prop_curso_vinculo,
                                                    :prop_nome_curso_vinculo,
                                                    :prop_justificativa,
                                                    :prop_obj_pedagogico,
                                                    :prop_publico_alvo,
                                                    :prop_area_conhecimento,
                                                    :prop_area_tematica,
                                                    :prop_semana,
                                                    :prop_horario,
                                                    --:prop_data_inicio,
                                                    --:prop_data_fim,
                                                    :prop_carga_hora,
                                                    :prop_total_vaga,
                                                    :prop_quant_turma,
                                                    :prop_forma_acesso,
                                                    :prop_outra_forma_acesso,
                                                    :prop_modalidade,
                                                    :prop_campus,
                                                    :prop_local,
                                                    :prop_preco,
                                                    :prop_preco_parcelas,
                                                    :prop_acao_acessibilidade,
                                                    :prop_desc_acao_acessibilidade,
                                                    :prop_ofertas_vagas,
                                                    :prop_quant_beneficios,
                                                    :prop_atendimento_doacao,
                                                    :prop_desc_beneficios,
                                                    :prop_comunidade,
                                                    :prop_localidade,
                                                    :prop_responsavel,
                                                    :prop_responsavel_contato,
                                                    :prop_info_complementar,
                                                    :prop_custos,
                                                    :prop_recursos,
                                                    :prop_desc_atividade,
                                                    :prop_rec_audio_video,
                                                    :prop_outros,
                                                    :prop_card,
                                                    :prop_texto_divulgacao,
                                                    :prop_diferenciais,
                                                    :prop_brienfing,
                                                    :prop_parceria,
                                                    :prop_informacoes,
                                                    :prop_event_patrocinio,
                                                    :prop_event_qual_patrocinio,
                                                    :prop_event_parceria,
                                                    :prop_event_qual_parceria,
                                                    :prop_event_contatos,
                                                    :prop_event_sorteio,
                                                    :prop_prog_tipo,
                                                    :prop_prog_categoria,
                                                    :prop_prog_valor_inscricao,
                                                    :prop_prog_docente,
                                                    :prop_prog_area_atuacao,
                                                    :prop_prog_local_atuacao,
                                                    :prop_prog_data_inicio,
                                                    :prop_prog_data_fim,
                                                    :prop_prog_obs,
                                                    :prop_parc_nome_empresa,
                                                    :prop_parc_tipo_empresa,
                                                    :prop_parc_tipo_outro,
                                                    :prop_parc_orgao_empresa,
                                                    :prop_parc_email,
                                                    :prop_parc_telefone,
                                                    :prop_parc_cep,
                                                    :prop_parc_logradouro,
                                                    :prop_parc_numero,
                                                    :prop_parc_bairro,
                                                    :prop_parc_municipio,
                                                    :prop_parc_estado,
                                                    :prop_parc_pais,
                                                    :prop_parc_responsavel,
                                                    :prop_parc_cargo,
                                                    :prop_parc_contato_referencia,
                                                    :prop_parc_possui_convenio,
                                                    :prop_parc_tipo_parceria,
                                                    :prop_parc_titulo_atividade,
                                                    :prop_parc_objetivo_atividade,
                                                    :prop_parc_local_atividade,
                                                    :prop_parc_tipo_espaco,
                                                    :prop_parc_campus_atividade,
                                                    :prop_parc_carga_hora,
                                                    :prop_parc_data_atividade,
                                                    :prop_parc_hora_atividade_inicial,
                                                    :prop_parc_hora_atividade_final,
                                                    :prop_parc_numero_participantes,
                                                    :prop_parc_recursos_necessarios,
                                                    :prop_parc_beneficios,
                                                    :prop_parc_beneficios_quantidade,
                                                    :prop_parc_organizacao_espaco,
                                                    :prop_parc_comentarios,
                                                    :prop_ext_tipo_programa,
                                                    :prop_ext_categoria_evento,
                                                    :prop_ext_categoria_evento_outro,
                                                    :prop_ext_inst_atendida,
                                                    :prop_ext_atividades,
                                                    :prop_ext_datas_horas,
                                                    :prop_ext_mob_equipamento,
                                                    :prop_ext_dinamica,
                                                    :prop_ext_quant_atendimento,
                                                    :prop_ext_forma_ingresso,
                                                    :prop_ext_valor_bolsa,
                                                    :prop_ext_atendimento_ofertado,
                                                    :prop_ext_impacto_social,
                                                    :prop_ext_obs,
                                                    :prop_user_id,
                                                    GETDATE(),
                                                    GETDATE()
                                                  )";

        $stmt_insert = $conn->prepare($sql_insert_prop);
        $stmt_insert->execute([
          ':prop_id' => $novo_prop_id,
          ':prop_tipo' => $prop['prop_tipo'],
          ':prop_codigo' => $prop_codigo,
          ':prop_status_etapa' => $prop_status_etapa,
          ':prop_titulo' => $prop_titulo,
          ':prop_descricao' => $prop['prop_descricao'],
          ':prop_vinculo_programa' => $prop['prop_vinculo_programa'],
          ':prop_qual_vinculo_programa' => $prop['prop_qual_vinculo_programa'],
          ':prop_curso_vinculo' => $prop['prop_curso_vinculo'],
          ':prop_nome_curso_vinculo' => $prop['prop_nome_curso_vinculo'],
          ':prop_justificativa' => $prop['prop_justificativa'],
          ':prop_obj_pedagogico' => $prop['prop_obj_pedagogico'],
          ':prop_publico_alvo' => $prop['prop_publico_alvo'],
          ':prop_area_conhecimento' => $prop['prop_area_conhecimento'],
          ':prop_area_tematica' => $prop['prop_area_tematica'],
          ':prop_semana' => $prop['prop_semana'],
          ':prop_horario' => $prop['prop_horario'],
          //$stmt_insert->bindParam(':prop_data_inicio', $prop['prop_data_inicio']);
          //$stmt_insert->bindParam(':prop_data_fim', $prop['prop_data_fim']);
          ':prop_carga_hora' => $prop['prop_carga_hora'],
          ':prop_total_vaga' => $prop['prop_total_vaga'],
          ':prop_quant_turma' => $prop['prop_quant_turma'],
          ':prop_forma_acesso' => $prop['prop_forma_acesso'],
          ':prop_outra_forma_acesso' => $prop['prop_outra_forma_acesso'],
          ':prop_modalidade' => $prop['prop_modalidade'],
          ':prop_campus' => $prop['prop_campus'],
          ':prop_local' => $prop['prop_local'],
          ':prop_preco' => $prop['prop_preco'],
          ':prop_preco_parcelas' => $prop['prop_preco_parcelas'],
          ':prop_acao_acessibilidade' => $prop['prop_acao_acessibilidade'],
          ':prop_desc_acao_acessibilidade' => $prop['prop_desc_acao_acessibilidade'],
          ':prop_ofertas_vagas' => $prop['prop_ofertas_vagas'],
          ':prop_quant_beneficios' => $prop['prop_quant_beneficios'],
          ':prop_atendimento_doacao' => $prop['prop_atendimento_doacao'],
          ':prop_desc_beneficios' => $prop['prop_desc_beneficios'],
          ':prop_comunidade' => $prop['prop_comunidade'],
          ':prop_localidade' => $prop['prop_localidade'],
          ':prop_responsavel' => $prop['prop_responsavel'],
          ':prop_responsavel_contato' => $prop['prop_responsavel_contato'],
          ':prop_info_complementar' => $prop['prop_info_complementar'],
          ':prop_custos' => $prop['prop_custos'],
          ':prop_recursos' => $prop['prop_recursos'],
          ':prop_desc_atividade' => $prop['prop_desc_atividade'],
          ':prop_rec_audio_video' => $prop['prop_rec_audio_video'],
          ':prop_outros' => $prop['prop_outros'],
          ':prop_card' => $prop['prop_card'],
          ':prop_texto_divulgacao' => $prop['prop_texto_divulgacao'],
          ':prop_diferenciais' => $prop['prop_diferenciais'],
          ':prop_brienfing' => $prop['prop_brienfing'],
          ':prop_parceria' => $prop['prop_parceria'],
          ':prop_informacoes' => $prop['prop_informacoes'],
          ':prop_event_patrocinio' => $prop['prop_event_patrocinio'],
          ':prop_event_qual_patrocinio' => $prop['prop_event_qual_patrocinio'],
          ':prop_event_parceria' => $prop['prop_event_parceria'],
          ':prop_event_qual_parceria' => $prop['prop_event_qual_parceria'],
          ':prop_event_contatos' => $prop['prop_event_contatos'],
          ':prop_event_sorteio' => $prop['prop_event_sorteio'],
          ':prop_prog_tipo' => $prop['prop_prog_tipo'],
          ':prop_prog_categoria' => $prop['prop_prog_categoria'],
          ':prop_prog_valor_inscricao' => $prop['prop_prog_valor_inscricao'],
          ':prop_prog_docente' => $prop['prop_prog_docente'],
          ':prop_prog_area_atuacao' => $prop['prop_prog_area_atuacao'],
          ':prop_prog_local_atuacao' => $prop['prop_prog_local_atuacao'],
          ':prop_prog_data_inicio' => $prop['prop_prog_data_inicio'],
          ':prop_prog_data_fim' => $prop['prop_prog_data_fim'],
          ':prop_prog_obs' => $prop['prop_prog_obs'],
          ':prop_parc_nome_empresa' => $prop['prop_parc_nome_empresa'],
          ':prop_parc_tipo_empresa' => $prop['prop_parc_tipo_empresa'],
          ':prop_parc_tipo_outro' => $prop['prop_parc_tipo_outro'],
          ':prop_parc_orgao_empresa' => $prop['prop_parc_orgao_empresa'],
          ':prop_parc_email' => $prop['prop_parc_email'],
          ':prop_parc_telefone' => $prop['prop_parc_telefone'],
          ':prop_parc_cep' => $prop['prop_parc_cep'],
          ':prop_parc_logradouro' => $prop['prop_parc_logradouro'],
          ':prop_parc_numero' => $prop['prop_parc_numero'],
          ':prop_parc_bairro' => $prop['prop_parc_bairro'],
          ':prop_parc_municipio' => $prop['prop_parc_municipio'],
          ':prop_parc_estado' => $prop['prop_parc_estado'],
          ':prop_parc_pais' => $prop['prop_parc_pais'],
          ':prop_parc_responsavel' => $prop['prop_parc_responsavel'],
          ':prop_parc_cargo' => $prop['prop_parc_cargo'],
          ':prop_parc_contato_referencia' => $prop['prop_parc_contato_referencia'],
          ':prop_parc_possui_convenio' => $prop['prop_parc_possui_convenio'],
          ':prop_parc_tipo_parceria' => $prop['prop_parc_tipo_parceria'],
          ':prop_parc_titulo_atividade' => $prop['prop_parc_titulo_atividade'],
          ':prop_parc_objetivo_atividade' => $prop['prop_parc_objetivo_atividade'],
          ':prop_parc_local_atividade' => $prop['prop_parc_local_atividade'],
          ':prop_parc_tipo_espaco' => $prop['prop_parc_tipo_espaco'],
          ':prop_parc_campus_atividade' => $prop['prop_parc_campus_atividade'],
          ':prop_parc_carga_hora' => $prop['prop_parc_carga_hora'],
          ':prop_parc_data_atividade' => $prop_parc_data_atividade,
          ':prop_parc_hora_atividade_inicial' => $prop['prop_parc_hora_atividade_inicial'],
          ':prop_parc_hora_atividade_final' => $prop['prop_parc_hora_atividade_final'],
          ':prop_parc_numero_participantes' => $prop['prop_parc_numero_participantes'],
          ':prop_parc_recursos_necessarios' => $prop['prop_parc_recursos_necessarios'],
          ':prop_parc_beneficios' => $prop['prop_parc_beneficios'],
          ':prop_parc_beneficios_quantidade' => $prop['prop_parc_beneficios_quantidade'],
          ':prop_parc_organizacao_espaco' => $prop['prop_parc_organizacao_espaco'],
          ':prop_parc_comentarios' => $prop['prop_parc_comentarios'],
          ':prop_ext_tipo_programa' => $prop['prop_ext_tipo_programa'],
          ':prop_ext_categoria_evento' => $prop['prop_ext_categoria_evento'],
          ':prop_ext_categoria_evento_outro' => $prop['prop_ext_categoria_evento_outro'],
          ':prop_ext_inst_atendida' => $prop['prop_ext_inst_atendida'],
          ':prop_ext_atividades' => $prop['prop_ext_atividades'],
          ':prop_ext_datas_horas' => $prop['prop_ext_datas_horas'],
          ':prop_ext_mob_equipamento' => $prop['prop_ext_mob_equipamento'],
          ':prop_ext_dinamica' => $prop['prop_ext_dinamica'],
          ':prop_ext_quant_atendimento' => $prop['prop_ext_quant_atendimento'],
          ':prop_ext_forma_ingresso' => $prop['prop_ext_forma_ingresso'],
          ':prop_ext_valor_bolsa' => $prop['prop_ext_valor_bolsa'],
          ':prop_ext_atendimento_ofertado' => $prop['prop_ext_atendimento_ofertado'],
          ':prop_ext_impacto_social' => $prop['prop_ext_impacto_social'],
          ':prop_ext_obs' => $prop['prop_ext_obs'],
          ':prop_user_id' => $reservm_user_id
        ]);
      }
    }


    /*****************************************************************************************
                                PROPOSTA - COORDENADOR PROJETO
     *****************************************************************************************/

    // SELECIONA A TABELA A SER CLONADA CONFORME ID
    $sql_select_pcp = "SELECT * FROM propostas_coordenador_projeto WHERE pcp_proposta_id = '$prop_id'";
    $stmt = $conn->prepare($sql_select_pcp);
    $stmt->execute();
    $rows_pcp = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($rows_pcp) > 0) {
      foreach ($rows_pcp as $pcp) {

        $novo_pcp_id = substr(md5(uniqid()), 0, 32); // GERA NOVO ID 

        $sql_insert_pcp = "INSERT INTO propostas_coordenador_projeto (
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
                                                                      :pcp_nome,
                                                                      :pcp_email,
                                                                      :pcp_contato,
                                                                      :pcp_partic_perfil,
                                                                      :pcp_outro_partic_perfil,
                                                                      :pcp_carga_hora,
                                                                      :pcp_area_atuacao,
                                                                      :pcp_nome_area_atuacao,
                                                                      :pcp_formacao,
                                                                      :pcp_lattes,
                                                                      :pcp_user_id,
                                                                      GETDATE(),
                                                                      GETDATE()
                                                                    )";
        $stmt_insert = $conn->prepare($sql_insert_pcp);
        $stmt_insert->execute([
          ':pcp_id' => $novo_pcp_id,
          ':pcp_proposta_id' => $novo_prop_id,
          ':pcp_nome' => $pcp['pcp_nome'],
          ':pcp_email' => $pcp['pcp_email'],
          ':pcp_contato' => $pcp['pcp_contato'],
          ':pcp_partic_perfil' => $pcp['pcp_partic_perfil'],
          ':pcp_outro_partic_perfil' => $pcp['pcp_outro_partic_perfil'],
          ':pcp_carga_hora' => $pcp['pcp_carga_hora'],
          ':pcp_area_atuacao' => $pcp['pcp_area_atuacao'],
          ':pcp_nome_area_atuacao' => $pcp['pcp_nome_area_atuacao'],
          ':pcp_formacao' => $pcp['pcp_formacao'],
          ':pcp_lattes' => $pcp['pcp_lattes'],
          ':pcp_user_id' => $reservm_user_id
        ]);
      }
    }











    /*****************************************************************************************
                                PROPOSTA - EQUIPE EXECUTORA
     *****************************************************************************************/

    // SELECIONA A TABELA A SER CLONADA CONFORME ID
    $sql_select_pex = "SELECT * FROM propostas_equipe_executora WHERE pex_proposta_id = '$prop_id'";
    $stmt = $conn->prepare($sql_select_pex);
    $stmt->execute();
    $rows_pex = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($rows_pex) > 0) {
      foreach ($rows_pex as $pex) {

        $novo_pex_id = substr(md5(uniqid()), 0, 32); // GERA NOVO ID 

        $sql_insert_pex = "INSERT INTO propostas_equipe_executora (
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
                                                                    :pex_nome,
                                                                    :pex_email,
                                                                    :pex_contato,
                                                                    :pex_partic_categ,
                                                                    :pex_qual_partic_categ,
                                                                    :pex_partic_perfil,
                                                                    :pex_outro_partic_perfil,
                                                                    :pex_carga_hora,
                                                                    :pex_area_atuacao,
                                                                    :pex_nome_area_atuacao,
                                                                    :pex_formacao,
                                                                    :pex_lattes,
                                                                    :pex_user_id,
                                                                    GETDATE(),
                                                                    GETDATE()
                                                                  )";

        $stmt_insert = $conn->prepare($sql_insert_pex);
        $stmt_insert->execute([
          ':pex_id' => $novo_pex_id,
          ':pex_proposta_id' => $novo_prop_id,
          ':pex_nome' => $pex['pex_nome'],
          ':pex_email' => $pex['pex_email'],
          ':pex_contato' => $pex['pex_contato'],
          ':pex_partic_categ' => $pex['pex_partic_categ'],
          ':pex_qual_partic_categ' => $pex['pex_qual_partic_categ'],
          ':pex_partic_perfil' => $pex['pex_partic_perfil'],
          ':pex_outro_partic_perfil' => $pex['pex_outro_partic_perfil'],
          ':pex_carga_hora' => $pex['pex_carga_hora'],
          ':pex_area_atuacao' => $pex['pex_area_atuacao'],
          ':pex_nome_area_atuacao' => $pex['pex_nome_area_atuacao'],
          ':pex_formacao' => $pex['pex_formacao'],
          ':pex_lattes' => $pex['pex_lattes'],
          ':pex_user_id' => $reservm_user_id
        ]);
      }
    }









    /*****************************************************************************************
                        PROPOSTA - PARCEIROS EXTERNOS / PATROCINADORES
     *****************************************************************************************/

    // SELECIONA A TABELA A SER CLONADA CONFORME ID
    $sql_select_ppe = "SELECT * FROM propostas_parceiro_externo WHERE ppe_proposta_id = '$prop_id'";
    $stmt = $conn->prepare($sql_select_ppe);
    $stmt->execute();
    $rows_ppe = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($rows_ppe) > 0) {
      foreach ($rows_ppe as $ppe) {

        $novo_ppe_id = substr(md5(uniqid()), 0, 32); // GERA NOVO ID 

        $sql_insert_ppe = "INSERT INTO propostas_parceiro_externo (
                                                                    ppe_id,
                                                                    ppe_proposta_id,
                                                                    ppe_nome,
                                                                    ppe_email,
                                                                    ppe_contato,
                                                                    ppe_cnpj,
                                                                    ppe_responsavel,
                                                                    ppe_area_atuacao,
                                                                    ppe_obs,
                                                                    ppe_convenio,
                                                                    ppe_user_id,
                                                                    ppe_data_cad,
                                                                    ppe_data_upd
                                                                  ) VALUES (
                                                                    :ppe_id,
                                                                    :ppe_proposta_id,
                                                                    :ppe_nome,
                                                                    :ppe_email,
                                                                    :ppe_contato,
                                                                    :ppe_cnpj,
                                                                    :ppe_responsavel,
                                                                    :ppe_area_atuacao,
                                                                    :ppe_obs,
                                                                    :ppe_convenio,
                                                                    :ppe_user_id,
                                                                    GETDATE(),
                                                                    GETDATE()
                                                                  )";
        $stmt_insert = $conn->prepare($sql_insert_ppe);
        $stmt_insert->execute([
          ':ppe_id' => $novo_ppe_id,
          ':ppe_proposta_id' => $novo_prop_id,
          ':ppe_nome' => $ppe['ppe_nome'],
          ':ppe_email' => $ppe['ppe_email'],
          ':ppe_contato' => $ppe['ppe_contato'],
          ':ppe_cnpj' => $ppe['ppe_cnpj'],
          ':ppe_responsavel' => $ppe['ppe_responsavel'],
          ':ppe_area_atuacao' => $ppe['ppe_area_atuacao'],
          ':ppe_obs' => $ppe['ppe_obs'],
          ':ppe_convenio' => $ppe['ppe_convenio'],
          ':ppe_user_id' => $reservm_user_id
        ]);
      }
    }










    /*****************************************************************************************
                        PROPOSTA - MATERIAIS DE CONSUMO / LABORATÓRIO
     *****************************************************************************************/

    // SELECIONA A TABELA A SER CLONADA CONFORME ID
    $sql_select_pmc = "SELECT * FROM propostas_material_consumo WHERE pmc_proposta_id = '$prop_id'";
    $stmt = $conn->prepare($sql_select_pmc);
    $stmt->execute();
    $rows_pmc = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($rows_pmc) > 0) {
      foreach ($rows_pmc as $pmc) {

        $novo_pmc_id = substr(md5(uniqid()), 0, 32); // GERA NOVO ID 

        $sql_insert_pmc = "INSERT INTO propostas_material_consumo (
                                                                    pmc_id,
                                                                    pmc_proposta_id,
                                                                    pmc_material_consumo,
                                                                    pmc_quantidade,
                                                                    pmc_obs,
                                                                    pmc_user_id,
                                                                    pmc_data_cad,
                                                                    pmc_data_upd
                                                                  ) VALUES (
                                                                    :pmc_id,
                                                                    :pmc_proposta_id,
                                                                    UPPER(:pmc_material_consumo),
                                                                    :pmc_quantidade,
                                                                    :pmc_obs,
                                                                    :pmc_user_id,
                                                                    GETDATE(),
                                                                    GETDATE()
                                                                  )";

        $stmt_insert = $conn->prepare($sql_insert_pmc);
        $stmt_insert->execute([
          ':pmc_id' => $novo_pmc_id,
          ':pmc_proposta_id' => $novo_prop_id,
          ':pmc_material_consumo' => $pmc['pmc_material_consumo'],
          ':pmc_quantidade' => $pmc['pmc_quantidade'],
          ':pmc_obs' => $pmc['pmc_obs'],
          ':pmc_user_id' => $reservm_user_id
        ]);
      }
    }













    /*****************************************************************************************
                                  PROPOSTA - SERVIÇOS
     *****************************************************************************************/

    // SELECIONA A TABELA A SER CLONADA CONFORME ID
    $sql_select_ps = "SELECT * FROM propostas_servico WHERE ps_proposta_id = '$prop_id'";
    $stmt = $conn->prepare($sql_select_ps);
    $stmt->execute();
    $rows_ps = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($rows_ps) > 0) {
      foreach ($rows_ps as $ps) {

        $novo_ps_id = substr(md5(uniqid()), 0, 32); // GERA NOVO ID 

        $sql_insert_ps = "INSERT INTO propostas_servico (
                                                          ps_id,
                                                          ps_proposta_id,
                                                          ps_mat_serv_id,
                                                          ps_quantidade,
                                                          ps_obs,
                                                          ps_user_id,
                                                          ps_data_cad,
                                                          ps_data_upd
                                                          ) VALUES (
                                                          :ps_id,
                                                          :ps_proposta_id,
                                                          :ps_mat_serv_id,
                                                          :ps_quantidade,
                                                          :ps_obs,
                                                          :ps_user_id,
                                                          GETDATE(),
                                                          GETDATE()
                                                          )";

        $stmt_insert = $conn->prepare($sql_insert_ps);
        $stmt_insert->execute([
          ':ps_id' => $novo_ps_id,
          ':ps_proposta_id' => $novo_prop_id,
          ':ps_mat_serv_id' => $ps['ps_mat_serv_id'],
          ':ps_quantidade' => $ps['ps_quantidade'],
          ':ps_obs' => $ps['ps_obs'],
          ':ps_user_id' => $reservm_user_id
        ]);
      }
    }












    /*****************************************************************************************
                                    PROPOSTA STEP 3 - ARQUIVOS
     *****************************************************************************************/

    // SELECIONA A TABELA A SER CLONADA CONFORME ID
    $sql_select_parq = "SELECT * FROM propostas_arq WHERE parq_prop_id = '$prop_id'";
    $stmt = $conn->prepare($sql_select_parq);
    $stmt->execute();
    $rows_parq = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($rows_parq) > 0) {
      foreach ($rows_parq as $parq) {

        $sql_insert_parq = "INSERT INTO propostas_arq (
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

        $stmt_insert = $conn->prepare($sql_insert_parq);
        $stmt_insert->execute([
          ':parq_prop_id' => $novo_prop_id,
          ':parq_codigo' => $prop_codigo,
          ':parq_categoria' => $parq['parq_categoria'],
          ':parq_arquivo' => $parq['parq_arquivo'],
          ':parq_user_id' => $reservm_user_id
        ]);
      }

      // CLONA A PASTA COM OS ARQUIVOS DA PROPOSTA
      $origem = "../uploads/propostas/" . $parq['parq_codigo'];
      $destino = "../uploads/propostas/" . $prop_codigo;

      function clonarPasta($origem, $destino)
      {
        // VERIFICA SE A PASTA DE ORIGEM EXISTE
        if (!is_dir($origem)) {
          return false;
        }

        // CRIA A PASTA DE DESTINO SE ELA NÃO EXISTIR
        if (!is_dir($destino)) {
          mkdir($destino, 0777, true);
        }

        $items = scandir($origem);
        foreach ($items as $item) {
          if ($item == "." || $item == "..") {
            continue; // Ignora as pastas . e ..
          }

          $origemItem = $origem . '/' . $item;
          $destinoItem = $destino . '/' . $item;

          if (is_dir($origemItem)) {
            // Se for um diretório, chame a função recursiva
            clonarPasta($origemItem, $destinoItem);
          } else {
            // Se for um arquivo, copie-o para o destino
            copy($origemItem, $destinoItem);
          }
        }
      }
      clonarPasta($origem, $destino);
    }







    /*****************************************************************************************
                                    PROPOSTA CURSOS MÓDULO
     *****************************************************************************************/
    if ($prop_tipo == 1) {

      // SELECIONA A TABELA A SER CLONADA CONFORME ID
      $sql_select_prop_cmod = "SELECT * FROM propostas_cursos_modulo WHERE prop_cmod_prop_id = '$prop_id'";
      $stmt = $conn->prepare($sql_select_prop_cmod);
      $stmt->execute();
      $rows_prop_cmod = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if (count($rows_prop_cmod) > 0) {
        foreach ($rows_prop_cmod as $prop_cmod) {

          $novo_prop_cmod_id = substr(md5(uniqid()), 0, 32); // GERA NOVO ID 

          $sql_insert_prop_cmod = "INSERT INTO propostas_cursos_modulo (
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
                                                                        :prop_cmod_nome_docente,
                                                                        :prop_cmod_titulo,
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

          $stmt_insert = $conn->prepare($sql_insert_prop_cmod);
          $stmt_insert->execute([
            ':prop_cmod_id' => $novo_prop_cmod_id,
            ':prop_cmod_prop_id' => $novo_prop_id,
            ':prop_cmod_tipo_docente' => $prop_cmod['prop_cmod_tipo_docente'],
            ':prop_cmod_nome_docente' => $prop_cmod['prop_cmod_nome_docente'],
            ':prop_cmod_titulo' => $prop_cmod['prop_cmod_titulo'],
            ':prop_cmod_assunto' => $prop_cmod['prop_cmod_assunto'],
            ':prop_cmod_data_hora' => $prop_cmod['prop_cmod_data_hora'],
            ':prop_cmod_organizacao' => $prop_cmod['prop_cmod_organizacao'],
            ':prop_cmod_outra_organizacao' => $prop_cmod['prop_cmod_outra_organizacao'],
            ':prop_cmod_forma_pagamento' => $prop_cmod['prop_cmod_forma_pagamento'],
            ':prop_cmod_curriculo' => $prop_cmod['prop_cmod_curriculo'],
            ':prop_cmod_user_id' => $reservm_user_id
          ]);
        }
      }
    }







    /*****************************************************************************************
                            PROPOSTA EXTENSÃO COMUNITÁRIA - CONTATOS
     *****************************************************************************************/
    if ($prop_tipo == 5) {

      // SELECIONA A TABELA A SER CLONADA CONFORME ID
      $sql_select_prc = "SELECT * FROM propostas_extensao_responsavel_contato WHERE prc_proposta_id = '$prop_id'";
      $stmt = $conn->prepare($sql_select_prc);
      $stmt->execute();
      $rows_prc = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if (count($rows_prc) > 0) {
        foreach ($rows_prc as $prc) {

          $novo_prc_id = substr(md5(uniqid()), 0, 32); // GERA NOVO ID 

          $sql_insert_prc = "INSERT INTO propostas_extensao_responsavel_contato (
                                                                                  prc_id,
                                                                                  prc_proposta_id,
                                                                                  prc_nome,
                                                                                  prc_contato,
                                                                                  prc_email,
                                                                                  prc_user_id,
                                                                                  prc_data_cad,
                                                                                  prc_data_upd
                                                                                ) VALUES (
                                                                                  :prc_id,
                                                                                  :prc_proposta_id,
                                                                                  :prc_nome,
                                                                                  :prc_contato,
                                                                                  :prc_email,
                                                                                  :prc_user_id,
                                                                                  GETDATE(),
                                                                                  GETDATE()
                                                                                )";

          $stmt_insert = $conn->prepare($sql_insert_prc);
          $stmt_insert->execute([
            ':prc_id' => $novo_prc_id,
            ':prc_proposta_id' => $novo_prop_id,
            ':prc_nome' => $prc['prc_nome'],
            ':prc_contato' => $prc['prc_contato'],
            ':prc_email' => $prc['prc_email'],
            ':prc_user_id' => $reservm_user_id
          ]);
        }
      }
    }




    /*****************************************************************************************
                                          STATUS DA ANÁLISE
     *****************************************************************************************/
    $num_status  = 1; // CADASTRO PENDENTE
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
      ':sta_an_prop_id' => $novo_prop_id,
      ':sta_an_status' => $num_status,
      ':sta_an_user_id' => $reservm_user_id
    ]);
    // -------------------------------



    /*****************************************************************************************
                                  STATUS DA PROPOSTA
     *****************************************************************************************/
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
      ':prop_sta_prop_id' => $novo_prop_id, // ID DA PROPOSTA
      ':prop_sta_status' => $num_status,
      ':prop_sta_user_id' => $reservm_user_id
    ]);
    // -------------------------------


    // ENVIA E-MAIL PARA USUÁRIO
    $mail = new PHPMailer(true);
    include '../controller/email_conf.php';
    $mail->addAddress($_SESSION['reservm_user_email'], 'RESERVM'); // E-MAIL DO USUÁRIO QUE RECEBERÁ A MENSAGEM
    $mail->isHTML(true);
    $mail->Subject = $prop_codigo . ' - Cadastro de Proposta duplicado'; //TÍTULO DO E-MAIL

    // CORPO DO EMAIL
    include '../includes/email/email_header.php';
    $email_conteudo .= "
        <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
          <td style='padding: 2em 2rem; display: inline-block; width: 100%'>

          <p style='font-size: 1.3rem; font-weight: 600; margin: 0px 0px 40px 0px;'>Cadastro de proposta duplicado!</p>

          <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 10px 0px;'>O novo código da proposta é: <strong> $prop_codigo </strong>. <br> Atualize o cadastro para que nossa equipe inicie a análise da proposta.</p>

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
    $mail->addAddress($email_extensao); // E-MAIL DA EXTENSÃO
    $mail->isHTML(true);
    $mail->Subject = $prop_codigo . ' - Cadastro de Proposta duplicado'; //TÍTULO DO E-MAIL

    // CORPO DO EMAIL
    include '../includes/email/email_header.php';
    $email_conteudo .= "
        <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
          <td style='padding: 2em 2rem; display: inline-block; width: 100%'>

          <p style='font-size: 1.3rem; font-weight: 600; margin: 0px 0px 40px 0px;'>Cadastro de proposta duplicado!</p>

          <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 10px 0px;'>Um usuário duplicou o cadastro de uma proposta.<br>O novo código da proposta é: <strong> $prop_codigo </strong>.</p>

          <a style='cursor: pointer;' href='$url_sistema/admin'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 30px;' target='_blank'>Acesse o sistema</button></a>
          </td>
        </tr>";
    include '../includes/email/email_footer.php';

    $mail->Body  = $email_conteudo;
    $mail->send();
    // -------------------------------

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES (:modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'    => 'PROPOSTA',
      ':acao'      => 'CLONE',
      ':acao_id'   => $novo_prop_id,
      ':dados'     =>
      'Tipo: ' . $prop['prop_tipo'] .
        '; Código: ' . $prop_codigo .
        '; Título: ' . $prop['prop_titulo'],
      ':user_id'   => $reservm_user_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "A proposta foi duplicada com sucesso!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    // echo "Erro na conexão com o banco de dados: " . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Erro ao tentar clonar os dados!" . $e->getMessage();
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
} // FIM REQUEST_METHOD