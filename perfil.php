<?php include 'includes/header.php'; ?>

<div class="profile-foreground position-relative mx-n4 mt-n4">
  <div class="profile-wid-bg">
    <!-- <img src="assets/images/profile-bg.jpg" alt="" class="profile-wid-img"> -->
  </div>
</div>

<div class="pt-2 mb-4 mb-lg-3 pb-lg-3 profile-wrapper header_perfil">
</div>

<div class="row">
  <div class="col-lg-12">
    <div>
      <div class="d-flex profile-wrapper header_perfil_nav">
        <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1" id="myTab" role="tablist">
          <li class="nav-item" role="presentation">
            <a class="nav-link fs-14 active" data-bs-toggle="tab" href="#dados" id="dados-tab" role="tab" aria-selected="true" title="Meus Dados">
              <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-person d-inline-block d-sm-none" viewBox="0 0 16 16">
                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z" />
              </svg> <span class="d-none d-sm-inline-block">Meus Dados</span>
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link fs-14" data-bs-toggle="tab" href="#senha" id="senha-tab" role="tab" aria-selected="false" tabindex="-1" title="Alterar Senha">
              <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-key d-inline-block d-sm-none" viewBox="0 0 16 16">
                <path d="M0 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 10h-.535A4 4 0 0 1 0 8m4-3a3 3 0 1 0 2.712 4.285A.5.5 0 0 1 7.163 9h.63l.853-.854a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.793-.793-1-1h-6.63a.5.5 0 0 1-.451-.285A3 3 0 0 0 4 5" />
                <path d="M4 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
              </svg> <span class="d-none d-sm-inline-block">Alterar Senha</span>
            </a>
          </li>
        </ul>
        <!-- <div class="flex-shrink-0">
          <a href="pages-profile-settings.html" class="btn btn-success"><i class="ri-edit-box-line align-bottom"></i> Edit Profile</a>
        </div> -->
      </div>
      <!-- Tab panes -->
      <div class="tab-content pt-4 text-muted">
        <div class="tab-pane active" id="dados" role="tabpanel">
          <div class="row">
            <div class="col-lg-12">
              <div class="card tabs_perfil">

                <div class="card-header">
                  <div class="row align-items-center">
                    <div class="col-sm-12">
                      <h5 class="card-title mb-0">Meus Dados</h5>
                    </div>
                  </div>
                </div>

                <div class="card-body p-4">

                  <div class="tab-content">

                    <form class="needs-validation" method="POST" action="controller/controller_usuarios.php" autocomplete="off" novalidate>

                      <div class="tab-pane active" id="tabDados" role="tabpanel">
                        <div class="row grid gx-3">

                          <input type="hidden" class="form-control" name="user_id" value="<?= base64_encode($global_user_id) ?>" required>

                          <div class="col-lg-4">
                            <div class="mb-3">
                              <label class="form-label">Matrícula <span>*</span></label>
                              <input type="text" class="form-control text-uppercase" name="" value="<?= $global_user_matricula ?>" readonly>
                              <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>
                          </div>

                          <div class="col-lg-4">
                            <div class="mb-3">
                              <label class="form-label">Nome Completo</label>
                              <input type="text" class="form-control text-uppercase" name="" value="<?= $global_user_nome ?>" readonly>
                            </div>
                          </div>

                          <div class="col-lg-4">
                            <div class="mb-3">
                              <label class="form-label">E-mail <span>*</span></label>
                              <input type="email" class="form-control text-lowercase" name="" value="<?= $global_user_email ?>" readonly>
                              <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>
                          </div>

                        </div>

                        <div class=" border-top mt-3 pt-3">
                          <ul class="list-unstyled mb-0">
                            <li class="d-flex">
                              <div class="flex-grow-1">
                                <label class="form-check-label fs-14">Excluir minha conta</label>
                                <p class="text-muted">Depois de excluir sua conta, não há como voltar atrás. Por favor, tenha certeza.</p>
                                <!-- <a href="controller/controller_usuarios.php?funcao=exc_user_conta&cod=<?= base64_encode($global_user_id) ?>" class="btn botao botao_vermelho_transparente waves-effect del-btn-conta" title="Excluir conta">Excluir conta</a> -->
                                <a class="btn botao botao_vermelho_transparente waves-effect" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modalSignin" title="Excluir conta">Excluir conta</a>
                              </div>
                            </li>
                          </ul>
                        </div>

                      </div>

                    </form>

                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>

        <!-- ALTERAR SENHA -->
        <div class="tab-pane" id="senha" role="tabpanel">
          <div class="card">

            <div class="card-header">
              <div class="row align-items-center">
                <div class="col-sm-12">
                  <h5 class="card-title mb-0">Alterar Senha</h5>
                </div>
              </div>
            </div>

            <div class="card-body p-4">

              <form class="needs-validation" method="POST" action="controller/controller_usuarios.php?funcao=alterSenhaPerfil" id="UserRegistro" novalidate>

                <input type="hidden" class="form-control" value="<?= base64_encode($global_user_id) ?>" name="cod" required>

                <div class="mb-3">
                  <label class="form-label">Senha atual <span>*</span></label>
                  <div class="position-relative auth-pass-inputgroup mb-3">
                    <input type="password" class="form-control pe-5 password-input" name="senha_atual" required>
                    <button class="btn btn-link position-absolute end-0 top-0 text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                    <div class="invalid-feedback">Digite a senha atual</div>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label" for="pwd">Senha <span>*</span></label>
                  <div class="position-relative auth-pass-inputgroup mb-3">
                    <input type="password" class="form-control pe-5 password-input" id="pwd" name="senha" required>
                    <button class="btn btn-link position-absolute end-0 top-0 text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                    <div class="invalid-feedback">Digite a senha</div>
                  </div>
                </div>

                <div id="criterios" class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show" role="alert" style="display: none;">
                  <i class="ri-error-warning-line label-icon"></i><strong>Erro</strong> - A senha deve atender os critérios!
                  <!-- <button  type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> -->
                </div>

                <p class="mb-0">Todas as marcas de seleção devem ficar verdes para prosseguir</p>
                <div id="password-info" class="password-info">
                  <ul>
                    <li id="length" class="invalid clearfix">
                      <span class="icon-container"></span>
                      Pelo menos 8 caracteres
                    </li>
                    <li id="capital" class="invalid clearfix">
                      <span class="icon-container"></span>
                      Pelo menos 1 letra maiúscula
                    </li>
                    <li id="letter" class="invalid clearfix">
                      <span class="icon-container"></span>
                      Pelo menos 1 letra minúscula
                    </li>
                    <li id="numb" class="invalid clearfix">
                      <span class="icon-container"></span>
                      Pelo menos 1 número
                    </li>
                    <li id="special" class="invalid clearfix">
                      <span class="icon-container"></span>
                      Pelo menos 1 <span><a data-tooltip="! @ # $ % ^ & *">caractere especial</a></span>
                    </li>
                  </ul>
                </div>

                <div class="mb-3">
                  <label class="form-label" for="cpwd">Confirmar senha <span>*</span></label>
                  <div class="position-relative auth-pass-inputgroup mb-3">
                    <input type="password" class="form-control pe-5 password-input" id="cpwd" name="conf_senha" required>
                    <button class="btn btn-link position-absolute end-0 top-0 text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                    <div class="invalid-feedback">Confirme a senha</div>
                  </div>
                </div>

                <div id="senha" class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show" role="alert" style="display: none;">
                  <i class="ri-error-warning-line label-icon"></i><strong>Erro</strong> - As senhas não correspondem!
                  <!-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> -->
                </div>

                <div class="mt-4 text-end">
                  <button type="submit" class="btn botao botao_verde waves-effect waves-light">Alterar Senha</button>
                </div>

              </form>

            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>


