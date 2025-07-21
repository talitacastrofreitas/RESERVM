<?php include 'includes/header.php'; ?>

<?php include 'includes/nav/header_analise.php'; ?>

<div class="row">
  <div class="col-lg-12">
    <div>
      <?php include 'includes/nav/nav_analise.php'; ?>

      <div class="pt-4">
        <div class="row">
          <div class="col-lg-12">

            <style>
              .card_dados_info {

                & label {
                  color: #878a99;
                  margin-bottom: 5px !important;
                }

                & p {
                  margin: 0;
                  text-transform: uppercase;
                }

                & hr {
                  margin-top: 5px;
                  color: #e9ebec !important;
                  opacity: 1 !important;
                  margin-bottom: 15px;
                }

                & .campo_textarea {
                  text-transform: none !important;
                }
              }

              .truncate {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
              }

              @media (max-width: 768px) {
                .truncate {
                  white-space: wrap;
                }
              }
            </style>

            <div class="tab-content text-muted">
              <div class="tab-pane active" id="overview-tab" role="tabpanel">

                <div class="card card_dados_info">
                  <div class="card-header">
                    <div class="row align-items-center" id="ancora_dados_projetos">
                      <div class="col-lg-12">
                        <h5 class="card-title m-0 ps-2">Dados da Solicitação</h5>
                      </div>
                    </div>
                  </div>

                  <div class="card-body p-4">
                    <div class="row grid gx-3">

                      <div class="col-md-4 col-xxl-3">
                        <label>Tipo de Atividade</label>
                        <p><?= $cta_tipo_atividade ?></p>
                        <hr>
                      </div>

                      <div class="col-md-4 col-xxl-3 <?= empty($cexc_curso) ? 'd-none' : '' ?>">
                        <label>Nome do Curso</label>
                        <p><?= $cexc_curso ?></p>
                        <hr>
                      </div>

                      <div class="col-md-4 col-xxl-3 <?= empty($curs_curso) ? 'd-none' : '' ?>">
                        <label>Curso</label>
                        <p><?= $curs_curso ?></p>
                        <hr>
                      </div>

                      <div class="col-md-4 col-xxl-3 <?= empty($cs_semestre) ? 'd-none' : '' ?>">
                        <label>Semestre</label>
                        <p><?= $cs_semestre ?></p>
                        <hr>
                      </div>

                      <div class="col-lg-8 col-xxl-6 <?= empty($solic_nome_atividade) ? 'd-none' : '' ?>">
                        <label>Nome da Atividade</label>
                        <p class="truncate" title="<?= $solic_nome_atividade ?>"><?= $solic_nome_atividade ?></p>
                        <hr>
                      </div>

                      <div class="col-xl-8 col-xxl-6 <?= empty($compc_componente) ? 'd-none' : '' ?>">
                        <label>Componente Curricular</label>
                        <p class="truncate" title="<?= $compc_componente ?>"><?= $compc_componente ?></p>
                        <hr>
                      </div>

                      <div class="col-xl-12 col-xxl-6 <?= empty($solic_nome_curso_text) ? 'd-none' : '' ?>">
                        <label>Nome do Curso</label>
                        <p class="truncate" title="<?= $solic_nome_curso_text ?>"><?= $solic_nome_curso_text ?></p>
                        <hr>
                      </div>

                      <div class="col-xl-12 col-xxl-6 <?= empty($solic_nome_comp_ativ) ? 'd-none' : '' ?>">
                        <label>Nome do Componente/Atividade</label>
                        <p class="truncate" title="<?= $solic_nome_comp_ativ ?>"><?= $solic_nome_comp_ativ ?></p>
                        <hr>
                      </div>

                      <div class="col-md-6 col-xxl-3 <?= empty($solic_nome_prof_resp) ? 'd-none' : '' ?>">
                        <label>Nome do Professor/Responsável</label>
                        <p><?= $solic_nome_prof_resp ?></p>
                        <hr class="d-block d-md-none">
                      </div>

                      <div class="col-md-6 col-xxl-3 <?= empty($solic_contato) ? 'd-none' : '' ?>">
                        <label>Telefone para contato</label>
                        <p><?= $solic_contato ?></p>
                      </div>

                    </div>

                    <div class="row grid gx-3 mt-3 d-none">

                      <?php if ($solic_ap_aula_pratica == 1) { ?>

                        <div class="tab-pane" id="profile1" role="tabpanel">
                          <div class="acordion_azul accordion custom-accordionwithicon accordion-flush accordion-fill-success" id="accordionFill_cp">


                            <div class="accordion-item">
                              <h2 class="accordion-header" id="accordionFill1">
                                <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#accor_fill1" aria-expanded="true" aria-controls="accor_fill1">AULAS PRÁTICAS</button>
                              </h2>
                              <div id="accor_fill1" class="accordion-collapse collapse show" aria-labelledby="accordionFill1" data-bs-parent="#accordionFill_cp">
                                <div class="accordion-body">

                                  <div class="row grid gx-3 mb-2">
                                    <div class="col-md-6 col-xl-4 col-xxl-3">
                                      <div class="mb-3">
                                        <label class="form-label">Campus</label>
                                        <select class="form-select text-uppercase" id="anal_ap_campus" disabled>
                                          <option value="<?= $campus_pratico_id  ?>"><?= $campus_pratico_nome ?></option>
                                        </select>
                                      </div>
                                    </div>

                                    <div class="col-md-6 col-xl-4 col-xxl-3">
                                      <div class="mb-3">
                                        <label class="form-label">Quantidade de turmas</label>
                                        <select class="form-select text-uppercase" id="solic_ap_quant_turma" disabled>
                                          <option value="<?= $ctp_id  ?>"><?= $ctp_turma ?></option>
                                        </select>
                                      </div>
                                    </div>

                                    <div class="col-md-6 col-xl-4 col-xxl-3">
                                      <div class="mb-3">
                                        <label class="form-label">Número estimado de participantes</label>
                                        <input type="text" class="form-control text-uppercase" id="solic_ap_quant_particip" value="<?= $solic_ap_quant_particip ?>" disabled>
                                      </div>
                                    </div>
                                  </div>

                                  <div class="row grid gx-3 mb-2">
                                    <div class="col-xl-12 col-xxl-3">
                                      <div class="mb-4">
                                        <label class="form-label">Tipo da reserva</label>
                                        <div class="check_item_container hstack gap-2 flex-wrap">
                                          <input type="checkbox" class="btn-check check_formulario_check" id="solic_ap_tipo_reserva1" value="1" <?php echo ($solic_ap_tipo_reserva == 1) ? 'checked' : ''; ?> disabled>
                                          <label class="check_item check_formulario" for="solic_ap_tipo_reserva1">Esporádica</label>

                                          <input type="checkbox" class="btn-check check_formulario_check" id="solic_ap_tipo_reserva2" value="2" <?php echo ($solic_ap_tipo_reserva == 2) ? 'checked' : ''; ?> disabled>
                                          <label class="check_item check_formulario" for="solic_ap_tipo_reserva2">Fixa</label>
                                        </div>
                                      </div>
                                    </div>

                                    <?php if ($solic_ap_tipo_reserva != 1) { ?>

                                      <div class="col-xl-12 col-xxl-9">
                                        <div class="mb-4">
                                          <label class="form-label">Dia(s) da semana</label>
                                          <div class="check_item_container hstack gap-2 flex-wrap">
                                            <?php $dias = explode(", ", $solic_ap_dia_reserva);
                                            $sql = $conn->prepare("SELECT week_id, week_dias FROM conf_dias_semana");
                                            $sql->execute();
                                            while ($result = $sql->fetch(PDO::FETCH_ASSOC)) { ?>
                                              <input type="checkbox" class="btn-check check_formulario_check" id="dias_semana<?= $result['week_id'] ?>" value="<?= $result['week_id'] ?>" <?= $solic_ap_dia_reserva_checked = in_array($result['week_id'], $dias) ? "checked" : ""; ?> disabled>
                                              <label class="check_item check_formulario" for="dias_semana<?= $result['week_id'] ?>"><?= $result['week_dias'] ?></label>
                                            <?php } ?>
                                          </div>
                                        </div>
                                      </div>

                                    <?php } else { ?>
                                      <div class="col-12">
                                        <div class="mb-4">
                                          <label class="form-label">Data(s) da reserva</label>
                                          <textarea class="form-control" id="solic_ap_data_reserva" rows="5" disabled><?= $solic_ap_data_reserva ?></textarea>
                                        </div>
                                      </div>
                                    <?php } ?>

                                    <div class="col-md-6 col-xl-4 col-xxl-3">
                                      <div class="mb-4">
                                        <label class="form-label">Horário inicial</label>
                                        <input type="time" class="form-control" id="solic_ap_hora_inicio" value="<?php echo ($solic_ap_hora_inicio) ? date("H:i", strtotime($solic_ap_hora_inicio)) : ''; ?>" disabled>
                                      </div>
                                    </div>

                                    <div class="col-md-6 col-xl-4 col-xxl-3">
                                      <div class="mb-4">
                                        <label class="form-label">Horário final</label>
                                        <input type="time" class="form-control" id="solic_ap_hora_fim" value="<?php echo ($solic_ap_hora_fim) ? date("H:i", strtotime($solic_ap_hora_fim)) : ''; ?>" disabled>
                                      </div>
                                    </div>

                                    <!-- <div class="row my-3">
                                  <div class="col-12 mb-2">
                                    <label class="form-label">Materiais, equipamentos e insumos necessários para a realização da aula nos espaços de prática</label>
                                    <div class="check_item_container hstack gap-2 flex-wrap">
                                      <input type="checkbox" class="btn-check check_formulario_check" id="checkMat1" value="1" <?= ($solic_ap_tipo_material == 1) ? 'checked' : ''; ?> disabled>
                                      <label class="check_item check_formulario" for="checkMat1">Formulário de planejamento</label>

                                      <input type="checkbox" class="btn-check check_formulario_check" id="checkMat2" value="2" <?= ($solic_ap_tipo_material == 2) ? 'checked' : ''; ?> disabled>
                                      <label class="check_item check_formulario" for="checkMat2">Título da aula</label>

                                      <input type="checkbox" class="btn-check check_formulario_check" id="checkMat3" value="3" <?= ($solic_ap_tipo_material == 3) ? 'checked' : ''; ?> disabled>
                                      <label class="check_item check_formulario" for="checkMat3">Descrição</label>
                                    </div>
                                  </div>
                                </div> -->


                                    <?php if ($solic_ap_tipo_material == 1) { ?>

                                      <div class="col-12" id="file_ancora">
                                        <div class="mb-4">
                                          <label class="form-label">Formulário de planejamento de atividades de práticas nos laboratórios de ensino</label>
                                          <div class="mt-0 mb-2">

                                            <?php $sql = $conn->prepare("SELECT * FROM solicitacao_arq WHERE sarq_solic_id = :sarq_solic_id");
                                            $sql->execute(['sarq_solic_id' => $solic_id]);
                                            while ($arq = $sql->fetch(PDO::FETCH_ASSOC)) {
                                              $sarq_id        = $arq['sarq_id'];
                                              $sarq_solic_id  = $arq['sarq_solic_id'];
                                              $sarq_categoria = $arq['sarq_categoria'];
                                              $sarq_arquivo   = $arq['sarq_arquivo'];
                                            ?>
                                              <div class="result_file">
                                                <div class="result_file_name p-1"><a href="uploads/solicitacoes/<?= $solic_codigo . '/' . $arq['sarq_arquivo'] ?>" target="_blank"><?= $arq['sarq_arquivo'] ?></a></div>
                                              </div>
                                            <?php } ?>

                                          </div>
                                        </div>
                                      </div>

                                    <?php } elseif ($solic_ap_tipo_material == 2) { ?>

                                      <div class="col-12">
                                        <div class="mb-4">
                                          <label class="form-label">Informe o título da(s) aula(s)</label>
                                          <textarea class="form-control" id="solic_ap_tit_aulas" rows="5" disabled><?= $solic_ap_tit_aulas ?></textarea>
                                        </div>
                                      </div>

                                    <?php } else { ?>

                                      <div class="col-12">
                                        <div class="mb-4">
                                          <label class="form-label">Descreva os materiais, insumos e equipamentos, com suas respectivas quantidades, que serão necessários para a realização da aula no espaço de prática</label>
                                          <textarea class="form-control" id="solic_ap_quant_material" rows="5" disabled><?= $solic_ap_quant_material ?></textarea>
                                        </div>
                                      </div>

                                    <?php } ?>


                                    <?php if (!empty($solic_ap_obs)) { ?>
                                      <div class="col-12">
                                        <div class="mb-0">
                                          <label class="form-label">Observações</label>
                                          <textarea class="form-control" rows="5" disabled><?= $solic_ap_obs ?></textarea>
                                        </div>
                                      </div>
                                    <?php } ?>


                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                      <?php } ?>

                      <?php if ($solic_at_aula_teorica == 1) { ?>

                        <div class="tab-pane" id="profile2" role="tabpanel">
                          <div class="acordion_roxo accordion custom-accordionwithicon accordion-flush accordion-fill-success" id="accordionFill_cp">


                            <div class="accordion-item">
                              <h2 class="accordion-header" id="accordionFill2">
                                <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#accor_fill2" aria-expanded="true" aria-controls="accor_fill2">AULAS TEÓRICAS</button>
                              </h2>
                              <div id="accor_fill2" class="accordion-collapse collapse show" aria-labelledby="accordionFill2" data-bs-parent="#accordionFill_cp">
                                <div class="accordion-body">

                                  <div class="row grid gx-3">
                                    <div class="col-md-6 col-xl-4 col-xxl-3">
                                      <div class="mb-3">
                                        <label class="form-label">Campus</label>
                                        <select class="form-select text-uppercase" id="solic_at_campus" disabled>
                                          <option value="<?= $campus_teorico_id  ?>"><?= $campus_teorico_nome ?></option>
                                        </select>
                                      </div>
                                    </div>

                                    <div class="col-md-6 col-xl-4 col-xxl-3">
                                      <div class="mb-3">
                                        <label class="form-label">Quantidade de sala(s) / laboratório(s) de informática</label>
                                        <select class="form-select text-uppercase" id="solic_at_quant_sala" disabled>
                                          <option value="<?= $cst_id  ?>"><?= $cst_sala ?></option>
                                        </select>
                                      </div>
                                    </div>

                                    <div class="col-md-6 col-xl-4 col-xxl-3">
                                      <div class="mb-3">
                                        <label class="form-label">Número estimado de participantes</label>
                                        <input type="text" class="form-control text-uppercase" id="solic_at_quant_particip" value="<?= $solic_at_quant_particip ?>" disabled>
                                      </div>
                                    </div>
                                  </div>

                                  <div class="row grid gx-3">
                                    <div class="col-xl-12 col-xxl-3">
                                      <div class="mb-4">
                                        <label class="form-label">Tipo da reserva</label>
                                        <div class="check_item_container hstack gap-2 flex-wrap">
                                          <input type="checkbox" class="btn-check check_formulario_check" id="solic_at_tipo_reserva1" value="1" <?php echo ($solic_at_tipo_reserva == 1) ? 'checked' : ''; ?> disabled>
                                          <label class="check_item check_formulario" for="solic_at_tipo_reserva1">Esporádica</label>

                                          <input type="checkbox" class="btn-check check_formulario_check" id="solic_at_tipo_reserva2" value="2" <?php echo ($solic_at_tipo_reserva == 2) ? 'checked' : ''; ?> disabled>
                                          <label class="check_item check_formulario" for="solic_at_tipo_reserva2">Fixa</label>
                                        </div>
                                      </div>
                                    </div>

                                    <?php if (!$solic_at_data_reserva) { ?>

                                      <div class="col-xl-12 col-xxl-9">
                                        <div class="mb-4">
                                          <label class="form-label">Dia(s) da semana</label>
                                          <div class="check_item_container hstack gap-2 flex-wrap">
                                            <?php $dias = explode(", ", $solic_at_dia_reserva);
                                            $sql = $conn->prepare("SELECT week_id, week_dias FROM conf_dias_semana");
                                            $sql->execute();
                                            while ($result = $sql->fetch(PDO::FETCH_ASSOC)) { ?>
                                              <input type="checkbox" class="btn-check check_formulario_check" id="dias_semana<?= $result['week_id'] ?>" value="<?= $result['week_id'] ?>" <?= $solic_at_dia_reserva_checked = in_array($result['week_id'], $dias) ? "checked" : ""; ?> disabled>
                                              <label class="check_item check_formulario" for="dias_semana<?= $result['week_id'] ?>"><?= $result['week_dias'] ?></label>
                                            <?php } ?>
                                          </div>
                                        </div>
                                      </div>

                                    <?php } else { ?>
                                      <div class="col-12" id="camp_info_pratic_datas" style="display: none;">
                                        <div class="mb-4">
                                          <label class="form-label">Data(s) da reserva</label>
                                          <textarea class="form-control" id="solic_ap_data_reserva" rows="5" disabled><?= $solic_ap_data_reserva ?></textarea>
                                        </div>
                                      </div>
                                    <?php } ?>

                                    <div class="col-md-6 col-xl-4 col-xxl-3">
                                      <div class="mb-3">
                                        <label class="form-label">Horário inicial</label>
                                        <input type="time" class="form-control" id="solic_at_hora_inicio" value="<?php echo ($solic_at_hora_inicio) ? date("H:i", strtotime($solic_at_hora_inicio)) : ''; ?>" disabled>
                                      </div>
                                    </div>

                                    <div class="col-md-6 col-xl-4 col-xxl-3">
                                      <div class="mb-3">
                                        <label class="form-label">Horário final</label>
                                        <input type="time" class="form-control" id="solic_at_hora_fim" value="<?php echo ($solic_at_hora_fim) ? date("H:i", strtotime($solic_at_hora_fim)) : ''; ?>" disabled>
                                      </div>
                                    </div>

                                    <?php if (!empty($solic_at_recursos)) { ?>
                                      <div class="col-12">
                                        <div class="mb-4">
                                          <label class="form-label">Recursos audiovisuais adicionais</label>
                                          <textarea class="form-control" rows="5" disabled><?= $solic_at_recursos ?></textarea>
                                        </div>
                                      </div>
                                    <?php } ?>

                                    <?php if (!empty($solic_at_obs)) { ?>
                                      <div class="col-12">
                                        <div class="mb-4">
                                          <label class="form-label">Observações</label>
                                          <textarea class="form-control" rows="5" disabled><?= $solic_at_obs ?></textarea>
                                        </div>
                                      </div>
                                    <?php } ?>

                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                      <?php } ?>

                    </div>
                  </div>
                </div>


                <?php if (!empty($solic_ap_espaco)) { ?>

                  <div class="card">
                    <div class="card-header">
                      <div class="row align-items-center">
                        <div class="col-sm-12 text-sm-start text-center">
                          <h5 class="card-title mb-0">Espaço Sugerido</h5>
                        </div>
                      </div>
                    </div>

                    <div class="card-body p-0">
                      <div class="table-responsive">
                        <table id="res_esp_sugerido" class="table dt-responsive nowrap align-middle mb-0" style="width:100%">
                          <thead>
                            <tr>
                              <th><span class="me-3">ID Local</span></th>
                              <th><span class="me-3">Local Reservado</span></th>
                              <th><span class="me-3">Tipo de Espaço</span></th>
                              <th><span class="me-3">Campus</span></th>
                              <th><span class="me-3">Pavilhão</span></th>
                              <th><span class="me-3">Andar</span></th>
                              <th><span class="me-3">Cap. Máx.</span></th>
                              <th><span class="me-3">Cap. Méd.</span></th>
                              <th><span class="me-3">Cap. Mín.</span></th>
                            </tr>
                          </thead>
                          <tbody>

                            <?php
                            $stmt = $conn->prepare("SELECT * FROM solicitacao WHERE solic_id = ?");
                            $stmt->execute([$_GET['i']]);
                            $solicitacao = $stmt->fetch(PDO::FETCH_ASSOC);

                            if ($solicitacao && !empty($solicitacao['solic_ap_espaco'])) {

                              // SEPARA OS IDs DOS ESPAÇOS
                              $espaco_ids = explode(', ', $solicitacao['solic_ap_espaco']);

                              // PREPARA PLACEHOLDERS PARA IN
                              $placeholders = implode(', ', array_fill(0, count($espaco_ids), '?'));

                              // BUSCA OS DADOS DOS ESPAÇOS
                              $sql = "SELECT esp_codigo, esp_nome_local, UPPER(tipesp_tipo_espaco) AS tipesp_tipo_espaco, UPPER(uni_unidade) AS uni_unidade, UPPER(pav_pavilhao) AS pav_pavilhao, UPPER(and_andar) AS and_andar, esp_quant_maxima, esp_quant_media, esp_quant_minima
                                            FROM espaco
                                            INNER JOIN tipo_espaco ON tipo_espaco.tipesp_id = espaco.esp_tipo_espaco
                                            INNER JOIN unidades ON unidades.uni_id = espaco.esp_unidade
                                            LEFT JOIN pavilhoes ON pavilhoes.pav_id = espaco.esp_pavilhao
                                            INNER JOIN andares ON andares.and_id = espaco.esp_andar
                                            INNER JOIN status ON status.st_id = espaco.esp_status
                                            WHERE esp_id IN ($placeholders)";
                              $stmt = $conn->prepare($sql);
                              $stmt->execute($espaco_ids);
                              $espacos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                              foreach ($espacos as $espaco) { ?>
                                <tr>
                                  <th><?= htmlspecialchars($espaco['esp_codigo']) ?></th>
                                  <td><?= htmlspecialchars($espaco['esp_nome_local']) ?></td>
                                  <td><?= htmlspecialchars($espaco['tipesp_tipo_espaco']) ?></td>
                                  <td><?= htmlspecialchars($espaco['uni_unidade']) ?></td>
                                  <td><?= htmlspecialchars($espaco['pav_pavilhao']) ?></td>
                                  <td><?= htmlspecialchars($espaco['and_andar']) ?></td>
                                  <td><?= htmlspecialchars($espaco['esp_quant_maxima']) ?></td>
                                  <td><?= htmlspecialchars($espaco['esp_quant_media']) ?></td>
                                  <td><?= htmlspecialchars($espaco['esp_quant_minima']) ?></td>
                                </tr>
                            <?php }
                            } ?>

                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>

                <?php } ?>

                <?php if ($solic_ap_aula_pratica == 1) { ?>

                  <div class="card card_dados_info">
                    <div class="card-header">
                      <div class="row align-items-center" id="ancora_dados_projetos">
                        <div class="col-lg-12">
                          <h5 class="card-title m-0 ps-2">Informações da Reserva <span class="d-inline-block ident_aula_solic ident_aula_solic_color_azul ms-sm-2 ms-0 mt-2 mt-sm-0">Aulas Práticas</span></h5>
                        </div>
                      </div>
                    </div>

                    <div class="card-body p-4">
                      <div class="row grid gx-3">

                        <div class="col-sm-6 col-xl-4 col-xxl-3">
                          <label>Campus</label>
                          <p><?= $campus_pratico_nome ?></p>
                          <hr>
                        </div>

                        <div class="col-sm-6 col-xl-4 col-xxl-3">
                          <label>Quantidade de turma</label>
                          <p><?= $ctp_turma ?></p>
                          <hr>
                        </div>

                        <div class="col-sm-6 col-xl-4 col-xxl-3">
                          <label>Nº estimado de participantes</label>
                          <p><?= $solic_ap_quant_particip ?></p>
                          <hr>
                        </div>

                        <div class="col-sm-6 col-xl-4 col-xxl-3">
                          <label>Tipo da reserva</label>
                          <p><?= $ctr_tipo_reserva ?></p>
                          <hr>
                        </div>

                        <div class="col-sm-6 col-xl-4 col-xxl-3">
                          <label>Horário inicial</label>
                          <p><?= date("H:i", strtotime($solic_ap_hora_inicio)) ?></p>
                          <hr>
                        </div>

                        <div class="col-sm-6 col-xl-4 col-xxl-3">
                          <label>Horário final</label>
                          <p><?= date("H:i", strtotime($solic_ap_hora_fim)) ?></p>
                          <hr>
                        </div>

                        <div class="col-xl-12 col-xxl-6 <?= empty($solic_ap_dia_reserva) ? 'd-none' : '' ?>">
                          <div class="mb-4">
                            <!-- <label>Dia(s) da semana</label> -->
                            <div class="check_item_container hstack gap-2 flex-wrap mt-2">
                              <?php $dias = explode(", ", $solic_ap_dia_reserva);
                              $sql = $conn->prepare("SELECT week_id, week_dias FROM conf_dias_semana");
                              $sql->execute();
                              while ($result = $sql->fetch(PDO::FETCH_ASSOC)) { ?>
                                <input type="checkbox" class="btn-check check_formulario_check" id="dias_semana<?= $result['week_id'] ?>" value="<?= $result['week_id'] ?>" <?= $solic_ap_dia_reserva_checked = in_array($result['week_id'], $dias) ? "checked" : ""; ?> disabled>
                                <label class="check_item check_formulario" for="dias_semana<?= $result['week_id'] ?>"><?= $result['week_dias'] ?></label>
                              <?php } ?>
                            </div>
                          </div>
                        </div>

                        <?php try {
                          $sql = $conn->prepare("SELECT * FROM solicitacao_arq WHERE sarq_solic_id = :sarq_solic_id");
                          $sql->execute(['sarq_solic_id' => $solic_id]);
                          $result_arq_ap = $sql->fetchAll(PDO::FETCH_ASSOC);
                        } catch (PDOException $e) {
                          // echo "Erro: " . $e->getMessage();
                          echo "Erro ao tentar recuperar os dados";
                        } ?>

                        <div class="col-12 mb-4 <?= empty($result_arq_ap) ? 'd-none' : '' ?>">
                          <label>Formulário de planejamento de atividades de práticas nos laboratórios de ensino</label>

                          <?php foreach ($result_arq_ap as $res_arq_ap) : ?>
                            <div class="result_file">
                              <div class="result_file_name py-1"><a href="../uploads/solicitacoes/<?= $solic_codigo . '/' . $res_arq_ap['sarq_arquivo'] ?>" target="_blank"><?= $res_arq_ap['sarq_arquivo'] ?></a></div>
                            </div>
                          <?php endforeach; ?>

                        </div>

                        <div class="col-12 <?= empty($solic_ap_data_reserva) ? 'd-none' : '' ?>">
                          <label>Data(s) da reserva</label>
                          <p class="campo_textarea"><?= $solic_ap_data_reserva ?></p>
                          <hr>
                        </div>

                        <div class="col-12 <?= empty($solic_ap_tit_aulas) ? 'd-none' : '' ?>">
                          <label>Informe o título da(s) aula(s)</label>
                          <p class="campo_textarea"><?= $solic_ap_tit_aulas ?></p>
                          <hr>
                        </div>

                        <div class="col-12 <?= empty($solic_ap_quant_material) ? 'd-none' : '' ?>">
                          <label>Descreva os materiais, insumos e equipamentos, com suas respectivas quantidades, que serão necessários para a realização da aula no espaço de prática</label>
                          <p class="campo_textarea"><?= $solic_ap_quant_material ?></p>
                          <hr>
                        </div>

                        <div class="col-12 <?= empty($solic_ap_obs) ? 'd-none' : '' ?>">
                          <label>Observações</label>
                          <p class="campo_textarea"><?= $solic_ap_obs ?></p>
                        </div>

                      </div>

                    </div>
                  </div>

                <?php } ?>


                <?php if ($solic_at_aula_teorica == 1) { ?>

                  <div class="card card_dados_info">
                    <div class="card-header">
                      <div class="row align-items-center" id="ancora_dados_projetos">
                        <div class="col-lg-12">
                          <h5 class="card-title m-0 ps-2">Informações da Reserva <span class="d-inline-block ident_aula_solic ident_aula_solic_color_roxo ms-sm-2 ms-0 mt-2 mt-sm-0">Aulas Teóricas</span></h5>
                        </div>
                      </div>
                    </div>

                    <div class="card-body p-4">
                      <div class="row grid gx-3">

                        <div class="col-md-6 col-xl-4 col-xxl-3">
                          <label>Campus</label>
                          <p><?= $campus_teorico_nome ?></p>
                          <hr>
                        </div>

                        <div class="col-md-6 col-xl-4 col-xxl-3">
                          <label>Quantidade de sala(s) / Laboratório(s) de informática</label>
                          <p><?= $cst_sala ?></p>
                          <hr>
                        </div>

                        <div class="col-md-6 col-xl-4 col-xxl-3">
                          <label>Nº estimado de participantes</label>
                          <p><?= $solic_at_quant_particip ?></p>
                          <hr>
                        </div>

                        <div class="col-md-6 col-xl-4 col-xxl-3">
                          <label>Tipo da reserva</label>
                          <p><?= $ctr_tipo_reserva ?></p>
                          <hr>
                        </div>

                        <div class="col-md-6 col-xl-4 col-xxl-3">
                          <label>Horário inicial</label>
                          <p><?= date("H:i", strtotime($solic_at_hora_inicio)) ?></p>
                          <hr>
                        </div>

                        <div class="col-md-6 col-xl-4 col-xxl-3">
                          <label>Horário final</label>
                          <p><?= date("H:i", strtotime($solic_at_hora_fim)) ?></p>
                          <hr>
                        </div>

                        <div class="col-xl-12 col-xxl-6  <?= empty($solic_at_dia_reserva) ? 'd-none' : '' ?>">
                          <div class="mb-4">
                            <!-- <label>Dia(s) da semana</label> -->
                            <div class="check_item_container hstack gap-2 flex-wrap mt-2">
                              <?php $dias = explode(", ", $solic_at_dia_reserva);
                              $sql = $conn->prepare("SELECT week_id, week_dias FROM conf_dias_semana");
                              $sql->execute();
                              while ($result = $sql->fetch(PDO::FETCH_ASSOC)) { ?>
                                <input type="checkbox" class="btn-check check_formulario_check" id="dias_semana<?= $result['week_id'] ?>" value="<?= $result['week_id'] ?>" <?= $solic_at_dia_reserva_checked = in_array($result['week_id'], $dias) ? "checked" : ""; ?> disabled>
                                <label class="check_item check_formulario" for="dias_semana<?= $result['week_id'] ?>"><?= $result['week_dias'] ?></label>
                              <?php } ?>
                            </div>
                          </div>
                        </div>

                        <div class="col-12 <?= empty($solic_at_data_reserva) ? 'd-none' : '' ?>">
                          <label>Data(s) da reserva</label>
                          <p class="campo_textarea"><?= $solic_at_data_reserva ?></p>
                          <hr>
                        </div>

                        <div class="col-12 <?= empty($solic_at_recursos) ? 'd-none' : '' ?>">
                          <label>Recursos audiovisuais adicionais</label>
                          <p class="campo_textarea"><?= $solic_at_recursos ?></p>
                          <hr>
                        </div>

                        <div class="col-12 <?= empty($solic_at_obs) ? 'd-none' : '' ?>">
                          <label>Observações</label>
                          <p class="campo_textarea"><?= $solic_at_obs ?></p>
                        </div>

                      </div>

                    </div>
                  </div>

                <?php } ?>





                <?php
                // SE SOLICITAÇÃO FORI DEFERIDA, MOSTRA FORMULÁRIO PARA RESERVAR ESPAÇO
                $solic_id = $_GET['i'];
                $query = "SELECT * FROM solicitacao_status WHERE solic_sta_solic_id = :solic_sta_solic_id AND solic_sta_status = 5";
                $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
                $stmt->execute([':solic_sta_solic_id' => $solic_id]);
                $row_count = $stmt->rowCount();
                ?>
                <?php if ($row_count) {  ?>

                  <div class="card" id="ancora_reservas_confirmadas">
                    <div class="card-header">
                      <div class="row align-items-center">
                        <div class="col-sm-6 text-sm-start text-center">
                          <h5 class="card-title mb-0">Reservas Confirmadas</h5>
                        </div>
                        <div class="col-sm-6 d-flex align-items-center d-flex justify-content-sm-end justify-content-center">
                          <button class="btn botao botao_amarelo waves-effect mt-3 mt-sm-0" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_cad_espaco">+ Cadastrar Reserva</button>
                        </div>
                      </div>
                    </div>

                    <style>
                      .table tbody th.bg_table_fix_vermelho,
                      .table tbody td.bg_table_fix_laranja,
                      .table tbody td.bg_table_fix_verde,
                      .table tbody td.bg_table_fix_azul,
                      .table tbody td.bg_table_fix_roxo,
                      .table tbody td.bg_table_fix_rosa,
                      .table tbody td.bg_table_fix_cinza {
                        border-color: var(--branco) !important;
                      }

                      .dataTables_scrollHead {
                        z-index: 10;
                        margin-bottom: -20px;
                      }



                      @media (width < 1200px) {
                        .dtfc-fixed-left {
                          position: static !important;
                        }
                      }
                    </style>

                    <div class="card-body p-0">
                      <div class="table-responsive">
                        <table id="tab_reserva_confirm" class="table align-middle" style="width:100%">
                          <thead>

                            <tr>
                              <th width="20px" class="rounded-start sorting_disabled" rowspan="1" colspan="1" aria-label=""><input type="checkbox" class="form-check-input" id="marcarTodos"></th>
                              <th><span class="me-2">Data</span></th>
                              <th><span class="me-2">Dia</span></th>
                              <th><span class="me-2">Mês</span></th>
                              <th><span class="me-2">Ano</span></th>
                              <th><span class="me-2">Início</span></th>
                              <th><span class="me-2">Fim</span></th>
                              <th><span class="me-2">Turno</span></th>
                              <th><span class="me-2">Tipo de Aula</span></th>
                              <th><span class="me-2">Curso</span></th>
                              <th><span class="me-2">Semestre</span></th>
                              <th><span class="me-2">Componente Curricular/Atividade</span></th>
                              <th><span class="me-2">Módulo</span></th>
                              <th><span class="me-2">Professor</span></th>
                              <th><span class="me-2">Título Aula</span></th>
                              <th><span class="me-2">Tipo Reserva</span></th>
                              <th><span class="me-2">Recursos</span></th>
                              <th><span class="me-2">Recursos Adicionais</span></th>
                              <th><span class="me-2">Observações</span></th>
                              <th><span class="me-2">Nº Pessoas</span></th>
                              <th><span class="me-2">ID Local</span></th>
                              <th><span class="me-2">Local Reservado</span></th>
                              <th><span class="me-2">Andar</span></th>
                              <th><span class="me-2">Pavilhão</span></th>
                              <th><span class="me-2">Campus</span></th>
                              <th><span class="me-2">Tipo de Sala</span></th>
                              <th><span class="me-2">Capacidade</span></th>
                              <th><span class="me-2">Confirmado por</span></th>
                              <th><span class="me-2">Data Solicitação</span></th>
                              <th><span class="me-2">Data Reserva</span></th>
                              <th><span class="me-2">ID Solicitação</span></th>
                              <th><span class="me-2">CH Programada</span></th>
                              <th><span class="me-2">Início Realizado</span></th>
                              <th><span class="me-2">Fim Realizado</span></th>
                              <th><span class="me-2">CH Realizada</span></th>
                              <th><span class="me-2">CH Faltante</span></th>
                              <th><span class="me-2">CH Mais</span></th>
                              <th width="20px"></th>
                            </tr>

                          </thead>
                          <tbody>

                            <?php
                            try {
                              $stmt = $conn->prepare("SELECT * FROM reservas
                                                      INNER JOIN solicitacao ON solicitacao.solic_id = reservas.res_solic_id
                                                      INNER JOIN conf_dias_semana ON conf_dias_semana.week_id = reservas.res_dia_semana
                                                      LEFT JOIN cursos ON cursos.curs_id = reservas.res_curso
                                                      LEFT JOIN conf_semestre ON conf_semestre.cs_id = reservas.res_semestre
                                                      LEFT JOIN componente_curricular ON componente_curricular.compc_id = reservas.res_componente_atividade
                                                      INNER JOIN conf_tipo_reserva ON conf_tipo_reserva.ctr_id = reservas.res_tipo_reserva
                                                      INNER JOIN conf_tipo_aula ON conf_tipo_aula.cta_id = reservas.res_tipo_aula
                                                      INNER JOIN espaco ON espaco.esp_id = reservas.res_espaco_id
                                                      INNER JOIN tipo_espaco ON tipo_espaco.tipesp_id = espaco.esp_tipo_espaco
                                                      LEFT JOIN pavilhoes ON pavilhoes.pav_id = espaco.esp_pavilhao
                                                      INNER JOIN andares ON andares.and_id = espaco.esp_andar
                                                      LEFT JOIN unidades ON unidades.uni_id = espaco.esp_unidade
                                                      INNER JOIN admin ON admin.admin_id = reservas.res_user_id
                                                      WHERE solic_id = :solic_id
                                                    ");
                              $stmt->execute([':solic_id' => $_GET['i']]);
                              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                extract($row);

                                //CONFIGURAÇÃO DO PERFIL
                                if ($res_tipo_aula == 1) {
                                  $tipo_aula_color = 'bg_info_azul_escuro';
                                } else {
                                  $tipo_aula_color = 'bg_info_laranja';
                                }

                            ?>
                                <tr>
                                  <td><input type="checkbox" class="form-check-input checkbox" name="exc_selecionados[]" value="<?= $res_id ?>"></td>
                                  <th scope="row" nowrap="nowrap" class="bg_table_fix_vermelho"><span class="hide_data"><?= date('Ymd', strtotime($res_data)) ?></span><?= date('d/m/Y', strtotime($res_data)) ?></th>
                                  <td scope="row" nowrap="nowrap" class="bg_table_fix_laranja"><?= substr($week_dias, 0, 3) ?></td>
                                  <td scope="row" nowrap="nowrap" class="bg_table_fix_verde"><?= $res_mes ?></td>
                                  <td scope="row" nowrap="nowrap" class="bg_table_fix_azul"><?= $res_ano ?></td>
                                  <td scope="row" nowrap="nowrap" class="bg_table_fix_roxo"><?= date("H:i", strtotime($res_hora_inicio)) ?></td>
                                  <td scope="row" nowrap="nowrap" class="bg_table_fix_rosa"><?= date("H:i", strtotime($res_hora_fim)) ?></td>
                                  <td scope="row" nowrap="nowrap" class="bg_table_fix_cinza"><?= $res_turno ?></td>
                                  <td scope="row" nowrap="nowrap"><span class="badge <?= $tipo_aula_color ?>"><?= $cta_tipo_aula ?></span></td>
                                  <td scope="row" nowrap="nowrap"><?= $curs_curso ?></td>
                                  <td scope="row" nowrap="nowrap"><?= $cs_semestre ?></td>
                                  <td scope="row" nowrap="nowrap"><?= $compc_componente ?></td>
                                  <td scope="row" nowrap="nowrap"><?= $res_modulo ?></td>
                                  <td scope="row" nowrap="nowrap"><?= $res_professor ?></td>
                                  <td scope="row" nowrap="nowrap"><?= $res_titulo_aula ?></td>
                                  <td scope="row" nowrap="nowrap" class="text-uppercase"><?= $ctr_tipo_reserva ?></td>
                                  <td scope="row" nowrap="nowrap"></td>
                                  <td scope="row" nowrap="nowrap">KIT DE TRANSMISSÃO</td>
                                  <td scope="row" nowrap="nowrap">KIT DE TRANSMISSÃO / 1 MICROFONE COM FIO</td>
                                  <td scope="row" nowrap="nowrap"><?= $res_quant_pessoas ?></td>
                                  <td scope="row" nowrap="nowrap"><?= $esp_codigo ?></td>
                                  <td scope="row" nowrap="nowrap"><?= $esp_nome_local ?></td>
                                  <td scope="row" nowrap="nowrap" class="text-uppercase"><?= $and_andar ?></td>
                                  <td scope="row" nowrap="nowrap" class="text-uppercase"><?= $pav_pavilhao ?></td>
                                  <td scope="row" nowrap="nowrap" class="text-uppercase"><?= $uni_unidade ?></td>
                                  <td scope="row" nowrap="nowrap" class="text-uppercase"><?= $tipesp_tipo_espaco ?></td>
                                  <td scope="row" nowrap="nowrap"><?= $esp_quant_maxima ?></td>
                                  <td scope="row" nowrap="nowrap"><?= $admin_nome ?></td>
                                  <td scope="row" nowrap="nowrap"><?= date('d/m/Y', strtotime($solic_data_cad)) ?></td>
                                  <td scope="row" nowrap="nowrap"><?= date('d/m/Y', strtotime($res_data_cad)) ?></td>
                                  <td scope="row" nowrap="nowrap"><?= $solic_codigo ?></td>
                                  <td scope="row" nowrap="nowrap">20:00</td>
                                  <td scope="row" nowrap="nowrap">14:00</td>
                                  <td scope="row" nowrap="nowrap">16:00</td>
                                  <td scope="row" nowrap="nowrap">02:00</td>
                                  <td scope="row" nowrap="nowrap">00:00</td>
                                  <td scope="row" nowrap="nowrap">00:00</td>
                                  <td class="text-end">
                                    <div class="dropdown drop_tabela d-inline-block">
                                      <button class="btn btn_soft_verde_musgo btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ri-more-fill align-middle"></i>
                                      </button>
                                      <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_espaco" data-bs-res_id="<?= $res_id ?>"
                                            data-bs-res_campus="<?= $res_campus ?>"
                                            data-bs-res_espaco_id_cabula="<?= $res_espaco_id ?>"
                                            data-bs-res_espaco_id_brotas="<?= $res_espaco_id ?>"
                                            data-bs-res_quant_pessoas="<?= $res_quant_pessoas ?>"
                                            data-bs-res_recursos="<?= $res_recursos ?>"
                                            data-bs-res_recursos_add="<?= $res_recursos_add ?>"
                                            data-bs-res_obs="<?= $res_obs ?>"
                                            title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                                        <li><a href="controller/controller_reservas.php?acao=deletar&res_id=<?= $res_id ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
                                      </ul>
                                    </div>
                                  </td>
                                </tr>

                            <?php }
                            } catch (PDOException $e) {
                              // echo "Erro: " . $e->getMessage();
                              echo "Erro ao tentar recuperar os dados";
                            } ?>


                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>

                <?php } ?>

              </div>

              <div class="tab-pane fade" id="activities" role="tabpanel">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title mb-3">Activities</h5>
                    <div class="acitivity-timeline">
                      <div class="acitivity-item d-flex">
                        <div class="flex-shrink-0">
                          <img src="assets/images/users/avatar-1.jpg" alt="" class="avatar-xs rounded-circle acitivity-avatar">
                        </div>
                        <div class="flex-grow-1 ms-3">
                          <h6 class="mb-1">Oliver Phillips <span class="badge bg-primary-subtle text-primary align-middle">New</span>
                          </h6>
                          <p class="text-muted mb-2">We talked about a project on
                            linkedin.</p>
                          <small class="mb-0 text-muted">Today</small>
                        </div>
                      </div>
                      <div class="acitivity-item py-3 d-flex">
                        <div class="flex-shrink-0 avatar-xs acitivity-avatar">
                          <div class="avatar-title bg-success-subtle text-success rounded-circle">
                            N
                          </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                          <h6 class="mb-1">Nancy Martino <span class="badge bg-secondary-subtle text-secondary align-middle">In
                              Progress</span></h6>
                          <p class="text-muted mb-2"><i class="ri-file-text-line align-middle ms-2"></i>
                            Create new project Buildng product</p>
                          <div class="avatar-group mb-2">
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Christi">
                              <img src="assets/images/users/avatar-4.jpg" alt="" class="rounded-circle avatar-xs">
                            </a>
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Frank Hook">
                              <img src="assets/images/users/avatar-3.jpg" alt="" class="rounded-circle avatar-xs">
                            </a>
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title=" Ruby">
                              <div class="avatar-xs">
                                <div class="avatar-title rounded-circle bg-light text-primary">
                                  R
                                </div>
                              </div>
                            </a>
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="more">
                              <div class="avatar-xs">
                                <div class="avatar-title rounded-circle">
                                  2+
                                </div>
                              </div>
                            </a>
                          </div>
                          <small class="mb-0 text-muted">Yesterday</small>
                        </div>
                      </div>
                      <div class="acitivity-item py-3 d-flex">
                        <div class="flex-shrink-0">
                          <img src="assets/images/users/avatar-2.jpg" alt="" class="avatar-xs rounded-circle acitivity-avatar">
                        </div>
                        <div class="flex-grow-1 ms-3">
                          <h6 class="mb-1">Natasha Carey <span class="badge bg-success-subtle text-success align-middle">Completed</span>
                          </h6>
                          <p class="text-muted mb-2">Adding a new event with
                            attachments</p>
                          <div class="row">
                            <div class="col-xxl-4">
                              <div class="row border border-dashed gx-2 p-2 mb-2">
                                <div class="col-4">
                                  <img src="assets/images/small/img-2.jpg" alt="" class="img-fluid rounded">
                                </div>
                                <!--end col-->
                                <div class="col-4">
                                  <img src="assets/images/small/img-3.jpg" alt="" class="img-fluid rounded">
                                </div>
                                <!--end col-->
                                <div class="col-4">
                                  <img src="assets/images/small/img-4.jpg" alt="" class="img-fluid rounded">
                                </div>
                                <!--end col-->
                              </div>
                              <!--end row-->
                            </div>
                          </div>
                          <small class="mb-0 text-muted">25 Nov</small>
                        </div>
                      </div>
                      <div class="acitivity-item py-3 d-flex">
                        <div class="flex-shrink-0">
                          <img src="assets/images/users/avatar-6.jpg" alt="" class="avatar-xs rounded-circle acitivity-avatar">
                        </div>
                        <div class="flex-grow-1 ms-3">
                          <h6 class="mb-1">Bethany Johnson</h6>
                          <p class="text-muted mb-2">added a new member to velzon
                            dashboard</p>
                          <small class="mb-0 text-muted">19 Nov</small>
                        </div>
                      </div>
                      <div class="acitivity-item py-3 d-flex">
                        <div class="flex-shrink-0">
                          <div class="avatar-xs acitivity-avatar">
                            <div class="avatar-title rounded-circle bg-danger-subtle text-danger">
                              <i class="ri-shopping-bag-line"></i>
                            </div>
                          </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                          <h6 class="mb-1">Your order is placed <span class="badge bg-danger-subtle text-danger align-middle ms-1">Out
                              of Delivery</span></h6>
                          <p class="text-muted mb-2">These customers can rest assured
                            their order has been placed.</p>
                          <small class="mb-0 text-muted">16 Nov</small>
                        </div>
                      </div>
                      <div class="acitivity-item py-3 d-flex">
                        <div class="flex-shrink-0">
                          <img src="assets/images/users/avatar-7.jpg" alt="" class="avatar-xs rounded-circle acitivity-avatar">
                        </div>
                        <div class="flex-grow-1 ms-3">
                          <h6 class="mb-1">Lewis Pratt</h6>
                          <p class="text-muted mb-2">They all have something to say
                            beyond the words on the page. They can come across as
                            casual or neutral, exotic or graphic. </p>
                          <small class="mb-0 text-muted">22 Oct</small>
                        </div>
                      </div>
                      <div class="acitivity-item py-3 d-flex">
                        <div class="flex-shrink-0">
                          <div class="avatar-xs acitivity-avatar">
                            <div class="avatar-title rounded-circle bg-info-subtle text-info">
                              <i class="ri-line-chart-line"></i>
                            </div>
                          </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                          <h6 class="mb-1">Monthly sales report</h6>
                          <p class="text-muted mb-2"><span class="text-danger">2 days
                              left</span> notification to submit the monthly sales
                            report. <a href="javascript:void(0);" class="link-warning text-decoration-underline">Reports
                              Builder</a></p>
                          <small class="mb-0 text-muted">15 Oct</small>
                        </div>
                      </div>
                      <div class="acitivity-item d-flex">
                        <div class="flex-shrink-0">
                          <img src="assets/images/users/avatar-8.jpg" alt="" class="avatar-xs rounded-circle acitivity-avatar">
                        </div>
                        <div class="flex-grow-1 ms-3">
                          <h6 class="mb-1">New ticket received <span class="badge bg-success-subtle text-success align-middle">Completed</span>
                          </h6>
                          <p class="text-muted mb-2">User <span class="text-secondary">Erica245</span> submitted a
                            ticket.</p>
                          <small class="mb-0 text-muted">26 Aug</small>
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
    </div>
  </div>
