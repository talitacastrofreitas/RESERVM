<?php
// BUSCAS AS HORAS DE FUNCIONAMENTO PARA LIMITAR NOS CAMPOS DE HORAS DE INÍCIO E FIM
$sql = "SELECT MIN(chf_hora_inicio) AS hora_inicio, MAX(chf_hora_fim) AS hora_fim FROM conf_hora_funcionamento";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$limit_hora_inicio = substr($result['hora_inicio'], 0, 5); // "07:00"
$limit_hora_fim = substr($result['hora_fim'], 0, 5);       // "21:00"
?>

<!-- CADASTRO -->
<div class="modal fade modal_padrao" id="modal_cad_espaco" aria-hidden="true" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title">Confirmar Reserva</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <form id="form_reserva" class="needs-validation" action="../router/web.php?r=Reserv" method="POST" novalidate>

        <input type="hidden" class="form-control" name="res_solic_id" value="<?= $_GET['i'] ?>" required>
        <input type="hidden" class="form-control" name="acao" value="cadastrar" required>

        <!-- Etapa 1 -->
        <div class="etapa" id="etapa1">
          <div class="modal-body">

            <div id="custom-progress-bar" class="progress-nav mb-5 mt-2">
              <div class="progress" style="height: 1px;">
                <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="0">
                </div>
              </div>

              <ul class="nav nav-pills progress-bar-tab custom-nav" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link rounded-pill active" data-progressbar="custom-progress-bar" id="pills-gen-info-tab" data-bs-toggle="pill" data-bs-target="#pills-gen-info" role="tab" aria-controls="pills-gen-info" aria-selected="false" data-position="0" tabindex="-1" disabled>Local</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link rounded-pill" data-progressbar="custom-progress-bar" id="pills-info-desc-tab" data-bs-toggle="pill" data-bs-target="#pills-info-desc" role="tab" aria-controls="pills-info-desc" aria-selected="false" data-position="1" tabindex="-1" disabled>Atividade</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link rounded-pill" data-progressbar="custom-progress-bar" id="pills-success-tab" data-bs-toggle="pill" data-bs-target="#pills-success" role="tab" aria-controls="pills-success" aria-selected="true" data-position="2" disabled>Período</button>
                </li>
              </ul>
            </div>

            <div class="row g-3">

              <div class="col-xl-3">
                <?php try {
                  $sql = $conn->prepare("SELECT uni_id, uni_unidade FROM unidades");
                  $sql->execute();
                  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar o perfil";
                } ?>
                <label class="form-label">Campus <span>*</span></label>
                <select class="form-select text-uppercase" name="res_campus" id="cad_reserva_campus" required>
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['uni_id'] ?>"><?= $res['uni_unidade'] ?></option>
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
                <select class="form-select text-uppercase" name="res_espaco_id_cabula" id="cad_reserva_local_cabula">
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['esp_id'] ?>"><?= $res['esp_codigo'] . ' - ' . $res['esp_nome_local'] ?></option>
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
                <select class="form-select text-uppercase" name="res_espaco_id_brotas" id="cad_reserva_local_brotas">
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['esp_id'] ?>"><?= $res['esp_codigo'] . ' - ' . $res['esp_nome_local'] ?></option>
                  <?php endforeach; ?>
                </select>
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
              </script>

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
                <select class="form-select text-uppercase" id="cad_reserva_tipo_sala" disabled>
                  <option selected value=""></option>
                  <?php foreach ($result as $res) : ?>
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
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar o perfil";
                } ?>
                <label class="form-label">Andar</label>
                <select class="form-select text-uppercase" id="cad_reserva_andar" disabled>
                  <option selected value=""></option>
                  <?php foreach ($result as $res) : ?>
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
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar o perfil";
                } ?>
                <label class="form-label">Pavilhão</label>
                <select class="form-select text-uppercase" id="cad_reserva_pavilhao" disabled>
                  <option selected value=""></option>
                  <?php foreach ($result as $res) : ?>
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
                <input type="text" class="form-control" name="res_quant_pessoas" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-xl-3">
                <label class="form-label">Recursos Audiovisuais <span>*</span></label>
                <select class="form-select text-uppercase" name="res_recursos" id="cad_res_recursos" required>
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
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar o perfil";
                } ?>
                <label class="form-label">Recursos Audiovisuais Adicionais <span>*</span></label>
                <select class="form-select text-uppercase" name="res_recursos_add[]" multiple id="cad_res_recursos_add">
                  <!-- <option selected value=""></option> -->
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['rec_id'] ?>"><?= $res['rec_recurso'] ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
                <script>
                  $(document).ready(function() {
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

                cad_res_recursos.addEventListener("change", function() {
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
                <textarea class="form-control" id="meuTextarea" name="res_obs" rows="3" cols="50" maxlength="200"></textarea>
                <p class="label_info text-end mt-1">Caracteres restantes: <span id="contador">200</span></p>
                <script>
                  const textarea = document.getElementById('meuTextarea');
                  const contador = document.getElementById('contador');
                  const limite = 200;

                  textarea.addEventListener('input', function() {
                    const total = textarea.value.length;
                    contador.textContent = `${total} / ${limite}`;
                  });
                </script>
              </div>

              <div class="col-lg-12">
                <div class="hstack gap-3 align-items-center justify-content-end mt-3">
                  <p class="label_asterisco m-0"><span>*</span> Campo obrigatório</p>
                  <button type="button" class="btn botao_azul_escuro btn-label right ms-auto nexttab nexttab waves-effect" id="btnProximo1" data-form="form_etapa1" data-next="#modal_cad_espaco2"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i> Próximo</button>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- Etapa 2 -->
        <div class="etapa d-none" id="etapa2">
          <div class="modal-body">

            <div id="custom-progress-bar" class="progress-nav mb-5 mt-2">
              <div class="progress" style="height: 1px;">
                <div class="progress-bar" role="progressbar" style="width: 50%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="0">
                </div>
              </div>

              <ul class="nav nav-pills progress-bar-tab custom-nav" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link rounded-pill done" data-progressbar="custom-progress-bar" id="pills-gen-info-tab" data-bs-toggle="pill" data-bs-target="#pills-gen-info" type="button" role="tab" aria-controls="pills-gen-info" aria-selected="false" data-position="0" tabindex="-1" disabled>Local</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link rounded-pill active" data-progressbar="custom-progress-bar" id="pills-info-desc-tab" data-bs-toggle="pill" data-bs-target="#pills-info-desc" type="button" role="tab" aria-controls="pills-info-desc" aria-selected="false" data-position="1" tabindex="-1" disabled>Atividade</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link rounded-pill" data-progressbar="custom-progress-bar" id="pills-success-tab" data-bs-toggle="pill" data-bs-target="#pills-success" type="button" role="tab" aria-controls="pills-success" aria-selected="true" data-position="2" disabled>Período</button>
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
                  // echo "Erro: " . $e->getMessage();
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

                  <?php foreach ($result as $res) : ?>
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
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar os dados";
                } ?>
                <label class="form-label">Curso <span>*</span></label>
                <select class="form-select text-uppercase" name="res_curso" id="cad_res_curso">
                  <option selected value="<?= $solic_row['curs_id'] ?>"><?= $solic_row['curs_curso'] ?></option>
                  <?php foreach ($result as $res) : ?>
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
                <select class="form-select text-uppercase" name="res_curso_extensao" id="cad_res_curso_extensao">
                  <option selected value="<?= $solic_row['cexc_id'] ?>"><?= $solic_row['cexc_curso'] ?></option>
                  <?php foreach ($result as $res) : ?>
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
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar os dados";
                } ?>
                <label class="form-label">Semestre</label>
                <select class="form-select text-uppercase" name="res_semestre" id="cad_res_semestre">
                  <option selected value="<?= $solic_row['cs_id'] ?>"><?= $solic_row['cs_semestre'] ?></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['cs_id'] ?>"><?= $res['cs_semestre'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-xl-12" id="campo_res_componente_atividade" style="display: none;">
                <label class="form-label">Componente Curricular/Atividade <span>*</span></label>
                <select class="form-select text-uppercase" name="res_componente_atividade" id="cad_res_componente_atividade">
                  <option selected value="<?= $solic_row['compc_id'] ?>"><?= $solic_row['compc_componente'] ?></option>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-xl-12" id="campo_res_componente_atividade_texto" style="display: none;">
                <label class="form-label">Componente Curricular/Atividade <span>*</span></label>
                <input type="text" class="form-control text-uppercase" name="res_componente_atividade_nome" id="cad_res_componente_atividade_nome" value="<?= $solic_row['solic_nome_comp_ativ'] ?>" maxlength="200">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-xl-12" id="campo_res_nome_atividade" style="display: none;">
                <label class="form-label">Nome da Atividade <span>*</span></label>
                <input type="text" class="form-control text-uppercase" name="res_nome_atividade" id="cad_res_nome_atividade" value="<?= $solic_row['solic_nome_atividade'] ?>" maxlength="200">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-xl-12" id="campo_res_nome_curso_texto" style="display: none;">
                <label class="form-label">Nome do curso <span>*</span></label>
                <input type="text" class="form-control text-uppercase" name="res_curso_nome" id="cad_res_nome_curso" value="<?= $solic_row['solic_nome_curso_text'] ?>" maxlength="200">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-xl-12">
                <label class="form-label">Módulo</label>
                <input type="text" class="form-control text-uppercase" name="res_modulo" maxlength="200">
              </div>

              <div class="col-12">
                <label class="form-label">Título da Aula</label>
                <input type="text" class="form-control text-uppercase" name="res_titulo_aula" maxlength="200">
              </div>

              <div class="col-xl-12">
                <label class="form-label">Professor(es)</label>
                <input type="text" class="form-control text-uppercase" name="res_professor" value="<?= $solic_row['solic_nome_prof_resp'] ?>" maxlength="200">
              </div>

              <div class="col-lg-12">
                <div class="hstack gap-3 align-items-center justify-content-between mt-2">
                  <button type="button" class="btn btn-light btn-label previestab waves-effect" id="btnAnterior2"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Anterior</button>
                  <button type="button" class="btn botao_azul_escuro btn-label right ms-auto nexttab nexttab waves-effect" id="btnProximo2" data-form="form_etapa2" data-next="#modal_cad_espaco3"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i> Próximo</button>
                </div>
              </div>
              <script>
                // const cad_res_curso = document.getElementById("cad_res_curso");
                // const cad_res_tipo_ativ = document.getElementById("cad_res_tipo_ativ");
                // //
                // const campo_res_componente_atividade = document.getElementById("campo_res_componente_atividade");
                // const campo_res_componente_atividade_texto = document.getElementById("campo_res_componente_atividade_texto");
                // const campo_res_nome_curso = document.getElementById("campo_res_nome_curso");
                // const campo_res_nome_curso_texto = document.getElementById("campo_res_nome_curso_texto");
                // const campo_res_nome_atividade = document.getElementById("campo_res_nome_atividade");

                // cad_res_curso.addEventListener("change", function() {
                //   const valor = cad_res_curso.value;

                //   // Oculta todos os campos por padrão
                //   campo_res_nome_curso.style.display = "none";
                //   campo_res_componente_atividade.style.display = "none";
                //   campo_res_componente_atividade_texto.style.display = "none";
                //   campo_res_nome_atividade.style.display = "none";
                //   campo_res_nome_curso_texto.style.display = "none";
                //   //
                //   document.getElementById("cad_res_componente_atividade").required = false;
                //   document.getElementById("cad_res_componente_atividade_nome").required = false;
                //   document.getElementById("cad_res_nome_atividade").required = false;
                //   document.getElementById("cad_res_nome_curso").required = false;
                //   document.getElementById("cad_res_curso_extensao").required = false;

                //   if (["2", "5", "6", "9", "13", "14", "17", "18", "21"].includes(valor)) {
                //     campo_res_componente_atividade.style.display = "block";
                //     //
                //     document.getElementById("cad_res_componente_atividade").required = true;

                //   } else if (["7", "10", "19", "28"].includes(valor)) {
                //     campo_res_nome_atividade.style.display = "block";
                //     //
                //     document.getElementById("cad_res_nome_atividade").required = true;

                //   } else if (valor === "8") {
                //     campo_res_nome_atividade.style.display = "block";
                //     campo_res_nome_curso.style.display = "block";
                //     //
                //     document.getElementById("cad_res_nome_atividade").required = true;
                //     //document.getElementById("cad_res_nome_curso").required = true;
                //     document.getElementById("cad_res_curso_extensao").required = true;

                //   } else if (["11", "22"].includes(valor)) {
                //     campo_res_componente_atividade_texto.style.display = "block";
                //     campo_res_nome_curso_texto.style.display = "block";
                //     //
                //     document.getElementById("cad_res_componente_atividade_nome").required = true;
                //     document.getElementById("cad_res_nome_curso").required = true;
                //   }
                // });

                // cad_res_tipo_ativ.addEventListener("change", function() {
                //   const valor_tipo_ativ = cad_res_tipo_ativ.value;

                //   // Oculta todos os campos por padrão
                //   campo_res_nome_curso.style.display = "none";
                //   campo_res_componente_atividade.style.display = "none";
                //   campo_res_componente_atividade_texto.style.display = "none";
                //   campo_res_nome_atividade.style.display = "none";
                //   campo_res_nome_curso_texto.style.display = "none";
                //   //
                //   document.getElementById("cad_res_componente_atividade").required = false;
                //   document.getElementById("cad_res_componente_atividade").value = '';
                //   document.getElementById("cad_res_componente_atividade_nome").required = false;
                //   document.getElementById("cad_res_componente_atividade_nome").value = '';
                //   document.getElementById("cad_res_nome_atividade").required = false;
                //   document.getElementById("cad_res_nome_atividade").value = '';
                //   document.getElementById("cad_res_nome_curso").required = false;
                //   document.getElementById("cad_res_nome_curso").value = '';
                //   document.getElementById("cad_res_curso_extensao").required = false;
                //   document.getElementById("cad_res_curso_extensao").value = '';
                //   //
                //   document.getElementById("cad_res_curso").value = '';
                //   document.getElementById("cad_res_curso").value = '';
                //   document.getElementById("cad_res_semestre").value = '';

                //   if (valor_tipo_ativ === "2") {
                //     campo_res_nome_atividade.style.display = "block";
                //     document.getElementById("cad_res_nome_atividade").required = true;
                //   } else {
                //     campo_res_nome_atividade.style.display = "none";
                //     document.getElementById("cad_res_nome_atividade").required = false;
                //   }
                // });

                // const valor = cad_res_curso.value;
                // const valor_tipo_ativ = cad_res_tipo_ativ.value;

                // // Oculta todos os campos por padrão
                // campo_res_nome_curso.style.display = "none";
                // campo_res_componente_atividade.style.display = "none";
                // campo_res_componente_atividade_texto.style.display = "none";
                // campo_res_nome_atividade.style.display = "none";
                // campo_res_nome_curso_texto.style.display = "none";
                // //
                // document.getElementById("cad_res_componente_atividade").required = false;
                // document.getElementById("cad_res_componente_atividade_nome").required = false;
                // document.getElementById("cad_res_nome_atividade").required = false;
                // document.getElementById("cad_res_nome_curso").required = false;
                // document.getElementById("cad_res_curso_extensao").required = false;

                // if (["2", "5", "6", "9", "13", "14", "17", "18", "21"].includes(valor)) {
                //   campo_res_componente_atividade.style.display = "block";
                //   //
                //   document.getElementById("cad_res_componente_atividade").required = true;

                // } else if (["7", "10", "19", "28"].includes(valor)) {
                //   campo_res_nome_atividade.style.display = "block";
                //   //
                //   document.getElementById("cad_res_nome_atividade").required = true;

                // } else if (valor === "8") {
                //   campo_res_nome_atividade.style.display = "block";
                //   campo_res_nome_curso.style.display = "block";
                //   //
                //   document.getElementById("cad_res_nome_atividade").required = true;
                //   // document.getElementById("cad_res_nome_curso").required = true;
                //   document.getElementById("cad_res_curso_extensao").required = true;

                // } else if (["11", "22"].includes(valor)) {
                //   campo_res_componente_atividade_texto.style.display = "block";
                //   campo_res_nome_curso_texto.style.display = "block";
                //   //
                //   document.getElementById("cad_res_componente_atividade_nome").required = true;
                //   document.getElementById("cad_res_nome_curso").required = true;

                // }

                // if (valor_tipo_ativ === "2") {
                //   campo_res_nome_atividade.style.display = "block";
                //   document.getElementById("cad_res_nome_atividade").required = true;
                // }
                // if (valor_tipo_ativ === "1") {
                //   campo_res_nome_atividade.style.display = "none";
                //   document.getElementById("cad_res_nome_atividade").required = false;
                // }







                // Elementos
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

                  // Oculta e remove required de todos por padrão
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

                  // Exibe conforme o valor do curso e define required
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

                // Atualiza os campos ao mudar a seleção
                cad_res_curso.addEventListener("change", atualizarCamposCad);

                // Atualiza os campos automaticamente ao abrir o modal
                const modalCad = document.getElementById('modal_cad_espaco');
                modalCad.addEventListener('shown.bs.modal', atualizarCamposCad);
              </script>
            </div>
          </div>
        </div>

        <!-- Etapa 3 -->
        <div class="etapa d-none" id="etapa3">

          <div class="modal-body">

            <div id="custom-progress-bar" class="progress-nav mb-5 mt-2">
              <div class="progress" style="height: 1px;">
                <div class="progress-bar" role="progressbar" style="width: 100%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="0">
                </div>
              </div>

              <ul class="nav nav-pills progress-bar-tab custom-nav" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link rounded-pill done" data-progressbar="custom-progress-bar" id="pills-gen-info-tab" data-bs-toggle="pill" data-bs-target="#pills-gen-info" type="button" role="tab" aria-controls="pills-gen-info" aria-selected="false" data-position="0" tabindex="-1" disabled>Local</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link rounded-pill done" data-progressbar="custom-progress-bar" id="pills-info-desc-tab" data-bs-toggle="pill" data-bs-target="#pills-info-desc" type="button" role="tab" aria-controls="pills-info-desc" aria-selected="false" data-position="1" tabindex="-1" disabled>Atividade</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link rounded-pill active" data-progressbar="custom-progress-bar" id="pills-success-tab" data-bs-toggle="pill" data-bs-target="#pills-success" type="button" role="tab" aria-controls="pills-success" aria-selected="true" data-position="2" disabled>Período</button>
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
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar o perfil";
                } ?>
                <label class="form-label">Tipo de Reserva <span>*</span></label>
                <select class="form-select text-uppercase" name="res_tipo_reserva" id="cad_tipo_reserva" required>
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['ctr_id'] ?>"><?= $res['ctr_tipo_reserva'] ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
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
                <label class="form-label">Dia da Semana <span>*</span></label>
                <select class="form-select text-uppercase" name="res_dia_semana" id="cad_res_dia_semana">
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['week_id'] ?>"><?= $res['week_dias'] ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-6 col-lg-4 col-xl-3" id="camp_reserv_data_disable">
                <label class="form-label">Data da Reserva</label>
                <input type="date" class="form-control" disabled>
              </div>

              <div class="col-6 col-lg-4 col-xl-3" id="camp_reserv_data" style="display: none;">
                <label class="form-label">Data da Reserva <span>*</span></label>
                <input type="text" class="form-control flatpickr-input" name="res_data" id="cad_data_reserva" onchange="preencherCampos()">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-6 col-lg-4 col-xl-3" id="camp_reserv_dia">
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
                  <input type="hidden" class="form-control text-uppercase" id="cad_diaSemanaId_reserva">
                  <select class="form-select text-uppercase" name="res_dia_semana" id="cad_diaSemana_reserva" disabled>
                    <option selected disabled value=""></option>
                    <?php foreach ($result as $res) : ?>
                      <option value="<?= $res['week_id'] ?>"><?= htmlspecialchars($res['week_dias']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>

              <div class="col-6 col-lg-4 col-xl-3" id="camp_reserv_mes">
                <label class="form-label">Mês</label>
                <input type="text" class="form-control text-uppercase" name="res_mes" id="cad_mes_reserva" readonly>
              </div>

              <div class="col-6 col-lg-4 col-xl-3" id="camp_reserv_ano">
                <label class="form-label">Ano</label>
                <input type="text" class="form-control text-uppercase" name="res_ano" id="cad_ano_reserva" readonly>
              </div>

              <div class="col-6 col-lg-4 col-xl-3">
                <label class="form-label">Hora Início <span>*</span></label>
                <input type="time" class="form-control hora" id="cad_res_hora_inicio" name="res_hora_inicio" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-6 col-lg-4 col-xl-3">
                <label class="form-label">Hora Fim <span>*</span></label>
                <input type="time" class="form-control hora" id="cad_res_hora_fim" name="res_hora_fim" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
              <script>
                document.addEventListener('DOMContentLoaded', function() {
                  const horaInicio = document.getElementById('cad_res_hora_inicio');
                  const horaFim = document.getElementById('cad_res_hora_fim');

                  function validarHoras() {
                    const inicio = horaInicio.value;
                    const fim = horaFim.value;

                    // Só valida se ambos os campos estiverem preenchidos
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

                  // Você pode ajustar o tipo de evento conforme preferir
                  horaInicio.addEventListener('change', validarHoras);
                  horaFim.addEventListener('change', validarHoras);
                });
              </script>
              <script>
                const inputHoraInicio = document.getElementById("cad_res_hora_inicio");
                const inputHoraFim = document.getElementById("cad_res_hora_fim");

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

                  input.addEventListener('input', function() {
                    if (input.value.trim()) {
                      input.classList.remove('is-invalid');
                    }
                  });
                });

                const inputData = document.getElementById("cad_data_reserva");
                const cad_tipo_reserva = document.getElementById("cad_tipo_reserva");
                const camp_reserv_dia_semana = document.getElementById("camp_reserv_dia_semana");
                const camp_reserv_data_disable = document.getElementById("camp_reserv_data_disable");
                const camp_reserv_data = document.getElementById("camp_reserv_data");
                const camp_reserv_mes = document.getElementById("camp_reserv_mes");
                const camp_reserv_ano = document.getElementById("camp_reserv_ano");
                const camp_reserv_dia = document.getElementById("camp_reserv_dia");

                let fpInstance;

                fetch('busca_datas_bloqueadas.php')
                  .then(response => response.json())
                  .then(datasBloqueadas => {
                    fpInstance = flatpickr("#cad_data_reserva", {
                      disable: datasBloqueadas,
                      dateFormat: "Y-m-d",
                      altInput: true,
                      altFormat: "d/m/Y",
                      locale: "pt",
                      allowInput: true,
                      onChange: function(selectedDates, dateStr, instance) {
                        instance.altInput.classList.remove('is-invalid');
                      }
                    });

                    const altInput = fpInstance.altInput;

                    altInput.addEventListener('input', function() {
                      if (altInput.value.trim()) {
                        altInput.classList.remove('is-invalid');
                      }
                    });

                    // Exibição condicional
                    cad_tipo_reserva.addEventListener("change", function() {
                      if (cad_tipo_reserva.value === "2") {
                        camp_reserv_dia_semana.style.display = "block";
                        camp_reserv_data_disable.style.display = "none";
                        camp_reserv_data.style.display = "none";
                        camp_reserv_mes.style.display = "none";
                        camp_reserv_ano.style.display = "none";
                        camp_reserv_dia.style.display = "none";

                        document.getElementById("cad_res_dia_semana").required = true;
                        inputData.required = false;
                        altInput.removeAttribute('required');
                        altInput.classList.remove('is-invalid');
                      } else {
                        camp_reserv_dia_semana.style.display = "none";
                        camp_reserv_data_disable.style.display = "none";
                        camp_reserv_data.style.display = "block";
                        camp_reserv_mes.style.display = "block";
                        camp_reserv_ano.style.display = "block";
                        camp_reserv_dia.style.display = "block";

                        document.getElementById("cad_res_dia_semana").required = false;
                        inputData.required = false;
                        altInput.setAttribute('required', 'required');
                      }
                    });

                    // Validação ao enviar o formulário
                    document.querySelector("form").addEventListener("submit", function(event) {
                      let formValido = true;

                      if (cad_tipo_reserva.value !== "2" && !altInput.value.trim()) {
                        altInput.classList.add('is-invalid');
                        formValido = false;
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
                <input type="text" class="form-control text-uppercase" name="res_turno" id="turno" readonly>
              </div>
              <script>
                document.getElementById('cad_res_hora_inicio').addEventListener('change', function() {
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
                  <button type="button" class="btn btn-light btn-label previestab waves-effect" id="btnAnterior3"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Anterior</button>
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

      // Corrigir validação visual dos campos select2
      document.querySelectorAll('select').forEach(select => {
        if ($(select).hasClass('select2-hidden-accessible')) {
          $(select).on('change', function() {
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
</div>



















<!-- EDIÇÃO -->
<div class="modal fade modal_padrao" id="modal_edit_espaco" aria-hidden="true" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title">Editar Reserva</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <form id="form_reserva_edit" class="needs-validation" action="../router/web.php?r=Reserv" method="POST" novalidate>

        <input type="hidden" class="form-control" name="res_solic_id" value="<?= $_GET['i'] ?>" required>
        <input type="hidden" class="form-control res_id" name="res_id" required>
        <input type="hidden" class="form-control" name="acao" value="atualizar" required>

        <!-- Etapa 1 -->
        <div class="etapa" id="EditEtapa1">
          <div class="modal-body">

            <div id="custom-progress-bar" class="progress-nav mb-5 mt-2">
              <div class="progress" style="height: 1px;">
                <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="0">
                </div>
              </div>

              <ul class="nav nav-pills progress-bar-tab custom-nav" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link rounded-pill active" data-progressbar="custom-progress-bar" id="pills-gen-info-tab" data-bs-toggle="pill" data-bs-target="#pills-gen-info" role="tab" aria-controls="pills-gen-info" aria-selected="false" data-position="0" tabindex="-1" disabled>Local</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link rounded-pill" data-progressbar="custom-progress-bar" id="pills-info-desc-tab" data-bs-toggle="pill" data-bs-target="#pills-info-desc" role="tab" aria-controls="pills-info-desc" aria-selected="false" data-position="1" tabindex="-1" disabled>Atividade</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link rounded-pill" data-progressbar="custom-progress-bar" id="pills-success-tab" data-bs-toggle="pill" data-bs-target="#pills-success" role="tab" aria-controls="pills-success" aria-selected="true" data-position="2" disabled>Período</button>
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
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar o perfil";
                } ?>
                <label class="form-label">Campus</label>
                <select class="form-select text-uppercase res_campus" name="res_campus" id="edit_reserva_campus">
                  <?php foreach ($result as $res) : ?>
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
                <select class="form-select text-uppercase res_espaco_id_cabula" name="res_espaco_id_cabula" id="edit_reserva_local_cabula">
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['esp_id'] ?>"><?= $res['esp_codigo'] . ' - ' . $res['esp_nome_local'] ?></option>
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
                <select class="form-select text-uppercase res_espaco_id_brotas" name="res_espaco_id_brotas" id="edit_reserva_local_brotas">
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['esp_id'] ?>"><?= $res['esp_codigo'] . ' - ' . $res['esp_nome_local'] ?></option>
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
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar o perfil";
                } ?>
                <label class="form-label">Tipo de Sala</label>
                <select class="form-select text-uppercase" name="" id="edit_reserva_tipo_sala" disabled>
                  <option selected value=""></option>
                  <?php foreach ($result as $res) : ?>
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
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar o perfil";
                } ?>
                <label class="form-label">Andar</label>
                <select class="form-select text-uppercase" name="" id="edit_reserva_andar" disabled>
                  <option selected value=""></option>
                  <?php foreach ($result as $res) : ?>
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
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar o perfil";
                } ?>
                <label class="form-label">Pavilhão</label>
                <select class="form-select text-uppercase" name="" id="edit_reserva_pavilhao" disabled>
                  <option selected value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['pav_id'] ?>"><?= $res['pav_pavilhao'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-xl-3">
                <label class="form-label">Capac. Máxima</label>
                <input class="form-control" name="" id="edit_reserva_camp_maximo" oninput="this.value = this.value.replace(/[^0-9]/g, '');" disabled>
              </div>

              <div class="col-xl-3">
                <label class="form-label">Capac. Média</label>
                <input class="form-control" name="" id="edit_reserva_camp_media" oninput="this.value = this.value.replace(/[^0-9]/g, '');" disabled>
              </div>

              <div class="col-xl-3">
                <label class="form-label">Capac. Mínima</label>
                <input class="form-control" name="" id="edit_reserva_camp_minima" oninput="this.value = this.value.replace(/[^0-9]/g, '');" disabled>
              </div>

              <script>
                function toggleLocalField(value) {
                  const cabula = document.getElementById('camp_edit_reserv_local_cabula');
                  const brotas = document.getElementById('camp_edit_reserv_local_brotas');

                  // Esconde os dois campos
                  cabula.style.display = 'none';
                  brotas.style.display = 'none';

                  // Mostra conforme o valor
                  if (value === '1') {
                    cabula.style.display = 'block';
                  } else if (value === '2') {
                    brotas.style.display = 'block';
                  }
                }

                function limparCamposDetalhes() {
                  $('#edit_reserva_local_cabula').val(null).trigger('change'); // APAGA OS DADOS DE UM SELECT2
                  $('#edit_reserva_local_brotas').val(null).trigger('change'); // APAGA OS DADOS DE UM SELECT2
                  document.getElementById("edit_reserva_tipo_sala").value = '';
                  document.getElementById("edit_reserva_andar").value = '';
                  document.getElementById("edit_reserva_pavilhao").value = '';
                  document.getElementById("edit_reserva_camp_maximo").value = '';
                  document.getElementById("edit_reserva_camp_media").value = '';
                  document.getElementById("edit_reserva_camp_minima").value = '';
                }

                // Detecta troca de unidade manualmente
                document.getElementById('edit_reserva_campus').addEventListener('change', function() {
                  toggleLocalField(this.value);
                  limparCamposDetalhes(); // limpa apenas na alteração manual
                });

                // Quando o modal de edição for aberto
                function inicializarModalEdicao() {
                  const valorSelecionado = document.getElementById('edit_reserva_campus').value;
                  toggleLocalField(valorSelecionado);
                }

                // Dispara quando o modal for mostrado (Bootstrap 5)
                const modal = document.getElementById('modal_edit_espaco'); // ID do seu modal
                if (modal) {
                  modal.addEventListener('shown.bs.modal', function() {
                    inicializarModalEdicao();
                  });
                }
              </script>

              <div class="col-xl-3">
                <label class="form-label">Nº Pessoas <span>*</span></label>
                <input class="form-control text-uppercase res_quant_pessoas" name="res_quant_pessoas">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-xl-3">
                <label class="form-label">Recursos Audiovisuais <span>*</span></label>
                <select class="form-select text-uppercase res_recursos" name="res_recursos" id="edit_res_recursos" required>
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
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar o perfil";
                } ?>
                <label class="form-label">Recursos Audiovisuais Adicionais <span>*</span></label>
                <select class="form-select text-uppercase res_recursos_add" name="res_recursos_add[]" multiple id="edit_res_recursos_add">
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['rec_id'] ?>"><?= $res['rec_recurso'] ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <script>
                $(document).ready(function() {
                  const edit_res_recursos = document.getElementById("edit_res_recursos");
                  const campo_edit_res_recursos_add = document.getElementById("campo_edit_res_recursos_add");
                  const select_add = document.getElementById("edit_res_recursos_add");

                  function toggleRecursosAdicionais() {
                    if (edit_res_recursos.value === "SIM") {
                      campo_edit_res_recursos_add.style.display = "block";
                      select_add.required = true;

                      // Corrige o select2: força a atualização do container no modal
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

                  // Aplica a lógica quando o select muda
                  edit_res_recursos.addEventListener("change", toggleRecursosAdicionais);

                  // Quando o modal for aberto
                  $('#modal_edit_espaco').on('shown.bs.modal', function() {
                    toggleRecursosAdicionais();
                  });
                });
              </script>

              <div class="col-12">
                <label class="form-label">Observações</label>
                <textarea class="form-control res_obs" id="EditmeuTextarea" name="res_obs" rows="3" cols="50" maxlength="200"></textarea>
                <p class="label_info text-end mt-1">Caracteres restantes: <span id="EditContador">200</span></p>
                <script>
                  const EditmeuTextarea = document.getElementById('EditmeuTextarea');
                  const EditContador = document.getElementById('EditContador');
                  const Editlimite = 200;

                  EditmeuTextarea.addEventListener('input', function() {
                    const total = EditmeuTextarea.value.length;
                    EditContador.textContent = `${total} / ${Editlimite}`;
                  });
                </script>
              </div>

              <div class="col-lg-12">
                <div class="hstack gap-3 align-items-center justify-content-end mt-3">
                  <p class="label_asterisco m-0"><span>*</span> Campo obrigatório</p>
                  <button type="button" class="btn botao_azul_escuro btn-label right ms-auto nexttab nexttab waves-effect" id="btnEditProximo1" data-form="form_etapa1" data-next="#modal_cad_espaco2"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i> Próximo</button>
                </div>
              </div>

            </div>
          </div>
        </div>

        <!-- Etapa 2 -->
        <div class="etapa d-none" id="EditEtapa2">
          <div class="modal-body">

            <div id="custom-progress-bar" class="progress-nav mb-5 mt-2">
              <div class="progress" style="height: 1px;">
                <div class="progress-bar" role="progressbar" style="width: 50%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="0">
                </div>
              </div>

              <ul class="nav nav-pills progress-bar-tab custom-nav" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link rounded-pill done" data-progressbar="custom-progress-bar" id="pills-gen-info-tab" data-bs-toggle="pill" data-bs-target="#pills-gen-info" type="button" role="tab" aria-controls="pills-gen-info" aria-selected="false" data-position="0" tabindex="-1" disabled>Local</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link rounded-pill active" data-progressbar="custom-progress-bar" id="pills-info-desc-tab" data-bs-toggle="pill" data-bs-target="#pills-info-desc" type="button" role="tab" aria-controls="pills-info-desc" aria-selected="false" data-position="1" tabindex="-1" disabled>Atividade</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link rounded-pill" data-progressbar="custom-progress-bar" id="pills-success-tab" data-bs-toggle="pill" data-bs-target="#pills-success" type="button" role="tab" aria-controls="pills-success" aria-selected="true" data-position="2" disabled>Período</button>
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
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar o perfil";
                } ?>
                <label class="form-label">Tipo de Aula <span>*</span></label>
                <select class="form-select text-uppercase res_tipo_aula" name="res_tipo_aula">
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
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
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar os dados";
                } ?>
                <label class="form-label">Curso <span>*</span></label>
                <select class="form-select text-uppercase res_curso" name="res_curso" id="edit_res_curso">
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
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
                <select class="form-select text-uppercase res_curso_extensao" name="res_curso_extensao" id="edit_res_curso_extensao">
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
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
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar os dados";
                } ?>
                <label class="form-label">Semestre</label>
                <select class="form-select text-uppercase res_semestre" name="res_semestre">
                  <option selected value="<?= $solic_row['cs_id'] ?>"><?= $solic_row['cs_semestre'] ?></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['cs_id'] ?>"><?= $res['cs_semestre'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-xl-12" id="campo_edit_res_componente_atividade" style="display: none;">
                <label class="form-label">Componente Curricular/Atividade <span>*</span></label>
                <select class="form-select text-uppercase res_componente_atividade" name="res_componente_atividade" id="edit_res_componente_atividade">
                  <option selected disabled value=""></option>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-xl-12" id="campo_edit_res_componente_atividade_texto" style="display: none;">
                <label class="form-label">Componente Curricular/Atividade <span>*</span></label>
                <input type="text" class="form-control text-uppercase res_componente_atividade_nome" name="res_componente_atividade_nome" maxlength="200" id="edit_res_componente_atividade_nome">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-xl-12" id="campo_edit_res_nome_atividade" style="display: none;">
                <label class="form-label">Nome da Atividade <span>*</span></label>
                <input type="text" class="form-control text-uppercase res_nome_atividade" name="res_nome_atividade" maxlength="200" id="edit_res_nome_atividade">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-xl-12" id="campo_edit_res_nome_curso_texto" style="display: none;">
                <label class="form-label">Nome do curso <span>*</span></label>
                <input type="text" class="form-control text-uppercase res_curso_nome" name="res_curso_nome" id="edit_res_nome_curso" maxlength="200">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-xl-12">
                <label class="form-label">Módulo</label>
                <input type="text" class="form-control text-uppercase res_modulo" name="res_modulo" maxlength="200">
              </div>

              <div class="col-12">
                <label class="form-label">Título da Aula</label>
                <input type="text" class="form-control text-uppercase res_titulo_aula" name="res_titulo_aula" maxlength="200">
              </div>

              <div class="col-xl-12">
                <label class="form-label">Professor(es)</label>
                <input type="text" class="form-control text-uppercase res_professor" name="res_professor" maxlength="200">
              </div>

              <script>
                // Elementos
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

                // Função que atualiza os campos visíveis e define obrigatoriedade
                function atualizarCamposEdicao() {
                  const valor = edit_res_curso.value;

                  // Oculta todos os campos
                  campo_edit_res_nome_curso.style.display = "none";
                  campo_edit_res_nome_curso_texto.style.display = "none";
                  campo_edit_res_componente_atividade.style.display = "none";
                  campo_edit_res_componente_atividade_texto.style.display = "none";
                  campo_edit_res_nome_atividade.style.display = "none";

                  // Remove obrigatoriedade
                  if (select_edit_res_componente_atividade) select_edit_res_componente_atividade.required = false;
                  if (input_edit_res_componente_atividade_nome) input_edit_res_componente_atividade_nome.required = false;
                  if (select_edit_res_curso_extensao) select_edit_res_curso_extensao.required = false;
                  if (input_edit_res_nome_curso) input_edit_res_nome_curso.required = false;
                  if (input_edit_res_nome_atividade) input_edit_res_nome_atividade.required = false;

                  // Exibe conforme o valor do curso
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

                // Atualiza os campos ao mudar a seleção
                edit_res_curso.addEventListener("change", atualizarCamposEdicao);

                // Atualiza os campos ao abrir o modal
                const modalEdit = document.getElementById('modal_edit_espaco');
                modalEdit.addEventListener('shown.bs.modal', atualizarCamposEdicao);
              </script>



              <div class="col-lg-12">
                <div class="hstack gap-3 align-items-center justify-content-between mt-2">
                  <button type="button" class="btn btn-light btn-label previestab waves-effect" id="btnEditAnterior2">
                    <i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Anterior
                  </button>
                  <button type="button" class="btn botao_azul_escuro btn-label right ms-auto nexttab nexttab waves-effect" id="btnEditProximo2">
                    <i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i> Próximo
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Etapa 3 -->
        <div class="etapa d-none" id="EditEtapa3">
          <div class="modal-body">

            <div id="custom-progress-bar" class="progress-nav mb-5 mt-2">
              <div class="progress" style="height: 1px;">
                <div class="progress-bar" role="progressbar" style="width: 100%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="0">
                </div>
              </div>

              <ul class="nav nav-pills progress-bar-tab custom-nav" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link rounded-pill done" data-progressbar="custom-progress-bar" id="pills-gen-info-tab" data-bs-toggle="pill" data-bs-target="#pills-gen-info" type="button" role="tab" aria-controls="pills-gen-info" aria-selected="false" data-position="0" tabindex="-1" disabled>Local</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link rounded-pill done" data-progressbar="custom-progress-bar" id="pills-info-desc-tab" data-bs-toggle="pill" data-bs-target="#pills-info-desc" type="button" role="tab" aria-controls="pills-info-desc" aria-selected="false" data-position="1" tabindex="-1" disabled>Atividade</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link rounded-pill active" data-progressbar="custom-progress-bar" id="pills-success-tab" data-bs-toggle="pill" data-bs-target="#pills-success" type="button" role="tab" aria-controls="pills-success" aria-selected="true" data-position="2" disabled>Período</button>
                </li>
              </ul>
            </div>

            <div class="row g-3">

              <!-- <div class="col-xl-3">
                <?php try {
                  $sql = $conn->prepare("SELECT * FROM conf_tipo_reserva ORDER BY ctr_tipo_reserva");
                  $sql->execute();
                  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar o perfil";
                } ?>
                <label class="form-label">Tipo de Reserva <span>*</span></label>
                <select class="form-select text-uppercase res_tipo_reserva" name="res_tipo_reserva" id="edit_tipo_reserva" disabled>
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['ctr_id'] ?>"><?= $res['ctr_tipo_reserva'] ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div> -->

              <!-- <div class="col-6 col-lg-4 col-xl-3" id="camp_edit_reserv_dia_semana" style="display: none;">
                <?php try {
                  $sql = $conn->prepare("SELECT week_id, week_dias FROM conf_dias_semana");
                  $sql->execute();
                  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar o perfil";
                } ?>
                <label class="form-label">Dia da Semana <span>*</span></label>
                <select class="form-select text-uppercase res_dia_semana" name="res_dia_semana" id="edit_res_dia_semana">
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['week_id'] ?>"><?= $res['week_dias'] ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div> -->

              <!-- <div class="col-6 col-lg-4 col-xl-3" id="camp_edit_reserv_data_disable">
                <label class="form-label">Data da Reserva <span>*</span></label>
                <input type="date" class="form-control" disabled>
              </div> -->

              <!-- <div class="col-6 col-lg-4 col-xl-3" id="camp_edit_reserv_data" style="display: none;"> -->
              <div class="col-6 col-lg-4 col-xl-3">
                <label class="form-label">Data da Reserva <span>*</span></label>
                <input type="text" class="form-control flatpickr-input res_data" name="res_data" id="edit_data_reserva" onchange="preencherCamposEdit()">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>


              <!-- <div class="col-6 col-lg-4 col-xl-3" id="camp_edit_reserv_dia">
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
                  <input type="text" class="form-control text-uppercase" id="edit_diaSemanaId_reserva">
                  <select class="form-select text-uppercase res_dia_semana" id="edit_diaSemana_reserva" disabled>
                    <?php foreach ($result as $res) : ?>
                      <option value="<?= $res['week_id'] ?>"><?= htmlspecialchars($res['week_dias']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div> -->

              <div class="col-6 col-lg-4 col-xl-3" id="camp_edit_reserv_mes">
                <label class="form-label">Mês</label>
                <input type="text" class="form-control text-uppercase res_mes" name="res_mes" id="edit_mes_reserva" readonly>
              </div>

              <div class="col-6 col-lg-4 col-xl-3" id="camp_edit_reserv_ano">
                <label class="form-label">Ano</label>
                <input type="text" class="form-control text-uppercase res_ano" name="res_ano" id="edit_ano_reserva" readonly>
              </div>

              <div class="col-6 col-lg-4 col-xl-3">
                <label class="form-label">Hora Início <span>*</span></label>
                <input type="time" class="form-control hora res_hora_inicio" id="edit_res_hora_inicio" name="res_hora_inicio" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-6 col-lg-4 col-xl-3">
                <label class="form-label">Hora Fim <span>*</span></label>
                <input type="time" class="form-control hora res_hora_fim" id="edit_res_hora_fim" name="res_hora_fim" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <script>
                document.addEventListener('DOMContentLoaded', function() {
                  const horaInicio = document.getElementById('edit_res_hora_inicio');
                  const horaFim = document.getElementById('edit_res_hora_fim');

                  function validarEditHoras() {
                    const inicio = horaInicio.value;
                    const fim = horaFim.value;

                    // Só valida se ambos os campos estiverem preenchidos
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

                  // Você pode ajustar o tipo de evento conforme preferir
                  horaInicio.addEventListener('change', validarEditHoras);
                  horaFim.addEventListener('change', validarEditHoras);
                });
              </script>

              <script>
                document.addEventListener('DOMContentLoaded', function() {
                  const form = document.getElementById("form_reserva_edit");
                  const inputEditHoraInicio = document.getElementById("edit_res_hora_inicio");
                  const inputEditHoraFim = document.getElementById("edit_res_hora_fim");
                  const edit_data_reserva = document.getElementById("edit_data_reserva");
                  const edit_tipo_reserva = document.getElementById("edit_tipo_reserva");
                  const camp_edit_reserv_data = document.getElementById("camp_edit_reserv_data");
                  const camp_edit_reserv_dia_semana = document.getElementById("camp_edit_reserv_dia_semana");

                  [inputEditHoraInicio, inputEditHoraFim].forEach(input => {
                    flatpickr(input, {
                      allowInput: true,
                      enableTime: true,
                      noCalendar: true,
                      dateFormat: "H:i",
                      minTime: "<?= $limit_hora_inicio ?>",
                      maxTime: "<?= $limit_hora_fim ?>",
                      time_24hr: true,
                      onChange: function(selectedDates, dateStr) {
                        if (dateStr) {
                          input.classList.remove('is-invalid');
                        }
                      }
                    });

                    input.addEventListener('input', function() {
                      if (input.value.trim()) {
                        input.classList.remove('is-invalid');
                      }
                    });
                  });

                  fetch('busca_datas_bloqueadas.php')
                    .then(response => response.json())
                    .then(datasBloqueadas => {
                      const fpInstance = flatpickr("#edit_data_reserva", {
                        disable: datasBloqueadas,
                        dateFormat: "Y-m-d",
                        altInput: true,
                        altFormat: "d/m/Y",
                        locale: "pt",
                        allowInput: true,
                        onClose: function() {
                          edit_data_reserva.classList.toggle('is-invalid', !edit_data_reserva.value.trim());
                        }
                      });

                      edit_data_reserva.addEventListener('input', function() {
                        const valor = this.value.trim();
                        const valido = !!Date.parse(valor);
                        this.classList.toggle('is-invalid', !valido);

                        if (fpInstance) {
                          fpInstance.setDate(valor, true);
                        }
                      });

                      function atualizarCamposEdicao() {
                        const isTipoSemanal = edit_tipo_reserva.value === "2";

                        camp_edit_reserv_dia_semana.style.display = isTipoSemanal ? "block" : "none";
                        camp_edit_reserv_data.style.display = isTipoSemanal ? "none" : "block";

                        document.getElementById("edit_res_dia_semana").required = isTipoSemanal;
                        edit_data_reserva.required = !isTipoSemanal;

                        if (isTipoSemanal) {
                          edit_data_reserva.classList.remove('is-invalid');
                        }
                      }

                      edit_tipo_reserva.addEventListener("change", atualizarCamposEdicao);
                      document.getElementById('modal_edit_espaco').addEventListener('shown.bs.modal', atualizarCamposEdicao);

                      form.addEventListener("submit", function(event) {
                        let formValido = true;
                        const isTipoSemanal = edit_tipo_reserva.value === "2";

                        if (!isTipoSemanal) {
                          const valorData = edit_data_reserva.value.trim();
                          const dataValida = !!Date.parse(valorData);

                          if (!valorData || !dataValida) {
                            edit_data_reserva.classList.add('is-invalid');
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

              <div class="col-6 col-lg-4 col-xl-3">
                <label class="form-label">Turno</label>
                <input type="text" class="form-control text-uppercase res_turno" name="res_turno" id="edit_turno" readonly>
              </div>
              <script>
                document.getElementById('edit_res_hora_inicio').addEventListener('change', function() {
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
                  <button type="button" class="btn btn-light btn-label previestab waves-effect" id="btnEditAnterior3">
                    <i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Anterior
                  </button>
                  <button type="submit" class="btn botao botao_verde" id="btnEditConcluir">Concluir</button>
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

    // Remove erro Bootstrap ao digitar
    document.querySelectorAll('#form_reserva_edit input, #form_reserva_edit textarea, #form_reserva_edit select').forEach(el => {
      el.addEventListener('input', () => {
        if (el.checkValidity()) {
          el.classList.remove('is-invalid');
        }
      });
    });

    // Botões
    document.getElementById('btnEditProximo1').addEventListener('click', () => {
      if (validarCamposDaEtapaEdit(0)) mostrarEtapaEdit(1);
    });

    document.getElementById('btnEditAnterior2').addEventListener('click', () => mostrarEtapaEdit(0));
    document.getElementById('btnEditProximo2').addEventListener('click', () => {
      if (validarCamposDaEtapaEdit(1)) mostrarEtapaEdit(2);
    });

    document.getElementById('btnEditAnterior3').addEventListener('click', () => mostrarEtapaEdit(1));

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

    // Resetar para etapa 0 ao abrir o modal
    document.getElementById('modal_edit_espaco').addEventListener('shown.bs.modal', function() {
      mostrarEtapaEdit(0);

      // Fecha o modal de cadastro, se estiver aberto
      const modalCadastro = bootstrap.Modal.getInstance(document.getElementById('modal_cad_espaco'));
      if (modalCadastro) modalCadastro.hide();
    });
  });
</script>

<script>
  // $(document).ready(function() {
  //   // Inicializa Select2 (será reinicializado após AJAX também)
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

  //           // Reaplica Select2
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

<script>
  function preencherCamposEdit() {
    const dataEditInputEdit = document.getElementById('edit_data_reserva').value;
    if (dataEditInputEdit) {
      // Pega a data e cria um objeto Date no fuso UTC
      const partes = dataEditInputEdit.split('-');
      const ano = parseInt(partes[0], 10);
      const mes = parseInt(partes[1], 10) - 1; // Meses começam do zero
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


<script>
  const modal_edit_espaco = document.getElementById('modal_edit_espaco')
  if (modal_edit_espaco) {
    modal_edit_espaco.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget

      // Coleta os dados
      const res_curso = button.getAttribute('data-bs-res_curso');
      const res_componente_atividade = button.getAttribute('data-bs-res_componente_atividade');

      // Atualiza o campo de componentes via AJAX no momento da abertura do modal
      if (["2", "5", "6", "9", "13", "14", "17", "18", "21"].includes(res_curso)) {
        $.ajax({
          url: '../buscar_componentes.php',
          type: 'POST',
          data: {
            curso_id: res_curso
          },
          success: function(data) {
            $('#edit_res_componente_atividade')
              .html(data)
              .val(res_componente_atividade)
              .trigger('change');

            $('#edit_res_componente_atividade').select2({
              dropdownParent: $('#modal_edit_espaco'),
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
        $('#edit_res_componente_atividade').html('<option value="">Selecione um componente</option>').trigger('change');
      }

      // EXTRAI DADOS DO data-bs-* 
      const res_id = button.getAttribute('data-bs-res_id')
      const res_campus = button.getAttribute('data-bs-res_campus')
      const res_espaco_id_cabula = button.getAttribute('data-bs-res_espaco_id_cabula')
      const res_espaco_id_brotas = button.getAttribute('data-bs-res_espaco_id_brotas')
      const res_quant_pessoas = button.getAttribute('data-bs-res_quant_pessoas')
      const res_recursos = button.getAttribute('data-bs-res_recursos')
      const res_recursos_add = button.getAttribute('data-bs-res_recursos_add')
      const res_obs = button.getAttribute('data-bs-res_obs')
      const res_tipo_aula = button.getAttribute('data-bs-res_tipo_aula')
      // const res_curso = button.getAttribute('data-bs-res_curso')
      const res_curso_nome = button.getAttribute('data-bs-res_curso_nome')
      const res_curso_extensao = button.getAttribute('data-bs-res_curso_extensao')
      const res_semestre = button.getAttribute('data-bs-res_semestre')
      //const res_componente_atividade = button.getAttribute('data-bs-res_componente_atividade')
      const res_componente_atividade_nome = button.getAttribute('data-bs-res_componente_atividade_nome')
      const res_nome_atividade = button.getAttribute('data-bs-res_nome_atividade')
      const res_modulo = button.getAttribute('data-bs-res_modulo')
      const res_professor = button.getAttribute('data-bs-res_professor')
      const res_titulo_aula = button.getAttribute('data-bs-res_titulo_aula')
      //const res_tipo_reserva = button.getAttribute('data-bs-res_tipo_reserva')
      const res_data = button.getAttribute('data-bs-res_data')
      //const res_dia_semana = button.getAttribute('data-bs-res_dia_semana')
      const res_mes = button.getAttribute('data-bs-res_mes')
      const res_ano = button.getAttribute('data-bs-res_ano')
      const res_hora_inicio = button.getAttribute('data-bs-res_hora_inicio')
      const res_hora_fim = button.getAttribute('data-bs-res_hora_fim')
      const res_turno = button.getAttribute('data-bs-res_turno')
      // 
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
      //const modal_res_tipo_reserva = modal_edit_espaco.querySelector('.res_tipo_reserva')
      const modal_res_data = modal_edit_espaco.querySelector('.res_data')
      //const modal_res_dia_semana = modal_edit_espaco.querySelector('.res_dia_semana')
      const modal_res_mes = modal_edit_espaco.querySelector('.res_mes')
      const modal_res_ano = modal_edit_espaco.querySelector('.res_ano')
      const modal_res_hora_inicio = modal_edit_espaco.querySelector('.res_hora_inicio')
      const modal_res_hora_fim = modal_edit_espaco.querySelector('.res_hora_fim')
      const modal_res_turno = modal_edit_espaco.querySelector('.res_turno')
      //
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
      //modal_res_tipo_reserva.value = res_tipo_reserva
      //modal_res_data.value = res_data

      // FLATPICKR - SETA A DATA CORRETAMENTE USANDO A API
      const fp = modal_res_data._flatpickr;
      if (fp && res_data) {
        fp.setDate(res_data); // ex: "2025-05-29"
      } //

      //modal_res_dia_semana.value = res_dia_semana
      modal_res_mes.value = res_mes
      modal_res_ano.value = res_ano
      modal_res_hora_inicio.value = res_hora_inicio
      modal_res_hora_fim.value = res_hora_fim
      modal_res_turno.value = res_turno
    })
  }
</script>