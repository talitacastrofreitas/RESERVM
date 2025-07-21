<?php
session_start();
include '../conexao/conexao.php';

// NECESSÁRIO PARA ENVIAR O EMAIL
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
// -------------------------------

if ($_SERVER["REQUEST_METHOD"] === "GET") {

  $prop_id      = $_GET['prop_id'];
  $prop_tipo    = $_GET['prop_tipo'];
  $novo_prop_id = substr(md5(uniqid()), 0, 32); // GERA NOVO ID 
  // GERA NOVO CÓDIGO
  function generateUniqueNumericCode($length = 6)
  {
    $uniqueString = uniqid();
    $numericString = preg_replace('/[^0-9]/', '', $uniqueString);
    $code = substr($numericString, 0, $length);
    return $code;
  }
  $prop_codigo = generateUniqueNumericCode();
  //
  $prop_user_id = $_SESSION['reservm_admin_id'];
  $data_real    = date('Y-m-d H:i:s');





  /*****************************************************************************************
                                    PROPOSTA STEP 1
   *****************************************************************************************/

  try {
    // SELECIONA A TABELA A SER CLONADA CONFORME ID
    $sql_prop = "SELECT * FROM propostas WHERE prop_id = '$prop_id'";
    $stmt = $conn->prepare($sql_prop);
    $stmt->execute();
    $rows_prop = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($rows_prop) > 0) {
      foreach ($rows_prop as $prop) {

        $prop_titulo              = $prop['prop_titulo'] . ' - (CÓPIA)';
        $prop_status_etapa        = 4;

        $prop_parc_data_atividade = isset($_POST['prop_parc_data_atividade']) ? $_POST['prop_parc_data_atividade'] : NULL;

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
                                                    :prop_data_inicio,
                                                    :prop_data_fim,
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
                                                    :prop_data_cad,
                                                    :prop_data_upd
                                                  )";

        $stmt_insert = $conn->prepare($sql_insert_prop);
        $stmt_insert->bindParam(':prop_id', $novo_prop_id);
        $stmt_insert->bindParam(':prop_tipo', $prop['prop_tipo']);
        $stmt_insert->bindParam(':prop_codigo', $prop_codigo);
        $stmt_insert->bindParam(':prop_status_etapa', $prop_status_etapa);
        $stmt_insert->bindParam(':prop_titulo', $prop_titulo);
        $stmt_insert->bindParam(':prop_descricao', $prop['prop_descricao']);
        $stmt_insert->bindParam(':prop_vinculo_programa', $prop['prop_vinculo_programa']);
        $stmt_insert->bindParam(':prop_qual_vinculo_programa', $prop['prop_qual_vinculo_programa']);
        $stmt_insert->bindParam(':prop_curso_vinculo', $prop['prop_curso_vinculo']);
        $stmt_insert->bindParam(':prop_nome_curso_vinculo', $prop['prop_nome_curso_vinculo']);
        $stmt_insert->bindParam(':prop_justificativa', $prop['prop_justificativa']);
        $stmt_insert->bindParam(':prop_obj_pedagogico', $prop['prop_obj_pedagogico']);
        $stmt_insert->bindParam(':prop_publico_alvo', $prop['prop_publico_alvo']);
        $stmt_insert->bindParam(':prop_area_conhecimento', $prop['prop_area_conhecimento']);
        $stmt_insert->bindParam(':prop_area_tematica', $prop['prop_area_tematica']);
        $stmt_insert->bindParam(':prop_semana', $prop['prop_semana']);
        $stmt_insert->bindParam(':prop_horario', $prop['prop_horario']);
        $stmt_insert->bindParam(':prop_data_inicio', $prop['prop_data_inicio']);
        $stmt_insert->bindParam(':prop_data_fim', $prop['prop_data_fim']);
        $stmt_insert->bindParam(':prop_carga_hora', $prop['prop_carga_hora']);
        $stmt_insert->bindParam(':prop_total_vaga', $prop['prop_total_vaga']);
        $stmt_insert->bindParam(':prop_quant_turma', $prop['prop_quant_turma']);
        $stmt_insert->bindParam(':prop_forma_acesso', $prop['prop_forma_acesso']);
        $stmt_insert->bindParam(':prop_outra_forma_acesso', $prop['prop_outra_forma_acesso']);
        $stmt_insert->bindParam(':prop_modalidade', $prop['prop_modalidade']);
        $stmt_insert->bindParam(':prop_campus', $prop['prop_campus']);
        $stmt_insert->bindParam(':prop_local', $prop['prop_local']);
        $stmt_insert->bindParam(':prop_preco', $prop['prop_preco']);
        $stmt_insert->bindParam(':prop_preco_parcelas', $prop['prop_preco_parcelas']);
        $stmt_insert->bindParam(':prop_acao_acessibilidade', $prop['prop_acao_acessibilidade']);
        $stmt_insert->bindParam(':prop_desc_acao_acessibilidade', $prop['prop_desc_acao_acessibilidade']);
        $stmt_insert->bindParam(':prop_ofertas_vagas', $prop['prop_ofertas_vagas']);
        $stmt_insert->bindParam(':prop_quant_beneficios', $prop['prop_quant_beneficios']);
        $stmt_insert->bindParam(':prop_atendimento_doacao', $prop['prop_atendimento_doacao']);
        $stmt_insert->bindParam(':prop_desc_beneficios', $prop['prop_desc_beneficios']);
        $stmt_insert->bindParam(':prop_comunidade', $prop['prop_comunidade']);
        $stmt_insert->bindParam(':prop_localidade', $prop['prop_localidade']);
        $stmt_insert->bindParam(':prop_responsavel', $prop['prop_responsavel']);
        $stmt_insert->bindParam(':prop_responsavel_contato', $prop['prop_responsavel_contato']);
        $stmt_insert->bindParam(':prop_info_complementar', $prop['prop_info_complementar']);
        $stmt_insert->bindParam(':prop_custos', $prop['prop_custos']);
        $stmt_insert->bindParam(':prop_recursos', $prop['prop_recursos']);
        $stmt_insert->bindParam(':prop_desc_atividade', $prop['prop_desc_atividade']);
        $stmt_insert->bindParam(':prop_rec_audio_video', $prop['prop_rec_audio_video']);
        $stmt_insert->bindParam(':prop_outros', $prop['prop_outros']);
        $stmt_insert->bindParam(':prop_card', $prop['prop_card']);
        $stmt_insert->bindParam(':prop_texto_divulgacao', $prop['prop_texto_divulgacao']);
        $stmt_insert->bindParam(':prop_diferenciais', $prop['prop_diferenciais']);
        $stmt_insert->bindParam(':prop_brienfing', $prop['prop_brienfing']);
        $stmt_insert->bindParam(':prop_parceria', $prop['prop_parceria']);
        $stmt_insert->bindParam(':prop_informacoes', $prop['prop_informacoes']);
        $stmt_insert->bindParam(':prop_event_patrocinio', $prop['prop_event_patrocinio']);
        $stmt_insert->bindParam(':prop_event_qual_patrocinio', $prop['prop_event_qual_patrocinio']);
        $stmt_insert->bindParam(':prop_event_parceria', $prop['prop_event_parceria']);
        $stmt_insert->bindParam(':prop_event_qual_parceria', $prop['prop_event_qual_parceria']);
        $stmt_insert->bindParam(':prop_event_contatos', $prop['prop_event_contatos']);
        $stmt_insert->bindParam(':prop_event_sorteio', $prop['prop_event_sorteio']);
        $stmt_insert->bindParam(':prop_prog_tipo', $prop['prop_prog_tipo']);
        $stmt_insert->bindParam(':prop_prog_categoria', $prop['prop_prog_categoria']);
        $stmt_insert->bindParam(':prop_prog_valor_inscricao', $prop['prop_prog_valor_inscricao']);
        $stmt_insert->bindParam(':prop_prog_docente', $prop['prop_prog_docente']);
        $stmt_insert->bindParam(':prop_prog_area_atuacao', $prop['prop_prog_area_atuacao']);
        $stmt_insert->bindParam(':prop_prog_local_atuacao', $prop['prop_prog_local_atuacao']);
        $stmt_insert->bindParam(':prop_prog_data_inicio', $prop['prop_prog_data_inicio']);
        $stmt_insert->bindParam(':prop_prog_data_fim', $prop['prop_prog_data_fim']);
        $stmt_insert->bindParam(':prop_prog_obs', $prop['prop_prog_obs']);
        $stmt_insert->bindParam(':prop_parc_nome_empresa', $prop['prop_parc_nome_empresa']);
        $stmt_insert->bindParam(':prop_parc_tipo_empresa', $prop['prop_parc_tipo_empresa']);
        $stmt_insert->bindParam(':prop_parc_tipo_outro', $prop['prop_parc_tipo_outro']);
        $stmt_insert->bindParam(':prop_parc_orgao_empresa', $prop['prop_parc_orgao_empresa']);
        $stmt_insert->bindParam(':prop_parc_email', $prop['prop_parc_email']);
        $stmt_insert->bindParam(':prop_parc_telefone', $prop['prop_parc_telefone']);
        $stmt_insert->bindParam(':prop_parc_cep', $prop['prop_parc_cep']);
        $stmt_insert->bindParam(':prop_parc_logradouro', $prop['prop_parc_logradouro']);
        $stmt_insert->bindParam(':prop_parc_numero', $prop['prop_parc_numero']);
        $stmt_insert->bindParam(':prop_parc_bairro', $prop['prop_parc_bairro']);
        $stmt_insert->bindParam(':prop_parc_municipio', $prop['prop_parc_municipio']);
        $stmt_insert->bindParam(':prop_parc_estado', $prop['prop_parc_estado']);
        $stmt_insert->bindParam(':prop_parc_pais', $prop['prop_parc_pais']);
        $stmt_insert->bindParam(':prop_parc_responsavel', $prop['prop_parc_responsavel']);
        $stmt_insert->bindParam(':prop_parc_cargo', $prop['prop_parc_cargo']);
        $stmt_insert->bindParam(':prop_parc_contato_referencia', $prop['prop_parc_contato_referencia']);
        $stmt_insert->bindParam(':prop_parc_possui_convenio', $prop['prop_parc_possui_convenio']);
        $stmt_insert->bindParam(':prop_parc_tipo_parceria', $prop['prop_parc_tipo_parceria']);
        $stmt_insert->bindParam(':prop_parc_titulo_atividade', $prop['prop_parc_titulo_atividade']);
        $stmt_insert->bindParam(':prop_parc_objetivo_atividade', $prop['prop_parc_objetivo_atividade']);
        $stmt_insert->bindParam(':prop_parc_local_atividade', $prop['prop_parc_local_atividade']);
        $stmt_insert->bindParam(':prop_parc_tipo_espaco', $prop['prop_parc_tipo_espaco']);
        $stmt_insert->bindParam(':prop_parc_campus_atividade', $prop['prop_parc_campus_atividade']);
        $stmt_insert->bindParam(':prop_parc_carga_hora', $prop['prop_parc_carga_hora']);
        $stmt_insert->bindParam(':prop_parc_data_atividade', $prop_parc_data_atividade);
        $stmt_insert->bindParam(':prop_parc_hora_atividade_inicial', $prop['prop_parc_hora_atividade_inicial']);
        $stmt_insert->bindParam(':prop_parc_hora_atividade_final', $prop['prop_parc_hora_atividade_final']);
        $stmt_insert->bindParam(':prop_parc_numero_participantes', $prop['prop_parc_numero_participantes']);
        $stmt_insert->bindParam(':prop_parc_recursos_necessarios', $prop['prop_parc_recursos_necessarios']);
        $stmt_insert->bindParam(':prop_parc_beneficios', $prop['prop_parc_beneficios']);
        $stmt_insert->bindParam(':prop_parc_beneficios_quantidade', $prop['prop_parc_beneficios_quantidade']);
        $stmt_insert->bindParam(':prop_parc_organizacao_espaco', $prop['prop_parc_organizacao_espaco']);
        $stmt_insert->bindParam(':prop_parc_comentarios', $prop['prop_parc_comentarios']);
        $stmt_insert->bindParam(':prop_ext_tipo_programa', $prop['prop_ext_tipo_programa']);
        $stmt_insert->bindParam(':prop_ext_categoria_evento', $prop['prop_ext_categoria_evento']);
        $stmt_insert->bindParam(':prop_ext_categoria_evento_outro', $prop['prop_ext_categoria_evento_outro']);
        $stmt_insert->bindParam(':prop_ext_inst_atendida', $prop['prop_ext_inst_atendida']);
        $stmt_insert->bindParam(':prop_ext_atividades', $prop['prop_ext_atividades']);
        $stmt_insert->bindParam(':prop_ext_datas_horas', $prop['prop_ext_datas_horas']);
        $stmt_insert->bindParam(':prop_ext_mob_equipamento', $prop['prop_ext_mob_equipamento']);
        $stmt_insert->bindParam(':prop_ext_dinamica', $prop['prop_ext_dinamica']);
        $stmt_insert->bindParam(':prop_ext_quant_atendimento', $prop['prop_ext_quant_atendimento']);
        $stmt_insert->bindParam(':prop_ext_forma_ingresso', $prop['prop_ext_forma_ingresso']);
        $stmt_insert->bindParam(':prop_ext_valor_bolsa', $prop['prop_ext_valor_bolsa']);
        $stmt_insert->bindParam(':prop_ext_atendimento_ofertado', $prop['prop_ext_atendimento_ofertado']);
        $stmt_insert->bindParam(':prop_ext_impacto_social', $prop['prop_ext_impacto_social']);
        $stmt_insert->bindParam(':prop_ext_obs', $prop['prop_ext_obs']);
        $stmt_insert->bindParam(':prop_user_id', $prop_user_id);
        $stmt_insert->bindParam(':prop_data_cad', $data_real);
        $stmt_insert->bindParam(':prop_data_upd', $data_real);

        if ($stmt_insert->execute()) {
          //$_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados duplicados com sucesso!";
          //header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        } else {
          // echo "Erro ao duplicar dados: " . implode(" ", $stmt_insert->errorInfo());
          $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro ao duplicar dados! - (Proposta 1)";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        }
      }
    }
    //else {
    // echo "Nenhum registro encontrado para duplicação.";
    //$_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Nenhum registro encontrado para duplicação! - (Proposta 1)";
    //header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    //}




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
                                                                      :pcp_data_cad,
                                                                      :pcp_data_upd
                                                                    )";
        $stmt_insert = $conn->prepare($sql_insert_pcp);
        $stmt_insert->bindParam(':pcp_id', $novo_pcp_id);
        $stmt_insert->bindParam(':pcp_proposta_id', $novo_prop_id);
        $stmt_insert->bindParam(':pcp_nome', $pcp['pcp_nome']);
        $stmt_insert->bindParam(':pcp_email', $pcp['pcp_email']);
        $stmt_insert->bindParam(':pcp_contato', $pcp['pcp_contato']);
        $stmt_insert->bindParam(':pcp_partic_perfil', $pcp['pcp_partic_perfil']);
        $stmt_insert->bindParam(':pcp_outro_partic_perfil', $pcp['pcp_outro_partic_perfil']);
        $stmt_insert->bindParam(':pcp_carga_hora', $pcp['pcp_carga_hora']);
        $stmt_insert->bindParam(':pcp_area_atuacao', $pcp['pcp_area_atuacao']);
        $stmt_insert->bindParam(':pcp_nome_area_atuacao', $pcp['pcp_nome_area_atuacao']);
        $stmt_insert->bindParam(':pcp_formacao', $pcp['pcp_formacao']);
        $stmt_insert->bindParam(':pcp_lattes', $pcp['pcp_lattes']);
        $stmt_insert->bindParam(':pcp_user_id', $prop_user_id);
        $stmt_insert->bindParam(':pcp_data_cad', $data_real);
        $stmt_insert->bindParam(':pcp_data_upd', $data_real);


        if ($stmt_insert->execute()) {
          // $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados duplicados com sucesso!";
          // header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        } else {
          // echo "Erro ao duplicar dados: " . implode(" ", $stmt_insert->errorInfo());
          $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro ao duplicar dados! - (Coordenador Projeto)";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        }
      }
    }
    //else {
    // echo "Nenhum registro encontrado para duplicação.";
    //$_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Nenhum registro encontrado para duplicação! - (Coordenador Projeto)";
    //header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    //}













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
                                                                    :pex_data_cad,
                                                                    :pex_data_upd
                                                                  )";

        $stmt_insert = $conn->prepare($sql_insert_pex);
        $stmt_insert->bindParam(':pex_id', $novo_pex_id);
        $stmt_insert->bindParam(':pex_proposta_id', $novo_prop_id);
        $stmt_insert->bindParam(':pex_nome', $pex['pex_nome']);
        $stmt_insert->bindParam(':pex_email', $pex['pex_email']);
        $stmt_insert->bindParam(':pex_contato', $pex['pex_contato']);
        $stmt_insert->bindParam(':pex_partic_categ', $pex['pex_partic_categ']);
        $stmt_insert->bindParam(':pex_qual_partic_categ', $pex['pex_qual_partic_categ']);
        $stmt_insert->bindParam(':pex_partic_perfil', $pex['pex_partic_perfil']);
        $stmt_insert->bindParam(':pex_outro_partic_perfil', $pex['pex_outro_partic_perfil']);
        $stmt_insert->bindParam(':pex_carga_hora', $pex['pex_carga_hora']);
        $stmt_insert->bindParam(':pex_area_atuacao', $pex['pex_area_atuacao']);
        $stmt_insert->bindParam(':pex_nome_area_atuacao', $pex['pex_nome_area_atuacao']);
        $stmt_insert->bindParam(':pex_formacao', $pex['pex_formacao']);
        $stmt_insert->bindParam(':pex_lattes', $pex['pex_lattes']);
        $stmt_insert->bindParam(':pex_user_id', $prop_user_id);
        $stmt_insert->bindParam(':pex_data_cad', $data_real);
        $stmt_insert->bindParam(':pex_data_upd', $data_real);

        if ($stmt_insert->execute()) {
          //$_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados duplicados com sucesso!";
          //header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        } else {
          // echo "Erro ao duplicar dados: " . implode(" ", $stmt_insert->errorInfo());
          $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro ao duplicar dados! - (Equipe Executora)";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        }
      }
    }
    //else {
    // echo "Nenhum registro encontrado para duplicação.";
    //$_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Nenhum registro encontrado para duplicação! - (Equipe Executora)";
    //header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    //}










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
                                                                    :ppe_data_cad,
                                                                    :ppe_data_upd
                                                                  )";
        $stmt_insert = $conn->prepare($sql_insert_ppe);
        $stmt_insert->bindParam(':ppe_id', $novo_ppe_id);
        $stmt_insert->bindParam(':ppe_proposta_id', $novo_prop_id);
        $stmt_insert->bindParam(':ppe_nome', $ppe['ppe_nome']);
        $stmt_insert->bindParam(':ppe_email', $ppe['ppe_email']);
        $stmt_insert->bindParam(':ppe_contato', $ppe['ppe_contato']);
        $stmt_insert->bindParam(':ppe_cnpj', $ppe['ppe_cnpj']);
        $stmt_insert->bindParam(':ppe_responsavel', $ppe['ppe_responsavel']);
        $stmt_insert->bindParam(':ppe_area_atuacao', $ppe['ppe_area_atuacao']);
        $stmt_insert->bindParam(':ppe_obs', $ppe['ppe_obs']);
        $stmt_insert->bindParam(':ppe_convenio', $ppe['ppe_convenio']);
        $stmt_insert->bindParam(':ppe_user_id', $prop_user_id);
        $stmt_insert->bindParam(':ppe_data_cad', $data_real);
        $stmt_insert->bindParam(':ppe_data_upd', $data_real);

        if ($stmt_insert->execute()) {
          // $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados duplicados com sucesso!";
          // header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        } else {
          // echo "Erro ao duplicar dados: " . implode(" ", $stmt_insert->errorInfo());
          $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro ao duplicar dados! - (Parceiros Externos)";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        }
      }
    }
    //else {
    // echo "Nenhum registro encontrado para duplicação.";
    //$_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Nenhum registro encontrado para duplicação! - (Parceiros Externos)";
    //header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    // }










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
                                                                    :pmc_data_cad,
                                                                    :pmc_data_upd
                                                                  )";

        $stmt_insert = $conn->prepare($sql_insert_pmc);
        $stmt_insert->bindParam(':pmc_id', $novo_pmc_id);
        $stmt_insert->bindParam(':pmc_proposta_id', $novo_prop_id);
        $stmt_insert->bindParam(':pmc_material_consumo', $pmc['pmc_material_consumo']);
        $stmt_insert->bindParam(':pmc_quantidade', $pmc['pmc_quantidade']);
        $stmt_insert->bindParam(':pmc_obs', $pmc['pmc_obs']);
        $stmt_insert->bindParam(':pmc_user_id', $prop_user_id);
        $stmt_insert->bindParam(':pmc_data_cad', $data_real);
        $stmt_insert->bindParam(':pmc_data_upd', $data_real);

        if ($stmt_insert->execute()) {
          //$_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados duplicados com sucesso!";
          //header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        } else {
          // echo "Erro ao duplicar dados: " . implode(" ", $stmt_insert->errorInfo());
          $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro ao duplicar dados! - (Material Consumo)";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        }
      }
    }
    //else {
    // echo "Nenhum registro encontrado para duplicação.";
    //$_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Nenhum registro encontrado para duplicação! - (Material Consumo)";
    //header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    //}














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
                                                          :ps_data_cad,
                                                          :ps_data_upd
                                                          )";

        $stmt_insert = $conn->prepare($sql_insert_ps);
        $stmt_insert->bindParam(':ps_id', $novo_ps_id);
        $stmt_insert->bindParam(':ps_proposta_id', $novo_prop_id);
        $stmt_insert->bindParam(':ps_mat_serv_id', $ps['ps_mat_serv_id']);
        $stmt_insert->bindParam(':ps_quantidade', $ps['ps_quantidade']);
        $stmt_insert->bindParam(':ps_obs', $ps['ps_obs']);
        $stmt_insert->bindParam(':ps_user_id', $prop_user_id);
        $stmt_insert->bindParam(':ps_data_cad', $data_real);
        $stmt_insert->bindParam(':ps_data_upd', $data_real);

        if ($stmt_insert->execute()) {
          // $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados duplicados com sucesso!";
          // header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        } else {
          // echo "Erro ao duplicar dados: " . implode(" ", $stmt_insert->errorInfo());
          $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro ao duplicar dados! - (Serviços)";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        }
      }
    }
    //else {
    // echo "Nenhum registro encontrado para duplicação.";
    //$_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Nenhum registro encontrado para duplicação! - (Serviços)";
    //header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    //}













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
                                                        :parq_data_cad
                                                      )";

        $stmt_insert = $conn->prepare($sql_insert_parq);
        $stmt_insert->bindParam(':parq_prop_id', $novo_prop_id);
        $stmt_insert->bindParam(':parq_codigo', $prop_codigo);
        $stmt_insert->bindParam(':parq_categoria', $parq['parq_categoria']);
        $stmt_insert->bindParam(':parq_arquivo', $parq['parq_arquivo']);
        $stmt_insert->bindParam(':parq_user_id', $prop_user_id);
        $stmt_insert->bindParam(':parq_data_cad', $data_real);

        if ($stmt_insert->execute()) {
          //$_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados duplicados com sucesso!";
          //header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        } else {
          // echo "Erro ao duplicar dados: " . implode(" ", $stmt_insert->errorInfo());
          $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro ao duplicar dados! - (Arquivos)";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        }
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

      //////////////////////

    }
    //else {
    // echo "Nenhum registro encontrado para duplicação.";
    //$_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Nenhum registro encontrado para duplicação! - (Proposta 3 - img)";
    //header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    //}








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
                                                                        :prop_cmod_data_cad,
                                                                        :prop_cmod_data_upd
                                                                      )";

          $stmt_insert = $conn->prepare($sql_insert_prop_cmod);
          $stmt_insert->bindParam(':prop_cmod_id', $novo_prop_cmod_id);
          $stmt_insert->bindParam(':prop_cmod_prop_id', $novo_prop_id);
          $stmt_insert->bindParam(':prop_cmod_tipo_docente', $prop_cmod['prop_cmod_tipo_docente']);
          $stmt_insert->bindParam(':prop_cmod_nome_docente', $prop_cmod['prop_cmod_nome_docente']);
          $stmt_insert->bindParam(':prop_cmod_titulo', $prop_cmod['prop_cmod_titulo']);
          $stmt_insert->bindParam(':prop_cmod_assunto', $prop_cmod['prop_cmod_assunto']);
          $stmt_insert->bindParam(':prop_cmod_data_hora', $prop_cmod['prop_cmod_data_hora']);
          $stmt_insert->bindParam(':prop_cmod_organizacao', $prop_cmod['prop_cmod_organizacao']);
          $stmt_insert->bindParam(':prop_cmod_outra_organizacao', $prop_cmod['prop_cmod_outra_organizacao']);
          $stmt_insert->bindParam(':prop_cmod_forma_pagamento', $prop_cmod['prop_cmod_forma_pagamento']);
          $stmt_insert->bindParam(':prop_cmod_curriculo', $prop_cmod['prop_cmod_curriculo']);
          $stmt_insert->bindParam(':prop_cmod_user_id', $prop_user_id);
          $stmt_insert->bindParam(':prop_cmod_data_cad', $data_real);
          $stmt_insert->bindParam(':prop_cmod_data_upd', $data_real);

          if ($stmt_insert->execute()) {
            $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados duplicados com sucesso!";
            header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
          } else {
            // echo "Erro ao duplicar dados: " . implode(" ", $stmt_insert->errorInfo());
            $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro ao duplicar dados! - (Curso Módulo)";
            header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
          }
        }
      } else {
        // echo "Nenhum registro encontrado para duplicação.";
        $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Nenhum registro encontrado para duplicação! - (Curso Módulo)";
        header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      }
    } // FIM TIPO 1







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
                                                                                  :prc_data_cad,
                                                                                  :prc_data_upd
                                                                                )";

          $stmt_insert = $conn->prepare($sql_insert_prc);
          $stmt_insert->bindParam(':prc_id', $novo_prc_id);
          $stmt_insert->bindParam(':prc_proposta_id', $novo_prop_id);
          $stmt_insert->bindParam(':prc_nome', $prc['prc_nome']);
          $stmt_insert->bindParam(':prc_contato', $prc['prc_contato']);
          $stmt_insert->bindParam(':prc_email', $prc['prc_email']);
          $stmt_insert->bindParam(':prc_user_id', $prop_user_id);
          $stmt_insert->bindParam(':prc_data_cad', $data_real);
          $stmt_insert->bindParam(':prc_data_upd', $data_real);


          if ($stmt_insert->execute()) {
            //$_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados duplicados com sucesso!";
            //header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
          } else {
            // echo "Erro ao duplicar dados: " . implode(" ", $stmt_insert->errorInfo());
            $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro ao duplicar dados! - (Extensão Comunitária - Contatos)";
            header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
          }
        }
      } else {
        // echo "Nenhum registro encontrado para duplicação.";
        $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Nenhum registro encontrado para duplicação! - (Extensão Comunitária - Contatos)";
        header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      }
    } // FIM TIPO 5














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
                                                  :sta_an_data_cad,
                                                  :sta_an_data_upd
                                                  )";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":sta_an_prop_id", $novo_prop_id);
    $stmt->bindParam(":sta_an_status", $num_status);
    $stmt->bindParam(":sta_an_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":sta_an_data_cad", $data_real);
    $stmt->bindParam(":sta_an_data_upd", $data_real);
    $stmt->execute();
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
                                          :prop_sta_data_cad
                                          )";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":prop_sta_prop_id", $novo_prop_id); // ID DA PROPOSTA
    $stmt->bindParam(":prop_sta_status", $num_status);
    $stmt->bindParam(":prop_sta_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":prop_sta_data_cad", $data_real);
    $stmt->execute();
    // -------------------------------

    try {
      $mail = new PHPMailer(true);
      include '../controller/email_conf.php';
      include '../includes/email/send_email.php'; // CONFIGURAÇÃO DE E-MAILS
      $mail->addAddress($_SESSION['email']); // E-MAIL DO USUÁRIO QUE RECEBERÁ A MENSAGEM
      $mail->isHTML(true);
      $mail->Subject = 'Proposta Duplicada: ' . $prop_codigo;

      //RECUPERA URL PARA O LINK DO EMAIL
      $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
      $url = $_SERVER['REQUEST_URI'];
      $path = parse_url($url, PHP_URL_PATH);
      $directories = explode('/', $path);
      array_shift($directories);
      $pagina = $protocol . $_SERVER['HTTP_HOST'] . '/' . $directories[0];

      // CORPO DO EMAIL
      include '../includes/email/email_header_600.php';
      $email_conteudo .= "

    <p style='width: 100%; font-size: 1.188rem; text-transform: uppercase; font-weight: 600; margin-bottom: 20px; display: inline-block;'>
    Proposta $prop_codigo foi duplicada</span><br><br>
    </p>

    <p style='font-size: 1rem;'>
    Você ja pode editar.
    </p>

    <a style='cursor: pointer;' href='$pagina'><button style='background: #62B790; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 13px 20px; margin-top: 20px;' target='_blank'>ACESSE O SISTEMA</button></a>";

      include '../includes/email/email_footer.php';
      $mail->Body  = $email_conteudo;
      $mail->send();

      $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> A proposta foi clonada!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    } catch (Exception $e) {
      //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
      $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro no enviar o e-mail!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    }





    ////
  } catch (PDOException $e) {
    // echo "Erro na conexão com o banco de dados: " . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro ao tentar clonar os dados!" . $e->getMessage();
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
} // FIM REQUEST_METHOD