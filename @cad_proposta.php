<?php include 'includes/header.php'; ?>

<div class="row">
  <div class="col-12">
    <div class="card mt-n4 mx-n4 mb-n5">
      <div class="bg_header_sm_verde">
        <div class="card-body pb-4 pt-sm-4 pt-5 mb-5">
          <div class="row">
            <div class="col-md">
              <div class="row align-items-center justify-content-md-center">
                <div class="col-xl-6 px-4 pb-lg-2">
                  <h4>Solicitação de Reserva dos Espaços de Ensino</h4>
                  <div class="hstack gap-3 flex-wrap">
                    <div class="text-muted">Solicitante: <span class="fw-medium"><?= $primeiroNome . '&nbsp;&nbsp;' . $ultimoNome ?></span></div>
                    <div class="vr"></div>
                    <div class="text-muted">Data: <span class="fw-medium"><?= date('d/m/Y H:i') ?></span></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
if (isset($_GET['i'])) {
  $prop_id = base64_decode($_GET['i']);
  $sql = 'SELECT * FROM propostas
          LEFT JOIN propostas_status ON propostas_status.prop_sta_prop_id = propostas.prop_id
          LEFT JOIN conf_cursos_coordenadores ON conf_cursos_coordenadores.cc_id = propostas.prop_curso_vinculo
          LEFT JOIN modalidade_encontro ON modalidade_encontro.mod_en_id = propostas.prop_modalidade
          LEFT JOIN unidades ON unidades.uni_id = propostas.prop_campus OR unidades.uni_id = propostas.prop_parc_campus_atividade
          LEFT JOIN forma_acesso ON forma_acesso.for_acess_id = propostas.prop_forma_acesso
          LEFT JOIN tipo_programa ON tipo_programa.tipprog_id = propostas.prop_prog_tipo
          LEFT JOIN conf_tipo_programa ON conf_tipo_programa.ctp_id = propostas.prop_prog_categoria
          LEFT JOIN conf_areas_tematicas ON conf_areas_tematicas.at_id = propostas.prop_prog_area_atuacao
          LEFT JOIN propostas_arq ON propostas_arq.parq_prop_id = propostas.prop_id
          LEFT JOIN conf_extensao_comunitaria ON conf_extensao_comunitaria.cec_id = propostas.prop_ext_tipo_programa
          LEFT JOIN conf_tipo_evento_social ON conf_tipo_evento_social.tes_id = propostas.prop_ext_categoria_evento
          LEFT JOIN tipo_ingresso_participante ON tipo_ingresso_participante.tip_id = propostas.prop_ext_forma_ingresso
          LEFT JOIN tipo_empresa ON tipo_empresa.tipemp_id = propostas.prop_parc_tipo_empresa
          LEFT JOIN paises ON paises.pais_nome = propostas.prop_parc_pais
          LEFT JOIN tipo_parceria ON tipo_parceria.tiparc_id = propostas.prop_parc_tipo_parceria
          LEFT JOIN tipo_espaco ON tipo_espaco.tipesp_id = propostas.prop_parc_tipo_espaco
          LEFT JOIN conf_tipo_espaco_organizacao ON conf_tipo_espaco_organizacao.esporg_id = propostas.prop_parc_organizacao_espaco
          WHERE prop_id = :prop_id';
  $stmt = $conn->prepare($sql);
  $stmt->execute(['prop_id' => $prop_id]);
  $result = $stmt->fetch();
  if ($result) {
    $prop_id                          = trim(isset($result['prop_id'])) ? $result['prop_id'] : NULL;
    $prop_tipo                        = trim(isset($result['prop_tipo'])) ? $result['prop_tipo'] : NULL;
    $prop_codigo                      = trim(isset($result['prop_codigo'])) ? $result['prop_codigo'] : NULL;
    $prop_status_etapa                = trim(isset($result['prop_status_etapa'])) ? $result['prop_status_etapa'] : NULL;
    $prop_titulo                      = trim(isset($result['prop_titulo'])) ? $result['prop_titulo'] : NULL;
    $prop_descricao                   = trim(isset($result['prop_descricao'])) ? $result['prop_descricao'] : NULL;
    $prop_vinculo_programa            = trim(isset($result['prop_vinculo_programa'])) ? $result['prop_vinculo_programa'] : NULL;
    $prop_qual_vinculo_programa       = trim(isset($result['prop_qual_vinculo_programa'])) ? $result['prop_qual_vinculo_programa'] : NULL;
    $prop_curso_vinculo               = trim(isset($result['prop_curso_vinculo'])) ? $result['prop_curso_vinculo'] : NULL;
    $prop_nome_curso_vinculo          = trim(isset($result['prop_nome_curso_vinculo'])) ? $result['prop_nome_curso_vinculo'] : NULL;
    $prop_justificativa               = trim(isset($result['prop_justificativa'])) ? $result['prop_justificativa'] : NULL;
    $prop_obj_pedagogico              = trim(isset($result['prop_obj_pedagogico'])) ? $result['prop_obj_pedagogico'] : NULL;
    $prop_publico_alvo                = trim(isset($result['prop_publico_alvo'])) ? $result['prop_publico_alvo'] : NULL;
    $prop_area_conhecimento           = trim(isset($result['prop_area_conhecimento'])) ? $result['prop_area_conhecimento'] : NULL;
    $prop_area_tematica               = trim(isset($result['prop_area_tematica'])) ? $result['prop_area_tematica'] : NULL;
    $prop_semana                      = trim(isset($result['prop_semana'])) ? $result['prop_semana'] : NULL;
    $prop_horario                     = trim(isset($result['prop_horario'])) ? $result['prop_horario'] : NULL;
    $prop_data_inicio                 = trim(isset($result['prop_data_inicio'])) ? $result['prop_data_inicio'] : NULL;
    $prop_data_fim                    = trim(isset($result['prop_data_fim'])) ? $result['prop_data_fim'] : NULL;
    $prop_carga_hora                  = trim(isset($result['prop_carga_hora'])) ? $result['prop_carga_hora'] : NULL;
    $prop_total_vaga                  = trim(isset($result['prop_total_vaga'])) ? $result['prop_total_vaga'] : NULL;
    $prop_quant_turma                 = trim(isset($result['prop_quant_turma'])) ? $result['prop_quant_turma'] : NULL;
    $prop_forma_acesso                = trim(isset($result['prop_forma_acesso'])) ? $result['prop_forma_acesso'] : NULL;
    $prop_outra_forma_acesso          = trim(isset($result['prop_outra_forma_acesso'])) ? $result['prop_outra_forma_acesso'] : NULL;
    $prop_modalidade                  = trim(isset($result['prop_modalidade'])) ? $result['prop_modalidade'] : NULL;
    $prop_campus                      = trim(isset($result['prop_campus'])) ? $result['prop_campus'] : NULL;
    $prop_local                       = trim(isset($result['prop_local'])) ? $result['prop_local'] : NULL;
    $prop_preco                       = trim(isset($result['prop_preco'])) ? $result['prop_preco'] : NULL;
    $prop_preco_parcelas              = trim(isset($result['prop_preco_parcelas'])) ? $result['prop_preco_parcelas'] : NULL;
    $prop_acao_acessibilidade         = trim(isset($result['prop_acao_acessibilidade'])) ? $result['prop_acao_acessibilidade'] : NULL;
    $prop_desc_acao_acessibilidade    = trim(isset($result['prop_desc_acao_acessibilidade'])) ? $result['prop_desc_acao_acessibilidade'] : NULL;
    $prop_ofertas_vagas               = trim(isset($result['prop_ofertas_vagas'])) ? $result['prop_ofertas_vagas'] : NULL;
    $prop_quant_beneficios            = trim(isset($result['prop_quant_beneficios'])) ? $result['prop_quant_beneficios'] : NULL;
    $prop_atendimento_doacao          = trim(isset($result['prop_atendimento_doacao'])) ? $result['prop_atendimento_doacao'] : NULL;
    $prop_desc_beneficios             = trim(isset($result['prop_desc_beneficios'])) ? $result['prop_desc_beneficios'] : NULL;
    $prop_comunidade                  = trim(isset($result['prop_comunidade'])) ? $result['prop_comunidade'] : NULL;
    $prop_localidade                  = trim(isset($result['prop_localidade'])) ? $result['prop_localidade'] : NULL;
    $prop_responsavel                 = trim(isset($result['prop_responsavel'])) ? $result['prop_responsavel'] : NULL;
    $prop_responsavel_contato         = trim(isset($result['prop_responsavel_contato'])) ? $result['prop_responsavel_contato'] : NULL;
    $prop_info_complementar           = trim(isset($result['prop_info_complementar'])) ? $result['prop_info_complementar'] : NULL;
    $prop_custos                      = trim(isset($result['prop_custos'])) ? $result['prop_custos'] : NULL;
    $prop_recursos                    = trim(isset($result['prop_recursos'])) ? $result['prop_recursos'] : NULL;
    $prop_desc_atividade              = trim(isset($result['prop_desc_atividade'])) ? $result['prop_desc_atividade'] : NULL;
    $prop_rec_audio_video             = trim(isset($result['prop_rec_audio_video'])) ? $result['prop_rec_audio_video'] : NULL;
    $prop_outros                      = trim(isset($result['prop_outros'])) ? $result['prop_outros'] : NULL;
    $prop_card                        = trim(isset($result['prop_card'])) ? $result['prop_card'] : NULL;
    $prop_texto_divulgacao            = trim(isset($result['prop_texto_divulgacao'])) ? $result['prop_texto_divulgacao'] : NULL;
    $prop_diferenciais                = trim(isset($result['prop_diferenciais'])) ? $result['prop_diferenciais'] : NULL;
    $prop_brienfing                   = trim(isset($result['prop_brienfing'])) ? $result['prop_brienfing'] : NULL;
    $prop_parceria                    = trim(isset($result['prop_parceria'])) ? $result['prop_parceria'] : NULL;
    $prop_informacoes                 = trim(isset($result['prop_informacoes'])) ? $result['prop_informacoes'] : NULL;
    $prop_event_patrocinio            = trim(isset($result['prop_event_patrocinio'])) ? $result['prop_event_patrocinio'] : NULL;
    $prop_event_qual_patrocinio       = trim(isset($result['prop_event_qual_patrocinio'])) ? $result['prop_event_qual_patrocinio'] : NULL;
    $prop_event_parceria              = trim(isset($result['prop_event_parceria'])) ? $result['prop_event_parceria'] : NULL;
    $prop_event_qual_parceria         = trim(isset($result['prop_event_qual_parceria'])) ? $result['prop_event_qual_parceria'] : NULL;
    $prop_event_contatos              = trim(isset($result['prop_event_contatos'])) ? $result['prop_event_contatos'] : NULL;
    $prop_event_sorteio               = trim(isset($result['prop_event_sorteio'])) ? $result['prop_event_sorteio'] : NULL;
    $prop_prog_tipo                   = trim(isset($result['prop_prog_tipo'])) ? $result['prop_prog_tipo'] : NULL;
    $prop_prog_categoria              = trim(isset($result['prop_prog_categoria'])) ? $result['prop_prog_categoria'] : NULL;
    $prop_prog_valor_inscricao        = trim(isset($result['prop_prog_valor_inscricao'])) ? $result['prop_prog_valor_inscricao'] : NULL;
    $prop_prog_docente                = trim(isset($result['prop_prog_docente'])) ? $result['prop_prog_docente'] : NULL;
    $prop_prog_area_atuacao           = trim(isset($result['prop_prog_area_atuacao'])) ? $result['prop_prog_area_atuacao'] : NULL;
    $prop_prog_local_atuacao          = trim(isset($result['prop_prog_local_atuacao'])) ? $result['prop_prog_local_atuacao'] : NULL;
    $prop_prog_data_inicio            = trim(isset($result['prop_prog_data_inicio'])) ? $result['prop_prog_data_inicio'] : NULL;
    $prop_prog_data_fim               = trim(isset($result['prop_prog_data_fim'])) ? $result['prop_prog_data_fim'] : NULL;
    $prop_prog_obs                    = trim(isset($result['prop_prog_obs'])) ? $result['prop_prog_obs'] : NULL;
    $prop_parc_nome_empresa           = trim(isset($result['prop_parc_nome_empresa'])) ? $result['prop_parc_nome_empresa'] : NULL;
    $prop_parc_tipo_empresa           = trim(isset($result['prop_parc_tipo_empresa'])) ? $result['prop_parc_tipo_empresa'] : NULL;
    $prop_parc_tipo_outro             = trim(isset($result['prop_parc_tipo_outro'])) ? $result['prop_parc_tipo_outro'] : NULL;
    $prop_parc_orgao_empresa          = trim(isset($result['prop_parc_orgao_empresa'])) ? $result['prop_parc_orgao_empresa'] : NULL;
    $prop_parc_email                  = trim(isset($result['prop_parc_email'])) ? $result['prop_parc_email'] : NULL;
    $prop_parc_telefone               = trim(isset($result['prop_parc_telefone'])) ? $result['prop_parc_telefone'] : NULL;
    $prop_parc_cep                    = trim(isset($result['prop_parc_cep'])) ? $result['prop_parc_cep'] : NULL;
    $prop_parc_logradouro             = trim(isset($result['prop_parc_logradouro'])) ? $result['prop_parc_logradouro'] : NULL;
    $prop_parc_numero                 = trim(isset($result['prop_parc_numero'])) ? $result['prop_parc_numero'] : NULL;
    $prop_parc_bairro                 = trim(isset($result['prop_parc_bairro'])) ? $result['prop_parc_bairro'] : NULL;
    $prop_parc_municipio              = trim(isset($result['prop_parc_municipio'])) ? $result['prop_parc_municipio'] : NULL;
    $prop_parc_estado                 = trim(isset($result['prop_parc_estado'])) ? $result['prop_parc_estado'] : NULL;
    $prop_parc_pais                   = trim(isset($result['prop_parc_pais'])) ? $result['prop_parc_pais'] : NULL;
    $prop_parc_responsavel            = trim(isset($result['prop_parc_responsavel'])) ? $result['prop_parc_responsavel'] : NULL;
    $prop_parc_cargo                  = trim(isset($result['prop_parc_cargo'])) ? $result['prop_parc_cargo'] : NULL;
    $prop_parc_contato_referencia     = trim(isset($result['prop_parc_contato_referencia'])) ? $result['prop_parc_contato_referencia'] : NULL;
    $prop_parc_possui_convenio        = trim(isset($result['prop_parc_possui_convenio'])) ? $result['prop_parc_possui_convenio'] : NULL;
    $prop_parc_tipo_parceria          = trim(isset($result['prop_parc_tipo_parceria'])) ? $result['prop_parc_tipo_parceria'] : NULL;
    $prop_parc_titulo_atividade       = trim(isset($result['prop_parc_titulo_atividade'])) ? $result['prop_parc_titulo_atividade'] : NULL;
    $prop_parc_objetivo_atividade     = trim(isset($result['prop_parc_objetivo_atividade'])) ? $result['prop_parc_objetivo_atividade'] : NULL;
    $prop_parc_local_atividade        = trim(isset($result['prop_parc_local_atividade'])) ? $result['prop_parc_local_atividade'] : NULL;
    $prop_parc_tipo_espaco            = trim(isset($result['prop_parc_tipo_espaco'])) ? $result['prop_parc_tipo_espaco'] : NULL;
    $prop_parc_campus_atividade       = trim(isset($result['prop_parc_campus_atividade'])) ? $result['prop_parc_campus_atividade'] : NULL;
    $prop_parc_carga_hora             = trim(isset($result['prop_parc_carga_hora'])) ? $result['prop_parc_carga_hora'] : NULL;
    $prop_parc_data_atividade         = trim(isset($result['prop_parc_data_atividade'])) ? $result['prop_parc_data_atividade'] : NULL;
    $prop_parc_hora_atividade_inicial = trim(isset($result['prop_parc_hora_atividade_inicial'])) ? $result['prop_parc_hora_atividade_inicial'] : NULL;
    $prop_parc_hora_atividade_final   = trim(isset($result['prop_parc_hora_atividade_final'])) ? $result['prop_parc_hora_atividade_final'] : NULL;
    $prop_parc_numero_participantes   = trim(isset($result['prop_parc_numero_participantes'])) ? $result['prop_parc_numero_participantes'] : NULL;
    $prop_parc_recursos_necessarios   = trim(isset($result['prop_parc_recursos_necessarios'])) ? $result['prop_parc_recursos_necessarios'] : NULL;
    $prop_parc_beneficios             = trim(isset($result['prop_parc_beneficios'])) ? $result['prop_parc_beneficios'] : NULL;
    $prop_parc_beneficios_quantidade  = trim(isset($result['prop_parc_beneficios_quantidade'])) ? $result['prop_parc_beneficios_quantidade'] : NULL;
    $prop_parc_organizacao_espaco     = trim(isset($result['prop_parc_organizacao_espaco'])) ? $result['prop_parc_organizacao_espaco'] : NULL;
    $prop_parc_comentarios            = trim(isset($result['prop_parc_comentarios'])) ? $result['prop_parc_comentarios'] : NULL;
    $prop_ext_tipo_programa           = trim(isset($result['prop_ext_tipo_programa'])) ? $result['prop_ext_tipo_programa'] : NULL;
    $prop_ext_categoria_evento        = trim(isset($result['prop_ext_categoria_evento'])) ? $result['prop_ext_categoria_evento'] : NULL;
    $prop_ext_categoria_evento_outro  = trim(isset($result['prop_ext_categoria_evento_outro'])) ? $result['prop_ext_categoria_evento_outro'] : NULL;
    $prop_ext_inst_atendida           = trim(isset($result['prop_ext_inst_atendida'])) ? $result['prop_ext_inst_atendida'] : NULL;
    $prop_ext_atividades              = trim(isset($result['prop_ext_atividades'])) ? $result['prop_ext_atividades'] : NULL;
    $prop_ext_datas_horas             = trim(isset($result['prop_ext_datas_horas'])) ? $result['prop_ext_datas_horas'] : NULL;
    $prop_ext_mob_equipamento         = trim(isset($result['prop_ext_mob_equipamento'])) ? $result['prop_ext_mob_equipamento'] : NULL;
    $prop_ext_dinamica                = trim(isset($result['prop_ext_dinamica'])) ? $result['prop_ext_dinamica'] : NULL;
    $prop_ext_quant_atendimento       = trim(isset($result['prop_ext_quant_atendimento'])) ? $result['prop_ext_quant_atendimento'] : NULL;
    $prop_ext_forma_ingresso          = trim(isset($result['prop_ext_forma_ingresso'])) ? $result['prop_ext_forma_ingresso'] : NULL;
    $prop_ext_valor_bolsa             = trim(isset($result['prop_ext_valor_bolsa'])) ? $result['prop_ext_valor_bolsa'] : NULL;
    $prop_ext_atendimento_ofertado    = trim(isset($result['prop_ext_atendimento_ofertado'])) ? $result['prop_ext_atendimento_ofertado'] : NULL;
    $prop_ext_impacto_social          = trim(isset($result['prop_ext_impacto_social'])) ? $result['prop_ext_impacto_social'] : NULL;
    $prop_ext_obs                     = trim(isset($result['prop_ext_obs'])) ? $result['prop_ext_obs'] : NULL;
    $prop_user_id                     = trim(isset($result['prop_user_id'])) ? $result['prop_user_id'] : NULL;
    $prop_data_cad                    = trim(isset($result['prop_data_cad'])) ? $result['prop_data_cad'] : NULL;
    $prop_data_upd                    = trim(isset($result['prop_data_upd'])) ? $result['prop_data_upd'] : NULL;
    // PROPOSTA STATUS
    $prop_sta_prop_id                 = trim(isset($result['prop_sta_prop_id'])) ? $result['prop_sta_prop_id'] : NULL;
    $prop_sta_status                  = trim(isset($result['prop_sta_status'])) ? $result['prop_sta_status'] : NULL;
    // CURSO
    $cc_id                            = trim(isset($result['cc_id'])) ? $result['cc_id'] : NULL;
    $cc_curso                         = trim(isset($result['cc_curso'])) ? $result['cc_curso'] : NULL;
    // MODALIDADE
    $mod_en_id                        = trim(isset($result['mod_en_id'])) ? $result['mod_en_id'] : NULL;
    $mod_en_modalidade                = trim(isset($result['mod_en_modalidade'])) ? $result['mod_en_modalidade'] : NULL;
    // CAMPUS
    $uni_id                           = trim(isset($result['uni_id'])) ? $result['uni_id'] : NULL;
    $uni_unidade                      = trim(isset($result['uni_unidade'])) ? $result['uni_unidade'] : NULL;
    // FORMA ACESSO 
    $for_acess_id                     = trim(isset($result['for_acess_id'])) ? $result['for_acess_id'] : NULL;
    $for_acess_forma_acesso           = trim(isset($result['for_acess_forma_acesso'])) ? $result['for_acess_forma_acesso'] : NULL;
    // TIPO PROGRAMAS
    $tipprog_id                       = trim(isset($result['tipprog_id'])) ? $result['tipprog_id'] : NULL;
    $tipprog_programa                 = trim(isset($result['tipprog_programa'])) ? $result['tipprog_programa'] : NULL;
    // PROGRAMAS
    $ctp_id                           = trim(isset($result['ctp_id'])) ? $result['ctp_id'] : NULL;
    $ctp_tipo                         = trim(isset($result['ctp_tipo'])) ? $result['ctp_tipo'] : NULL;
    $ctp_categoria                    = trim(isset($result['ctp_categoria'])) ? $result['ctp_categoria'] : NULL;
    // ÁREA DO CONHECIMENTO
    $at_id                            = trim(isset($result['at_id'])) ? $result['at_id'] : NULL;
    $at_area_tematica                 = trim(isset($result['at_area_tematica'])) ? $result['at_area_tematica'] : NULL;
    // PROGRAMA DE EXTENSÃO COMUNITÁRIA
    $cec_id                           = trim(isset($result['cec_id'])) ? $result['cec_id'] : NULL;
    $cec_extensao_comunitaria         = trim(isset($result['cec_extensao_comunitaria'])) ? $result['cec_extensao_comunitaria'] : NULL;
    $cec_desc                         = trim(isset($result['cec_desc'])) ? $result['cec_desc'] : NULL;
    $cec_status                       = trim(isset($result['cec_status'])) ? $result['cec_status'] : NULL;
    // TIPO EVENTO SOCIAL
    $tes_id                           = trim(isset($result['tes_id'])) ? $result['tes_id'] : NULL;
    $tes_evento_social                = trim(isset($result['tes_evento_social'])) ? $result['tes_evento_social'] : NULL;
    $tes_status                       = trim(isset($result['tes_status'])) ? $result['tes_status'] : NULL;
    // EXTENSÃO: FORMA INGRESSO PARTICIPANTE
    $tip_id                           = trim(isset($result['tip_id'])) ? $result['tip_id'] : NULL;
    $tip_tipo_ingresso                = trim(isset($result['tip_tipo_ingresso'])) ? $result['tip_tipo_ingresso'] : NULL;
    // TIPO EMPRESA
    $tipemp_id                        = trim(isset($result['tipemp_id'])) ? $result['tipemp_id'] : NULL;
    $tipemp_tipo_empresa              = trim(isset($result['tipemp_tipo_empresa'])) ? $result['tipemp_tipo_empresa'] : NULL;
    // PAISES
    $pais_id                          = trim(isset($result['pais_id'])) ? $result['pais_id'] : NULL;
    $pais_nome                        = trim(isset($result['pais_nome'])) ? $result['pais_nome'] : NULL;
    // TIPO PARCERIA
    $tiparc_id                        = trim(isset($result['tiparc_id'])) ? $result['tiparc_id'] : NULL;
    $tiparc_tipo_parceria             = trim(isset($result['tiparc_tipo_parceria'])) ? $result['tiparc_tipo_parceria'] : NULL;
    // TIPO DE ESPAÇO
    $tipesp_id                        = trim(isset($result['tipesp_id'])) ? $result['tipesp_id'] : NULL;
    $tipesp_tipo_espaco               = trim(isset($result['tipesp_tipo_espaco'])) ? $result['tipesp_tipo_espaco'] : NULL;
    // TIPO ORGANIZAÇÃO ED ESPAÇOS
    $esporg_id                        = trim(isset($result['esporg_id'])) ? $result['esporg_id'] : NULL;
    $esporg_espaco_organizacao        = trim(isset($result['esporg_espaco_organizacao'])) ? $result['esporg_espaco_organizacao'] : NULL;
    // ARQUIVOS
    $parq_id                          = trim(isset($result['parq_id'])) ? $result['parq_id'] : NULL;
    $parq_prop_id                     = trim(isset($result['parq_prop_id'])) ? $result['parq_prop_id'] : NULL;
    $parq_codigo                      = trim(isset($result['parq_codigo'])) ? $result['parq_codigo'] : NULL;
    $parq_categoria                   = trim(isset($result['parq_categoria'])) ? $result['parq_categoria'] : NULL;
    $parq_arquivo                     = trim(isset($result['parq_arquivo'])) ? $result['parq_arquivo'] : NULL;
  } else {
    header("Location: nova_proposta.php");
  }
}
?>