</div>
</div>


<!-- DEFERIR SOLICITAÇÃO -->
<div class="modal fade modal_padrao" id="modal_deferir_solicitacao" tabindex="-1" aria-labelledby="modal_deferir_solicitacao" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header modal_padrao_verde">
        <h5 class="modal-title" id="modal_deferir_solicitacao">Deferir Solicitação</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_solicitacao_analise_status.php?funcao=cad_solic_def" class="needs-validation" id="ValidaBotaoProgressFinal" novalidate>

          <div class="row g-3">

            <input type="hidden" class="form-control" name="solic_id" value="<?= $_GET['i'] ?>" required>
            <!-- <input type="hidden" class="form-control" name="solic_codigo" value="<?= $solic_codigo ?>" required>
            <input type="hidden" class="form-control" name="solic_titulo" value="<?= $solic_titulo ?>" required>
            <input type="hidden" class="form-control" name="sta_an_user_email" value="<?= $user_email ?>" required> -->

            <div class="col-12">
              <div>
                <label class="form-label">Observação</label>
                <textarea class="form-control" name="sta_an_obs" id="" rows="10"></textarea>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect">Deferir</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>



























<style>
  .progress-nav .nav .nav-link.active,
  .progress-nav .nav .nav-link.done,
  .progress-nav .nav .nav-link:focus,
  .progress-nav .nav .nav-link:active {
    border: 0 !important;
    background-color: var(--verde_musgo) !important;
  }

  .progress-nav .nav .nav-link {
    width: 100% !important;
    padding: 0 15px;
  }

  .progress-nav .nav .nav-link {
    background: #CFD6D6 !important
  }

  .progress-bar {
    background-color: var(--verde_musgo) !important;
  }

  .progress,
  .progress-stacked {
    background-color: #CFD6D6 !important
  }
