<?php include 'includes/header.php'; ?>

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Meus Dados</h4>

      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="javascript: void(0);">Minha Conta</a></li>
          <li class="breadcrumb-item active">Meus Dados</li>
        </ol>
      </div>

    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="card tabs_perfil">
      <div class="card-header">
        <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist" id="nav-tabs">
          <li class="nav-item link_nav_perfil">
            <a class="nav-link text-body active" data-bs-toggle="tab" id="tabDados-tab" href="#tabDados" role="tab">Meus Dados</a>
          </li>
          <li class="nav-item link_nav_perfil">
            <a class="nav-link text-body" data-bs-toggle="tab" id="tabSenha-tab" href="#tabSenha" role="tab">Alterar Senha</a>
          </li>
        </ul>
      </div>
      <div class="card-body p-0">
        <div class="card-body p-4">
          <div class="tab-content">

            <div class="tab-pane active" id="tabDados" role="tabpanel">

              <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-4">
                <div class="col">
                  <div class="mb-3">
                    <label for="firstnameInput" class="form-label">Matrícula</label>
                    <input type="text" class="form-control" value="<?= $global_admin_matricula ?>" disabled>
                  </div>
                </div>
                <div class="col">
                  <div class="mb-3">
                    <label for="lastnameInput" class="form-label">Nome Completo</label>
                    <input type="text" class="form-control" value="<?= $global_admin_nome ?>" disabled>
                  </div>
                </div>
                <div class="col">
                  <div class="mb-3">
                    <label for="phonenumberInput" class="form-label">E-mail</label>
                    <input type="email" class="form-control" value="<?= $global_admin_email ?>" disabled>
                  </div>
                </div>
                <div class="col">
                  <div class="mb-3">
                    <label for="emailInput" class="form-label">Perfil</label>
                    <input type="text" class="form-control" value="<?= $global_adm_perfil ?>" disabled>
                  </div>
                </div>
              </div>

              <div class=" border-top mt-2 pt-3">
                <ul class="list-unstyled mb-0">
                  <li class="d-flex">
                    <div class="flex-grow-1">
                      <label class="form-check-label fs-14">Excluir minha conta</label>
                      <p class="text-muted">Depois de excluir sua conta, não há como voltar atrás. Por favor, tenha certeza.</p>
                      <!-- <a href="controller/controller_admin.php?funcao=exc_admin_conta&cod=<?= base64_encode($global_admin_id) ?>" class="btn botao botao_vermelho_transparente waves-effect del-btn-conta" title="Excluir conta">Excluir conta</a> -->
                      <a class="btn botao botao_vermelho_transparente waves-effect" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modalSignin" title="Excluir conta">Excluir conta</a>
                    </div>
                  </li>
                </ul>
              </div>

            </div>

            <div class="tab-pane" id="tabSenha" role="tabpanel">

              <form class="needs-validation" method="POST" action="../router/web.php?r=updPassPerf&func=upd_pass_perf" id="UserRegistro" novalidate>

                <input type="hidden" class="form-control" value="<?= $global_admin_id ?>" name="cod" required>

                <div class="mb-3">
                  <label class="form-label">Senha Atual <span>*</span></label>
                  <div class="position-relative auth-pass-inputgroup mb-3">
                    <input type="password" class="form-control pe-5 password-input" name="senha_atual" required>
                    <button class="btn btn-link position-absolute end-0 top-0 text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                    <div class="invalid-feedback">Digite a senha atual</div>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label" for="pwd">Nova Senha <span>*</span></label>
                  <div class="position-relative auth-pass-inputgroup mb-3">
                    <input type="password" class="form-control pe-5 password-input" id="pwd" name="senha" required>
                    <button class="btn btn-link position-absolute end-0 top-0 text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                    <div class="invalid-feedback">Digite a senha</div>
                  </div>
                </div>

                <div id="criterios" class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show" role="alert" style="display: none;">
                  <i class="ri-error-warning-line label-icon"></i><strong>Erro</strong> - A senha deve atender os critérios!
                </div>

                <p class="mb-0">Todas as marcas de seleção devem ficar verdes para prosseguir</p>
                <div id="password-info">
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
                  <label class="form-label" for="cpwd">Confirme a Senha <span>*</span></label>
                  <div class="position-relative auth-pass-inputgroup mb-3">
                    <input type="password" class="form-control pe-5 password-input" id="cpwd" name="conf_senha" required>
                    <button class="btn btn-link position-absolute end-0 top-0 text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                    <div class="invalid-feedback">Confirme a senha</div>
                  </div>
                </div>

                <div id="senha" class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show" role="alert" style="display: none;">
                  <i class="ri-error-warning-line label-icon"></i><strong>Erro</strong> - As senhas não correspondem!
                </div>

                <div class="col-lg-12 mt-4">
                  <div class="hstack gap-3 align-items-center justify-content-end">
                    <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                    <button type="submit" class="btn botao botao_verde waves-effect waves-light">Alterar Senha</button>
                  </div>
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
        <form method="POST" action="../router/web.php?r=AdminExcConta" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" name="i" value="<?= $global_admin_id ?>">
            <input type="hidden" name="acao" value="deletar_conta">

            <div class="col-12">
              <div>
                <label class="form-label">Senha <span>*</span></label>
                <input type="password" class="form-control" name="admin_senha" required>
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
<script src="../assets/js/valid-password.js"></script>
<!-- PASSWORD ADDON INIT -->
<script src="../assets/js/pages/password-addon.init.js"></script>