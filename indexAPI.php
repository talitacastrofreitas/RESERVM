<?php include 'includes/header_registro.php'; ?>

<div class="card card_user_registro">
  <div class="card-body">

    <!-- <form method="POST" id="loginForm" class="needs-validation" action="router/web.php?r=UserAcess" novalidate> -->

    <form method="POST" id="loginForm" class="needs-validation" action="controller/controller_acesso.php" novalidate>

      <div class="setup-content" id="step-1">

        <input type="hidden" name="acao" value="acesso">

        <div class="mb-3">
          <label class="form-label">Sua Matrícula <span>*</span></label>
          <input type="text" class="form-control text-lowercase" name="login" id="email" required>
          <div class="invalid-feedback">Informe sua Matricula</div>
        </div>

        <div class="mb-3">
          <div class="d-flex justify-content-between">
            <label class="form-label" for="password-input">Sua senha</label>
            <a href="us-forgot-pass.php" class="link">Esqueceu sua senha?</a>
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
          <button type="submit" class="btn botao botao_verde waves-effect waves-light w-100"
            name="loginUser">Entrar</button>
        </div>

        <div class="mt-4 text-center">
          <div class="signin-other-title">
            <p class="fs-13 mb-4 title text-muted">Não tem uma conta?</p>
          </div>
        </div>

        <div class="mt-0">
          <a href="us-record.php" type="button"
            class="btn botao botao_azul_escuro_transparente waves-effect waves-light w-100">Cadastre-se</a>
        </div>

      </div>

  </div>
</div>

<div class="link_back_reg">
  <p class="mb-0"></p>
</div>


<!-- FOOTER -->
<?php include 'includes/footer_registro.php'; ?>
<!-- PASSWORD EYE -->
<script src="assets/js/pages/password-addon.init.js"></script>
<!-- LEMBRAR ACESSO -->
<script src="assets/js/lembrar_acesso.js"></script>