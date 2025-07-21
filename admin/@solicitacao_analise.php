<?php include 'includes/header.php'; ?>

<?php include 'includes/nav/header_analise.php'; ?>

<div class="row">
  <div class="col-lg-12">
    <div>
      <?php include 'includes/nav/nav_analise.php'; ?>

      <div class="pt-4">
        <div class="row">
          <div class="col-lg-12">


            <div class="tab-content text-muted">
              <div class="tab-pane active" id="overview-tab" role="tabpanel">

                <div class="card tabs_perfil">
                  <div class="card-header">
                    <div class="row align-items-center" id="ancora_dados_projetos">
                      <div class="col-lg-12">
                        <h5 class="card-title m-0 ps-2">Dados da Solicitação</h5>
                      </div>
                      <!-- <div class="col-xl-6 d-flex align-items-center justify-content-xl-end justify-content-center">
                            <nav class="navbar navbar_analise p-0">

                              <button class="btn botao botao_vermelho_transparente waves-effect mb-2 mb-md-0 ms-md-3 ms-0" type="button" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_deferir_solicitacao">Indeferir</button>

                              <button class="btn botao botao_verde waves-effect mb-2 mb-md-0 ms-md-3 ms-0" type="button" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_deferir_solicitacao">Deferir</button>

                            </nav>
                          </div> -->
                    </div>
                  </div>

                  <div class="card-body p-4">
                    <div class="row grid gx-3">

                      <div class="col-lg-6 col-xl-4 col-xxl-3">
                        <div class="mb-3">
                          <label class="form-label">Tipo de Atividade</label>
                          <select class="form-select text-uppercase" id="anal_solic_tipo_ativ" disabled>
                            <option selected value="<?= $cta_id  ?>"><?= $cta_tipo_atividade ?></option>
                          </select>
                        </div>
                      </div>

                      <div class="col-lg-6 col-xl-4 col-xxl-3" id="campo_anal_curso" style="display: none;">
                        <div class="mb-3">
                          <label class="form-label">Curso</label>
                          <select class="form-select text-uppercase" id="anal_solic_curso" disabled>
                            <option selected value="<?= $solic_curso ?>"><?= $curs_curso ?></option>
                          </select>
                        </div>
                        <script>
                          const anal_solic_tipo_ativ = document.getElementById("anal_solic_tipo_ativ");
                          const campo_anal_curso = document.getElementById("campo_anal_curso");
                          anal_solic_tipo_ativ.addEventListener("change", function() {
                            if (anal_solic_tipo_ativ.value === "1") {
                              campo_anal_curso.style.display = "block";
                            } else {
                              campo_anal_curso.style.display = "none";
                            }
                          });
                          if (anal_solic_tipo_ativ.value === "1") {
                            campo_anal_curso.style.display = "block";
                          }
                        </script>
                      </div>

                      <div class="col-lg-6 col-xl-4 col-xxl-3" id="campo_solic_comp_curric" style="display: none;">
                        <div class="mb-3">
                          <label class="form-label">Componente Curricular</label>
                          <select class="form-select text-uppercase" id="anal_solic_comp_curric" data-valor="<?= $compc_id ?>" disabled>
                            <option value="<?= $compc_id ?>"><?= $compc_componente ?></option>
                          </select>
                        </div>
                        <script>
                          $(document).ready(function() {
                            // Inicializa o select2
                            $('#anal_solic_curso').select2();

                            // Função para verificar e exibir o campo oculto se necessário
                            function verificarSelecao() {
                              if ($('#anal_solic_curso').val() == "2") { // Ajuste para o valor que deve exibir o campo
                                $('#campo_solic_comp_curric').show();
                              } else {
                                $('#campo_oculto').hide();
                              }
                            }

                            // Verifica na inicialização
                            verificarSelecao();

                            // Adiciona o evento de mudança
                            $('#anal_solic_curso').on('change', function() {
                              verificarSelecao();
                            });
                          });
                        </script>
                      </div>

                      <div class="col-lg-6 col-xl-4 col-xxl-3" id="campo_solic_nome_curso" style="display: none;">
                        <div class="mb-3">
                          <label class="form-label">Nome do Curso</label>
                          <select class="form-select text-uppercase" id="anal_solic_nome_curso" disabled>
                            <option selected value="<?= $cexc_id  ?>"><?= $cexc_curso ?></option>
                          </select>
                        </div>
                      </div>

                      <div class="col-lg-6 col-xl-4 col-xxl-3" id="campo_solic_nome_curso_text" style="display: none;">
                        <div class="mb-3">
                          <label class="form-label">Nome do Curso</label>
                          <input type="text" class="form-control text-uppercase" id="anal_solic_nome_curso_text" value="<?= $solic_nome_curso_text ?>" maxlength="100" disabled>
                        </div>
                      </div>

                      <div class="col-lg-6 col-xl-4 col-xxl-3" id="campo_solic_nome_atividade" style="display: none;">
                        <div class="mb-3">
                          <label class="form-label">Nome da Atividade</label>
                          <input type="text" class="form-control text-uppercase" id="anal_solic_nome_atividade" value="<?= $solic_nome_atividade ?>" maxlength="100" disabled>
                        </div>
                      </div>

                      <div class="col-lg-6 col-xl-4 col-xxl-3" id="campo_solic_nome_comp_ativ" style="display: none;">
                        <div class="mb-3">
                          <label class="form-label">Nome do Componente/Atividade</label>
                          <input type="text" class="form-control text-uppercase" id="anal_solic_nome_comp_ativ" value="<?= $solic_nome_comp_ativ ?>" maxlength="100" disabled>
                        </div>
                      </div>

                      <div class="col-lg-6 col-xl-4 col-xxl-3" id="campo_solic_semestre" style="display: none;">
                        <div class="mb-3">
                          <label class="form-label">Semestre</label>
                          <select class="form-select text-uppercase" id="anal_solic_semestre" disabled>
                            <option selected value="<?= $cs_id  ?>"><?= $cs_semestre ?></option>
                          </select>
                        </div>
                      </div>

                      <div class="col-lg-6 col-xl-4 col-xxl-3" id="campo_solic_nome_prof_resp" style="display: none;">
                        <div class="mb-3">
                          <label class="form-label">Nome do Professor/Responsável</label>
                          <input type="text" class="form-control text-uppercase" id="anal_solic_nome_prof_resp" value="<?= $solic_nome_prof_resp ?>" maxlength="100" disabled>
                          <div class="invalid-feedback">Este campo é obrigatório</div>
                        </div>
                      </div>

                      <div class="col-lg-6 col-xl-4 col-xxl-3" id="campo_solic_contato" style="display: none;">
                        <div class="mb-3">
                          <label class="form-label">Telefone para contato</label>
                          <input type="text" class="form-control cel_tel" id="anal_solic_contato" value="<?= $solic_contato ?>" disabled>
                          <div class="invalid-feedback">Este campo é obrigatório</div>
                        </div>
                      </div>
                    </div>

                    <div class="row grid gx-3 mt-3">

                      <?php if ($solic_ap_aula_pratica == 1) { ?>

                        <div class="tab-pane" id="profile1" role="tabpanel">
                          <div class="acordion_azul accordion custom-accordionwithicon accordion-flush accordion-fill-success" id="accordionFill_cp">


                            <div class="accordion-item">
                              <h2 class="accordion-header" id="accordionFill1">
                                <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#accor_fill1" aria-expanded="true" aria-controls="accor_fill1">AULAS PRÁTICAS</button>
                              </h2>
                              <div id="accor_fill1" class="accordion-collapse collapse show" aria-labelledby="accordionFill1" data-bs-parent="#accordionFill_cp">
                                <div class="accordion-body">
                                  <div class="row py-3">

                                    <label class="form-label">Espaço sugerido</label>
                                    <table id="res_esp_sugerido" class="table dt-responsive nowrap align-middle mb-4" style="width:100%">
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

                <?php
                // SE SOLICITAÇÃO FORI DEFERIDA, MOSTRA FORMULÁRIO PARA RESERVAR ESPAÇO
                $solic_id = $_GET['i'];
                $query = "SELECT * FROM solicitacao_status WHERE solic_sta_solic_id = :solic_sta_solic_id AND solic_sta_status = 5";
                $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
                $stmt->execute([':solic_sta_solic_id' => $solic_id]);
                $row_count = $stmt->rowCount();
                ?>
                <?php if ($row_count) {  ?>

                  <div class="card card_reserva">
                    <div class="card-header">
                      <div class="row align-items-center" id="ancora_dados_projetos">
                        <div class="col-lg-12">
                          <h5 class="card-title m-0 ps-2">Reservar Espaço</h5>
                        </div>
                      </div>
                    </div>

                    <div class="card-body p-4">

                      <form method="POST" action="" class="needs-validation" novalidate>

                        <div class="row grid gx-3">

                          <input type="hidden" class="form-control" name="res_solic_id" value="<?= $_GET['i'] ?>" required>
                          <input type="hidden" class="form-control" name="acao" value="cadastrar" required>

                          <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-2">
                            <?php try {
                              $sql = $conn->prepare("SELECT * FROM conf_tipo_aula ORDER BY cta_tipo_aula");
                              $sql->execute();
                              $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                              // echo "Erro: " . $e->getMessage();
                              echo "Erro ao tentar recuperar o perfil";
                            } ?>
                            <div class="mb-3">
                              <label class="form-label">Tipo de Aula <span>*</span></label>
                              <select class="form-select text-uppercase" name="res_tipo_aula" required>
                                <option selected disabled value=""></option>
                                <?php foreach ($result as $res) : ?>
                                  <option value="<?= $res['cta_id'] ?>"><?= $res['cta_tipo_aula'] ?></option>
                                <?php endforeach; ?>
                              </select>
                              <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>
                          </div>

                          <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-2">
                            <?php try {
                              $sql = $conn->prepare("SELECT * FROM conf_tipo_reserva ORDER BY ctr_tipo_reserva");
                              $sql->execute();
                              $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                              // echo "Erro: " . $e->getMessage();
                              echo "Erro ao tentar recuperar o perfil";
                            } ?>
                            <div class="mb-3">
                              <label class="form-label">Tipo de Reserva <span>*</span></label>
                              <select class="form-select text-uppercase" name="res_tipo_reserva" id="cad_tipo_reserva" required>
                                <option selected disabled value=""></option>
                                <?php foreach ($result as $res) : ?>
                                  <option value="<?= $res['ctr_id'] ?>"><?= $res['ctr_tipo_reserva'] ?></option>
                                <?php endforeach; ?>
                              </select>
                              <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>
                          </div>

                          <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-2">
                            <?php try {
                              $sql = $conn->prepare("SELECT * FROM unidades");
                              $sql->execute();
                              $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                              // echo "Erro: " . $e->getMessage();
                              echo "Erro ao tentar recuperar o perfil";
                            } ?>
                            <div class="mb-3">
                              <label class="form-label">Campus <span>*</span></label>
                              <select class="form-select text-uppercase" name="res_campus" id="cad_reserva_campus" required>
                                <option selected disabled value=""></option>
                                <?php foreach ($result as $res) : ?>
                                  <option value="<?= $res['uni_id'] ?>"><?= $res['uni_unidade'] ?></option>
                                <?php endforeach; ?>
                              </select>
                              <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>
                          </div>

                          <div class="col-md-6 col-lg-8 col-xl-6 col-xxl-4" id="camp_reserv_campus">
                            <div class="mb-3">
                              <label class="form-label">Local <span>*</span></label>
                              <select class="form-select text-uppercase" disabled></select>
                            </div>
                          </div>

                          <div class="col-md-6 col-lg-8 col-xl-6 col-xxl-4" id="camp_reserv_local_cabula" style="display: none;">
                            <?php try {
                              $sql = $conn->prepare("SELECT esp_id, esp_codigo, esp_nome_local FROM espaco WHERE esp_unidade = 1 ORDER BY esp_nome_local ASC");
                              $sql->execute();
                              $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                              echo "Erro ao tentar recuperar os dados";
                            } ?>
                            <div class="mb-3">
                              <label class="form-label">Local <span>*</span></label>
                              <select class="form-select text-uppercase" name="res_espaco_id_cabula" id="cad_reserva_local_cabula">
                                <option selected disabled value=""></option>
                                <?php foreach ($result as $res) : ?>
                                  <option value="<?= $res['esp_id'] ?>"><?= $res['esp_codigo'] . ' - ' . $res['esp_nome_local'] ?></option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                            <div class="invalid-feedback">Este campo é obrigatório</div>
                          </div>

                          <div class="col-md-6 col-lg-8 col-xl-6 col-xxl-4" id="camp_reserv_local_brotas" style="display: none;">
                            <?php try {
                              $sql = $conn->prepare("SELECT esp_id, esp_codigo, esp_nome_local FROM espaco WHERE esp_unidade = 2 ORDER BY esp_nome_local ASC");
                              $sql->execute();
                              $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                              echo "Erro ao tentar recuperar os dados";
                            } ?>
                            <div class="mb-3">
                              <label class="form-label">Local <span>*</span></label>
                              <select class="form-select text-uppercase" name="res_espaco_id_brotas" id="cad_reserva_local_brotas">
                                <option selected disabled value=""></option>
                                <?php foreach ($result as $res) : ?>
                                  <option value="<?= $res['esp_id'] ?>"><?= $res['esp_codigo'] . ' - ' . $res['esp_nome_local'] ?></option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                            <div class="invalid-feedback">Este campo é obrigatório</div>
                          </div>
                          <script>
                            const cad_reserva_campus = document.getElementById("cad_reserva_campus");
                            const camp_reserv_campus = document.getElementById("camp_reserv_campus");
                            const camp_reserv_local_cabula = document.getElementById("camp_reserv_local_cabula");
                            const camp_reserv_local_brotas = document.getElementById("camp_reserv_local_brotas");

                            cad_reserva_campus.addEventListener("change", function() {
                              if (cad_reserva_campus.value === "1") {
                                camp_reserv_campus.style.display = "none";
                                //
                                camp_reserv_local_cabula.style.display = "block";
                                document.getElementById("cad_reserva_local_cabula").required = true;


                                camp_reserv_local_brotas.style.display = "none";
                                document.getElementById("cad_reserva_local_cabula").value = '';
                                document.getElementById("cad_reserva_tipo_sala").value = '';
                                document.getElementById("cad_reserva_andar").value = '';
                                document.getElementById("cad_reserva_pavilhao").value = '';
                                document.getElementById("cad_reserva_camp_media").value = '';

                              } else {
                                camp_reserv_campus.style.display = "none";
                                //
                                camp_reserv_local_cabula.style.display = "none";
                                document.getElementById("cad_reserva_local_cabula").required = false;
                                document.getElementById("cad_reserva_local_cabula").value = '';
                                document.getElementById("cad_reserva_tipo_sala").value = '';
                                document.getElementById("cad_reserva_andar").value = '';
                                document.getElementById("cad_reserva_pavilhao").value = '';
                                document.getElementById("cad_reserva_camp_media").value = '';
                                //
                                camp_reserv_local_brotas.style.display = "block";
                                // document.getElementById("prop_campus").required = false;

                                //cad_tipo_reserva.style.display = "none";
                                // document.getElementById("prop_local").required = false;
                              }
                            });
                          </script>

                          <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-2">
                            <?php try {
                              $sql = $conn->prepare("SELECT * FROM tipo_espaco ORDER BY tipesp_tipo_espaco");
                              $sql->execute();
                              $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                              // echo "Erro: " . $e->getMessage();
                              echo "Erro ao tentar recuperar o perfil";
                            } ?>
                            <div class="mb-3">
                              <label class="form-label">Tipo de Sala</label>
                              <select class="form-select text-uppercase" id="cad_reserva_tipo_sala" disabled>
                                <option selected disabled value=""></option>
                                <?php foreach ($result as $res) : ?>
                                  <option value="<?= $res['tipesp_id'] ?>"><?= $res['tipesp_tipo_espaco'] ?></option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                          </div>

                          <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-2">
                            <?php try {
                              $sql = $conn->prepare("SELECT * FROM andares ORDER BY and_andar");
                              $sql->execute();
                              $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                              // echo "Erro: " . $e->getMessage();
                              echo "Erro ao tentar recuperar o perfil";
                            } ?>
                            <div class="mb-3">
                              <label class="form-label">Andar</label>
                              <select class="form-select text-uppercase" name="" id="cad_reserva_andar" disabled>
                                <option selected disabled value=""></option>
                                <?php foreach ($result as $res) : ?>
                                  <option value="<?= $res['and_id'] ?>"><?= $res['and_andar'] ?></option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                          </div>

                          <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-2">
                            <?php try {
                              $sql = $conn->prepare("SELECT * FROM pavilhoes ORDER BY pav_pavilhao");
                              $sql->execute();
                              $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                              // echo "Erro: " . $e->getMessage();
                              echo "Erro ao tentar recuperar o perfil";
                            } ?>
                            <div class="mb-3">
                              <label class="form-label">Pavilhão</label>
                              <select class="form-select text-uppercase" name="" id="cad_reserva_pavilhao" disabled>
                                <option selected value=""></option>
                                <?php foreach ($result as $res) : ?>
                                  <option value="<?= $res['pav_id'] ?>"><?= $res['pav_pavilhao'] ?></option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                          </div>

                          <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-2">
                            <div class="mb-3">
                              <label class="form-label">Capacidade Média</label>
                              <input class="form-control" name="" oninput="this.value = this.value.replace(/[^0-9]/g, '');" id="cad_reserva_camp_media" disabled>
                              <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>
                          </div>

                          <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-2" id="camp_reserv_dia_semana" style="display: none;">
                            <?php try {
                              $sql = $conn->prepare("SELECT week_id, week_dias FROM conf_dias_semana");
                              $sql->execute();
                              $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                              // echo "Erro: " . $e->getMessage();
                              echo "Erro ao tentar recuperar o perfil";
                            } ?>
                            <div class="mb-3">
                              <label class="form-label">Dia da Semana <span>*</span></label>
                              <select class="form-select text-uppercase" name="res_dia_semana">
                                <option selected disabled value=""></option>
                                <?php foreach ($result as $res) : ?>
                                  <option value="<?= $res['week_id'] ?>"><?= $res['week_dias'] ?></option>
                                <?php endforeach; ?>
                              </select>
                              <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>
                          </div>

                          <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-2" id="camp_reserv_data">
                            <div class="mb-3">
                              <label class="form-label">Data da Reserva <span>*</span></label>
                              <input type="date" class="form-control" name="res_data">
                              <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>
                          </div>

                          <script>
                            // const cad_tipo_reserva = document.getElementById("cad_tipo_reserva");
                            // const camp_reserv_dia_semana = document.getElementById("camp_reserv_dia_semana");
                            // const camp_reserv_data = document.getElementById("camp_reserv_data");

                            // cad_tipo_reserva.addEventListener("change", function() {
                            //   if (cad_tipo_reserva.value === "2") {
                            //     camp_reserv_dia_semana.style.display = "block";
                            //     camp_reserv_data.style.display = "none";
                            //     // document.getElementById("prop_campus").required = true;
                            //   } else {
                            //     camp_reserv_dia_semana.style.display = "none";
                            //     camp_reserv_data.style.display = "block";
                            //     // document.getElementById("prop_campus").required = false;

                            //     //cad_tipo_reserva.style.display = "none";
                            //     // document.getElementById("prop_local").required = false;
                            //   }
                            // });

                            // if (prop_modalidade.value === "3" || prop_modalidade.value === "4") {
                            //   campo_prop_campus.style.display = "block";
                            //   document.getElementById("prop_campus").required = true;
                            // } 
                          </script>

                          <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-2">
                            <div class="mb-3">
                              <label class="form-label">Hora Início <span>*</span></label>
                              <input type="time" class="form-control" name="res_hora_inicio" required>
                              <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>
                          </div>

                          <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-2">
                            <div class="mb-3">
                              <label class="form-label">Hora Fim <span>*</span></label>
                              <input type="time" class="form-control" name="res_hora_fim" required>
                              <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>
                          </div>

                          <div class="col-lg-12">
                            <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                              <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                              <button type="submit" class="btn botao botao_verde waves-effect">Reservar Espaço</button>
                            </div>
                          </div>

                        </div>

                      </form>

                    </div>

                    <div class="my-2">

                      <style>
                        .tabela_reservas .list_box {
                          margin: 5px 3px 5px 0;
                          display: inline-block;
                        }

                        .tabela_reservas .box {
                          display: inline;
                          padding: 1px 5px !important;
                          margin: 0px 3px 0px 0px;
                        }

                        .tabela_reservas .box_s {
                          background: #DAEDF3 !important;
                          font-weight: 500;
                          color: #44A6C4 !important;
                          font-size: 10px !important;
                          text-align: center !important;
                          border-radius: 2px;
                          display: inline-block;
                        }

                        .tabela_reservas .box_a {
                          background: #D4DFEE !important;
                          font-weight: 500;
                          color: #285FAB !important;
                          font-size: 10px !important;
                          text-align: center !important;
                          border-radius: 2px;
                          display: inline-block;
                        }

                        .tabela_reservas .box_p {
                          background: #F3DAD8 !important;
                          font-weight: 500;
                          color: #C4453E !important;
                          font-size: 10px !important;
                          text-align: center !important;
                          border-radius: 2px;
                          display: inline-block;
                        }

                        .tabela_reservas .box_c {
                          background: #E7DCED !important;
                          font-weight: 500;
                          color: #8652A6 !important;
                          font-size: 10px !important;
                          text-align: center !important;
                          border-radius: 2px;
                          display: inline-block;
                        }
                      </style>

                      <table id="res_alocamento" class="table dt-responsive nowrap align-middle tabela_reservas mb-2" style="width:100%">
                        <thead>
                          <tr>
                            <th><span class="me-3">ID Local</span></th>
                            <th><span class="me-3">Local Reservado</span></th>
                            <th><span class="me-3">Tipo de Aula</span></th>
                            <th><span class="me-3">Tipo Reserva</span></th>
                            <th><span class="me-3">Título da Aula</span></th>
                            <th><span class="me-3">Data da Reserva</span></th>
                            <th><span class="me-3">Hora Inicial</span></th>
                            <th><span class="me-3">Hora Final</span></th>
                            <th><span class="me-3">Cadastrado por</span></th>
                            <th><span class="me-3">Data Cadastro</span></th>
                            <th width="20px"></th>
                          </tr>
                        </thead>
                        <tbody>

                          <?php
                          try {
                            $solic_id = $_GET['i'];
                            $stmt = $conn->prepare("SELECT * FROM reservas
                                              INNER JOIN espaco ON espaco.esp_id = reservas.res_espaco_id
                                              INNER JOIN tipo_espaco ON tipo_espaco.tipesp_id = espaco.esp_tipo_espaco
                                              INNER JOIN conf_tipo_aula ON conf_tipo_aula.cta_id = reservas.res_tipo_aula
                                              INNER JOIN conf_tipo_reserva ON conf_tipo_reserva.ctr_id = reservas.res_tipo_reserva
                                              INNER JOIN conf_dias_semana ON conf_dias_semana.week_id = reservas.res_dia_semana
                                              LEFT JOIN andares ON andares.and_id = espaco.esp_andar
                                              LEFT JOIN pavilhoes ON pavilhoes.pav_id = espaco.esp_pavilhao
                                              INNER JOIN unidades ON unidades.uni_id = espaco.esp_unidade
                                              INNER JOIN admin ON admin.admin_id = reservas.res_user_id
                                              WHERE res_solic_id = ?");
                            $stmt->execute([$solic_id]);
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                              extract($row);

                              // PEGA O PRIMEIRO NOME E ÚLTIMO NOME
                              $partesNome = explode(" ", $admin_nome);
                              $primeiroNome = $partesNome[0];
                              $ultimoNome = end($partesNome);

                              // PEGA A PRIMEIRO E ÚLTIMO LETRA
                              $firstNameInitial = strtoupper(substr($partesNome[0], 0, 1)); // PEGA A PRIMEIRA LETRA DO PRIMEIRO NOME
                              $lastNameInitial = strtoupper(substr(end($partesNome), 0, 1)); // PEGA A PRIMEIRA LETRA DO ÚLTIMO NOME
                              $iniciais = $firstNameInitial . $lastNameInitial; // RETORNA AS INICIAIS

                              if ($res_tipo_aula == 1) {
                                $tipo_aula_color = 'bg_info_azul';
                              } else {
                                $tipo_aula_color = 'bg_info_roxo';
                              }

                              if ($res_tipo_reserva == 1) {
                                $tipo_reserva_color = 'bg_info_laranja';
                              } else {
                                $tipo_reserva_color = 'bg_info_azul_escuro';
                              }
                          ?>

                              <tr>
                                <th><?= $esp_codigo ?></th>
                                <td>
                                  <nobr class="list_box">
                                    <div class="box box_s"><?= $tipesp_tipo_espaco_icone ?></div>
                                    <?php if ($and_andar_icone) { ?>
                                      <div class="box box_a"><?= $and_andar_icone ?></div>
                                    <?php } ?>
                                    <?php if ($pav_pavilhao_icone) { ?>
                                      <div class="box box_p"><?= $pav_pavilhao_icone ?></div>
                                    <?php } ?>
                                    <div class="box box_c"><?= $uni_unidade_icone ?></div>
                                    <?= $esp_nome_local ?>
                                  </nobr>
                                </td>
                                <td scope="row"><span class="badge <?= $tipo_aula_color ?>"><?= $cta_tipo_aula ?></span></td>
                                <td scope="row"><span class="badge <?= $tipo_reserva_color ?>"><?= $ctr_tipo_reserva ?></span></td>
                                <td>CURSO DE APERFEIÇOAMENTO - “O EXCESSO NA CONTEMPORANEIDADE: CONSUMO DE ÁLCOOL E OUTRAS DROGAS, TRANSTORNO ALIMENTAR E ADIÇÃO A JOGOS E ÀS NOVAS TECNOLOGIAS”</td>
                                <td><?= $res_data ? date('d/m/Y', strtotime($res_data)) : $week_dias ?></td>
                                <td><?= date('H:i', strtotime($res_hora_inicio)) ?></td>
                                <td><?= date('H:i', strtotime($res_hora_fim)) ?></td>
                                <td><?= $primeiroNome . '&nbsp;&nbsp;' . $ultimoNome ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($res_data_upd)) ?></td>
                                <td class="text-end">
                                  <div class="dropdown drop_tabela d-inline-block">
                                    <button class="btn btn_soft_verde_musgo btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                      <i class="ri-more-fill align-middle"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                      <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_espaco"
                                          data-bs-esp_id="<?= $esp_id ?>"
                                          title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                                      <li><a href="../router/web.php?r=esp&func=exc_esp&esp_id=<?= $esp_id ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
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


<script>
  $(document).ready(function() {
    function toggleFields() {
      var tipoAtiv = $('#anal_solic_tipo_ativ').val();
      var curso = $('#anal_solic_curso').val();
      var compCurric = $('#anal_solic_comp_curric').val();
      var nomeCurso = $('#anal_solic_nome_curso').val();

      $('[id^="campo_"]').hide().find('input, select').prop('required', false);

      if (tipoAtiv == '1') {
        // 1	ATIVIDADE ACADÊMICA
        $('#campo_anal_curso').show().find('#anal_solic_curso').prop('required', true);
      } else if (tipoAtiv == '2') {
        // 2	ATIVIDADE ADMINISTRATIVA
        $('[id^="campo_"]').hide();
        $('#campo_solic_nome_atividade, #campo_solic_nome_prof_resp, #campo_solic_contato').show().find('input').prop('required', true);
        return;
      }

      if ([2, 5, 6, 9, 13, 17, 18, 21].includes(parseInt(curso))) {
        // 2	BIOMEDICINA
        // 5	EDUCAÇÃO FÍSICA
        // 6	ENFERMAGEM
        // 9	FISIOTERAPIA
        // 13	LIGA ACADÊMICA
        // 17	NÚCLEO COMUM
        // 18	ODONTOLOGIA
        // 21	PSICOLOGIA
        $('#campo_solic_comp_curric').show().find('#anal_solic_comp_curric').prop('required', true);
        // 0	OUTRO
        if (compCurric == '0') {
          $('#campo_solic_nome_comp_ativ, #campo_solic_semestre, #campo_solic_nome_prof_resp, #campo_solic_contato').show().find('input, select').prop('required', true);
        } else if (compCurric) {
          $('#campo_solic_nome_prof_resp, #campo_solic_contato').show().find('input').prop('required', true);
        }
      }

      if (curso == '8') {
        // 8	EXTENSÃO CURRICULARIZADA
        $('#campo_solic_nome_curso').show().find('#anal_solic_nome_curso').prop('required', true);
        if (nomeCurso) {
          $('#campo_solic_nome_atividade, #campo_solic_semestre, #campo_solic_nome_prof_resp, #campo_solic_contato').show().find('input, select').prop('required', true);
        }
      }

      if ([7, 10, 19, 28].includes(parseInt(curso))) {
        // 7	EXTENSÃO
        // 10	GRUPO DE PESQUISA
        // 19	PROGRAMA CANDEAL
        // 28	NIDD
        $('#campo_solic_nome_atividade, #campo_solic_nome_prof_resp, #campo_solic_contato').show().find('input').prop('required', true);
      }

      if ([11, 22].includes(parseInt(curso))) {
        // 11	LATO SENSU
        // 22	STRICTO SENSU
        $('#campo_solic_nome_curso_text, #campo_solic_nome_comp_ativ, #campo_solic_semestre, #campo_solic_nome_prof_resp, #campo_solic_contato').show().find('input, select').prop('required', true);
      }
    }

    $('#anal_solic_tipo_ativ, #anal_solic_curso, #anal_solic_comp_curric, #anal_solic_nome_curso, #campo_solic_nome_curso_text').change(function() {
      $('[id^="campo_"]').hide().find('input, select').prop('required', false);
      toggleFields();
    });

    toggleFields();
  });
</script>

<script>
  /////////////////////////////////////////////
  // INFORMAÇÕES DA RESERVA - AULAS PRÁTICAS //
  /////////////////////////////////////////////

  // CAMPO INFO. DA RESERVA - AULAS PRÁTICAS - SOLICITA RESERVA PARA AULAS PRÁTICAS
  document.addEventListener("DOMContentLoaded", function() {
    // const form = document.getElementById("meuFormulario");
    const form = document.querySelector(".meuFormulario");
    const radios_solic_ap_aula_pratica = document.querySelectorAll('input[name="solic_ap_aula_pratica"]');
    const campo_anal_info_pratic_campus = document.getElementById("campo_anal_info_pratic_campus");
    const anal_ap_campus = document.getElementById("anal_ap_campus");
    const solic_ap_quant_turma = document.getElementById("solic_ap_quant_turma");
    const camp_anal_pratic_espaco = document.getElementById("camp_anal_pratic_espaco");
    const camp_anal_pratic_espaco_brotas = document.getElementById("camp_anal_pratic_espaco_brotas");
    const camp_anal_pratic_espaco_cabula = document.getElementById("camp_anal_pratic_espaco_cabula");

    let solicitacaoSimSelecionada = false; // Variável para armazenar o estado do radio "Sim"

    // Evento para mostrar/esconder os campos ao mudar o radio
    radios_solic_ap_aula_pratica.forEach(radio => {
      radio.addEventListener("change", function() {
        solicitacaoSimSelecionada = this.value === "1"; // Atualiza o estado baseado no radio selecionado

        if (solicitacaoSimSelecionada) {
          campo_anal_info_pratic_campus.style.display = "block"; // Exibe campo campus
          anal_ap_campus.required = true;
        } else {
          campo_anal_info_pratic_campus.style.display = "none"; // Oculta campo campus
          anal_ap_campus.required = false;
          camp_anal_pratic_espaco.style.display = "none"; // Oculta todos os checkboxes

          // 🔹 Desativar todas as obrigações dos campos relacionados
          const camposObrigatorios = [
            //anal_ap_campus,
            solic_ap_quant_turma,
            solic_ap_quant_particip,
            solic_ap_data_reserva,
            solic_ap_hora_inicio,
            solic_ap_hora_fim,
            cad_info_pratic_arquivo,
            solic_ap_tit_aulas,
            solic_ap_quant_material
          ];

          camposObrigatorios.forEach(campo => {
            campo.required = false;
            campo.value = ""; // Opcional: limpar os valores dos campos
          });

          // 🔹 Ocultar campos dependentes
          //camp_anal_pratic_espaco.style.display = "none";
          //camp_anal_pratic_espaco_brotas.style.display = "none";
          //camp_anal_pratic_espaco_cabula.style.display = "none";
          camp_anal_pratic_tipo_reserva.style.display = "none";
          camp_info_pratic_data_reserva.style.display = "none";
          camp_info_pratic_dias_semana.style.display = "none";

          // 🔹 Remover validação visual dos checkboxes
          limparValidacao("solic_ap_espaco_brotas", "msgCheckInfoPraticEspacoBrotas");
          limparValidacao("solic_ap_espaco_cabula", "msgCheckInfoPraticEspacoCabula");

          // **Desmarcar checkboxes e rádios**
          document.querySelectorAll('input[type="checkbox"], input[name="solic_ap_tipo_reserva"], input[name="solic_ap_tipo_material"]').forEach(input => {
            input.checked = false;
          });

          // **Remover classes de erro**
          document.querySelectorAll('.is-invalid').forEach(element => {
            element.classList.remove('is-invalid');
          });


        }
      });
    });

    // Função para validar checkboxes de um grupo
    function validarCheckboxesPorNome(nome, erroId) {
      const checkboxes_IP_espaco = document.querySelectorAll(`input[name="${nome}[]"]`);
      const erroCheckbox_IP_espaco = document.getElementById(erroId);
      const algumMarcado_IP_espaco = Array.from(checkboxes_IP_espaco).some(checkbox => checkbox.checked);

      if (!algumMarcado_IP_espaco) {
        erroCheckbox_IP_espaco.style.display = "block";
        checkboxes_IP_espaco.forEach(checkbox => checkbox.classList.add("is-invalid"));
        return false;
      } else {
        erroCheckbox_IP_espaco.style.display = "none";
        checkboxes_IP_espaco.forEach(checkbox => checkbox.classList.remove("is-invalid"));
        return true;
      }
    }

    // Função para limpar validação
    function limparValidacao(nome, erroId) {
      const checkboxes_IP_espaco = document.querySelectorAll(`input[name="${nome}[]"]`);
      const erroCheckbox_IP_espaco = document.getElementById(erroId);

      erroCheckbox_IP_espaco.style.display = "none";
      checkboxes_IP_espaco.forEach(checkbox => checkbox.classList.remove("is-invalid"));
    }

    // Evento para exibir os checkboxes corretos ao selecionar o campus
    anal_ap_campus.addEventListener("change", function() {
      if (!solicitacaoSimSelecionada) return; // Se "Sim" não estiver selecionado, ignora

      if (anal_ap_campus.value === "2") {
        camp_anal_pratic_espaco.style.display = "block";
        camp_anal_pratic_espaco_brotas.style.display = "block";
        camp_anal_pratic_espaco_cabula.style.display = "none";
        limparValidacao("solic_ap_espaco_cabula", "msgCheckInfoPraticEspacoCabula");
      } else {
        camp_anal_pratic_espaco.style.display = "block";
        camp_anal_pratic_espaco_brotas.style.display = "none";
        camp_anal_pratic_espaco_cabula.style.display = "block";
        limparValidacao("solic_ap_espaco_brotas", "msgCheckInfoPraticEspacoBrotas");
      }
      solic_ap_quant_turma.required = true;
    });

    // Evento para validar os checkboxes apenas se "Sim" estiver marcado
    form.addEventListener("submit", function(event) {
      let valido = true;

      if (solicitacaoSimSelecionada) {
        if (anal_ap_campus.value === "2") {
          if (!validarCheckboxesPorNome("solic_ap_espaco_brotas", "msgCheckInfoPraticEspacoBrotas")) {
            valido = false;
          }
        } else {
          if (!validarCheckboxesPorNome("solic_ap_espaco_cabula", "msgCheckInfoPraticEspacoCabula")) {
            valido = false;
          }
        }
      }

      if (!valido) {
        event.preventDefault();
        event.stopPropagation();
      }
    });

    // Evento para validar os checkboxes ao marcar/desmarcar
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
      checkbox.addEventListener("change", function() {
        if (!solicitacaoSimSelecionada) return; // Só valida se "Sim" estiver marcado

        if (anal_ap_campus.value === "2") {
          validarCheckboxesPorNome("solic_ap_espaco_brotas", "msgCheckInfoPraticEspacoBrotas");
        } else {
          validarCheckboxesPorNome("solic_ap_espaco_cabula", "msgCheckInfoPraticEspacoCabula");
        }
      });
    });




    // CAMPO INFO. DA RESERVA - AULAS PRÁTICAS - NÚMERO DE PARTICIPANTES / TIPO RESERVA
    document.getElementById('solic_ap_quant_turma').addEventListener('change', function() {
      const camp_anal_pratic_tipo_reserva = document.getElementById('camp_anal_pratic_tipo_reserva');

      // NÚMERO DE PARTICIPANTES
      if (this.value) {
        camp_anal_pratic_tipo_reserva.style.display = 'block';
        solic_ap_quant_particip.required = true;
      } else {
        camp_anal_pratic_tipo_reserva.style.display = 'none';
        solic_ap_quant_particip.required = false;
      }
    });

    // TIPO RESERVA
    form.addEventListener('submit', function(event) {
      const camp_anal_pratic_tipo_reserva = document.getElementById('camp_anal_pratic_tipo_reserva');
      const radios_info_pratic_tipo_reserva = document.querySelectorAll('input[name="solic_ap_tipo_reserva"]');
      let radioSelecionado = false;

      radios_info_pratic_tipo_reserva.forEach(radio => {
        if (radio.checked) {
          radioSelecionado = true;
        }
      });

      if (camp_anal_pratic_tipo_reserva.style.display === 'block' && !radioSelecionado) {
        event.preventDefault();
        camp_anal_pratic_tipo_reserva.classList.add('was-validated');
        radios_info_pratic_tipo_reserva.forEach(radio => {
          radio.classList.add('is-invalid');
        });
      }
    });

    // Adiciona um evento para remover a validação quando um radio é selecionado
    document.querySelectorAll('input[name="solic_ap_tipo_reserva"]').forEach(radio => {
      radio.addEventListener('change', function() {
        const camp_anal_pratic_tipo_reserva = document.getElementById('camp_anal_pratic_tipo_reserva');
        const radios = document.querySelectorAll('input[name="solic_ap_tipo_reserva"]');

        // Remove a classe is-invalid de todos os radios
        radios.forEach(radio => {
          radio.classList.remove('is-invalid');
        });

        // Remove a classe was-validated do campo oculto
        camp_anal_pratic_tipo_reserva.classList.remove('was-validated');
      });
    });



    // CAMPO INFO. DA RESERVA - AULAS PRÁTICAS - TIPO DE RESERVA
    const radio = document.querySelectorAll('input[name="solic_ap_tipo_reserva"]');
    const camp_info_pratic_data_reserva = document.getElementById("camp_info_pratic_data_reserva");
    const checkboxes = document.querySelectorAll("input[name='solic_ap_dia_reserva[]']");
    const feedback = document.getElementById("msgCheckInfoPraticDiasSemana");

    radio.forEach(radio => {
      radio.addEventListener("change", function() {
        if (this.value === "1") {
          camp_info_pratic_data_reserva.style.display = "block";
          solic_ap_data_reserva.required = true;

          camp_info_pratic_dias_semana.style.display = "none";
          solic_ap_hora_inicio.required = true;

          camp_info_pratic_datas.style.display = "block";
          solic_ap_hora_fim.required = true;

        } else {
          camp_info_pratic_data_reserva.style.display = "block";
          solic_ap_data_reserva.required = false;

          camp_info_pratic_dias_semana.style.display = "block";
          camp_info_pratic_datas.style.display = "none";

          solic_ap_hora_inicio.required = true;
          solic_ap_hora_fim.required = true;
        }
      });
    });

    const subOpcoes = document.querySelectorAll('input[name="solic_ap_tipo_material"]');
    const invalidFeedback = document.getElementById("msgCheckInfoPraticMateriais");

    form.addEventListener('submit', function(event) {
      let subOpcaoSelecionada = false;
      subOpcoes.forEach(subOpcao => {
        if (subOpcao.checked) {
          subOpcaoSelecionada = true;
        }
      });

      invalidFeedback.style.display = 'none';

      if (camp_info_pratic_data_reserva.style.display === 'block' && !subOpcaoSelecionada) {
        event.preventDefault();
        invalidFeedback.style.display = 'block';
      } else {
        invalidFeedback.style.display = 'none';
      }
    });

    subOpcoes.forEach(subOpcao => {
      subOpcao.addEventListener('change', function() {
        if (this.checked) {
          invalidFeedback.style.display = 'none';
        }
      });
    });

    //


    const opcao1 = document.getElementById("solic_ap_tipo_reserva1");
    const opcao2 = document.getElementById("solic_ap_tipo_reserva2");
    const textareaContainer = document.getElementById("camp_info_pratic_datas");
    const checkboxContainer = document.getElementById("camp_info_pratic_dias_semana");
    const textarea = document.getElementById("solic_ap_data_reserva");
    const checkboxes_info_pratic_dias_semana = document.querySelectorAll("input[name='solic_ap_dia_reserva[]']");
    const checkboxFeedback = document.getElementById("msgCheckInfoPraticDiasSemana");

    function validarCheckboxes() {
      let algumMarcado = Array.from(checkboxes_info_pratic_dias_semana).some(checkbox => checkbox.checked);
      checkboxFeedback.style.display = algumMarcado ? "none" : "block";
      return algumMarcado;
    }

    opcao1.addEventListener("change", function() {
      if (this.checked) {
        textareaContainer.style.display = "block";
        checkboxContainer.style.display = "none";
        textarea.classList.remove("is-invalid");
      }
    });

    opcao2.addEventListener("change", function() {
      if (this.checked) {
        checkboxContainer.style.display = "block";
        textareaContainer.style.display = "none";
        checkboxFeedback.style.display = "none";
      }
    });

    textarea.addEventListener("input", function() {
      if (this.value.trim() !== "") {
        this.classList.remove("is-invalid");
      }
    });

    checkboxes_info_pratic_dias_semana.forEach(checkbox => {
      checkbox.addEventListener("change", validarCheckboxes);
    });

    form.addEventListener("submit", function(event) {
      let valido = true;

      if (opcao1.checked && textarea.value.trim() === "") {
        textarea.classList.add("is-invalid");
        valido = false;
      }

      if (opcao2.checked && !validarCheckboxes()) {
        valido = false;
      }

      if (!valido) {
        event.preventDefault();
        event.stopPropagation();
      }
    });


    // CAMPO INFO. DA RESERVA - AULAS PRÁTICAS - MAETERIAIS E EQUIPAMENTOS
    const solic_ap_tipo_material = document.querySelectorAll('input[name="solic_ap_tipo_material"]');
    const camp_info_pratic_anexar = document.getElementById("camp_info_pratic_anexar");
    const camp_info_pratic_titulo = document.getElementById("camp_info_pratic_titulo");
    const camp_info_pratic_quant = document.getElementById("camp_info_pratic_quant");

    // MATERIAIS
    const radios_info_pratic_material = document.querySelectorAll('input[name="solic_ap_tipo_material"]');
    let radioSelecionado = false;

    radios_info_pratic_material.forEach(radio => {
      if (radio.checked) {
        radioSelecionado = true;
      }
    });

    if (!radioSelecionado) {
      event.preventDefault();
      radios_info_pratic_material.forEach(radio => {
        radio.classList.add('is-invalid');
      });
    }

    // Adiciona um evento para remover a validação quando um radio é selecionado
    document.querySelectorAll('input[name="solic_ap_tipo_material"]').forEach(radio => {
      radio.addEventListener('change', function() {
        const radios = document.querySelectorAll('input[name="solic_ap_tipo_material"]');

        // Remove a classe is-invalid de todos os radios
        radios.forEach(radio => {
          radio.classList.remove('is-invalid');
        });

      });
    });

    solic_ap_tipo_material.forEach(radio => {
      radio.addEventListener("change", function() {
        if (this.value === "1") {
          camp_info_pratic_anexar.style.display = "block";
          cad_info_pratic_arquivo.required = true;
          //
          camp_info_pratic_titulo.style.display = "none";
          solic_ap_tit_aulas.required = false;
          //
          camp_info_pratic_quant.style.display = "none";
          solic_ap_quant_material.required = false;
          //
          camp_info_pratic_obs.style.display = "block";

        } else if (this.value === "2") {
          camp_info_pratic_anexar.style.display = "none";
          cad_info_pratic_arquivo.required = false;
          //
          camp_info_pratic_titulo.style.display = "block";
          solic_ap_tit_aulas.required = true;
          //
          camp_info_pratic_quant.style.display = "none";
          solic_ap_quant_material.required = false;
          //
          camp_info_pratic_obs.style.display = "block";

        } else {
          camp_info_pratic_anexar.style.display = "none";
          cad_info_pratic_arquivo.required = false;
          //
          camp_info_pratic_titulo.style.display = "none";
          solic_ap_tit_aulas.required = false;
          //
          camp_info_pratic_quant.style.display = "block";
          solic_ap_quant_material.required = true;
          //
          camp_info_pratic_obs.style.display = "block";
        }
      });
    });
  });
</script>




























<button class="btn botao botao_amarelo waves-effect mt-3 mt-sm-0" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_cad_espaco">+ Cadastrar Espaço</button>

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



<div class="modal fade modal_padrao" id="modal_cad_espaco" aria-hidden="true" aria-labelledby="modal_cad_espaco" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_espaco">Confirmar Reserva</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>

      <form id="form_etapa1" class="needs-validation" novalidate>
        <div class="modal-body">

          <div id="custom-progress-bar" class="progress-nav mb-5 mt-2">
            <div class="progress" style="height: 1px;">
              <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="0">
              </div>
            </div>

            <ul class="nav nav-pills progress-bar-tab custom-nav" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill active" data-progressbar="custom-progress-bar" id="pills-gen-info-tab" data-bs-toggle="pill" data-bs-target="#pills-gen-info" role="tab" aria-controls="pills-gen-info" aria-selected="false" data-position="0" tabindex="-1" disabled>Período</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill" data-progressbar="custom-progress-bar" id="pills-info-desc-tab" data-bs-toggle="pill" data-bs-target="#pills-info-desc" role="tab" aria-controls="pills-info-desc" aria-selected="false" data-position="1" tabindex="-1" disabled>Atividade</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill" data-progressbar="custom-progress-bar" id="pills-success-tab" data-bs-toggle="pill" data-bs-target="#pills-success" role="tab" aria-controls="pills-success" aria-selected="true" data-position="2" disabled>Local</button>
              </li>
            </ul>
          </div>

          <div class="row g-3">

            <input type="hidden" class="form-control" name="res_solic_id" value="<?= $_GET['i'] ?>" required>
            <input type="hidden" class="form-control" name="acao" value="cadastrar" required>

            <div class="col-xl-3">
              <?php try {
                $sql = $conn->prepare("SELECT * FROM conf_tipo_reserva ORDER BY ctr_tipo_reserva");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <div class="mb-0">
                <label class="form-label">Tipo de Reserva <span>*</span></label>
                <select class="form-select text-uppercase" name="res_tipo_reserva" id="cad_tipo_reserva" required>
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['ctr_id'] ?>"><?= $res['ctr_tipo_reserva'] ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>


            <div class="col-6 col-lg-4 col-xl-3" id="camp_reserv_dia_semana" style="display: none;">
              <?php try {
                $sql = $conn->prepare("SELECT week_id, week_dias FROM conf_dias_semana");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <div class="mb-0">
                <label class="form-label">Dia da Semana <span>*</span></label>
                <select class="form-select text-uppercase" name="res_dia_semana">
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['week_id'] ?>"><?= $res['week_dias'] ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-6 col-lg-4 col-xl-3" id="camp_reserv_data">
              <div>
                <label class="form-label">Data da Reserva <span>*</span></label>
                <input type="date" class="form-control" name="res_data" id="cad_data_reserva" onchange="preencherCampos()">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-6 col-lg-4 col-xl-3" id="camp_reserv_mes">
              <div>
                <label class="form-label">Mês</label>
                <input type="text" class="form-control text-uppercase" name="res_mes" id="cad_mes_reserva" readonly>
              </div>
            </div>

            <div class="col-6 col-lg-4 col-xl-3" id="camp_reserv_ano">
              <div>
                <label class="form-label">Ano</label>
                <input type="text" class="form-control text-uppercase" name="res_ano" id="cad_ano_reserva" readonly>
              </div>
            </div>

            <!-- <div class="col-6 col-lg-4 col-xl-3" id="camp_reserv_dia">
              <?php try {
                $sql = $conn->prepare("SELECT week_id, week_dias FROM conf_dias_semana");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <div>
                <label class="form-label">Dia da semana</label>
                <input type="hidden" class="form-control text-uppercase" name="res_dia_semana" id="cad_diaSemanaId_reserva">
                <select class="form-select text-uppercase" id="cad_diaSemana_reserva" disabled>
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['week_id'] ?>"><?= htmlspecialchars($res['week_dias']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div> -->

            <script>
              const cad_tipo_reserva = document.getElementById("cad_tipo_reserva");
              const camp_reserv_dia_semana = document.getElementById("camp_reserv_dia_semana");
              const camp_reserv_data = document.getElementById("camp_reserv_data");
              const camp_reserv_mes = document.getElementById("camp_reserv_mes");
              const camp_reserv_ano = document.getElementById("camp_reserv_ano");
              const camp_reserv_dia = document.getElementById("camp_reserv_dia");

              cad_tipo_reserva.addEventListener("change", function() {
                if (cad_tipo_reserva.value === "2") {
                  camp_reserv_dia_semana.style.display = "block";
                  camp_reserv_data.style.display = "none";
                  camp_reserv_mes.style.display = "none";
                  camp_reserv_ano.style.display = "none";
                  camp_reserv_dia.style.display = "none";
                  // document.getElementById("prop_campus").required = true;
                } else {
                  camp_reserv_dia_semana.style.display = "none";
                  camp_reserv_data.style.display = "block";
                  camp_reserv_mes.style.display = "block";
                  camp_reserv_ano.style.display = "block";
                  camp_reserv_dia.style.display = "block";
                  // document.getElementById("prop_campus").required = false;

                  //cad_tipo_reserva.style.display = "none";
                  // document.getElementById("prop_local").required = false;
                }
              });

              // if (prop_modalidade.value === "3" || prop_modalidade.value === "4") {
              //   campo_prop_campus.style.display = "block";
              //   document.getElementById("prop_campus").required = true;
              // } 
            </script>


            <div class="col-6 col-lg-4 col-xl-3">
              <label class="form-label">Hora Início <span>*</span></label>
              <input type="time" class="form-control" id="hora_inicio" name="res_hora_inicio">
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-6 col-lg-4 col-xl-3">
              <label class="form-label">Hora Fim <span>*</span></label>
              <input type="time" class="form-control" id="hora_fim" name="res_hora_fim">
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-6 col-lg-4 col-xl-3">
              <div>
                <label class="form-label">Turno</label>
                <input type="text" class="form-control text-uppercase" name="" id="turno" readonly>
              </div>
            </div>

            <!-- <div class="col-6 col-lg-4 col-xl-3">
              <?php try {
                $sql = $conn->prepare("SELECT cturn_id, cturn_turno FROM conf_turno");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <div>
                <label class="form-label">Turno</label>
                <select class="form-select text-uppercase" id="">
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['cturn_id'] ?>" <?= ($_SESSION['form_dbloq']['dbloq_dia'] ?? '') == $res['cturn_id'] ? 'selected' : '' ?>><?= htmlspecialchars($res['cturn_turno']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div> -->

            <script>
              document.getElementById('hora_inicio').addEventListener('change', function() {
                const horaInicio = this.value;

                if (!horaInicio) return;

                const [hora, minuto] = horaInicio.split(':').map(Number);
                let turno = '';

                if (hora >= 6 && hora < 12) {
                  turno = 'MANHÃ';
                } else if (hora >= 12 && hora < 18) {
                  turno = 'TARDE';
                } else {
                  turno = 'NOITE';
                }

                document.getElementById('turno').value = turno;
              });
            </script>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <!-- <button type="submit" class="btn botao botao_azul_escuro waves-effect" data-bs-target="#modal_cad_espaco2" data-bs-toggle="modal">Próximo</button> -->

                <!-- <button type="button" class="btn botao botao_azul_escuro next-btn" id="btnEtapa1" data-form="form_etapa1" data-next="#modal_cad_espaco2">Próximo</button> -->

                <button type="button" class="btn botao_azul_escuro btn-label right ms-auto nexttab nexttab waves-effect" id="btnEtapa1" data-form="form_etapa1" data-next="#modal_cad_espaco2"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i> Próximo</button>

              </div>
            </div>


          </div>


        </div>
      </form>
    </div>
  </div>
</div>







<div class="modal fade modal_padrao" id="modal_cad_espaco2" aria-hidden="true" aria-labelledby="modal_cad_espacoLabel2" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_espaco">Confirmar Reserva</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <form id="form_etapa2" novalidate>
        <div class="modal-body">

          <div id="custom-progress-bar" class="progress-nav mb-5 mt-2">
            <div class="progress" style="height: 1px;">
              <div class="progress-bar" role="progressbar" style="width: 50%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="0">
              </div>
            </div>

            <ul class="nav nav-pills progress-bar-tab custom-nav" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill done" data-progressbar="custom-progress-bar" id="pills-gen-info-tab" data-bs-toggle="pill" data-bs-target="#pills-gen-info" type="button" role="tab" aria-controls="pills-gen-info" aria-selected="false" data-position="0" tabindex="-1" disabled>Período</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill active" data-progressbar="custom-progress-bar" id="pills-info-desc-tab" data-bs-toggle="pill" data-bs-target="#pills-info-desc" type="button" role="tab" aria-controls="pills-info-desc" aria-selected="false" data-position="1" tabindex="-1" disabled>Atividade</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill" data-progressbar="custom-progress-bar" id="pills-success-tab" data-bs-toggle="pill" data-bs-target="#pills-success" type="button" role="tab" aria-controls="pills-success" aria-selected="true" data-position="2" disabled>Local</button>
              </li>
            </ul>
          </div>

          <!-- <div class="tit_section">
            <h3>Dados da Atividade</h3>
          </div> -->

          <div class="row g-3">

            <div class="col-xl-3">
              <div class="form_margem">
                <?php try {
                  $sql = $conn->prepare("SELECT cta_id, cta_tipo_atividade FROM conf_tipo_atividade ORDER BY cta_id ASC");
                  $sql->execute();
                  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                  echo "Erro ao tentar recuperar os dados";
                } ?>
                <label class="form-label">Tipo de Atividade <span>*</span></label>
                <select class="form-select text-uppercase" name="solic_tipo_ativ" id="cad_solic_tipo_ativ">
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['cta_id'] ?>"><?= $res['cta_tipo_atividade'] ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-xl-3">
              <?php try {
                $sql = $conn->prepare("SELECT * FROM conf_tipo_aula ORDER BY cta_tipo_aula");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <div class="mb-3">
                <label class="form-label">Tipo de Aula <span>*</span></label>
                <select class="form-select text-uppercase" name="res_tipo_aula">
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['cta_id'] ?>"><?= $res['cta_tipo_aula'] ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-xl-3">
              <?php try {
                $sql = $conn->prepare("SELECT curs_id, curs_curso FROM cursos WHERE curs_status != 0 ORDER BY curs_curso");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar os dados";
              } ?>
              <label class="form-label">Curso <span>*</span></label>
              <select class="form-select text-uppercase" name="solic_curso" id="cad_solic_curso">
                <option selected value=""></option>
                <?php foreach ($result as $res) : ?>
                  <option value="<?= $res['curs_id'] ?>"><?= $res['curs_curso'] ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-xl-3">
              <?php try {
                $sql = $conn->prepare("SELECT cs_id, cs_semestre FROM conf_semestre ORDER BY cs_id");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar os dados";
              } ?>
              <label class="form-label">Semestre <span>*</span></label>
              <select class="form-select text-uppercase" name="solic_curso">
                <option selected value=""></option>
                <?php foreach ($result as $res) : ?>
                  <option value="<?= $res['cs_id'] ?>"><?= $res['cs_semestre'] ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <!-- <div class="col-xl-6">
              <label class="form-label">Componente Curricular/Atividade <span>*</span></label>
              <input class="form-control text-uppercase" name="" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div> -->

            <div class="col-xl-6">
              <label class="form-label">Componente Curricular/Atividade <span>*</span></label>
              <select class="form-select text-uppercase" name="solic_comp_curric" id="cad_reserv_comp_curric">
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
              <script>
                $("#cad_reserv_comp_curric").select2({
                  dropdownParent: $("#modal_cad_espaco"),
                  // allowClear: true,
                  language: {
                    noResults: function(params) {
                      return "Dados não encontrado";
                    },
                  },
                });
              </script>
            </div>

            <div class="col-xl-6">
              <label class="form-label">Módulo <span>*</span></label>
              <input class="form-control text-uppercase" name="">
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">Título da Aula <span>*</span></label>
              <input class="form-control text-uppercase" name="">
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-xl-12">
              <label class="form-label">Professor(es) <span>*</span></label>
              <input class="form-control text-uppercase" name="">
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>


            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-between mt-2">
                <!-- <button type="button" class="btn botao botao_azul_escuro waves-effect" data-bs-target="#modal_cad_espaco" data-bs-toggle="modal">Voltar</button> -->

                <button type="button" class="btn btn-light btn-label previestab waves-effect" data-bs-target="#modal_cad_espaco" data-bs-toggle="modal"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Anterior</button>

                <!-- <button type="button" class="btn btn-link text-decoration-none btn-label previestab" data-bs-target="#modal_cad_espaco" data-bs-toggle="modal"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Back to General</button> -->
                <!-- <button type="submit" class="btn botao botao_azul_escuro waves-effect" data-bs-target="#modal_cad_espaco3" data-bs-toggle="modal">Próximo</button> -->

                <!-- <button type="button" class="btn botao botao_azul_escuro next-btn" id="btnEtapa2" data-form="form_etapa2" data-next="#modal_cad_espaco3">Próximo</button> -->

                <button type="button" class="btn botao_azul_escuro btn-label right ms-auto nexttab nexttab waves-effect" id="btnEtapa2" data-form="form_etapa2" data-next="#modal_cad_espaco3"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i> Próximo</button>

              </div>
            </div>

          </div>
        </div>
      </form>
    </div>
  </div>
</div>






<div class="modal fade modal_padrao" id="modal_cad_espaco3" aria-hidden="true" aria-labelledby="modal_cad_espacoLabel3" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_espaco2">Confirmar Reserva</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <form id="form_etapa3" novalidate>
        <div class="modal-body">

          <div id="custom-progress-bar" class="progress-nav mb-5 mt-2">
            <div class="progress" style="height: 1px;">
              <div class="progress-bar" role="progressbar" style="width: 100%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="0">
              </div>
            </div>

            <ul class="nav nav-pills progress-bar-tab custom-nav" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill done" data-progressbar="custom-progress-bar" id="pills-gen-info-tab" data-bs-toggle="pill" data-bs-target="#pills-gen-info" type="button" role="tab" aria-controls="pills-gen-info" aria-selected="false" data-position="0" tabindex="-1" disabled>Período</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill done" data-progressbar="custom-progress-bar" id="pills-info-desc-tab" data-bs-toggle="pill" data-bs-target="#pills-info-desc" type="button" role="tab" aria-controls="pills-info-desc" aria-selected="false" data-position="1" tabindex="-1" disabled>Atividade</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill active" data-progressbar="custom-progress-bar" id="pills-success-tab" data-bs-toggle="pill" data-bs-target="#pills-success" type="button" role="tab" aria-controls="pills-success" aria-selected="true" data-position="2" disabled>Local</button>
              </li>
            </ul>
          </div>

          <!-- <div class="tit_section">
            <h3>Dados do Local</h3>
          </div> -->

          <div class="row g-3">

            <div class="col-xl-2">
              <?php try {
                $sql = $conn->prepare("SELECT * FROM unidades");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <label class="form-label">Campus</label>
              <select class="form-select text-uppercase" name="esp_unidade">
                <option selected disabled value=""></option>
                <?php foreach ($result as $res) : ?>
                  <option value="<?= $res['uni_id'] ?>"><?= $res['uni_unidade'] ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-xl-7">
              <label class="form-label">Local <span>*</span></label>
              <input class="form-control text-uppercase" name="">
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-xl-3">
              <?php try {
                $sql = $conn->prepare("SELECT * FROM andares ORDER BY and_andar");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <label class="form-label">Tipo de Sala</label>
              <select class="form-select text-uppercase" name="esp_andar" disabled>
                <option selected value=""></option>
                <?php foreach ($result as $res) : ?>
                  <option value="<?= $res['and_id'] ?>"><?= $res['and_andar'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-xl-2">
              <?php try {
                $sql = $conn->prepare("SELECT * FROM andares ORDER BY and_andar");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <label class="form-label">Andar</label>
              <select class="form-select text-uppercase" name="esp_andar" disabled>
                <option selected value=""></option>
                <?php foreach ($result as $res) : ?>
                  <option value="<?= $res['and_id'] ?>"><?= $res['and_andar'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-xl-2">
              <?php try {
                $sql = $conn->prepare("SELECT * FROM pavilhoes ORDER BY pav_pavilhao");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <label class="form-label">Pavilhão</label>
              <select class="form-select text-uppercase" name="esp_pavilhao" disabled>
                <option selected value=""></option>
                <?php foreach ($result as $res) : ?>
                  <option value="<?= $res['pav_id'] ?>"><?= $res['pav_pavilhao'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-xl-2">
              <label class="form-label">Capac. Máxima</label>
              <input class="form-control" name="esp_quant_maxima" oninput="this.value = this.value.replace(/[^0-9]/g, '');" disabled>
            </div>

            <div class="col-xl-2">
              <label class="form-label">Capac. Média</label>
              <input class="form-control" name="esp_quant_media" oninput="this.value = this.value.replace(/[^0-9]/g, '');" disabled>
            </div>

            <div class="col-xl-2">
              <label class="form-label">Capac. Mínima</label>
              <input class="form-control" name="esp_quant_minima" oninput="this.value = this.value.replace(/[^0-9]/g, '');" disabled>
            </div>

            <div class="col-xl-2">
              <label class="form-label">Nº Pessoas <span>*</span></label>
              <input class="form-control text-uppercase" name="">
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>


            <div class="col-12">
              <div class="form-check form_solicita mt-2">
                <input class="form-check-input form_solicita me-2" type="checkbox" name="solic_ap_espaco_brotas[]" id="formCheck1" value="1">
                <label class="form-check-label" for="formCheck1"><strong>Recursos Audiovisuais</strong></label>
              </div>
            </div>

            <div class="col-12 mt-2 d-none">
              <label class="form-label">Recursos Audiovisuais disponíveis</label>
              <div class="check_item_container hstack gap-2 flex-wrap">
                <?php try {
                  $sql = $conn->prepare("SELECT rec_id, rec_recurso FROM recursos ORDER BY rec_recurso");
                  $sql->execute();
                  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar o perfil";
                } ?>
                <?php foreach ($result as $res) : ?>
                  <input type="checkbox" class="btn-check check_formulario_check" name="esp_recursos[]" id="checkRecurso<?= $res['rec_id'] ?>" value="<?= $res['rec_id'] ?>">
                  <label class="check_item check_formulario" for="checkRecurso<?= $res['rec_id'] ?>"><?= $res['rec_recurso'] ?></label>
                <?php endforeach; ?>
              </div>
            </div>

            <div class="col-12">
              <label class="form-label">Observações <span>*</span></label>
              <textarea class="form-control" name="" rows="4"></textarea>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>



            <!-- <div class="col-12 mb-2">
            <label class="form-label">Recursos disponíveis</label>
            <div class="check_item_container hstack gap-2 flex-wrap">
              <?php try {
                $sql = $conn->prepare("SELECT rec_id, rec_recurso FROM recursos ORDER BY rec_recurso");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <?php foreach ($result as $res) : ?>
                <input type="checkbox" class="btn-check check_formulario_check" name="esp_recursos[]" id="checkRecurso<?= $res['rec_id'] ?>" value="<?= $res['rec_id'] ?>">
                <label class="check_item check_formulario" for="checkRecurso<?= $res['rec_id'] ?>"><?= $res['rec_recurso'] ?></label>
              <?php endforeach; ?>
            </div>
          </div> -->

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-between mt-2">
                <!-- <button type="button" class="btn botao botao_azul_escuro waves-effect" data-bs-target="#modal_cad_espaco2" data-bs-toggle="modal">Voltar</button> -->

                <button type="button" class="btn btn-light btn-label previestab waves-effect" data-bs-target="#modal_cad_espaco2" data-bs-toggle="modal"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Anterior</button>

                <!-- <button type="submit" class="btn botao botao_verde waves-effect" data-bs-target="#modal_cad_espaco3" data-bs-toggle="modal">Concluir</button> -->

                <button type="submit" class="btn botao botao_verde next-btn" id="btnEtapa3" data-form="form_etapa3" data-next="#modal_cad_espaco3">Concluir</button>

              </div>
            </div>

          </div>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- <script>
  // Etapa 1 → Etapa 2
  document.getElementById('btnEtapa1').addEventListener('click', function() {
    const form1 = document.getElementById('form_etapa1');

    if (form1.checkValidity()) {
      const modal1 = bootstrap.Modal.getInstance(document.getElementById('modal_cad_espaco'));
      modal1.hide();

      const modal2 = new bootstrap.Modal(document.getElementById('modal_cad_espaco2'));
      modal2.show();
    } else {
      form1.classList.add('was-validated');
    }
  });

  // Etapa 2 → Etapa 3
  document.getElementById('btnEtapa2').addEventListener('click', function() {
    const form2 = document.getElementById('form_etapa2');

    if (form2.checkValidity()) {
      const modal2 = bootstrap.Modal.getInstance(document.getElementById('modal_cad_espaco2'));
      modal2.hide();

      const modal3 = new bootstrap.Modal(document.getElementById('modal_cad_espaco3'));
      modal3.show();
    } else {
      form2.classList.add('was-validated');
    }
  });

  // Etapa 3 → Finalizar envio
  document.getElementById('btnEtapa3').addEventListener('click', function() {
    const form3 = document.getElementById('form_etapa3');

    if (form3.checkValidity()) {
      // Aqui você pode enviar via AJAX ou dar submit real
      form3.submit();
      //alert('Formulário final validado com sucesso!');
    } else {
      form3.classList.add('was-validated');
    }
  });
</script> -->

<script>
  // Etapa 1 → Etapa 2
  document.getElementById('btnEtapa1').addEventListener('click', function() {
    const form1 = document.getElementById('form_etapa1');

    if (form1.checkValidity()) {
      const modal1 = bootstrap.Modal.getInstance(document.getElementById('modal_cad_espaco'));
      modal1.hide();

      const modal2 = new bootstrap.Modal(document.getElementById('modal_cad_espaco2'));
      modal2.show();
    } else {
      form1.classList.add('was-validated');
    }
  });

  // Etapa 2 → Etapa 3
  document.getElementById('btnEtapa2').addEventListener('click', function() {
    const form2 = document.getElementById('form_etapa2');

    if (form2.checkValidity()) {
      const modal2 = bootstrap.Modal.getInstance(document.getElementById('modal_cad_espaco2'));
      modal2.hide();

      const modal3 = new bootstrap.Modal(document.getElementById('modal_cad_espaco3'));
      modal3.show();
    } else {
      form2.classList.add('was-validated');
    }
  });

  // Etapa 3 → Envio AJAX
  document.getElementById('btnEtapa3').addEventListener('click', function() {
    const form1 = document.getElementById('form_etapa1');
    const form2 = document.getElementById('form_etapa2');
    const form3 = document.getElementById('form_etapa3');

    const valid1 = form1.checkValidity();
    const valid2 = form2.checkValidity();
    const valid3 = form3.checkValidity();

    if (!valid1) form1.classList.add('was-validated');
    if (!valid2) form2.classList.add('was-validated');
    if (!valid3) form3.classList.add('was-validated');

    if (valid1 && valid2 && valid3) {
      const formData = new FormData();

      [form1, form2, form3].forEach(form => {
        new FormData(form).forEach((value, key) => {
          formData.append(key, value);
        });
      });

      // Pega parâmetro 'i' da URL e adiciona ao formData
      const urlParams = new URLSearchParams(window.location.search);
      const i = urlParams.get('i');
      if (i) {
        formData.append('i', i);
      }

      fetch('controller/controller_reservas.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(result => {
          if (result.redirect) {
            window.location.href = result.redirect;
          } else {
            alert('Formulário enviado com sucesso!');
            location.reload();
          }
        })
        .catch(error => {
          alert('Erro ao enviar formulário.');
          console.error(error);
        });
    }
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
    // Quando o curso for alterado
    $('#cad_solic_curso').change(function() {
      var cursoId = $(this).val();
      if (cursoId !== "") {
        $.ajax({
          url: '../buscar_componentes.php',
          type: 'POST',
          data: {
            curso_id: cursoId
          },
          success: function(data) {
            $('#cad_reserv_comp_curric').html(data);
          }
        });
      } else {
        $('#cad_reserv_comp_curric').html('<option value="">Selecione um componente</option>');
      }
    });
  });
</script>





















<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>
<!-- COMPLETO FORM -->
<script src="includes/select/completa_form.js"></script>
<!-- SELECT2 FORM -->
<script src="includes/select/select2.js"></script>