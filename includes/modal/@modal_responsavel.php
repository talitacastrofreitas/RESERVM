<!-- CADASTRAR -->
<div class="modal fade modal_padrao" id="modal_cad_responsavel" tabindex="-1" aria-labelledby="modal_cad_responsavel" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_responsavel">Cadastrar Responsável</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_responsavel_contato.php" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control" name="prc_proposta_id" value="<?= $_GET['i'] ?>" required>

            <div class="col-12">
              <label class="form-label">Nome <span>*</span></label>
              <input type="text" class="form-control text-uppercase" name="prc_nome" maxlength="100" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">E-mail <span>*</span></label>
              <input type="email" class="form-control text-lowercase" name="prc_email" maxlength="100" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">Contato <span>*</span></label>
              <input type="text" class="form-control text-uppercase cel_tel" name="prc_contato" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="CadResponsavelContato">Cadastrar</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- EDITAR -->
<div class="modal fade modal_padrao" id="modal_edit_responsavel" tabindex="-1" aria-labelledby="modal_edit_responsavel" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_edit_responsavel">Cadastrar Coordenador(a) do Projeto</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_responsavel_contato.php" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control prc_id" name="prc_id" required>
            <input type="hidden" class="form-control prc_proposta_id" name="prc_proposta_id" required>

            <div class="col-12">
              <label class="form-label">Nome <span>*</span></label>
              <input type="text" class="form-control text-uppercase prc_nome" name="prc_nome" maxlength="100" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">E-mail <span>*</span></label>
              <input type="email" class="form-control text-lowercase prc_email" name="prc_email" maxlength="100" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">Contato <span>*</span></label>
              <input type="text" class="form-control text-uppercase cel_tel prc_contato" name="prc_contato" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="EditResponsavelContato">Atualizar</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
  <script>
    const modal_edit_responsavel = document.getElementById('modal_edit_responsavel')
    if (modal_edit_responsavel) {
      modal_edit_responsavel.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget
        // EXTRAI DADOS DO data-bs-* 
        const prc_id = button.getAttribute('data-bs-prc_id')
        const prc_proposta_id = button.getAttribute('data-bs-prc_proposta_id')
        const prc_nome = button.getAttribute('data-bs-prc_nome')
        const prc_email = button.getAttribute('data-bs-prc_email')
        const prc_contato = button.getAttribute('data-bs-prc_contato')
        // 
        const modalTitle = modal_edit_responsavel.querySelector('.modal-title')
        const modal_prc_id = modal_edit_responsavel.querySelector('.prc_id')
        const modal_prc_proposta_id = modal_edit_responsavel.querySelector('.prc_proposta_id')
        const modal_prc_nome = modal_edit_responsavel.querySelector('.prc_nome')
        const modal_prc_email = modal_edit_responsavel.querySelector('.prc_email')
        const modal_prc_contato = modal_edit_responsavel.querySelector('.prc_contato')
        //
        modalTitle.textContent = 'Atualizar Dados'
        modal_prc_id.value = prc_id
        modal_prc_proposta_id.value = prc_proposta_id
        modal_prc_nome.value = prc_nome
        modal_prc_email.value = prc_email
        modal_prc_contato.value = prc_contato
      })
    }
  </script>
</div>