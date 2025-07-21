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

      <form id="form_reserva" class="needs-validation" action="controller/controller_reservas.php" method="POST" novalidate>

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
                  $sql = $conn->prepare("SELECT * FROM unidades");
                  $sql->execute();
                  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar o perfil";
                } ?>
                <label class="form-label">Campus</label>
                <select class="form-select text-uppercase" name="res_campus" id="cad_reserva_campus">
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['uni_id'] ?>"><?= $res['uni_unidade'] ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-xl-9" id="camp_reserv_campus">
                <div class="">
                  <label class="form-label">Local <span>*</span></label>
                  <select class="form-select text-uppercase" disabled></select>
                </div>
              </div>

              <div class="col-xl-9" id="camp_reserv_local_cabula" style="display: none;">
                <?php try {
                  $sql = $conn->prepare("SELECT esp_id, esp_codigo, esp_nome_local FROM espaco WHERE esp_unidade = 1 ORDER BY esp_nome_local ASC");
                  $sql->execute();
                  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                  echo "Erro ao tentar recuperar os dados";
                } ?>
                <div class="">
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

              <div class="col-xl-9" id="camp_reserv_local_brotas" style="display: none;">
                <?php try {
                  $sql = $conn->prepare("SELECT esp_id, esp_codigo, esp_nome_local FROM espaco WHERE esp_unidade = 2 ORDER BY esp_nome_local ASC");
                  $sql->execute();
                  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                  echo "Erro ao tentar recuperar os dados";
                } ?>
                <div class="">
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
                    camp_reserv_local_brotas.style.display = "none";
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
                    camp_reserv_local_brotas.style.display = "block";
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
                <select class="form-select text-uppercase" name="" id="cad_reserva_tipo_sala" disabled>
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
                <select class="form-select text-uppercase" name="" id="cad_reserva_andar" disabled>
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
                <select class="form-select text-uppercase" name="" id="cad_reserva_pavilhao" disabled>
                  <option selected value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['pav_id'] ?>"><?= $res['pav_pavilhao'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-xl-3">
                <label class="form-label">Capac. Máxima</label>
                <input class="form-control" name="" id="esp_quant_maxima" oninput="this.value = this.value.replace(/[^0-9]/g, '');" disabled>
              </div>

              <div class="col-xl-2">
                <label class="form-label">Capac. Média</label>
                <input class="form-control" name="" id="cad_reserva_camp_media" oninput="this.value = this.value.replace(/[^0-9]/g, '');" disabled>
              </div>

              <div class="col-xl-2">
                <label class="form-label">Capac. Mínima</label>
                <input class="form-control" name="" id="esp_quant_minima" oninput="this.value = this.value.replace(/[^0-9]/g, '');" disabled>
              </div>

              <div class="col-xl-2">
                <label class="form-label">Nº Pessoas <span>*</span></label>
                <input class="form-control text-uppercase" name="res_quant_pessoas">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-xl-3">
                <label class="form-label">Recursos Audiovisuais</label>
                <select class="form-select text-uppercase" name="res_recursos" id="">
                  <option selected value=""></option>
                  <option value="1">SIM</option>
                  <option value="2">NÃO</option>
                </select>
              </div>

              <div class="col-xl-3">
                <?php try {
                  $sql = $conn->prepare("SELECT rec_id, rec_recurso FROM recursos ORDER BY rec_recurso");
                  $sql->execute();
                  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar o perfil";
                } ?>
                <label class="form-label">Recursos Audiovisuais Adicionais </label>
                <select class="form-select text-uppercase" name="res_recursos_add" id="">
                  <option selected value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['rec_id'] ?>"><?= $res['rec_recurso'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-12">
                <label class="form-label">Observações <span>*</span></label>
                <input class="form-control text-uppercase" name="res_obs">
                <div class="invalid-feedback">Este campo é obrigatório</div>
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
                  $sql = $conn->prepare("SELECT cta_id, cta_tipo_atividade FROM conf_tipo_atividade ORDER BY cta_id ASC");
                  $sql->execute();
                  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                  echo "Erro ao tentar recuperar os dados";
                } ?>
                <label class="form-label">Tipo de Atividade <span>*</span></label>
                <select class="form-select text-uppercase" id="cad_res_tipo_ativ" disabled>
                  <option selected value="<?= $solic_row['cta_id'] ?>"><?= $solic_row['cta_tipo_atividade'] ?></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['cta_id'] ?>"><?= $res['cta_tipo_atividade'] ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

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
                <select class="form-select text-uppercase" name="res_tipo_aula">
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
                <select class="form-select text-uppercase" name="res_curso_extensao" id="">
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
                <select class="form-select text-uppercase" name="res_semestre">
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
                <input type="text" class="form-control text-uppercase" name="res_componente_atividade_nome" value="<?= $solic_row['solic_nome_comp_ativ'] ?>" maxlength="200">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-xl-12" id="campo_res_nome_atividade" style="display: none;">
                <label class="form-label">Nome da Atividade <span>*</span></label>
                <input type="text" class="form-control text-uppercase" name="res_nome_atividade" value="<?= $solic_row['solic_nome_atividade'] ?>" maxlength="200">
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
                const cad_res_curso = document.getElementById("cad_res_curso");
                //
                const campo_res_componente_atividade = document.getElementById("campo_res_componente_atividade");
                const campo_res_componente_atividade_texto = document.getElementById("campo_res_componente_atividade_texto");
                const campo_res_nome_curso = document.getElementById("campo_res_nome_curso");
                const campo_res_nome_curso_texto = document.getElementById("campo_res_nome_curso_texto");
                const campo_res_nome_atividade = document.getElementById("campo_res_nome_atividade");

                cad_res_curso.addEventListener("change", function() {
                  if (["2", "5", "6", "9", "13", "14", "17", "18", "21"].includes(cad_res_curso.value)) {
                    campo_res_componente_atividade.style.display = "block";
                    //
                    campo_res_nome_curso.style.display = "none";
                    campo_res_componente_atividade_texto.style.display = "none";
                    campo_res_nome_atividade.style.display = "none";
                    campo_res_nome_curso_texto.style.display = "none";
                  } else if (["7", "8", "10", "19", "28"].includes(cad_res_curso.value)) {
                    campo_res_nome_atividade.style.display = "block";
                    //
                    campo_res_nome_curso.style.display = "none";
                    campo_res_componente_atividade.style.display = "none";
                    campo_res_componente_atividade_texto.style.display = "none";
                    campo_res_nome_curso_texto.style.display = "none";
                  } else if (["8"].includes(cad_res_curso.value)) {
                    campo_res_nome_curso.style.display = "block";
                    //
                    campo_res_componente_atividade.style.display = "none";
                    campo_res_componente_atividade_texto.style.display = "none";
                    campo_res_nome_atividade.style.display = "none";
                    campo_res_nome_curso_texto.style.display = "none";
                  } else if (["11", "22"].includes(cad_res_curso.value)) {
                    campo_res_componente_atividade_texto.style.display = "block";
                    campo_res_nome_curso_texto.style.display = "block";
                    //
                    campo_res_nome_curso.style.display = "none";
                    campo_res_componente_atividade.style.display = "none";
                    campo_res_nome_atividade.style.display = "none";
                  } else {
                    campo_res_nome_curso.style.display = "none";
                    campo_res_componente_atividade.style.display = "none";
                    campo_res_componente_atividade_texto.style.display = "none";
                    campo_res_nome_atividade.style.display = "none";
                    campo_res_nome_curso_texto.style.display = "none";
                  }
                });

                if (["2", "5", "6", "9", "13", "14", "17", "18", "21"].includes(cad_res_curso.value)) {
                  campo_res_componente_atividade.style.display = "block";
                  //
                  campo_res_nome_curso.style.display = "none";
                  campo_res_componente_atividade_texto.style.display = "none";
                  campo_res_nome_atividade.style.display = "none";
                  campo_res_nome_curso_texto.style.display = "none";
                } else if (["7", "10", "19", "28"].includes(cad_res_curso.value)) {
                  campo_res_nome_atividade.style.display = "block";
                  //
                  campo_res_nome_curso.style.display = "none";
                  campo_res_componente_atividade.style.display = "none";
                  campo_res_componente_atividade_texto.style.display = "none";
                  campo_res_nome_curso_texto.style.display = "none";
                } else if (["8"].includes(cad_res_curso.value)) {
                  campo_res_nome_curso.style.display = "block";
                  //
                  campo_res_componente_atividade.style.display = "none";
                  campo_res_componente_atividade_texto.style.display = "none";
                  campo_res_nome_atividade.style.display = "none";
                  campo_res_nome_curso_texto.style.display = "none";
                } else if (["11", "22"].includes(cad_res_curso.value)) {
                  campo_res_componente_atividade_texto.style.display = "block";
                  campo_res_nome_curso_texto.style.display = "block";
                  //
                  campo_res_nome_curso.style.display = "none";
                  campo_res_componente_atividade.style.display = "none";
                  campo_res_nome_atividade.style.display = "none";
                } else {
                  campo_res_nome_curso.style.display = "none";
                  campo_res_componente_atividade.style.display = "none";
                  campo_res_componente_atividade_texto.style.display = "none";
                  campo_res_nome_atividade.style.display = "none";
                  campo_res_nome_curso_texto.style.display = "none";
                }
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
                <select class="form-select text-uppercase" name="res_tipo_reserva" id="cad_tipo_reserva">
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
                <label class="form-label">Data da Reserva <span>*</span></label>
                <input type="date" class="form-control" disabled>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-6 col-lg-4 col-xl-3" id="camp_reserv_data" style="display: none;">
                <label class="form-label">Data da Reserva <span>*</span></label>
                <input type="date" class="form-control" name="res_data" id="cad_data_reserva" onchange="preencherCampos()">
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
                  <select class="form-select text-uppercase" id="cad_diaSemana_reserva" disabled>
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
              <script>
                const cad_tipo_reserva = document.getElementById("cad_tipo_reserva");
                const camp_reserv_dia_semana = document.getElementById("camp_reserv_dia_semana");
                const camp_reserv_data_disable = document.getElementById("camp_reserv_data_disable");
                const camp_reserv_data = document.getElementById("camp_reserv_data");
                const camp_reserv_mes = document.getElementById("camp_reserv_mes");
                const camp_reserv_ano = document.getElementById("camp_reserv_ano");
                const camp_reserv_dia = document.getElementById("camp_reserv_dia");

                cad_tipo_reserva.addEventListener("change", function() {
                  if (cad_tipo_reserva.value === "2") {
                    camp_reserv_dia_semana.style.display = "block";
                    camp_reserv_data_disable.style.display = "none";
                    camp_reserv_data.style.display = "none";
                    camp_reserv_mes.style.display = "none";
                    camp_reserv_ano.style.display = "none";
                    camp_reserv_dia.style.display = "none";
                    document.getElementById("cad_res_dia_semana").required = true;
                    document.getElementById("cad_data_reserva").required = false;
                  } else {
                    camp_reserv_dia_semana.style.display = "none";
                    camp_reserv_data_disable.style.display = "none";
                    camp_reserv_data.style.display = "block";
                    camp_reserv_mes.style.display = "block";
                    camp_reserv_ano.style.display = "block";
                    camp_reserv_dia.style.display = "block";
                    document.getElementById("cad_res_dia_semana").required = false;
                    document.getElementById("cad_data_reserva").required = true;
                  }
                });
              </script>

              <div class="col-6 col-lg-4 col-xl-3">
                <label class="form-label">Hora Início <span>*</span></label>
                <input type="time" class="form-control" id="cad_res_hora_inicio" name="res_hora_inicio">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-6 col-lg-4 col-xl-3">
                <label class="form-label">Hora Fim <span>*</span></label>
                <input type="time" class="form-control" id="cad_res_hora_fim" name="res_hora_fim">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

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
</div>