</style>

<!-- MODAL CADASTRA RESERVAS -->
<?php include 'includes/modal/modal_reservas.php'; ?>

<script>
  // ETAPAS DO CADASTRO
  const etapas = ['etapa1', 'etapa2', 'etapa3'];
  let etapaAtual = 0;

  // Mostrar a etapa correta e controlar os botões
  function mostrarEtapa(index) {
    etapas.forEach((id, i) => {
      document.getElementById(id).classList.toggle('d-none', i !== index);
    });

    etapaAtual = index;

    document.getElementById('btnProximo1').style.display = (index === 0) ? 'inline-block' : 'none';
    document.getElementById('btnAnterior2').style.display = (index === 1) ? 'inline-block' : 'none';
    document.getElementById('btnProximo2').style.display = (index === 1) ? 'inline-block' : 'none';
    document.getElementById('btnAnterior3').style.display = (index === 2) ? 'inline-block' : 'none';
    document.getElementById('btnConcluir').style.display = (index === 2) ? 'inline-block' : 'none';
  }

  // Valida uma etapa usando a API nativa de validação
  function validarCamposDaEtapa(index) {
    const etapa = document.getElementById(etapas[index]);
    const inputs = etapa.querySelectorAll('input, textarea, select');
    let valido = true;

    inputs.forEach(input => {
      if (!input.checkValidity()) {
        input.classList.add('is-invalid');
        valido = false;
      } else {
        input.classList.remove('is-invalid');
      }
    });

    return valido;
  }

  // Remove erro Bootstrap ao digitar
  document.querySelectorAll('input, textarea, select').forEach(el => {
    el.addEventListener('input', () => {
      if (el.checkValidity()) {
        el.classList.remove('is-invalid');
      }
    });
  });

  document.getElementById('btnProximo1').addEventListener('click', () => {
    if (validarCamposDaEtapa(0)) mostrarEtapa(1);
  });

  document.getElementById('btnAnterior2').addEventListener('click', () => mostrarEtapa(0));
  document.getElementById('btnProximo2').addEventListener('click', () => {
    if (validarCamposDaEtapa(1)) mostrarEtapa(2);
  });
  document.getElementById('btnAnterior3').addEventListener('click', () => mostrarEtapa(1));

  document.getElementById('form_reserva').addEventListener('submit', function(event) {
    // Verifica se cada etapa está válida
    for (let i = 0; i < etapas.length; i++) {
      if (!validarCamposDaEtapa(i)) {
        event.preventDefault();
        event.stopPropagation();
        mostrarEtapa(i);
        return;
      }
    }
    // Formulário será enviado normalmente
  });

  // Inicializar na etapa 0
  mostrarEtapa(0);


  ////////////////////////
  // ETAPAS DA EDIÇÃO
  ///////////////////////
  // ETAPAS DO MODAL DE EDIÇÃO
  document.addEventListener('DOMContentLoaded', () => {
    const etapasEdit = ['EditEtapa1', 'EditEtapa2', 'EditEtapa3'];
    let etapaEditAtual = 0;

    function mostrarEtapaEdit(index) {
      etapasEdit.forEach((id, i) => {
        const el = document.getElementById(id);
        if (el) el.classList.toggle('d-none', i !== index);
      });

      etapaEditAtual = index;

      document.getElementById('btnEditProximo1').style.display = (index === 0) ? 'inline-block' : 'none';
      document.getElementById('btnEditAnterior2').style.display = (index === 1) ? 'inline-block' : 'none';
      document.getElementById('btnEditProximo2').style.display = (index === 1) ? 'inline-block' : 'none';
      document.getElementById('btnEditAnterior3').style.display = (index === 2) ? 'inline-block' : 'none';
      const btnConcluir = document.getElementById('btnEditConcluir');
      if (btnConcluir) btnConcluir.style.display = (index === 2) ? 'inline-block' : 'none';
    }

    function validarCamposDaEtapaEdit(index) {
      const etapa = document.getElementById(etapasEdit[index]);
      if (!etapa) return false;

      const inputs = etapa.querySelectorAll('input, textarea, select');
      let valido = true;

      inputs.forEach(input => {
        if (!input.checkValidity()) {
          input.classList.add('is-invalid');
          valido = false;
        } else {
          input.classList.remove('is-invalid');
        }
      });

      return valido;
    }

    // Limpa erro ao digitar
    document.querySelectorAll('#form_reserva_edit input, #form_reserva_edit textarea, #form_reserva_edit select').forEach(el => {
      el.addEventListener('input', () => {
        if (el.checkValidity()) {
          el.classList.remove('is-invalid');
        }
      });
    });

    // Botões de navegação
    document.getElementById('btnEditProximo1').addEventListener('click', () => {
      if (validarCamposDaEtapaEdit(0)) mostrarEtapaEdit(1);
    });

    document.getElementById('btnEditAnterior2').addEventListener('click', () => mostrarEtapaEdit(0));

    document.getElementById('btnEditProximo2').addEventListener('click', () => {
      if (validarCamposDaEtapaEdit(1)) mostrarEtapaEdit(2);
    });

    document.getElementById('btnEditAnterior3').addEventListener('click', () => mostrarEtapaEdit(1));

    // Validação no submit
    document.getElementById('form_reserva_edit').addEventListener('submit', function(event) {
      for (let i = 0; i < etapasEdit.length; i++) {
        if (!validarCamposDaEtapaEdit(i)) {
          event.preventDefault();
          event.stopPropagation();
          mostrarEtapaEdit(i);
          return;
        }
      }
    });

    const modalCadastro = bootstrap.Modal.getInstance(document.getElementById('modal_cad_espaco'));
    if (modalCadastro) modalCadastro.hide();

    // Resetar para etapa 0 ao abrir o modal
    document.getElementById('modal_edit_espaco').addEventListener('shown.bs.modal', function() {
      mostrarEtapaEdit(0);
    });
  });
