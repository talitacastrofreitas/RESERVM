<!-- CADASTRAR SERVIÇO -->
<div class="modal fade modal_padrao" id="modal_cad_servico" tabindex="-1" aria-labelledby="modal_cad_servico" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_servico">Cadastrar Serviço</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_propostas_servico.php" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control" name="ps_proposta_id" value="<?= $_GET['i'] ?>" required>

            <div class="col-12">
              <?php try {
                $stmt = $conn->query("SELECT cms_id, cms_material_servico FROM conf_material_servico WHERE cms_natureza = 2 ORDER BY CASE WHEN cms_material_servico = 'OUTRO' THEN 1 ELSE 0 END, cms_material_servico");
                $result_itens  = $stmt->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar materiais";
              } ?>
              <label class="form-label">Serviço <span>*</span></label>
              <select class="form-select text-uppercase" name="ps_mat_serv_id" id="cad_ps_mat_serv_id" required>
                <option selected disabled value=""></option>
                <?php foreach ($result_itens as $result_iten) : ?>
                  <option value="<?= $result_iten['cms_id']; ?>"><?= $result_iten['cms_material_servico']; ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">Quantidade <span>*</span></label>
              <input type="text" class="form-control" name="ps_quantidade" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="CadServico">Cadastrar</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- EDITAR SERVIÇO -->
<div class="modal fade modal_padrao" id="modal_edit_servico" tabindex="-1" aria-labelledby="modal_edit_servico" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_edit_servico">Cadastrar Serviço</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_propostas_servico.php" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control ps_id" name="ps_id" required>
            <input type="hidden" class="form-control ps_proposta_id" name="ps_proposta_id" required>

            <div class="col-12">
              <?php try {
                $stmt = $conn->query("SELECT cms_id, cms_material_servico FROM conf_material_servico WHERE cms_natureza = 2 ORDER BY CASE WHEN cms_material_servico = 'OUTRO' THEN 1 ELSE 0 END, cms_material_servico");
                $result_itens  = $stmt->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar materiais";
              } ?>
              <label class="form-label">Serviço <span>*</span></label>
              <select class="form-select text-uppercase ps_mat_serv_id" name="ps_mat_serv_id" id="edit_ps_mat_serv_id" required>
                <option selected disabled value=""></option>
                <?php foreach ($result_itens as $result_iten) : ?>
                  <option value="<?= $result_iten['cms_id']; ?>"><?= $result_iten['cms_material_servico']; ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">Quantidade <span>*</span></label>
              <input type="text" class="form-control ps_quantidade" name="ps_quantidade" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="EditServico">Atualizar</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
  <script>
    const modal_edit_servico = document.getElementById('modal_edit_servico')
    if (modal_edit_servico) {
      modal_edit_servico.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget
        // EXTRAI DADOS DO data-bs-* 
        const ps_id = button.getAttribute('data-bs-ps_id')
        const ps_proposta_id = button.getAttribute('data-bs-ps_proposta_id')
        const ps_mat_serv_id = button.getAttribute('data-bs-ps_mat_serv_id')
        const ps_quantidade = button.getAttribute('data-bs-ps_quantidade')
        // 
        const modalTitle = modal_edit_servico.querySelector('.modal-title')
        const modal_ps_id = modal_edit_servico.querySelector('.ps_id')
        const modal_ps_proposta_id = modal_edit_servico.querySelector('.ps_proposta_id')
        const modal_ps_mat_serv_id = modal_edit_servico.querySelector('.ps_mat_serv_id')
        const modal_ps_quantidade = modal_edit_servico.querySelector('.ps_quantidade')
        //
        modalTitle.textContent = 'Atualizar Dados'
        modal_ps_id.value = ps_id
        modal_ps_proposta_id.value = ps_proposta_id
        modal_ps_mat_serv_id.value = ps_mat_serv_id
        modal_ps_quantidade.value = ps_quantidade

        // ESTE SELETOR LEVA O DADO DA BASE PARA O CAMPO SELECT2
        $('#edit_ps_mat_serv_id').val(ps_mat_serv_id).trigger('change');
      })
    }
  </script>
</div>