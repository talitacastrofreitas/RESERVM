<?php
// Certifique-se de que as variáveis de busca do banco de dados estão disponíveis
// Ex: $unidades, $locais_cabula, $locais_brotas, $recursos, etc.
// Se este arquivo for chamado diretamente, estas variáveis precisarão ser definidas.
?>

<div class="etapa" id="<?= $prefixo_id ?>Etapa1">
  <div class="modal-body">
    <div id="<?= $prefixo_id ?>-progress-bar" class="progress-nav mb-5 mt-2">
      <div class="progress" style="height: 1px;">
        <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="0"></div>
      </div>
      <ul class="nav nav-pills progress-bar-tab custom-nav" role="tablist">
        <li class="nav-item" role="presentation"><button class="nav-link rounded-pill active" data-progressbar="<?= $prefixo_id ?>-progress-bar" data-position="0" tabindex="-1" disabled>Local</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link rounded-pill" data-progressbar="<?= $prefixo_id ?>-progress-bar" data-position="1" tabindex="-1" disabled>Atividade</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link rounded-pill" data-progressbar="<?= $prefixo_id ?>-progress-bar" data-position="2" disabled>Período</button></li>
      </ul>
    </div>
    <div class="row g-3">
      <div class="col-xl-3">
        <label class="form-label">Campus <span>*</span></label>
        <select class="form-select text-uppercase" name="res_campus" id="<?= $prefixo_id ?>_reserva_campus" required>
          <option selected disabled value=""></option>
          <?php foreach ($unidades as $res) : ?><option value="<?= $res['uni_id'] ?>"><?= $res['uni_unidade'] ?></option><?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Este campo é obrigatório</div>
      </div>
      <div class="col-xl-9" id="<?= $prefixo_id ?>_camp_reserv_campus">
        <label class="form-label">Local</label>
        <select class="form-select text-uppercase" disabled></select>
      </div>
      <div class="col-xl-9" id="<?= $prefixo_id ?>_camp_reserv_local_cabula" style="display: none;">
        <label class="form-label">Local <span>*</span></label>
        <select class="form-select text-uppercase" name="res_espaco_id_cabula" id="<?= $prefixo_id ?>_reserva_local_cabula">
          <option selected disabled value=""></option>
          <?php foreach ($locais_cabula as $res) : ?><option value="<?= $res['esp_id'] ?>"><?= $res['esp_codigo'] . ' - ' . $res['esp_nome_local'] ?></option><?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Este campo é obrigatório</div>
      </div>
      <div class="col-xl-9" id="<?= $prefixo_id ?>_camp_reserv_local_brotas" style="display: none;">
        <label class="form-label">Local <span>*</span></label>
        <select class="form-select text-uppercase" name="res_espaco_id_brotas" id="<?= $prefixo_id ?>_reserva_local_brotas">
          <option selected disabled value=""></option>
          <?php foreach ($locais_brotas as $res) : ?><option value="<?= $res['esp_id'] ?>"><?= $res['esp_codigo'] . ' - ' . $res['esp_nome_local'] ?></option><?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Este campo é obrigatório</div>
      </div>
      <div class="col-xl-3"><label class="form-label">Tipo de Sala</label><select class="form-select text-uppercase" id="<?= $prefixo_id ?>_reserva_tipo_sala" disabled>
          <option selected value=""></option><?php foreach ($andares as $res) : ?><option value="<?= $res['and_id'] ?>"><?= $res['and_andar'] ?></option><?php endforeach; ?>
        </select></div>
      <div class="col-xl-3"><label class="form-label">Andar</label><select class="form-select text-uppercase" id="<?= $prefixo_id ?>_reserva_andar" disabled>
          <option selected value=""></option><?php foreach ($andares as $res) : ?><option value="<?= $res['and_id'] ?>"><?= $res['and_andar'] ?></option><?php endforeach; ?>
        </select></div>
      <div class="col-xl-3"><label class="form-label">Pavilhão</label><select class="form-select text-uppercase" id="<?= $prefixo_id ?>_reserva_pavilhao" disabled>
          <option selected value=""></option><?php foreach ($pavilhoes as $res) : ?><option value="<?= $res['pav_id'] ?>"><?= $res['pav_pavilhao'] ?></option><?php endforeach; ?>
        </select></div>
      <div class="col-xl-3"><label class="form-label">Capac. Máxima</label><input type="text" class="form-control" id="<?= $prefixo_id ?>_esp_quant_maxima" disabled></div>
      <div class="col-xl-3"><label class="form-label">Capac. Média</label><input type="text" class="form-control" id="<?= $prefixo_id ?>_reserva_camp_media" disabled></div>
      <div class="col-xl-3"><label class="form-label">Capac. Mínima</label><input type="text" class="form-control" id="<?= $prefixo_id ?>_esp_quant_minima" disabled></div>
      <div class="col-xl-3">
        <label class="form-label">Nº Pessoas <span>*</span></label>
        <input type="text" class="form-control" id="<?= $prefixo_id ?>_reserva_quant_pessoas" name="res_quant_pessoas" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
        <div class="invalid-feedback">Este campo é obrigatório</div>
      </div>
      <div class="col-xl-3">
        <label class="form-label">Recursos Audiovisuais <span>*</span></label>
        <select class="form-select text-uppercase" name="res_recursos" id="<?= $prefixo_id ?>_res_recursos" required>
          <option value="NÃO">NÃO</option>
          <option value="SIM">SIM</option>
        </select>
        <div class="invalid-feedback">Este campo é obrigatório</div>
      </div>
      <div class="col-xl-12" id="<?= $prefixo_id ?>_campo_res_recursos_add" style="display: none;">
        <label class="form-label">Recursos Audiovisuais Adicionais <span>*</span></label>
        <select class="form-select text-uppercase" name="res_recursos_add[]" multiple id="<?= $prefixo_id ?>_res_recursos_add">
          <?php foreach ($recursos as $res) : ?><option value="<?= $res['rec_id'] ?>"><?= $res['rec_recurso'] ?></option><?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Este campo é obrigatório</div>
      </div>
      <div class="col-12">
        <label class="form-label">Observações</label>
        <textarea class="form-control" id="<?= $prefixo_id ?>_meuTextarea" name="res_obs" rows="3" cols="50" maxlength="200"></textarea>
        <p class="label_info text-end mt-1">Caracteres restantes: <span id="<?= $prefixo_id ?>_contador">200</span></p>
      </div>
      <div class="col-lg-12">
        <div class="hstack gap-3 align-items-center justify-content-end mt-3">
          <p class="label_asterisco m-0"><span>*</span> Campo obrigatório</p>
          <button type="button" class="btn botao_azul_escuro btn-label right ms-auto nexttab waves-effect" id="btn<?= ucfirst($prefixo_id) ?>Proximo1"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i> Próximo</button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="etapa d-none" id="<?= $prefixo_id ?>Etapa2">
  <div class="modal-body">
    <div id="<?= $prefixo_id ?>-progress-bar" class="progress-nav mb-5 mt-2">
      <div class="progress" style="height: 1px;">
        <div class="progress-bar" role="progressbar" style="width: 50%;"></div>
      </div>
      <ul class="nav nav-pills progress-bar-tab custom-nav" role="tablist">
        <li class="nav-item" role="presentation"><button class="nav-link rounded-pill done" data-progressbar="<?= $prefixo_id ?>-progress-bar" data-position="0" tabindex="-1" disabled>Local</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link rounded-pill active" data-progressbar="<?= $prefixo_id ?>-progress-bar" data-position="1" tabindex="-1" disabled>Atividade</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link rounded-pill" data-progressbar="<?= $prefixo_id ?>-progress-bar" data-position="2" disabled>Período</button></li>
      </ul>
    </div>
    <div class="row g-3">
      <div class="col-sm">
        <label class="form-label">Tipo de Aula <span>*</span></label>
        <select class="form-select text-uppercase" name="res_tipo_aula" id="<?= $prefixo_id ?>_tipo_aula" required>
          <option selected disabled value=""></option>
          <?php foreach ($tipos_aula as $res) : ?><option value="<?= $res['cta_id'] ?>"><?= $res['cta_tipo_aula'] ?></option><?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Este campo é obrigatório</div>
      </div>
      <div class="col-sm">
        <label class="form-label">Curso <span>*</span></label>
        <select class="form-select text-uppercase" name="res_curso" id="<?= $prefixo_id ?>_res_curso">
          <option selected disabled value=""></option>
          <?php foreach ($cursos as $res) : ?><option value="<?= $res['curs_id'] ?>"><?= $res['curs_curso'] ?></option><?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Este campo é obrigatório</div>
      </div>
      <div class="col-sm" id="<?= $prefixo_id ?>_campo_res_nome_curso" style="display: none;">
        <label class="form-label">Nome do Curso <span>*</span></label>
        <select class="form-select text-uppercase" name="res_curso_extensao" id="<?= $prefixo_id ?>_res_curso_extensao">
          <option selected disabled value=""></option>
          <?php foreach ($cursos_extensao as $res) : ?><option value="<?= $res['cexc_id'] ?>"><?= $res['cexc_curso'] ?></option><?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Este campo é obrigatório</div>
      </div>
      <div class="col-sm">
        <label class="form-label">Semestre</label>
        <select class="form-select text-uppercase" name="res_semestre" id="<?= $prefixo_id ?>_res_semestre">
          <option selected disabled value=""></option>
          <?php foreach ($semestres as $res) : ?><option value="<?= $res['cs_id'] ?>"><?= $res['cs_semestre'] ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-xl-12" id="<?= $prefixo_id ?>_campo_res_componente_atividade" style="display: none;">
        <label class="form-label">Componente Curricular/Atividade <span>*</span></label>
        <select class="form-select text-uppercase" name="res_componente_atividade" id="<?= $prefixo_id ?>_res_componente_atividade">
          <option selected disabled value=""></option>
        </select>
        <div class="invalid-feedback">Este campo é obrigatório</div>
      </div>
      <div class="col-xl-12" id="<?= $prefixo_id ?>_campo_res_componente_atividade_texto" style="display: none;">
        <label class="form-label">Componente Curricular/Atividade <span>*</span></label>
        <input type="text" class="form-control text-uppercase" name="res_componente_atividade_nome" id="<?= $prefixo_id ?>_res_componente_atividade_nome" maxlength="200">
        <div class="invalid-feedback">Este campo é obrigatório</div>
      </div>
      <div class="col-xl-12" id="<?= $prefixo_id ?>_campo_res_nome_atividade" style="display: none;">
        <label class="form-label">Nome da Atividade <span>*</span></label>
        <input type="text" class="form-control text-uppercase" name="res_nome_atividade" id="<?= $prefixo_id ?>_res_nome_atividade" maxlength="200">
        <div class="invalid-feedback">Este campo é obrigatório</div>
      </div>
      <div class="col-xl-12" id="<?= $prefixo_id ?>_campo_res_nome_curso_texto" style="display: none;">
        <label class="form-label">Nome do curso <span>*</span></label>
        <input type="text" class="form-control text-uppercase" name="res_curso_nome" id="<?= $prefixo_id ?>_res_nome_curso" maxlength="200">
        <div class="invalid-feedback">Este campo é obrigatório</div>
      </div>
      <div class="col-xl-12">
        <label class="form-label">Módulo</label>
        <input type="text" class="form-control text-uppercase" name="res_modulo" id="<?= $prefixo_id ?>_res_modulo" maxlength="200">
      </div>
      <div class="col-12">
        <label class="form-label">Título da Aula</label>
        <input type="text" class="form-control text-uppercase" name="res_titulo_aula" id="<?= $prefixo_id ?>_res_titulo_aula" maxlength="200">
      </div>
      <div class="col-xl-12">
        <label class="form-label">Professor(es)</label>
        <input type="text" class="form-control text-uppercase" name="res_professor" id="<?= $prefixo_id ?>_res_professor" maxlength="200">
      </div>
      <div class="col-lg-12">
        <div class="hstack gap-3 align-items-center justify-content-between mt-2">
          <button type="button" class="btn btn-light btn-label previestab waves-effect" id="btn<?= ucfirst($prefixo_id) ?>Anterior2"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Anterior</button>
          <button type="button" class="btn botao_azul_escuro btn-label right ms-auto nexttab waves-effect" id="btn<?= ucfirst($prefixo_id) ?>Proximo2"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i> Próximo</button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="etapa d-none" id="<?= $prefixo_id ?>Etapa3">
  <div class="modal-body">
    <div id="<?= $prefixo_id ?>-progress-bar" class="progress-nav mb-5 mt-2">
      <div class="progress" style="height: 1px;">
        <div class="progress-bar" role="progressbar" style="width: 100%;"></div>
      </div>
      <ul class="nav nav-pills progress-bar-tab custom-nav" role="tablist">
        <li class="nav-item" role="presentation"><button class="nav-link rounded-pill done" data-progressbar="<?= $prefixo_id ?>-progress-bar" data-position="0" tabindex="-1" disabled>Local</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link rounded-pill done" data-progressbar="<?= $prefixo_id ?>-progress-bar" data-position="1" tabindex="-1" disabled>Atividade</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link rounded-pill active" data-progressbar="<?= $prefixo_id ?>-progress-bar" data-position="2" disabled>Período</button></li>
      </ul>
    </div>
    <div class="row g-3">
      <div class="col-xl-3">
        <label class="form-label">Tipo de Reserva <span>*</span></label>
        <select class="form-select text-uppercase" name="res_tipo_reserva" id="<?= $prefixo_id ?>_tipo_reserva" required>
          <option selected disabled value=""></option>
          <?php foreach ($tipos_reserva as $res) : ?><option value="<?= $res['ctr_id'] ?>"><?= $res['ctr_tipo_reserva'] ?></option><?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Este campo é obrigatório</div>
      </div>
      <div class="col-6 col-lg-4 col-xl-3" id="<?= $prefixo_id ?>_camp_reserv_dia_semana" style="display: none;">
        <label class="form-label">Dia da Semana <span>*</span></label>
        <select class="form-select text-uppercase" name="res_dia_semana" id="<?= $prefixo_id ?>_res_dia_semana">
          <option selected disabled value=""></option>
          <?php foreach ($dias_semana as $res) : ?><option value="<?= $res['week_id'] ?>"><?= $res['week_dias'] ?></option><?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Este campo é obrigatório</div>
      </div>
      <div class="col-6 col-lg-4 col-xl-3" id="<?= $prefixo_id ?>_camp_reserv_data">
        <label class="form-label">Data da Reserva <span>*</span></label>
        <input type="text" class="form-control" name="res_data" id="<?= $prefixo_id ?>_data_reserva">
        <div class="invalid-feedback">Este campo é obrigatório</div>
      </div>
      <div class="col-6 col-lg-4 col-xl-3" id="<?= $prefixo_id ?>_camp_reserv_dia">
        <div>
          <label class="form-label">Dia da semana</label>
          <input type="hidden" class="form-control text-uppercase" id="<?= $prefixo_id ?>_diaSemanaId_reserva">
          <select class="form-select text-uppercase" name="res_dia_semana_hidden" id="<?= $prefixo_id ?>_diaSemana_reserva" disabled>
            <option selected disabled value=""></option>
            <?php foreach ($dias_semana as $res) : ?><option value="<?= $res['week_id'] ?>"><?= htmlspecialchars($res['week_dias']) ?></option><?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="col-6 col-lg-4 col-xl-3" id="<?= $prefixo_id ?>_camp_reserv_mes">
        <label class="form-label">Mês</label>
        <input type="text" class="form-control text-uppercase" name="res_mes" id="<?= $prefixo_id ?>_mes_reserva" readonly>
      </div>
      <div class="col-6 col-lg-4 col-xl-3" id="<?= $prefixo_id ?>_camp_reserv_ano">
        <label class="form-label">Ano</label>
        <input type="text" class="form-control text-uppercase" name="res_ano" id="<?= $prefixo_id ?>_ano_reserva" readonly>
      </div>
      <div class="col-6 col-lg-4 col-xl-3">
        <label class="form-label">Hora Início <span>*</span></label>
        <input type="time" class="form-control hora" id="<?= $prefixo_id ?>_res_hora_inicio" name="res_hora_inicio" required>
        <div class="invalid-feedback">Este campo é obrigatório</div>
      </div>
      <div class="col-6 col-lg-4 col-xl-3">
        <label class="form-label">Hora Fim <span>*</span></label>
        <input type="time" class="form-control hora" id="<?= $prefixo_id ?>_res_hora_fim" name="res_hora_fim" required>
        <div class="invalid-feedback">Este campo é obrigatório</div>
      </div>
      <div class="col-6 col-lg-4 col-xl-3">
        <label class="form-label">Turno</label>
        <input type="text" class="form-control text-uppercase" name="res_turno" id="<?= $prefixo_id ?>_turno" readonly>
      </div>
      <div class="col-lg-12">
        <div class="hstack gap-3 align-items-center justify-content-between mt-2">
          <button type="button" class="btn btn-light btn-label previestab waves-effect" id="btn<?= ucfirst($prefixo_id) ?>Anterior3"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Anterior</button>
          <button type="submit" class="btn botao botao_verde" id="btn<?= ucfirst($prefixo_id) ?>Concluir">Concluir</button>
        </div>
      </div>
    </div>
  </div>
</div>