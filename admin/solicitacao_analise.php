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
              <div class="tab-pane active" id="dados_solicitacao" role="tabpanel">

                <div class="card card_dados_info">
                  <div class="card-header">
                    <div class="row align-items-center" id="ancora_dados_projetos">
                      <div class="col-sm-6">
                        <h5 class="card-title m-0 ps-2">Dados da Solicitação</h5>
                      </div>

                      <div class="col-sm-6 <?php echo ($global_admin_perfil != 1) ? 'd-none' : ''; ?>">
                        <nav class="navbar d-flex align-items-center justify-content-sm-end justify-content-center p-0 mt-3 mt-sm-0">

                          <?php
                          $sta_solic = array(1, 5, 6);
                          if (!in_array($solic_sta_status, $sta_solic)) {
                          ?>
                            <button class="btn botao_w botao botao_vermelho waves-effect mb-2 mb-sm-0 ms-0 ms-sm-3" type="button" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_indeferir_solicitacao">Indeferir</button>

                          <?php } else { ?>
                            <a class="btn botao_w botao botao_disabled waves-effect mb-2 mb-sm-0 ms-0 ms-sm-3">Indeferir</a>
                          <?php } ?>

                          <?php
                          $sta_solic = array(1, 5, 6);
                          if (!in_array($solic_sta_status, $sta_solic)) {
                          ?>
                            <button class="btn botao_w botao botao_verde waves-effect mb-2 mb-sm-0 ms-0 ms-sm-3" type="button" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_deferir_solicitacao">Deferir</button>

                          <?php } else { ?>
                            <button class="btn botao_w botao botao_disabled waves-effect mb-2 mb-sm-0 ms-0 ms-sm-3" type="button">Deferir</button>
                          <?php } ?>

                        </nav>
                      </div>

                    </div>
                  </div>

                  <div class="card-body p-4">
                    <div class="row grid gx-3">

                      <!-- <div class="col-md-4 col-xxl-3">
                        <label>Tipo de Atividade</label>
                        <p><?= $cta_tipo_atividade ?></p>
                        <hr>
                      </div> -->

                      <div class="col-md <?= empty($curs_curso) ? 'd-none' : '' ?>">
                        <label>Curso</label>
                        <p><?= $curs_curso ?></p>
                        <hr>
                      </div>

                      <div class="col-md <?= empty($cexc_curso) ? 'd-none' : '' ?>">
                        <label>Nome do Curso</label>
                        <p><?= $cexc_curso ?></p>
                        <hr>
                      </div>


                      <div class="col-md <?= empty($cs_semestre) ? 'd-none' : '' ?>">
                        <label>Semestre</label>
                        <p><?= $cs_semestre ?></p>
                        <hr>
                      </div>

                      <div class="col-12 <?= empty($solic_nome_atividade) ? 'd-none' : '' ?>">
                        <label>Nome da Atividade</label>
                        <p class="truncate" title="<?= $solic_nome_atividade ?>"><?= $solic_nome_atividade ?></p>
                        <hr>
                      </div>

                      <div class="col-12 <?= empty($compc_componente) ? 'd-none' : '' ?>">
                        <label>Componente Curricular</label>
                        <p class="truncate" title="<?= $compc_componente ?>"><?= $compc_componente ?></p>
                        <hr>
                      </div>

                      <div class="col-12 <?= empty($solic_nome_curso_text) ? 'd-none' : '' ?>">
                        <label>Nome do Curso</label>
                        <p class="truncate" title="<?= $solic_nome_curso_text ?>"><?= $solic_nome_curso_text ?></p>
                        <hr>
                      </div>

                      <div class="col-12 <?= empty($solic_nome_comp_ativ) ? 'd-none' : '' ?>">
                        <label>Nome do Componente/Atividade</label>
                        <p class="truncate" title="<?= $solic_nome_comp_ativ ?>"><?= $solic_nome_comp_ativ ?></p>
                        <hr>
                      </div>

                      <div class="col-md <?= empty($solic_nome_prof_resp) ? 'd-none' : '' ?>">
                        <label>Nome do Professor/Responsável</label>
                        <p><?= $solic_nome_prof_resp ?></p>
                        <hr class="d-block d-md-none">
                      </div>

                      <div class="col-md <?= empty($solic_contato) ? 'd-none' : '' ?>">
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


                    <div class="card-header" style="background: var(--azul_alpha);">
                      <div class="row align-items-center">
                        <div class="col-12 tit_nova_solicitacao">
                          <h3 class="m-0 fs-16" style="color: var(--preto);">Informações da Reserva <span class="fs-12 ms-2" style="background: var(--azul); color: #fff; padding: 3px 10px; border-radius: 3px; font-weight: 500;">Aulas Práticas</span></h3>
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

                        <div class="col-12 <?= empty($solic_ap_data_reserva) ? 'd-none' : '' ?>">
                          <label>Data(s) da reserva</label>
                          <p class="campo_textarea"><?= $solic_ap_data_reserva ?></p>
                          <hr>
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

                        <div class="col-12 <?= empty($solic_ap_tit_aulas) ? 'd-none' : '' ?>">
                          <label>Informe o título da(s) aula(s)</label>
                          <p class="campo_textarea"><?= $solic_ap_tit_aulas ?></p>
                          <hr class="<?= empty($solic_ap_obs) ? 'd-none' : '' ?>">
                        </div>

                        <div class="col-12 <?= empty($solic_ap_quant_material) ? 'd-none' : '' ?>">
                          <label>Descreva os materiais, insumos e equipamentos, com suas respectivas quantidades, que serão necessários para a realização da aula no espaço de prática</label>
                          <p class="campo_textarea"><?= $solic_ap_quant_material ?></p>
                          <hr class="<?= empty($solic_ap_obs) ? 'd-none' : '' ?>">
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

                    <div class="card-header" style="background: var(--roxo_alpha);">
                      <div class="row align-items-center">
                        <div class="col-12 tit_nova_solicitacao">
                          <h3 class="m-0 fs-16" style="color: var(--preto);">Informações da Reserva <span class="fs-12 ms-2" style="background: var(--roxo); color: #fff; padding: 3px 10px; border-radius: 3px; font-weight: 500;">Aulas Teóricas</span></h3>
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
                          <hr class="<?= empty($solic_at_obs) ? 'd-none' : '' ?>">
                        </div>

                        <div class="col-12 <?= empty($solic_at_obs) ? 'd-none' : '' ?>">
                          <label>Observações</label>
                          <p class="campo_textarea"><?= $solic_at_obs ?></p>
                        </div>

                      </div>

                    </div>
                  </div>

                <?php } ?>





                <?php include 'includes/modal/modal_reservas.php'; ?>



                <?php
                // SE SOLICITAÇÃO FORI DEFERIDA, MOSTRA FORMULÁRIO PARA RESERVAR ESPAÇO
                $solic_id = $_GET['i'];
                $query = "SELECT * FROM solicitacao_status WHERE solic_sta_solic_id = :solic_sta_solic_id AND solic_sta_status = 5";
                $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
                $stmt->execute([':solic_sta_solic_id' => $solic_id]);
                $row_count = $stmt->rowCount();
                ?>
                <?php if ($row_count) {  ?>

                  <form id="formExcluirSelecionados" method="POST" action="../router/web.php?r=Reserv">
                    <input type="hidden" name="acao" value="deletar_selecao">

                    <div class="card" id="ancora_reservas_confirmadas">
                      <div class="card-header" style="background: #0B3132;">
                        <div class="row align-items-center">
                          <div class="col-md-4 text-md-start text-center">
                            <h5 class="card-title mb-0" style="color: #fff !important">Reservas Confirmadas</h5>
                          </div>
                          <div class="col-md-8 d-flex align-items-center d-flex justify-content-md-end justify-content-center <?php echo ($global_admin_perfil != 1) ? 'd-none' : ''; ?>">

                            <!-- <div class="d-sm-none d-block">
                              <button class="btn botao botao_vermelho waves-effect mt-3 mt-md-0 me-md-3 me-2" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_cad_espaco"><i class="fa-regular fa-trash-can"></i></button>
                            </div> -->

                            <div class="d-none d-sm-block">
                              <button type="submit" id="btnExcluirSelecionados" class="btn botao_excluir_selecao botao_vermelho waves-effect mt-3 mt-md-0 me-md-3 me-2" style="display: none;">
                                <i class="fa-regular fa-trash-can me-2"></i>Excluir selecionados
                              </button>
                            </div>

                            <div class="btn botao botao_amarelo waves-effect mt-3 mt-md-0" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_cad_espaco">+ Cadastrar Reserva</div>
                          </div>
                        </div>
                      </div>

                      <div class="card-body p-0">

                        <div class="table-responsive">
                          <table id="tab_reserva_confirm_single" class="table align-middle" style="width:100%">
                            <thead>
                              <tr>
                                <th width="20px" class="rounded-start sorting_disabled" rowspan="1" colspan="1" aria-label=""><input type="checkbox" class="form-check-input" id="marcarTodos"></th>
                                <th nowrap="nowrap"><span class="me-3">Data</span></th>
                                <th nowrap="nowrap"><span class="me-3">Dia</span></th>
                                <th nowrap="nowrap"><span class="me-3">Mês</span></th>
                                <th nowrap="nowrap"><span class="me-3">Ano</span></th>
                                <th nowrap="nowrap"><span class="me-3">Início</span></th>
                                <th nowrap="nowrap"><span class="me-3">Fim</span></th>
                                <th nowrap="nowrap"><span class="me-3">Turno</span></th>
                                <th nowrap="nowrap"><span class="me-3">ID da Reserva</span></th>
                                <th nowrap="nowrap"><span class="me-3">Tipo de Aula</span></th>
                                <th nowrap="nowrap"><span class="me-3">Curso</span></th>
                                <th nowrap="nowrap"><span class="me-3">Semestre</span></th>
                                <th nowrap="nowrap"><span class="me-3">Componente Curricular/Atividade</span></th>
                                <th nowrap="nowrap"><span class="me-3">Módulo</span></th>
                                <th nowrap="nowrap"><span class="me-3">Professor</span></th>
                                <th nowrap="nowrap"><span class="me-3">Título Aula</span></th>
                                <th nowrap="nowrap"><span class="me-3">Recursos</span></th>
                                <th nowrap="nowrap"><span class="me-3">Recursos Audiovisuais Add</span></th>
                                <th nowrap="nowrap"><span class="me-3">Obs</span></th>
                                <th nowrap="nowrap"><span class="me-3">Nº Pessoas</span></th>
                                <th nowrap="nowrap"><span class="me-3">Tipo Reserva</span></th>
                                <th nowrap="nowrap"><span class="me-3">ID Local</span></th>
                                <th nowrap="nowrap"><span class="me-3">Local Reservado</span></th>
                                <th nowrap="nowrap"><span class="me-3">Andar</span></th>
                                <th nowrap="nowrap"><span class="me-3">Pavilhão</span></th>
                                <th nowrap="nowrap"><span class="me-3">Campus</span></th>
                                <th nowrap="nowrap"><span class="me-3">Tipo de Sala</span></th>
                                <th nowrap="nowrap"><span class="me-3">Capacidade</span></th>
                                <th nowrap="nowrap"><span class="me-3">Confirmado por</span></th>
                                <th nowrap="nowrap"><span class="me-3">Data Solicitação</span></th>
                                <th nowrap="nowrap"><span class="me-3">Data Reserva</span></th>
                                <th nowrap="nowrap"><span class="me-3">ID Solicitação</span></th>
                                <th nowrap="nowrap"><span class="me-3">CH Programada</span></th>
                                <th nowrap="nowrap"><span class="me-3">ID Ocorrência</span></th>
                                <th nowrap="nowrap"><span class="me-3">Início Realizado</span></th>
                                <th nowrap="nowrap"><span class="me-3">Fim Realizado</span></th>
                                <th nowrap="nowrap"><span class="me-3">CH Realizada</span></th>
                                <th nowrap="nowrap"><span class="me-3">CH Faltante</span></th>
                                <th nowrap="nowrap"><span class="me-3">CH Mais</span></th>
                                <th nowrap="nowrap"><span class="me-3">Conflito</span></th>
                                <th width="20px"></th>
                              </tr>

                            </thead>
                            <tbody>

                              <?php
                              try {
                                // PARA VERIFICAR CONFLITOS ANTERIORES
                                $reservas_analisadas = [];
                                $reservas = [];

                                // CALCULO DAS CARGAS HORÁRIAS
                                function calcularDiferencaHoras($inicio, $fim)
                                {
                                  if (!$inicio || !$fim) return null;
                                  $inicio = DateTime::createFromFormat('H:i:s.u', substr($inicio, 0, 15));
                                  $fim = DateTime::createFromFormat('H:i:s.u', substr($fim, 0, 15));
                                  if ($inicio && $fim && $fim > $inicio) {
                                    $diff = $fim->diff($inicio);
                                    return $diff->format('%H:%I');
                                  }
                                  return '00:00';
                                }

                                function paraMinutos($hora)
                                {
                                  list($h, $m) = explode(':', $hora);
                                  return ($h * 60) + $m;
                                }

                                function paraHoraMin($minutos)
                                {
                                  $h = floor($minutos / 60);
                                  $m = $minutos % 60;
                                  return sprintf('%02d:%02d', $h, $m);
                                }
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
                                                        LEFT JOIN andares ON andares.and_id = espaco.esp_andar
                                                        LEFT JOIN unidades ON unidades.uni_id = espaco.esp_unidade
                                                        LEFT JOIN recursos ON ',' + ISNULL(reservas.res_recursos_add, '') + ',' LIKE '%,' + CAST(recursos.rec_id AS VARCHAR) + ',%'
                                                        INNER JOIN admin ON admin.admin_id = reservas.res_user_id
                                                        LEFT JOIN ocorrencias ON ocorrencias.oco_res_id = reservas.res_id
                                                        WHERE solic_id = :solic_id");
                                $stmt->execute([':solic_id' => $_GET['i']]);
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                                  $solic_id                      = $row['solic_id'];
                                  $res_id                        = $row['res_id'];
                                  $res_espaco_id                 = $row['res_espaco_id'];
                                  $res_data                      = $row['res_data'];
                                  $week_dias                     = $row['week_dias'];
                                  $res_mes                       = $row['res_mes'];
                                  $res_ano                       = $row['res_ano'];
                                  $res_hora_inicio               = $row['res_hora_inicio'];
                                  $res_hora_fim                  = $row['res_hora_fim'];
                                  $res_turno                     = $row['res_turno'];
                                  $res_tipo_aula                 = $row['res_tipo_aula'];
                                  $res_tipo_reserva              = $row['res_tipo_reserva'];
                                  $res_recursos                  = $row['res_recursos'];
                                  $res_recursos_add              = $row['res_recursos_add'];
                                  $res_codigo                    = $row['res_codigo'];
                                  $cta_tipo_aula                 = $row['cta_tipo_aula'];
                                  $curs_curso                    = $row['curs_curso'];
                                  $cs_semestre                   = $row['cs_semestre'];
                                  $res_componente_atividade      = $row['res_componente_atividade'];
                                  $compc_componente              = $row['compc_componente'];
                                  $res_componente_atividade_nome = $row['res_componente_atividade_nome'];
                                  $res_nome_atividade            = $row['res_nome_atividade'];
                                  $res_modulo                    = $row['res_modulo'];
                                  $res_professor                 = $row['res_professor'];
                                  $res_titulo_aula               = $row['res_titulo_aula'];
                                  $res_obs                       = $row['res_obs'];
                                  $res_quant_pessoas             = $row['res_quant_pessoas'];
                                  $ctr_tipo_reserva              = $row['ctr_tipo_reserva'];
                                  $esp_codigo                    = $row['esp_codigo'];
                                  $esp_nome_local                = $row['esp_nome_local'];
                                  $and_andar                     = $row['and_andar'];
                                  $pav_pavilhao                  = $row['pav_pavilhao'];
                                  $uni_unidade                   = $row['uni_unidade'];
                                  $tipesp_tipo_espaco            = $row['tipesp_tipo_espaco'];
                                  $esp_quant_maxima              = $row['esp_quant_maxima'];
                                  $admin_nome                    = $row['admin_nome'];
                                  $solic_data_cad                = $row['solic_data_cad'];
                                  $res_data_cad                  = $row['res_data_cad'];
                                  $solic_codigo                  = $row['solic_codigo'];
                                  $oco_codigo                    = $row['oco_codigo'];
                                  $oco_hora_inicio_realizado     = $row['oco_hora_inicio_realizado'];
                                  $oco_hora_fim_realizado        = $row['oco_hora_fim_realizado'];

                                  ////////////////////////////////////////////
                                  // TRATA OS DADOS DOS RECURSOS ADICIONAIS //
                                  ////////////////////////////////////////////

                                  // Pegue o campo res_recursos_add e limpe para o formato certo
                                  $res_recursos_ids = trim($res_recursos_add ?? '');
                                  $res_recursos_ids = rtrim($res_recursos_ids, ','); // Remove vírgula final, se existir

                                  if (empty($res_recursos_ids)) {
                                    // Sem recursos adicionados
                                    $row['recursos_formatados'] = '';
                                  } else {
                                    // Explode e filtra só ids numéricos
                                    $ids_array = array_filter(array_map('trim', explode(',', $res_recursos_ids)), 'ctype_digit');

                                    if (count($ids_array) === 0) {
                                      $row['recursos_formatados'] = '';
                                    } else {
                                      $res_recursos_ids_sql = implode(',', $ids_array);

                                      // Busca nomes dos recursos para esses IDs
                                      $sql_recursos = "SELECT rec_recurso FROM recursos WHERE rec_id IN ($res_recursos_ids_sql)";
                                      $stmt_rec = $conn->prepare($sql_recursos);
                                      $stmt_rec->execute();
                                      $recursos = $stmt_rec->fetchAll(PDO::FETCH_COLUMN);

                                      // Monta string separada por ' / '
                                      $row['recursos_formatados'] = implode(' / ', $recursos);
                                    }
                                  }

                                  ///////////////////////
                                  // FIM DO TRATAMENTO //
                                  ///////////////////////

                                  $hora_inicio = new DateTime($res_hora_inicio);
                                  $hora_fim = new DateTime($res_hora_fim);

                                  // Verificação de conflito
                                  $conflito = false;
                                  foreach ($reservas_analisadas as $r) {
                                    if (
                                      $r['data'] === $res_data &&
                                      $r['espaco_id'] === $res_espaco_id &&
                                      $r['esp_codigo'] === $esp_codigo &&
                                      (
                                        ($hora_inicio < $r['fim'] && $hora_fim > $r['inicio']) ||  // sobreposição geral
                                        ($hora_inicio == $r['inicio'] || $hora_fim == $r['fim'])   // mesmo horário exato
                                      )
                                    ) {
                                      $conflito = true;
                                      break;
                                    }
                                  }

                                  // Armazena a reserva para futuras comparações
                                  $reservas_analisadas[] = [
                                    'data' => $res_data,
                                    'espaco_id' => $res_espaco_id,
                                    'esp_codigo' => $esp_codigo,
                                    'inicio' => $hora_inicio,
                                    'fim' => $hora_fim,
                                  ];

                                  // Aplique a classe de conflito, se necessário
                                  $conflito_class = $conflito ? 'conflict-cell' : '';

                                  extract($row);

                                  $recursos_formatados = $row['recursos_formatados'];

                                  // STATUS TIPO AULA
                                  $tipo_aula_color = $res_tipo_aula == 1 ? 'bg_info_laranja' : 'bg_info_azul';

                                  // STATUS TIPO RESERVA
                                  $tipo_reserva_color = $res_tipo_reserva == 1 ? 'bg_info_roxo' : 'bg_info_azul_escuro';

                                  // STATUS RECURSOS
                                  $recursos_color = $res_recursos == 'SIM' ? 'bg_info_verde' : 'bg_info_vermelho';

                                  // CARGA HORÁRIA PROGRAMADA
                                  $ch_programada = calcularDiferencaHoras($res_hora_inicio, $res_hora_fim);

                                  // INÍCIO e FIM REALIZADO (preferencialmente os da tabela ocorrencias)
                                  $inicio_real = $oco_hora_inicio_realizado ?: $res_hora_inicio;
                                  $fim_real = $oco_hora_fim_realizado ?: $res_hora_fim;
                                  //
                                  $borda_inicio_realizado = $oco_hora_inicio_realizado ? 'borda_dado' : '';
                                  $borda_fim_realizado = $oco_hora_fim_realizado ? 'borda_dado' : '';

                                  // CARGA HORÁRIA REALIZADA
                                  $realizada = calcularDiferencaHoras($inicio_real, $fim_real);

                                  // Convertendo para minutos
                                  $prog_min = paraMinutos($ch_programada);
                                  $real_min = paraMinutos($realizada);

                                  // CARGA HORÁRIA FALTANTE
                                  $faltante = $real_min < $prog_min ? paraHoraMin($prog_min - $real_min) : '00:00';

                                  // CARGA HORÁRIA A MAIS
                                  $a_mais = $real_min > $prog_min ? paraHoraMin($real_min - $prog_min) : '00:00';

                                  //

                                  if (!empty($res_componente_atividade)) {
                                    $componente = $compc_componente;
                                  } else if (!empty($res_componente_atividade_nome)) {
                                    $componente = $res_componente_atividade_nome;
                                  } else if (!empty($res_nome_atividade)) {
                                    $componente = $res_nome_atividade;
                                  }

                                  //CONFIGURAÇÃO DO PERFIL
                                  // if ($res_tipo_aula == 1) {
                                  //   $tipo_aula_color = 'bg_info_azul_escuro';
                                  // } else {
                                  //   $tipo_aula_color = 'bg_info_laranja';
                                  // }

                              ?>
                                  <tr>
                                    <td><input type="checkbox" class="form-check-input checkbox" name="exc_selecionados[]" value="<?= $res_id ?>"></td>
                                    <th scope="row" nowrap="nowrap" class="bg_table_fix_vermelho <?= $conflito_class ? 'bg_table_fix_vermelho_escuro' : '' ?>"><span class="hide_data"><?= date('Ymd', strtotime($res_data)) ?></span><?= htmlspecialchars(date('d/m/Y', strtotime($res_data))) ?></th>
                                    <td scope="row" nowrap="nowrap" class="bg_table_fix_laranja <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_laranja_escuro' : '' ?>"><?= htmlspecialchars(substr($week_dias, 0, 3)) ?></td>
                                    <td scope="row" nowrap="nowrap" class="bg_table_fix_verde <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_verde_escuro' : '' ?>"><?= htmlspecialchars($res_mes) ?></td>
                                    <td scope="row" nowrap="nowrap" class="bg_table_fix_azul <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_azul_escuro' : '' ?>"><?= htmlspecialchars($res_ano) ?></td>
                                    <td scope="row" nowrap="nowrap" class="bg_table_fix_roxo <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_roxo_escuro' : '' ?>"><?= htmlspecialchars(date("H:i", strtotime($res_hora_inicio))) ?></td>
                                    <td scope="row" nowrap="nowrap" class="bg_table_fix_rosa <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_rosa_escuro' : '' ?>"><?= htmlspecialchars(date("H:i", strtotime($res_hora_fim))) ?></td>
                                    <td scope="row" nowrap="nowrap" class="bg_table_fix_cinza <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_cinza_escuro' : '' ?>"><?= htmlspecialchars($res_turno) ?></td>
                                    <th scope="row" nowrap="nowrap"><?= htmlspecialchars($res_codigo) ?></th>
                                    <td scope="row" nowrap="nowrap"><span class="badge <?= $tipo_aula_color ?>"><?= htmlspecialchars($cta_tipo_aula) ?></span></td>
                                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($curs_curso) ?></td>
                                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($cs_semestre) ?></td>
                                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($componente) ?></td>
                                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($res_modulo) ?></td>
                                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($res_professor) ?></td>
                                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($res_titulo_aula) ?></td>
                                    <td scope="row" nowrap="nowrap"><span class="badge <?= $recursos_color ?>"><?= htmlspecialchars($res_recursos) ?></span></td>
                                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($recursos_formatados) ?></td>
                                    <td scope="row">

                                      <?php if ($res_obs) { ?>
                                        <button type="button" class="btn btn_soft_azul_escuro btn-sm" data-bs-toggle="modal" data-bs-target="#modal_obs<?= $res_id ?>"><i class="fa-regular fa-comment-dots"></i></button>
                                        <div id="modal_obs<?= $res_id ?>" class="modal zoomIn fade" tabindex="-1" aria-labelledby="ModalObsLabel" aria-hidden="true" style="display: none;">
                                          <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                              <div class="modal-header">
                                                <h5 class="modal-title" id="ModalObsLabel">Observação</h5>
                                                <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
                                              </div>
                                              <div class="modal-body">
                                                <p class="fs-14 m-0"><?= htmlspecialchars($res_obs) ?></p>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      <?php } ?>

                                    </td>
                                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($res_quant_pessoas) ?></td>
                                    <td scope="row" nowrap="nowrap"><span class="badge <?= $tipo_reserva_color ?>"><?= htmlspecialchars($ctr_tipo_reserva) ?></span></td>
                                    <td scope="row" nowrap="nowrap"><strong><?= htmlspecialchars($esp_codigo) ?></strong></td>
                                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($esp_nome_local) ?></td>
                                    <td scope="row" nowrap="nowrap" class="text-uppercase"><?= htmlspecialchars($and_andar) ?></td>
                                    <td scope="row" nowrap="nowrap" class="text-uppercase"><?= htmlspecialchars($pav_pavilhao) ?></td>
                                    <td scope="row" nowrap="nowrap" class="text-uppercase"><?= htmlspecialchars($uni_unidade) ?></td>
                                    <td scope="row" nowrap="nowrap" class="text-uppercase"><?= htmlspecialchars($tipesp_tipo_espaco) ?></td>
                                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($esp_quant_maxima) ?></td>
                                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($admin_nome) ?></td>
                                    <td scope="row" nowrap="nowrap"><span class="hide_data"><?= date('Ymd', strtotime($solic_data_cad)) ?></span><?= htmlspecialchars(htmlspecialchars(date('d/m/Y', strtotime($solic_data_cad)))) ?></td>
                                    <td scope="row" nowrap="nowrap"><span class="hide_data"><?= date('Ymd', strtotime($res_data_cad)) ?></span><?= htmlspecialchars(htmlspecialchars(date('d/m/Y', strtotime($res_data_cad)))) ?></td>
                                    <td scope="row" nowrap="nowrap"><strong><?= htmlspecialchars($solic_codigo) ?></strong></td>
                                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($ch_programada) ?></td>
                                    <td scope="row" nowrap="nowrap"><strong><?= htmlspecialchars($oco_codigo) ?></strong></td>
                                    <td><span class="<?= $borda_inicio_realizado ?>"><?= htmlspecialchars(date("H:i", strtotime($inicio_real))) ?></span></td>
                                    <td><span class="<?= $borda_fim_realizado ?>"><?= htmlspecialchars(date("H:i", strtotime($fim_real))) ?></span></td>
                                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($realizada) ?></td>
                                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($faltante) ?></td>
                                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($a_mais) ?></td>
                                    <td scope="row" nowrap="nowrap"> <span class="badge <?= $conflito_class ? 'bg_info_vermelho' : '' ?>"><?= $conflito_class ? 'CONFLITO' : '' ?></span></td>
                                    <td class="text-end d-flex flex-row">
                                      <a class="btn btn_soft_azul btn-sm me-2" data-bs-toggle="modal" data-bs-target="#modal_edit_espaco"
                                        data-bs-res_id="<?= $res_id ?>"
                                        data-bs-res_tipo_reserva="<?= $res_tipo_reserva ?>"
                                        data-bs-res_campus="<?= $res_campus ?>"
                                        data-bs-res_espaco_id_cabula="<?= $res_espaco_id ?>"
                                        data-bs-res_espaco_id_brotas="<?= $res_espaco_id ?>"
                                        data-bs-res_quant_pessoas="<?= $res_quant_pessoas ?>"
                                        data-bs-res_recursos="<?= $res_recursos ?>"
                                        data-bs-res_recursos_add="<?= $res_recursos_add ?>"
                                        data-bs-res_obs="<?= $res_obs ?>"
                                        data-bs-res_tipo_aula="<?= $res_tipo_aula ?>"
                                        data-bs-res_curso="<?= $res_curso ?>"
                                        data-bs-res_curso_nome="<?= $res_curso_nome ?>"
                                        data-bs-res_curso_extensao="<?= $res_curso_extensao ?>"
                                        data-bs-res_semestre="<?= $res_semestre ?>"
                                        data-bs-res_componente_atividade="<?= $res_componente_atividade ?>"
                                        data-bs-res_componente_atividade_nome="<?= $res_componente_atividade_nome ?>"
                                        data-bs-res_nome_atividade="<?= $res_nome_atividade ?>"
                                        data-bs-res_modulo="<?= $res_modulo ?>"
                                        data-bs-res_professor="<?= $res_professor ?>"
                                        data-bs-res_titulo_aula="<?= $res_titulo_aula ?>"
                                        data-bs-res_data="<?= $res_data ?>"
                                        data-bs-res_dia_semana="<?= $res_dia_semana ?>"
                                        data-bs-res_mes="<?= $res_mes ?>"
                                        data-bs-res_ano="<?= $res_ano ?>"
                                        data-bs-res_hora_inicio="<?= date('H:i', strtotime($res_hora_inicio)) ?>"
                                        data-bs-res_hora_fim="<?= date('H:i', strtotime($res_hora_fim)) ?>"
                                        data-bs-res_turno="<?= $res_turno ?>"
                                        title="Editar"><i class="fa-regular fa-pen-to-square"></i></a>

                                      <!-- <a class="btn btn_soft_azul btn-sm me-2"><i class="fa-regular fa-pen-to-square"></i></a> -->
                                      <a href="../router/web.php?r=Reserv&acao=deletar&res_id=<?= $res_id ?>" class="btn btn_soft_vermelho btn-sm del-btn"><i class="fa-regular fa-trash-can"></i></a>

                                      <div class="dropdown drop_tabela d-inline-block d-none">
                                        <button class="btn btn_soft_verde_musgo btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                          <i class="ri-more-fill align-middle"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                          <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_espaco" data-bs-esp_id="id" title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                                          <li><a href="../router/web.php?r=Reserv&acao=deletar&res_id=<?= $res_id ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
                                        </ul>
                                      </div>
                                    </td>

                                    <!-- <td class="text-end">
                                      <div class="dropdown drop_tabela d-inline-block">
                                        <button class="btn btn_soft_verde_musgo btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                          <i class="ri-more-fill align-middle"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                          <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_espaco"
                                              data-bs-res_id="<?= $res_id ?>"
                                              data-bs-res_tipo_reserva="<?= $res_tipo_reserva ?>"
                                              data-bs-res_campus="<?= $res_campus ?>"
                                              data-bs-res_espaco_id_cabula="<?= $res_espaco_id ?>"
                                              data-bs-res_espaco_id_brotas="<?= $res_espaco_id ?>"
                                              data-bs-res_quant_pessoas="<?= $res_quant_pessoas ?>"
                                              data-bs-res_recursos="<?= $res_recursos ?>"
                                              data-bs-res_recursos_add="<?= $res_recursos_add ?>"
                                              data-bs-res_obs="<?= $res_obs ?>"
                                              data-bs-res_tipo_aula="<?= $res_tipo_aula ?>"
                                              data-bs-res_curso="<?= $res_curso ?>"
                                              data-bs-res_curso_nome="<?= $res_curso_nome ?>"
                                              data-bs-res_curso_extensao="<?= $res_curso_extensao ?>"
                                              data-bs-res_semestre="<?= $res_semestre ?>"
                                              data-bs-res_componente_atividade="<?= $res_componente_atividade ?>"
                                              data-bs-res_componente_atividade_nome="<?= $res_componente_atividade_nome ?>"
                                              data-bs-res_nome_atividade="<?= $res_nome_atividade ?>"
                                              data-bs-res_modulo="<?= $res_modulo ?>"
                                              data-bs-res_professor="<?= $res_professor ?>"
                                              data-bs-res_titulo_aula="<?= $res_titulo_aula ?>"
                                              data-bs-res_data="<?= $res_data ?>"
                                              data-bs-res_dia_semana="<?= $res_dia_semana ?>"
                                              data-bs-res_mes="<?= $res_mes ?>"
                                              data-bs-res_ano="<?= $res_ano ?>"
                                              data-bs-res_hora_inicio="<?= date('H:i', strtotime($res_hora_inicio)) ?>"
                                              data-bs-res_hora_fim="<?= date('H:i', strtotime($res_hora_fim)) ?>"
                                              data-bs-res_turno="<?= $res_turno ?>"
                                              title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                                          <li><a href="../router/web.php?r=Reserv&acao=deletar&res_id=<?= $res_id ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
                                        </ul>
                                      </div>
                                    </td> -->
                                  </tr>

                              <?php }
                              } catch (PDOException $e) {
                                // echo "Erro: " . $e->getMessage();
                                echo "Erro ao tentar recuperar os dados";
                              } ?>

                            </tbody>
                          </table>
                        </div>



                        <script>
                          document.addEventListener('DOMContentLoaded', function() {
                            const marcarTodos = document.getElementById('marcarTodos');
                            const checkboxes = document.querySelectorAll('.checkbox');
                            const btnExcluir = document.getElementById('btnExcluirSelecionados');
                            const form = document.getElementById('formExcluirSelecionados');

                            if (!marcarTodos || !checkboxes.length || !btnExcluir || !form) return;

                            function atualizarBotaoExcluir() {
                              const algumMarcado = document.querySelectorAll('.checkbox:checked').length > 0;
                              btnExcluir.style.display = algumMarcado ? 'inline-block' : 'none';
                            }

                            marcarTodos.addEventListener('change', function() {
                              checkboxes.forEach(cb => cb.checked = this.checked);
                              atualizarBotaoExcluir();
                            });

                            checkboxes.forEach(cb => cb.addEventListener('change', atualizarBotaoExcluir));

                            form.addEventListener('submit', function(e) {
                              e.preventDefault(); // Impede envio imediato

                              Swal.fire({
                                text: 'Deseja excluir os itens selecionados?',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#0461AD',
                                cancelButtonColor: '#C4453E',
                                confirmButtonText: 'Excluir',
                                cancelButtonText: 'Cancelar',
                              }).then((result) => {
                                if (result.isConfirmed) {
                                  form.submit(); // Envia o formulário manualmente após confirmação
                                }
                              });
                            });

                          });
                        </script>


                      </div>
                    </div>

                  </form>

                <?php } ?>

              </div>

              <div class="tab-pane fade" id="atividades" role="tabpanel">
                <div class="card">

                  <div class="card-header">
                    <div class="row align-items-center" id="ancora_dados_projetos">
                      <div class="col-lg-12">
                        <h5 class="card-title m-0 ps-2">Atividades</h5>
                      </div>
                    </div>
                  </div>

                  <div class="card-body p-4">
                    <div class="acitivity-timeline">

                      <?php
                      try {
                        $stmt = $conn->prepare("SELECT * FROM solicitacao_analise_status
                                                INNER JOIN status_solicitacao ON status_solicitacao.stsolic_id = solicitacao_analise_status.sta_an_status
                                                LEFT JOIN admin ON admin.admin_id COLLATE SQL_Latin1_General_CP1_CI_AI = solicitacao_analise_status.sta_an_user_id
                                                WHERE sta_an_solic_id = :solic_id
                                                ORDER BY sta_an_data_upd DESC");
                        $stmt->execute([':solic_id' => $solic_id]);
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                          $row = array_map('htmlspecialchars', $row); // APLICAR htmlspecialchars A TODOS OS VALORES DO ARRAY
                          extract($row); //EXTRAI OS DADOS JÁ TRATADOS

                          // ÍCONE NOME
                          $nome_atv  = empty($admin_nome) ? $user_nome : $admin_nome;
                          $color_atv = empty($admin_nome) ? 'icon_avatar_roxo' : 'icon_avatar_azul';

                          // PEGA O PRIMEIRO NOME E ÚLTIMO NOME
                          $partesNome = explode(" ", $nome_atv);
                          $primeiroNome = $partesNome[0];
                          $ultimoNome = end($partesNome);

                          // PEGA A PRIMEIRO E ÚLTIMO LETRA
                          $firstNameInitial = strtoupper(substr($partesNome[0], 0, 1)); // PEGA A PRIMEIRA LETRA DO PRIMEIRO NOME
                          $lastNameInitial = strtoupper(substr(end($partesNome), 0, 1)); // PEGA A PRIMEIRA LETRA DO ÚLTIMO NOME
                          $iniciais_analise = $firstNameInitial . $lastNameInitial; // RETORNA AS INICIAIS

                          // CONFIGURAÇÃO DO STATUS
                          $status_colors = [
                            1  => 'bg_info_laranja',
                            2  => 'bg_info_azul',
                            3  => 'bg_info_roxo',
                            4  => 'bg_info_azul_escuro',
                            5  => 'bg_info_verde',
                            6  => 'bg_info_vermelho'
                          ];

                          $solic_status_color = $status_colors[$sta_an_status] ?? ''; // Usa '' como padrão se não existir

                      ?>

                          <div class="line_time acitivity-item pb-3 d-flex">
                            <div class="flex-shrink-0">
                              <div class="icon_avatar <?= $color_atv ?>"><?= $iniciais_analise ?></div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                              <span class="badge align-middle mb-2 <?= $solic_status_color ?>"><?= $stsolic_status ?></span>
                              <h6 class="mb-1"><?= date("d/m/Y H:i", strtotime($sta_an_data_upd)) . ' ' . $primeiroNome . '&nbsp;' . $ultimoNome ?></h6>
                              <p class="text-muted mt-2"><?= htmlspecialchars_decode($sta_an_obs) ?></p>
                            </div>
                          </div>

                      <?php }
                      } catch (PDOException $e) {
                        // echo "Erro: " . $e->getMessage();
                        echo "Erro ao tentar recuperar os dados";
                      } ?>

                    </div>
                  </div>
                </div>
              </div>

              <div class="tab-pane fade" id="ocorrencias" role="tabpanel">
                <div class="card">

                  <div class="card-header">
                    <div class="row align-items-center">
                      <div class="col-sm-6 d-flex align-items-center d-flex justify-content-sm-start justify-content-center">
                        <h5 class="card-title m-0 ps-2">Ocorrências</h5>
                      </div>
                      <div class="col-sm-6 d-flex align-items-center d-flex justify-content-sm-end justify-content-center">
                        <button class="btn botao botao_amarelo waves-effect mt-3 mt-sm-0" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_cad_ocorrencia">+ Cadastrar Ocorrência</button>
                      </div>
                    </div>
                  </div>

                  <div class="card-body p-0">
                    <table id="tab_solic_user" class="table dt-responsive  align-middle" style="width:100%">
                      <thead>
                        <tr>
                          <th><span class="me-2">Código Ocorrência</span></th>
                          <th><span class="me-2">Tipo Ocorrência</span></th>
                          <th><span class="me-2">Local</span></th>
                          <th><span class="me-2">Andar</span></th>
                          <th><span class="me-2">Pavilhão</span></th>
                          <th><span class="me-2">Campus</span></th>
                          <th><span class="me-2">Tipo de espaço</span></th>
                          <th><span class="me-2">Data Ocorrência</span></th>
                          <th><span class="me-2">Início Realizado</span></th>
                          <th><span class="me-2">Término Realizado</span></th>
                          <th><span class="me-2">Operador</span></th>
                          <th><span class="me-2">Data cadastro</span></th>
                          <th width="20px"></th>
                        </tr>
                      </thead>
                      <tbody>

                        <?php
                        try {
                          $stmt = $conn->prepare("SELECT * FROM ocorrencias
                                                  INNER JOIN reservas ON reservas.res_id = ocorrencias.oco_res_id
                                                  INNER JOIN espaco ON espaco.esp_id = reservas.res_espaco_id
                                                  INNER JOIN tipo_espaco ON tipo_espaco.tipesp_id = espaco.esp_tipo_espaco
                                                  LEFT JOIN pavilhoes ON pavilhoes.pav_id = espaco.esp_pavilhao
                                                  LEFT JOIN andares ON andares.and_id = espaco.esp_andar
                                                  LEFT JOIN unidades ON unidades.uni_id = espaco.esp_unidade
                                                  INNER JOIN admin ON admin.admin_id = ocorrencias.oco_user_id
                                                  WHERE oco_solic_id = :oco_solic_id");
                          $stmt->execute([':oco_solic_id' => $_GET['i']]);
                          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                            ////////////////////////////////////////////
                            // TRATA OS DADOS DOS TIPOS DE OCORRÊNCIAS //
                            ////////////////////////////////////////////

                            // Pegue o campo oco_tipo_ocorrencia e limpe para o formato certo
                            $tipo_ocorrencia_ids = trim($row['oco_tipo_ocorrencia'] ?? '');
                            $tipo_ocorrencia_ids = rtrim($tipo_ocorrencia_ids, ','); // Remove vírgula final, se existir

                            if (empty($tipo_ocorrencia_ids)) {
                              // Sem topo de ocorrencia
                              $row['tipos_formatados'] = '';
                            } else {
                              // Explode e filtra só ids numéricos
                              $ids_array = array_filter(array_map('trim', explode(',', $tipo_ocorrencia_ids)), 'ctype_digit');

                              if (count($ids_array) === 0) {
                                $row['tipos_formatados'] = '';
                              } else {
                                $tipo_ocorrencia_ids_sql = implode(',', $ids_array);

                                // Busca nomes dos tipos para esses IDs
                                $sql_tipo_oco = "SELECT cto_tipo_ocorrencia FROM conf_tipo_ocorrencia WHERE cto_id IN ($tipo_ocorrencia_ids_sql)";
                                $stmt_oco = $conn->prepare($sql_tipo_oco);
                                $stmt_oco->execute();
                                $tipo_oco = $stmt_oco->fetchAll(PDO::FETCH_COLUMN);

                                // Monta string separada por ' / '
                                $row['tipos_formatados'] = '• ' . implode('<br>• ', $tipo_oco);
                              }
                            }

                            ///////////////////////
                            // FIM DO TRATAMENTO //
                            ///////////////////////


                            extract($row);


                        ?>
                            <tr>
                              <th scope="row" class="text-bolder"><?= $oco_codigo ?></th>
                              <td scope="row" class="text-uppercase"><?= $tipos_formatados ?></td>
                              <td scope="row" class="text-uppercase"><?= $esp_nome_local ?></td>
                              <td scope="row" nowrap="nowrap" class="text-uppercase"><?= $and_andar ?></td>
                              <td scope="row" nowrap="nowrap" class="text-uppercase"><?= $pav_pavilhao ?></td>
                              <td scope="row" class="text-uppercase"><?= $uni_unidade ?></td>
                              <td scope="row" class="text-uppercase"><?= $tipesp_tipo_espaco ?></td>
                              <!-- <td scope="row" nowrap="nowrap"><?= $primeiroNome . ' ' . $ultimoNome ?></td> -->
                              <th scope="row" nowrap="nowrap" class="text-bolder"><span class="hide_data"><?= date('Ymd', strtotime($res_data)) ?></span><?= htmlspecialchars(date('d/m/Y', strtotime($res_data))) ?></th>
                              <th scope="row" nowrap="nowrap" class="text-bolder"><span class="hide_data"><?= date('iH', strtotime($oco_hora_inicio_realizado)) ?></span><?= date('H:i', strtotime($oco_hora_inicio_realizado)) ?></th>
                              <th scope="row" nowrap="nowrap" class="text-bolder"><span class="hide_data"><?= date('iH', strtotime($oco_hora_fim_realizado)) ?></span><?= date('H:i', strtotime($oco_hora_fim_realizado)) ?></th>
                              <td scope="row" class="text-uppercase"><?= $admin_nome ?></td>
                              <td scope="row" nowrap="nowrap" class="text-bolder"><span class="hide_data"><?= date('Ymd', strtotime($oco_data_cad)) ?></span><?= date('d/m/Y H:i', strtotime($oco_data_cad)) ?></td>
                              <td class="text-end">
                                <div class="dropdown dropdown drop_tabela d-inline-block">
                                  <button class="btn btn_soft_verde_musgo btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ri-more-fill align-middle"></i>
                                  </button>
                                  <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_ocorrencia"
                                        data-bs-oco_id="<?= $oco_id ?>"
                                        data-bs-oco_res_id="<?= $oco_res_id ?>"
                                        data-bs-oco_solic_id="<?= $oco_solic_id ?>"
                                        data-bs-oco_tipo_ocorrencia="<?= $oco_tipo_ocorrencia ?>"
                                        data-bs-oco_hora_inicio_realizado="<?= date('H:i', strtotime($oco_hora_inicio_realizado)) ?>"
                                        data-bs-oco_hora_fim_realizado="<?= date('H:i', strtotime($oco_hora_fim_realizado)) ?>"
                                        data-bs-oco_obs="<?= $oco_obs ?>"
                                        title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                                    <li><a href="../router/web.php?r=Ocorrenc&acao=deletar&oco_id=<?= $oco_id ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
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
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>





















<!-- DEFERIR -->
<?php include 'includes/modal/modal_deferir_solicitacao.php'; ?>

<!-- INDEFERIR -->
<?php include 'includes/modal/modal_indeferir_solicitacao.php'; ?>

<!-- OCORRÊNCIA -->
<?php include 'includes/modal/modal_ocorrencia.php'; ?>



<style>
  /* .progress-nav .nav .nav-link.active,
  .progress-nav .nav .nav-link.done,
  .progress-nav .nav .nav-link:focus,
  .progress-nav .nav .nav-link:active {
    border: 0 !important;
    background-color: var(--verde_musgo) !important;
  } */

  .progress-nav .nav .nav-link {
    width: 100% !important;
    padding: 0 15px;
  }

  .progress-nav .nav .nav-link {
    background: var(--azul_escuro_claro) !important
  }

  /* .progress-bar {
    background-color: var(--verde_musgo) !important;
  } */

  .progress,
  .progress-stacked {
    background: var(--azul_escuro_claro) !important
  }
</style>

<script>
  const localCabulaInput = document.getElementById("cad_reserva_local_cabula");
  const localBrotasInput = document.getElementById("cad_reserva_local_brotas");
  const dataInput = document.getElementById("cad_data_reserva");
  const inicioInput = document.getElementById("cad_res_hora_inicio");
  const fimInput = document.getElementById("cad_res_hora_fim");

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

<script>
  document.addEventListener("DOMContentLoaded", function() {
    // Obtém as tabs e adiciona evento de clique
    document.querySelectorAll(".nav-link").forEach(tab => {
      tab.addEventListener("click", function() {
        let tabId = this.getAttribute("href").substring(1); // Ex: "tab1"

        // Preserva os parâmetros existentes da URL
        let url = new URL(window.location);
        url.searchParams.set("tab", tabId); // Adiciona ou atualiza o parâmetro "tab"

        history.pushState(null, "", url.toString()); // Atualiza a URL
      });
    });

    // Ativa a tab correta baseada na URL
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get("tab");

    if (activeTab) {
      let tabElement = document.querySelector(`#${activeTab}-tab`);
      if (tabElement) {
        new bootstrap.Tab(tabElement).show();
      }
    }
  });
</script>

<!-- ITENS DOS SELECTS -->
<!-- <script src="../assets/js/351.jquery.min.js"></script>
<script src="includes/select/get_reserva.js"></script> -->

<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>
<!-- COMPLETO FORM -->
<script src="includes/select/completa_form.js"></script>
<!-- SELECT2 FORM -->
<script src="includes/select/select2.js"></script>