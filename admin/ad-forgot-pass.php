<?php include 'includes/header_login.php'; ?>

<div class="row justify-content-center align-items-center flex-column">
  <div class="card card_login">
    <div class="card-body px-3 px-sm-4 py-4">

      <div class="header_card_login text-center mb-4">
        <h5>Recuperar Senha</h5>
        <p>Informe o seu e-mail para a recuperação de senha</p>
      </div>

      <form method="POST" id="ValidaBotaoProgressPadrao" class="needs-validation" action="../router/web.php?r=reset&func=reset_pass" novalidate>
        <div class="mb-3">
          <label class="form-label">Seu e-mail</label>
          <input type="email" class="form-control" name="email" required>
          <div class="invalid-feedback">Informe seu e-mail</div>
        </div>

        <div class="mt-4">
          <button type="submit" class="btn botao botao_verde_musgo waves-effect waves-light w-100">Recuperar</button>
        </div>
      </form>

      <div class="mt-4 text-center">
        <p class="mb-0"><a href="../admin" class="link">Voltar para o login</a></p>
      </div>

    </div>
  </div>
</div>

<?php include 'includes/footer_login.php'; ?>
<!-- PASSWORD EYE -->
<script src="../assets/js/pages/password-addon.init.js"></script>
<!-- LEMBRAR ACESSO -->
<script src="../assets/js/lembrar_acesso.js"></script>
<!-- VALIDA FORMULÁRIOS -->
<script src="../assets/js/valida_form.js"></script>