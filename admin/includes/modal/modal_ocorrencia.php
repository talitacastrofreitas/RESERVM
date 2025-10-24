<!-- CADASTRAR -->
<div class="modal fade modal_padrao" id="modal_cad_ocorrencia" tabindex="-1" aria-labelledby="modal_cad_ocorrencia"
  aria-modal="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_ocorrencia">Cadastrar Ocorrência</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i
            class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../router/web.php?r=Ocorrenc" class="needs-validation" novalidate>

          <div class="row g-3">

            <input type="hidden" class="form-control" name="solic_id" value="<?= $_GET['i'] ?>" required>
            <input type="hidden" class="form-control" name="acao" value="cadastrar" required>

            <div class="col-lg-12">

              <?php try {
                $sql = $conn->prepare("SELECT res_id, res_codigo, res_data, res_hora_inicio, res_hora_fim FROM reservas
                                      INNER JOIN solicitacao ON solicitacao.solic_id = reservas.res_solic_id
                                      WHERE solic_id = :solic_id");
                $sql->execute([':solic_id' => $_GET['i']]);
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <label class="form-label">Código/Data da Aula <span>*</span></label>
              <select class="form-select res_id" name="res_codigo" id="cad_oco_res_codigo" required>
                <option selected value=""></option>
                <?php foreach ($result as $res): ?>
                  <option value="<?= $res['res_id'] ?>">
                    <?= $res['res_codigo'] . ': ' . date('d/m/Y', strtotime($res['res_data'])) . ' (' . date('H:i', strtotime($res['res_hora_inicio'])) . ' - ' . date('H:i', strtotime($res['res_hora_fim'])) . ')' ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <input type="hidden" class="form-control" name="oco_res_id" id="cad_oco_res_id" required>

            <div class="col-lg-6">
              <label class="form-label">Horário de Início Previsto <span>*</span></label>
              <input class="form-control" name="" id="cad_oco_res_hora_inicio" disabled>
              <div class="invalid-feedback">Este campo é obrigatório</div>
              <script>
                flatpickr("#cad_oco_res_hora_inicio", {
                  enableTime: true, // ativa o seletor de hora
                  noCalendar: true, // oculta o calendário
                  dateFormat: "H:i", // formato 24h: horas:minutos
                  time_24hr: true, // garante o formato 24h
                  allowInput: true // permite apagar e digitar manualmente
                });
              </script>
            </div>

            <div class="col-lg-6">
              <label class="form-label">Horário de Término Previsto <span>*</span></label>
              <input class="form-control" name="" id="cad_oco_res_hora_fim" disabled>
              <div class="invalid-feedback">Este campo é obrigatório</div>
              <script>
                flatpickr("#cad_oco_res_hora_fim", {
                  enableTime: true, // ativa o seletor de hora
                  noCalendar: true, // oculta o calendário
                  dateFormat: "H:i", // formato 24h: horas:minutos
                  time_24hr: true, // garante o formato 24h
                  allowInput: true // permite apagar e digitar manualmente
                });
              </script>
            </div>

            <div class="col-lg-12">
              <?php try {
                $sql = $conn->prepare("SELECT cto_id, UPPER(cto_tipo_ocorrencia) AS cto_tipo_ocorrencia FROM conf_tipo_ocorrencia WHERE cto_status = 1 ORDER BY cto_tipo_ocorrencia");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <label class="form-label">Selecione o(s) tipo(s) de ocorrência <span>*</span></label>
              <select class="form-select text-uppercase" name="oco_tipo_ocorrencia[]" multiple id="cad_tipo_ocorrencia"
                autocomplete="off" required>
                <!-- <option selected value=""></option> -->
                <?php foreach ($result as $res): ?>
                  <option value="<?= $res['cto_id'] ?>"><?= $res['cto_tipo_ocorrencia'] ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
              <script>
                $(document).ready(function () {
                  $('#cad_tipo_ocorrencia').select2({
                    placeholder: "Selecione as opções",
                    tags: false,
                    allowClear: true,
                    dropdownParent: $('#modal_cad_ocorrencia'),
                    width: '100%'
                  });
                });
              </script>
            </div>

            <div class="col-lg-6">
              <label class="form-label">Horário de Início Realizado <span>*</span></label>
              <input type="time" class="form-control time" name="oco_hora_inicio_realizado"
                id="cad_oco_hora_inicio_realizado" autocomplete="off" value="00:00" required>
              <script>
                flatpickr("#cad_oco_hora_inicio_realizado", {
                  enableTime: true, // ativa o seletor de hora
                  noCalendar: true, // oculta o calendário
                  dateFormat: "H:i", // formato 24h: horas:minutos
                  time_24hr: true, // garante o formato 24h
                  allowInput: true // permite apagar e digitar manualmente
                });
              </script>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6">
              <label class="form-label">Horário de Término Realizado <span>*</span></label>
              <input type="time" class="form-control time" name="oco_hora_fim_realizado" id="cad_oco_hora_fim_realizado"
                value="00:00" required>
              <script>
                flatpickr("#cad_oco_hora_fim_realizado", {
                  enableTime: true, // ativa o seletor de hora
                  noCalendar: true, // oculta o calendário
                  dateFormat: "H:i", // formato 24h: horas:minutos
                  time_24hr: true, // garante o formato 24h
                  allowInput: true // permite apagar e digitar manualmente
                });
              </script>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <div>
                <label class="form-label">Observações</label>
                <textarea class="form-control" name="oco_obs" rows="3"></textarea>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal"
                  data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect">Cadastrar</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>




<!-- EDITAR -->
<div class="modal fade modal_padrao" id="modal_edit_ocorrencia" tabindex="-1" aria-labelledby="modal_edit_ocorrencia"
  aria-modal="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_edit_ocorrencia">Cadastrar Ocorrência</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i
            class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../router/web.php?r=Ocorrenc" class="needs-validation" novalidate>

          <div class="row g-3">

            <input type="hidden" class="form-control oco_id" name="oco_id_original" required>
            <input type="hidden" class="form-control" name="acao" value="atualizar" required>

            <div class="col-lg-12">

              <?php try {
                $sql = $conn->prepare("SELECT res_id, res_codigo, res_data, res_hora_inicio, res_hora_fim FROM reservas
                                      INNER JOIN solicitacao ON solicitacao.solic_id = reservas.res_solic_id
                                      WHERE solic_id = :solic_id");
                $sql->execute([':solic_id' => $_GET['i']]);
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <label class="form-label">Código/Data da Aula <span>*</span></label>
              <select class="form-select oco_res_id" name="res_codigo" id="edit_oco_res_codigo" required>
                <option selected disabled value=""></option>
                <?php foreach ($result as $res): ?>
                  <option value="<?= $res['res_id'] ?>">
                    <?= $res['res_codigo'] . ': ' . date('d/m/Y', strtotime($res['res_data'])) . ' (' . date('H:i', strtotime($res['res_hora_inicio'])) . ' - ' . date('H:i', strtotime($res['res_hora_fim'])) . ')' ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <input type="hidden" class="form-control" name="oco_res_id" id="edit_oco_res_id" required>

            <div class="col-lg-6">
              <label class="form-label">Horário de Início Previsto <span>*</span></label>
              <input class="form-control" name="" id="edit_oco_res_hora_inicio" disabled>
              <div class="invalid-feedback">Este campo é obrigatório</div>
              <script>
                flatpickr("#edit_oco_res_hora_inicio", {
                  enableTime: true, // ativa o seletor de hora
                  noCalendar: true, // oculta o calendário
                  dateFormat: "H:i", // formato 24h: horas:minutos
                  time_24hr: true, // garante o formato 24h
                  allowInput: true // permite apagar e digitar manualmente
                });
              </script>
            </div>

            <div class="col-lg-6">
              <label class="form-label">Horário de Término Previsto <span>*</span></label>
              <input class="form-control" name="" id="edit_oco_res_hora_fim" disabled>
              <div class="invalid-feedback">Este campo é obrigatório</div>
              <script>
                flatpickr("#edit_oco_res_hora_fim", {
                  enableTime: true, // ativa o seletor de hora
                  noCalendar: true, // oculta o calendário
                  dateFormat: "H:i", // formato 24h: horas:minutos
                  time_24hr: true, // garante o formato 24h
                  allowInput: true // permite apagar e digitar manualmente
                });
              </script>
            </div>

            <div class="col-lg-12">
              <?php try {
                $sql = $conn->prepare("SELECT cto_id, UPPER(cto_tipo_ocorrencia) AS cto_tipo_ocorrencia FROM conf_tipo_ocorrencia WHERE cto_status = 1 ORDER BY cto_tipo_ocorrencia");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <label class="form-label">Selecione o(s) tipo(s) de ocorrência <span>*</span></label>
              <select class="form-select text-uppercase oco_tipo_ocorrencia" name="oco_tipo_ocorrencia[]" multiple
                id="edit_tipo_ocorrencia" required>
                <!-- <option selected value=""></option> -->
                <?php foreach ($result as $res): ?>
                  <option value="<?= $res['cto_id'] ?>"><?= $res['cto_tipo_ocorrencia'] ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
              <script>
                $(document).ready(function () {
                  $('#edit_tipo_ocorrencia').select2({
                    placeholder: "Selecione as opções",
                    tags: false,
                    allowClear: true,
                    dropdownParent: $('#modal_edit_ocorrencia'),
                    width: '100%'
                  });
                });
              </script>
            </div>

            <div class="col-lg-6">
              <label class="form-label">Horário de Início Realizado <span>*</span></label>
              <input type="time" class="form-control time oco_hora_inicio_realizado" name="oco_hora_inicio_realizado"
                id="edit_oco_hora_inicio_realizado" autocomplete="off" required>
              <script>
                flatpickr("#edit_oco_hora_inicio_realizado", {
                  enableTime: true, // ativa o seletor de hora
                  noCalendar: true, // oculta o calendário
                  dateFormat: "H:i", // formato 24h: horas:minutos
                  time_24hr: true, // garante o formato 24h
                  allowInput: true // permite apagar e digitar manualmente
                });
              </script>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6">
              <label class="form-label">Horário de Término Realizado <span>*</span></label>
              <input type="time" class="form-control time oco_hora_fim_realizado" name="oco_hora_fim_realizado"
                id="edit_oco_hora_fim_realizado" autocomplete="off" required>
              <script>
                flatpickr("#edit_oco_hora_fim_realizado", {
                  enableTime: true, // ativa o seletor de hora
                  noCalendar: true, // oculta o calendário
                  dateFormat: "H:i", // formato 24h: horas:minutos
                  time_24hr: true, // garante o formato 24h
                  allowInput: true // permite apagar e digitar manualmente
                });
              </script>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <div>
                <label class="form-label">Observações</label>
                <textarea class="form-control oco_obs" name="oco_obs" rows="3"></textarea>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal"
                  data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect">Atualizar</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>




<script>
  const modal_edit_ocorrencia = document.getElementById('modal_edit_ocorrencia')
  if (modal_edit_ocorrencia) {
    modal_edit_ocorrencia.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget
      // EXTRAI DADOS DO data-bs-* 
      const oco_id = button.getAttribute('data-bs-oco_id')
      const oco_res_id = button.getAttribute('data-bs-oco_res_id')
      const oco_tipo_ocorrencia = button.getAttribute('data-bs-oco_tipo_ocorrencia')
      const oco_hora_inicio_realizado = button.getAttribute('data-bs-oco_hora_inicio_realizado')
      const oco_hora_fim_realizado = button.getAttribute('data-bs-oco_hora_fim_realizado')
      const oco_obs = button.getAttribute('data-bs-oco_obs')
      // 
      const modalTitle = modal_edit_ocorrencia.querySelector('.modal-title')
      const modal_oco_id = modal_edit_ocorrencia.querySelector('.oco_id')
      const modal_oco_res_id = modal_edit_ocorrencia.querySelector('.oco_res_id')
      const modal_oco_tipo_ocorrencia = modal_edit_ocorrencia.querySelector('.oco_tipo_ocorrencia')
      const modal_oco_hora_inicio_realizado = modal_edit_ocorrencia.querySelector('.oco_hora_inicio_realizado')
      const modal_oco_hora_fim_realizado = modal_edit_ocorrencia.querySelector('.oco_hora_fim_realizado')
      const modal_oco_obs = modal_edit_ocorrencia.querySelector('.oco_obs')
      //
      modalTitle.textContent = 'Atualizar Dados'
      modal_oco_id.value = oco_id
      $('#edit_oco_res_codigo').val(oco_res_id).trigger('change');
      $('#edit_tipo_ocorrencia').val(oco_tipo_ocorrencia.split(',').map(id => id.trim())).trigger('change');
      modal_oco_hora_inicio_realizado.value = oco_hora_inicio_realizado
      modal_oco_hora_fim_realizado.value = oco_hora_fim_realizado
      modal_oco_obs.value = oco_obs
    })
  }
</script>



<!-- MODAL ADMIN OCORRENCIA -->
<div class="modal fade modal_padrao" id="modal_admin_ocorrencia" tabindex="-1" aria-labelledby="modal_admin_ocorrencia"
  aria-modal="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_admin_ocorrencia_title">Parecer Técnico</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i
            class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../router/web.php?r=Ocorrenc" class="needs-validation" novalidate>
          <div class="row g-3">
            <input type="hidden" class="form-control" name="acao" value="atualizar_admin" required>
            <input type="hidden" class="form-control oco_id" name="oco_id" required>
            <input type="hidden" name="acao_tipo_edicao" value="insert_versao" required>
            <input type="hidden" class="form-control admin_oco_res_id_hidden" name="oco_res_id" required>
            <input type="hidden" class="form-control admin_oco_obs_hidden" name="oco_obs">
            <input type="hidden" class="form-control admin_oco_carga_horaria" name="oco_carga_horaria_calculada">
            <input type="hidden" class="form-control admin_oco_status" name="oco_status" value="2">
            <div class="col-lg-12">
              <label class="form-label">Código/Data da Aula <span>*</span></label>
              <select class="form-select" id="admin_oco_res_codigo" disabled>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6">
              <label class="form-label">Horário de Início Previsto <span>*</span></label>
              <input class="form-control admin_res_hora_inicio" disabled>
            </div>

            <div class="col-lg-6">
              <label class="form-label">Horário de fim Previsto <span>*</span></label>
              <input class="form-control admin_res_hora_fim" disabled>
            </div>

            <div class="col-lg-12">
              <?php try {
                $sql = $conn->prepare("SELECT cto_id, UPPER(cto_tipo_ocorrencia) AS cto_tipo_ocorrencia FROM conf_tipo_ocorrencia WHERE cto_status = 1 ORDER BY cto_tipo_ocorrencia");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <label class="form-label">Selecione o(s) tipo(s) de ocorrência <span>*</span></label>
              <select class="form-select text-uppercase oco_tipo_ocorrencia admin_oco_tipo_ocorrencia_select"
                name="oco_tipo_ocorrencia[]" multiple id="admin_oco_tipo_ocorrencia_select" required>
                <?php foreach ($result as $res): ?>
                  <option value="<?= $res['cto_id'] ?>"><?= $res['cto_tipo_ocorrencia'] ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
              <script>
                $(document).ready(function () {
                  $('#admin_oco_tipo_ocorrencia_select').select2({
                    placeholder: "Selecione as opções",
                    tags: false,
                    allowClear: true,
                    dropdownParent: $('#modal_admin_ocorrencia'),
                    width: '100%'
                  });
                });
              </script>
            </div>

            <div class="col-lg-6">
              <label class="form-label">Horário de Início Realizado <span>*</span></label>
              <input type="time" class="form-control time admin_oco_hora_inicio_realizado"
                name="oco_hora_inicio_realizado" id="admin_oco_hora_inicio_realizado" required>
            </div>

            <div class="col-lg-6">
              <label class="form-label">Horário de fim Realizado <span>*</span></label>
              <input type="text" class="form-control time admin_oco_hora_fim_realizado" name="oco_hora_fim_realizado"
                id="admin_oco_hora_fim_realizado" required>
            </div>

            <div class="col-12">
              <label class="form-label">Observações</label>
              <textarea class="form-control admin_oco_obs_original" rows="3" disabled></textarea>
            </div>

            <div class="col-12 mt-3">
              <label class="form-label">Parecer Técnico <span>*</span></label>
              <textarea class="form-control admin_oco_parecer_tecnico" name="oco_parecer_tecnico" rows="3"
                required></textarea>
              <div class="invalid-feedback">O parecer é obrigatório para validação/ajuste.</div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect">Salvar e Aplicar</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  flatpickr("#admin_oco_hora_inicio_realizado", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
    allowInput: true
  });
  flatpickr("#admin_oco_hora_fim_realizado", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
    allowInput: true
  });
</script>

<script>
  // =========================================================
  // FUNÇÃO DE CÁLCULO DE CARGA HORÁRIA
  // =========================================================
  function calcularCargaHoraria(inicio, fim) {
    if (!inicio || !fim) return '00:00';

    const [hInicio, mInicio] = inicio.split(':').map(Number);
    const [hFim, mFim] = fim.split(':').map(Number);

    const totalMinutosInicio = hInicio * 60 + mInicio;
    let totalMinutosFim = hFim * 60 + mFim;

    if (totalMinutosFim < totalMinutosInicio) {
      totalMinutosFim += 24 * 60;
    }

    let diferencaMinutos = totalMinutosFim - totalMinutosInicio;

    const diffHoras = Math.floor(diferencaMinutos / 60);
    const diffMinutos = diferencaMinutos % 60;

    const horasFormatadas = String(diffHoras).padStart(2, '0');
    const minutosFormatados = String(diffMinutos).padStart(2, '0');

    return `${horasFormatadas}:${minutosFormatados}`;
  }


  // =========================================================
  // LÓGICA DO MODAL DO ADMINISTRADOR (#modal_admin_ocorrencia)
  // =========================================================
  const modal_admin_ocorrencia = document.getElementById('modal_admin_ocorrencia')

  if (modal_admin_ocorrencia) {

    // Adiciona o listener para recalcular a carga horária
    const inputInicio = modal_admin_ocorrencia.querySelector('.admin_oco_hora_inicio_realizado');
    const inputFim = modal_admin_ocorrencia.querySelector('.admin_oco_hora_fim_realizado');
    const inputCargaHoraria = modal_admin_ocorrencia.querySelector('input[name="oco_carga_horaria_calculada"]'); // Hidden field
    const inputStatus = modal_admin_ocorrencia.querySelector('input[name="oco_status"]'); // Hidden field

    // NOVO: Busca o elemento select múltiplo
    const modal_tipo_oco_select = modal_admin_ocorrencia.querySelector('.admin_oco_tipo_ocorrencia_select');

    const recalcular = () => {
      const carga = calcularCargaHoraria(inputInicio.value, inputFim.value);
      inputCargaHoraria.value = carga;
    };

    // Recalcula sempre que os inputs de hora mudarem
    inputInicio.addEventListener('change', recalcular);
    inputFim.addEventListener('change', recalcular);


    modal_admin_ocorrencia.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget

      // EXTRAI DADOS do botão (de ocorrencia_analise.php)
      const oco_id = button.getAttribute('data-bs-oco_id')
      const oco_parecer_tecnico = button.getAttribute('data-bs-oco_parecer_tecnico')
      const oco_res_id = button.getAttribute('data-bs-oco_res_id');
      const oco_tipo_ocorrencia_ids = button.getAttribute('data-bs-oco_tipo_ocorrencia');
      const oco_hora_inicio_realizado = button.getAttribute('data-bs-oco_hora_inicio_realizado')
      const oco_hora_fim_realizado = button.getAttribute('data-bs-oco_hora_fim_realizado')
      const oco_obs_raw_nl2br = button.getAttribute('data-bs-oco_obs')

      // NOVOS DADOS DA RESERVA (adicionados ao botão na página de análise)
      const res_hora_inicio_previsto = button.getAttribute('data-bs-res_hora_inicio') // NOVO
      const res_hora_fim_previsto = button.getAttribute('data-bs-res_hora_fim')       // NOVO
      const res_codigo_data = button.getAttribute('data-bs-res_codigo_data')         // NOVO


      // REVERTE O NL2BR DO PHP (para exibir no textarea)
      let oco_obs = oco_obs_raw_nl2br ? oco_obs_raw_nl2br.replace(/<br\s*\/?>/gi, '\n') : '';

      // REVERTE O PARECER TÉCNICO (O parecer técnico também usa nl2br)
      let parecer_tecnico = oco_parecer_tecnico ? oco_parecer_tecnico.replace(/<br\s*\/?>/gi, '\n') : ''; // <--- LINHA DE TRATAMENTO ADICIONADA/CORRIGIDA

      // BUSCA ELEMENTOS
      const modal_oco_id = modal_admin_ocorrencia.querySelector('input[name="oco_id"]')
      const modal_parecer = modal_admin_ocorrencia.querySelector('.admin_oco_parecer_tecnico')
      const modal_obs_original = modal_admin_ocorrencia.querySelector('.admin_oco_obs_original');
      const modal_obs_hidden = modal_admin_ocorrencia.querySelector('.admin_oco_obs_hidden');
      const modal_res_id_hidden = modal_admin_ocorrencia.querySelector('.admin_oco_res_id_hidden');
      const modal_res_codigo_select = modal_admin_ocorrencia.querySelector('#admin_oco_res_codigo'); // O select de Código/Data

      // Horários previstos (Apenas visualização)
      const modal_res_inicio_previsto = modal_admin_ocorrencia.querySelector('.admin_res_hora_inicio');
      const modal_res_fim_previsto = modal_admin_ocorrencia.querySelector('.admin_res_hora_fim');


      // ===============================================
      // PREENCHIMENTO DOS CAMPOS
      // ===============================================
      modal_oco_id.value = oco_id
      inputStatus.value = '2'; // Define o status para VALIDADA por padrão no modal admin
      modal_parecer.value = parecer_tecnico // <--- LINHA CHAVE CORRIGIDA
      inputStatus.value = '2';
      // 1. PREENCHIMENTO DOS CAMPOS OBRIGATÓRIOS (HIDDEN)
      modal_res_id_hidden.value = oco_res_id;
      modal_obs_hidden.value = oco_obs_raw_nl2br; // Envia o valor original COM NL2BR de volta ao controller


      // 2. HORÁRIOS PREVISTOS E CÓDIGO DA AULA (Dados da Reserva)
      if (modal_res_codigo_select) {
        // Limpa opções antigas
        modal_res_codigo_select.innerHTML = '';

        // Cria a opção com os dados da reserva
        const newOption = document.createElement('option');
        newOption.value = oco_res_id;
        newOption.textContent = res_codigo_data;
        newOption.selected = true; // Garante que a opção é selecionada

        // Adiciona a opção ao select
        modal_res_codigo_select.appendChild(newOption);
      }

      // Preenche os horários previstos (visualização)
      // Nota: As variáveis res_hora_inicio_previsto e res_hora_fim_previsto vêm dos data-bs-* no botão de chamada
      modal_res_inicio_previsto.value = (res_hora_inicio_previsto || '00:00:00.0000000').substring(0, 5);
      modal_res_fim_previsto.value = (res_hora_fim_previsto || '00:00:00.0000000').substring(0, 5);


      // 3. TIPO DE OCORRÊNCIA (Preenche o Select Multiple)
      if (modal_tipo_oco_select) {
        let selectedIds = [];
        if (oco_tipo_ocorrencia_ids) {
          selectedIds = oco_tipo_ocorrencia_ids.split(',').map(id => id.trim());
        }
        $(modal_tipo_oco_select).val(selectedIds).trigger('change');
      }


      // 4. HORÁRIOS REALIZADOS (Do Operador, preenchimento para edição)
      const horaInicioFormatada = (oco_hora_inicio_realizado || '00:00:00.0000000').substring(0, 5);
      const horaFimFormatada = (oco_hora_fim_realizado || '00:00:00.0000000').substring(0, 5);

      inputInicio.value = horaInicioFormatada;
      inputFim.value = horaFimFormatada;

      // 5. OBSERVAÇÕES ORIGINAIS (Somente visualização)
      modal_obs_original.value = oco_obs; // Valor tratado sem <br>

      // CALCULA A CARGA HORÁRIA INICIALMENTE
      recalcular();
    })
  }
</script>