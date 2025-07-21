<!-- CADASTRAR COORDENADOR PROJETO -->
<div class="modal fade modal_padrao" id="modal_cad_material" tabindex="-1" aria-labelledby="modal_cad_material" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_material">Cadastrar Material</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_propostas_material.php" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control" name="pmc_proposta_id" value="<?= $_GET['i'] ?>" required>

            <div class="col-12">
              <?php try {
                $stmt = $conn->query("SELECT cms_id, cms_material_servico FROM conf_material_servico WHERE cms_natureza = 1 ORDER BY CASE WHEN cms_material_servico = 'OUTRO' THEN 1 ELSE 0 END, cms_material_servico");
                $result_itens  = $stmt->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar materiais";
              } ?>
              <label class="form-label">Material <span>*</span></label>
              <select class="form-select text-uppercase" name="pmc_material_consumo" id="cad_pmc_material_consumo" required>
                <option selected disabled value=""></option>
                <?php foreach ($result_itens as $result_iten) : ?>
                  <option value="<?= $result_iten['cms_id']; ?>"><?= $result_iten['cms_material_servico']; ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">Quantidade <span>*</span></label>
              <input type="text" class="form-control" name="pmc_quantidade" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="CadMaterial">Cadastrar</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- EDITAR COORDENADOR PROJETO -->
<div class="modal fade modal_padrao" id="modal_edit_material" tabindex="-1" aria-labelledby="modal_edit_material" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_edit_material">Cadastrar Coordenador(a) do Projeto</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_propostas_material.php" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control pmc_id" name="pmc_id" required>
            <input type="hidden" class="form-control pmc_proposta_id" name="pmc_proposta_id" required>

            <div class="col-12">
              <?php try {
                $stmt = $conn->query("SELECT cms_id, cms_material_servico FROM conf_material_servico WHERE cms_natureza = 1 ORDER BY CASE WHEN cms_material_servico = 'OUTRO' THEN 1 ELSE 0 END, cms_material_servico");
                $result_itens  = $stmt->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar materiais";
              } ?>
              <label class="form-label">Material <span>*</span></label>
              <select class="form-select text-uppercase pmc_material_consumo" name="pmc_material_consumo" id="edit_pmc_material_consumo" required>
                <option selected disabled value=""></option>
                <?php foreach ($result_itens as $result_iten) : ?>
                  <option value="<?= $result_iten['cms_id']; ?>"><?= $result_iten['cms_material_servico']; ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">Quantidade <span>*</span></label>
              <input type="text" class="form-control pmc_quantidade" name="pmc_quantidade" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="EditMaterial">Atualizar</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
  <script>
    const modal_edit_material = document.getElementById('modal_edit_material')
    if (modal_edit_material) {
      modal_edit_material.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget
        // EXTRAI DADOS DO data-bs-* 
        const pmc_id = button.getAttribute('data-bs-pmc_id')
        const pmc_proposta_id = button.getAttribute('data-bs-pmc_proposta_id')
        const pmc_material_consumo = button.getAttribute('data-bs-pmc_material_consumo')
        const pmc_quantidade = button.getAttribute('data-bs-pmc_quantidade')
        // 
        const modalTitle = modal_edit_material.querySelector('.modal-title')
        const modal_pmc_id = modal_edit_material.querySelector('.pmc_id')
        const modal_pmc_proposta_id = modal_edit_material.querySelector('.pmc_proposta_id')
        const modal_pmc_material_consumo = modal_edit_material.querySelector('.pmc_material_consumo')
        const modal_pmc_quantidade = modal_edit_material.querySelector('.pmc_quantidade')
        //
        modalTitle.textContent = 'Atualizar Dados'
        modal_pmc_id.value = pmc_id
        modal_pmc_proposta_id.value = pmc_proposta_id
        modal_pmc_material_consumo.value = pmc_material_consumo
        modal_pmc_quantidade.value = pmc_quantidade

        // ESTE SELETOR LEVA O DADO DA BASE PARA O CAMPO SELECT2
        $('#edit_pmc_material_consumo').val(pmc_material_consumo).trigger('change');
      })
    }
  </script>
</div>