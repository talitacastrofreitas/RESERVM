<div class="modal fade modal_padrao" id="modal_deferir_solicitacao" tabindex="-1"
  aria-labelledby="modal_deferir_solicitacao" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header modal_padrao_verde">
        <h5 class="modal-title" id="modal_deferir_solicitacao">Deferir Solicitação</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i
            class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../router/web.php?r=AprovaAnaliseAdmin" class="needs-validation"
          id="ValidaBotaoProgressDeferir" novalidate>

          <div class="row g-3">

            <input type="hidden" class="form-control" name="solic_id" value="<?= $_GET['i'] ?>" required>
            <input type="hidden" class="form-control" name="sta_an_solic_codigo" value="<?= $solic_codigo ?>" required>
            <input type="hidden" class="form-control" name="sta_an_user_email" value="<?= $user_email ?>" required>
            <input type="hidden" class="form-control" name="acao" value="deferir" required>

            <div class="col-12">
              <div>
                <label class="form-label">Observação</label>
                <textarea class="form-control" name="sta_an_obs"
                  rows="10">Em caso de desistência do espaço reservado, entre em contato com a Equipe de Suporte ao Ensino com, no mínimo, 48 horas de antecedência da data agendada.</textarea>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal"
                  data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect">Deferir</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>