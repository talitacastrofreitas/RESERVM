<?php include 'includes/header_registro.php'; ?>

<div class="card card_user_registro">
  <div class="card-body">

    <div class="header_card_login text-center mb-4">
      <h5>Recuperar Senha</h5>
      <p>Informe o seu e-mail para a recuperação de senha</p>
    </div>

    <form method="POST" id="ValidaBotaoProgressPadrao" class="needs-validation" action="router/web.php?r=UserReset" novalidate>

      <input type="hidden" name="acao" value="recuperar">

      <div class="mb-3">
        <label class="form-label">Seu e-mail</label>
        <input type="email" class="form-control text-lowercase" name="email" id="email" required>
        <div class="invalid-feedback">Informe seu e-mail</div>
      </div>

      <div class="mt-3">
        <button type="submit" class="btn botao botao_verde waves-effect waves-light w-100">Recuperar</button>
      </div>

    </form>

  </div>
</div>

<div class="link_back_reg">
  <p class="mb-0">Lembrou sua senha? <a href="<?= $url_sistema ?>" class="link"> Clique aqui</a> </p>
</div>

<!-- FOOTER -->
<?php include 'includes/footer_registro.php'; ?>