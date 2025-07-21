<?php include 'includes/header_login.php'; ?>

<div class="row justify-content-center align-items-center flex-column">
  <div class="card card_login pb-3">
    <div class="card-body px-3 px-sm-4 py-4">

      <div class="header_card_login text-center mb-4">
        <h5>Bem-vindo(a)!</h5>
        <p>Informe seus dados para acesso ao sistema</p>
      </div>

      <form method="POST" id="loginForm" class="needs-validation" action="../router/web.php?r=acess" novalidate>

        <div class="mb-3">
          <label class="form-label">Matrícula</label>
          <input type="text" class="form-control text-lowercase" name="matricula" id="matricula" required>
          <div class="invalid-feedback">Informe sua matrícula</div>
        </div>

        <div class="mb-3">
          <div class="d-flex justify-content-between">
            <label class="form-label" for="password-input">Sua senha</label>
            <a href="ad-forgot-pass.php" class="link">Esqueceu sua senha?</a>
          </div>
          <div class="position-relative auth-pass-inputgroup mb-3">
            <input type="password" class="form-control password-input" name="senha" id="senha" required>
            <button class="btn btn-link position-absolute end-0 top-0 text-muted password-addon" type="button"
              id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
            <div class="invalid-feedback">Informe sua senha</div>
          </div>
        </div>

        <div class="form-check form-check-success">
          <input class="form-check-input" type="checkbox" value="" name="rememberMe" id="rememberMe">
          <label class="form-check-label" for="rememberMe">Lembrar acesso</label>
        </div>

        <div class="mt-4">
          <button type="submit" class="btn botao botao_verde_musgo waves-effect waves-light w-100"
            name="login_admin">Entrar</button>
        </div>

        <script>
          document.addEventListener('DOMContentLoaded', function () {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('senha');
            //const forgotPasswordButton = document.getElementById('forgot-password');

            emailInput.addEventListener('keydown', function (event) {
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

<?php include 'includes/footer_login.php'; ?>
<!-- PASSWORD EYE -->
<script src="../assets/js/pages/password-addon.init.js"></script>
<!-- LEMBRAR ACESSO -->
<script src="../assets/js/lembrar_acesso_admin.js"></script>