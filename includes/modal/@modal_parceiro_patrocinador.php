<!-- CADASTRAR PARCEIROS / PATROCINADORES -->
<div class="modal fade modal_padrao" id="modal_cad_parceiro_patrocinador" tabindex="-1" aria-labelledby="modal_cad_parceiro_patrocinador" aria-modal="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_parceiro_patrocinador">Cadastrar Parceiro Externo/Patrocinador</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_parceiro_externo.php" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control" name="ppe_proposta_id" value="<?= $_GET['i'] ?>" required>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">Instituição/Razão Social <span>*</span></label>
              <input type="text" class="form-control text-uppercase" name="ppe_nome" maxlength="50" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">E-mail <span>*</span></label>
              <input type="email" class="form-control text-lowercase" name="ppe_email" maxlength="50" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">CNPJ</label>
              <input type="text" class="form-control text-uppercase cnpj" name="ppe_cnpj" maxlength="50">
            </div>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">Responsável <span>*</span></label>
              <input type="text" class="form-control text-uppercase" name="ppe_responsavel" maxlength="50" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">Contato do responsável <span>*</span></label>
              <input type="text" class="form-control text-uppercase cel_tel" name="ppe_contato" maxlength="50" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">Área de atuação <span>*</span></label>
              <input type="text" class="form-control text-uppercase" name="ppe_area_atuacao" maxlength="50" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">Descreva o que foi negociado na parceria para emissão de termo de patrocínio/parceria</label>
              <textarea class="form-control" name="ppe_obs" rows="3"></textarea>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="ppe_convenio" name="ppe_convenio" value="1">
                <label class="form-check-label" for="ppe_convenio">Existência de convênio ou acordo formalizado</label>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="CadParceiroExterno">Cadastrar</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- EDITAR PARCEIROS / PATROCINADORES -->
<div class="modal fade modal_padrao" id="modal_edit_parceiro_patrocinador" tabindex="-1" aria-labelledby="modal_edit_parceiro_patrocinador" aria-modal="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_edit_parceiro_patrocinador">Cadastrar Parceiro Externo/Patrocinador</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_parceiro_externo.php" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control ppe_id" name="ppe_id" required>
            <input type="hidden" class="form-control ppe_proposta_id" name="ppe_proposta_id" required>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">Instituição/Razão Social <span>*</span></label>
              <input type="text" class="form-control text-uppercase ppe_nome" name="ppe_nome" maxlength="50" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">E-mail <span>*</span></label>
              <input type="email" class="form-control text-lowercase ppe_email" name="ppe_email" maxlength="50" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">CNPJ</label>
              <input type="text" class="form-control text-uppercase cnpj ppe_cnpj" name="ppe_cnpj" maxlength="50">
            </div>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">Responsável <span>*</span></label>
              <input type="text" class="form-control text-uppercase ppe_responsavel" name="ppe_responsavel" maxlength="50" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">Contato do responsável <span>*</span></label>
              <input type="text" class="form-control text-uppercase cel_tel ppe_contato" name="ppe_contato" maxlength="50" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">Área de atuação <span>*</span></label>
              <input type="text" class="form-control text-uppercase ppe_area_atuacao" name="ppe_area_atuacao" maxlength="50" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">Descreva o que foi negociado na parceria para emissão de termo de patrocínio/parceria</label>
              <textarea class="form-control ppe_obs" name="ppe_obs" rows="3"></textarea>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input ppe_convenio" type="checkbox" id="check_ppe_convenio" name="ppe_convenio" value="1" checked>
                <label class="form-check-label" for="check_ppe_convenio">Existência de convênio ou acordo formalizado</label>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="EditParceiroExterno">Atualizar</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    const modal_edit_parceiro_patrocinador = document.getElementById('modal_edit_parceiro_patrocinador')
    if (modal_edit_parceiro_patrocinador) {
      modal_edit_parceiro_patrocinador.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget
        // EXTRAI DADOS DO data-bs-* 
        const ppe_id = button.getAttribute('data-bs-ppe_id')
        const ppe_proposta_id = button.getAttribute('data-bs-ppe_proposta_id')
        const ppe_nome = button.getAttribute('data-bs-ppe_nome')
        const ppe_email = button.getAttribute('data-bs-ppe_email')
        const ppe_contato = button.getAttribute('data-bs-ppe_contato')
        const ppe_cnpj = button.getAttribute('data-bs-ppe_cnpj')
        const ppe_responsavel = button.getAttribute('data-bs-ppe_responsavel')
        const ppe_area_atuacao = button.getAttribute('data-bs-ppe_area_atuacao')
        const ppe_obs = button.getAttribute('data-bs-ppe_obs')
        const ppe_convenio = button.getAttribute('data-bs-ppe_convenio')
        // 
        const modalTitle = modal_edit_parceiro_patrocinador.querySelector('.modal-title')
        const modal_ppe_id = modal_edit_parceiro_patrocinador.querySelector('.ppe_id')
        const modal_ppe_proposta_id = modal_edit_parceiro_patrocinador.querySelector('.ppe_proposta_id')
        const modal_ppe_nome = modal_edit_parceiro_patrocinador.querySelector('.ppe_nome')
        const modal_ppe_email = modal_edit_parceiro_patrocinador.querySelector('.ppe_email')
        const modal_ppe_contato = modal_edit_parceiro_patrocinador.querySelector('.ppe_contato')
        const modal_ppe_cnpj = modal_edit_parceiro_patrocinador.querySelector('.ppe_cnpj')
        const modal_ppe_responsavel = modal_edit_parceiro_patrocinador.querySelector('.ppe_responsavel')
        const modal_ppe_area_atuacao = modal_edit_parceiro_patrocinador.querySelector('.ppe_area_atuacao')
        const modal_ppe_obs = modal_edit_parceiro_patrocinador.querySelector('.ppe_obs')
        const modal_ppe_convenio = modal_edit_parceiro_patrocinador.querySelector('.ppe_convenio')
        //
        modalTitle.textContent = 'Atualizar Dados'
        modal_ppe_id.value = ppe_id
        modal_ppe_proposta_id.value = ppe_proposta_id
        modal_ppe_nome.value = ppe_nome
        modal_ppe_email.value = ppe_email
        modal_ppe_contato.value = ppe_contato
        modal_ppe_cnpj.value = ppe_cnpj
        modal_ppe_responsavel.value = ppe_responsavel
        modal_ppe_area_atuacao.value = ppe_area_atuacao
        modal_ppe_obs.value = ppe_obs
        // VERIFICA DE O CHECKBOX ESTÁ MARCADO
        if (ppe_convenio === '1') {
          modal_ppe_convenio.checked = true;
        } else {
          modal_ppe_convenio.checked = false;
        }
      })
    }
  </script>
</div>