<!-- EDITAR -->
<div class="modal fade modal_padrao" id="modal_edit_espaco" aria-hidden="true" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title">Confirmar Reserva</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <form id="form_reserva_edit" class="needs-validation" action="controller/controller_reservas.php" method="POST" novalidate>

        <input type="text" class="form-control" name="res_solic_id" value="<?= $_GET['i'] ?>" required>
        <input type="text" class="form-control res_id" name="res_id" required>
        <input type="text" class="form-control" name="acao" value="atualizar" required>

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

              <div class="col-xl-2">
                <label class="form-label">Capac. Média</label>
                <input class="form-control" name="" id="edit_reserva_camp_media" oninput="this.value = this.value.replace(/[^0-9]/g, '');" disabled>
              </div>

              <div class="col-xl-2">
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


              <div class="col-xl-2">
                <label class="form-label">Nº Pessoas <span>*</span></label>
                <input class="form-control text-uppercase res_quant_pessoas" name="res_quant_pessoas">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-xl-3">
                <label class="form-label">Recursos Audiovisuais</label>
                <select class="form-select text-uppercase res_recursos" name="res_recursos">
                  <option selected value=""></option>
                  <option value="1">SIM</option>
                  <option value="2">NÃO</option>
                </select>
              </div>

              <div class="col-xl-3">
                <?php try {
                  $sql = $conn->prepare("SELECT rec_id, rec_recurso FROM recursos ORDER BY rec_recurso");
                  $sql->execute();
                  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar o perfil";
                } ?>
                <label class="form-label">Recursos Audiovisuais Adicionais </label>
                <select class="form-select text-uppercase res_recursos_add" name="res_recursos_add">
                  <option selected value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['rec_id'] ?>"><?= $res['rec_recurso'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-12">
                <label class="form-label">Observações <span>*</span></label>
                <input class="form-control text-uppercase res_obs" name="res_obs">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-lg-12">
                <div class="hstack gap-3 align-items-center justify-content-end mt-3">
                  <p class="label_asterisco m-0"><span>*</span> Campo obrigatório</p>
                  <button type="button" class="btn botao_azul_escuro btn-label right ms-auto nexttab nexttab waves-effect" id="btnEditProximo1" data-form="form_etapa1" data-next="#modal_edit_espaco"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i> Próximo</button>
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
                  $sql = $conn->prepare("SELECT cta_id, cta_tipo_atividade FROM conf_tipo_atividade ORDER BY cta_id ASC");
                  $sql->execute();
                  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                  echo "Erro ao tentar recuperar os dados";
                } ?>
                <label class="form-label">Tipo de Atividade <span>*</span></label>
                <select class="form-select text-uppercase" id="edit_res_tipo_ativ" disabled>
                  <option selected value="<?= $solic_row['cta_id'] ?>"><?= $solic_row['cta_tipo_atividade'] ?></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['cta_id'] ?>"><?= $res['cta_tipo_atividade'] ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

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
                <select class="form-select text-uppercase" name="res_tipo_aula">
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
                <select class="form-select text-uppercase" name="res_curso" id="edit_res_curso">
                  <option selected value="<?= $solic_row['curs_id'] ?>"><?= $solic_row['curs_curso'] ?></option>
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
                <select class="form-select text-uppercase" name="res_curso_extensao" id="">
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
                <select class="form-select text-uppercase" name="res_semestre">
                  <option selected value="<?= $solic_row['cs_id'] ?>"><?= $solic_row['cs_semestre'] ?></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['cs_id'] ?>"><?= $res['cs_semestre'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-xl-12" id="campo_edit_res_componente_atividade" style="display: none;">
                <label class="form-label">Componente Curricular/Atividade <span>*</span></label>
                <select class="form-select text-uppercase" name="res_componente_atividade" id="edit_res_componente_atividade">
                  <option selected value="<?= $solic_row['compc_id'] ?>"><?= $solic_row['compc_componente'] ?></option>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-xl-12" id="campo_edit_res_componente_atividade_texto" style="display: none;">
                <label class="form-label">Componente Curricular/Atividade <span>*</span></label>
                <input type="text" class="form-control text-uppercase" name="res_componente_atividade_nome" value="<?= $solic_row['solic_nome_comp_ativ'] ?>" maxlength="200">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-xl-12" id="campo_edit_res_nome_atividade" style="display: none;">
                <label class="form-label">Nome da Atividade <span>*</span></label>
                <input type="text" class="form-control text-uppercase" name="res_nome_atividade" value="<?= $solic_row['solic_nome_atividade'] ?>" maxlength="200">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-xl-12" id="campo_edit_res_nome_curso_texto" style="display: none;">
                <label class="form-label">Nome do curso <span>*</span></label>
                <input type="text" class="form-control text-uppercase" name="res_curso_nome" id="edit_res_nome_curso" value="<?= $solic_row['solic_nome_curso_text'] ?>" maxlength="200">
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
                  <button type="button" class="btn btn-light btn-label previestab waves-effect" id="btnEditAnterior2"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Anterior</button>
                  <button type="button" class="btn botao_azul_escuro btn-label right ms-auto nexttab nexttab waves-effect" id="btnEditProximo2" data-form="form_etapa2" data-next="#modal_edit_espaco"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i> Próximo</button>
                </div>
              </div>

              <script>
                const edit_res_curso = document.getElementById("edit_res_curso");
                //
                const campo_edit_res_componente_atividade = document.getElementById("campo_edit_res_componente_atividade");
                const campo_edit_res_componente_atividade_texto = document.getElementById("campo_edit_res_componente_atividade_texto");
                const campo_edit_res_nome_curso = document.getElementById("campo_edit_res_nome_curso");
                const campo_edit_res_nome_curso_texto = document.getElementById("campo_edit_res_nome_curso_texto");
                const campo_edit_res_nome_atividade = document.getElementById("campo_edit_res_nome_atividade");

                edit_res_curso.addEventListener("change", function() {
                  if (["2", "5", "6", "9", "13", "14", "17", "18", "21"].includes(edit_res_curso.value)) {
                    campo_edit_res_componente_atividade.style.display = "block";
                    //
                    campo_edit_res_nome_curso.style.display = "none";
                    campo_edit_res_componente_atividade_texto.style.display = "none";
                    campo_edit_res_nome_atividade.style.display = "none";
                    campo_edit_res_nome_curso_texto.style.display = "none";
                  } else if (["7", "8", "10", "19", "28"].includes(edit_res_curso.value)) {
                    campo_edit_res_nome_atividade.style.display = "block";
                    //
                    campo_edit_res_nome_curso.style.display = "none";
                    campo_edit_res_componente_atividade.style.display = "none";
                    campo_edit_res_componente_atividade_texto.style.display = "none";
                    campo_edit_res_nome_curso_texto.style.display = "none";
                  } else if (["8"].includes(edit_res_curso.value)) {
                    campo_edit_res_nome_curso.style.display = "block";
                    //
                    campo_edit_res_componente_atividade.style.display = "none";
                    campo_edit_res_componente_atividade_texto.style.display = "none";
                    campo_edit_res_nome_atividade.style.display = "none";
                    campo_edit_res_nome_curso_texto.style.display = "none";
                  } else if (["11", "22"].includes(edit_res_curso.value)) {
                    campo_edit_res_componente_atividade_texto.style.display = "block";
                    campo_edit_res_nome_curso_texto.style.display = "block";
                    //
                    campo_edit_res_nome_curso.style.display = "none";
                    campo_edit_res_componente_atividade.style.display = "none";
                    campo_edit_res_nome_atividade.style.display = "none";
                  } else {
                    campo_edit_res_nome_curso.style.display = "none";
                    campo_edit_res_componente_atividade.style.display = "none";
                    campo_edit_res_componente_atividade_texto.style.display = "none";
                    campo_edit_res_nome_atividade.style.display = "none";
                    campo_edit_res_nome_curso_texto.style.display = "none";
                  }
                });

                if (["2", "5", "6", "9", "13", "14", "17", "18", "21"].includes(edit_res_curso.value)) {
                  campo_edit_res_componente_atividade.style.display = "block";
                  //
                  campo_edit_res_nome_curso.style.display = "none";
                  campo_edit_res_componente_atividade_texto.style.display = "none";
                  campo_edit_res_nome_atividade.style.display = "none";
                  campo_edit_res_nome_curso_texto.style.display = "none";
                } else if (["7", "10", "19", "28"].includes(edit_res_curso.value)) {
                  campo_edit_res_nome_atividade.style.display = "block";
                  //
                  campo_edit_res_nome_curso.style.display = "none";
                  campo_edit_res_componente_atividade.style.display = "none";
                  campo_edit_res_componente_atividade_texto.style.display = "none";
                  campo_edit_res_nome_curso_texto.style.display = "none";
                } else if (["8"].includes(edit_res_curso.value)) {
                  campo_edit_res_nome_curso.style.display = "block";
                  //
                  campo_edit_res_componente_atividade.style.display = "none";
                  campo_edit_res_componente_atividade_texto.style.display = "none";
                  campo_edit_res_nome_atividade.style.display = "none";
                  campo_edit_res_nome_curso_texto.style.display = "none";
                } else if (["11", "22"].includes(edit_res_curso.value)) {
                  campo_edit_res_componente_atividade_texto.style.display = "block";
                  campo_edit_res_nome_curso_texto.style.display = "block";
                  //
                  campo_edit_res_nome_curso.style.display = "none";
                  campo_edit_res_componente_atividade.style.display = "none";
                  campo_edit_res_nome_atividade.style.display = "none";
                } else {
                  campo_edit_res_nome_curso.style.display = "none";
                  campo_edit_res_componente_atividade.style.display = "none";
                  campo_edit_res_componente_atividade_texto.style.display = "none";
                  campo_edit_res_nome_atividade.style.display = "none";
                  campo_edit_res_nome_curso_texto.style.display = "none";
                }
              </script>

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
                <select class="form-select text-uppercase" name="res_tipo_reserva" id="edit_tipo_reserva">
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['ctr_id'] ?>"><?= $res['ctr_tipo_reserva'] ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-6 col-lg-4 col-xl-3" id="camp_edit_reserv_dia_semana" style="display: none;">
                <?php try {
                  $sql = $conn->prepare("SELECT week_id, week_dias FROM conf_dias_semana");
                  $sql->execute();
                  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar o perfil";
                } ?>
                <label class="form-label">Dia da Semana <span>*</span></label>
                <select class="form-select text-uppercase" name="res_dia_semana" id="edit_res_dia_semana">
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['week_id'] ?>"><?= $res['week_dias'] ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-6 col-lg-4 col-xl-3" id="camp_edit_reserv_data_disable">
                <label class="form-label">Data da Reserva <span>*</span></label>
                <input type="date" class="form-control" disabled>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-6 col-lg-4 col-xl-3" id="camp_edit_reserv_data" style="display: none;">
                <label class="form-label">Data da Reserva <span>*</span></label>
                <input type="date" class="form-control" name="res_data" id="edit_data_reserva" onchange="preencherCampos()">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-6 col-lg-4 col-xl-3" id="camp_edit_reserv_dia">
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
                  <input type="hidden" class="form-control text-uppercase" id="edit_diaSemanaId_reserva">
                  <select class="form-select text-uppercase" id="edit_diaSemana_reserva" disabled>
                    <option selected disabled value=""></option>
                    <?php foreach ($result as $res) : ?>
                      <option value="<?= $res['week_id'] ?>"><?= htmlspecialchars($res['week_dias']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>

              <div class="col-6 col-lg-4 col-xl-3" id="camp_edit_reserv_mes">
                <label class="form-label">Mês</label>
                <input type="text" class="form-control text-uppercase" name="res_mes" id="edit_mes_reserva" readonly>
              </div>

              <div class="col-6 col-lg-4 col-xl-3" id="camp_edit_reserv_ano">
                <label class="form-label">Ano</label>
                <input type="text" class="form-control text-uppercase" name="res_ano" id="edit_ano_reserva" readonly>
              </div>
              <script>
                const edit_tipo_reserva = document.getElementById("edit_tipo_reserva");
                const camp_edit_reserv_dia_semana = document.getElementById("camp_edit_reserv_dia_semana");
                const camp_edit_reserv_data_disable = document.getElementById("camp_edit_reserv_data_disable");
                const camp_edit_reserv_data = document.getElementById("camp_edit_reserv_data");
                const camp_edit_reserv_mes = document.getElementById("camp_edit_reserv_mes");
                const camp_edit_reserv_ano = document.getElementById("camp_edit_reserv_ano");
                const camp_edit_reserv_dia = document.getElementById("camp_edit_reserv_dia");

                edit_tipo_reserva.addEventListener("change", function() {
                  if (edit_tipo_reserva.value === "2") {
                    camp_edit_reserv_dia_semana.style.display = "block";
                    camp_edit_reserv_data_disable.style.display = "none";
                    camp_edit_reserv_data.style.display = "none";
                    camp_edit_reserv_mes.style.display = "none";
                    camp_edit_reserv_ano.style.display = "none";
                    camp_edit_reserv_dia.style.display = "none";
                    document.getElementById("edit_res_dia_semana").required = true;
                    document.getElementById("edit_data_reserva").required = false;
                  } else {
                    camp_edit_reserv_dia_semana.style.display = "none";
                    camp_edit_reserv_data_disable.style.display = "none";
                    camp_edit_reserv_data.style.display = "block";
                    camp_edit_reserv_mes.style.display = "block";
                    camp_edit_reserv_ano.style.display = "block";
                    camp_edit_reserv_dia.style.display = "block";
                    document.getElementById("edit_res_dia_semana").required = false;
                    document.getElementById("edit_data_reserva").required = true;
                  }
                });
              </script>

              <div class="col-6 col-lg-4 col-xl-3">
                <label class="form-label">Hora Início <span>*</span></label>
                <input type="time" class="form-control" id="edit_res_hora_inicio" name="res_hora_inicio">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-6 col-lg-4 col-xl-3">
                <label class="form-label">Hora Fim <span>*</span></label>
                <input type="time" class="form-control" id="edit_res_hora_fim" name="res_hora_fim">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <div class="col-6 col-lg-4 col-xl-3">
                <label class="form-label">Turno</label>
                <input type="text" class="form-control text-uppercase" name="res_turno" id="turno" readonly>
              </div>
              <script>
                document.getElementById('edit_res_hora_inicio').addEventListener('change', function() {
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
                  <button type="button" class="btn btn-light btn-label previestab waves-effect" id="btnEditAnterior3"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Anterior</button>
                  <button type="submit" id="btnEditConcluir" class="btn botao botao_verde next-btn">Concluir</button>
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
  const modal_edit_espaco = document.getElementById('modal_edit_espaco')
  if (modal_edit_espaco) {
    modal_edit_espaco.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget
      // EXTRAI DADOS DO data-bs-* 
      const res_id = button.getAttribute('data-bs-res_id')
      const res_campus = button.getAttribute('data-bs-res_campus')
      const res_espaco_id_cabula = button.getAttribute('data-bs-res_espaco_id_cabula')
      const res_espaco_id_brotas = button.getAttribute('data-bs-res_espaco_id_brotas')
      const res_quant_pessoas = button.getAttribute('data-bs-res_quant_pessoas')
      const res_recursos = button.getAttribute('data-bs-res_recursos')
      const res_recursos_add = button.getAttribute('data-bs-res_recursos_add')
      const res_obs = button.getAttribute('data-bs-res_obs')
      // const admin_status = button.getAttribute('data-bs-admin_status')
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
      // const modal_admin_status = modal_edit_espaco.querySelector('.admin_status')
      //
      modalTitle.textContent = 'Atualizar Dados'
      modal_res_id.value = res_id
      modal_res_campus.value = res_campus
      $('#edit_reserva_local_cabula').val(res_espaco_id_cabula).trigger('change');
      $('#edit_reserva_local_brotas').val(res_espaco_id_brotas).trigger('change');
      modal_res_quant_pessoas.value = res_quant_pessoas
      modal_res_recursos.value = res_recursos
      modal_res_recursos_add.value = res_recursos_add
      modal_res_obs.value = res_obs
      // // VERIFICA DE O CHECKBOX ESTÁ MARCADO
      // if (admin_status === '1') {
      //   modal_admin_status.checked = true;
      // } else {
      //   modal_admin_status.checked = false;
      // }
    })
  }
</script>