<div class="row justify-content-md-center">
  <div class="col-xl-6">



    <?php //require 'includes/proposta/form_step.php'; 
    ?>
    <?php // require 'includes/proposta/form_step1.php'; 
    ?>
    <?php // require 'includes/proposta/form_step2.php'; 
    ?>


    <?php
    if (isset($_GET['tp']) && base64_decode($_GET['tp']) == 4 || isset($prop_tipo) && $prop_tipo == 4) {
      require 'includes/proposta/form_parcerias.php';
    } else {
      if (!isset($_GET['st'])) {
        require 'includes/proposta/form_step.php';
      } else if (isset($_GET['st']) && base64_decode($_GET['st']) == 1) {
        require 'includes/proposta/form_step1.php';
      } else if (isset($_GET['st']) && base64_decode($_GET['st']) == 2) {
        require 'includes/proposta/form_step2.php';
      } else if (isset($_GET['st']) && base64_decode($_GET['st']) == 3) {
        require 'includes/proposta/form_step3.php';
      } else if (isset($_GET['st']) && base64_decode($_GET['st']) == 4) {
        require 'includes/proposta/form_step4.php';
      } else if (isset($_GET['st']) && base64_decode($_GET['st']) == 5) {
        if ($prop_tipo == 1) {
          require 'includes/proposta/form_cursos.php';
        }
        if ($prop_tipo == 2) {
          require 'includes/proposta/form_eventos_cientificos.php';
        }
        if ($prop_tipo == 3) {
          require 'includes/proposta/form_programas.php';
        }
        // if ($prop_tipo == 4) {
        // require 'includes/proposta/form_parcerias.php';
        // }
        if ($prop_tipo == 5) {
          require 'includes/proposta/form_extensao_comunitaria.php';
        }
      } else {
        header("Location: nova_proposta.php");
      }
    } ?>

  </div>
</div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- CEP -->
<!-- <script src="assets/js/1120_jquery.min.js"></script> -->
<script src="assets/js/CEP.js"></script>
<!-- SELECT2 FORM -->
<script src="includes/select/select2.js"></script>