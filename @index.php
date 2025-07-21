<?php include 'includes/header_login.php'; ?>

<div class="container">

  <div class="row justify-content-center align-items-center flex-column">
    <div class="col-md-8 col-lg-6 col-xl-9">
      <div class="card card_login">
        <div class="card-body px-3 px-sm-4 py-4">
          <div class="header_card_login text-center mt-2">
            <div class="logo_login">
              <img src="assets/images/logo-dark-register.svg" alt="RESERVM">
            </div>
          </div>

          <div class="app-menu navbar-menu d-none"></div>

          <div class="p-2 mt-4">

            <form method="POST" id="loginForm" class="needs-validation" action="controller/controller_acesso.php" novalidate>

              <div class="mb-3">
                <label class="form-label">Seu e-mail</label>
                <input type="email" class="form-control text-lowercase" name="email" id="email" required>
                <div class="invalid-feedback">Informe seu e-mail</div>
              </div>

              <div class="mb-3">
                <div class="d-flex justify-content-between">
                  <label class="form-label" for="password-input">Sua senha</label>
                  <a href="us-forgot-pass.php" class="link">Esqueceu sua senha?</a>
                </div>
                <div class="position-relative auth-pass-inputgroup mb-3">
                  <input type="password" class="form-control password-input" name="senha" id="senha" required>
                  <button class="btn btn-link position-absolute end-0 top-0 text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                  <div class="invalid-feedback">Informe sua senha</div>
                </div>
              </div>

              <div class="form-check form-check-success">
                <input class="form-check-input" type="checkbox" value="" name="rememberMe" id="rememberMe">
                <label class="form-check-label" for="rememberMe">Lembrar acesso</label>
              </div>

              <div class="mt-4">
                <button type="submit" class="btn botao botao_verde waves-effect waves-light w-100" name="loginUser">Entrar</button>
              </div>

              <div class="mt-4 text-center">
                <div class="signin-other-title">
                  <p class="fs-13 mb-4 title text-muted">Não tem uma conta?</p>
                </div>
              </div>

              <div class="mt-0">
                <a href="us-record.php" type="button" class="btn botao botao_azul_escuro_transparente waves-effect waves-light w-100">Cadastre-se</a>
              </div>

              <script>
                document.addEventListener('DOMContentLoaded', function() {
                  const emailInput = document.getElementById('email');
                  const passwordInput = document.getElementById('senha');
                  //const forgotPasswordButton = document.getElementById('forgot-password');

                  emailInput.addEventListener('keydown', function(event) {
                    if (event.key === 'Tab' && !event.shiftKey) {
                      event.preventDefault(); // Impede o comportamento padrão do Tab
                      passwordInput.focus(); // Foca diretamente no campo de senha
                    }
                  });
                });
              </script>

            </form>

          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<?php include 'includes/footer_login.php'; ?>
<!-- PASSWORD EYE -->
<script src="assets/js/pages/password-addon.init.js"></script>
<!-- LEMBRAR ACESSO -->
<script src="assets/js/lembrar_acesso.js"></script>