</script>

<script>
  function preencherCampos() {
    const dataInput = document.getElementById('cad_data_reserva').value;
    if (dataInput) {
      // Pega a data e cria um objeto Date no fuso UTC
      const partes = dataInput.split('-');
      const ano = parseInt(partes[0], 10);
      const mes = parseInt(partes[1], 10) - 1; // Meses começam do zero
      const dia = parseInt(partes[2], 10);
      const data = new Date(Date.UTC(ano, mes, dia));

      const meses = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
      const diasSemana = ["7", "1", "2", "3", "4", "5", "6"];

      document.getElementById('cad_mes_reserva').value = meses[data.getUTCMonth()];
      document.getElementById('cad_ano_reserva').value = data.getUTCFullYear();
      document.getElementById('cad_diaSemana_reserva').value = diasSemana[data.getUTCDay()];
      document.getElementById('cad_diaSemanaId_reserva').value = diasSemana[data.getUTCDay()];
    }
  }
</script>

<script>
  $(document).ready(function() {
    // Inicializa Select2 com dropdownParent
    $('#cad_res_componente_atividade').select2({
      dropdownParent: $('#modal_cad_espaco'),
      width: '100%',
      language: {
        noResults: function() {
          return "Dados não encontrados";
        }
      }
    });

    $('#cad_res_curso').change(function() {
      var cursoId = $(this).val();
      var componenteSelecionado = "<?= $solic_row['compc_id'] ?? '' ?>";

      if (cursoId !== "") {
        $.ajax({
          url: '../buscar_componentes.php',
          type: 'POST',
          data: {
            curso_id: cursoId
          },
          success: function(data) {
            $('#cad_res_componente_atividade').html(data);

            if (componenteSelecionado !== "") {
              $('#cad_res_componente_atividade').val(componenteSelecionado).trigger('change');
            }

            // Re-inicializa Select2 com o mesmo dropdownParent
            $('#cad_res_componente_atividade').select2({
              dropdownParent: $('#modal_cad_espaco'),
              width: '100%',
              language: {
                noResults: function() {
                  return "Dados não encontrados";
                }
              }
            });
          }
        });
      } else {
        $('#cad_res_componente_atividade').html('<option value="">Selecione um componente</option>').trigger('change');
      }
    });

    if ($('#cad_res_curso').val() !== "") {
      $('#cad_res_curso').trigger('change');
    }
  });