<!-- MODAL EXCLUIR CONTA -->
<div class="modal fade modal_padrao" id="modalSignin" tabindex="-1" aria-labelledby="modalSignin" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-header header_card_login text-center pb-3 border-bottom-0 ">
        <h5 class="m-0">Confirmação de exclusão</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="header_card_login text-center px-3">
        <p class="m-0">Esta ação é permanente e não poderá ser desfeita.
          Para confirmar a exclusão da sua conta, digite sua senha abaixo.</p>
      </div>

      <div class="modal-body">
        <form method="POST" action="router/web.php?r=UserExcConta" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" name="i" value="<?= $global_user_id ?>">
            <input type="hidden" name="acao" value="deletar_conta">

            <div class="col-12">
              <div>
                <label class="form-label">Senha <span>*</span></label>
                <input type="password" class="form-control" name="user_senha" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <button type="submit" class="btn botao botao_vermelho waves-effect w-100">Confirmar</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<script>
  document.addEventListener("DOMContentLoaded", function() {
    // Obtém as tabs e adiciona evento de clique
    document.querySelectorAll(".nav-link").forEach(tab => {
      tab.addEventListener("click", function() {
        let tabId = this.getAttribute("href").substring(1); // Obtém "tab1" ou "tab2"
        let newUrl = window.location.pathname + "?tab=" + tabId;
        history.pushState(null, "", newUrl); // Atualiza a URL sem recarregar a página
      });
    });

    // Ao carregar a página, ativa a tab correta baseada na URL
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get("tab");

    if (activeTab) {
      let tabElement = document.querySelector(`#${activeTab}-tab`);
      if (tabElement) {
        new bootstrap.Tab(tabElement).show();
      }
    }
  });
</script>

<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>
<!-- VALIDA SENHA -->
<script src="assets/js/valid-password.js"></script>
<!-- PASSWORD ADDON INIT -->
<script src="assets/js/pages/password-addon.init.js"></script>