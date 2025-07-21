<!-- CADASTRAR -->
<div class="modal fade modal_padrao" id="modal_cad_ocorrencia" tabindex="-1" aria-labelledby="modal_cad_ocorrencia" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_ocorrencia">Cadastrar Ocorrência</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
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
                <?php foreach ($result as $res) : ?>
                  <option value="<?= $res['res_id'] ?>"><?= $res['res_codigo'] . ': ' .  date('d/m/Y', strtotime($res['res_data'])) . ' (' .  date('H:i', strtotime($res['res_hora_inicio'])) . ' - ' .  date('H:i', strtotime($res['res_hora_fim'])) . ')' ?></option>
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
              <select class="form-select text-uppercase" name="oco_tipo_ocorrencia[]" multiple id="cad_tipo_ocorrencia" autocomplete="off" required>
                <!-- <option selected value=""></option> -->
                <?php foreach ($result as $res) : ?>
                  <option value="<?= $res['cto_id'] ?>"><?= $res['cto_tipo_ocorrencia'] ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
              <script>
                $(document).ready(function() {
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
              <input type="time" class="form-control time" name="oco_hora_inicio_realizado" id="cad_oco_hora_inicio_realizado" autocomplete="off" required>
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
              <input type="time" class="form-control time" name="oco_hora_fim_realizado" id="cad_oco_hora_fim_realizado" required>
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
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
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
<div class="modal fade modal_padrao" id="modal_edit_ocorrencia" tabindex="-1" aria-labelledby="modal_edit_ocorrencia" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_edit_ocorrencia">Cadastrar Ocorrência</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../router/web.php?r=Ocorrenc" class="needs-validation" novalidate>

          <div class="row g-3">

            <input type="hidden" class="form-control oco_id" name="oco_id" required>
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
                <?php foreach ($result as $res) : ?>
                  <option value="<?= $res['res_id'] ?>"><?= $res['res_codigo'] . ': ' .  date('d/m/Y', strtotime($res['res_data'])) . ' (' .  date('H:i', strtotime($res['res_hora_inicio'])) . ' - ' .  date('H:i', strtotime($res['res_hora_fim'])) . ')' ?></option>
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
              <select class="form-select text-uppercase oco_tipo_ocorrencia" name="oco_tipo_ocorrencia[]" multiple id="edit_tipo_ocorrencia" required>
                <!-- <option selected value=""></option> -->
                <?php foreach ($result as $res) : ?>
                  <option value="<?= $res['cto_id'] ?>"><?= $res['cto_tipo_ocorrencia'] ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
              <script>
                $(document).ready(function() {
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
              <input type="time" class="form-control time oco_hora_inicio_realizado" name="oco_hora_inicio_realizado" id="edit_oco_hora_inicio_realizado" autocomplete="off" required>
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
              <input type="time" class="form-control time oco_hora_fim_realizado" name="oco_hora_fim_realizado" id="edit_oco_hora_fim_realizado" autocomplete="off" required>
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
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
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