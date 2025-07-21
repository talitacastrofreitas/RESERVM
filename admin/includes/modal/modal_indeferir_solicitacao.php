<div class="modal fade modal_padrao" id="modal_indeferir_solicitacao" tabindex="-1" aria-labelledby="modalIndeferirsolicitacao" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header modal_padrao_vermelho">
        <h5 class="modal-title" id="modalIndeferirsolicitacao">Indeferir Proposta</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../router/web.php?r=AprovaAnaliseAdmin" class="needs-validation" id="ValidaBotaoProgressIndeferir" novalidate>

          <div class="row g-3">

            <input type="hidden" class="form-control" name="solic_id" value="<?= $_GET['i'] ?>" required>
            <input type="hidden" class="form-control" name="sta_an_solic_codigo" value="<?= $solic_codigo ?>" required>
            <input type="hidden" class="form-control" name="sta_an_user_email" value="<?= $user_email ?>" required>
            <input type="hidden" class="form-control" name="acao" value="indeferir" required>

            <div class="col-12">
              <div>
                <label class="form-label">Motivo <span>*</span></label>
                <textarea class="form-control" name="sta_an_obs" id="" rows="10" required></textarea>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_vermelho waves-effect">Indeferir</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>