<?php
// BUSCAS AS HORAS DE FUNCIONAMENTO PARA LIMITAR NOS CAMPOS DE HORAS DE INÍCIO E FIM
$sql = "SELECT MIN(chf_hora_inicio) AS hora_inicio, MAX(chf_hora_fim) AS hora_fim FROM conf_hora_funcionamento";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$limit_hora_inicio = substr($result['hora_inicio'], 0, 5); // "07:00"
$limit_hora_fim = substr($result['hora_fim'], 0, 5);       // "21:00"
?>

<div class="modal fade modal_padrao" id="modal_cad_espaco" aria-hidden="true" tabindex="-1">

    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal_padrao_cinza">
                <h5 class="modal-title">Confirmar Reserva</h5>
                <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="form_reserva" class="needs-validation" action="../router/web.php?r=Reserv" method="POST"
                novalidate>

                <input type="hidden" class="form-control" name="res_solic_id" value="<?= $_GET['i'] ?>" required>
                <input type="hidden" class="form-control" name="acao" value="cadastrar" required>

                <div class="etapa" id="etapa1">
                    <div class="modal-body">

                        <div id="custom-progress-bar" class="progress-nav mb-5 mt-2">
                            <div class="progress" style="height: 1px;">
                                <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0"
                                    aria-valuemin="0" aria-valuemax="0">
                                </div>
                            </div>

                            <ul class="nav nav-pills progress-bar-tab custom-nav" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill active" data-progressbar="custom-progress-bar"
                                        id="pills-gen-info-tab" data-bs-toggle="pill" data-bs-target="#pills-gen-info"
                                        role="tab" aria-controls="pills-gen-info" aria-selected="false"
                                        data-position="0" tabindex="-1" disabled>Local</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill" data-progressbar="custom-progress-bar"
                                        id="pills-info-desc-tab" data-bs-toggle="pill" data-bs-target="#pills-info-desc"
                                        role="tab" aria-controls="pills-info-desc" aria-selected="false"
                                        data-position="1" tabindex="-1" disabled>Atividade</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill" data-progressbar="custom-progress-bar"
                                        id="pills-success-tab" data-bs-toggle="pill" data-bs-target="#pills-success"
                                        role="tab" aria-controls="pills-success" aria-selected="true" data-position="2"
                                        disabled>Período</button>
                                </li>
                            </ul>
                        </div>

                        <div class="row g-3">






                            <div class="col-xl-3">
                                <?php
                                try {
                                    // Obtenha o ID da solicitação diretamente da URL
                                    $solicId = $_GET['i'];

                                    // Consulta para obter o campus das aulas práticas e teóricas
                                    $stmt = $conn->prepare("SELECT solic_ap_campus, solic_at_campus FROM solicitacao WHERE solic_id = ?");
                                    $stmt->execute([$solicId]);
                                    $solicitacao = $stmt->fetch(PDO::FETCH_ASSOC);

                                    $selected_campus = '';
                                    if ($solicitacao) {
                                        $solic_ap_campus = $solicitacao['solic_ap_campus'];
                                        $solic_at_campus = $solicitacao['solic_at_campus'];

                                        if (!empty($solic_ap_campus) && ($solic_ap_campus == $solic_at_campus || empty($solic_at_campus))) {
                                            $selected_campus = $solic_ap_campus;
                                        } else if (!empty($solic_at_campus) && empty($solic_ap_campus)) {
                                            $selected_campus = $solic_at_campus;
                                        }
                                    }

                                    $sql = $conn->prepare("SELECT uni_id, uni_unidade FROM unidades");
                                    $sql->execute();
                                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);

                                } catch (PDOException $e) {
                                    // Exibe uma mensagem de erro em caso de falha na consulta
                                    echo "Erro ao tentar recuperar os dados do campus: " . $e->getMessage();
                                }
                                ?>
                                <label class="form-label">Campus <span>*</span></label>
                                <select class="form-select text-uppercase" name="res_campus" id="cad_reserva_campus"
                                    required>
                                    <option selected disabled value="">Selecione</option>
                                    <?php foreach ($result as $res): ?>
                                        <option value="<?= $res['uni_id'] ?>" <?= ($res['uni_id'] == $selected_campus) ? 'selected' : '' ?>>
                                            <?= $res['uni_unidade'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>




                            <div class="col-xl-9" id="camp_reserv_campus">
                                <label class="form-label">Local</label>
                                <select class="form-select text-uppercase" disabled></select>
                            </div>

                            <div class="col-xl-9" id="camp_reserv_local_cabula" style="display: none;">
                                <?php try {
                                    $sql = $conn->prepare("SELECT esp_id, esp_codigo, esp_nome_local FROM espaco WHERE esp_unidade = 1 ORDER BY esp_nome_local ASC");
                                    $sql->execute();
                                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Erro ao tentar recuperar os dados";
                                } ?>
                                <label class="form-label">Local <span>*</span></label>
                                <select class="form-select text-uppercase" name="res_espaco_id_cabula"
                                    id="cad_reserva_local_cabula">
                                    <option selected disabled value=""></option>
                                    <?php foreach ($result as $res): ?>
                                        <option value="<?= $res['esp_id'] ?>">
                                            <?= $res['esp_codigo'] . ' - ' . $res['esp_nome_local'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <div class="col-xl-9" id="camp_reserv_local_brotas" style="display: none;">
                                <?php try {
                                    $sql = $conn->prepare("SELECT esp_id, esp_codigo, esp_nome_local FROM espaco WHERE esp_unidade = 2 ORDER BY esp_nome_local ASC");
                                    $sql->execute();
                                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Erro ao tentar recuperar os dados";
                                } ?>
                                <label class="form-label">Local <span>*</span></label>
                                <select class="form-select text-uppercase" name="res_espaco_id_brotas"
                                    id="cad_reserva_local_brotas">
                                    <option selected disabled value=""></option>
                                    <?php foreach ($result as $res): ?>
                                        <option value="<?= $res['esp_id'] ?>">
                                            <?= $res['esp_codigo'] . ' - ' . $res['esp_nome_local'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>
                            <!-- <script>
                                const cad_reserva_campus = document.getElementById("cad_reserva_campus");
                                const camp_reserv_campus = document.getElementById("camp_reserv_campus");
                                const camp_reserv_local_cabula = document.getElementById("camp_reserv_local_cabula");
                                const camp_reserv_local_brotas = document.getElementById("camp_reserv_local_brotas");

                                cad_reserva_campus.addEventListener("change", function () {
                                    if (cad_reserva_campus.value === "1") {
                                        camp_reserv_campus.style.display = "none";
                                        //
                                        camp_reserv_local_cabula.style.display = "block";
                                        // document.getElementById("cad_reserva_local_cabula").required = true;
                                        $('#cad_reserva_local_cabula').prop('required', true);
                                        //
                                        camp_reserv_local_brotas.style.display = "none";
                                        // document.getElementById("cad_reserva_local_brotas").required = false;
                                        $('#cad_reserva_local_brotas').prop('required', false);
                                        //
                                        $('#cad_reserva_local_cabula').val(null).trigger('change'); // APAGA OS DADOS DE UM SELECT2
                                        $('#cad_reserva_local_brotas').val(null).trigger('change'); // APAGA OS DADOS DE UM SELECT2
                                        document.getElementById("cad_reserva_tipo_sala").value = '';
                                        document.getElementById("cad_reserva_andar").value = '';
                                        document.getElementById("cad_reserva_pavilhao").value = '';
                                        document.getElementById("esp_quant_maxima").value = '';
                                        document.getElementById("cad_reserva_camp_media").value = '';
                                        document.getElementById("esp_quant_minima").value = '';
                                    } else {
                                        camp_reserv_campus.style.display = "none";
                                        //
                                        camp_reserv_local_cabula.style.display = "none";
                                        document.getElementById("cad_reserva_local_cabula").required = false;
                                        //
                                        camp_reserv_local_brotas.style.display = "block";
                                        document.getElementById("cad_reserva_local_brotas").required = true;
                                        //
                                        $('#cad_reserva_local_cabula').val(null).trigger('change'); // APAGA OS DADOS DE UM SELECT2
                                        $('#cad_reserva_local_brotas').val(null).trigger('change'); // APAGA OS DADOS DE UM SELECT2
                                        document.getElementById("cad_reserva_tipo_sala").value = '';
                                        document.getElementById("cad_reserva_andar").value = '';
                                        document.getElementById("cad_reserva_pavilhao").value = '';
                                        document.getElementById("esp_quant_maxima").value = '';
                                        document.getElementById("cad_reserva_camp_media").value = '';
                                        document.getElementById("esp_quant_minima").value = '';
                                    }
                                });
                            </script> -->

                            <script>
                                // Crie uma função para a lógica de atualização
                                function atualizarLocalPorCampus() {
                                    const cad_reserva_campus = document.getElementById("cad_reserva_campus");
                                    const camp_reserv_campus = document.getElementById("camp_reserv_campus");
                                    const camp_reserv_local_cabula = document.getElementById("camp_reserv_local_cabula");
                                    const camp_reserv_local_brotas = document.getElementById("camp_reserv_local_brotas");

                                    if (cad_reserva_campus.value === "1") {
                                        camp_reserv_campus.style.display = "none";
                                        //
                                        camp_reserv_local_cabula.style.display = "block";
                                        $('#cad_reserva_local_cabula').prop('required', true);
                                        //
                                        camp_reserv_local_brotas.style.display = "none";
                                        $('#cad_reserva_local_brotas').prop('required', false);
                                        //
                                        $('#cad_reserva_local_cabula').val(null).trigger('change');
                                        $('#cad_reserva_local_brotas').val(null).trigger('change');
                                        document.getElementById("cad_reserva_tipo_sala").value = '';
                                        document.getElementById("cad_reserva_andar").value = '';
                                        document.getElementById("cad_reserva_pavilhao").value = '';
                                        document.getElementById("esp_quant_maxima").value = '';
                                        document.getElementById("cad_reserva_camp_media").value = '';
                                        document.getElementById("esp_quant_minima").value = '';
                                    } else if (cad_reserva_campus.value === "2") {
                                        camp_reserv_campus.style.display = "none";
                                        //
                                        camp_reserv_local_cabula.style.display = "none";
                                        document.getElementById("cad_reserva_local_cabula").required = false;
                                        //
                                        camp_reserv_local_brotas.style.display = "block";
                                        document.getElementById("cad_reserva_local_brotas").required = true;
                                        //
                                        $('#cad_reserva_local_cabula').val(null).trigger('change');
                                        $('#cad_reserva_local_brotas').val(null).trigger('change');
                                        document.getElementById("cad_reserva_tipo_sala").value = '';
                                        document.getElementById("cad_reserva_andar").value = '';
                                        document.getElementById("cad_reserva_pavilhao").value = '';
                                        document.getElementById("esp_quant_maxima").value = '';
                                        document.getElementById("cad_reserva_camp_media").value = '';
                                        document.getElementById("esp_quant_minima").value = '';
                                    } else {
                                        // Se o campus não estiver selecionado (opção "Selecione"), mostre o campo inicial
                                        camp_reserv_campus.style.display = "block";
                                        camp_reserv_local_cabula.style.display = "none";
                                        camp_reserv_local_brotas.style.display = "none";
                                    }
                                }

                                // Chame a função quando o valor do campus mudar
                                document.getElementById("cad_reserva_campus").addEventListener("change",
                                    atualizarLocalPorCampus);

                                // Chame a função quando o modal for exibido
                                document.getElementById('modal_cad_espaco').addEventListener('shown.bs.modal', function () {
                                    atualizarLocalPorCampus();
                                });
                            </script>

                            <div class="col-xl-3">
                                <?php try {
                                    $sql = $conn->prepare("SELECT * FROM andares ORDER BY and_andar");
                                    $sql->execute();
                                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Erro ao tentar recuperar o perfil";
                                } ?>
                                <label class="form-label">Tipo de Sala</label>
                                <select class="form-select text-uppercase" id="cad_reserva_tipo_sala" disabled>
                                    <option selected value=""></option>
                                    <?php foreach ($result as $res): ?>
                                        <option value="<?= $res['and_id'] ?>"><?= $res['and_andar'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-xl-3">
                                <?php try {
                                    $sql = $conn->prepare("SELECT * FROM andares ORDER BY and_andar");
                                    $sql->execute();
                                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Erro ao tentar recuperar o perfil";
                                } ?>
                                <label class="form-label">Andar</label>
                                <select class="form-select text-uppercase" id="cad_reserva_andar" disabled>
                                    <option selected value=""></option>
                                    <?php foreach ($result as $res): ?>
                                        <option value="<?= $res['and_id'] ?>"><?= $res['and_andar'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-xl-3">
                                <?php try {
                                    $sql = $conn->prepare("SELECT * FROM pavilhoes ORDER BY pav_pavilhao");
                                    $sql->execute();
                                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Erro ao tentar recuperar o perfil";
                                } ?>
                                <label class="form-label">Pavilhão</label>
                                <select class="form-select text-uppercase" id="cad_reserva_pavilhao" disabled>
                                    <option selected value=""></option>
                                    <?php foreach ($result as $res): ?>
                                        <option value="<?= $res['pav_id'] ?>"><?= $res['pav_pavilhao'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-xl-3">
                                <label class="form-label">Capac. Máxima</label>
                                <input type="text" class="form-control" id="esp_quant_maxima" disabled>
                            </div>

                            <div class="col-xl-3">
                                <label class="form-label">Capac. Média</label>
                                <input type="text" class="form-control" id="cad_reserva_camp_media" disabled>
                            </div>

                            <div class="col-xl-3">
                                <label class="form-label">Capac. Mínima</label>
                                <input type="text" class="form-control" id="esp_quant_minima" disabled>
                            </div>

                            <div class="col-xl-3">
                                <label class="form-label">Nº Pessoas <span>*</span></label>
                                <input type="text" class="form-control" id="cad_reserva_quant_pessoas"
                                    name="res_quant_pessoas" oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                    required>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <div class="col-xl-3">
                                <label class="form-label">Recursos Audiovisuais <span>*</span></label>
                                <select class="form-select text-uppercase" name="res_recursos" id="cad_res_recursos"
                                    required>
                                    <option value="NÃO">NÃO</option>
                                    <option value="SIM">SIM</option>
                                </select>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <div class="col-xl-12" id="campo_cad_res_recursos_add" style="display: none;">
                                <?php try {
                                    $sql = $conn->prepare("SELECT rec_id, rec_recurso FROM recursos ORDER BY rec_recurso");
                                    $sql->execute();
                                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Erro ao tentar recuperar o perfil";
                                } ?>
                                <label class="form-label">Recursos Audiovisuais Adicionais <span>*</span></label>
                                <select class="form-select text-uppercase" name="res_recursos_add[]" multiple
                                    id="cad_res_recursos_add">
                                    <?php foreach ($result as $res): ?>
                                        <option value="<?= $res['rec_id'] ?>"><?= $res['rec_recurso'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                                <script>
                                    $(document).ready(function () {
                                        $('#cad_res_recursos_add').select2({
                                            placeholder: "Selecione as opções",
                                            tags: false,
                                            allowClear: true,
                                            dropdownParent: $('#modal_cad_espaco'),
                                            width: '100%'
                                        });
                                    });
                                </script>
                            </div>

                            <script>
                                const cad_res_recursos = document.getElementById("cad_res_recursos");
                                const campo_cad_res_recursos_add = document.getElementById("campo_cad_res_recursos_add");

                                cad_res_recursos.addEventListener("change", function () {
                                    if (cad_res_recursos.value === "SIM") {
                                        campo_cad_res_recursos_add.style.display = "block";
                                        document.getElementById("cad_res_recursos_add").required = true;
                                    } else {
                                        campo_cad_res_recursos_add.style.display = "none";
                                        document.getElementById("cad_res_recursos_add").required = false;
                                    }
                                });
                            </script>

                            <div class="col-12">
                                <label class="form-label">Observações</label>
                                <textarea class="form-control" id="meuTextarea" name="res_obs" rows="3" cols="50"
                                    maxlength="200"></textarea>
                                <p class="label_info text-end mt-1">Caracteres restantes: <span id="contador">200</span>
                                </p>
                                <script>
                                    const textarea = document.getElementById('meuTextarea');
                                    const contador = document.getElementById('contador');
                                    const limite = 200;

                                    textarea.addEventListener('input', function () {
                                        const total = textarea.value.length;
                                        contador.textContent = `${total} / ${limite}`;
                                    });
                                </script>
                            </div>

                            <div class="col-lg-12">
                                <div class="hstack gap-3 align-items-center justify-content-end mt-3">
                                    <p class="label_asterisco m-0"><span>*</span> Campo obrigatório</p>
                                    <button type="button"
                                        class="btn botao_azul_escuro btn-label right ms-auto nexttab nexttab waves-effect"
                                        id="btnProximo1" data-form="form_etapa1" data-next="#modal_cad_espaco2"><i
                                            class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>
                                        Próximo</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="etapa d-none" id="etapa2">
                    <div class="modal-body">

                        <div id="custom-progress-bar" class="progress-nav mb-5 mt-2">
                            <div class="progress" style="height: 1px;">
                                <div class="progress-bar" role="progressbar" style="width: 50%;" aria-valuenow="0"
                                    aria-valuemin="0" aria-valuemax="0">
                                </div>
                            </div>

                            <ul class="nav nav-pills progress-bar-tab custom-nav" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill done" data-progressbar="custom-progress-bar"
                                        id="pills-gen-info-tab" data-bs-toggle="pill" data-bs-target="#pills-gen-info"
                                        type="button" role="tab" aria-controls="pills-gen-info" aria-selected="false"
                                        data-position="0" tabindex="-1" disabled>Local</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill active" data-progressbar="custom-progress-bar"
                                        id="pills-info-desc-tab" data-bs-toggle="pill" data-bs-target="#pills-info-desc"
                                        type="button" role="tab" aria-controls="pills-info-desc" aria-selected="false"
                                        data-position="1" tabindex="-1" disabled>Atividade</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill" data-progressbar="custom-progress-bar"
                                        id="pills-success-tab" data-bs-toggle="pill" data-bs-target="#pills-success"
                                        type="button" role="tab" aria-controls="pills-success" aria-selected="true"
                                        data-position="2" disabled>Período</button>
                                </li>
                            </ul>
                        </div>

                        <div class="row g-3">

                            <div class="col-sm">
                                <?php try {
                                    $sql = $conn->prepare("SELECT * FROM conf_tipo_aula ORDER BY cta_tipo_aula");
                                    $sql->execute();
                                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Erro ao tentar recuperar o perfil";
                                } ?>
                                <label class="form-label">Tipo de Aula <span>*</span></label>
                                <select class="form-select text-uppercase" name="res_tipo_aula" required>

                                    <?php if ($solic_ap_aula_pratica == 1 && $solic_at_aula_teorica == 0) { ?>
                                        <option selected value="1">PRÁTICA</option>
                                    <?php } else if ($solic_at_aula_teorica == 1 && $solic_ap_aula_pratica == 0) { ?>
                                            <option selected value="2">TEÓRICA</option>
                                    <?php } else { ?>
                                            <option selected disabled value=""></option>
                                    <?php } ?>

                                    <?php foreach ($result as $res): ?>
                                        <option value="<?= $res['cta_id'] ?>"><?= $res['cta_tipo_aula'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <div class="col-sm">
                                <?php try {
                                    $sql = $conn->prepare("SELECT curs_id, curs_curso FROM cursos WHERE curs_status != 0 ORDER BY curs_curso");
                                    $sql->execute();
                                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Erro ao tentar recuperar os dados";
                                } ?>
                                <label class="form-label">Curso <span>*</span></label>
                                <select class="form-select text-uppercase" name="res_curso" id="cad_res_curso">
                                    <option selected value="<?= $solic_row['curs_id'] ?>">
                                        <?= $solic_row['curs_curso'] ?>
                                    </option>
                                    <?php foreach ($result as $res): ?>
                                        <option value="<?= $res['curs_id'] ?>"><?= $res['curs_curso'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <div class="col-sm" id="campo_res_nome_curso" style="display: none;">
                                <?php try {
                                    $sql = $conn->prepare("SELECT cexc_id, cexc_curso FROM conf_cursos_extensao_curricularizada ORDER BY cexc_curso");
                                    $sql->execute();
                                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Erro ao tentar recuperar os dados";
                                } ?>
                                <label class="form-label">Nome do Curso <span>*</span></label>
                                <select class="form-select text-uppercase" name="res_curso_extensao"
                                    id="cad_res_curso_extensao">
                                    <option selected value="<?= $solic_row['cexc_id'] ?>">
                                        <?= $solic_row['cexc_curso'] ?>
                                    </option>
                                    <?php foreach ($result as $res): ?>
                                        <option value="<?= $res['cexc_id'] ?>"><?= $res['cexc_curso'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <div class="col-sm">
                                <?php try {
                                    $sql = $conn->prepare("SELECT cs_id, cs_semestre FROM conf_semestre ORDER BY cs_id");
                                    $sql->execute();
                                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Erro ao tentar recuperar os dados";
                                } ?>
                                <label class="form-label">Semestre</label>
                                <select class="form-select text-uppercase" name="res_semestre" id="cad_res_semestre">
                                    <option selected value="<?= $solic_row['cs_id'] ?>"><?= $solic_row['cs_semestre'] ?>
                                    </option>
                                    <?php foreach ($result as $res): ?>
                                        <option value="<?= $res['cs_id'] ?>"><?= $res['cs_semestre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-xl-12" id="campo_res_componente_atividade" style="display: none;">
                                <label class="form-label">Componente Curricular/Atividade <span>*</span></label>
                                <select class="form-select text-uppercase" name="res_componente_atividade"
                                    id="cad_res_componente_atividade">
                                    <option selected value="<?= $solic_row['compc_id'] ?>">
                                        <?= $solic_row['compc_componente'] ?>
                                    </option>
                                </select>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <div class="col-xl-12" id="campo_res_componente_atividade_texto" style="display: none;">
                                <label class="form-label">Componente Curricular/Atividade <span>*</span></label>
                                <input type="text" class="form-control text-uppercase"
                                    name="res_componente_atividade_nome" id="cad_res_componente_atividade_nome"
                                    value="<?= $solic_row['solic_nome_comp_ativ'] ?>" maxlength="200">
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <div class="col-xl-12" id="campo_res_nome_atividade" style="display: none;">
                                <label class="form-label">Nome da Atividade <span>*</span></label>
                                <input type="text" class="form-control text-uppercase" name="res_nome_atividade"
                                    id="cad_res_nome_atividade" value="<?= $solic_row['solic_nome_atividade'] ?>"
                                    maxlength="200">
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <div class="col-xl-12" id="campo_res_nome_curso_texto" style="display: none;">
                                <label class="form-label">Nome do curso <span>*</span></label>
                                <input type="text" class="form-control text-uppercase" name="res_curso_nome"
                                    id="cad_res_nome_curso" value="<?= $solic_row['solic_nome_curso_text'] ?>"
                                    maxlength="200">
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <div class="col-xl-12">
                                <label class="form-label">Módulo</label>
                                <input type="text" class="form-control text-uppercase" name="res_modulo"
                                    maxlength="200">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Título da Aula</label>
                                <input type="text" class="form-control text-uppercase" name="res_titulo_aula"
                                    maxlength="200">
                            </div>

                            <div class="col-xl-12">
                                <label class="form-label">Professor(es)</label>
                                <input type="text" class="form-control text-uppercase" name="res_professor"
                                    value="<?= $solic_row['solic_nome_prof_resp'] ?>" maxlength="200">
                            </div>

                            <div class="col-lg-12">
                                <div class="hstack gap-3 align-items-center justify-content-between mt-2">
                                    <button type="button" class="btn btn-light btn-label previestab waves-effect"
                                        id="btnAnterior2"><i
                                            class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>
                                        Anterior</button>
                                    <button type="button"
                                        class="btn botao_azul_escuro btn-label right ms-auto nexttab nexttab waves-effect"
                                        id="btnProximo2" data-form="form_etapa2" data-next="#modal_cad_espaco3"><i
                                            class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>
                                        Próximo</button>
                                </div>
                            </div>
                            <script>
                                const campo_res_componente_atividade = document.getElementById("campo_res_componente_atividade");
                                const campo_res_componente_atividade_select = document.getElementById("cad_res_componente_atividade");
                                const campo_res_componente_atividade_texto = document.getElementById("campo_res_componente_atividade_texto");
                                const campo_res_componente_atividade_nome = document.getElementById("cad_res_componente_atividade_nome");
                                const campo_res_nome_curso = document.getElementById("campo_res_nome_curso");
                                const campo_res_curso_extensao = document.getElementById("cad_res_curso_extensao");
                                const campo_res_nome_curso_texto = document.getElementById("campo_res_nome_curso_texto");
                                const campo_res_nome_curso_input = document.getElementById("cad_res_nome_curso");
                                const campo_res_nome_atividade = document.getElementById("campo_res_nome_atividade");
                                const campo_res_nome_atividade_input = document.getElementById("cad_res_nome_atividade");

                                function atualizarCamposCad() {
                                    const valor = cad_res_curso.value;
                                    campo_res_nome_curso.style.display = "none";
                                    campo_res_nome_curso_texto.style.display = "none";
                                    campo_res_componente_atividade.style.display = "none";
                                    campo_res_componente_atividade_texto.style.display = "none";
                                    campo_res_nome_atividade.style.display = "none";

                                    campo_res_curso_extensao.required = false;
                                    campo_res_nome_curso_input.required = false;
                                    campo_res_componente_atividade_select.required = false;
                                    campo_res_componente_atividade_nome.required = false;
                                    campo_res_nome_atividade_input.required = false;

                                    if (["2", "5", "6", "9", "13", "14", "17", "18", "21"].includes(valor)) {
                                        campo_res_componente_atividade.style.display = "block";
                                        campo_res_componente_atividade_select.required = true;

                                    } else if (["7", "10", "19", "28", "31"].includes(valor)) {
                                        campo_res_nome_atividade.style.display = "block";
                                        campo_res_nome_atividade_input.required = true;

                                    } else if (valor === "8") {
                                        campo_res_nome_atividade.style.display = "block";
                                        campo_res_nome_curso.style.display = "block";
                                        campo_res_nome_atividade_input.required = true;
                                        campo_res_curso_extensao.required = true;

                                    } else if (["11", "22"].includes(valor)) {
                                        campo_res_componente_atividade_texto.style.display = "block";
                                        campo_res_nome_curso_texto.style.display = "block";
                                        campo_res_componente_atividade_nome.required = true;
                                        campo_res_nome_curso_input.required = true;
                                    }
                                }
                                cad_res_curso.addEventListener("change", atualizarCamposCad);
                                const modalCad = document.getElementById('modal_cad_espaco');
                                modalCad.addEventListener('shown.bs.modal', atualizarCamposCad);
                            </script>
                        </div>
                    </div>
                </div>

                <div class="etapa d-none" id="etapa3">

                    <div class="modal-body">

                        <div id="custom-progress-bar" class="progress-nav mb-5 mt-2">
                            <div class="progress" style="height: 1px;">
                                <div class="progress-bar" role="progressbar" style="width: 100%;" aria-valuenow="0"
                                    aria-valuemin="0" aria-valuemax="0">
                                </div>
                            </div>

                            <ul class="nav nav-pills progress-bar-tab custom-nav" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill done" data-progressbar="custom-progress-bar"
                                        id="pills-gen-info-tab" data-bs-toggle="pill" data-bs-target="#pills-gen-info"
                                        type="button" role="tab" aria-controls="pills-gen-info" aria-selected="false"
                                        data-position="0" tabindex="-1" disabled>Local</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill done" data-progressbar="custom-progress-bar"
                                        id="pills-info-desc-tab" data-bs-toggle="pill" data-bs-target="#pills-info-desc"
                                        type="button" role="tab" aria-controls="pills-info-desc" aria-selected="false"
                                        data-position="1" tabindex="-1" disabled>Atividade</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill active" data-progressbar="custom-progress-bar"
                                        id="pills-success-tab" data-bs-toggle="pill" data-bs-target="#pills-success"
                                        type="button" role="tab" aria-controls="pills-success" aria-selected="true"
                                        data-position="2" disabled>Período</button>
                                </li>
                            </ul>
                        </div>

                        <div class="row g-3">

                            <div class="col-xl-3">
                                <?php try {
                                    $sql = $conn->prepare("SELECT * FROM conf_tipo_reserva ORDER BY ctr_tipo_reserva");
                                    $sql->execute();
                                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Erro ao tentar recuperar o perfil";
                                } ?>
                                <label class="form-label">Tipo de Reserva <span>*</span></label>
                                <select class="form-select text-uppercase" name="res_tipo_reserva" id="cad_tipo_reserva"
                                    required>
                                    <option selected disabled value=""></option>
                                    <?php foreach ($result as $res): ?>
                                        <option value="<?= $res['ctr_id'] ?>"><?= $res['ctr_tipo_reserva'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <!-- <div class="col-xl-9 row"> -->
                            <div class="col-6 col-lg-4 col-xl-3 campo-diario-cad">
                                <label class="form-label">Data da Reserva <span>*</span></label>
                                <input type="text" class="form-control flatpickr-input" name="res_data"
                                    id="cad_data_reserva" onchange="preencherCampos()">
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>
                            <div class="col-6 col-lg-4 col-xl-3 campo-diario-cad">
                                <label class="form-label">Dia da semana</label>
                                <select class="form-select text-uppercase" name="res_dia_semana"
                                    id="cad_diaSemana_reserva" disabled>
                                    <option selected disabled value=""></option>
                                    <option value="7">Domingo</option>
                                    <option value="1">Segunda-feira</option>
                                    <option value="2">Terça-feira</option>
                                    <option value="3">Quarta-feira</option>
                                    <option value="4">Quinta-feira</option>
                                    <option value="5">Sexta-feira</option>
                                    <option value="6">Sábado</option>
                                </select>
                            </div>
                            <div class="col-6 col-lg-4 col-xl-3 campo-diario-cad">
                                <label class="form-label">Mês</label>
                                <input type="text" class="form-control text-uppercase" name="res_mes"
                                    id="cad_mes_reserva" readonly>
                            </div>
                            <div class="col-6 col-lg-4 col-xl-3 campo-diario-cad">
                                <label class="form-label">Ano</label>
                                <input type="text" class="form-control text-uppercase" name="res_ano"
                                    id="cad_ano_reserva" readonly>
                            </div>

                            <div class="col-6 col-lg-4 col-xl-3 campo-fixa-cad" style="display: none;">
                                <?php try {
                                    $sql = $conn->prepare("SELECT week_id, week_dias FROM conf_dias_semana");
                                    $sql->execute();
                                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Erro ao tentar recuperar o perfil";
                                } ?>
                                <label class="form-label">Dia da Semana <span>*</span></label>
                                <select class="form-select text-uppercase" name="res_dia_semana_fixa"
                                    id="cad_res_dia_semana_fixa">
                                    <option selected disabled value=""></option>
                                    <?php foreach ($result as $res): ?>
                                        <option value="<?= $res['week_id'] ?>"><?= $res['week_dias'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>
                            <div class="col-6 col-lg-4 col-xl-3 campo-fixa-cad" style="display: none;">
                                <label class="form-label">Data Início Repetição <span>*</span></label>
                                <input type="text" class="form-control flatpickr-input" name="res_data_inicio_semanal"
                                    id="cad_data_inicio_semanal">
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>
                            <div class="col-6 col-lg-4 col-xl-3 campo-fixa-cad" style="display: none;">
                                <label class="form-label">Data Fim Repetição <span>*</span></label>
                                <input type="text" class="form-control flatpickr-input" name="res_data_fim_semanal"
                                    id="cad_data_fim_semanal">
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>
                            <!-- </div> -->
                            <div class="col-6 col-lg-4 col-xl-3">
                                <label class="form-label">Hora Início <span>*</span></label>
                                <input type="time" class="form-control hora" id="cad_res_hora_inicio"
                                    name="res_hora_inicio" required>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <div class="col-6 col-lg-4 col-xl-3">
                                <label class="form-label">Hora Fim <span>*</span></label>
                                <input type="time" class="form-control hora" id="cad_res_hora_fim" name="res_hora_fim"
                                    required>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const horaInicio = document.getElementById('cad_res_hora_inicio');
                                    const horaFim = document.getElementById('cad_res_hora_fim');
                                    function validarHoras() {
                                        const inicio = horaInicio.value;
                                        const fim = horaFim.value;
                                        if (inicio && fim) {
                                            if (inicio >= fim) {
                                                Swal.fire({
                                                    icon: 'warning',
                                                    title: 'Horário inválido',
                                                    text: 'A hora de início deve ser menor que a hora de fim.',
                                                }).then(() => {
                                                    horaInicio.value = '';
                                                    horaFim.value = '';
                                                    horaInicio.focus();
                                                });
                                            }
                                        }
                                    }
                                    horaInicio.addEventListener('change', validarHoras);
                                    horaFim.addEventListener('change', validarHoras);
                                });
                            </script>
                            <script>
                                const inputHoraInicio = document.getElementById("cad_res_hora_inicio");
                                const inputHoraFim = document.getElementById("cad_res_hora_fim");
                                const cad_tipo_reserva = document.getElementById("cad_tipo_reserva");

                                const camposDiariaCad = document.querySelectorAll('.campo-diario-cad');
                                const camposFixaCad = document.querySelectorAll('.campo-fixa-cad');
                                const cad_data_reserva = document.getElementById("cad_data_reserva");
                                const cad_res_dia_semana_fixa = document.getElementById('cad_res_dia_semana_fixa');
                                const cad_data_inicio_semanal = document.getElementById('cad_data_inicio_semanal');
                                const cad_data_fim_semanal = document.getElementById('cad_data_fim_semanal');

                                [inputHoraInicio, inputHoraFim].forEach(input => {
                                    flatpickr(input, {
                                        allowInput: true,
                                        enableTime: true,
                                        noCalendar: true,
                                        dateFormat: "H:i",
                                        minTime: "<?= $limit_hora_inicio ?>",
                                        maxTime: "<?= $limit_hora_fim ?>",
                                        time_24hr: true
                                    });
                                    input.addEventListener('input', function () {
                                        if (input.value.trim()) {
                                            input.classList.remove('is-invalid');
                                        }
                                    });
                                });

                                let fpInstanceCad;
                                let fpInicioSemanalCad;
                                let fpFimSemanalCad;

                                fetch('busca_datas_bloqueadas.php')
                                    .then(response => response.json())
                                    .then(datasBloqueadas => {
                                        fpInstanceCad = flatpickr(cad_data_reserva, {
                                            disable: datasBloqueadas,
                                            dateFormat: "Y-m-d",
                                            altInput: true,
                                            altFormat: "d/m/Y",
                                            locale: "pt",
                                            allowInput: true,
                                            onClose: function (selectedDates, dateStr, instance) {
                                                instance.altInput.classList.remove('is-invalid');
                                                preencherCampos();
                                            }
                                        });

                                        fpInicioSemanalCad = flatpickr(cad_data_inicio_semanal, {
                                            dateFormat: "Y-m-d",
                                            altInput: true,
                                            altFormat: "d/m/Y",
                                            locale: "pt"
                                        });
                                        fpFimSemanalCad = flatpickr(cad_data_fim_semanal, {
                                            dateFormat: "Y-m-d",
                                            altInput: true,
                                            altFormat: "d/m/Y",
                                            locale: "pt"
                                        });

                                        function toggleCamposDeDataCad() {
                                            if (cad_tipo_reserva.value === "2") { // Reserva Fixa (Semanal)
                                                camposDiariaCad.forEach(campo => campo.style.display = 'none');
                                                camposFixaCad.forEach(campo => campo.style.display = 'block');
                                                cad_res_dia_semana_fixa.required = true;
                                                cad_data_inicio_semanal.required = true;
                                                cad_data_fim_semanal.required = true;
                                                cad_data_reserva.required = false;

                                                cad_data_reserva.value = '';
                                                if (fpInstanceCad.altInput) fpInstanceCad.altInput.value = '';

                                            } else if (cad_tipo_reserva.value === "1") { // Reserva Esporádica (Diária)
                                                camposDiariaCad.forEach(campo => campo.style.display = 'block');
                                                camposFixaCad.forEach(campo => campo.style.display = 'none');
                                                cad_res_dia_semana_fixa.required = false;
                                                cad_data_inicio_semanal.required = false;
                                                cad_data_fim_semanal.required = false;
                                                cad_data_reserva.required = true;

                                                cad_res_dia_semana_fixa.value = '';
                                                cad_data_inicio_semanal.value = '';
                                                if (fpInicioSemanalCad.altInput) fpInicioSemanalCad.altInput.value = '';
                                                cad_data_fim_semanal.value = '';
                                                if (fpFimSemanalCad.altInput) fpFimSemanalCad.altInput.value = '';
                                            }
                                        }

                                        cad_tipo_reserva.addEventListener("change", toggleCamposDeDataCad);
                                        document.getElementById('modal_cad_espaco').addEventListener('shown.bs.modal', function () {
                                            toggleCamposDeDataCad();
                                        });

                                        document.querySelector("form").addEventListener("submit", function (event) {
                                            let formValido = true;
                                            const isTipoSemanal = cad_tipo_reserva.value === "2";

                                            if (isTipoSemanal) {
                                                if (!cad_res_dia_semana_fixa.value.trim() || !cad_data_inicio_semanal.value.trim() || !cad_data_fim_semanal.value.trim()) {
                                                    formValido = false;
                                                }
                                            } else {
                                                if (!fpInstanceCad.altInput.value.trim()) {
                                                    formValido = false;
                                                }
                                            }

                                            if (!inputHoraInicio.value.trim()) {
                                                inputHoraInicio.classList.add('is-invalid');
                                                formValido = false;
                                            }

                                            if (!inputHoraFim.value.trim()) {
                                                inputHoraFim.classList.add('is-invalid');
                                                formValido = false;
                                            }

                                            if (!formValido) {
                                                event.preventDefault();
                                                event.stopPropagation();
                                            }
                                        });
                                    });
                            </script>


                            <div class="col-6 col-lg-4 col-xl-3">
                                <label class="form-label">Turno</label>
                                <input type="text" class="form-control text-uppercase" name="res_turno" id="turno"
                                    readonly>
                            </div>

                            <script>
                                document.getElementById('cad_res_hora_inicio').addEventListener('change', function () {
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
                                <div class="hstack gap-3 align-items-center justify-content-between mt-2">
                                    <button type="button" class="btn btn-light btn-label previestab waves-effect"
                                        id="btnAnterior3"><i
                                            class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>
                                        Anterior</button>
                                    <button type="submit" class="btn botao botao_verde next-btn">Concluir</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>

        </div>
    </div>

    <script>
        const etapas = ['etapa1', 'etapa2', 'etapa3'];
        let etapaAtual = 0;

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
            document.querySelectorAll('select').forEach(select => {
                if ($(select).hasClass('select2-hidden-accessible')) {
                    $(select).on('change', function () {
                        if (select.checkValidity()) {
                            select.classList.remove('is-invalid');
                        } else {
                            select.classList.add('is-invalid');
                        }
                    });
                }
            });
            return valido;
        }

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

        document.getElementById('form_reserva').addEventListener('submit', function (event) {
            for (let i = 0; i < etapas.length; i++) {
                if (!validarCamposDaEtapa(i)) {
                    event.preventDefault();
                    event.stopPropagation();
                    mostrarEtapa(i);
                    return;
                }
            }
        });
        mostrarEtapa(0);
    </script>

    <script>
        $(document).ready(function () {
            $('#cad_res_componente_atividade').select2({
                dropdownParent: $('#modal_cad_espaco'),
                width: '100%',
                language: {
                    noResults: function () {
                        return "Dados não encontrados";
                    }
                }
            });

            $('#cad_res_curso').change(function () {
                var cursoId = $(this).val();
                var componenteSelecionado = "<?= $solic_row['compc_id'] ?? '' ?>";

                if (cursoId !== "") {
                    $.ajax({
                        url: '../buscar_componentes.php',
                        type: 'POST',
                        data: {
                            curso_id: cursoId
                        },
                        success: function (data) {
                            $('#cad_res_componente_atividade').html(data);

                            if (componenteSelecionado !== "") {
                                $('#cad_res_componente_atividade').val(componenteSelecionado).trigger('change');
                            }
                            $('#cad_res_componente_atividade').select2({
                                dropdownParent: $('#modal_cad_espaco'),
                                width: '100%',
                                language: {
                                    noResults: function () {
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
    </script>
</div>











<!-- MODAL DE EDIÇÃO -->

<div class="modal fade modal_padrao" id="modal_edit_espaco" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal_padrao_cinza">
                <h5 class="modal-title">Editar Reserva</h5>
                <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="form_reserva_edit" class="needs-validation" action="../router/web.php?r=Reserv" method="POST"
                novalidate>

                <input type="hidden" class="form-control" name="res_solic_id" value="<?= $_GET['i'] ?>" required>
                <input type="hidden" class="form-control res_id" name="res_id" required>
                <input type="hidden" class="form-control" name="acao" value="atualizar" required>

                <div class="etapa" id="EditEtapa1">
                    <div class="modal-body">

                        <div id="custom-progress-bar" class="progress-nav mb-5 mt-2">
                            <div class="progress" style="height: 1px;">
                                <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0"
                                    aria-valuemin="0" aria-valuemax="0">
                                </div>
                            </div>

                            <ul class="nav nav-pills progress-bar-tab custom-nav" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill active" data-progressbar="custom-progress-bar"
                                        id="pills-gen-info-tab" data-bs-toggle="pill" data-bs-target="#pills-gen-info"
                                        role="tab" aria-controls="pills-gen-info" aria-selected="false"
                                        data-position="0" tabindex="-1" disabled>Local</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill" data-progressbar="custom-progress-bar"
                                        id="pills-info-desc-tab" data-bs-toggle="pill" data-bs-target="#pills-info-desc"
                                        role="tab" aria-controls="pills-info-desc" aria-selected="false"
                                        data-position="1" tabindex="-1" disabled>Atividade</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill" data-progressbar="custom-progress-bar"
                                        id="pills-success-tab" data-bs-toggle="pill" data-bs-target="#pills-success"
                                        role="tab" aria-controls="pills-success" aria-selected="true" data-position="2"
                                        disabled>Período</button>
                                </li>
                            </ul>
                        </div>

                        <div class="row g-3">

                            <div class="col-xl-3">
                                <?php try {
                                    $sql = $conn->prepare("SELECT * FROM unidades");
                                    $sql->execute();
                                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Erro ao tentar recuperar o perfil";
                                } ?>
                                <label class="form-label">Campus</label>
                                <select class="form-select text-uppercase res_campus" name="res_campus"
                                    id="edit_reserva_campus">
                                    <?php foreach ($result as $res): ?>
                                        <option value="<?= $res['uni_id'] ?>"><?= $res['uni_unidade'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <div class="col-xl-9" id="camp_edit_reserv_local_cabula" style="display: none;">
                                <?php try {
                                    $sql = $conn->prepare("SELECT esp_id, esp_codigo, esp_nome_local FROM espaco WHERE esp_unidade = 1 ORDER BY esp_nome_local ASC");
                                    $sql->execute();
                                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Erro ao tentar recuperar os dados";
                                } ?>
                                <label class="form-label">Local <span>*</span></label>
                                <select class="form-select text-uppercase res_espaco_id_cabula"
                                    name="res_espaco_id_cabula" id="edit_reserva_local_cabula">
                                    <option selected disabled value=""></option>
                                    <?php foreach ($result as $res): ?>
                                        <option value="<?= $res['esp_id'] ?>">
                                            <?= $res['esp_codigo'] . ' - ' . $res['esp_nome_local'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <div class="col-xl-9" id="camp_edit_reserv_local_brotas" style="display: none;">
                                <?php try {
                                    $sql = $conn->prepare("SELECT esp_id, esp_codigo, esp_nome_local FROM espaco WHERE esp_unidade = 2 ORDER BY esp_nome_local ASC");
                                    $sql->execute();
                                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Erro ao tentar recuperar os dados";
                                } ?>
                                <label class="form-label">Local <span>*</span></label>
                                <select class="form-select text-uppercase res_espaco_id_brotas"
                                    name="res_espaco_id_brotas" id="edit_reserva_local_brotas">
                                    <option selected disabled value=""></option>
                                    <?php foreach ($result as $res): ?>
                                        <option value="<?= $res['esp_id'] ?>">
                                            <?= $res['esp_codigo'] . ' - ' . $res['esp_nome_local'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <div class="col-xl-3">
                                <?php try {
                                    $sql = $conn->prepare("SELECT * FROM andares ORDER BY and_andar");
                                    $sql->execute();
                                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Erro ao tentar recuperar o perfil";
                                } ?>
                                <label class="form-label">Tipo de Sala</label>
                                <select class="form-select text-uppercase" name="" id="edit_reserva_tipo_sala" disabled>
                                    <option selected value=""></option>
                                    <?php foreach ($result as $res): ?>
                                        <option value="<?= $res['and_id'] ?>"><?= $res['and_andar'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-xl-3">
                                <?php try {
                                    $sql = $conn->prepare("SELECT * FROM andares ORDER BY and_andar");
                                    $sql->execute();
                                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Erro ao tentar recuperar o perfil";
                                } ?>
                                <label class="form-label">Andar</label>
                                <select class="form-select text-uppercase" name="" id="edit_reserva_andar" disabled>
                                    <option selected value=""></option>
                                    <?php foreach ($result as $res): ?>
                                        <option value="<?= $res['and_id'] ?>"><?= $res['and_andar'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-xl-3">
                                <?php try {
                                    $sql = $conn->prepare("SELECT * FROM pavilhoes ORDER BY pav_pavilhao");
                                    $sql->execute();
                                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Erro ao tentar recuperar o perfil";
                                } ?>
                                <label class="form-label">Pavilhão</label>
                                <select class="form-select text-uppercase" name="" id="edit_reserva_pavilhao" disabled>
                                    <option selected value=""></option>
                                    <?php foreach ($result as $res): ?>
                                        <option value="<?= $res['pav_id'] ?>"><?= $res['pav_pavilhao'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-xl-3">
                                <label class="form-label">Capac. Máxima</label>
                                <input class="form-control" name="" id="edit_reserva_camp_maximo"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '');" disabled>
                            </div>

                            <div class="col-xl-3">
                                <label class="form-label">Capac. Média</label>
                                <input class="form-control" name="" id="edit_reserva_camp_media"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '');" disabled>
                            </div>

                            <div class="col-xl-3">
                                <label class="form-label">Capac. Mínima</label>
                                <input class="form-control" name="" id="edit_reserva_camp_minima"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '');" disabled>
                            </div>

                            <script>
                                function toggleLocalField(value) {
                                    const cabula = document.getElementById('camp_edit_reserv_local_cabula');
                                    const brotas = document.getElementById('camp_edit_reserv_local_brotas');
                                    cabula.style.display = 'none';
                                    brotas.style.display = 'none';

                                    if (value === '1') {
                                        cabula.style.display = 'block';
                                    } else if (value === '2') {
                                        brotas.style.display = 'block';
                                    }
                                }
                                function limparCamposDetalhes() {
                                    $('#edit_reserva_local_cabula').val(null).trigger('change');
                                    $('#edit_reserva_local_brotas').val(null).trigger('change');
                                    document.getElementById("edit_reserva_tipo_sala").value = '';
                                    document.getElementById("edit_reserva_andar").value = '';
                                    document.getElementById("edit_reserva_pavilhao").value = '';
                                    document.getElementById("edit_reserva_camp_maximo").value = '';
                                    document.getElementById("edit_reserva_camp_media").value = '';
                                    document.getElementById("edit_reserva_camp_minima").value = '';
                                }
                                document.getElementById('edit_reserva_campus').addEventListener('change', function () {
                                    toggleLocalField(this.value);
                                    limparCamposDetalhes();
                                });
                                function inicializarModalEdicao() {
                                    const valorSelecionado = document.getElementById('edit_reserva_campus').value;
                                    toggleLocalField(valorSelecionado);
                                }
                                const modal = document.getElementById('modal_edit_espaco');
                                if (modal) {
                                    modal.addEventListener('shown.bs.modal', function () {
                                        inicializarModalEdicao();
                                    });
                                }
                            </script>

                            <div class="col-xl-3">
                                <label class="form-label">Nº Pessoas <span>*</span></label>
                                <input class="form-control text-uppercase res_quant_pessoas"
                                    id="edit_reserva_quant_pessoas" name="res_quant_pessoas">
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <div class="col-xl-3">
                                <label class="form-label">Recursos Audiovisuais <span>*</span></label>
                                <select class="form-select text-uppercase res_recursos" name="res_recursos"
                                    id="edit_res_recursos" required>
                                    <option value="NÃO">NÃO</option>
                                    <option value="SIM">SIM</option>
                                </select>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <div class="col-xl-12" id="campo_edit_res_recursos_add" style="display: none;">
                                <?php try {
                                    $sql = $conn->prepare("SELECT rec_id, rec_recurso FROM recursos ORDER BY rec_recurso");
                                    $sql->execute();
                                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Erro ao tentar recuperar o perfil";
                                } ?>
                                <label class="form-label">Recursos Audiovisuais Adicionais <span>*</span></label>
                                <select class="form-select text-uppercase res_recursos_add" name="res_recursos_add[]"
                                    multiple id="edit_res_recursos_add">
                                    <?php foreach ($result as $res): ?>
                                        <option value="<?= $res['rec_id'] ?>"><?= $res['rec_recurso'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <script>
                                $(document).ready(function () {
                                    const edit_res_recursos = document.getElementById("edit_res_recursos");
                                    const campo_edit_res_recursos_add = document.getElementById("campo_edit_res_recursos_add");
                                    const select_add = document.getElementById("edit_res_recursos_add");

                                    function toggleRecursosAdicionais() {
                                        if (edit_res_recursos.value === "SIM") {
                                            campo_edit_res_recursos_add.style.display = "block";
                                            select_add.required = true;

                                            $('#edit_res_recursos_add').select2({
                                                placeholder: "Selecione as opções",
                                                tags: false,
                                                allowClear: true,
                                                dropdownParent: $('#modal_edit_espaco'),
                                                width: '100%'
                                            });
                                        } else {
                                            campo_edit_res_recursos_add.style.display = "none";
                                            select_add.required = false;
                                        }
                                    }
                                    edit_res_recursos.addEventListener("change", toggleRecursosAdicionais);
                                    $('#modal_edit_espaco').on('shown.bs.modal', function () {
                                        toggleRecursosAdicionais();
                                    });
                                });
                            </script>

                            <div class="col-12">
                                <label class="form-label">Observações</label>
                                <textarea class="form-control res_obs" id="EditmeuTextarea" name="res_obs" rows="3"
                                    cols="50" maxlength="200"></textarea>
                                <p class="label_info text-end mt-1">Caracteres restantes: <span
                                        id="EditContador">200</span></p>
                                <script>
                                    const EditmeuTextarea = document.getElementById('EditmeuTextarea');
                                    const EditContador = document.getElementById('EditContador');
                                    const Editlimite = 200;
                                    EditmeuTextarea.addEventListener('input', function () {
                                        const total = EditmeuTextarea.value.length;
                                        EditContador.textContent = `${total} / ${Editlimite}`;
                                    });
                                </script>
                            </div>

                            <div class="col-lg-12">
                                <div class="hstack gap-3 align-items-center justify-content-end mt-3">
                                    <p class="label_asterisco m-0"><span>*</span> Campo obrigatório</p>
                                    <button type="button"
                                        class="btn botao_azul_escuro btn-label right ms-auto nexttab nexttab waves-effect"
                                        id="btnEditProximo1" data-form="form_etapa1" data-next="#modal_cad_espaco2"><i
                                            class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>
                                        Próximo</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="etapa d-none" id="EditEtapa2">
                    <div class="modal-body">

                        <div id="custom-progress-bar" class="progress-nav mb-5 mt-2">
                            <div class="progress" style="height: 1px;">
                                <div class="progress-bar" role="progressbar" style="width: 50%;" aria-valuenow="0"
                                    aria-valuemin="0" aria-valuemax="0">
                                </div>
                            </div>

                            <ul class="nav nav-pills progress-bar-tab custom-nav" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill done" data-progressbar="custom-progress-bar"
                                        id="pills-gen-info-tab" data-bs-toggle="pill" data-bs-target="#pills-gen-info"
                                        type="button" role="tab" aria-controls="pills-gen-info" aria-selected="false"
                                        data-position="0" tabindex="-1" disabled>Local</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill active" data-progressbar="custom-progress-bar"
                                        id="pills-info-desc-tab" data-bs-toggle="pill" data-bs-target="#pills-info-desc"
                                        type="button" role="tab" aria-controls="pills-info-desc" aria-selected="false"
                                        data-position="1" tabindex="-1" disabled>Atividade</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill" data-progressbar="custom-progress-bar"
                                        id="pills-success-tab" data-bs-toggle="pill" data-bs-target="#pills-success"
                                        type="button" role="tab" aria-controls="pills-success" aria-selected="true"
                                        data-position="2" disabled>Período</button>
                                </li>
                            </ul>
                        </div>

                        <div class="row g-3">

                            <div class="col-sm">
                                <?php try {
                                    $sql = $conn->prepare("SELECT * FROM conf_tipo_aula ORDER BY cta_tipo_aula");
                                    $sql->execute();
                                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Erro ao tentar recuperar o perfil";
                                } ?>
                                <label class="form-label">Tipo de Aula <span>*</span></label>
                                <select class="form-select text-uppercase res_tipo_aula" name="res_tipo_aula">
                                    <option selected disabled value=""></option>
                                    <?php foreach ($result as $res): ?>
                                        <option value="<?= $res['cta_id'] ?>"><?= $res['cta_tipo_aula'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <div class="col-sm">
                                <?php try {
                                    $sql = $conn->prepare("SELECT curs_id, curs_curso FROM cursos WHERE curs_status != 0 ORDER BY curs_curso");
                                    $sql->execute();
                                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Erro ao tentar recuperar os dados";
                                } ?>
                                <label class="form-label">Curso <span>*</span></label>
                                <select class="form-select text-uppercase res_curso" name="res_curso"
                                    id="edit_res_curso">
                                    <option selected disabled value=""></option>
                                    <?php foreach ($result as $res): ?>
                                        <option value="<?= $res['curs_id'] ?>"><?= $res['curs_curso'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <div class="col-sm" id="campo_edit_res_nome_curso" style="display: none;">
                                <?php try {
                                    $sql = $conn->prepare("SELECT cexc_id, cexc_curso FROM conf_cursos_extensao_curricularizada ORDER BY cexc_curso");
                                    $sql->execute();
                                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Erro ao tentar recuperar os dados";
                                } ?>
                                <label class="form-label">Nome do Curso <span>*</span></label>
                                <select class="form-select text-uppercase res_curso_extensao" name="res_curso_extensao"
                                    id="edit_res_curso_extensao">
                                    <option selected disabled value=""></option>
                                    <?php foreach ($result as $res): ?>
                                        <option value="<?= $res['cexc_id'] ?>"><?= $res['cexc_curso'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <div class="col-sm">
                                <?php try {
                                    $sql = $conn->prepare("SELECT cs_id, cs_semestre FROM conf_semestre ORDER BY cs_id");
                                    $sql->execute();
                                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Erro ao tentar recuperar os dados";
                                } ?>
                                <label class="form-label">Semestre</label>
                                <select class="form-select text-uppercase res_semestre" name="res_semestre">
                                    <option selected value="<?= $solic_row['cs_id'] ?>"><?= $solic_row['cs_semestre'] ?>
                                    </option>
                                    <?php foreach ($result as $res): ?>
                                        <option value="<?= $res['cs_id'] ?>"><?= $res['cs_semestre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-xl-12" id="campo_edit_res_componente_atividade" style="display: none;">
                                <label class="form-label">Componente Curricular/Atividade <span>*</span></label>
                                <select class="form-select text-uppercase res_componente_atividade"
                                    name="res_componente_atividade" id="edit_res_componente_atividade">
                                    <option selected disabled value=""></option>
                                </select>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <div class="col-xl-12" id="campo_edit_res_componente_atividade_texto"
                                style="display: none;">
                                <label class="form-label">Componente Curricular/Atividade <span>*</span></label>
                                <input type="text" class="form-control text-uppercase res_componente_atividade_nome"
                                    name="res_componente_atividade_nome" maxlength="200"
                                    id="edit_res_componente_atividade_nome">
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <div class="col-xl-12" id="campo_edit_res_nome_atividade" style="display: none;">
                                <label class="form-label">Nome da Atividade <span>*</span></label>
                                <input type="text" class="form-control text-uppercase res_nome_atividade"
                                    name="res_nome_atividade" maxlength="200" id="edit_res_nome_atividade">
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <div class="col-xl-12" id="campo_edit_res_nome_curso_texto" style="display: none;">
                                <label class="form-label">Nome do curso <span>*</span></label>
                                <input type="text" class="form-control text-uppercase res_curso_nome"
                                    name="res_curso_nome" id="edit_res_nome_curso" maxlength="200">
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <div class="col-xl-12">
                                <label class="form-label">Módulo</label>
                                <input type="text" class="form-control text-uppercase res_modulo" name="res_modulo"
                                    maxlength="200">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Título da Aula</label>
                                <input type="text" class="form-control text-uppercase res_titulo_aula"
                                    name="res_titulo_aula" maxlength="200">
                            </div>

                            <div class="col-xl-12">
                                <label class="form-label">Professor(es)</label>
                                <input type="text" class="form-control text-uppercase res_professor"
                                    name="res_professor" maxlength="200">
                            </div>

                            <script>
                                const edit_res_curso = document.getElementById("edit_res_curso");
                                const campo_edit_res_componente_atividade = document.getElementById("campo_edit_res_componente_atividade");
                                const select_edit_res_componente_atividade = document.getElementById("edit_res_componente_atividade");
                                const campo_edit_res_componente_atividade_texto = document.getElementById("campo_edit_res_componente_atividade_texto");
                                const input_edit_res_componente_atividade_nome = document.getElementById("edit_res_componente_atividade_nome");
                                const campo_edit_res_nome_curso = document.getElementById("campo_edit_res_nome_curso");
                                const select_edit_res_curso_extensao = document.getElementById("edit_res_curso_extensao");
                                const campo_edit_res_nome_curso_texto = document.getElementById("campo_edit_res_nome_curso_texto");
                                const input_edit_res_nome_curso = document.getElementById("edit_res_nome_curso");
                                const campo_edit_res_nome_atividade = document.getElementById("campo_edit_res_nome_atividade");
                                const input_edit_res_nome_atividade = document.getElementById("edit_res_nome_atividade");
                                function atualizarCamposEdicao() {
                                    const valor = edit_res_curso.value;
                                    campo_edit_res_nome_curso.style.display = "none";
                                    campo_edit_res_nome_curso_texto.style.display = "none";
                                    campo_edit_res_componente_atividade.style.display = "none";
                                    campo_edit_res_componente_atividade_texto.style.display = "none";
                                    campo_edit_res_nome_atividade.style.display = "none";
                                    if (select_edit_res_componente_atividade) select_edit_res_componente_atividade.required = false;
                                    if (input_edit_res_componente_atividade_nome) input_edit_res_componente_atividade_nome.required = false;
                                    if (select_edit_res_curso_extensao) select_edit_res_curso_extensao.required = false;
                                    if (input_edit_res_nome_curso) input_edit_res_nome_curso.required = false;
                                    if (input_edit_res_nome_atividade) input_edit_res_nome_atividade.required = false;

                                    if (["2", "5", "6", "9", "13", "14", "17", "18", "21"].includes(valor)) {
                                        campo_edit_res_componente_atividade.style.display = "block";
                                        select_edit_res_componente_atividade.required = true;

                                    } else if (["7", "10", "19", "28", "31"].includes(valor)) {
                                        campo_edit_res_nome_atividade.style.display = "block";
                                        input_edit_res_nome_atividade.required = true;

                                    } else if (valor === "8") {
                                        campo_edit_res_nome_atividade.style.display = "block";
                                        campo_edit_res_nome_curso.style.display = "block";
                                        input_edit_res_nome_atividade.required = true;
                                        select_edit_res_curso_extensao.required = true;

                                    } else if (["11", "22"].includes(valor)) {
                                        campo_edit_res_componente_atividade_texto.style.display = "block";
                                        campo_edit_res_nome_curso_texto.style.display = "block";
                                        input_edit_res_componente_atividade_nome.required = true;
                                        input_edit_res_nome_curso.required = true;
                                    }
                                }
                                edit_res_curso.addEventListener("change", atualizarCamposEdicao);
                                const modalEdit = document.getElementById('modal_edit_espaco');
                                modalEdit.addEventListener('shown.bs.modal', atualizarCamposEdicao);
                            </script>

                            <div class="col-lg-12">
                                <div class="hstack gap-3 align-items-center justify-content-between mt-2">
                                    <button type="button" class="btn btn-light btn-label previestab waves-effect"
                                        id="btnEditAnterior2">
                                        <i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Anterior
                                    </button>
                                    <button type="button"
                                        class="btn botao_azul_escuro btn-label right ms-auto nexttab nexttab waves-effect"
                                        id="btnEditProximo2">
                                        <i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>
                                        Próximo</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="etapa d-none" id="EditEtapa3">
                    <div class="modal-body">

                        <div id="custom-progress-bar" class="progress-nav mb-5 mt-2">
                            <div class="progress" style="height: 1px;">
                                <div class="progress-bar" role="progressbar" style="width: 100%;" aria-valuenow="0"
                                    aria-valuemin="0" aria-valuemax="0">
                                </div>
                            </div>

                            <ul class="nav nav-pills progress-bar-tab custom-nav" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill done" data-progressbar="custom-progress-bar"
                                        id="pills-gen-info-tab" data-bs-toggle="pill" data-bs-target="#pills-gen-info"
                                        type="button" role="tab" aria-controls="pills-gen-info" aria-selected="false"
                                        data-position="0" tabindex="-1" disabled>Local</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill done" data-progressbar="custom-progress-bar"
                                        id="pills-info-desc-tab" data-bs-toggle="pill" data-bs-target="#pills-info-desc"
                                        type="button" role="tab" aria-controls="pills-info-desc" aria-selected="false"
                                        data-position="1" tabindex="-1" disabled>Atividade</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill active" data-progressbar="custom-progress-bar"
                                        id="pills-success-tab" data-bs-toggle="pill" data-bs-target="#pills-success"
                                        type="button" role="tab" aria-controls="pills-success" aria-selected="true"
                                        data-position="2" disabled>Período</button>
                                </li>
                            </ul>
                        </div>

                        <div class="row g-3">

                            <div class="col-xl-3">
                                <?php try {
                                    $sql = $conn->prepare("SELECT * FROM conf_tipo_reserva ORDER BY ctr_tipo_reserva");
                                    $sql->execute();
                                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Erro ao tentar recuperar o perfil";
                                } ?>
                                <label class="form-label">Tipo de Reserva <span>*</span></label>
                                <select class="form-select text-uppercase res_tipo_reserva" name="res_tipo_reserva"
                                    id="edit_tipo_reserva" required>
                                    <option selected disabled value=""></option>
                                    <?php foreach ($result as $res): ?>
                                        <option value="<?= $res['ctr_id'] ?>"><?= $res['ctr_tipo_reserva'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <!-- <div class="col-xl-9 row g-3"> -->
                            <div class="col-6 col-lg-4 col-xl-3 campo-diario-edit">
                                <label class="form-label">Data da Reserva <span>*</span></label>
                                <input type="text" class="form-control flatpickr-input res_data" name="res_data"
                                    id="edit_data_reserva" onchange="preencherCamposEdit()">
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>
                            <div class="col-6 col-lg-4 col-xl-3 campo-diario-edit">
                                <?php try {
                                    $sql = $conn->prepare("SELECT week_id, week_dias FROM conf_dias_semana");
                                    $sql->execute();
                                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Erro ao tentar recuperar o perfil";
                                } ?>
                                <div>
                                    <label class="form-label">Dia da semana</label>
                                    <!-- <input type="text" class="form-control text-uppercase" id="edit_diaSemanaId_reserva"
                                        disabled> -->
                                    <select class="form-select text-uppercase res_dia_semana"
                                        id="edit_diaSemana_reserva" disabled>
                                        <?php foreach ($result as $res): ?>
                                            <option value="<?= $res['week_id'] ?>">
                                                <?= htmlspecialchars($res['week_dias']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 col-lg-4 col-xl-3 campo-diario-edit">
                                <label class="form-label">Mês</label>
                                <input type="text" class="form-control text-uppercase res_mes" name="res_mes"
                                    id="edit_mes_reserva" readonly>
                            </div>
                            <div class="col-6 col-lg-4 col-xl-3 campo-diario-edit">
                                <label class="form-label">Ano</label>
                                <input type="text" class="form-control text-uppercase res_ano" name="res_ano"
                                    id="edit_ano_reserva" readonly>
                            </div>

                            <div class="col-6 col-lg-4 col-xl-3 campo-fixa-edit" style="display: none;">
                                <?php try {
                                    $sql = $conn->prepare("SELECT week_id, week_dias FROM conf_dias_semana");
                                    $sql->execute();
                                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Erro ao tentar recuperar o perfil";
                                } ?>
                                <label class="form-label">Dia da Semana <span>*</span></label>
                                <select class="form-select text-uppercase res_dia_semana_fixa"
                                    name="res_dia_semana_fixa" id="edit_res_dia_semana_fixa">
                                    <option disabled value=""></option>
                                    <?php foreach ($result as $res): ?>
                                        <option value="<?= $res['week_id'] ?>"><?= $res['week_dias'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>
                            <div class="col-6 col-lg-4 col-xl-3 campo-fixa-edit" style="display: none;">
                                <label class="form-label">Data Início Repetição <span>*</span></label>
                                <input type="text" class="form-control flatpickr-input" name="res_data_inicio_semanal"
                                    id="edit_data_inicio_semanal">
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>
                            <div class="col-6 col-lg-4 col-xl-3 campo-fixa-edit" style="display: none;">
                                <label class="form-label">Data Fim Repetição <span>*</span></label>
                                <input type="text" class="form-control flatpickr-input" name="res_data_fim_semanal"
                                    id="edit_data_fim_semanal">
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>
                            <!-- </div> -->
                            <div class="col-6 col-lg-4 col-xl-3">
                                <label class="form-label">Hora Início <span>*</span></label>
                                <input type="time" class="form-control hora res_hora_inicio" id="edit_res_hora_inicio"
                                    name="res_hora_inicio" required>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <div class="col-6 col-lg-4 col-xl-3">
                                <label class="form-label">Hora Fim <span>*</span></label>
                                <input type="time" class="form-control hora res_hora_fim" id="edit_res_hora_fim"
                                    name="res_hora_fim" required>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>

                            <div class="col-6 col-lg-4 col-xl-3">
                                <label class="form-label">Turno</label>
                                <input type="text" class="form-control text-uppercase res_turno" name="res_turno"
                                    id="edit_turno" readonly>
                            </div>

                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const horaInicio = document.getElementById('edit_res_hora_inicio');
                                    const horaFim = document.getElementById('edit_res_hora_fim');
                                    function validarEditHoras() {
                                        const inicio = horaInicio.value;
                                        const fim = horaFim.value;
                                        if (inicio && fim) {
                                            if (inicio >= fim) {
                                                Swal.fire({
                                                    icon: 'warning',
                                                    title: 'Horário inválido',
                                                    text: 'A hora de início deve ser menor que a hora de fim.',
                                                }).then(() => {
                                                    horaInicio.value = '';
                                                    horaFim.value = '';
                                                    horaInicio.focus();
                                                });
                                            }
                                        }
                                    }
                                    horaInicio.addEventListener('change', validarEditHoras);
                                    horaFim.addEventListener('change', validarEditHoras);
                                });
                            </script>

                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const form = document.getElementById("form_reserva_edit");
                                    const inputEditHoraInicio = document.getElementById("edit_res_hora_inicio");
                                    const inputEditHoraFim = document.getElementById("edit_res_hora_fim");
                                    const edit_tipo_reserva = document.getElementById("edit_tipo_reserva");

                                    const camposDiariaEdit = document.querySelectorAll('.campo-diario-edit');
                                    const camposFixaEdit = document.querySelectorAll('.campo-fixa-edit');
                                    const edit_data_reserva = document.getElementById("edit_data_reserva");
                                    const edit_res_dia_semana_fixa = document.getElementById('edit_res_dia_semana_fixa');
                                    const edit_data_inicio_semanal = document.getElementById('edit_data_inicio_semanal');
                                    const edit_data_fim_semanal = document.getElementById('edit_data_fim_semanal');

                                    [inputEditHoraInicio, inputEditHoraFim].forEach(input => {
                                        flatpickr(input, {
                                            allowInput: true,
                                            enableTime: true,
                                            noCalendar: true,
                                            dateFormat: "H:i",
                                            minTime: "<?= $limit_hora_inicio ?>",
                                            maxTime: "<?= $limit_hora_fim ?>",
                                            time_24hr: true,
                                            onChange: function (selectedDates, dateStr) {
                                                if (dateStr) {
                                                    input.classList.remove('is-invalid');
                                                }
                                            }
                                        });
                                        input.addEventListener('input', function () {
                                            if (input.value.trim()) {
                                                input.classList.remove('is-invalid');
                                            }
                                        });
                                    });

                                    fetch('busca_datas_bloqueadas.php')
                                        .then(response => response.json())
                                        .then(datasBloqueadas => {
                                            const fpInstanceEdit = flatpickr(edit_data_reserva, {
                                                disable: datasBloqueadas,
                                                dateFormat: "Y-m-d",
                                                altInput: true,
                                                altFormat: "d/m/Y",
                                                locale: "pt",
                                                allowInput: true,
                                                onClose: function () {
                                                    edit_data_reserva.classList.toggle('is-invalid', !edit_data_reserva.value.trim());
                                                }
                                            });
                                            const fpInicioSemanalEdit = flatpickr(edit_data_inicio_semanal, {
                                                dateFormat: "Y-m-d",
                                                altInput: true,
                                                altFormat: "d/m/Y",
                                                locale: "pt"
                                            });
                                            const fpFimSemanalEdit = flatpickr(edit_data_fim_semanal, {
                                                dateFormat: "Y-m-d",
                                                altInput: true,
                                                altFormat: "d/m/Y",
                                                locale: "pt"
                                            });
                                            edit_data_reserva.addEventListener('input', function () {
                                                const valor = this.value.trim();
                                                const valido = !!Date.parse(valor);
                                                this.classList.toggle('is-invalid', !valido);
                                                if (fpInstanceEdit) {
                                                    fpInstanceEdit.setDate(valor, true);
                                                }
                                            });
                                            function toggleCamposDeDataEdit() {
                                                if (edit_tipo_reserva.value === "2") { // Reserva Fixa (Semanal)
                                                    camposFixaEdit.forEach(campo => campo.style.display = 'block');
                                                    camposDiariaEdit.forEach(campo => campo.style.display = 'none');
                                                    edit_res_dia_semana_fixa.required = true;
                                                    edit_data_inicio_semanal.required = true;
                                                    edit_data_fim_semanal.required = true;
                                                    edit_data_reserva.required = false;

                                                } else if (edit_tipo_reserva.value === "1") { // Reserva Esporádica (Diária)
                                                    camposFixaEdit.forEach(campo => campo.style.display = 'none');
                                                    camposDiariaEdit.forEach(campo => campo.style.display = 'block');
                                                    edit_res_dia_semana_fixa.required = false;
                                                    edit_data_inicio_semanal.required = false;
                                                    edit_data_fim_semanal.required = false;
                                                    edit_data_reserva.required = true;
                                                }
                                            }
                                            edit_tipo_reserva.addEventListener("change", toggleCamposDeDataEdit);
                                            document.getElementById('modal_edit_espaco').addEventListener('shown.bs.modal', toggleCamposDeDataEdit);
                                            form.addEventListener("submit", function (event) {
                                                let formValido = true;
                                                const isTipoSemanal = edit_tipo_reserva.value === "2";

                                                if (isTipoSemanal) {
                                                    if (!edit_res_dia_semana_fixa.value.trim() || !edit_data_inicio_semanal.value.trim() || !edit_data_fim_semanal.value.trim()) {
                                                        formValido = false;
                                                    }
                                                } else {
                                                    if (!fpInstanceEdit.altInput.value.trim()) {
                                                        formValido = false;
                                                    }
                                                }
                                                if (!inputEditHoraInicio.value.trim()) {
                                                    inputEditHoraInicio.classList.add('is-invalid');
                                                    formValido = false;
                                                }
                                                if (!inputEditHoraFim.value.trim()) {
                                                    inputEditHoraFim.classList.add('is-invalid');
                                                    formValido = false;
                                                }
                                                if (!formValido) {
                                                    event.preventDefault();
                                                    event.stopPropagation();
                                                    form.classList.add('was-validated');
                                                    const firstInvalid = form.querySelector('.is-invalid');
                                                    if (firstInvalid) {
                                                        firstInvalid.scrollIntoView({
                                                            behavior: 'smooth',
                                                            block: 'center'
                                                        });
                                                    }
                                                }
                                            });
                                        })
                                        .catch(error => {
                                            console.error('Erro ao carregar datas bloqueadas:', error);
                                        });
                                });
                            </script>

                            <script>
                                document.getElementById('edit_res_hora_inicio').addEventListener('change', function () {
                                    const EdithoraInicio = this.value;
                                    if (!EdithoraInicio) return;
                                    const [hora, minuto] = EdithoraInicio.split(':').map(Number);
                                    let turno = '';
                                    if (hora >= 6 && hora < 12) {
                                        turno = 'MANHÃ';
                                    } else if (hora >= 12 && hora < 18) {
                                        turno = 'TARDE';
                                    } else {
                                        turno = 'NOITE';
                                    }
                                    document.getElementById('edit_turno').value = turno;
                                });
                            </script>
                            <div class="col-lg-12">
                                <div class="hstack gap-3 align-items-center justify-content-between mt-2">
                                    <button type="button" class="btn btn-light btn-label previestab waves-effect"
                                        id="btnEditAnterior3">
                                        <i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Anterior
                                    </button>
                                    <button type="submit" class="btn botao botao_verde"
                                        id="btnEditConcluir">Concluir</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>




<script>
    document.addEventListener('DOMContentLoaded', () => {
        const etapasEdit = ['EditEtapa1', 'EditEtapa2', 'EditEtapa3'];
        let etapaEditAtual = 0;
        function mostrarEtapaEdit(index) {
            etapasEdit.forEach((id, i) => {
                document.getElementById(id).classList.toggle('d-none', i !== index);
            });
            etapaEditAtual = index;
            document.getElementById('btnEditProximo1').style.display = (index === 0) ? 'inline-block' : 'none';
            document.getElementById('btnEditAnterior2').style.display = (index === 1) ? 'inline-block' : 'none';
            document.getElementById('btnEditProximo2').style.display = (index === 1) ? 'inline-block' : 'none';
            document.getElementById('btnEditAnterior3').style.display = (index === 2) ? 'inline-block' : 'none';
            document.getElementById('btnEditConcluir').style.display = (index === 2) ? 'inline-block' : 'none';
        }
        function validarCamposDaEtapaEdit(index) {
            const etapa = document.getElementById(etapasEdit[index]);
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
        document.querySelectorAll('#form_reserva_edit input, #form_reserva_edit textarea, #form_reserva_edit select').forEach(el => {
            el.addEventListener('input', () => {
                if (el.checkValidity()) {
                    el.classList.remove('is-invalid');
                }
            });
        });
        document.getElementById('btnEditProximo1').addEventListener('click', () => {
            if (validarCamposDaEtapaEdit(0)) mostrarEtapaEdit(1);
        });
        document.getElementById('btnEditAnterior2').addEventListener('click', () => mostrarEtapaEdit(0));
        document.getElementById('btnEditProximo2').addEventListener('click', () => {
            if (validarCamposDaEtapaEdit(1)) mostrarEtapaEdit(2);
        });
        document.getElementById('btnEditAnterior3').addEventListener('click', () => mostrarEtapaEdit(1));
        document.getElementById('form_reserva_edit').addEventListener('submit', function (event) {
            for (let i = 0; i < etapasEdit.length; i++) {
                if (!validarCamposDaEtapaEdit(i)) {
                    event.preventDefault();
                    event.stopPropagation();
                    mostrarEtapaEdit(i);
                    return;
                }
            }
        });
        document.getElementById('modal_edit_espaco').addEventListener('shown.bs.modal', function () {
            mostrarEtapaEdit(0);
            const modalCadastro = bootstrap.Modal.getInstance(document.getElementById('modal_cad_espaco'));
            if (modalCadastro) modalCadastro.hide();
        });
    });
</script>

<script>
    function preencherCamposEdit() {
        const dataEditInputEdit = document.getElementById('edit_data_reserva').value;
        if (dataEditInputEdit) {
            const partes = dataEditInputEdit.split('-');
            const ano = parseInt(partes[0], 10);
            const mes = parseInt(partes[1], 10) - 1;
            const dia = parseInt(partes[2], 10);
            const data = new Date(Date.UTC(ano, mes, dia));
            const meses = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
            const diasSemana = ["7", "1", "2", "3", "4", "5", "6"];
            document.getElementById('edit_mes_reserva').value = meses[data.getUTCMonth()];
            document.getElementById('edit_ano_reserva').value = data.getUTCFullYear();
            document.getElementById('edit_diaSemana_reserva').value = diasSemana[data.getUTCDay()];
            document.getElementById('edit_diaSemanaId_reserva').value = diasSemana[data.getUTCDay()];
        }
    }
</script>

<!-- <script>
    const modal_edit_espaco = document.getElementById('modal_edit_espaco')
    if (modal_edit_espaco) {
        modal_edit_espaco.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget
            const res_curso = button.getAttribute('data-bs-res_curso');
            const res_componente_atividade = button.getAttribute('data-bs-res_componente_atividade');
            const res_tipo_reserva = button.getAttribute('data-bs-res_tipo_reserva');
            const res_data = button.getAttribute('data-bs-res_data');
            const res_data_inicio_semanal = button.getAttribute('data-bs-res_data_inicio_semanal');
            const res_data_fim_semanal = button.getAttribute('data-bs-res_data_fim_semanal');
            const res_dia_semana = button.getAttribute('data-bs-res_dia_semana');

            if (["2", "5", "6", "9", "13", "14", "17", "18", "21"].includes(res_curso)) {
                $.ajax({
                    url: '../buscar_componentes.php',
                    type: 'POST',
                    data: {
                        curso_id: res_curso
                    },
                    success: function (data) {
                        $('#edit_res_componente_atividade')
                            .html(data)
                            .val(res_componente_atividade)
                            .trigger('change');
                        $('#edit_res_componente_atividade').select2({
                            dropdownParent: $('#modal_edit_espaco'),
                            width: '100%',
                            language: {
                                noResults: function () {
                                    return "Dados não encontrados";
                                }
                            }
                        });
                    }
                });
            } else {
                $('#edit_res_componente_atividade').html('<option value="">Selecione um componente</option>').trigger('change');
            }

            const res_id = button.getAttribute('data-bs-res_id')
            const res_campus = button.getAttribute('data-bs-res_campus')
            const res_espaco_id_cabula = button.getAttribute('data-bs-res_espaco_id_cabula')
            const res_espaco_id_brotas = button.getAttribute('data-bs-res_espaco_id_brotas')
            const res_quant_pessoas = button.getAttribute('data-bs-res_quant_pessoas')
            const res_recursos = button.getAttribute('data-bs-res_recursos')
            const res_recursos_add = button.getAttribute('data-bs-res_recursos_add')
            const res_obs = button.getAttribute('data-bs-res_obs')
            const res_tipo_aula = button.getAttribute('data-bs-res_tipo_aula')
            const res_curso_nome = button.getAttribute('data-bs-res_curso_nome')
            const res_curso_extensao = button.getAttribute('data-bs-res_curso_extensao')
            const res_semestre = button.getAttribute('data-bs-res_semestre')
            const res_componente_atividade_nome = button.getAttribute('data-bs-res_componente_atividade_nome')
            const res_nome_atividade = button.getAttribute('data-bs-res_nome_atividade')
            const res_modulo = button.getAttribute('data-bs-res_modulo')
            const res_professor = button.getAttribute('data-bs-res_professor')
            const res_titulo_aula = button.getAttribute('data-bs-res_titulo_aula')
            const res_mes = button.getAttribute('data-bs-res_mes')
            const res_ano = button.getAttribute('data-bs-res_ano')
            const res_hora_inicio = button.getAttribute('data-bs-res_hora_inicio')
            const res_hora_fim = button.getAttribute('data-bs-res_hora_fim')
            const res_turno = button.getAttribute('data-bs-res_turno')
            const modalTitle = modal_edit_espaco.querySelector('.modal-title')
            const modal_res_id = modal_edit_espaco.querySelector('.res_id')
            const modal_res_campus = modal_edit_espaco.querySelector('.res_campus')
            const modal_res_espaco_id_cabula = modal_edit_espaco.querySelector('.res_espaco_id_cabula')
            const modal_res_espaco_id_brotas = modal_edit_espaco.querySelector('.res_espaco_id_brotas')
            const modal_res_quant_pessoas = modal_edit_espaco.querySelector('.res_quant_pessoas')
            const modal_res_recursos = modal_edit_espaco.querySelector('.res_recursos')
            const modal_res_recursos_add = modal_edit_espaco.querySelector('.res_recursos_add')
            const modal_res_obs = modal_edit_espaco.querySelector('.res_obs')
            const modal_res_tipo_aula = modal_edit_espaco.querySelector('.res_tipo_aula')
            const modal_res_curso = modal_edit_espaco.querySelector('.res_curso')
            const modal_res_curso_nome = modal_edit_espaco.querySelector('.res_curso_nome')
            const modal_res_curso_extensao = modal_edit_espaco.querySelector('.res_curso_extensao')
            const modal_res_semestre = modal_edit_espaco.querySelector('.res_semestre')
            const modal_res_componente_atividade = modal_edit_espaco.querySelector('.res_componente_atividade')
            const modal_res_componente_atividade_nome = modal_edit_espaco.querySelector('.res_componente_atividade_nome')
            const modal_res_nome_atividade = modal_edit_espaco.querySelector('.res_nome_atividade')
            const modal_res_modulo = modal_edit_espaco.querySelector('.res_modulo')
            const modal_res_professor = modal_edit_espaco.querySelector('.res_professor')
            const modal_res_titulo_aula = modal_edit_espaco.querySelector('.res_titulo_aula')
            const modal_res_tipo_reserva = modal_edit_espaco.querySelector('.res_tipo_reserva')
            const modal_res_data = modal_edit_espaco.querySelector('.res_data')
            const modal_res_dia_semana = modal_edit_espaco.querySelector('.res_dia_semana')
            const modal_res_mes = modal_edit_espaco.querySelector('.res_mes')
            const modal_res_ano = modal_edit_espaco.querySelector('.res_ano')
            const modal_res_hora_inicio = modal_edit_espaco.querySelector('.res_hora_inicio')
            const modal_res_hora_fim = modal_edit_espaco.querySelector('.res_hora_fim')
            const modal_res_turno = modal_edit_espaco.querySelector('.res_turno')
            const modal_data_inicio_semanal = document.getElementById('edit_data_inicio_semanal');
            const modal_data_fim_semanal = document.getElementById('edit_data_fim_semanal');

            modalTitle.textContent = 'Atualizar Dados'
            modal_res_id.value = res_id
            modal_res_campus.value = res_campus
            $('#edit_reserva_local_cabula').val(res_espaco_id_cabula).trigger('change');
            $('#edit_reserva_local_brotas').val(res_espaco_id_brotas).trigger('change');
            modal_res_quant_pessoas.value = res_quant_pessoas
            modal_res_recursos.value = res_recursos
            $('#edit_res_recursos_add').val(res_recursos_add.split(',').map(id => id.trim())).trigger('change');
            modal_res_obs.value = res_obs
            modal_res_tipo_aula.value = res_tipo_aula
            modal_res_curso.value = res_curso
            modal_res_curso_nome.value = res_curso_nome
            modal_res_curso_extensao.value = res_curso_extensao
            modal_res_semestre.value = res_semestre
            $('#edit_res_componente_atividade').val(res_componente_atividade).trigger('change');
            modal_res_componente_atividade_nome.value = res_componente_atividade_nome
            modal_res_nome_atividade.value = res_nome_atividade
            modal_res_modulo.value = res_modulo
            modal_res_professor.value = res_professor
            modal_res_titulo_aula.value = res_titulo_aula

            modal_res_tipo_reserva.value = res_tipo_reserva;
            if (res_tipo_reserva === "2") {
                const fp_inicio = modal_data_inicio_semanal._flatpickr;
                if (fp_inicio && res_data_inicio_semanal) {
                    fp_inicio.setDate(res_data_inicio_semanal);
                }
                const fp_fim = modal_data_fim_semanal._flatpickr;
                if (fp_fim && res_data_fim_semanal) {
                    fp_fim.setDate(res_data_fim_semanal);
                }
                modal_res_dia_semana.value = res_dia_semana;
            } else {
                const fp_data_diaria = modal_res_data._flatpickr;
                if (fp_data_diaria && res_data) {
                    fp_data_diaria.setDate(res_data);
                }
            }

            modal_res_mes.value = res_mes
            modal_res_ano.value = res_ano
            modal_res_hora_inicio.value = res_hora_inicio
            modal_res_hora_fim.value = res_hora_fim
            modal_res_turno.value = res_turno
        })
    }
</script> -->




<!-- <script>
    const modal_edit_espaco = document.getElementById('modal_edit_espaco');

    if (modal_edit_espaco) {
        modal_edit_espaco.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;

            // Coleta de todos os atributos data-bs
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
            const res_dia_semana_nome = button.getAttribute('data-bs-res_dia_semana');
            const res_mes = button.getAttribute('data-bs-res_mes');
            const res_ano = button.getAttribute('data-bs-res_ano');
            const res_hora_inicio = button.getAttribute('data-bs-res_hora_inicio');
            const res_hora_fim = button.getAttribute('data-bs-res_hora_fim');
            const res_turno = button.getAttribute('data-bs-res_turno');
            const res_dia_semana_fixa_id = button.getAttribute('data-bs-res_dia_semana_fixa');
            const res_data_inicio_semanal = button.getAttribute('data-bs-res_data_inicio_semanal');
            const res_data_fim_semanal = button.getAttribute('data-bs-res_data_fim_semanal');

            // Preenchimento dos campos gerais (sem alteração)
            const modalTitle = modal_edit_espaco.querySelector('.modal-title');
            modalTitle.textContent = 'Atualizar Dados';
            modal_edit_espaco.querySelector('.res_id').value = res_id;
            modal_edit_espaco.querySelector('.res_campus').value = res_campus;
            $('#edit_reserva_local_cabula').val(res_espaco_id_cabula).trigger('change');
            $('#edit_reserva_local_brotas').val(res_espaco_id_brotas).trigger('change');
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

            // Lógica para carregar dinamicamente os componentes curriculares (sem alteração)
            const select_edit_res_componente_atividade = $('#edit_res_componente_atividade');
            if (["2", "5", "6", "9", "13", "14", "17", "18", "21"].includes(res_curso)) {
                $.ajax({
                    url: '../buscar_componentes.php',
                    type: 'POST',
                    data: { curso_id: res_curso },
                    success: function (data) {
                        select_edit_res_componente_atividade.html(data);
                        select_edit_res_componente_atividade.val(res_componente_atividade).trigger('change');
                        select_edit_res_componente_atividade.select2({
                            dropdownParent: $('#modal_edit_espaco'),
                            width: '100%',
                            language: { noResults: () => "Dados não encontrados" }
                        });
                    }
                });
            } else {
                select_edit_res_componente_atividade.html('<option value="">Selecione um componente</option>').trigger('change');
            }


            // Lógica específica para o tipo de reserva
            const modal_res_tipo_reserva = modal_edit_espaco.querySelector('.res_tipo_reserva');
            modal_res_tipo_reserva.value = res_tipo_reserva;

            const modal_res_data = modal_edit_espaco.querySelector('.res_data');
            const input_dia_semana_esporadica = document.getElementById('edit_diaSemanaId_reserva');
            const select_dia_semana_fixa = document.getElementById('edit_res_dia_semana_fixa');
            const modal_res_mes = modal_edit_espaco.querySelector('.res_mes');
            const modal_res_ano = modal_edit_espaco.querySelector('.res_ano');
            const modal_data_inicio_semanal = document.getElementById('edit_data_inicio_semanal');
            const modal_data_fim_semanal = document.getElementById('edit_data_fim_semanal');
            const modal_turno = document.getElementById('edit_turno');
            const modal_hora_inicio = document.getElementById('edit_res_hora_inicio');
            const modal_hora_fim = document.getElementById('edit_res_hora_fim');

            // --- CORREÇÕES A PARTIR DAQUI ---

            if (res_tipo_reserva === "2") { // Reserva Fixa
                // Limpa campos da reserva esporádica
                if (modal_res_data && modal_res_data._flatpickr) modal_res_data._flatpickr.clear();
                input_dia_semana_esporadica.value = '';
                modal_res_mes.value = '';
                modal_res_ano.value = '';

                // Preenche campos da reserva fixa
                if (select_dia_semana_fixa) select_dia_semana_fixa.value = res_dia_semana_fixa_id;
                if (modal_data_inicio_semanal && modal_data_inicio_semanal._flatpickr) {
                    modal_data_inicio_semanal._flatpickr.setDate(res_data_inicio_semanal);
                }
                if (modal_data_fim_semanal && modal_data_fim_semanal._flatpickr) {
                    modal_data_fim_semanal._flatpickr.setDate(res_data_fim_semanal);
                }

            } else { // Reserva Esporádica
                // Limpa campos da reserva fixa
                if (select_dia_semana_fixa) select_dia_semana_fixa.value = '';
                if (modal_data_inicio_semanal && modal_data_inicio_semanal._flatpickr) modal_data_inicio_semanal._flatpickr.clear();
                if (modal_data_fim_semanal && modal_data_fim_semanal._flatpickr) modal_data_fim_semanal._flatpickr.clear();

                // Preenche campos da reserva esporádica
                if (modal_res_data && modal_res_data._flatpickr) {
                    modal_res_data._flatpickr.setDate(res_data);
                }
                modal_res_mes.value = res_mes;
                modal_res_ano.value = res_ano;
                // Preenche o campo de texto do dia da semana
                input_dia_semana_esporadica.value = res_dia_semana_nome;
            }

            // Preenche os campos de hora e turno (são comuns a ambos os tipos de reserva)
            modal_hora_inicio.value = res_hora_inicio;
            modal_hora_fim.value = res_hora_fim;
            modal_turno.value = res_turno;
        });
    }
</script> -->




<script>
    function preencherCamposEdit() {
        const dataEditInputEdit = document.getElementById('edit_data_reserva').value;
        if (dataEditInputEdit) {
            const partes = dataEditInputEdit.split('-');
            const ano = parseInt(partes[0], 10);
            const mes = parseInt(partes[1], 10) - 1;
            const dia = parseInt(partes[2], 10);
            const data = new Date(Date.UTC(ano, mes, dia));
            const meses = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
            const diasSemana = ["7", "1", "2", "3", "4", "5", "6"]; // Esta é a lógica que precisa ser verificada no seu código

            // --- CORREÇÃO AQUI ---
            const diasSemanaCorrigido = [7, 1, 2, 3, 4, 5, 6];

            document.getElementById('edit_mes_reserva').value = meses[data.getUTCMonth()];
            document.getElementById('edit_ano_reserva').value = data.getUTCFullYear();

            // Preenche o valor do select do dia da semana com o ID correto
            document.getElementById('edit_diaSemana_reserva').value = diasSemanaCorrigido[data.getUTCDay()];
        }
    }
</script>

<!-- <script>
    const modal_edit_espaco = document.getElementById('modal_edit_espaco');

    if (modal_edit_espaco) {
        modal_edit_espaco.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;

            // Coleta de todos os atributos data-bs
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

            // Preenchimento dos campos gerais (sem alteração)
            const modalTitle = modal_edit_espaco.querySelector('.modal-title');
            modalTitle.textContent = 'Atualizar Dados';
            modal_edit_espaco.querySelector('.res_id').value = res_id;
            modal_edit_espaco.querySelector('.res_campus').value = res_campus;
            $('#edit_reserva_local_cabula').val(res_espaco_id_cabula).trigger('change');
            $('#edit_reserva_local_brotas').val(res_espaco_id_brotas).trigger('change');
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

            // Lógica para carregar dinamicamente os componentes curriculares (sem alteração)
            const select_edit_res_componente_atividade = $('#edit_res_componente_atividade');
            if (["2", "5", "6", "9", "13", "14", "17", "18", "21"].includes(res_curso)) {
                $.ajax({
                    url: '../buscar_componentes.php',
                    type: 'POST',
                    data: { curso_id: res_curso },
                    success: function (data) {
                        select_edit_res_componente_atividade.html(data);
                        select_edit_res_componente_atividade.val(res_componente_atividade).trigger('change');
                        select_edit_res_componente_atividade.select2({
                            dropdownParent: $('#modal_edit_espaco'),
                            width: '100%',
                            language: { noResults: () => "Dados não encontrados" }
                        });
                    }
                });
            } else {
                select_edit_res_componente_atividade.html('<option value="">Selecione um componente</option>').trigger('change');
            }

            // Lógica específica para o tipo de reserva
            const modal_res_tipo_reserva = modal_edit_espaco.querySelector('.res_tipo_reserva');
            modal_res_tipo_reserva.value = res_tipo_reserva;

            const modal_res_data = modal_edit_espaco.querySelector('.res_data');
            const input_dia_semana_esporadica = document.getElementById('edit_diaSemanaId_reserva');
            const select_dia_semana_fixa = document.getElementById('edit_res_dia_semana_fixa');
            const modal_res_mes = modal_edit_espaco.querySelector('.res_mes');
            const modal_res_ano = modal_edit_espaco.querySelector('.res_ano');
            const modal_data_inicio_semanal = document.getElementById('edit_data_inicio_semanal');
            const modal_data_fim_semanal = document.getElementById('edit_data_fim_semanal');
            const modal_turno = document.getElementById('edit_turno');
            const modal_hora_inicio = document.getElementById('edit_res_hora_inicio');
            const modal_hora_fim = document.getElementById('edit_res_hora_fim');

            if (res_tipo_reserva === "2") { // Reserva Fixa
                // Limpa campos da reserva esporádica
                if (modal_res_data && modal_res_data._flatpickr) modal_res_data._flatpickr.clear();
                input_dia_semana_esporadica.value = '';
                modal_res_mes.value = '';
                modal_res_ano.value = '';

                // Preenche campos da reserva fixa
                if (select_dia_semana_fixa) select_dia_semana_fixa.value = res_dia_semana_fixa_id;
                if (modal_data_inicio_semanal && modal_data_inicio_semanal._flatpickr) {
                    modal_data_inicio_semanal._flatpickr.setDate(res_data_inicio_semanal);
                }
                if (modal_data_fim_semanal && modal_data_fim_semanal._flatpickr) {
                    modal_data_fim_semanal._flatpickr.setDate(res_data_fim_semanal);
                }

            } else { // Reserva Esporádica
                // Limpa campos da reserva fixa
                if (select_dia_semana_fixa) select_dia_semana_fixa.value = '';
                if (modal_data_inicio_semanal && modal_data_inicio_semanal._flatpickr) modal_data_inicio_semanal._flatpickr.clear();
                if (modal_data_fim_semanal && modal_data_fim_semanal._flatpickr) modal_data_fim_semanal._flatpickr.clear();

                // Preenche campos da reserva esporádica
                if (modal_res_data && modal_res_data._flatpickr) {
                    modal_res_data._flatpickr.setDate(res_data);
                }
                modal_res_mes.value = res_mes;
                modal_res_ano.value = res_ano;

                // Preenche o campo de texto do dia da semana a partir da data
                if (res_data) {
                    preencherCamposEdit();
                }
            }

            // Preenche os campos de hora e turno (são comuns a ambos os tipos de reserva)
            modal_hora_inicio.value = res_hora_inicio;
            modal_hora_fim.value = res_hora_fim;
            modal_turno.value = res_turno;
        });
    }
</script> -->



<script>
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
</script>

<!-- <script>
    const modal_edit_espaco = document.getElementById('modal_edit_espaco');

    if (modal_edit_espaco) {
        modal_edit_espaco.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;

            // Coleta de todos os atributos data-bs
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
            const modalTitle = modal_edit_espaco.querySelector('.modal-title');
            modalTitle.textContent = 'Atualizar Dados';
            modal_edit_espaco.querySelector('.res_id').value = res_id;
            modal_edit_espaco.querySelector('.res_campus').value = res_campus;
            $('#edit_reserva_local_cabula').val(res_espaco_id_cabula).trigger('change');
            $('#edit_reserva_local_brotas').val(res_espaco_id_brotas).trigger('change');
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
                    data: { curso_id: res_curso },
                    success: function (data) {
                        select_edit_res_componente_atividade.html(data);
                        select_edit_res_componente_atividade.val(res_componente_atividade).trigger('change');
                        select_edit_res_componente_atividade.select2({
                            dropdownParent: $('#modal_edit_espaco'),
                            width: '100%',
                            language: { noResults: () => "Dados não encontrados" }
                        });
                    }
                });
            } else {
                select_edit_res_componente_atividade.html('<option value="">Selecione um componente</option>').trigger('change');
            }

            // Lógica específica para o tipo de reserva
            const modal_res_tipo_reserva = modal_edit_espaco.querySelector('.res_tipo_reserva');
            modal_res_tipo_reserva.value = res_tipo_reserva;

            const modal_res_data = modal_edit_espaco.querySelector('.res_data');
            const input_dia_semana_esporadica = document.getElementById('edit_diaSemanaId_reserva');
            const select_dia_semana_fixa = document.getElementById('edit_res_dia_semana_fixa');
            const modal_res_mes = modal_edit_espaco.querySelector('.res_mes');
            const modal_res_ano = modal_edit_espaco.querySelector('.res_ano');
            const modal_data_inicio_semanal = document.getElementById('edit_data_inicio_semanal');
            const modal_data_fim_semanal = document.getElementById('edit_data_fim_semanal');
            const modal_turno = document.getElementById('edit_turno');
            const modal_hora_inicio = document.getElementById('edit_res_hora_inicio');
            const modal_hora_fim = document.getElementById('edit_res_hora_fim');

            if (res_tipo_reserva === "2") { // Reserva Fixa
                // Limpa campos da reserva esporádica
                if (modal_res_data && modal_res_data._flatpickr) modal_res_data._flatpickr.clear();
                input_dia_semana_esporadica.value = '';
                modal_res_mes.value = '';
                modal_res_ano.value = '';

                // Preenche campos da reserva fixa
                if (select_dia_semana_fixa) select_dia_semana_fixa.value = res_dia_semana_fixa_id;
                if (modal_data_inicio_semanal && modal_data_inicio_semanal._flatpickr) {
                    modal_data_inicio_semanal._flatpickr.setDate(res_data_inicio_semanal);
                }
                if (modal_data_fim_semanal && modal_data_fim_semanal._flatpickr) {
                    modal_data_fim_semanal._flatpickr.setDate(res_data_fim_semanal);
                }

            } else { // Reserva Esporádica
                // Limpa campos da reserva fixa
                if (select_dia_semana_fixa) select_dia_semana_fixa.value = '';
                if (modal_data_inicio_semanal && modal_data_inicio_semanal._flatpickr) modal_data_inicio_semanal._flatpickr.clear();
                if (modal_data_fim_semanal && modal_data_fim_semanal._flatpickr) modal_data_fim_semanal._flatpickr.clear();

                // Preenche campos da reserva esporádica
                if (modal_res_data && modal_res_data._flatpickr) {
                    modal_res_data._flatpickr.setDate(res_data);
                }
                modal_res_mes.value = res_mes;
                modal_res_ano.value = res_ano;

                // Preenche o campo de dia da semana a partir da data
                if (res_data) {
                    preencherCamposEdit();
                }
            }

            // Preenche os campos de hora e turno (comuns a ambos)
            modal_hora_inicio.value = res_hora_inicio;
            modal_hora_fim.value = res_hora_fim;
            modal_turno.value = res_turno;
        });
    }
</script> -->




<script>
    const modal_edit_espaco = document.getElementById('modal_edit_espaco');

    if (modal_edit_espaco) {
        modal_edit_espaco.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;

            // Coleta de todos os atributos data-bs
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
            const modalTitle = modal_edit_espaco.querySelector('.modal-title');
            modalTitle.textContent = 'Atualizar Dados';
            modal_edit_espaco.querySelector('.res_id').value = res_id;
            modal_edit_espaco.querySelector('.res_campus').value = res_campus;
            $('#edit_reserva_local_cabula').val(res_espaco_id_cabula).trigger('change');
            $('#edit_reserva_local_brotas').val(res_espaco_id_brotas).trigger('change');
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
                    data: { curso_id: res_curso },
                    success: function (data) {
                        select_edit_res_componente_atividade.html(data);
                        select_edit_res_componente_atividade.val(res_componente_atividade).trigger('change');
                        select_edit_res_componente_atividade.select2({
                            dropdownParent: $('#modal_edit_espaco'),
                            width: '100%',
                            language: { noResults: () => "Dados não encontrados" }
                        });
                    }
                });
            } else {
                select_edit_res_componente_atividade.html('<option value="">Selecione um componente</option>').trigger('change');
            }

            // Lógica específica para o tipo de reserva
            const modal_res_tipo_reserva = modal_edit_espaco.querySelector('.res_tipo_reserva');
            modal_res_tipo_reserva.value = res_tipo_reserva;

            const modal_res_data = modal_edit_espaco.querySelector('.res_data');
            const select_dia_semana_esporadica = document.getElementById('edit_diaSemana_reserva');
            const select_dia_semana_fixa = document.getElementById('edit_res_dia_semana_fixa');
            const modal_res_mes = modal_edit_espaco.querySelector('.res_mes');
            const modal_res_ano = modal_edit_espaco.querySelector('.res_ano');
            const modal_data_inicio_semanal = document.getElementById('edit_data_inicio_semanal');
            const modal_data_fim_semanal = document.getElementById('edit_data_fim_semanal');
            const modal_turno = document.getElementById('edit_turno');
            const modal_hora_inicio = document.getElementById('edit_res_hora_inicio');
            const modal_hora_fim = document.getElementById('edit_res_hora_fim');

            if (res_tipo_reserva === "2") { // Reserva Fixa
                // Limpa campos da reserva esporádica
                if (modal_res_data && modal_res_data._flatpickr) modal_res_data._flatpickr.clear();
                select_dia_semana_esporadica.value = '';
                modal_res_mes.value = '';
                modal_res_ano.value = '';

                // Preenche campos da reserva fixa
                if (select_dia_semana_fixa) select_dia_semana_fixa.value = res_dia_semana_fixa_id;
                if (modal_data_inicio_semanal && modal_data_inicio_semanal._flatpickr) {
                    modal_data_inicio_semanal._flatpickr.setDate(res_data_inicio_semanal);
                }
                if (modal_data_fim_semanal && modal_data_fim_semanal._flatpickr) {
                    modal_data_fim_semanal._flatpickr.setDate(res_data_fim_semanal);
                }

            } else { // Reserva Esporádica
                // Limpa campos da reserva fixa
                if (select_dia_semana_fixa) select_dia_semana_fixa.value = '';
                if (modal_data_inicio_semanal && modal_data_inicio_semanal._flatpickr) modal_data_inicio_semanal._flatpickr.clear();
                if (modal_data_fim_semanal && modal_data_fim_semanal._flatpickr) modal_data_fim_semanal._flatpickr.clear();

                // Preenche campos da reserva esporádica
                if (modal_res_data && modal_res_data._flatpickr) {
                    modal_res_data._flatpickr.setDate(res_data);
                }
                modal_res_mes.value = res_mes;
                modal_res_ano.value = res_ano;
                // Preenche o campo de dia da semana a partir da data
                if (res_data) {
                    preencherCamposEdit();
                }
            }

            // Preenche os campos de hora e turno (comuns a ambos)
            modal_hora_inicio.value = res_hora_inicio;
            modal_hora_fim.value = res_hora_fim;
            modal_turno.value = res_turno;
        });
    }
</script>




<script>
    // $(document).ready(function() {
    //   $('#edit_res_componente_atividade').select2({
    //     dropdownParent: $('#modal_edit_espaco'),
    //     width: '100%',
    //     language: {
    //       noResults: function() {
    //         return "Dados não encontrados";
    //       }
    //     }
    //   });
    //   $('#edit_res_curso').change(function() {
    //     const cursoId = $(this).val();

    //     if (cursoId !== "") {
    //       $.ajax({
    //         url: '../buscar_componentes.php',
    //         type: 'POST',
    //         data: {
    //           curso_id: cursoId
    //         },
    //         success: function(data) {
    //           $('#edit_res_componente_atividade').html(data).trigger('change');

    //           $('#edit_res_componente_atividade').select2({
    //             dropdownParent: $('#modal_edit_espaco'),
    //             width: '100%',
    //             language: {
    //               noResults: function() {
    //                 return "Dados não encontrados";
    //               }
    //             }
    //           });
    //         }
    //       });
    //     } else {
    //       $('#edit_res_componente_atividade').html('<option value="">Selecione um componente</option>').trigger('change');
    //     }
    //   });
    //   if ($('#edit_res_curso').val() !== "") {
    //     $('#edit_res_curso').trigger('change');
    //   }
    // });
</script>