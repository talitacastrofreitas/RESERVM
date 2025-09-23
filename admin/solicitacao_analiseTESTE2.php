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

                                            <div
                                                class="col-sm-6 <?php echo ($global_admin_perfil != 1) ? 'd-none' : ''; ?>">
                                                <nav
                                                    class="navbar d-flex align-items-center justify-content-sm-end justify-content-center p-0 mt-3 mt-sm-0">

                                                    <?php
                                                    $sta_solic = array(1, 4, 5, 6);
                                                    if (!in_array($solic_sta_status, $sta_solic)) {
                                                        ?>
                                                        <button
                                                            class="btn botao_w botao botao_vermelho waves-effect mb-2 mb-sm-0 ms-0 ms-sm-3"
                                                            type="button" data-bs-toggle="modal" data-bs-toggle="button"
                                                            data-bs-target="#modal_indeferir_solicitacao">Indeferir</button>

                                                    <?php } else { ?>
                                                        <a
                                                            class="btn botao_w botao botao_disabled waves-effect mb-2 mb-sm-0 ms-0 ms-sm-3">Indeferir</a>
                                                    <?php } ?>

                                                    <?php
                                                    $sta_solic = array(1, 4, 5, 6);
                                                    if (!in_array($solic_sta_status, $sta_solic)) {
                                                        ?>
                                                        <button
                                                            class="btn botao_w botao botao_verde waves-effect mb-2 mb-sm-0 ms-0 ms-sm-3"
                                                            type="button" data-bs-toggle="modal" data-bs-toggle="button"
                                                            data-bs-target="#modal_deferir_solicitacao">Deferir</button>

                                                    <?php } else { ?>
                                                        <button
                                                            class="btn botao_w botao botao_disabled waves-effect mb-2 mb-sm-0 ms-0 ms-sm-3"
                                                            type="button">Deferir</button>
                                                    <?php } ?>

                                                </nav>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="card-body p-4">
                                        <div class="row grid gx-3">

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
                                                <p class="truncate" title="<?= $solic_nome_atividade ?>">
                                                    <?= $solic_nome_atividade ?>
                                                </p>
                                                <hr>
                                            </div>

                                            <div class="col-12 <?= empty($compc_componente) ? 'd-none' : '' ?>">
                                                <label>Componente Curricular</label>
                                                <p class="truncate" title="<?= $compc_componente ?>">
                                                    <?= $compc_componente ?>
                                                </p>
                                                <hr>
                                            </div>

                                            <div class="col-12 <?= empty($solic_nome_curso_text) ? 'd-none' : '' ?>">
                                                <label>Nome do Curso</label>
                                                <p class="truncate" title="<?= $solic_nome_curso_text ?>">
                                                    <?= $solic_nome_curso_text ?>
                                                </p>
                                                <hr>
                                            </div>

                                            <div class="col-12 <?= empty($solic_nome_comp_ativ) ? 'd-none' : '' ?>">
                                                <label>Nome do Componente/Atividade</label>
                                                <p class="truncate" title="<?= $solic_nome_comp_ativ ?>">
                                                    <?= $solic_nome_comp_ativ ?>
                                                </p>
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
                                                    <div class="acordion_azul accordion custom-accordionwithicon accordion-flush accordion-fill-success"
                                                        id="accordionFill_cp">


                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="accordionFill1">
                                                                <button class="accordion-button fw-semibold" type="button"
                                                                    data-bs-toggle="collapse" data-bs-target="#accor_fill1"
                                                                    aria-expanded="true" aria-controls="accor_fill1">AULAS
                                                                    PRÁTICAS</button>
                                                            </h2>
                                                            <div id="accor_fill1" class="accordion-collapse collapse show"
                                                                aria-labelledby="accordionFill1"
                                                                data-bs-parent="#accordionFill_cp">
                                                                <div class="accordion-body">

                                                                    <div class="row grid gx-3 mb-2">
                                                                        <div class="col-md-6 col-xl-4 col-xxl-3">
                                                                            <div class="mb-3">
                                                                                <label class="form-label">Campus</label>
                                                                                <select class="form-select text-uppercase"
                                                                                    id="anal_ap_campus" disabled>
                                                                                    <option
                                                                                        value="<?= $campus_pratico_id ?>">
                                                                                        <?= $campus_pratico_nome ?>
                                                                                    </option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-6 col-xl-4 col-xxl-3">
                                                                            <div class="mb-3">
                                                                                <label class="form-label">Quantidade de
                                                                                    turmas</label>
                                                                                <select class="form-select text-uppercase"
                                                                                    id="solic_ap_quant_turma" disabled>
                                                                                    <option value="<?= $ctp_id ?>">
                                                                                        <?= $ctp_turma ?>
                                                                                    </option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-6 col-xl-4 col-xxl-3">
                                                                            <div class="mb-3">
                                                                                <label class="form-label">Número estimado de
                                                                                    participantes</label>
                                                                                <input type="text"
                                                                                    class="form-control text-uppercase"
                                                                                    id="solic_ap_quant_particip"
                                                                                    value="<?= $solic_ap_quant_particip ?>"
                                                                                    disabled>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row grid gx-3 mb-2">
                                                                        <div class="col-xl-12 col-xxl-3">
                                                                            <div class="mb-4">
                                                                                <label class="form-label">Tipo da
                                                                                    reserva</label>
                                                                                <div
                                                                                    class="check_item_container hstack gap-2 flex-wrap">
                                                                                    <input type="checkbox"
                                                                                        class="btn-check check_formulario_check"
                                                                                        id="solic_ap_tipo_reserva1"
                                                                                        value="1" <?php echo ($solic_ap_tipo_reserva == 1) ? 'checked' : ''; ?> disabled>
                                                                                    <label
                                                                                        class="check_item check_formulario"
                                                                                        for="solic_ap_tipo_reserva1">Esporádica</label>

                                                                                    <input type="checkbox"
                                                                                        class="btn-check check_formulario_check"
                                                                                        id="solic_ap_tipo_reserva2"
                                                                                        value="2" <?php echo ($solic_ap_tipo_reserva == 2) ? 'checked' : ''; ?> disabled>
                                                                                    <label
                                                                                        class="check_item check_formulario"
                                                                                        for="solic_ap_tipo_reserva2">Fixa</label>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <?php if ($solic_ap_tipo_reserva != 1) { ?>

                                                                            <div class="col-xl-12 col-xxl-9">
                                                                                <div class="mb-4">
                                                                                    <label class="form-label">Dia(s) da
                                                                                        semana</label>
                                                                                    <div
                                                                                        class="check_item_container hstack gap-2 flex-wrap">
                                                                                        <?php $dias = explode(", ", $solic_ap_dia_reserva);
                                                                                        $sql = $conn->prepare("SELECT week_id, week_dias FROM conf_dias_semana");
                                                                                        $sql->execute();
                                                                                        while ($result = $sql->fetch(PDO::FETCH_ASSOC)) { ?>
                                                                                            <input type="checkbox"
                                                                                                class="btn-check check_formulario_check"
                                                                                                id="dias_semana<?= $result['week_id'] ?>"
                                                                                                value="<?= $result['week_id'] ?>"
                                                                                                <?= $solic_ap_dia_reserva_checked = in_array($result['week_id'], $dias) ? "checked" : ""; ?>
                                                                                                disabled>
                                                                                            <label
                                                                                                class="check_item check_formulario"
                                                                                                for="dias_semana<?= $result['week_id'] ?>"><?= $result['week_dias'] ?></label>
                                                                                        <?php } ?>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                        <?php } else { ?>
                                                                            <div class="col-12">
                                                                                <div class="mb-4">
                                                                                    <label class="form-label">Data(s) da
                                                                                        reserva</label>
                                                                                    <textarea class="form-control"
                                                                                        id="solic_ap_data_reserva" rows="5"
                                                                                        disabled><?= $solic_ap_data_reserva ?></textarea>
                                                                                </div>
                                                                            </div>
                                                                        <?php } ?>

                                                                        <div class="col-md-6 col-xl-4 col-xxl-3">
                                                                            <div class="mb-4">
                                                                                <label class="form-label">Horário
                                                                                    inicial</label>
                                                                                <input type="time" class="form-control"
                                                                                    id="solic_ap_hora_inicio"
                                                                                    value="<?php echo ($solic_ap_hora_inicio) ? date("H:i", strtotime($solic_ap_hora_inicio)) : ''; ?>"
                                                                                    disabled>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-6 col-xl-4 col-xxl-3">
                                                                            <div class="mb-4">
                                                                                <label class="form-label">Horário
                                                                                    final</label>
                                                                                <input type="time" class="form-control"
                                                                                    id="solic_ap_hora_fim"
                                                                                    value="<?php echo ($solic_ap_hora_fim) ? date("H:i", strtotime($solic_ap_hora_fim)) : ''; ?>"
                                                                                    disabled>
                                                                            </div>
                                                                        </div>

                                                                        <?php if ($solic_ap_tipo_material == 1) { ?>

                                                                            <div class="col-12" id="file_ancora">
                                                                                <div class="mb-4">
                                                                                    <label class="form-label">Formulário de
                                                                                        planejamento de atividades de práticas
                                                                                        nos
                                                                                        laboratórios de ensino</label>
                                                                                    <div class="mt-0 mb-2">

                                                                                        <?php $sql = $conn->prepare("SELECT * FROM solicitacao_arq WHERE sarq_solic_id = :sarq_solic_id");
                                                                                        $sql->execute(['sarq_solic_id' => $solic_id]);
                                                                                        while ($arq = $sql->fetch(PDO::FETCH_ASSOC)) {
                                                                                            $sarq_id = $arq['sarq_id'];
                                                                                            $sarq_solic_id = $arq['sarq_solic_id'];
                                                                                            $sarq_categoria = $arq['sarq_categoria'];
                                                                                            $sarq_arquivo = $arq['sarq_arquivo'];
                                                                                            ?>
                                                                                            <div class="result_file">
                                                                                                <div class="result_file_name p-1"><a
                                                                                                        href="uploads/solicitacoes/<?= $solic_codigo . '/' . $arq['sarq_arquivo'] ?>"
                                                                                                        target="_blank"><?= $arq['sarq_arquivo'] ?></a>
                                                                                                </div>
                                                                                            </div>
                                                                                        <?php } ?>

                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                        <?php } elseif ($solic_ap_tipo_material == 2) { ?>

                                                                            <div class="col-12">
                                                                                <div class="mb-4">
                                                                                    <label class="form-label">Informe o título
                                                                                        da(s) aula(s)</label>
                                                                                    <textarea class="form-control"
                                                                                        id="solic_ap_tit_aulas" rows="5"
                                                                                        disabled><?= $solic_ap_tit_aulas ?></textarea>
                                                                                </div>
                                                                            </div>

                                                                        <?php } else { ?>

                                                                            <div class="col-12">
                                                                                <div class="mb-4">
                                                                                    <label class="form-label">Descreva os
                                                                                        materiais, insumos e equipamentos, com
                                                                                        suas
                                                                                        respectivas quantidades, que serão
                                                                                        necessários para a realização da aula no
                                                                                        espaço de prática</label>
                                                                                    <textarea class="form-control"
                                                                                        id="solic_ap_quant_material" rows="5"
                                                                                        disabled><?= $solic_ap_quant_material ?></textarea>
                                                                                </div>
                                                                            </div>

                                                                        <?php } ?>


                                                                        <?php if (!empty($solic_ap_obs)) { ?>
                                                                            <div class="col-12">
                                                                                <div class="mb-0">
                                                                                    <label
                                                                                        class="form-label">Observações</label>
                                                                                    <textarea class="form-control" rows="5"
                                                                                        disabled><?= $solic_ap_obs ?></textarea>
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
                                                    <div class="acordion_roxo accordion custom-accordionwithicon accordion-flush accordion-fill-success"
                                                        id="accordionFill_cp">


                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="accordionFill2">
                                                                <button class="accordion-button fw-semibold" type="button"
                                                                    data-bs-toggle="collapse" data-bs-target="#accor_fill2"
                                                                    aria-expanded="true" aria-controls="accor_fill2">AULAS
                                                                    TEÓRICAS</button>
                                                            </h2>
                                                            <div id="accor_fill2" class="accordion-collapse collapse show"
                                                                aria-labelledby="accordionFill2"
                                                                data-bs-parent="#accordionFill_cp">
                                                                <div class="accordion-body">

                                                                    <div class="row grid gx-3">
                                                                        <div class="col-md-6 col-xl-4 col-xxl-3">
                                                                            <div class="mb-3">
                                                                                <label class="form-label">Campus</label>
                                                                                <select class="form-select text-uppercase"
                                                                                    id="solic_at_campus" disabled>
                                                                                    <option
                                                                                        value="<?= $campus_teorico_id ?>">
                                                                                        <?= $campus_teorico_nome ?>
                                                                                    </option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-6 col-xl-4 col-xxl-3">
                                                                            <div class="mb-3">
                                                                                <label class="form-label">Quantidade de
                                                                                    sala(s) / laboratório(s) de
                                                                                    informática</label>
                                                                                <select class="form-select text-uppercase"
                                                                                    id="solic_at_quant_sala" disabled>
                                                                                    <option value="<?= $cst_id ?>">
                                                                                        <?= $cst_sala ?>
                                                                                    </option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-6 col-xl-4 col-xxl-3">
                                                                            <div class="mb-3">
                                                                                <label class="form-label">Número estimado de
                                                                                    participantes</label>
                                                                                <input type="text"
                                                                                    class="form-control text-uppercase"
                                                                                    id="solic_at_quant_particip"
                                                                                    value="<?= $solic_at_quant_particip ?>"
                                                                                    disabled>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row grid gx-3">
                                                                        <div class="col-xl-12 col-xxl-3">
                                                                            <div class="mb-4">
                                                                                <label class="form-label">Tipo da
                                                                                    reserva</label>
                                                                                <div
                                                                                    class="check_item_container hstack gap-2 flex-wrap">
                                                                                    <input type="checkbox"
                                                                                        class="btn-check check_formulario_check"
                                                                                        id="solic_at_tipo_reserva1"
                                                                                        value="1" <?php echo ($solic_at_tipo_reserva == 1) ? 'checked' : ''; ?> disabled>
                                                                                    <label
                                                                                        class="check_item check_formulario"
                                                                                        for="solic_at_tipo_reserva1">Esporádica</label>

                                                                                    <input type="checkbox"
                                                                                        class="btn-check check_formulario_check"
                                                                                        id="solic_at_tipo_reserva2"
                                                                                        value="2" <?php echo ($solic_at_tipo_reserva == 2) ? 'checked' : ''; ?> disabled>
                                                                                    <label
                                                                                        class="check_item check_formulario"
                                                                                        for="solic_at_tipo_reserva2">Fixa</label>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <?php if (!$solic_at_data_reserva) { ?>

                                                                            <div class="col-xl-12 col-xxl-9">
                                                                                <div class="mb-4">
                                                                                    <label class="form-label">Dia(s) da
                                                                                        semana</label>
                                                                                    <div
                                                                                        class="check_item_container hstack gap-2 flex-wrap">
                                                                                        <?php $dias = explode(", ", $solic_at_dia_reserva);
                                                                                        $sql = $conn->prepare("SELECT week_id, week_dias FROM conf_dias_semana");
                                                                                        $sql->execute();
                                                                                        while ($result = $sql->fetch(PDO::FETCH_ASSOC)) { ?>
                                                                                            <input type="checkbox"
                                                                                                class="btn-check check_formulario_check"
                                                                                                id="dias_semana<?= $result['week_id'] ?>"
                                                                                                value="<?= $result['week_id'] ?>"
                                                                                                <?= $solic_at_dia_reserva_checked = in_array($result['week_id'], $dias) ? "checked" : ""; ?>
                                                                                                disabled>
                                                                                            <label
                                                                                                class="check_item check_formulario"
                                                                                                for="dias_semana<?= $result['week_id'] ?>"><?= $result['week_dias'] ?></label>
                                                                                        <?php } ?>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                        <?php } else { ?>
                                                                            <div class="col-12" id="camp_info_pratic_datas"
                                                                                style="display: none;">
                                                                                <div class="mb-4">
                                                                                    <label class="form-label">Data(s) da
                                                                                        reserva</label>
                                                                                    <textarea class="form-control"
                                                                                        id="solic_ap_data_reserva" rows="5"
                                                                                        disabled><?= $solic_ap_data_reserva ?></textarea>
                                                                                </div>
                                                                            </div>
                                                                        <?php } ?>

                                                                        <div class="col-md-6 col-xl-4 col-xxl-3">
                                                                            <div class="mb-3">
                                                                                <label class="form-label">Horário
                                                                                    inicial</label>
                                                                                <input type="time" class="form-control"
                                                                                    id="solic_at_hora_inicio"
                                                                                    value="<?php echo ($solic_at_hora_inicio) ? date("H:i", strtotime($solic_at_hora_inicio)) : ''; ?>"
                                                                                    disabled>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-6 col-xl-4 col-xxl-3">
                                                                            <div class="mb-3">
                                                                                <label class="form-label">Horário
                                                                                    final</label>
                                                                                <input type="time" class="form-control"
                                                                                    id="solic_at_hora_fim"
                                                                                    value="<?php echo ($solic_at_hora_fim) ? date("H:i", strtotime($solic_at_hora_fim)) : ''; ?>"
                                                                                    disabled>
                                                                            </div>
                                                                        </div>

                                                                        <?php if (!empty($solic_at_recursos)) { ?>
                                                                            <div class="col-12">
                                                                                <div class="mb-4">
                                                                                    <label class="form-label">Recursos
                                                                                        audiovisuais adicionais</label>
                                                                                    <textarea class="form-control" rows="5"
                                                                                        disabled><?= $solic_at_recursos ?></textarea>
                                                                                </div>
                                                                            </div>
                                                                        <?php } ?>

                                                                        <?php if (!empty($solic_at_obs)) { ?>
                                                                            <div class="col-12">
                                                                                <div class="mb-4">
                                                                                    <label
                                                                                        class="form-label">Observações</label>
                                                                                    <textarea class="form-control" rows="5"
                                                                                        disabled><?= $solic_at_obs ?></textarea>
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
                                // Lógica para pré-selecionar o campus com base na solicitação
                                $selected_campus = '';
                                if (isset($solic_ap_campus) && isset($solic_at_campus)) {
                                    // Se houver apenas aula prática, ou aula prática e teórica no mesmo campus
                                    if (!empty($solic_ap_campus) && ($solic_ap_campus == $solic_at_campus || empty($solic_at_campus))) {
                                        $selected_campus = $solic_ap_campus;
                                    }
                                    // Se houver apenas aula teórica
                                    else if (!empty($solic_at_campus) && empty($solic_ap_campus)) {
                                        $selected_campus = $solic_at_campus;
                                    }
                                }
                                ?>
                                <?php include 'includes/modal/modal_reservas.php'; ?>



                                <?php
                                // SE SOLICITAÇÃO FORI DEFERIDA, MOSTRA FORMULÁRIO PARA RESERVAR ESPAÇO
                                // $solic_id = $_GET['i'];
                                // $query = "SELECT * FROM solicitacao_status WHERE solic_sta_solic_id = :solic_sta_solic_id AND solic_sta_status = 5";
                                // $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
                                // $stmt->execute([':solic_sta_solic_id' => $solic_id]);
                                // $row_count = $stmt->rowCount();
                                $solic_id = $_GET['i'];

                                // Consulta para verificar se existem reservas para esta solicitação
                                $query_check_reservas_exist = "SELECT COUNT(*) FROM reservas WHERE res_solic_id = :solic_id";
                                $stmt_check_reservas_exist = $conn->prepare($query_check_reservas_exist);
                                $stmt_check_reservas_exist->execute([':solic_id' => $solic_id]);
                                $has_reservations = $stmt_check_reservas_exist->fetchColumn() > 0; // true se houver 1 ou mais reservas
                                
                                // NOVO: Consulta para obter o status atual da solicitação
                                $query_current_solic_status = "SELECT solic_sta_status FROM solicitacao_status WHERE solic_sta_solic_id = :solic_id";
                                $stmt_current_solic_status = $conn->prepare($query_current_solic_status);
                                $stmt_current_solic_status->execute([':solic_id' => $solic_id]);
                                $solic_status_current = $stmt_current_solic_status->fetchColumn();

                                // DEFINIR SE A SEÇÃO DE RESERVAS DEVE SER EXIBIDA E SE O BOTÃO DE CADASTRO DEVE ESTAR ATIVO
                                // A seção deve aparecer se:
                                // 1. Já houver reservas para a solicitação (tabela de reservas).
                                // 2. O status da solicitação for 'Aguardando Reserva' (3) ou 'Reservado' (4),
                                //    mesmo que ainda não haja nenhuma reserva (para permitir o cadastro da primeira reserva).
                                $show_reservas_section = ($has_reservations || in_array($solic_status_current, [4, 5]));
                                // O botão de "Cadastrar Reserva" deve estar ativo apenas se o status for 'Aguardando Reserva' (3) ou 'Reservado' (4)
                                $enable_cadastrar_button = in_array($solic_status_current, [3, 4, 5]);

                                // Agora, a tabela será exibida se $show_reservas_section FOR VERDADEIRO
                                
                                ?>
                                <?php if ($show_reservas_section) { ?>

                                    <form id="formExcluirSelecionados" method="POST" action="../router/web.php?r=Reserv">
                                        <input type="hidden" name="acao" value="deletar_selecao">

                                        <div class="card" id="ancora_reservas_confirmadas">
                                            <div class="card-header" style="background: #0B3132;">
                                                <div class="row align-items-center">
                                                    <div class="col-md-4 text-md-start text-center">
                                                        <h5 class="card-title mb-0" style="color: #fff !important">Reservas
                                                            Confirmadas</h5>
                                                    </div>
                                                    <div
                                                        class="col-md-8 d-flex align-items-center d-flex justify-content-md-end justify-content-center <?php echo ($global_admin_perfil != 1) ? 'd-none' : ''; ?>">

                                                        <div class="d-none d-sm-block">
                                                            <button type="submit" id="btnExcluirSelecionados"
                                                                class="btn botao_excluir_selecao botao_vermelho waves-effect mt-3 mt-md-0 me-md-3 me-2"
                                                                style="display: none;">
                                                                <i class="fa-regular fa-trash-can me-2"></i>Excluir
                                                                selecionados
                                                            </button>
                                                            <button type="button" id="btnEditarSelecionados"
                                                                class="btn botao_editar_selecao botao_azul waves-effect mt-3 mt-md-0 me-md-3 me-2"
                                                                style="display: none;">
                                                                <i class="fa-regular fa-pen-to-square me-2"></i> Editar
                                                                selecionados
                                                            </button>
                                                        </div>


                                                        <!-- <button type="button" class="btn botao botao_amarelo waves-effect mt-3 mt-md-0"
                              data-bs-solic_id="<?= htmlspecialchars($solic_id) ?>" id="modal_cad_espaco_button">+
                              Cadastrar Reserva</button> -->

                                                        <button type="button"
                                                            class="btn botao botao_amarelo waves-effect mt-3 mt-md-0"
                                                            data-bs-solic_id="<?= htmlspecialchars($solic_id) ?>"
                                                            id="btn_cadastrar_reserva">+ Cadastrar
                                                            Reserva</button>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card-body p-0">

                                                <div class="table-responsive">
                                                    <table id="tab_reserva_confirm_single" class="table align-middle"
                                                        style="width:100%">
                                                        <thead>
                                                            <tr>
                                                                <th width="20px" class="rounded-start sorting_disabled"
                                                                    rowspan="1" colspan="1" aria-label=""><input
                                                                        type="checkbox" class="form-check-input"
                                                                        id="marcarTodos"></th>
                                                                <th nowrap="nowrap"><span class="me-3">Data</span></th>
                                                                <th nowrap="nowrap"><span class="me-3">Dia</span></th>
                                                                <th nowrap="nowrap"><span class="me-3">Mês</span></th>
                                                                <th nowrap="nowrap"><span class="me-3">Ano</span></th>
                                                                <th nowrap="nowrap"><span class="me-3">Início</span></th>
                                                                <th nowrap="nowrap"><span class="me-3">Fim</span></th>
                                                                <th nowrap="nowrap"><span class="me-3">Turno</span></th>
                                                                <th nowrap="nowrap"><span class="me-3">ID da Reserva</span>
                                                                </th>
                                                                <th nowrap="nowrap"><span class="me-3">Tipo de Aula</span>
                                                                </th>
                                                                <th nowrap="nowrap"><span class="me-3">Curso</span></th>
                                                                <th nowrap="nowrap"><span class="me-3">Semestre</span></th>
                                                                <th nowrap="nowrap"><span class="me-3">Componente
                                                                        Curricular/Atividade</span></th>
                                                                <th nowrap="nowrap"><span class="me-3">Módulo</span></th>
                                                                <th nowrap="nowrap"><span class="me-3">Professor</span></th>
                                                                <th nowrap="nowrap"><span class="me-3">Título Aula</span>
                                                                </th>
                                                                <th nowrap="nowrap"><span class="me-3">Recursos</span></th>
                                                                <th nowrap="nowrap"><span class="me-3">Recursos Audiovisuais
                                                                        Add</span></th>
                                                                <th nowrap="nowrap"><span class="me-3">Obs</span></th>
                                                                <th nowrap="nowrap"><span class="me-3">Nº Pessoas</span>
                                                                </th>
                                                                <th nowrap="nowrap"><span class="me-3">Tipo Reserva</span>
                                                                </th>
                                                                <th nowrap="nowrap"><span class="me-3">ID Local</span></th>
                                                                <th nowrap="nowrap"><span class="me-3">Local
                                                                        Reservado</span></th>
                                                                <th nowrap="nowrap"><span class="me-3">Andar</span></th>
                                                                <th nowrap="nowrap"><span class="me-3">Pavilhão</span></th>
                                                                <th nowrap="nowrap"><span class="me-3">Campus</span></th>
                                                                <th nowrap="nowrap"><span class="me-3">Tipo de Sala</span>
                                                                </th>
                                                                <th nowrap="nowrap"><span class="me-3">Capacidade</span>
                                                                </th>
                                                                <th nowrap="nowrap"><span class="me-3">Confirmado por</span>
                                                                </th>
                                                                <th nowrap="nowrap"><span class="me-3">Data
                                                                        Solicitação</span></th>
                                                                <th nowrap="nowrap"><span class="me-3">Data Reserva</span>
                                                                </th>
                                                                <th nowrap="nowrap"><span class="me-3">ID Solicitação</span>
                                                                </th>
                                                                <th nowrap="nowrap"><span class="me-3">CH Programada</span>
                                                                </th>
                                                                <th nowrap="nowrap"><span class="me-3">ID Ocorrência</span>
                                                                </th>
                                                                <th nowrap="nowrap"><span class="me-3">Início
                                                                        Realizado</span></th>
                                                                <th nowrap="nowrap"><span class="me-3">Fim Realizado</span>
                                                                </th>
                                                                <th nowrap="nowrap"><span class="me-3">CH Realizada</span>
                                                                </th>
                                                                <th nowrap="nowrap"><span class="me-3">CH Faltante</span>
                                                                </th>
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
                                                                    if (!$inicio || !$fim)
                                                                        return null;
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
                                                                // $stmt = $conn->prepare("SELECT * FROM reservas
                                                                //                                                          INNER JOIN solicitacao ON solicitacao.solic_id = reservas.res_solic_id
                                                                //                                                          INNER JOIN conf_dias_semana ON conf_dias_semana.week_id = reservas.res_dia_semana
                                                                //                                                          LEFT JOIN cursos ON cursos.curs_id = reservas.res_curso
                                                                //                                                          LEFT JOIN conf_semestre ON conf_semestre.cs_id = reservas.res_semestre
                                                                //                                                          LEFT JOIN componente_curricular ON componente_curricular.compc_id = reservas.res_componente_atividade
                                                                //                                                          INNER JOIN conf_tipo_reserva ON conf_tipo_reserva.ctr_id = reservas.res_tipo_reserva
                                                                //                                                          INNER JOIN conf_tipo_aula ON conf_tipo_aula.cta_id = reservas.res_tipo_aula
                                                                //                                                          INNER JOIN espaco ON espaco.esp_id = reservas.res_espaco_id
                                                                //                                                          INNER JOIN tipo_espaco ON tipo_espaco.tipesp_id = espaco.esp_tipo_espaco
                                                                //                                                          LEFT JOIN pavilhoes ON pavilhoes.pav_id = espaco.esp_pavilhao
                                                                //                                                          LEFT JOIN andares ON andares.and_id = espaco.esp_andar
                                                                //                                                          LEFT JOIN unidades ON unidades.uni_id = espaco.esp_unidade
                                                                //                                                          LEFT JOIN recursos ON ',' + ISNULL(reservas.res_recursos_add, '') + ',' LIKE '%,' + CAST(recursos.rec_id AS VARCHAR) + ',%'
                                                                //                                                          INNER JOIN admin ON admin.admin_id = reservas.res_user_id
                                                                //                                                          LEFT JOIN ocorrencias ON ocorrencias.oco_res_id = reservas.res_id
                                                                //                                                          WHERE solic_id = :solic_id");
                                                        

                                                                $stmt = $conn->prepare("SELECT
    reservas.*,
    solicitacao.solic_codigo,
    solicitacao.solic_data_cad,
    CASE
        WHEN reservas.res_tipo_reserva = 1 THEN conf_dias_semana_diaria.week_dias
        ELSE conf_dias_semana_fixa.week_dias
    END AS week_dias,
    cursos.curs_curso,
    conf_semestre.cs_semestre,
    componente_curricular.compc_componente,
    conf_tipo_reserva.ctr_tipo_reserva,
    conf_tipo_aula.cta_tipo_aula,
    espaco.esp_codigo,
    espaco.esp_nome_local,
    espaco.esp_quant_maxima,
    espaco.esp_quant_media,
    espaco.esp_quant_minima,
    tipo_espaco.tipesp_tipo_espaco,
    pavilhoes.pav_pavilhao,
    andares.and_andar,
    unidades.uni_unidade,
    admin.admin_nome,
    ocorrencias.oco_codigo,
    ocorrencias.oco_tipo_ocorrencia,
    ocorrencias.oco_data_cad AS oco_data_cad,
    ocorrencias.oco_hora_inicio_realizado,
    ocorrencias.oco_hora_fim_realizado,
    ocorrencias.oco_obs,
    reservas.res_data_inicio_semanal,
    reservas.res_data_fim_semanal,
    reservas.res_dia_semana
FROM reservas
INNER JOIN solicitacao ON solicitacao.solic_id = reservas.res_solic_id
LEFT JOIN conf_dias_semana AS conf_dias_semana_fixa ON conf_dias_semana_fixa.week_id = reservas.res_dia_semana AND reservas.res_tipo_reserva = 2
LEFT JOIN conf_dias_semana AS conf_dias_semana_diaria ON conf_dias_semana_diaria.week_id = CASE WHEN DATEPART(weekday, reservas.res_data) = 1 THEN 7 ELSE DATEPART(weekday, reservas.res_data) - 1 END
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
INNER JOIN admin ON admin.admin_id = reservas.res_user_id
LEFT JOIN ocorrencias ON ocorrencias.oco_res_id = reservas.res_id
WHERE reservas.res_solic_id = :solic_id
ORDER BY reservas.res_data ASC;");




                                                                $stmt->execute([':solic_id' => $_GET['i']]);
                                                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                                                                    ////////////////////////////////////////////
                                                                    // TRATA OS DADOS DOS RECURSOS ADICIONAIS //
                                                                    ////////////////////////////////////////////
                                                        
                                                                    // Pegue o campo res_recursos_add e limpe para o formato certo
                                                                    $res_recursos_ids = trim($row['res_recursos_add'] ?? '');
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
                                                        
                                                                    $hora_inicio = new DateTime($row['res_hora_inicio']);
                                                                    $hora_fim = new DateTime($row['res_hora_fim']);

                                                                    // Verificação de conflito
                                                                    $conflito = false;
                                                                    foreach ($reservas_analisadas as $r) {
                                                                        if (
                                                                            $r['data'] === $row['res_data'] &&
                                                                            $r['espaco_id'] === $row['res_espaco_id'] &&
                                                                            $r['esp_codigo'] === $row['esp_codigo'] &&
                                                                            (
                                                                                ($hora_inicio < $r['fim'] && $hora_fim > $r['inicio']) ||  // sobreposição geral
                                                                                ($hora_inicio == $r['inicio'] || $hora_fim == $r['fim'])    // mesmo horário exato
                                                                            )
                                                                        ) {
                                                                            $conflito = true;
                                                                            break;
                                                                        }
                                                                    }

                                                                    // Armazena a reserva para futuras comparações
                                                                    $reservas_analisadas[] = [
                                                                        'data' => $row['res_data'],
                                                                        'espaco_id' => $row['res_espaco_id'],
                                                                        'esp_codigo' => $row['esp_codigo'],
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
                                                                    //    $tipo_aula_color = 'bg_info_azul_escuro';
                                                                    // } else {
                                                                    //    $tipo_aula_color = 'bg_info_laranja';
                                                                    // }
                                                        
                                                                    ?>
                                                                    <tr>
                                                                        <td><input type="checkbox" class="form-check-input checkbox"
                                                                                name="exc_selecionados[]" value="<?= $res_id ?>">
                                                                        </td>
                                                                        <th scope="row" nowrap="nowrap"
                                                                            class="bg_table_fix_vermelho <?= $conflito_class ? 'bg_table_fix_vermelho_escuro' : '' ?>">
                                                                            <span
                                                                                class="hide_data"><?= date('Ymd', strtotime($res_data)) ?></span><?= htmlspecialchars(date('d/m/Y', strtotime($res_data))) ?>
                                                                        </th>

                                                                        <td scope="row" nowrap="nowrap"
                                                                            class="bg_table_fix_laranja <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_laranja_escuro' : '' ?>">
                                                                            <?= htmlspecialchars(substr($row['week_dias'], 0, 3)) ?>
                                                                        </td>


                                                                        <td scope="row" nowrap="nowrap"
                                                                            class="bg_table_fix_verde <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_verde_escuro' : '' ?>">
                                                                            <?= htmlspecialchars($res_mes) ?>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap"
                                                                            class="bg_table_fix_azul <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_azul_escuro' : '' ?>">
                                                                            <?= htmlspecialchars($res_ano) ?>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap"
                                                                            class="bg_table_fix_roxo <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_roxo_escuro' : '' ?>">
                                                                            <?= htmlspecialchars(date("H:i", strtotime($res_hora_inicio))) ?>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap"
                                                                            class="bg_table_fix_rosa <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_rosa_escuro' : '' ?>">
                                                                            <?= htmlspecialchars(date("H:i", strtotime($res_hora_fim))) ?>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap"
                                                                            class="bg_table_fix_cinza <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_cinza_escuro' : '' ?>">
                                                                            <?= htmlspecialchars($res_turno) ?>
                                                                        </td>
                                                                        <th scope="row" nowrap="nowrap">
                                                                            <?= htmlspecialchars($res_codigo) ?>
                                                                        </th>
                                                                        <td scope="row" nowrap="nowrap"><span
                                                                                class="badge <?= $tipo_aula_color ?>"><?= htmlspecialchars($cta_tipo_aula) ?></span>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap">
                                                                            <?= htmlspecialchars($curs_curso) ?>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap">
                                                                            <?= htmlspecialchars($cs_semestre) ?>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap">
                                                                            <?= htmlspecialchars($componente) ?>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap">
                                                                            <?= htmlspecialchars($res_modulo) ?>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap">
                                                                            <?= htmlspecialchars($res_professor) ?>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap">
                                                                            <?= htmlspecialchars($res_titulo_aula) ?>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap"><span
                                                                                class="badge <?= $recursos_color ?>"><?= htmlspecialchars($res_recursos) ?></span>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap">
                                                                            <?= htmlspecialchars($recursos_formatados) ?>
                                                                        </td>
                                                                        <td scope="row">

                                                                            <?php if ($res_obs) { ?>
                                                                                <button type="button"
                                                                                    class="btn btn_soft_azul_escuro btn-sm"
                                                                                    data-bs-toggle="modal"
                                                                                    data-bs-target="#modal_obs<?= $res_id ?>"><i
                                                                                        class="fa-regular fa-comment-dots"></i></button>
                                                                                <div id="modal_obs<?= $res_id ?>"
                                                                                    class="modal zoomIn fade" tabindex="-1"
                                                                                    aria-labelledby="ModalObsLabel" aria-hidden="true"
                                                                                    style="display: none;">
                                                                                    <div class="modal-dialog modal-dialog-centered">
                                                                                        <div class="modal-content">
                                                                                            <div class="modal-header">
                                                                                                <h5 class="modal-title"
                                                                                                    id="ModalObsLabel">Observação</h5>
                                                                                                <a type="button" class="btn-close"
                                                                                                    data-bs-dismiss="modal"
                                                                                                    aria-label="Close"></a>
                                                                                            </div>
                                                                                            <div class="modal-body">
                                                                                                <p class="fs-14 m-0">
                                                                                                    <?= htmlspecialchars($res_obs) ?>
                                                                                                </p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            <?php } ?>

                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap">
                                                                            <?= htmlspecialchars($res_quant_pessoas) ?>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap"><span
                                                                                class="badge <?= $tipo_reserva_color ?>"><?= htmlspecialchars($ctr_tipo_reserva) ?></span>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap">
                                                                            <strong><?= htmlspecialchars($esp_codigo) ?></strong>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap">
                                                                            <?= htmlspecialchars($esp_nome_local) ?>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap" class="text-uppercase">
                                                                            <?= htmlspecialchars($and_andar) ?>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap" class="text-uppercase">
                                                                            <?= htmlspecialchars($pav_pavilhao) ?>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap" class="text-uppercase">
                                                                            <?= htmlspecialchars($uni_unidade) ?>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap" class="text-uppercase">
                                                                            <?= htmlspecialchars($tipesp_tipo_espaco) ?>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap">
                                                                            <?= htmlspecialchars($esp_quant_maxima) ?>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap">
                                                                            <?= htmlspecialchars($admin_nome) ?>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap"><span
                                                                                class="hide_data"><?= date('Ymd', strtotime($solic_data_cad)) ?></span><?= htmlspecialchars(htmlspecialchars(date('d/m/Y', strtotime($solic_data_cad)))) ?>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap"><span
                                                                                class="hide_data"><?= date('Ymd', strtotime($res_data_cad)) ?></span><?= htmlspecialchars(htmlspecialchars(date('d/m/Y', strtotime($res_data_cad)))) ?>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap">
                                                                            <strong><?= htmlspecialchars($solic_codigo) ?></strong>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap">
                                                                            <?= htmlspecialchars($ch_programada) ?>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap">
                                                                            <strong><?= htmlspecialchars($oco_codigo) ?></strong>
                                                                        </td>
                                                                        <td><span
                                                                                class="<?= $borda_inicio_realizado ?>"><?= htmlspecialchars(date("H:i", strtotime($inicio_real))) ?></span>
                                                                        </td>
                                                                        <td><span
                                                                                class="<?= $borda_fim_realizado ?>"><?= htmlspecialchars(date("H:i", strtotime($fim_real))) ?></span>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap">
                                                                            <?= htmlspecialchars($realizada) ?>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap">
                                                                            <?= htmlspecialchars($faltante) ?>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap">
                                                                            <?= htmlspecialchars($a_mais) ?>
                                                                        </td>
                                                                        <td scope="row" nowrap="nowrap"> <span
                                                                                class="badge <?= $conflito_class ? 'bg_info_vermelho' : '' ?>"><?= $conflito_class ? 'CONFLITO' : '' ?></span>
                                                                        </td>
                                                                        <td class="text-end d-flex flex-row">
                                                                            <a class="btn btn_soft_azul btn-sm me-2"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#modal_edit_espaco"
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
                                                                                data-bs-res_mes="<?= $res_mes ?>"
                                                                                data-bs-res_ano="<?= $res_ano ?>"
                                                                                data-bs-res_data_inicio_semanal="<?= $res_data_inicio_semanal ?>"
                                                                                data-bs-res_data_fim_semanal="<?= $res_data_fim_semanal ?>"
                                                                                data-bs-res_dia_semana_fixa="<?= $row['res_dia_semana'] ?>"
                                                                                data-bs-res_hora_inicio="<?= date('H:i', strtotime($res_hora_inicio)) ?>"
                                                                                data-bs-res_hora_fim="<?= date('H:i', strtotime($res_hora_fim)) ?>"
                                                                                data-bs-res_turno="<?= $res_turno ?>"
                                                                                title="Editar">
                                                                                <i class="fa-regular fa-pen-to-square"></i>
                                                                            </a>

                                                                            <a href="../router/web.php?r=Reserv&acao=deletar&res_id=<?= $res_id ?>"
                                                                                class="btn btn_soft_vermelho btn-sm del-btn"><i
                                                                                    class="fa-regular fa-trash-can"></i></a>

                                                                            <div class="dropdown drop_tabela d-inline-block d-none">
                                                                                <button
                                                                                    class="btn btn_soft_verde_musgo btn-sm dropdown"
                                                                                    type="button" data-bs-toggle="dropdown"
                                                                                    aria-expanded="false">
                                                                                    <i class="ri-more-fill align-middle"></i>
                                                                                </button>
                                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                                    <li><a href=""
                                                                                            class="dropdown-item edit-item-btn"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#modal_edit_espaco"
                                                                                            data-bs-esp_id="id" title="Editar"><i
                                                                                                class="fa-regular fa-pen-to-square me-2"></i>
                                                                                            Editar</a></li>
                                                                                    <li><a href="../router/web.php?r=Reserv&acao=deletar&res_id=<?= $res_id ?>"
                                                                                            class="dropdown-item remove-item-btn del-btn"
                                                                                            title="Excluir"><i
                                                                                                class="fa-regular fa-trash-can me-2"></i>
                                                                                            Excluir</a></li>
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



                                                <script>
                                                    document.addEventListener('DOMContentLoaded', function () {
                                                        const checkboxes = document.querySelectorAll('.checkbox');
                                                        const btnExcluir = document.getElementById('btnExcluirSelecionados');
                                                        const formExcluirSelecionados = document.getElementById('formExcluirSelecionados');
                                                        const btnEditar = document.getElementById('btnEditarSelecionados');
                                                        const modalEdit = document.getElementById('modal_edit_espaco');

                                                        // Função para atualizar os botões de ação
                                                        function atualizarBotoesAcao() {
                                                            const algumMarcado = document.querySelectorAll('.checkbox:checked').length > 0;
                                                            btnExcluir.style.display = algumMarcado ? 'inline-block' : 'none';
                                                            btnEditar.style.display = algumMarcado ? 'inline-block' : 'none';
                                                        }

                                                        // Adiciona event listeners aos checkboxes
                                                        checkboxes.forEach(cb => cb.addEventListener('change', atualizarBotoesAcao));
                                                        document.getElementById('marcarTodos').addEventListener('change', function () {
                                                            checkboxes.forEach(cb => cb.checked = this.checked);
                                                            atualizarBotoesAcao();
                                                        });

                                                        // Event listener para o botão de Editar em Massa
                                                        btnEditar.addEventListener('click', function () {
                                                            const checkboxesSelecionados = document.querySelectorAll('.checkbox:checked');
                                                            const idsSelecionados = Array.from(checkboxesSelecionados).map(cb => cb.value).join(',');

                                                            if (idsSelecionados) {
                                                                // Pega a primeira linha da tabela com um checkbox marcado
                                                                const primeiraLinha = checkboxesSelecionados[0].closest('tr');
                                                                const button = primeiraLinha.querySelector('a[data-bs-toggle="modal"]');

                                                                if (button) {
                                                                    // Chama a função de preenchimento com os dados da primeira linha
                                                                    preencherModalEdicao(button);

                                                                    // Seta o campo de ID no modal com a lista completa de IDs
                                                                    modalEdit.querySelector('.res_id').value = idsSelecionados;

                                                                    // Oculta os campos de data de repetição para evitar confusão na edição em massa
                                                                    const tipoReserva = button.getAttribute('data-bs-res_tipo_reserva');
                                                                    if (tipoReserva === "2") {
                                                                        modalEdit.querySelector('#edit_data_inicio_semanal').closest('.col-6').style.display = 'none';
                                                                        modalEdit.querySelector('#edit_data_fim_semanal').closest('.col-6').style.display = 'none';
                                                                    } else {
                                                                        modalEdit.querySelector('#edit_data_reserva').closest('.col-6').style.display = 'none';
                                                                    }

                                                                    const myModal = new bootstrap.Modal(modalEdit);
                                                                    myModal.show();
                                                                }
                                                            }
                                                        });

                                                        // Event listener para o botão de Excluir Selecionados
                                                        formExcluirSelecionados.addEventListener('submit', function (e) {
                                                            e.preventDefault();
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
                                                                    formExcluirSelecionados.submit();
                                                                }
                                                            });
                                                        });

                                                        // Chama a função ao carregar a página para o estado inicial
                                                        atualizarBotoesAcao();
                                                    });
                                                </script>
                                                <script>
                                                    const modal_edit_espaco = document.getElementById('modal_edit_espaco');

                                                    if (modal_edit_espaco) {
                                                        modal_edit_espaco.addEventListener('show.bs.modal', event => {
                                                            const button = event.relatedTarget;
                                                            if (button) {
                                                                preencherModalEdicao(button);
                                                            }
                                                        });
                                                    }
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
                                                // Consulta 1: Buscar atividades de status
                                                $sql_status = "SELECT
                        sas.sta_an_data_upd AS data_registro,
                        sas.sta_an_obs AS obs,
                        admin.admin_nome AS nome_usuario,
                        ss.stsolic_status AS tipo_registro,
                        'status' AS tipo_evento
                    FROM
                        solicitacao_analise_status AS sas
                    INNER JOIN
                        status_solicitacao AS ss ON ss.stsolic_id = sas.sta_an_status
                    LEFT JOIN
                        admin ON admin.admin_id = sas.sta_an_user_id
                    WHERE
                        sas.sta_an_solic_id = :solic_id";

                                                $stmt_status = $conn->prepare($sql_status);
                                                $stmt_status->execute([':solic_id' => $solic_id]);
                                                $atividades_status = $stmt_status->fetchAll(PDO::FETCH_ASSOC);

                                                // Consulta 2: Buscar registros de ocorrências
                                                $sql_ocorrencias = "SELECT
                        oco.oco_data_cad AS data_registro,
                        oco.oco_obs AS obs,
                        admin.admin_nome AS nome_usuario,
                        oco.oco_tipo_ocorrencia AS tipo_ocorrencia_ids,
                        'ocorrencia' AS tipo_evento
                    FROM
                        ocorrencias AS oco
                    LEFT JOIN
                        admin ON admin.admin_id = oco.oco_user_id
                    WHERE
                        oco.oco_solic_id = :solic_id";

                                                $stmt_ocorrencias = $conn->prepare($sql_ocorrencias);
                                                $stmt_ocorrencias->execute([':solic_id' => $solic_id]);
                                                $registros_ocorrencia = $stmt_ocorrencias->fetchAll(PDO::FETCH_ASSOC);

                                                // Unir e ordenar os resultados
                                                $todos_registros = array_merge($atividades_status, $registros_ocorrencia);

                                                // A função de ordenação é crucial aqui
                                                usort($todos_registros, function ($a, $b) {
                                                    return strtotime($b['data_registro']) - strtotime($a['data_registro']);
                                                });

                                                foreach ($todos_registros as $row) {
                                                    $row = array_map('htmlspecialchars', $row);
                                                    extract($row);

                                                    // ÍCONE E NOME
                                                    $nome_atv = !empty($nome_usuario) ? $nome_usuario : 'Desconhecido';
                                                    $color_atv = 'icon_avatar_azul';

                                                    // PEGA O PRIMEIRO NOME E ÚLTIMO NOME
                                                    $partesNome = explode(" ", $nome_atv);
                                                    $primeiroNome = $partesNome[0];
                                                    $ultimoNome = end($partesNome);
                                                    $iniciais_analise = strtoupper(substr($primeiroNome, 0, 1) . substr($ultimoNome, 0, 1));

                                                    // CONFIGURAÇÃO DO STATUS E DESCRIÇÃO
                                                    $descricao = '';
                                                    $solic_status_color = 'bg_info_cinza';

                                                    if ($tipo_evento === 'ocorrencia') { // É uma ocorrência
                                                        $tipo_registro = 'Ocorrência Registrada';
                                                        $solic_status_color = 'bg_info_vermelho';
                                                        $tipos_ocorrencia_display = 'N/A';

                                                        $tipo_ids = trim($tipo_ocorrencia_ids);
                                                        if (!empty($tipo_ids)) {
                                                            $ids_array = array_filter(array_map('trim', explode(',', $tipo_ids)), 'ctype_digit');
                                                            if (count($ids_array) > 0) {
                                                                $tipo_ocorrencia_ids_sql = implode(',', $ids_array);
                                                                $sql_tipo_oco = "SELECT cto_tipo_ocorrencia FROM conf_tipo_ocorrencia WHERE cto_id IN ($tipo_ocorrencia_ids_sql)";
                                                                $stmt_oco = $conn->prepare($sql_tipo_oco);
                                                                $stmt_oco->execute();
                                                                $tipos_ocorrencia_db = $stmt_oco->fetchAll(PDO::FETCH_COLUMN);
                                                                $tipos_ocorrencia_display = implode(' / ', $tipos_ocorrencia_db);
                                                            }
                                                        }
                                                        $descricao = "Tipo de Ocorrência: " . $tipos_ocorrencia_display . ". Observação: " . htmlspecialchars_decode($obs);
                                                    } else { // É uma mudança de status
                                                        $status_colors = [
                                                            1 => 'bg_info_laranja',
                                                            2 => 'bg_info_azul',
                                                            5 => 'bg_info_roxo',
                                                            4 => 'bg_info_verde',
                                                            6 => 'bg_info_vermelho',
                                                            'Concluído' => 'bg_info_azul_escuro'
                                                        ];
                                                        $solic_status_color = $status_colors[$tipo_registro] ?? 'bg_info_cinza';
                                                        $descricao = htmlspecialchars_decode($obs);
                                                    }
                                                    ?>

                                                    <div class="line_time acitivity-item pb-3 d-flex">
                                                        <div class="flex-shrink-0">
                                                            <div class="icon_avatar <?= $color_atv ?>"><?= $iniciais_analise ?>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <span
                                                                class="badge align-middle mb-2 <?= $solic_status_color ?>"><?= $tipo_registro ?></span>
                                                            <h6 class="mb-1">
                                                                <?= date("d/m/Y H:i", strtotime($data_registro)) . ' ' . $primeiroNome . '&nbsp;' . $ultimoNome ?>
                                                            </h6>
                                                            <p class="text-muted mt-2">
                                                                <?php if ($tipo_evento === 'ocorrencia') {
                                                                    $tipos_ocorrencia = [];
                                                                    $tipo_ids = trim($tipo_ocorrencia_ids);
                                                                    if (!empty($tipo_ids)) {
                                                                        $ids_array = array_filter(array_map('trim', explode(',', $tipo_ids)), 'ctype_digit');
                                                                        if (count($ids_array) > 0) {
                                                                            $tipo_ocorrencia_ids_sql = implode(',', $ids_array);
                                                                            $sql_tipo_oco = "SELECT cto_tipo_ocorrencia FROM conf_tipo_ocorrencia WHERE cto_id IN ($tipo_ocorrencia_ids_sql)";
                                                                            $stmt_oco = $conn->prepare($sql_tipo_oco);
                                                                            $stmt_oco->execute();
                                                                            $tipos_ocorrencia = $stmt_oco->fetchAll(PDO::FETCH_COLUMN);
                                                                        }
                                                                    }
                                                                    $descricao = empty($tipos_ocorrencia) ? 'Detalhes: ' . htmlspecialchars_decode($obs) : 'Tipo de Ocorrência: ' . implode(' / ', $tipos_ocorrencia) . '. Observação: ' . htmlspecialchars_decode($obs);
                                                                    echo $descricao;
                                                                } else {
                                                                    echo htmlspecialchars_decode($obs);
                                                                }
                                                                ?>
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <?php
                                                }
                                            } catch (PDOException $e) {
                                                echo "Erro: " . $e->getMessage();
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="ocorrencias" role="tabpanel">
                                <div class="card">

                                    <div class="card-header">
                                        <div class="row align-items-center">
                                            <div
                                                class="col-sm-6 d-flex align-items-center d-flex justify-content-sm-start justify-content-center">
                                                <h5 class="card-title m-0 ps-2">Ocorrências</h5>
                                            </div>
                                            <div
                                                class="col-sm-6 d-flex align-items-center d-flex justify-content-sm-end justify-content-center">
                                                <button class="btn botao botao_amarelo waves-effect mt-3 mt-sm-0"
                                                    data-bs-toggle="modal" data-bs-toggle="button"
                                                    data-bs-target="#modal_cad_ocorrencia">+ Cadastrar
                                                    Ocorrência</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body p-0">
                                        <table id="tab_solic_user" class="table dt-responsive  align-middle"
                                            style="width:100%">
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
                                                            <td scope="row" nowrap="nowrap" class="text-uppercase">
                                                                <?= $and_andar ?>
                                                            </td>
                                                            <td scope="row" nowrap="nowrap" class="text-uppercase">
                                                                <?= $pav_pavilhao ?>
                                                            </td>
                                                            <td scope="row" class="text-uppercase"><?= $uni_unidade ?></td>
                                                            <td scope="row" class="text-uppercase"><?= $tipesp_tipo_espaco ?>
                                                            </td>
                                                            <th scope="row" nowrap="nowrap" class="text-bolder"><span
                                                                    class="hide_data"><?= date('Ymd', strtotime($res_data)) ?></span><?= htmlspecialchars(date('d/m/Y', strtotime($res_data))) ?>
                                                            </th>
                                                            <th scope="row" nowrap="nowrap" class="text-bolder"><span
                                                                    class="hide_data"><?= date('iH', strtotime($oco_hora_inicio_realizado)) ?></span><?= date('H:i', strtotime($oco_hora_inicio_realizado)) ?>
                                                            </th>
                                                            <th scope="row" nowrap="nowrap" class="text-bolder"><span
                                                                    class="hide_data"><?= date('iH', strtotime($oco_hora_fim_realizado)) ?></span><?= date('H:i', strtotime($oco_hora_fim_realizado)) ?>
                                                            </th>
                                                            <td scope="row" class="text-uppercase"><?= $admin_nome ?></td>
                                                            <td scope="row" nowrap="nowrap" class="text-bolder"><span
                                                                    class="hide_data"><?= date('Ymd', strtotime($oco_data_cad)) ?></span><?= date('d/m/Y H:i', strtotime($oco_data_cad)) ?>
                                                            </td>
                                                            <td class="text-end">
                                                                <div class="dropdown dropdown drop_tabela d-inline-block">
                                                                    <button class="btn btn_soft_verde_musgo btn-sm dropdown"
                                                                        type="button" data-bs-toggle="dropdown"
                                                                        aria-expanded="false">
                                                                        <i class="ri-more-fill align-middle"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                                        <li><a href="" class="dropdown-item edit-item-btn"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#modal_edit_ocorrencia"
                                                                                data-bs-oco_id="<?= $oco_id ?>"
                                                                                data-bs-oco_res_id="<?= $oco_res_id ?>"
                                                                                data-bs-oco_solic_id="<?= $oco_solic_id ?>"
                                                                                data-bs-oco_tipo_ocorrencia="<?= $oco_tipo_ocorrencia ?>"
                                                                                data-bs-oco_hora_inicio_realizado="<?= date('H:i', strtotime($oco_hora_inicio_realizado)) ?>"
                                                                                data-bs-oco_hora_fim_realizado="<?= date('H:i', strtotime($oco_hora_fim_realizado)) ?>"
                                                                                data-bs-oco_obs="<?= $oco_obs ?>"
                                                                                title="Editar"><i
                                                                                    class="fa-regular fa-pen-to-square me-2"></i>
                                                                                Editar</a></li>
                                                                        <li><a href="../router/web.php?r=Ocorrenc&acao=deletar&oco_id=<?= $oco_id ?>"
                                                                                class="dropdown-item remove-item-btn del-btn"
                                                                                title="Excluir"><i
                                                                                    class="fa-regular fa-trash-can me-2"></i>
                                                                                Excluir</a></li>
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


<?php include 'includes/modal/modal_deferir_solicitacao.php'; ?>
<?php include 'includes/modal/modal_indeferir_solicitacao.php'; ?>
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


<?php include 'includes/footer.php'; ?>
<script src="includes/select/completa_form.js"></script>
<script src="includes/select/select2.js"></script>




<script>
    // Funções auxiliares para preencher os campos do modal de edição
    function preencherCampos() {
        const dataInput = document.getElementById('cad_data_reserva').value;
        if (dataInput) {
            const partes = dataInput.split('-');
            const ano = parseInt(partes[0], 10);
            const mes = parseInt(partes[1], 10) - 1;
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

    function preencherCamposEdit() {
        const dataEditInputEdit = document.getElementById('edit_data_reserva').value;
        if (dataEditInputEdit) {
            const partes = dataEditInputEdit.split('-');
            const ano = parseInt(partes[0], 10);
            const mes = parseInt(partes[1], 10) - 1;
            const dia = parseInt(partes[2], 10);
            const data = new Date(Date.UTC(ano, mes, dia));
            const meses = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
            const diasSemanaCorrigido = [7, 1, 2, 3, 4, 5, 6];
            document.getElementById('edit_mes_reserva').value = meses[data.getUTCMonth()];
            document.getElementById('edit_ano_reserva').value = data.getUTCFullYear();
            document.getElementById('edit_diaSemana_reserva').value = diasSemanaCorrigido[data.getUTCDay()];
        }
    }

    // Função centralizada para preencher o modal de edição
    function preencherModalEdicao(button) {
        const modal_edit_espaco = document.getElementById('modal_edit_espaco');

        const res_id = button.getAttribute('data-bs-res_id');
        const res_campus = button.getAttribute('data-bs-res_campus');
        const res_espaco_id_cabula = button.getAttribute('data-bs-res_espaco_id_cabula');
        const res_espaco_id_brotas = button.getAttribute('data-bs-res_espaco_id_brotas');
        const res_quant_pessoas = button.getAttribute('data-bs-res_quant_pessoas');
        const res_recursos = button.getAttribute('data-bs-res_recursos');
        const res_recursos_add = button.getAttribute('data-bs-res_recursos_add');
        const res_obs = button.getAttribute('data-bs-res_obs');
        const res_tipo_aula = button.getAttribute('data-bs-res_tipo_aula');
        const res_curso = button.getAttribute('data-bs-res_curso');
        const res_curso_nome = button.getAttribute('data-bs-res_curso_nome');
        const res_curso_extensao = button.getAttribute('data-bs-res_curso_extensao');
        const res_semestre = button.getAttribute('data-bs-res_semestre');
        const res_componente_atividade = button.getAttribute('data-bs-res_componente_atividade');
        const res_componente_atividade_nome = button.getAttribute('data-bs-res_componente_atividade_nome');
        const res_nome_atividade = button.getAttribute('data-bs-res_nome_atividade');
        const res_modulo = button.getAttribute('data-bs-res_modulo');
        const res_professor = button.getAttribute('data-bs-res_professor');
        const res_titulo_aula = button.getAttribute('data-bs-res_titulo_aula');
        const res_tipo_reserva = button.getAttribute('data-bs-res_tipo_reserva');
        const res_data = button.getAttribute('data-bs-res_data');
        const res_mes = button.getAttribute('data-bs-res_mes');
        const res_ano = button.getAttribute('data-bs-res_ano');
        const res_hora_inicio = button.getAttribute('data-bs-res_hora_inicio');
        const res_hora_fim = button.getAttribute('data-bs-res_hora_fim');
        const res_turno = button.getAttribute('data-bs-res_turno');
        const res_dia_semana_fixa_id = button.getAttribute('data-bs-res_dia_semana_fixa');
        const res_data_inicio_semanal = button.getAttribute('data-bs-res_data_inicio_semanal');
        const res_data_fim_semanal = button.getAttribute('data-bs-res_data_fim_semanal');

        // Preenchimento dos campos gerais
        modal_edit_espaco.querySelector('.modal-title').textContent = 'Atualizar Dados';
        modal_edit_espaco.querySelector('.res_id').value = res_id;
        modal_edit_espaco.querySelector('.res_campus').value = res_campus;

        const selectLocalCabula = $('#edit_reserva_local_cabula');
        const selectLocalBrotas = $('#edit_reserva_local_brotas');
        if (res_campus === "1") {
            selectLocalCabula.val(res_espaco_id_cabula).trigger('change');
            selectLocalBrotas.val('').trigger('change');
        } else if (res_campus === "2") {
            selectLocalBrotas.val(res_espaco_id_brotas).trigger('change');
            selectLocalCabula.val('').trigger('change');
        }

        modal_edit_espaco.querySelector('.res_quant_pessoas').value = res_quant_pessoas;
        modal_edit_espaco.querySelector('.res_recursos').value = res_recursos;
        $('#edit_res_recursos_add').val(res_recursos_add.split(',').map(id => id.trim())).trigger('change');
        modal_edit_espaco.querySelector('.res_obs').value = res_obs;
        modal_edit_espaco.querySelector('.res_tipo_aula').value = res_tipo_aula;
        modal_edit_espaco.querySelector('.res_curso').value = res_curso;
        modal_edit_espaco.querySelector('.res_curso_nome').value = res_curso_nome;
        modal_edit_espaco.querySelector('.res_curso_extensao').value = res_curso_extensao;
        modal_edit_espaco.querySelector('.res_semestre').value = res_semestre;
        modal_edit_espaco.querySelector('.res_componente_atividade_nome').value = res_componente_atividade_nome;
        modal_edit_espaco.querySelector('.res_nome_atividade').value = res_nome_atividade;
        modal_edit_espaco.querySelector('.res_modulo').value = res_modulo;
        modal_edit_espaco.querySelector('.res_professor').value = res_professor;
        modal_edit_espaco.querySelector('.res_titulo_aula').value = res_titulo_aula;

        const select_edit_res_componente_atividade = $('#edit_res_componente_atividade');
        if (["2", "5", "6", "9", "13", "14", "17", "18", "21"].includes(res_curso)) {
            $.ajax({
                url: '../buscar_componentes.php',
                type: 'POST',
                data: {
                    curso_id: res_curso
                },
                success: function (data) {
                    select_edit_res_componente_atividade.html(data);
                    select_edit_res_componente_atividade.val(res_componente_atividade).trigger('change');
                    select_edit_res_componente_atividade.select2({
                        dropdownParent: $('#modal_edit_espaco'),
                        width: '100%',
                        language: {
                            noResults: () => "Dados não encontrados"
                        }
                    });
                }
            });
        } else {
            select_edit_res_componente_atividade.html('<option value="">Selecione um componente</option>').trigger('change');
        }

        const modal_res_tipo_reserva = modal_edit_espaco.querySelector('.res_tipo_reserva');
        modal_res_tipo_reserva.value = res_tipo_reserva;

        const modal_res_data = modal_edit_espaco.querySelector('#edit_data_reserva');
        const select_dia_semana_esporadica = document.getElementById('edit_diaSemana_reserva');
        const select_dia_semana_fixa = document.getElementById('edit_res_dia_semana_fixa');
        const modal_res_mes = modal_edit_espaco.querySelector('#edit_mes_reserva');
        const modal_res_ano = modal_edit_espaco.querySelector('#edit_ano_reserva');
        const modal_data_inicio_semanal = document.getElementById('edit_data_inicio_semanal');
        const modal_data_fim_semanal = document.getElementById('edit_data_fim_semanal');
        const modal_turno = document.getElementById('edit_turno');
        const modal_hora_inicio = document.getElementById('edit_res_hora_inicio');
        const modal_hora_fim = document.getElementById('edit_res_hora_fim');

        if (res_tipo_reserva === "2") {
            if (select_dia_semana_fixa) select_dia_semana_fixa.value = res_dia_semana_fixa_id;

            if (modal_data_inicio_semanal && modal_data_inicio_semanal._flatpickr) {
                modal_data_inicio_semanal._flatpickr.setDate(res_data_inicio_semanal);
            }
            if (modal_data_fim_semanal && modal_data_fim_semanal._flatpickr) {
                modal_data_fim_semanal._flatpickr.setDate(res_data_fim_semanal);
            }

        } else {
            if (modal_res_data && modal_res_data._flatpickr) {
                modal_res_data._flatpickr.setDate(res_data);
            }
            modal_res_mes.value = res_mes;
            modal_res_ano.value = res_ano;
            if (res_data) {
                preencherCamposEdit();
            }
        }

        modal_hora_inicio.value = res_hora_inicio;
        modal_hora_fim.value = res_hora_fim;
        modal_turno.value = res_turno;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const modalCadReserva = document.getElementById('modal_cad_espaco');
        const modalEditReserva = document.getElementById('modal_edit_espaco');
        const checkboxes = document.querySelectorAll('.checkbox');
        const btnExcluir = document.getElementById('btnExcluirSelecionados');
        const formExcluirSelecionados = document.getElementById('formExcluirSelecionados');
        const btnEditar = document.getElementById('btnEditarSelecionados');

        async function checkTimeConflict(contextModalId) {
            const currentModal = document.getElementById(contextModalId);
            if (!currentModal) {
                console.error(`Modal ${contextModalId} not found.`);
                return;
            }

            const currentData = currentModal.querySelector("input[id$='_data_reserva']");
            const currentInicio = currentModal.querySelector("input[id$='_hora_inicio']");
            const currentFim = currentModal.querySelector("input[id$='_hora_fim']");
            const currentLocalCabula = currentModal.querySelector("select[id$='_local_cabula']");
            const currentLocalBrotas = currentModal.querySelector("select[id$='_local_brotas']");

            if (!currentData || !currentInicio || !currentFim) {
                console.error(`Error: One or more date/time fields were not found in modal ${contextModalId}.`);
                return;
            }

            const localCabulaValue = currentLocalCabula ? currentLocalCabula.value : '';
            const localBrotasValue = currentLocalBrotas ? currentLocalBrotas.value : '';
            const dataValue = currentData.value;
            const inicioValue = currentInicio.value;
            const fimValue = currentFim.value;

            if (dataValue && inicioValue && fimValue && (localCabulaValue || localBrotasValue)) {
                try {
                    const response = await fetch(`verificar_conflito.php?localBrotas=${localBrotasValue}&localCabula=${localCabulaValue}&data=${dataValue}&hora_inicio=${inicioValue}&hora_fim=${fimValue}`);
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    const res = await response.json();
                    if (res.conflito) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Horário em Conflito',
                            text: 'Já existe uma reserva nesse intervalo para o local selecionado. Por favor, verifique.',
                            confirmButtonText: 'OK',
                        });
                    }
                } catch (error) {
                    console.error('Error checking time conflict:', error);
                    Swal.fire('Error', 'An error occurred while checking for time conflicts. Please try again.', 'error');
                }
            }
        }

        async function checkCapacity(selectedSpaceId, numPeopleValue, mode = 'change') {
            if (!selectedSpaceId || numPeopleValue === '' || numPeopleValue === null) {
                console.log('checkCapacity: selectedSpaceId or numPeopleValue is missing/invalid. Skipping verification.');
                return true;
            }

            const numPeopleInt = parseInt(numPeopleValue);
            if (isNaN(numPeopleInt) || numPeopleInt <= 0) {
                console.warn('checkCapacity: Invalid number of people. Ignoring alert.');
                return true;
            }

            try {
                const response = await fetch(`get_space_capacity.php?space_id=${selectedSpaceId}`);

                if (!response.ok) {
                    console.error(`checkCapacity: HTTP error in response: ${response.status} - ${response.statusText}`);
                    const errorText = await response.text();
                    console.error('Error response content:', errorText);
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                const data = await response.json();
                console.log('checkCapacity: Server response:', data);

                if (data.success && data.max_capacity !== null) {
                    const maxCapacity = parseInt(data.max_capacity);

                    if (numPeopleInt > maxCapacity) {
                        if (mode === 'change') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Capacidade Excedida!',
                                text: `O número de pessoas excede a capacidade máxima do local.`,
                                confirmButtonText: 'Ok',
                            });
                            return true;
                        } else if (mode === 'submit') {
                            const result = await Swal.fire({
                                icon: 'warning',
                                title: 'Capacidade Excedida!',
                                text: `O local selecionado tem capacidade máxima para ${maxCapacity} pessoas. Você informou ${numPeopleInt}. Deseja continuar com essa quantidade?`,
                                showCancelButton: true,
                                confirmButtonColor: '#285FAB',
                                cancelButtonColor: '#C4453E',
                                confirmButtonText: 'Continuar',
                                cancelButtonText: 'Cancelar',
                            });
                            return result.isConfirmed;
                        }
                    }
                } else {
                    console.warn(`checkCapacity: get_space_capacity.php did not return valid capacity data for space ${selectedSpaceId} (success: ${data.success}, max_capacity: ${data.max_capacity}). Proceeding.`);
                }
                return true;
            } catch (error) {
                console.error('checkCapacity: Error fetching space capacity:', error);
                Swal.fire('Erro', 'Ocorreu um erro ao verificar a capacidade do espaço. Por favor, tente novamente.', 'error');
                return false;
            }
        }

        function getSelectedSpaceId(modalElement) {
            const cadLocalCabulaSelect = modalElement.querySelector("#cad_reserva_local_cabula");
            const cadLocalBrotasSelect = modalElement.querySelector("#cad_reserva_local_brotas");
            if (cadLocalCabulaSelect && cadLocalCabulaSelect.value) {
                return cadLocalCabulaSelect.value;
            }
            if (cadLocalBrotasSelect && cadLocalBrotasSelect.value) {
                return cadLocalBrotasSelect.value;
            }

            const editLocalCabulaSelect = modalElement.querySelector("#edit_reserva_local_cabula");
            const editLocalBrotasSelect = modalElement.querySelector("#edit_reserva_local_brotas");
            if (editLocalCabulaSelect && editLocalCabulaSelect.value) {
                return editLocalCabulaSelect.value;
            }
            if (editLocalBrotasSelect && editLocalBrotasSelect.value) {
                return editLocalBrotasSelect.value;
            }

            return null;
        }

        // Event listener para abrir o modal de edição individual
        if (modalEditReserva) {
            modalEditReserva.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                if (button) {
                    preencherModalEdicao(button);
                }
            });
        }

        if (modalCadReserva) {
            modalCadReserva.addEventListener('shown.bs.modal', function () {
                const formCadReserva = this.querySelector('form');
                const cadNumPessoasInput = this.querySelector("#cad_reserva_quant_pessoas");
                const cadLocalCabulaSelect = this.querySelector("#cad_reserva_local_cabula");
                const cadLocalBrotasSelect = this.querySelector("#cad_reserva_local_brotas");
                const cadCapacMaximaInput = this.querySelector("#esp_quant_maxima");

                if (formCadReserva && cadNumPessoasInput) {
                    const handleCadCapacityCheck = async () => {
                        const selectedSpaceId = getSelectedSpaceId(modalCadReserva);
                        await checkCapacity(selectedSpaceId, cadNumPessoasInput.value, 'change');
                        if (selectedSpaceId && cadCapacMaximaInput) {
                            try {
                                const response = await fetch(`get_space_capacity.php?space_id=${selectedSpaceId}`);
                                const data = await response.json();
                                if (data.success && data.max_capacity !== null) {
                                    cadCapacMaximaInput.value = data.max_capacity;
                                } else {
                                    cadCapacMaximaInput.value = '';
                                }
                            } catch (error) {
                                cadCapacMaximaInput.value = 'Erro';
                            }
                        }
                    };

                    cadNumPessoasInput.removeEventListener('input', handleCadCapacityCheck);
                    if (cadLocalCabulaSelect) cadLocalCabulaSelect.removeEventListener('change', handleCadCapacityCheck);
                    if (cadLocalBrotasSelect) cadLocalBrotasSelect.removeEventListener('change', handleCadCapacityCheck);

                    cadNumPessoasInput.addEventListener('input', handleCadCapacityCheck);
                    if (cadLocalCabulaSelect) cadLocalCabulaSelect.addEventListener('change', handleCadCapacityCheck);
                    if (cadLocalBrotasSelect) cadLocalBrotasSelect.addEventListener('change', handleCadCapacityCheck);

                    formCadReserva.removeEventListener('submit', formSubmitHandler);
                    formCadReserva.addEventListener('submit', formSubmitHandler);

                    async function formSubmitHandler(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        const selectedSpaceId = getSelectedSpaceId(modalCadReserva);
                        const numPeopleValue = cadNumPessoasInput.value;
                        const canSubmit = await checkCapacity(selectedSpaceId, numPeopleValue, 'submit');
                        if (canSubmit) {
                            this.submit();
                        }
                    }
                    handleCadCapacityCheck();
                }

                const cadDataInput = this.querySelector("#cad_data_reserva");
                const cadInicioInput = this.querySelector("#cad_res_hora_inicio");
                const cadFimInput = this.querySelector("#cad_res_hora_fim");

                if (cadDataInput) {
                    cadDataInput.removeEventListener("change", () => checkTimeConflict('modal_cad_espaco'));
                    cadDataInput.addEventListener("change", () => checkTimeConflict('modal_cad_espaco'));
                }
                if (cadInicioInput) {
                    cadInicioInput.removeEventListener("change", () => checkTimeConflict('modal_cad_espaco'));
                    cadInicioInput.addEventListener("change", () => checkTimeConflict('modal_cad_espaco'));
                }
                if (cadFimInput) {
                    cadFimInput.removeEventListener("change", () => checkTimeConflict('modal_cad_espaco'));
                    cadFimInput.addEventListener("change", () => checkTimeConflict('modal_cad_espaco'));
                }
            });
        }

        if (modalEditReserva) {
            modalEditReserva.addEventListener('shown.bs.modal', function () {
                const formEditReserva = this.querySelector('form');
                const editNumPessoasInput = this.querySelector("#edit_reserva_quant_pessoas");
                const editLocalCabulaSelect = this.querySelector("#edit_reserva_local_cabula");
                const editLocalBrotasSelect = this.querySelector("#edit_reserva_local_brotas");
                const editCapacMaximaInput = this.querySelector("#edit_reserva_camp_maximo");

                if (formEditReserva && editNumPessoasInput) {
                    const handleEditCapacityCheck = async () => {
                        const selectedSpaceId = getSelectedSpaceId(modalEditReserva);
                        await checkCapacity(selectedSpaceId, editNumPessoasInput.value, 'change');
                        if (selectedSpaceId && editCapacMaximaInput) {
                            try {
                                const response = await fetch(`get_space_capacity.php?space_id=${selectedSpaceId}`);
                                const data = await response.json();
                                if (data.success && data.max_capacity !== null) {
                                    editCapacMaximaInput.value = data.max_capacity;
                                } else {
                                    editCapacMaximaInput.value = '';
                                }
                            } catch (error) {
                                editCapacMaximaInput.value = 'Erro';
                            }
                        }
                    };

                    editNumPessoasInput.removeEventListener('input', handleEditCapacityCheck);
                    if (editLocalCabulaSelect) editLocalCabulaSelect.removeEventListener('change', handleEditCapacityCheck);
                    if (editLocalBrotasSelect) editLocalBrotasSelect.removeEventListener('change', handleEditCapacityCheck);

                    editNumPessoasInput.addEventListener('input', handleEditCapacityCheck);
                    if (editLocalCabulaSelect) editLocalCabulaSelect.addEventListener('change', handleEditCapacityCheck);
                    if (editLocalBrotasSelect) editLocalBrotasSelect.addEventListener('change', handleEditCapacityCheck);

                    formEditReserva.removeEventListener('submit', editFormSubmitHandler);
                    formEditReserva.addEventListener('submit', editFormSubmitHandler);

                    async function editFormSubmitHandler(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        const selectedSpaceId = getSelectedSpaceId(modalEditReserva);
                        const numPeopleEditValue = editNumPessoasInput.value;
                        const canSubmit = await checkCapacity(selectedSpaceId, numPeopleEditValue, 'submit');
                        if (canSubmit) {
                            this.submit();
                        }
                    }
                    handleEditCapacityCheck();
                }

                const editDataInput = this.querySelector("#edit_data_reserva");
                const editInicioInput = this.querySelector("#edit_res_hora_inicio");
                const editFimInput = this.querySelector("#edit_res_hora_fim");

                if (editDataInput) {
                    editDataInput.removeEventListener("change", () => checkTimeConflict('modal_edit_espaco'));
                    editDataInput.addEventListener("change", () => checkTimeConflict('modal_edit_espaco'));
                }
                if (editInicioInput) {
                    editInicioInput.removeEventListener("change", () => checkTimeConflict('modal_edit_espaco'));
                    editInicioInput.addEventListener("change", () => checkTimeConflict('modal_edit_espaco'));
                }
                if (editFimInput) {
                    editFimInput.removeEventListener("change", () => checkTimeConflict('modal_edit_espaco'));
                    editFimInput.addEventListener("change", () => checkTimeConflict('modal_edit_espaco'));
                }
            });
        }

        function atualizarBotoesAcao() {
            const algumMarcado = document.querySelectorAll('.checkbox:checked').length > 0;
            btnExcluir.style.display = algumMarcado ? 'inline-block' : 'none';
            btnEditar.style.display = algumMarcado ? 'inline-block' : 'none';
        }

        checkboxes.forEach(cb => cb.addEventListener('change', atualizarBotoesAcao));
        document.getElementById('marcarTodos').addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = this.checked);
            atualizarBotoesAcao();
        });

        btnEditar.addEventListener('click', function () {
            const checkboxesSelecionados = document.querySelectorAll('.checkbox:checked');
            const idsSelecionados = Array.from(checkboxesSelecionados).map(cb => cb.value).join(',');

            // if (idsSelecionados) {
            //   const primeiraLinha = checkboxesSelecionados[0].closest('tr');
            //   const button = primeiraLinha.querySelector('a[data-bs-toggle="modal"]');

            if (idsSelecionados) {
                const primeiraLinha = checkboxesSelecionados[0].closest('tr');
                const button = primeiraLinha.querySelector('a[data-bs-toggle="modal"]');

                const modalEdit = document.getElementById('modal_edit_espaco');

                // if (button) {
                //   preencherModalEdicao(button);

                //   modalEdit.querySelector('.res_id').value = idsSelecionados;

                if (button && modalEdit) {
                    preencherModalEdicao(button);
                    modalEdit.querySelector('.res_id').value = idsSelecionados;


                    const tipoReserva = button.getAttribute('data-bs-res_tipo_reserva');
                    if (tipoReserva === "2") {
                        modalEdit.querySelector('#edit_data_inicio_semanal').closest('.col-6').style.display = 'none';
                        modalEdit.querySelector('#edit_data_fim_semanal').closest('.col-6').style.display = 'none';
                    } else {
                        modalEdit.querySelector('#edit_data_reserva').closest('.col-6').style.display = 'none';
                    }

                    // const myModal = new bootstrap.Modal(modalEdit);
                    // myModal.show();

                    // Por esta nova lógica:
                    const myModalInstance = bootstrap.Modal.getInstance(modalEdit) || new bootstrap.Modal(modalEdit);
                    myModalInstance.show();
                }
            }
        });

        formExcluirSelecionados.addEventListener('submit', function (e) {
            e.preventDefault();
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
                    formExcluirSelecionados.submit();
                }
            });
        });

        atualizarBotoesAcao();
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btnCadastrarReserva = document.getElementById('btn_cadastrar_reserva');
        const modalReserva = new bootstrap.Modal(document.getElementById('modal_cad_espaco'));

        if (btnCadastrarReserva) {
            btnCadastrarReserva.addEventListener('click', function () {
                const solicId = this.getAttribute('data-bs-solic_id');

                // Chama o script para registrar a edição via AJAX
                fetch('includes/modal/registrar_edicao.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `solic_id=${solicId}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Se for um sucesso, abre o modal
                            modalReserva.show();
                        } else {
                            // Se houver um erro (outra pessoa editando), exibe o SweetAlert
                            Swal.fire({
                                icon: 'warning',
                                title: 'Reserva em edição',
                                html: data.message,
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro de conexão',
                            text: 'Não foi possível verificar o status da reserva. Tente novamente.',
                            confirmButtonText: 'OK'
                        });
                    });
            });
        }

    });
</script>