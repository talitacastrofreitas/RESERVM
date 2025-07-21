<?php include 'includes/header.php'; ?>

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Reservas Confirmadas</h4>

      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="painel.php">Painel</a></li>
          <li class="breadcrumb-item active">Reservas Confirmadas</li>
        </ol>
      </div>

    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row align-items-center">
          <div class="col-sm-6 text-sm-start text-center">
            <h5 class="card-title mb-0">Lista de Reservas Confirmadas</h5>
          </div>
          <div class="col-sm-6 d-flex align-items-center d-flex justify-content-sm-end justify-content-center">
            <button class="btn botao botao_amarelo waves-effect mt-3 mt-sm-0" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_cad_espaco">+ Cadastrar Espaço</button>
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
                <th><span class="me-2">Data</span></th>
                <th><span class="me-2">Dia</span></th>
                <th><span class="me-2">Mês</span></th>
                <th><span class="me-2">Ano</span></th>
                <th><span class="me-2">Início</span></th>
                <th><span class="me-2">Fim</span></th>
                <th><span class="me-2">Turno</span></th>
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
                <th><span class="me-2">Local Reservado</span></th>
                <th><span class="me-2">ID Local</span></th>
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
                $stmt = $conn->prepare("SELECT esp_id, esp_codigo, esp_nome_local, esp_nome_local_resumido, esp_tipo_espaco, UPPER(tipesp_tipo_espaco) AS tipesp_tipo_espaco, esp_andar, esp_pavilhao, UPPER(uni_unidade) AS uni_unidade, UPPER(pav_pavilhao) AS pav_pavilhao, UPPER(and_andar) AS and_andar, esp_quant_maxima, esp_quant_media, esp_quant_minima, esp_unidade, esp_recursos, esp_status, st_status
                                      FROM espaco
                                      INNER JOIN tipo_espaco ON tipo_espaco.tipesp_id = espaco.esp_tipo_espaco
                                      INNER JOIN unidades ON unidades.uni_id = espaco.esp_unidade
                                      LEFT JOIN pavilhoes ON pavilhoes.pav_id = espaco.esp_pavilhao
                                      LEFT JOIN andares ON andares.and_id = espaco.esp_andar
                                      INNER JOIN status ON status.st_id = espaco.esp_status");
                $stmt->execute();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  extract($row);

              ?>

                  <tr>
                    <!-- <th scope="row" nowrap="nowrap" class="bg_table_fix_vermelho">20/05/2025</th>
                    <td scope="row" nowrap="nowrap" class="bg_table_fix_laranja">SEX</td>
                    <td scope="row" nowrap="nowrap" class="bg_table_fix_verde">JANEIRO</td>
                    <td scope="row" nowrap="nowrap" class="bg_table_fix_azul">2025</td>
                    <td scope="row" nowrap="nowrap" class="bg_table_fix_roxo">16:00</td>
                    <td scope="row" nowrap="nowrap" class="bg_table_fix_rosa">18:00</td>
                    <td scope="row" nowrap="nowrap" class="bg_table_fix_cinza">NOITE</td> -->
                    <th scope="row" nowrap="nowrap" class="bg_table_fix_vermelho">20/05/2025</th>
                    <td scope="row" nowrap="nowrap" class="bg_table_fix_laranja">SEX</td>
                    <td scope="row" nowrap="nowrap" class="bg_table_fix_verde">JANEIRO</td>
                    <td scope="row" nowrap="nowrap" class="bg_table_fix_azul">2025</td>
                    <td scope="row" nowrap="nowrap" class="bg_table_fix_roxo">16:00</td>
                    <td scope="row" nowrap="nowrap" class="bg_table_fix_rosa">18:00</td>
                    <td scope="row" nowrap="nowrap" class="bg_table_fix_cinza">NOITE</td>
                    <td scope="row" nowrap="nowrap">ODONTOLOGIA</td>
                    <td scope="row" nowrap="nowrap">5º</td>
                    <td scope="row" nowrap="nowrap">5º SEM - MDS334 CLÍNICA INTEGRADA I</td>
                    <td scope="row" nowrap="nowrap">PEDIATRIA</td>
                    <td scope="row" nowrap="nowrap">NORMA SUELY SOUTO SOUZA / JOSE CARLOS PETRONILO PASSOS SOUZA / MARIA LUCIA RIBEIRO ROCHA</td>
                    <td scope="row" nowrap="nowrap">2ª CHAMADA TEÓRICA E PRÁTICA</td>
                    <td scope="row" nowrap="nowrap">FIXA</td>
                    <td scope="row" nowrap="nowrap">NÃO</td>
                    <td scope="row" nowrap="nowrap">KIT DE TRANSMISSÃO</td>
                    <td scope="row" nowrap="nowrap">KIT DE TRANSMISSÃO / 1 MICROFONE COM FIO</td>
                    <td scope="row" nowrap="nowrap">60</td>
                    <td scope="row" nowrap="nowrap">C3SA101 - SALA DE AULA 101 - 1º ANDAR - PAVILHÃO III - CABULA</td>
                    <td scope="row" nowrap="nowrap">C3SA101</td>
                    <td scope="row" nowrap="nowrap">60</td>
                    <td scope="row" nowrap="nowrap">PEDRO HENRIQUE</td>
                    <td scope="row" nowrap="nowrap">13/11/2023</td>
                    <td scope="row" nowrap="nowrap">02/01/2024</td>
                    <td scope="row" nowrap="nowrap">3256</td>
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
                          <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_espaco" data-bs-esp_id="id" title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
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
    </div>
  </div>
</div>




<div class="modal modal_padrao" id="modal_cad_espaco" aria-hidden="true" aria-labelledby="modal_cad_espaco" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_espaco">Confirmar Reserva</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>

      <form id="form_etapa1" class="needs-validation" novalidate>
        <div class="modal-body">

          <div class="tit_section">
            <h3>Dados do Período</h3>
          </div>

          <div class="row g-3">

            <input type="hidden" name="acao" value="cadastrar">

            <div class="col-6 col-lg-4 col-xl-3">
              <div>
                <label class="form-label">Data da Reserva <span>*</span></label>
                <input type="date" class="form-control" name="dbloq_data" id="data" onchange="preencherCampos()" value="<?= htmlspecialchars($_SESSION['form_dbloq']['dbloq_data'] ?? '') ?>" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-6 col-lg-4 col-xl-3">
              <div>
                <label class="form-label">Mês</label>
                <input type="text" class="form-control text-uppercase dbloq_mes" name="dbloq_mes" id="mes" readonly>
              </div>
            </div>

            <div class="col-6 col-lg-4 col-xl-3">
              <div>
                <label class="form-label">Ano</label>
                <input type="text" class="form-control text-uppercase dbloq_ano" name="dbloq_ano" id="ano" readonly>
              </div>
            </div>

            <div class="col-6 col-lg-4 col-xl-3">
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
                <input type="hidden" class="form-control text-uppercase dbloq_dia_id" name="dbloq_dia" id="diaSemanaId" value="<?= htmlspecialchars($_SESSION['form_dbloq']['dbloq_dia'] ?? '') ?>">
                <select class="form-select text-uppercase dbloq_dia" id="diaSemana" disabled>
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['week_id'] ?>" <?= ($_SESSION['form_dbloq']['dbloq_dia'] ?? '') == $res['week_id'] ? 'selected' : '' ?>><?= htmlspecialchars($res['week_dias']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>


            <div class="col-6 col-lg-4 col-xl-3">
              <label class="form-label">Hora Início <span>*</span></label>
              <input type="time" class="form-control" id="hora_inicio" name="" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-6 col-lg-4 col-xl-3">
              <label class="form-label">Hora Fim <span>*</span></label>
              <input type="time" class="form-control" id="hora_fim" name="" required>
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

                <button type="button" class="btn botao botao_azul_escuro next-btn" id="btnEtapa1" data-form="form_etapa1" data-next="#modal_cad_espaco2">Próximo</button>

              </div>
            </div>

          </div>


        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal modal_padrao" id="modal_cad_espaco2" aria-hidden="true" aria-labelledby="modal_cad_espacoLabel2" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_espaco">Confirmar Reserva</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <form id="form_etapa2" novalidate>
        <div class="modal-body">

          <div class="tit_section">
            <h3>Dados do Evento</h3>
          </div>

          <div class="row g-3">

            <div class="col-xl-3">
              <label class="form-label">Curso <span>*</span></label>
              <input class="form-control text-uppercase" name="" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-xl-3">
              <label class="form-label">Semestre <span>*</span></label>
              <input class="form-control text-uppercase" name="" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-xl-6">
              <label class="form-label">Componente Curricular/Atividade <span>*</span></label>
              <input class="form-control text-uppercase" name="" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-xl-6">
              <label class="form-label">Módulo <span>*</span></label>
              <input class="form-control text-uppercase" name="" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-xl-6">
              <label class="form-label">Título da Aula <span>*</span></label>
              <input class="form-control text-uppercase" name="" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-xl-12">
              <label class="form-label">Professor(es) <span>*</span></label>
              <input class="form-control text-uppercase" name="" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-xl-3">
              <label class="form-label">Tipo Reserva <span>*</span></label>
              <input class="form-control text-uppercase" name="" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-xl-3">
              <label class="form-label">Recursos <span>*</span></label>
              <input class="form-control text-uppercase" name="" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-xl-3">
              <label class="form-label">Recursos Adicionais <span>*</span></label>
              <input class="form-control text-uppercase" name="" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-xl-3">
              <label class="form-label">Nº Pessoas <span>*</span></label>
              <input class="form-control text-uppercase" name="" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-between mt-2">
                <button type="button" class="btn botao botao_azul_escuro waves-effect" data-bs-target="#modal_cad_espaco" data-bs-toggle="modal">Voltar</button>
                <!-- <button type="submit" class="btn botao botao_azul_escuro waves-effect" data-bs-target="#modal_cad_espaco3" data-bs-toggle="modal">Próximo</button> -->

                <button type="button" class="btn botao botao_azul_escuro next-btn" id="btnEtapa2" data-form="form_etapa2" data-next="#modal_cad_espaco3">Próximo</button>

              </div>
            </div>

          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal modal_padrao" id="modal_cad_espaco3" aria-hidden="true" aria-labelledby="modal_cad_espacoLabel3" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_espaco2">Confirmar Reserva</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <form id="form_etapa3" novalidate>
        <div class="modal-body">

          <div class="tit_section">
            <h3>Dados do Local</h3>
          </div>

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
              <select class="form-select text-uppercase" name="esp_unidade" required>
                <option selected disabled value=""></option>
                <?php foreach ($result as $res) : ?>
                  <option value="<?= $res['uni_id'] ?>"><?= $res['uni_unidade'] ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-xl-7">
              <label class="form-label">Local <span>*</span></label>
              <input class="form-control text-uppercase" name="" required>
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
              <select class="form-select text-uppercase" name="esp_andar">
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
              <select class="form-select text-uppercase" name="esp_andar">
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
              <select class="form-select text-uppercase" name="esp_pavilhao">
                <option selected value=""></option>
                <?php foreach ($result as $res) : ?>
                  <option value="<?= $res['pav_id'] ?>"><?= $res['pav_pavilhao'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-xl-3">
              <label class="form-label">Capacidade <span>*</span></label>
              <input class="form-control text-uppercase" name="" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-xl-3">
              <label class="form-label">Capac. Máxima</label>
              <input class="form-control" name="esp_quant_maxima" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
            </div>

            <div class="col-xl-3">
              <label class="form-label">Capac. Média</label>
              <input class="form-control" name="esp_quant_media" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
            </div>

            <div class="col-xl-3">
              <label class="form-label">Capac. Mínima</label>
              <input class="form-control" name="esp_quant_minima" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
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
                <button type="button" class="btn botao botao_azul_escuro waves-effect" data-bs-target="#modal_cad_espaco2" data-bs-toggle="modal">Voltar</button>
                <!-- <button type="submit" class="btn botao botao_verde waves-effect" data-bs-target="#modal_cad_espaco3" data-bs-toggle="modal">Concluir</button> -->

                <button type="button" class="btn botao botao_azul_escuro next-btn" id="btnEtapa3" data-form="form_etapa3" data-next="#modal_cad_espaco3">Concluir</button>

              </div>
            </div>

          </div>
        </div>
      </form>
    </div>
  </div>
</div>


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
</script>


<script>
  function preencherCampos() {
    const dataInput = document.getElementById('data').value;
    if (dataInput) {
      // Pega a data e cria um objeto Date no fuso UTC
      const partes = dataInput.split('-');
      const ano = parseInt(partes[0], 10);
      const mes = parseInt(partes[1], 10) - 1; // Meses começam do zero
      const dia = parseInt(partes[2], 10);
      const data = new Date(Date.UTC(ano, mes, dia));

      const meses = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
      const diasSemana = ["7", "1", "2", "3", "4", "5", "6"];

      document.getElementById('mes').value = meses[data.getUTCMonth()];
      document.getElementById('ano').value = data.getUTCFullYear();
      document.getElementById('diaSemana').value = diasSemana[data.getUTCDay()];
      document.getElementById('diaSemanaId').value = diasSemana[data.getUTCDay()];
    }
  }
</script>


<script>
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
      // Juntar dados de todos os forms
      const formData = new FormData();
      [form1, form2, form3].forEach(form => {
        new FormData(form).forEach((value, key) => {
          formData.append(key, value);
        });
      });

      // Enviar com fetch ou AJAX
      fetch('controller/controller_reservas.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.text())
        .then(result => {
          //alert('Formulário enviado com sucesso!');
          console.log(result);
        })
        .catch(error => {
          //alert('Erro ao enviar formulário.');
          console.error(error);
        });
    }
  });
</script>


<!-- ITENS DOS SELECTS -->
<script src="../assets/js/351.jquery.min.js"></script>
<script src="includes/select/select_colaboradores.js"></script>
<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>
<!-- COMPLETO FORM -->
<script src="includes/select/completa_form.js"></script>
<!-- SELECT2 FORM -->
<script src="includes/select/select2.js"></script>