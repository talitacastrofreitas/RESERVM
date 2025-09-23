<div class="modal fade modal_padrao" id="modal_cancelar_solicitacao" tabindex="-1" aria-labelledby="modal_cancelar_solicitacao" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header modal_padrao_cinza">
                <h5 class="modal-title" id="modal_cancelar_solicitacao">Solicitar cancelamento de solicitação </h5>
                <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <form method="POST" class="needs-validation" id="form_cancelar_solicitacao" novalidate>

                    <input type="hidden" name="solic_id_cancelar" id="solic_id_cancelar">

                    <div class="row g-3">
                        <div class="col-12">
                            <div>
                                <label for="motivo_cancelamento_solicitacao" class="form-label">Motivo do cancelamento<span class="text-danger">*</span></label>
                                <textarea class="form-control" id="motivo_cancelamento_solicitacao" name="motivo_cancelamento_solicitacao" rows="10" required></textarea>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal">Fechar</button>
                                <button type="submit" class="btn botao botao_verde waves-effect">Confirmar Cancelamento</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>