</script>

<script>
  const localCabulaInput = document.getElementById("cad_reserva_local_cabula");
  const localBrotasInput = document.getElementById("cad_reserva_local_brotas");
  const dataInput = document.getElementById("cad_data_reserva");
  const inicioInput = document.getElementById("cad_res_hora_inicio");
  const fimInput = document.getElementById("cad_res_hora_fim");

  // function verificarConflito() {
  //   const data = dataInput.value;
  //   const inicio = inicioInput.value;
  //   const fim = fimInput.value;

  //   // Só verifica se os três campos estiverem preenchidos
  //   if (data && inicio && fim) {
  //     fetch(`verificar_conflito.php?data=${data}&hora_inicio=${inicio}&hora_fim=${fim}`)
  //       .then(response => response.json())
  //       .then(res => {
  //         if (res.conflito) {
  //           Swal.fire({
  //             icon: 'error',
  //             title: 'Conflito de Horário!',
  //             text: 'Já existe uma reserva nesse intervalo!',
  //           });
  //           btnSubmit.disabled = true;
  //         } else {
  //           btnSubmit.disabled = false;
  //         }
  //       });
  //   }
  // }



  function verificarConflito() {
    const localCabula = localCabulaInput.value;
    const localBrotas = localBrotasInput.value;
    const data = dataInput.value;
    const inicio = inicioInput.value;
    const fim = fimInput.value;

    if (data && inicio && fim) {
      fetch(`verificar_conflito.php?localBrotas=${localBrotas}&localCabula=${localCabula}&data=${data}&hora_inicio=${inicio}&hora_fim=${fim}`)
        .then(response => response.json())
        .then(res => {
          if (res.conflito) {
            Swal.fire({
              icon: 'warning',
              title: 'Horário em Conflito',
              text: 'Já existe uma reserva nesse intervalo. Deseja continuar?',
              showCancelButton: false,
              confirmButtonText: 'OK',
            });
            // 👉 NÃO desativa o botão
          }
        });
    }
  }


  // Detecta mudanças nos campos
  dataInput.addEventListener("change", verificarConflito);
  inicioInput.addEventListener("change", verificarConflito);
  fimInput.addEventListener("change", verificarConflito);
</script>



<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>
<!-- COMPLETO FORM -->
<script src="includes/select/completa_form.js"></script>
<!-- SELECT2 FORM -->
<script src="includes/select/select2.js"></script>